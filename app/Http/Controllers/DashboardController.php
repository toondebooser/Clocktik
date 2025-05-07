<?php

namespace App\Http\Controllers;

use App\Helpers\UserActivityLogger;
use App\Models\Timelog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DashboardController extends Controller
{
    public function userDashboard(Request $request)
    {
        try {
            $user = User::find(auth()->user()->id);
            if (!$user) {
                return redirect()->back()->withErrors('error', 'Gebruiker niet gevonden.');
            }

            $userRow = Timelog::where('UserId', $user->id)->first();
            if (!$userRow) {
                return redirect()->back()->withErrors('error', 'Geen timelog gevonden voor deze gebruiker.');
            }

            $dayTotal = $user->dayTotals()->whereDate('Month', now('Europe/Brussels'))->first();

            $userNoteInput = $request->input('userNote');
            if ($userNoteInput !== null) {
                DB::transaction(function () use ($userRow, $userNoteInput) {
                    $userRow->userNote = $userNoteInput === '' ? null : $userNoteInput;
                    $userRow->save();

                    // Log note update
                    UserActivityLogger::log('User note updated successfully', [
                        'user_id' => auth()->user()->id,
                        'user_note' => $userNoteInput,
                    ]);
                });
            }

            $userNote = $userRow->userNote;
            $shiftStatus = $userRow->ShiftStatus;
            $breakStatus = $userRow->BreakStatus;
            $start = $userRow->EndBreak ?? $userRow->StartWork;
            $startBreak = $userRow->StartBreak;
            $breakHours = $dayTotal->BreakHours ?? 0;
            $workedHours = $dayTotal->RegularHours ?? 0;


            return view('dashboard', [
                'user' => $user,
                'workedHours' => $workedHours,
                'breakHours' => $breakHours,
                'startBreak' => $startBreak,
                'start' => $start,
                'shiftStatus' => $shiftStatus,
                'breakStatus' => $breakStatus,
                'userNote' => $userNote,
            ]);
        } catch (QueryException $e) {
            Log::error('Failed to load user dashboard', [
                'user_id' => auth()->user()->id ?? null,
                'user_note' => $request->input('userNote'),
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);

            return redirect()->back()->withErrors('error', 'Er is een fout opgetreden bij het laden van het dashboard.');
        }
    }
}