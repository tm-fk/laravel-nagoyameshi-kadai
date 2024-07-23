<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Restaurant;



class FavoriteController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();

        $favorite_restaurants =  $user->favorite_restaurants()->orderBy('created_at', 'desc')->paginate(15);
        
        return view('favorites.index', compact('favorite_restaurants'));
    }


    public function store($restaurant_id)
    {
        Auth::user()->favorite_restaurants()->attach($restaurant_id);

        return back()->with('flash_message','お気に入り追加しました');
    }

    public function destroy($restaurant_id)
    {
        Auth::user()->favorite_restaurants()->detach($restaurant_id);

        return back()->with('flash_message','お気に入りを解除しました。');
    }
    }

