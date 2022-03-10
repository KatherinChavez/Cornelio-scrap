<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\NewUser;
use App\Models\Company;
use App\User;
use Caffeinated\Shinobi\Models\Role;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function crea()
    {
        //$roles = Role::where('id','NOT LIKE','1')->paginate();
        $roles = Role::paginate();
        $user_id=Auth::id();
        $user=User::find($user_id);
        $companies=$user->companies;
        return view('admin.users.create', compact('companies','roles'));
    }


    public function index1(Request $request)
    {
        //buscador
        if($request){
            $users=User::where('name','LIKE','%'.$request->search.'%')->whereHas('companies',function ($q){
                $q->where('companies.id',session('company_id'));
            })->paginate();
        }else{
            $users=User::whereHas('companies',function ($q){
                $q->where('companies.id',session('company_id'));
            })->paginate();
        }
        return view('admin.users.index', compact('users'));
    }

    public function index(Request $request)
    {
        if($request){
            $users=User::where('name','LIKE','%'.$request->search.'%')->paginate(10);
            return view('admin.users.index', compact('users'));

        }else{
            $users=User::paginate(10);
            return view('admin.users.index', compact('users'));
        }
        return view('admin.users.index', compact('users'));

    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'last_name' => 'required',
            'sec_last_name' => 'required',
            'email' => 'required|unique:users|email',

        ]);

        $random=substr(md5(mt_rand()), 0, 8);
        $user_id=User::create([
            'name'=>$request->name,
            'last_name'=>$request->last_name,
            'sec_last_name'=>$request->sec_last_name,
            'email'=>$request->email,
            'password' => Hash::make($random),
            'created_at' => Carbon::now(),
        ]);
        $correo=array(
            'name'=>$request->name,
            'last_name'=>$request->last_name,
            'sec_last_name'=>$request->sec_last_name,
            'email'=>$request->email,
            'password' => $random,
            'created_at' => Carbon::now(),
        );
        $user_id->companies()->sync($request->get('companies'));
        $user_id->roles()->sync($request->get('roles'));

        $user = $user_id;
        Mail::to($request->email)->send(new NewUser($correo));
        return redirect()->route('users.index',$user->id)->with('info','Registro guardado con éxito');

        //return view('admin.profile.show', compact('user'))->with('info','Registro guardado con éxito');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */

    public function show(User $user)
    {
        $user_id=Auth::id();
        $userP = $user->id;

        if($user_id == $userP){
            return view('admin.profile.show', compact('user'));
        }
        else{
            return view('admin.users.show', compact('user'));
        }

        //return view('admin.users.show', compact('user'));


    }

    public function show1(User $user)
    {

        if(!$user->id){
            $user=Auth::user();
            return view('admin.profile.show', compact('user'));
        }

        return view('admin.profile.show', compact('user'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function edit(User $user)
    {
        if(!$user->id){
            $user=  Auth::user();
            return view('admin.profile.edit', compact('user'));
        }
        //$roles = Role::where('id','NOT LIKE','1')->paginate();
        $roles = Role::paginate();
        $userManager= Auth::user();
        $companies=$userManager->companies;
        return view('admin.users.edit', compact('user','roles','companies'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, User $user)
    {
        /**
         * 1 actualizar usuario
         * 2 actualizar roles
         */
        if(!$user->id){
            $user_id=auth()->id();
            $user= Auth::user();
            $request->validate([
                'email' => ['required','email', Rule::unique('users')->ignore($user_id)],'max:255',
            ]);
            $user->update(['email'=>$request->email]);

            if($request->get('password')){
                $request->validate([
                    'password' => ['required', 'string', 'min:8', 'confirmed','regex:/[a-z]/',      // must contain at least one lowercase letter
                        'regex:/[A-Z]/',      // must contain at least one uppercase letter
                        'regex:/[0-9]/',      // must contain at least one digit
                        'regex:/[@;$!-¡._%*#¿?&]/'
                    ],
                ]);

                $enc = Hash::make($request['password']);
                $user = User::find($user_id);
                $user->update([
                    'password' => $enc,
                ]);
            }

            return redirect()->route('users.profile',$user->id)->with('info','Usuario actualizado con éxito');
        }
        $request->validate([
            'name' => 'required',
            'last_name' => 'required',
            'sec_last_name' => 'required',
            'email' => ['required','email', Rule::unique('users')->ignore($user->id)],'max:255',
        ]);
        $user->update($request->all());
        $user->companies()->sync($request->companies);
        $user->roles()->sync($request->roles);

        return redirect()->route('users.index',$user->id)->with('info','Usuario actualizado con éxito');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user)
    {
        $user->delete();
        return back()->with('info', 'Eliminado correctamente');
    }


}
