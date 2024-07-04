<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller

{
    public function index(Request $request) {

    //検索キーワードの取得
    $keyword = $request->input('keyword');

    //クエリの作成
    $query = User::query();

    //検索条件の適用
    if ($keyword) {
        $query->where(function($q) use ($keyword) {
            $q->where('name','LIKE',"%{$keyword}%")
             ->orWhere('kane','LIKE',"%{$keyword}%");
        });
    }

    //ページネーションの適用
    $users = $query->paginate(10);

    //取得したデータの総数
    $total = $users->total();

    //ビューにデータを渡す
    return view('admin.users.index', compact('users','keyword','total'));
}

public function show(User $user) {

    //ビューにデータを渡す
    return view('admin.users.show', compact('user'));
}

}
