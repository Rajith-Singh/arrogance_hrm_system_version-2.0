<?php

namespace App\Http\Controllers;

use App\Models\EmployeeDocument;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class EmployeeDocumentController extends Controller
{
    public function index()
    {
        $documents = EmployeeDocument::where('employee_id', Auth::id())->latest()->get();
        return view('user-document', compact('documents'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'document_type' => 'required|string',
            'document_files.*' => 'required|file|mimes:pdf,jpg,jpeg,png|max:2048',
        ]);

        $user = Auth::user();

        foreach ($request->file('document_files') as $index => $file) {
            $existingCount = EmployeeDocument::where('employee_id', $user->id)
                ->where('document_type', $request->document_type)
                ->count();

            $count = $existingCount + $index + 1;
            $extension = $file->getClientOriginalExtension();
            $fileName = "{$request->document_type}_{$user->name}_{$user->id}_{$count}.{$extension}";

            $filePath = $file->storeAs('documents', $fileName, 'public');

            EmployeeDocument::create([
                'employee_id' => $user->id,
                'document_type' => $request->document_type,
                'file_path' => $filePath,
            ]);
        }

        return back()->with('success', 'Documents uploaded successfully!');
    }

    public function download(EmployeeDocument $document)
    {
        $user = Auth::user();
        $extension = pathinfo($document->file_path, PATHINFO_EXTENSION);
        $downloadName = "{$document->document_type}_{$user->name}_{$user->id}.{$extension}";

        return Storage::disk('public')->download($document->file_path, $downloadName);
    }

    public function destroy(EmployeeDocument $document)
    {
        Storage::disk('public')->delete($document->file_path);
        $document->delete();

        return back()->with('success', 'Document deleted successfully!');
    }
}

