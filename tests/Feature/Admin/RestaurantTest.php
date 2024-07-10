<?php

namespace Tests\Feature\Admin;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Admin;
use App\Models\Restaurant;
use App\Models\Category;
use App\Models\RegularHoliday;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use App\Providers\RouteServiceProvider;


class RestaurantTest extends TestCase
{



   
     //未ログインのユーザーは管理者側の店舗一覧ページにアクセスできない
     public function test_unauthenticated_user_cannnot_access_admin_restaurant_list_page(): void 
     {
         $response = $this->get('admin.restaurants.index');
 
         $response->assertStatus(404);
     }
     
     // ログイン済みの一般ユーザーは管理者側の店舗一覧ページにアクセスできない
     public function test_authenticated_regular_user_cannot_access_admin_restaurant_list_page(): void
     {
         $user = User::factory()->create();
 
         $response = $this->actingAs($user)->get('admin.restaurants.index');
 
         $response->assertStatus(404);
     }
 
     // ログイン済みの管理者は管理者側の店舗一覧ページにアクセスできる
     public function test_authenticated_admin_can_access_admin_restaurant_list_page(): void
     {
         $admin = new Admin();
         $admin->email = 'admin@example.com';
         $admin->password = Hash::make('nagoyameshi');
         $admin->save();
 
         $restaurant = Restaurant::factory()->create();

         // 管理者としてログイン
        $this->actingAs($admin);
    
        // 会員の詳細ページにアクセス
        $response = $this->get(route('admin.restaurants.index', ['restaurant' => $restaurant]));
    
        // リダイレクトされることを確認
        $response->assertStatus(200);
    }

     // 未ログインのユーザーは管理者側の店舗詳細ページにアクセスできない
     public function test_unauthenticated_user_cannnot_access_admin_restaurant_detail_page(): void
     {
         $response = $this->get('admin.restaurants.show');
 
         $response->assertStatus(404);
     }
 
     // ログイン済みの一般ユーザーは管理者側の店舗詳細ページにアクセスできない
     public function test_authenticated_regular_user_cannot_access_admin_restaurant_detail_page(): void
     {
         $user = User::factory()->create();
 
         $response = $this->actingAs($user)->get('admin.restaurants.show');
 
         $response->assertStatus(404);
     }
 
     // ログイン済みの管理者は管理者側の店舗詳細ページにアクセスできる
     public function test_authenticated_admin_can_access_admin_restaurant_detail_page(): void
     {
         $admin = new Admin();
         $admin->email = 'admin@example.com';
         $admin->password = Hash::make('nagoyameshi');
         $admin->save();
 
         $restaurant = Restaurant::factory()->create();
     
         // 管理者としてログイン
         $this->actingAs($admin);
     
         // 店舗の詳細ページにアクセス
         $response = $this->get(route('admin.restaurants.show', ['restaurant' => $restaurant]));
     
         // リダイレクトされることを確認
         $response->assertStatus(200);
     }

     // 未ログインのユーザーは管理者側の店舗登録ページにアクセスできない
    public function test_unauthenticated_user_cannnot_access_admin_restaurant_register_page(): void 
    {
        $response = $this->get('admin.restaurants.create');

        $response->assertStatus(404);
    }

    // ログイン済みの一般ユーザーは管理者側の店舗登録ページにアクセスできない
    public function test_authenticated_regular_user_cannot_access_admin_restaurant_register_page(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('admin.restaurants.create');

        $response->assertStatus(404);
    }

    // ログイン済みの管理者は管理者側の店舗登録ページにアクセスできる
    public function test_authenticated_admin_can_access_admin_restaurant_register_page(): void
    {
        $admin = new Admin();
        $admin->email = 'admin@example.com';
        $admin->password = Hash::make('nagoyameshi');
        $admin->save();

        $restaurant = Restaurant::factory()->create();
    
        // 管理者としてログイン
        $this->actingAs($admin);
    
        // 会員の詳細ページにアクセス
        $response = $this->get(route('admin.restaurants.create', ['restaurant' => $restaurant]));
    
        // リダイレクトされることを確認
        $response->assertStatus(200);
    }


    public function test_unauthenticated_users_cannot_store_restaurant()
    {
        $response = $this->post(route('admin.restaurants.store'), [
            'name' => 'テスト',
            'description' => 'テスト',
            'lowest_price' => 1000,
            'highest_price' => 5000,
            'postal_code' => '0000000',
            'address' => 'テスト',
            'opening_time' => '10:00:00',
            'closing_time' => '20:00:00',
            'seating_capacity' => 50,
        ]);

        $response->assertStatus(419); // CSRFトークンエラーを確認
        $this->assertDatabaseMissing('restaurants', ['name' => 'テスト']); // データが保存されていないことを確認

    
 }

 public function test_authenticated_general_users_cannot_store_restaurant()
    {
        $user = User::factory()->create(); // 一般ユーザーを作成

        $response = $this->actingAs($user)->post(route('admin.restaurants.store'), [
            'name' => 'テスト',
            'description' => 'テスト',
            'lowest_price' => 1000,
            'highest_price' => 5000,
            'postal_code' => '0000000',
            'address' => 'テスト',
            'opening_time' => '10:00:00',
            'closing_time' => '20:00:00',
            'seating_capacity' => 50,
        ]);

        $response->assertStatus(419); // 403 Forbidden を確認
        $this->assertDatabaseMissing('restaurants', ['name' => 'テスト']); // データが保存されていないことを確認


    }

 // ログイン済みの管理者は管理者側の店舗編集ページにアクセスできる
 public function test_authenticated_admin_can_access_admin_restaurant_edit_page(): void
 {
     $admin = new Admin();
     $admin->email = 'admin@example.com';
     $admin->password = Hash::make('nagoyameshi');
     $admin->save();

     $restaurant = Restaurant::factory()->create();
 
     // 管理者としてログイン
     $this->actingAs($admin);
 
     // 店舗の編集ページにアクセス
     $response = $this->get(route('admin.restaurants.edit', ['restaurant' => $restaurant]));
 
     // リダイレクトされることを確認
     $response->assertStatus(200);
 }



 // 未ログインのユーザーは店舗を削除できない
 public function test_unauthenticated_user_cannnot_delete_restaurant(): void
 {
     $restaurant = Restaurant::factory()->create();
     $data = $restaurant->toArray();

     unset($data['created_at'], $data['updated_at']);

     $response = $this->delete(route('admin.restaurants.destroy', $restaurant), $data);

     $this->assertDatabaseHas('restaurants', $data);
     $response->assertRedirect(route('admin.login'));
 }

 // ログイン済みの一般ユーザーは店舗を削除できない
 public function test_authenticated_regular_user_cannot_delete_restaurant(): void
 {
     $user = User::factory()->create();

     $restaurant = Restaurant::factory()->create();
     $data = $restaurant->toArray();

     unset($data['created_at'], $data['updated_at']);

     $response = $this->actingAs($user)->delete(route('admin.restaurants.destroy', $restaurant), $data);

     $this->assertDatabaseHas('restaurants', $data);
     $response->assertRedirect(route('admin.login'));
 }

 // ログイン済みの管理者は店舗を削除できる
 public function test_authenticated_admin_can_delete_restaurant(): void
 {
     $admin = new Admin();
     $admin->email = 'admin@example.com';
     $admin->password = Hash::make('nagoyameshi');
     $admin->save();

     $restaurant = Restaurant::factory()->create();
     $data = $restaurant->toArray();

     $response = $this->actingAs($admin)->delete(route('admin.restaurants.destroy', $restaurant), $data);

     $this->assertDatabaseMissing('restaurants', $data);
     $response->assertStatus(302);
 }


            // 未ログインのユーザーは店舗を登録できない
            public function test_unauthenticated_user_cannnot_register_restaurant(): void
            {
                $dayOff = RegularHoliday::factory()->create();
        
                $categoryIds = [];
                for ($i = 1; $i <= 3; $i++) {
                    $category = Category::create([
                        'name' => 'カテゴリ' . $i
                    ]);
                    array_push($categoryIds, $category->id);    
                }
        
                $restaurant = [
                    'name' => 'テスト',
                    'description' => 'テスト',
                    'lowest_price' => 1000,
                    'highest_price' => 5000,
                    'postal_code' => '0000000',
                    'address' => 'テスト',
                    'opening_time' => '10:00:00',
                    'closing_time' => '20:00:00',
                    'day' => $dayOff,
                    'seating_capacity' => 50,
                    'category_ids' => $categoryIds,
                ];
        
                $response = $this->post(route('admin.restaurants.store'), $restaurant);
                unset($restaurant['category_ids'], $restaurant['day']);
        
                $response->assertRedirect(route('admin.login'));
            }
        
        
            // ログイン済みの一般ユーザーは店舗を登録できない
            public function test_authenticated_regular_user_cannot_register_restaurant(): void
            {
                $user = User::factory()->create();
                $this->actingAs($user);
        
                $dayOff = RegularHoliday::factory()->create();
        
                $categoryIds = [];
                for ($i = 1; $i <= 3; $i++) {
                    $category = Category::create([
                        'name' => 'カテゴリ' . $i
                    ]);
                    array_push($categoryIds, $category->id);    
                }
        
                $restaurant = [
                    'name' => 'テスト',
                    'description' => 'テスト',
                    'lowest_price' => 1000,
                    'highest_price' => 5000,
                    'postal_code' => '0000000',
                    'address' => 'テスト',
                    'opening_time' => '10:00:00',
                    'closing_time' => '20:00:00',
                    'day' => $dayOff,
                    'seating_capacity' => 50,
                    'category_ids' => $categoryIds,
                ];
        
                $response = $this->post(route('admin.restaurants.store'), $restaurant);
                unset($restaurant['category_ids'], $restaurant['day']);
        
                $response->assertRedirect(route('admin.login'));
            }
        
            // ログイン済みの管理者は店舗を登録できる
            public function test_authenticated_admin_can_register_restaurant(): void
            {
                $admin = new Admin();
                $admin->email = 'admin@example.com';
                $admin->password = Hash::make('nagoyameshi');
                $admin->save();
                $this->actingAs($admin);
        
                $categoryIds = [];
                for ($i = 1; $i <= 3; $i++) {
                    $category = Category::create([
                        'name' => 'カテゴリ' . $i
                    ]);
                    array_push($categoryIds, $category->id);    
                }
        
                $dayOff = RegularHoliday::factory()->create();
        
                $restaurant = [
                    'name' => 'テスト',
                    'description' => 'テスト',
                    'lowest_price' => 1000,
                    'highest_price' => 5000,
                    'postal_code' => '0000000',
                    'address' => 'テスト',
                    'opening_time' => '10:00:00',
                    'closing_time' => '20:00:00',
                    'day' => $dayOff,
                    'seating_capacity' => 50,
                    'category_ids' => $categoryIds,
                ];
        
                $response = $this->post(route('admin.restaurants.store'), $restaurant);
        
                unset($restaurant['day'],$restaurant['category_ids'],$restaurant['updated_at'],$restaurant['created_at']);
                $this->assertDatabaseHas('restaurants', $restaurant);
                
                $response->assertRedirect(route('admin.restaurants.index'));
            }
        
        


   // 未ログインのユーザーは店舗を更新できない
   public function test_unauthenticated_user_cannnot_update_restaurant(): void
   {
       $categoryIds = [];
       for ($i = 1; $i <= 3; $i++) {
           $category = Category::create([
               'name' => 'カテゴリ' . $i
           ]);
           array_push($categoryIds, $category->id);    
       }

       $old_restaurant = Restaurant::factory()->create();
       $new_restaurant = [
           'name' => 'テスト',
           'description' => 'テスト',
           'lowest_price' => 1000,
           'highest_price' => 5000,
           'postal_code' => '0000000',
           'address' => 'テスト',
           'opening_time' => '10:00:00',
           'closing_time' => '20:00:00',
           'seating_capacity' => 50,
           'category_ids' => $categoryIds,
       ];

       $response = $this->patch(route('admin.restaurants.update', $old_restaurant), $new_restaurant);

       $response->assertRedirect(route('admin.login'));
   }

   // ログイン済みの一般ユーザーは店舗を更新できない
   public function test_authenticated_regular_user_cannot_update_restaurant(): void
   {
       $user = User::factory()->create();
       $this->actingAs($user);

       $categoryIds = [];
       for ($i = 1; $i <= 3; $i++) {
           $category = Category::create([
               'name' => 'カテゴリ' . $i
           ]);
           array_push($categoryIds, $category->id);    
       }

       $old_restaurant = Restaurant::factory()->create();
       $new_restaurant = [
           'name' => 'テスト',
           'description' => 'テスト',
           'lowest_price' => 1000,
           'highest_price' => 5000,
           'postal_code' => '0000000',
           'address' => 'テスト',
           'opening_time' => '10:00:00',
           'closing_time' => '20:00:00',
           'seating_capacity' => 50,
           'category_ids' => $categoryIds,
       ];

       $response = $this->patch(route('admin.restaurants.update', $old_restaurant), $new_restaurant);

       $response->assertRedirect(route('admin.login'));
   }

   // ログイン済みの管理者は店舗を更新できる
   public function test_authenticated_admin_can_update_restaurant(): void
   {
       $admin = new Admin();
       $admin->email = 'admin@example.com';
       $admin->password = Hash::make('nagoyameshi');
       $admin->save();
       $this->actingAs($admin);

       $categoryIds = [];
       for ($i = 1; $i <= 3; $i++) {
           $category = Category::create([
               'name' => 'カテゴリ' . $i
           ]);
           array_push($categoryIds, $category->id);    
       }

       $old_restaurant = Restaurant::factory()->create();
       $new_restaurant = [
           'name' => 'テスト',
           'description' => 'テスト',
           'lowest_price' => 1000,
           'highest_price' => 5000,
           'postal_code' => '0000000',
           'address' => 'テスト',
           'opening_time' => '10:00:00',
           'closing_time' => '20:00:00',
           'seating_capacity' => 50,
           'category_ids' => $categoryIds,
       ];

       $response = $this->patch(route('admin.restaurants.update', $old_restaurant), $new_restaurant);

       unset($new_restaurant['category_ids']);
       $this->assertDatabaseHas('restaurants', $new_restaurant);

       $response->assertStatus(302);
   }

 
 }