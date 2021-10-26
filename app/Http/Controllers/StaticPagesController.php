<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Status;
use Auth;

class StaticPagesController extends Controller
{
    public function home()
    {
        $feed_items = [];
        //Auth::check() 来检查用户是否已登录
        if (Auth::check()) {
            $feed_items = Auth::user()->feed()->paginate(30);
        }
        return view('static_pages/home', compact('feed_items'));
    }
    public function help()
    {
        return view('static_pages/help');

    }
    public function about()
    {
        return view('static_pages/about');

    }
}
