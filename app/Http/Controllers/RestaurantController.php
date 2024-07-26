<?php

namespace App\Http\Controllers;

use App\Models\Restaurant;
use App\Models\Category;
use Illuminate\Http\Request;


class RestaurantController extends Controller
{
    public function index(Request $request)
    {


        $keyword = $request->keyword;
        $category_id = $request->category_id;
        $price = $request->price;
        $sort = $request->input('sort', 'created_at desc');
        
        $sorts = [
            '掲載日が新しい順' => 'created_at desc',
            '価格が安い順' => 'lowest_price asc',
            '評価が高い順' => 'rating desc',
            '予約が多い順' => 'reservations_count desc', // 予約が多い順
        ];
        
        $sort_query = [];
        $sorted = "created_at desc";
        
        if ($request->has('select_sort')) {
            $slices = explode(' ', $request->input('select_sort'));
            $sort_query[$slices[0]] = $slices[1];
            $sorted = $request->input('select_sort');
        }
        
        $query = Restaurant::query();
        
        if ($keyword !== null) {
            $query = $query->where(function($q) use ($keyword) {
                $q->whereHas('categories', function ($query) use ($keyword) {
                    $query->where('categories.name', 'like', "%{$keyword}%");
                })
                ->orWhere('address', 'like', "%{$keyword}%")
                ->orWhere('name', 'like', "%{$keyword}%");
            });
        }
        if ($category_id !== null) {
            $query = $query->whereHas('categories', function ($query) use ($category_id) {
                $query->where('categories.id', $category_id);
            });
        }
        if ($price !== null) {
            $query = $query->where('lowest_price', '<=', $price);
        }
        
        // ソートの適用
        $query = $query->withCount('reservation');
if (isset($sort_query['reservation_count'])) {
    $query = $query->orderBy('reservation_count', $sort_query['reservations_count']);
} elseif (!empty($sort_query)) {
    $query = $query->sortable($sort_query);
}

        $restaurants = $query->paginate(15);
        $total = $restaurants->total();
        
        $categories = Category::all();

       return view('restaurants.index', compact('keyword', 'category_id', 'price', 'sorts', 'sorted', 'restaurants', 'categories', 'total'));
    }

    public function show(Restaurant $restaurant)
    {
        return view('restaurants.show', compact('restaurant'));
    }

}