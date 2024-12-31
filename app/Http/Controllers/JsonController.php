<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class JsonController extends Controller
{
    public function callJson($table)
    {
         if($table &&  $table->AdditionalTimestamps){
            $json = json_decode($table->AdditionalTimestamps, true);
             return $json;

         }else{
            return [];
         }
     
    }
   
   
}
