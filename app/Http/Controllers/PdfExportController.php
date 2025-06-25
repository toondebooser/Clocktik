<?php

namespace App\Http\Controllers;

use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Dompdf\Dompdf;
use Dompdf\Helpers;

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

        $monthlyTotal = $user->userTotals()
            ->whereMonth('Month', $date)
            ->whereYear('Month', $date)
            ->orderBy('Month', 'asc')
            ->get();

        $type = $request->type;
        $filename = 'Uurrooster-' . $user->name . '-' . $date->format('F-Y') . '-' . now()->format('YmdHis') . '.pdf';

        // Generate HTML from the view
        $html = view('pdf', [
            'user' => $user,
            'dayTotal' => $dayTotal,
            'monthlyTotal' => $monthlyTotal
        ])->render();

        // Create Dompdf instance
        $dompdf = new Dompdf();
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
        $pdf = $dompdf->output();

        if ($type === 'preview') {
            if (headers_sent()) {
                die("Unable to stream PDF: headers already sent");
            }

            header("Cache-Control: no-cache, no-store, must-revalidate");
            header("Pragma: no-cache");
            header("Expires: 0");
            header("Content-Type: application/pdf");
            header("Content-Length: " . mb_strlen($pdf, "8bit"));
            header(Helpers::buildContentDispositionHeader("inline", $filename));
            echo $pdf;
            flush();
            exit;
        } elseif ($type === 'download') {
            return response($pdf, 200, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
                'Content-Length' => mb_strlen($pdf, "8bit"),
                'Cache-Control' => 'no-store, no-cache, must-revalidate, max-age=0',
                'Pragma' => 'no-cache',
                'Expires' => '0',
            ]);
        }
    }
}
