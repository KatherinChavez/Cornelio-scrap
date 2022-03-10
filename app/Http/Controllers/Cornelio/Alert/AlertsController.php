<?php

namespace App\Http\Controllers\Cornelio\Alert;

use App\Http\Controllers\Controller;
use App\Models\Alert;
use App\Models\Subcategory;
use Illuminate\Http\Request;

class AlertsController extends Controller
{
    public function index()
    {
        $alerts=Alert::join('subcategory','subcategory.id', 'alert.subcategory_id')
            ->where('subcategory.company_id', session('company_id'))
            ->paginate();
        return view('Cornelio.Alert.index',compact('alerts'));
    }
    public function status(Request $request){
        $status = Subcategory::where('id', $request->id)->first();
        $status->update([
            'status' => 1,
        ]);
    }
    public function statusOff(Request $request){
        $status = Subcategory::where('id', $request->id)->first();
        $status->update([
            'status' => 0,
        ]);
    }
    public function notification(Request $request){
        $alerts = Alert::where('subcategory_id', $request->id)->first();
        $alerts->update([
            'notification' => 1,
        ]);
    }
    public function notificationOff(Request $request){
        $alerts = Alert::where('subcategory_id', $request->id)->first();
        $alerts->update([
            'notification' => 0,
        ]);
    }
    public function report(Request $request){
        $alerts = Alert::where('subcategory_id', $request->id)->first();
        $alerts->update([
            'report' => 1,
        ]);
    }
    public function reportOff(Request $request){
        $alerts = Alert::where('subcategory_id', $request->id)->first();
        $alerts->update([
            'report' => 0,
        ]);
    }
    public function consult(Request $request){
        $alerts=Alert::join('subcategory','subcategory.id', 'alert.subcategory_id')
            ->where('subcategory.company_id', session('company_id'))
            ->get();
        return $alerts;
    }
}
