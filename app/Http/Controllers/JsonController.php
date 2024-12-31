<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class JsonController extends Controller
{
    public function callJson($userRow)
    {
         $json = $userRow->AdditionalTimestamps
    ? json_decode($userRow->AdditionalTimestamps, true)
    : [];
        return $json;
    }
   
    public function saveJson($userRow, $json)
    {
        $userRow->AdditionalTimestamps = json_encode($json);
        $userRow->save();
    }
}
