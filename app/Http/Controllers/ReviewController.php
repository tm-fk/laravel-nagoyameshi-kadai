<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Review;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    public function index(Request $request , Restaurant $restaurant)
    {
        
        
            $sorts = [
                '掲載日が新しい順' => 'created_at desc',
            ];
            $sort_query = [];
            $sorted = "created_at desc";
        
            if ($request->has('select_sort')) {
                $slices = explode(' ', $request->input('select_sort'));
                $sort_query[$slices[0]] = $slices[1];
                $sorted = $request->input('select_sort');
            }
        
            if  (! $request->user()?->subscribed('premium_plan')) { 
                $reviews = Review::whereHas('restaurant', function($query) use ($restaurant){
                    $query->where('restaurants.id', $restaurant->id);
                    })->sortable($sort_query)->orderBy('created_at', 'desc')->limit(3)->get(); // ->get() を追加
            } else {
                $reviews = Review::whereHas('restaurant', function($query) use ($restaurant){
                    $query->where('restaurants.id', $restaurant->id);
                    })->sortable($sort_query)->orderBy('created_at', 'desc')->paginate(5);
            } 
        
            return view('reviews.index', compact('restaurant', 'reviews'));    
        }


     public function create(Restaurant $restaurant)
     {
         return view('reviews.create', compact('restaurant'));

     }   
     
     
     public function store(Request $request)
     { 
       $request->validate([
        'score' => 'required|numeric|between:1,5',
        'content' => 'required'
       ]);

       $reviews = new Review();
       $reviews->score = $request->input('score');
       $reviews->content = $request->input('content');
       $reviews->restaurnat_id = $request->input('restaurant_id');
       $reviews->user_id = $request->input('user_id');
       $reviews->save();

       return redirect()->route('reviews.index')->with('flash_message','レビューを投稿しました。');

     }


     public function edit(Restaurant $restaurant , Review $review , User $user , $id)
     {
           $user = Auth::user();

           if ($user_id !== Auth::id()) {
            return redirect()->route('reviews.index')->with('flash_message','不正なアクセスです。');
           } else {
            return view('reviews.edit', compact('restauranr','review'));
           }
     }


     public function update(Request $request , Review $review , User $user , $id)
     {
        $request->validate([
            'score' => 'required|numeric|between:1,5',
            'content' => 'required'
           ]);
    
           $user=Auth::user();

           if ($user->id !== Auth::id()) {
               return redirect()->route('reviews.index')->with('error_message', '不正なアクセスです。');
           } else {
               $reviews = Review::find($id);
               $reviews->update(['score' => $request->input('score')]);
               $reviews->update(['content' => $request->input('content')]);
               $reviews->update(['restaurant_id' => $request->input('restaurant_id')]);
               $reviews->update(['user_id' => $request->input('user_id')]);
               $reviews->save();
   
               return redirect()->route('reviews.index')->with('flash_message', 'レビューを編集しました。');
           }

     }


     public function destroy(Review $review , User $user , $id) 
     {
           $user = Auth::user();

           if ($user->id !== Auth::id()) {
            return redirect()->route('reviews.index')->with('error_message', '不正なアクセスです。');
        } else {
            $reviews = Review::find($id);
            $reviews->delete();

            return redirect()->route('reviews.index')->with('flash_message', 'レビューを削除しました。');
        }    
     }



    
}
