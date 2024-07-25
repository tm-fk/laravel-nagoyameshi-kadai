<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    public function index() {


// ユーザー総数を取得
$total_users = DB::table('users')->count();

// 有料会員数を取得
$total_premium_users = DB::table('subscriptions')
    ->where('stripe_status', 'active')
    ->count();

// 無料会員数を計算
$total_free_users = $total_users - $total_premium_users;

// レストラン総数を取得
$total_restaurants = DB::table('restaurants')->count();

// 予約総数を取得
$total_reservations = DB::table('reservations')->count();

// 月間売上を計算
$sales_for_this_month = 300 * $total_premium_users;


        return view('admin.home', compact(
            'total_users',
            'total_premium_users',
            'total_free_users',
            'total_restaurants',
            'total_reservations',
            'sales_for_this_month'
        ));
    }
}
