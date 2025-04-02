<?php

// app/Http/Controllers/UserController.php
namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    // List all users
    public function index()
    {
        $users = User::where('department', '!=', 'not set')
                ->where('emp_no', '!=', 0)
                ->orderBy('id')
                ->paginate(10);
        return view('hr.users.index', compact('users'));
    }

    // Show edit form
    public function edit(User $user)
    {
        $departments = [
            'NW' => 'Networking & Infrastructure Department',
            'OT' => 'Operational Technology Department',
            'RD' => 'Research & Development Department',
            'AT' => 'Arro Tech Marketing Department',
            'SL' => 'Sales and Marketing Department',
            'FN' => 'Finance Department',
            'HR' => 'Human Resource & Administration Department'
        ];// Add your departments
        $categories = ['internship', 'probation', 'permanent'];
        $subCategories = ['fresh', null];
        
        return view('hr.users.edit', compact('user', 'departments', 'categories', 'subCategories'));
    }
    

    // Update user
    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'emp_no' => 'required|string|max:10',
            'department' => 'required|string|max:10',
            'email' => 'required|email|unique:users,email,'.$user->id,
            'status' => 'required|integer',
            'phone' => 'nullable|string|max:255',
            'usertype' => 'required|string',
            'category' => 'nullable|string|in:internship,probation,permanent',
            'sub_category' => 'nullable|string|in:fresh',
            'joining_date' => 'nullable|date',
            'permanent_date' => 'nullable|date|after_or_equal:joining_date',
        ]);

        $user->update($validated);

        return redirect()->route('users.index')
            ->with('success', 'User updated successfully');
    }
}