<?php

namespace App\Http\Controllers;

use App\Models\Timesheet;
use App\Models\User;
use App\Models\Usertotal;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class PdfExportController extends Controller
{
    public function exportPdf () {

        $user = User::find(request('userId'));
        $month = date('m', strtotime(request('month')));
        $year = date('Y', strtotime(request('month')));
        $findTimesheet = new Timesheet;
        $total = new Usertotal;
        $timesheet = $findTimesheet->where('UserId', '=', $user->id)
        ->whereMonth('Month', '=', $month)
        ->whereYear('Month', '=', $year)
        ->orderBy('Month', 'asc')
        ->get();
        $monthlyTotal = $total->where('UserId',$user->id)
        ->whereMonth('Month', $month)
        ->whereYear('Month', $year)
        ->orderBy('Month', 'asc')
        ->get();

        $type = request('type');
        $pdf = Pdf::loadView('pdf', ['user'=>$user, 'timesheet'=> $timesheet, "monthlyTotal" => $monthlyTotal]);
        $filename = 'Uurrooster'.'-'.$user->name.'-'.date('F', strtotime($timesheet[0]->Month)).'-'.date('Y', strtotime($timesheet[0]->Month)) . '.pdf';
        if ($type == 'preview')
        {
        return $pdf->stream($filename);
        }elseif($type =='download')
        {
        return $pdf->download($filename);
        }
    }
}
