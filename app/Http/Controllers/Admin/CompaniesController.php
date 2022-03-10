<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\CompaniesCreate;
use App\Http\Requests\CompaniesEdit;
use App\Models\Company;
use App\Models\Scraps;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CompaniesController extends Controller
{
    public function index()
    {
        $user=Auth::user();
        $user=User::find($user->id);
        $companies=$user->companies;
        if($user->hasRole('admin')){
            $otras=Company::get();
            return view('admin.companies.index', compact('companies','otras'));
        }else{
            return view('admin.companies.index', compact('companies'));
        }
    }

    public function create()
    {
        //return view('admin.companies.create');
        $company = session('company_id');
        $user_id=Auth::id();
        $user=User::where('id', '!=', $user_id)->paginate();
        $page = Scraps::where('company_id', $company)->orderBy('page_name')->pluck('page_name', 'id');
        return view('admin.companies.create', compact('user', 'page'));
    }

    public function store(CompaniesCreate $request)
    {
        $request->validate([
            'nombre' => 'required',
            'slug' => 'required',
            'emailCompanies' => 'required|email',
            'channel' => 'required|numeric',
            'phone' => 'required|numeric|digits_between:11,15',
            'phoneOptional' => 'numeric|digits_between:11,15',
            'descripcion' => 'required',
            'status' => 'required',
            'key' => 'required',
            'client_id' => 'required',
            'instance' => 'required',
        ]);
        $user_id=Auth::id();
//        $request->request->add([
//            'created_by' => $user_id
//        ]);
        //$companies=Company::create($request->all());

        $companies=Company::create([
            'nombre' => $request->nombre,
            'descripcion' =>$request->nombre,
            'slug' =>$request->slug,
            'page' =>$request->page,
            'status' =>$request->status,
            'emailCompanies'=>$request->emailCompanies,
            'channel'=>$request->channel,
            'client_id' => $request->client_id,
            'instance' => $request->instance,
            'group_id' => $request->group_id,
            'phone' => $request->phone,
            'phoneOptional' => $request->phoneOptional,
            'key' => $request->key,
            'created_by' =>$user_id,
            'created_at' => Carbon::now(),
        ]);

        //dd($companies->users(), $request->get('user'));

        $companies->users()->sync($request->get('users'));

        $companies->users()->syncWithoutDetaching([$user_id]);

        //return redirect()->route('users.index',$companies->id)->with('info','Por favor proceda a agregar usuarios a la empresa');
        return redirect()->route('companies.index')->with('info','Registro actualizado con éxito');

    }

    public function edit( Company $companies)
    {
        $user_id=Auth::id();
        $company = session('company_id');
        //$user=User::where('id', '!=', $user_id)->paginate();
        $user=User::paginate();
        $page = Scraps::where('company_id', $company)->orderBy('page_name');
        return view('admin.companies.edit', compact('companies','user_id', 'user', 'page'));
    }

    public function update(CompaniesEdit $request, Company $companies)
    {

        $request->validate([
            'nombre' => 'required',
            'slug' => 'required',
            'emailCompanies' => 'required|email',
            'channel' => 'required|numeric',
            'phone' => 'required|numeric|digits_between:11,15',
            'phoneOptional' => 'numeric|digits_between:11,15',
            'descripcion' => 'required',
            'status' => 'required',
            'key' => 'required',
            'client_id' => 'required',
            'instance' => 'required',
        ]);
        $user_id=Auth::id();
        $companies->update($request->all());
        $companies->users()->sync($request->users);
        $companies->users()->sync($request->get('users'));
        $companies->users()->syncWithoutDetaching([$user_id]);
        return redirect()->route('companies.index')->with('info','Registro actualizado con éxito');
    }

    public function destroy(Company $companies)
    {
        $user_id=Auth::id();
        $companies->users()->sync([$user_id]);
        $companies->delete();
        return back()->with('info', 'Eliminado correctamente');
    }
}
