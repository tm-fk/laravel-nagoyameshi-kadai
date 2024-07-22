<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Review;
use Illuminate\Support\Facades\Auth;
use App\Models\Restaurant;
use App\Models\User;

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
                    $reviews = Review::where('restaurant_id', $restaurant->id)->orderBy('created_at', 'desc')->take(3)->get();

                } else {
                    $reviews = Review::whereHas('restaurant', function($query) use ($restaurant) {
                        $query->where('restaurants.id', $restaurant->id);
                    });
            
                    if (!empty($sort_query)) {
                        foreach ($sort_query as $column => $direction) {
                            $reviews = $reviews->orderBy($column, $direction);
                        }
                    } else {
                        $reviews = $reviews->orderBy('created_at', 'desc');
                    }
            
                    $reviews = $reviews->paginate(5);
                }
            return view('reviews.index', compact('restaurant', 'reviews'));    
        }


     public function create(Restaurant $restaurant)
     {
         return view('reviews.create', compact('restaurant'));

     }   
     
     
     public function store(Request $request, Restaurant $restaurant)
     { 
       $request->validate([
        'score' => 'required|numeric|between:1,5',
        'content' => 'required',
       ]);

       $reviews = new Review();
       $reviews->score = $request->input('score');
       $reviews->content = $request->input('content');
       $reviews->restaurant_id = $restaurant->id;
       $reviews->user_id = Auth::id();
       $reviews->save();



       return redirect()->route('restaurants.reviews.index', $restaurant->id)->with('flash_message','レビューを投稿しました。');

     }


     public function edit(Restaurant $restaurant , Review $review, User $user)
     {
           $user = Auth::user();
           $user_id = $user->id;

           if ($user_id !== Auth::id()) {
            return redirect()->route('reviews.index')->with('flash_message','不正なアクセスです。');
           } else {
            return view('reviews.edit', compact('restaurant','review'));
           }
     }


     public function update(Request $request, Restaurant $restaurant, Review $review)
     {
        $request->validate([
            'score' => 'required|numeric|between:1,5',
            'content' => 'required'
           ]);
    
           $user= Auth::user();

           if ($review->user_id !== $user->id) {
            return redirect()->route('reviews.index')->with('error_message', '不正なアクセスです。');
        } 
    
        $review->update([
            'score' => $request->input('score'),
            'content' => $request->input('content')
        ]);
    
        return redirect()->route('restaurants.reviews.index', $restaurant->id)->with('flash_message', 'レビューを編集しました。');
    }
    
     


     public function destroy(Request $request, Restaurant $restaurant, Review $review) 
     {
           $user = Auth::user();

           if ($review->user_id !== $user->id) {
            return redirect()->route('restaurants.reviews.index', $restaurant->id)->with('error_message', '不正なアクセスです。');
        

        } else {
            $review->delete();

            return redirect()->route('restaurants.reviews.index', $restaurant->id)->with('flash_message', 'レビューを削除しました。');
        }    
     }



    
}
