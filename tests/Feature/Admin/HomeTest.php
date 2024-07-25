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

class HomeTest extends TestCase
{
    
  use RefreshDatabase;

   // 未ログインのユーザーは管理者側のトップページにアクセスできない
    public function test_unauthenticated_user_cannot_access_admin_toppage()
{
    $responce = $this->get('admin.home.index');

    $responce->assertStatus(404);
}
    // ログイン済みの一般ユーザーは管理者側のトップページにアクセスでない
    public function test_authenticated_regular_user_can_not_admin_toppage()
{
    $user = User::factory()->create();
 
         $response = $this->actingAs($user)->get('admin.home.index');
 
         $response->assertStatus(404);
}
    //  ログイン済みの管理者は管理者側のトップページにアクセスできる
    public function test_authenticated_regular_user_can_admin_tpopage()
{
    $admin = new Admin();
    $admin->email = 'admin@example.com';
    $admin->password = Hash::make('nagoyameshi');
    $admin->save();

    $response = $this->actingAs($admin)->get('home');

        $response->assertStatus(404);
}
}
