<?php

namespace App\Http\Controllers;

use App\Models\Daytotal;
use App\Models\Timesheet;
use App\Models\User;
use App\Models\Usertotal;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
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

        $monthlyTotal = $user->userTotals()
            ->whereMonth('Month', $date)
            ->whereYear('Month', $date)
            ->orderBy('Month', 'asc')
            ->get();
        $type = request('type');
        $pdf = Pdf::loadView('pdf', ['user' => $user, 'dayTotal' => $dayTotal, "monthlyTotal" => $monthlyTotal]);
        $filename = 'Uurrooster' . '-' . $user->name . '-' . date('F', strtotime($dayTotal[0]->Month)) . '-' . date('Y', strtotime($dayTotal[0]->Month)) . '.pdf';
        if ($type == 'preview') {
            return $pdf->stream($filename);
        } elseif ($type == 'download') {
            return $pdf->download($filename);
        }
    }
}
