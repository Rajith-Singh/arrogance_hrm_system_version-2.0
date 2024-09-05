<?php

namespace App\Services;

use TCPDF;
use Carbon\Carbon;

class SummaryPdfService
{
    public function generateSummaryReport($groupedAttendances, $employees, $startDate, $endDate)
    {
        // Create a new PDF document
        $pdf = new TCPDF();

        // Remove the default header
        $pdf->setPrintHeader(false);

        // Set document information
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor('Arrogance Technologies Pvt Ltd');
        $pdf->SetTitle('Summary Attendance Report');
        $pdf->SetSubject('Summary Attendance Report');

        // Set header and footer fonts
        $pdf->setFooterFont(['helvetica', '', 10]);

        // Set default monospaced font
        $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

        // Set margins
        $pdf->SetMargins(PDF_MARGIN_LEFT, 50, PDF_MARGIN_RIGHT);
        $pdf->SetHeaderMargin(10);
        $pdf->SetFooterMargin(10);

        // Set auto page breaks
        $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

        // Set image scale factor
        $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

        // Add a page
        $pdf->AddPage();

        // Set font
        $pdf->SetFont('helvetica', '', 12);

        // Add company logo and information
        $logo = public_path('images/logo-jpg.jpg');
        $pdf->Image($logo, 15, 10, 50, '', 'JPG', '', 'T', false, 300, '', false, false, 0, false, false, false);
        $pdf->SetY(10);
        $pdf->SetX(70);
        $pdf->SetFont('helvetica', 'B', 14);
        $pdf->Cell(0, 15, 'Arrogance Technologies Pvt Ltd', 0, 1, 'L');
        $pdf->SetFont('helvetica', '', 10);
        $pdf->SetY(25); // Adjust Y position to align properly
        $pdf->SetFont('helvetica', '', 10);
        $pdf->Cell(0, 5, '14/6, Park Street, Colombo 02, Sri Lanka', 0, 1, 'L');
        $pdf->Cell(0, 5, 'Web: www.arrogance.lk', 0, 1, 'L');
        $pdf->Cell(0, 5, 'Phone: +94113416500', 0, 1, 'L');
        
        // Draw a line under the header
        $pdf->Line(15, 40, 195, 40);

        // Report header
        $pdf->SetY(45);
        $pdf->SetFont('helvetica', 'B', 12);
        $pdf->Cell(0, 7, 'Summary Attendance Report', 0, 1, 'C');
        $pdf->SetFont('helvetica', '', 10);
        $pdf->Cell(0, 7, 'Date Range: ' . Carbon::parse($startDate)->format('Y-m-d') . ' to ' . Carbon::parse($endDate)->format('Y-m-d'), 0, 1, 'C');

        // Table header
        $pdf->SetFont('helvetica', 'B', 12);
        $pdf->SetFillColor(220, 220, 220);
        $pdf->Cell(40, 10, 'Employee Name', 1, 0, 'C', 1);
        
        // Generate the date columns for the selected range
        $dateColumns = [];
        $currentDate = Carbon::parse($startDate);
        while ($currentDate->lte(Carbon::parse($endDate))) {
            $dateColumns[] = $currentDate->format('Y-m-d');
            $pdf->Cell(30, 10, $currentDate->format('Y-m-d'), 1, 0, 'C', 1);
            $currentDate->addDay();
        }
        $pdf->Ln();

        // Table body
        $pdf->SetFont('helvetica', '', 10);
        foreach ($groupedAttendances as $employeeId => $records) {
            $employee = $employees[$employeeId];
            $pdf->Cell(40, 10, $employee->name, 1, 0, 'C');

            foreach ($dateColumns as $date) {
                $record = collect($records)->firstWhere('date', $date);
                if ($record) {
                    $checkIn = Carbon::parse($record->real_check_in)->format('H:i');
                    $checkOut = Carbon::parse($record->real_check_out)->format('H:i');

                    $checkInTime = Carbon::parse($checkIn);
                    $checkOutTime = Carbon::parse($checkOut);

                    // Check-in cell coloring
                    if ($checkInTime->gt(Carbon::parse('08:45')) && $checkInTime->gte(Carbon::parse('08:30'))) {
                        $pdf->SetFillColor(255, 0, 0); // Red
                    } else {
                        $pdf->SetFillColor(255, 255, 255); // White
                    }
                    $pdf->Cell(15, 10, $checkIn, 1, 0, 'C', 1);

                    // Check-out cell coloring
                    if ($checkOutTime->lt(Carbon::parse('17:00'))) {
                        $pdf->SetFillColor(255, 0, 0); // Red
                    } elseif ($checkOutTime->gt(Carbon::parse('20:00')) && $checkOutTime->lt(Carbon::parse('22:00'))) {
                        $pdf->SetFillColor(255, 255, 0); // Yellow
                    } elseif ($checkOutTime->gt(Carbon::parse('22:00'))) {
                        $pdf->SetFillColor(0, 255, 0); // Green
                    } else {
                        $pdf->SetFillColor(255, 255, 255); // White
                    }
                    $pdf->Cell(15, 10, $checkOut, 1, 0, 'C', 1);
                } else {
                    $pdf->Cell(30, 10, '', 1, 0, 'C');
                }
            }
            $pdf->Ln();
        }

        // Footer with generation details
        $pdf->Ln(10);
        $pdf->SetFont('helvetica', '', 10);
        $pdf->Cell(0, 5, 'Report generated by: ' . auth()->user()->name, 0, 1, 'L');
        $pdf->Cell(0, 5, 'Date: ' . Carbon::now('Asia/Colombo')->format('Y-m-d'), 0, 1, 'L');
        $pdf->Cell(0, 5, 'Time: ' . Carbon::now('Asia/Colombo')->format('H:i'), 0, 1, 'L');

        // Output the PDF
        $pdf->Output('summary_attendance_report.pdf', 'I');
    }
}
