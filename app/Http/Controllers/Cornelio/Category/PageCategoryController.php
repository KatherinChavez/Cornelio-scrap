<?php

namespace App\Http\Controllers\Cornelio\Category;

use App\Http\Controllers\Controller;
use App\Models\Alert;
use App\Models\Category;
use App\Models\Company;
use App\Models\Cron;
use App\Models\Info_page;
use App\Models\Megacategory;
use App\Models\Scraps;
use App\Models\Subcategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PageCategoryController extends Controller
{
    public function index(Request $request)
    {
        if ($request->page) {
            $data = [];
            $contents = Category::where('company_id', session('company_id'))
                ->where(function ($query) use ($request) {
                    if ($request->search) {
                        $query->where('name', 'LIKE', "%$request->search%");
                    }
                })
                ->get();
            $pages = Scraps::all();
            foreach ($contents as $content) {
                $detail = $pages->where('categoria_id', $content->id);
                $paginas = [];
                foreach ($detail as $page) {
                    $paginas [] = ['id' => $page->id, 'page_id' => $page->page_id, 'page_name' => $page->page_name, 'category_id' => $page->categoria_id];
                }
                $data[] = ['id' => $content->id, 'name' => $content->name, 'description' => $content->description, 'pages' => $paginas];
            }
            return $data;
        } else {
            $data = [];
            $contents = Category::where('company_id', session('company_id'))->get();
            $pages = Scraps::all();
            foreach ($contents as $content) {
                $detail = $pages->where('categoria_id', $content->id);
                $paginas = [];
                foreach ($detail as $page) {
                    $paginas [] = ['id' => $page->id, 'page_id' => $page->page_id, 'page_name' => $page->page_name, 'category_id' => $page->categoria_id];
                }
                $data[] = ['id' => $content->id, 'name' => $content->name, 'description' => $content->description, 'pages' => $paginas];
            }
            return view('Cornelio.Category.PageCategory.index', compact('data'));

        }

    }

    public function create()
    {
        $empresas = Megacategory::where('company_id', session('company_id'))->pluck('name', 'id');

        return view('Cornelio.Category.PageCategory.create', compact('empresas'));
    }

    public function store(Request $request){
        $user_id = Auth::id();
        $company_id = session('company_id');

        $content = Category::create([
            'name' => $request->category_name,
            'description' => $request->description,
            'company_id' => $company_id,
        ]);
        $id_content=$content->id;
        if($request->pages){
            $pages = \GuzzleHttp\json_decode($request->pages);
            foreach ($pages as $page) {
                $pagesO = (object) $page;
                $scrap = Scraps::create([
                    'page_id' => $pagesO->page_id,
                    'page_name' => $pagesO->page_name,
                    'competence' => $pagesO->competence,
                    'user_id'=>$user_id,
                    'categoria_id' => $id_content,
                    'company_id' => $company_id,
                ]);
                $selectCron = Cron::where('page_id', $pagesO->page_id)->first();
                if(!$selectCron){
                    $cron= Cron::create([
                        'company_id'=> $company_id,
                        'page_id' => $pagesO->page_id,
                        'page_name' => $pagesO->page_name,
                        'timePost' => 1440,
                        'timeReaction' => 1440,
                        'id_appPost' => 16,
                        'id_appReaction' => 16,
                    ]);
                }
            }
        }
        $mensaje='Registro guardado con éxito';
        return Response()->json( $mensaje,200);
    }

    public function storeOld(Request $request)
    {
        $request->validate
        ([
            'empresa' => 'required',
            'categoria' => 'required',
            'description' => 'required',
        ]);

        $user_id = Auth::id();
        $company_id = session('company_id');
        // valida si es una categoria nueva o es una que yo tenia creada y me envia el id
        if (!is_numeric($request->empresa)) {
            $request->request->add([
                'name' => $request->empresa,
                'company_id' => $company_id,
                'user_id' => $user_id,
            ]);
            $megacategorias = Megacategory::where('name', $request->empresa)->where('company_id', $company_id)->first();
            if (!$megacategorias) {
                $megacategorias = Megacategory::create($request->all());
            }
            $id_mega = $megacategorias->id;

        } else {
            $id_mega = $request->empresa;
        }

        $request->request->add([
            'name' => $request->categoria,
            'company_id' => $company_id,
            'megacategory_id' => $id_mega,
            'user_id' => $user_id,
        ]);
        $categorias = Category::where('name', $request->categoria)->where('megacategory_id', $id_mega)->first();
        $categorias ? null : $categorias = Category::create($request->all());

        $subarray = $request->subtemas;
        if ($request->subtemas) {

            foreach ($subarray as $subtema) {
                $subtemaO = (object)$subtema;
                $request->request->add([
                    'name' => $subtemaO->subtema,
                    'category_id' => $categorias->id,
                    'channel' => $subtemaO->channel,
                    'nameTelegram' => $subtemaO->nameTelegram,

                ]);
                $subcategorias = Subcategory::create($request->all());
            }
        }
        $request->request->add([
            'notification' => 0,
            'report' => 0,
        ]);

        $alert = Alert::create($request->all());
        $mensaje = 'Registro guardado con éxito';
        return Response()->json($mensaje, 200);
    }

    public function edit(Category $categorias)
    {
        $empresas = Megacategory::where('company_id', session('company_id'))->pluck('name', 'id');
        $selectCategoria = Megacategory::where('id', $categorias->megacategory_id)->first();
        $subcategories = Subcategory::where('category_id', $categorias->id)->get();
        $categorias->empresa = $selectCategoria->name;
        $categorias->categoria = $categorias->name;

        return view('Cornelio.Category.PageCategory.edit', compact('categorias', 'empresas', 'subcategories'));
    }

    public function update(Request $request)
    {
        $company_id = session('company_id');
        $cate = Category::where('id',$request->category_id )->update([
            'name'=> $request->category_name,
            'description'=> $request->description,
            'company_id'=> $company_id,
            ]);
        if($request->pages){
            $pages = json_decode($request->pages);
            foreach ($pages as $page) {
                $selectCron = Cron::where('page_id', $page->page_id)->first();
                if(!$selectCron){
                    $cron= Cron::create([
                        'company_id'=> $company_id,
                        'page_id' => $page->page_id,
                        'page_name' => $page->page_name,
                        'timePost' => 1440,
                        'timeReaction' => 1440,
                        'id_appPost' => 16,
                        'id_appReaction' => 16,
                    ]);
                }
//                $scrap = Scraps::create([
//                    'page_id' => $page->page_id,
//                    'page_name' => $page->page_name,
//                    'categoria_id' => $cate->id,
//                    'company_id' => $company_id,
//                ]);
            }
        }

    }
    public function updateOld(Request $request)
    {
        $categoria = Category::find($request->id);
        $user_id = Auth::id();
        $company_id = session('company_id');
        //Megacategoria = empresa
        if (!is_numeric($request->empresa)) {
            $request->request->add([
                'name' => $request->empresa,
                'company_id' => $company_id,
                'user_id' => $user_id,
            ]);
            $megacategorias = Megacategory::find($categoria->megacategory_id);
            $megacategorias->update($request->only(['name', 'company_id', 'user_id', 'description']));
            $megacategorias->save();
            $id_mega = $megacategorias->id;
        } else {
            $id_mega = $request->empresa;
        }

        $request->request->add([
            'name' => $request->categoria,
            'company_id' => $company_id,
            'megacategory_id' => $id_mega,
            'user_id' => $user_id,
        ]);
        $categoria->update($request->only(['name', 'company_id', 'megacategory_id', 'description']));
        $categoria->save();
        $subarray = $request->subtemas;
        if ($request->subtemas) {

            foreach ($subarray as $subtema) {
                $subtemaO = (object)$subtema;
                $request->request->add([
                    'name' => $subtemaO->subtema,
                    'category_id' => $categoria->id,
                    'channel' => $subtemaO->channel,
                    'nameTelegram' => $subtemaO->nameTelegram,
                ]);
                //array
                $datos = ([
                    'name' => $subtemaO->subtema,
                    'category_id' => $categoria->id,
                    'channel' => $subtemaO->channel,
                    'nameTelegram' => $subtemaO->nameTelegram,
                    'megacategory_id' => $id_mega,
                    'company_id' => $company_id,
                    'user_id' => $user_id
                ]);
                $subcategorias = Subcategory::where('id', $subtemaO->id)->first();
                if ($subcategorias) {
                    $subcategorias->update($datos);
                } else {
                    $subcategorias = Subcategory::create($datos);
                }


            }

//            foreach ($subarray as $subtema) {
//                $subtemaO = (object) $subtema;
//                $request->request->add([
//                    'name' => $subtemaO->subtema,
//                    'category_id' => $categoria->id,
//                    'channel' => $subtemaO->channel,
//                    'nameTelegram' => $subtemaO->nameTelegram,
//
//                ]);
//                $subcategorias = Subcategory::create($request->all());
//            }
        }
//        $alert = Alert::firstOrNew($request->all());
        $mensaje = 'Registro guardado con éxito';
        return Response()->json($mensaje, 200);

        //return redirect()->route('Category.index', [$categorias->id])->with('info', 'Registro actualizado con éxito');
    }

    public function destroy(Category $categorias)
    {
        $scraps=Scraps::where('categoria_id',$categorias->id)->delete();
        $categorias->delete();
        return Response()->json(['code'=>200]);
    }

    public function showTheme(Request $request)
    {
        $theme = Subcategory::where('category_id', $request->categoria)->get();
        return $theme;
    }

    public function destroyTheme(Subcategory $subcategory)
    {
        if ($subcategory->company_id == session('company_id')) {

            $subcategory->delete();

            return back()->with('info', 'Eliminada correctamente');
        }
        return back()->with('info', 'No ha sido posible eliminar');

    }

    public function search(Request $request)
    {
        $take = 20;
        if ($request->search) {
            $categoria = Category::join('megacategory', 'megacategory.id', 'category.megacategory_id')
                ->where('name', 'like', '%' . $request->search . '%')
                ->orWhere('description', 'LIKE', '%' . $request->search . '%')
                ->where('megacategory.company_id', session('company_id'))
                ->select('category.*', 'megacategory.name as meganame')
                //->take($take)
                ->get();
        } else {
            $categoria = Category::join('megacategory', 'megacategory.id', 'category.megacategory_id')
                ->where('megacategory.company_id', session('company_id'))
                ->select('category.*', 'megacategory.name as meganame')
                //->take($take)
                ->get();
        }
        return $categoria;
    }
}
