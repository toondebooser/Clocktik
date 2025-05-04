<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\User;
use App\Utilities\DateUtility;
use App\Utilities\UserUtility;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Exception;
use Illuminate\Auth\Events\Validated;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Spatie\Holidays\Holidays;

class SettingsController extends Controller
{
    public function settingsView($company_code)
    {

        $data = Company::where('company_code', $company_code)->first();
        $admins = User::where('company_code', $company_code)->where('admin', true)->get();
        $workers = User::where('company_code', $company_code)->where('admin', false)->get();
        $holidaysCheck = DateUtility::checkHolidaysInMonth();
        $holidays = null;
        $workersNeedingHolidays = null;
        if (!empty($holidaysCheck)) {
            $holidays = $holidaysCheck;
            $workersNeedingHolidays = UserUtility::workersHolidayCheck($company_code);
        }
        return view('settings', ['data' =>  $data, 'admins' => $admins, 'holidays' => $holidays, 'workersNeedingHolidays' => $workersNeedingHolidays, 'workers' => $workers]);
    }

   

    public function changeRights(Request $request, $id, $company_code)
    {
        try {
            $user = User::find($id);
            if (!$user) {
                throw new Exception("User $id not found");
            }

            $targetSide = $request->input('target_side');
            $shouldToggle = false;

            if ($targetSide === 'left' && !$user->admin) {
                $shouldToggle = true;
            } elseif ($targetSide === 'right' && $user->admin) {
                $shouldToggle = true;
            }

            if ($shouldToggle) {
                DB::transaction(function () use ($user) {
                    $user->update([
                        'admin' => !$user->admin
                    ]);
                });

                return redirect()->route('adminSettings', ['company_code' => $company_code])
                    ->with('success', "Rechten voor $user->name zijn aangepast");
            }

            return redirect()->route('adminSettings', ['company_code' => $company_code])
                ->with('message', "No rights change needed for user $id");
        } catch (Exception $e) {
            Log::error("Error in changeRights for user ID $id: " . $e->getMessage());
            return redirect()->route('adminSettings', ['company_code' => $company_code])
                ->withErrors(['error' => 'Er ging iets mis bij het aanpassen van de rechten: ' . $e->getMessage()]);
        }
    }

    public function logohandler($company, $company_logo)
    {
        $validator = Validator::make(
            ['company_logo' => $company_logo],
            [
                'company_logo' =>  'required|image|max:2048'

            ],
            [
                'company_logo.required' => 'A company logo is required.',
                'company_logo.image' => 'Logo moet een afbeelding zijn (e.g., JPEG, PNG).',
                'company_logo.max' => 'Logo mag niet groter zijn dan 2 MB.',
            ]
        );

        if ($validator->fails()) {
            redirect()->back()->withErrors($validator)->withInput();
            return null;
        }
        $logoName = time() . '_' . $company_logo->getClientOriginalName();
        $folderPath = 'logos/' . $company->company_code;
        $fullPath = public_path($folderPath);

        if (!File::exists($fullPath)) {
            File::makeDirectory($fullPath, 0755, true);
        } else {
            $existingFiles = File::files($fullPath);
            foreach ($existingFiles as $file) {
                File::delete($file->getPathname());
            }
        }
        $company_logo->move($fullPath, $logoName);
        $logoPath = $folderPath . '/' . $logoName;

        return $logoPath;
    }

    public function updateSettings(Request $request)
    {
        try {
            $company = Company::where('company_code', $request->company_code)->first();

            $updateData = [];
            foreach ($request->all() as $key => $value) {
                if ($key === '_token') {
                    continue;
                }
                if ($key === 'company_logo' && $value && $request->hasFile('company_logo')) {
                    $logoResult = $this->logohandler($company, $value);
                    if ($logoResult !== null) {
                        $updateData[$key] = $logoResult;
                    }
                } else {
                    $updateData[$key] = $value;
                }
            }

            $success = $company->update($updateData);
            if (!$success) {
                throw new Exception('Failed to update company settings');
            }

            $result = UserUtility::updateAllUsersDayTotals($company->company_code);
            if (is_array($result) && isset($result['error'])) {
                throw new Exception($result['error']);
            }

            return redirect()->back()->with('success', 'Instellingen zijn aangepast.');
        } catch (Exception $e) {
            Log::error("Error in updateSettings for company {$request->company_code}: " . $e->getMessage());
            return redirect()->back()->withErrors(['error',  $e->getMessage()]);
        }
    }
}
