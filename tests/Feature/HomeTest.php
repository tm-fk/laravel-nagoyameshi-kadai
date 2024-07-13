<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Category;
use App\Models\Admin;
use App\Models\Restaurant;
use Illuminate\Support\Facades\Hash;

class HomeTest extends TestCase
{

    use RefreshDatabase, WithFaker;

    /**
     * A basic feature test example.
     */

    //未ログインのユーザーは会員側のトップページにアクセスできる
    public function test_unauthenticated_users_can_access_the_member_side_top_page(): void

    {
         $response = $this->get('/');

         $response->assertStatus(200);
    }

     //ログイン済みの一般ユーザーは会員側のトップページにアクセスできる。
     public function test_unauthenticated_general_user_can_access_member_side_top_page(): void
     {
          $user = User::factory()->create();

          $response = $this->actingAs($user)->get('/');

        $response->assertStatus(200);
    }

    //ログイン済みの管理者は会員側のトップページにアクセスできない。
    public function test_admin_cannot_access_member_home_page(): void 
    {
        $admin = new Admin();
        $admin->email = 'admin@example.com';
        $admin->password = Hash::make('nagoyameshi');
        $admin->save();

        $response = $this->actingAs($admin)->get('home');

        $response->assertStatus(404);
    }
     }

