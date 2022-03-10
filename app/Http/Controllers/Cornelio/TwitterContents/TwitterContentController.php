<?php

namespace App\Http\Controllers\Cornelio\TwitterContents;

use App\Http\Controllers\Controller;
use App\Models\Twitter\TwitterContent;
use App\Models\Twitter\TwitterScrap;
use App\Traits\ScrapTweetTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TwitterContentController extends Controller
{
    use ScrapTweetTrait;
    public function index()
    {
        return view('Twitter.ContentTwitter.index');
    }

    function syncTwitter(): array
    {
        $data = [];
        $contents = TwitterContent::where('company_id', session('company_id'))->get();
        $pages = TwitterScrap::all();
        foreach ($contents as $content) {
            $detail = $pages->where('categoria_id', $content->id);
            $paginas = [];
            foreach ($detail as $page) {
                $paginas [] = ['id' => $page->id, 'page_id' => $page->page_id, 'page_name' => $page->name, 'category_id' => $page->categoria_id];
            }
            $data[] = ['id' => $content->id, 'name' => $content->name, 'description' => $content->description, 'pages' => $paginas];
        }
        return $data;
    }

    public function store(Request $request){
        $user_id = Auth::id();
        $company_id = session('company_id');

        $content = TwitterContent::create([
            'name' => $request->category_name,
            'description' => $request->description,
            'company_id' => $company_id,
        ]);
        $id_content=$content->id;
        if($request->pages){
            $pages = \GuzzleHttp\json_decode($request->pages);
            foreach ($pages as $page) {
                $pageQuery = $this->get_username($page->username);
                $decodePage = \GuzzleHttp\json_decode($pageQuery);
                $query = TwitterScrap::where('page_id',$decodePage->data->id)->where('categoria_id', $id_content)->first();
                if(!$query){
                    TwitterScrap::create([
                        'page_id'      => $decodePage->data->id,
                        'username'     => $decodePage->data->username,
                        'name'         => $decodePage->data->name,
                        'user_id'      => $user_id,
                        'categoria_id' => $id_content,
                        'company_id'   => $company_id,
                        'status'       => 1,
                    ]);
                }
            }
        }
        $mensaje='Registro guardado con éxito';
        return Response()->json( $mensaje,200);
    }

    public function saveScrapTwitter(Request $request){
        $user_id = Auth::id();
        $company_id = session('company_id');
        $info = $this->get_info($request);
        if($info == 200){
            $page = $this->get_username($request->username);
            $decodePage = \GuzzleHttp\json_decode($page);
            $query = TwitterScrap::where('page_id', $decodePage->data->id)->where('categoria_id', $request->categoria)->first();
            if(!$query){
                TwitterScrap::create([
                    'page_id'      => $decodePage->data->id,
                    'username'     => $decodePage->data->username,
                    'name'         => $decodePage->data->name,
                    'user_id'      => $user_id,
                    'categoria_id' => $request->categoria,
                    'company_id'   => $company_id,
                    'status'       => 1,
                ]);
                return 200;
            }
            return 201;
        }
        return 500;
    }

    public function get_info(Request $request){
        $validate = $this->get_username($request->username);
        $decode_validate = json_decode($validate);
        if(!isset($decode_validate->errors)){
            $twitter_info = $this->InformationPageTweet($decode_validate->data->id);
            dd($twitter_info);
            return $twitter_info;
        }
        return 500;
    }

    public function update(Request $request)
    {
        $company_id = session('company_id');
        $cate = TwitterContent::where('id',$request->category_id )->update([
            'name'=> $request->category_name,
            'description'=> $request->description,
            'company_id'=> $company_id,
        ]);
    }

    public function destroyScrap(Request $request)
    {
        if($request->id){
            TwitterScrap::where('id',$request->id)->first()->delete();
            return Response()->json(['code'=>200]);
        }
        return Response()->json(['code'=>204]);

    }

    public function destroyCategory(TwitterContent $twitterContent)
    {
        TwitterScrap::where('categoria_id',$twitterContent->id)->delete();
        $twitterContent->delete();
        return Response()->json(['code'=>200]);
    }


}
