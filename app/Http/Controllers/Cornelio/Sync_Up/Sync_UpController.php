<?php

namespace App\Http\Controllers\Cornelio\Sync_Up;

use App\Http\Controllers\Controller;
use App\Models\Alert;
use App\Models\Company;
use App\Models\Compare;
use App\Models\NumberWhatsapp;
use App\Models\Subcategory;
use App\Models\Word;
use Illuminate\Http\Request;

class Sync_UpController extends Controller
{

    public function index()
    {
        $companie = session('company_id');
        $data = Company::where('id', $companie)->where('client_id', '!=', "0")->where('instance', '!=', "0")->select('client_id', 'instance')->first();
        return view('Cornelio.Sync_Up.index', compact('data'));
    }

    public function Reconnect(){
        $companie = session('company_id');
        $data = Company::where('id', $companie)->select('client_id', 'instance')->first();
        $reboot= file_get_contents("https://wapiad.com/api/reconnect.php?client_id=$data->client_id&instance=$data->instance");
        $result = json_decode($reboot);

        if($result->status == true && $result->message == "device reconnecting"){
            return 200;
        }
        return 500;
    }

    public function Reboot()
    {
        $companie = session('company_id');
        $data = Company::where('id', $companie)->select('client_id', 'instance')->first();
        $reboot= file_get_contents("https://wapiad.com/api/reboot.php?client_id=$data->client_id&instance=$data->instance");
        $result = json_decode($reboot);

        if($result->status == true && $result->message == "Instance Rebooted Successfully"){
            return 200;
        }
        return 500;
    }
}
