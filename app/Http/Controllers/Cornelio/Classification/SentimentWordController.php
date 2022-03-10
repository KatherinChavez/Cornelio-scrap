<?php

namespace App\Http\Controllers\Cornelio\Classification;

use App\Http\Controllers\Controller;
use App\Http\Requests\CompaniesCreate;
use App\Http\Requests\CompaniesEdit;
use App\Models\App_Fb;
use App\Models\Company;
use App\Models\Word;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SentimentWordController extends Controller
{
    public function index(Request $request)
    {
        if($request->search){
            $word = Word::where('word', 'like', '%' . $request->search . '%')
                ->orWhere('sentiment', 'like', '%' . $request->search . '%')
                ->paginate();

        }else{
            $word = Word::paginate();
        }
        return view('Cornelio.Classification.Word.index', compact('word'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'word' => 'required',
            'sentiment' => 'required',
        ]);
        $user_id = Auth::id();
        $compain = Company::where('slug', session('company'))->first();
        $companies = $compain->id;

        $word = Word::create([
            'word' => $request->word,
            'sentiment' => $request->sentiment,
        ]);

        return redirect()->route('SentimentWord.index')->with('info','Registro actualizado con éxito');

    }

    public function edit(Request $request)
    {
        $word = Word::where('id', $request->id_word)->get();
        return $word;
    }

    public function update(Request $request)
    {
        $request->validate([
            'word_edit' => 'required',
            'sentiment_edit' => 'required',
        ]);

        $word = Word::where('id', $request->id_word)->first();
        $word->update([
            'word' => $request->word_edit,
            'sentiment' => $request->sentiment_edit,
        ]);
        return redirect()->route('SentimentWord.index')->with('info','Se ha actualizado exitosamente');
    }

    public function destroy(Word $word)
    {
        $word->delete();
        return redirect()->route('SentimentWord.index')->with('info', 'Eliminada correctamente');
    }
}
