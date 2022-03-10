<?php

namespace App\Http\Controllers\Facebook;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ScrapCommentsController extends Controller
{
    public function index()
    {
        return view('Facebook.Scrapcomments.index');
    }
}
