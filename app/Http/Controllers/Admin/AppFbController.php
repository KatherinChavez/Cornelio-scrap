<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\CompaniesCreate;
use App\Http\Requests\CompaniesEdit;
use App\Models\App_Fb;
use App\Models\Company;
use App\Models\Twitter\TwitterApp;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AppFbController extends Controller
{
    public function index(Request $request)
    {
        if($request->search){
            $app = App_Fb::where('name_app', 'like', '%' . $request->search . '%')
                ->orWhere('app', 'like', '%' . $request->search . '%')
                ->orWhere('app_fb_id', 'like', '%' . $request->search . '%')
                ->orWhere('app_fb_secret', 'like', '%' . $request->search . '%')
                ->orWhere('app_fb_token', 'like', '%' . $request->search . '%')
                ->paginate();

        }else{
            $app = App_Fb::paginate();
            $twitter = TwitterApp::paginate();
        }
        return view('admin.App.index', compact('app', 'twitter'));
    }

    public function store(Request $request)
    {
        if($request->app == "Facebook"){
            $request->validate([
                'name_app' => 'required',
                'app' => 'required',
                'app_fb_id' => 'required|numeric',
                'app_fb_secret' => 'required',
                'app_fb_token' => 'required',
                'country' => 'required',
                'number' => 'required',
            ]);
            $app = App_Fb::create([
                'name_app' => $request->name_app,
                'app' => $request->app,
                'app_fb_id' => $request->app_fb_id,
                'app_fb_secret' => base64_encode($request->app_fb_secret),
                'app_fb_token' => base64_encode($request->app_fb_token),
                'number_one' => $request->country.$request->number,
                'number' => $request->country_num.$request->number_one,
            ]);
        }
        if($request->app == "Twitter"){
            $request->validate([
                'name_app' => 'required',
                'consumer_key' => 'required',
                'consumer_secret' => 'required',
                'token_twitter' => 'required',
                'token_secret_twitter' => 'required',
                'bearer_token' => 'required',
                'country' => 'required',
                'number' => 'required',
            ]);
            TwitterApp::create([
                'name_app'       => $request->name_app,
                'consumer_key' => $request->consumer_key,
                'consumer_secret' => base64_encode($request->consumer_secret),
                'token_twitter' => base64_encode($request->token_twitter),
                'token_secret_twitter' => base64_encode($request->token_secret_twitter),
                'bearer_token' => base64_encode($request->bearer_token),
                'number_one' => $request->country.$request->number,
                'number' => $request->country_num.$request->number_one,
            ]);
        }

        return redirect()->route('app.index')->with('info','Reg istro actualizado con éxito');

    }

    public function edit(Request $request)
    {
        $app = App_Fb::where('id', $request->id_app)->get();
        return $app;
    }

    public function editTwitter(Request $request)
    {
        $app = TwitterApp::where('id', $request->id_app)->get();
        return $app;
    }

    public function update(Request $request)
    {
        if($request->app == "Facebook"){
            $request->validate([
                'name_app' => 'required',
                'app_fb_id' => 'required|numeric',
                'app_fb_secret' => 'required',
                'app_fb_token' => 'required',
            ]);

            $app = App_Fb::where('id', $request->id)->first();
            $app->update([
                'name_app' => $request->name_app,
                'app' => $request->app,
                'app_fb_id' => $request->app_fb_id,
                'app_fb_secret' => base64_encode($request->app_fb_secret),
                'app_fb_token' => base64_encode($request->app_fb_token),
                'number_one' => $request->number,
                'number' => $request->number_one,
            ]);
        }
        if($request->app == "Twitter") {
            $request->validate([
                'name_app'             => 'required',
                'consumer_key'         => 'required',
                'consumer_secret'      => 'required',
                'token_twitter'        => 'required',
                'token_secret_twitter' => 'required',
                'bearer_token'         => 'required',
                'number'               => 'required',
            ]);

            TwitterApp::where('id', $request->id)->update([
                'name_app'             => $request->name_app,
                'consumer_key'         => $request->consumer_key,
                'consumer_secret'      => base64_encode($request->consumer_secret),
                'token_twitter'        => base64_encode($request->token_twitter),
                'token_secret_twitter' => base64_encode($request->token_secret_twitter),
                'bearer_token'         => base64_encode($request->bearer_token),
                'number_one'           => $request->number,
                'number'               => $request->number_one,
            ]);
        }
        return back()->with('info', 'Se ha actualizado exitosamente');
    }

    public function destroy(App_Fb $app)
    {
        $app->delete();
        return redirect()->route('app.index')->with('info', 'Eliminada correctamente');
    }

    public function destroyTwitter(TwitterApp $app)
    {
        $app->delete();
        return redirect()->route('app.index')->with('info', 'Eliminada correctamente');
    }
}
