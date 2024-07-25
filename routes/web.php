<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Admin\RestaurantController as AdminRestaurantController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\CompanyController as AdminCompanyController;
use App\Http\Controllers\Admin\TermController as AdminTermController;
use App\Http\Controllers\RestaurantController;
use App\Http\Controllers\SubscriptionController;
use App\Http\Middleware\Subscribed;
use App\Http\Middleware\NotSubscribed;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\ReservationController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\TermController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Route::get('/', function () {
//     return view('welcome');
// });

// Route::get('/dashboard', function () {
//     return view('dashboard');
// })->middleware('auth');




require __DIR__.'/auth.php';

Route::group(['prefix' => 'admin', 'as' => 'admin.', 'middleware' => 'auth:admin'], function () {
    Route::get('home', [Admin\HomeController::class, 'index'])->name('home');
    Route::get('admin/index', [Admin\UserController::class, 'index'])->name('users.index');
    Route::get('admin/show={user}', [Admin\UserController::class, 'show'])->name('users.show');
//Restaunrant
Route::resource('restaurants', Admin\RestaurantController::class);

//Category
Route::resource('categories', Admin\CategoryController::class)->only(['index', 'store', 'update', 'destroy']);

//Company
Route::resource('company', Admin\CompanyController::class)->only(['index', 'edit', 'update']);

//Term
Route::resource('terms', Admin\TermController::class)->only(['index', 'edit', 'update']);

});



Route::middleware(['auth'])->group(function () {
    Route::get('/member-list', 'MemberController@index')->name('member.list');
});

// Route::controller(RestaurantController::class)->group(function () {
//     Route::get('/admin/restaurants/index', 'index')->name('admin.restaurants.index');
//     Route::get('/admin/restaurants/show/{restaurant}', 'show')->name('admin.restaurants.show');
//     Route::get('/admin/restaurants/edit/{restaurant}', 'edit')->name('admin.restaurants.edit');
//     Route::get('/admin/restaurants/create', 'create')->name('admin.restaurants.create');
//      Route::post('/admin/restaurants/store', 'store')->name('admin.restaurants.store');
//     Route::delete('/admin/restaurants/destroy/{restaurant}', 'destroy')->name('admin.restaurants.destroy');
//     Route::patch('/admin/restaurants/update/{restaurant}', 'update')->name('admin.restaurants.update');
// });


// Route::resource('admin/categories', CategoryController::class)->only(['index', 'store', 'update', 'destroy'])->names('admin.categories');

// Route::prefix('admin/company')->middleware('auth:admin')->group(function () {
//     Route::get('/index', [CompanyController::class, 'index'])->name('admin.company.index');
//     Route::get('/edit', [CompanyController::class, 'edit'])->name('admin.company.edit');
//     Route::patch('/edit', [CompanyController::class, 'update'])->name('admin.company.update');
// });

// Route::prefix('admin/terms')->middleware('auth:admin')->group(function () {
//     Route::get('/index', [TermController::class, 'index'])->name('admin.terms.index');
//     Route::get('/edit', [TermController::class, 'edit'])->name('admin.terms.edit');
//     Route::patch('/edit', [TermController::class, 'update'])->name('admin.terms.update');
// });


Route::group(['middleware' => 'guest:admin'], function () {
    Route::get('/', [HomeController::class, 'index'])->name('home');
    Route::resource('restaurants', RestaurantController::class)->only(['index', 'show'])->names('restaurants');
    Route::resource('restaurants.reviews', ReviewController::class)->only(['index']);
    
});

Route::get('company', [CompanyController::class, 'index'])->name('company.index');

Route::get('terms', [TermController::class, 'index'])->name('terms.index');

Route::group(['middleware' => ['auth', 'verified']], function () {
    Route::resource('user', UserController::class)->only(['index', 'edit', 'update']);

    Route::resource('restaurants.reviews', ReviewController::class)->only(['index']);



//一般ユーザとしてログイン済かつメール認証済で有料プラン未登録の場合
Route::group(['middleware' => [NotSubscribed::class]], function () {
    Route::get('subscription/create', [SubscriptionController::class, 'create'])->name('subscription.create');
    Route::post('subscription', [SubscriptionController::class, 'store'])->name('subscription.store');
});
//一般ユーザとしてログイン済かつメール認証済で有料プラン登録済の場合
Route::group(['middleware' => [Subscribed::class]], function () {
    Route::get('subscription/edit', [SubscriptionController::class, 'edit'])->name('subscription.edit');
    Route::patch('subscription', [SubscriptionController::class, 'update'])->name('subscription.update');
    Route::get('subscription/cancel', [SubscriptionController::class, 'cancel'])->name('subscription.cancel');
    Route::delete('subscription', [SubscriptionController::class, 'destroy'])->name('subscription.destroy');

    Route::resource('restaurants.reviews', ReviewController::class)->only(['create', 'store', 'edit', 'update', 'destroy']);

    Route::get('reservations', [ReservationController::class, 'index'])->name('reservations.index');
    Route::get('restaurants/{restaurant}/reservations/create', [ReservationController::class, 'create'])->name('restaurants.reservations.create');
    Route::post('restaurants/{restaurant}/reservations', [ReservationController::class, 'store'])->name('restaurants.reservations.store');
    Route::delete('reservations/{reservation}', [ReservationController::class, 'destroy'])->name('reservations.destroy');
  

    Route::get('/favorites',[FavoriteController::class, 'index'])->name('favorites.index');
    Route::post('/favorites/{restaurant_id}',[FavoriteController::class, 'store'])->name('favorites.store');
    Route::delete('/favorites/{restaurant_id}',[FavoriteController::class, 'destroy'])->name('favorites.destroy');
    
});

});