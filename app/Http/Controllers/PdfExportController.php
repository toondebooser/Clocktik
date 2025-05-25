<?php

namespace App\Http\Controllers;

use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Dompdf\Helpers;
use Illuminate\Http\Request;

class PdfExportController extends Controller
{
    public function exportPdf(Request $request)
    {
        $user = User::find($request->userId);
        $date = Carbon::parse($request->month);
        $dayTotal = $user->dayTotals()
        ->whereMonth('Month', $date)
            ->whereYear('Month', $date)
            ->orderBy('Month', 'asc')
            ->get();

            dd($date);
            $monthlyTotal = $user->userTotals()
            ->whereMonth('Month', $date)
            ->whereYear('Month', $date)
            ->orderBy('Month', 'asc')
            ->get();

        $type = $request->type;
        $pdf = Pdf::loadView('pdf', [
            'user' => $user,
            'dayTotal' => $dayTotal,
            'monthlyTotal' => $monthlyTotal
        ]);
        $timestamp = now()->format('YmdHis'); 
        $filename = 'Uurrooster-' . $user->name . '-' . $date->format('F-Y') . '-' . $timestamp . '.pdf';
        // Add cache-busting headers
        $response = $pdf->stream($filename);

        if ($type === 'preview') {
            return $response->withHeaders([
                'Cache-Control' => 'no-store, no-cache, must-revalidate, max-age=0',
                'Pragma' => 'no-cache',
                'Expires' => 'Fri, 01 Jan 1990 00:00:00 GMT',
            ]);
        } elseif ($type === 'download') {
            $pdf->download($filename);
            return back()->with('success', 'Pdf succesvol gedownload');
        }
    }
}
