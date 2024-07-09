<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;

class CategoryController extends Controller
{
    public function index(Request $request)
{

    $keyword = $request->keyword;

    if ($keyword !== null) {
       $categories = Category::where('name', 'like', "%{$keyword}%")->paginate(15);
       $total = $categories->total();
    } else {
       $categories = Category::paginate(15);
       $total = Category::all()->count();
    }

    return view('admin.categories.index', compact('categories', 'keyword', 'total'));
}

    public function store(Request $request) {
    $request->validate([
        'name' => 'required',
    ]);

    $category = new Category();

    $category->name = $request->input('name');

    $category->save();

    return redirect()->route('admin.categories.index', $category)->with('flash_message', 'カテゴリを登録しました。');
}


    public function update(Request $request, Category $category) {
        $request->validate([
            'name' => 'required',
        ]);

        $category->name = $request->input('name');

        $category->update();

        return redirect()->route('admin.categories.index', $category)->with('flash_message', 'カテゴリを編集しました。');
    }


    public function destroy(Category $category) {

        $category->delete();

        return redirect()->route('admin.categories.index', compact('category'))->with('flash_message', 'カテゴリを削除しました。');
    }
}
