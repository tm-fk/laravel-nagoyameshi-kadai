<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller

{
    public function index(Request $request) {

        $keyword = $request->input('keyword');
        if($keyword != null){
          $users = User::where('name', 'like', "%{$keyword}%")->paginate(15);
          $total = User::where('name', 'like', "%{$keyword}%")->count();
        }else{
          $users = User::paginate(15);
          $total = User::all()->count();
        }
        
    //ビューにデータを渡す
    return view('admin.users.index', compact('users','keyword','total'));
}

public function show(User $user) {

    //ビューにデータを渡す
    return view('admin.users.show', compact('user'));
}

}
