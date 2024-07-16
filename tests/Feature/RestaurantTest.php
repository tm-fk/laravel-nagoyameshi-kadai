<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Admin;
use Illuminate\Support\Facades\Hash;
use App\Models\Restaurant;
use App\Models\Category;

class RestaurantTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    // public function test_example(): void
    // {
    //     $response = $this->get('/');

    //     $response->assertStatus(200);
    // }

    use Refreshdatabase;


    //未ログインのユーザーは会員側の店舗一覧ページにアクセスできる
    public function  test_unauthenticated_user_can_access_member_store_page() {

        $response = $this->get(route('restaurants.index'));

        $response->assertStatus(200);
    }


      //ログイン済みの一般ユーザーは会員側の店舗一覧ページにアクセスできる
      public function test_authenticated_user_can_access_member_store_page(): void
      {
          $user = User::factory()->create();
  
          $response = $this->actingAs($user)->get(route('restaurants.index'));
  
          $response->assertStatus(200);
      }

       //ログイン済みの管理者は会員側の店舗一覧ページにアクセスできない
    public function test_admin_cannot_access_member_store_page(): void
    {
        $admin = new Admin();
        $admin->email = 'admin@example.com';
        $admin->password = Hash::make('nagoyameshi');
        $admin->save();
     
        $response = $this->post('admin/login', [
            'email' => $admin->email,
            'password' => 'nagoyameshi',
        ]);
        
        $response = $this->actingAs($admin)->get(route('restaurants.index'));

        $response->assertStatus(302);
        $response->assertRedirect('admin/home');
    }


        //未ログインのユーザーは会員側の店舗詳細ページにアクセスできる
        public function  test_unauthenticated_user_can_access_member_store_detail_page() {

            $restaurant = Restaurant::factory()->create();

            $response = $this->get(route('restaurants.show', ['restaurant' => $restaurant]));
    
            $response->assertStatus(200);
        }


              //ログイン済みの一般ユーザーは会員側の店舗詳細ページにアクセスできる
      public function test_authenticated_user_can_access_member_store_detail_page(): void
      {
          $user = User::factory()->create();

          $restaurant = Restaurant::factory()->create();
  
          $response = $this->actingAs($user)->get(route('restaurants.show',['restaurant' => $restaurant]));
  
          $response->assertStatus(200);
      }


      public function test_admin_cannot_access_member_store_detail_page(): void
      {
          $admin = new Admin();
          $admin->email = 'admin@example.com';
          $admin->password = Hash::make('nagoyameshi');
          $admin->save();
       
          $response = $this->post('admin/login', [
              'email' => $admin->email,
              'password' => 'nagoyameshi',
          ]);

          $restaurant = Restaurant::factory()->create();
          
          $response = $this->actingAs($admin)->get(route('restaurants.show',['restaurant' => $restaurant]));
  
          $response->assertStatus(302);
          $response->assertRedirect('admin/home');
      }
  

    
}
