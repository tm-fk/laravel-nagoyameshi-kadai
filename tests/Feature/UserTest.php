<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Admin;
use Illuminate\Support\Facades\Hash;


class UserTest extends TestCase
{

    use RefreshDatabase;

   
   //未ログインのユーザーは会員側の会員情報ページにアクセスできない
    public function test_can_not_access_member_user_info_page() {

    $response = $this->get('users.index');

    $response->assertStatus(404);
    }

     //ログイン済みの一般ユーザーは会員側の会員情報ページにアクセスできる
     public function test_authenticated_regular_user_can_access_user_member_list_page() {

        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('user.index'));

        $response->assertStatus(200);
     }

    //ログイン済みの管理者は会員側の会員情報ページにアクセスできない
    public function test_authenticated_admin_cannot_access_user_member_list_page(): void
    {
        $admin = new Admin();
        $admin->email = 'admin@example.com';
        $admin->password = Hash::make('nagoyameshi');
        $admin->save();
     
        $response = $this->post('admin/login', [
            'email' => $admin->email,
            'password' => 'nagoyameshi',
        ]);
        
        $response = $this->actingAs($admin)->get(route('user.index'));

        $response->assertStatus(302);
        $response->assertRedirect('admin/home');
    }

    // 未ログインのユーザーは会員側の会員情報編集ページにアクセスできない
    public function test_unauthenticated_user_cannnot_access_user_edit_page(): void 
    {
        $response = $this->get(route('user.edit', ['user' => 99999]));

        $response->assertStatus(302);
        $response->assertRedirect('/login');
    }

    // ログイン済みの一般ユーザーは会員側の他人の会員情報編集ページにアクセスできない
    public function test_authenticated_regular_user_cannot_access_another_user_edit_page(): void
    {
        $user = User::factory()->create();
        $another_user = User::factory()->create();
        
        $response = $this->actingAs($another_user)->get(route('user.edit', ['user' => $user->id]));

        $response->assertStatus(302);
        $response->assertRedirect('/user');
    }

    // ログイン済みの一般ユーザーは会員側の自身の会員情報編集ページにアクセスできる
    public function test_authenticated_regular_user_can_access_user_edit_page(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('user.edit', ['user' => $user->id]));

        $response->assertStatus(200);
    }

    // ログイン済みの管理者は会員側の会員情報編集ページにアクセスできない
    public function test_authenticated_admin_cannot_access_user_edit_page(): void
    {
        $admin = new Admin();
        $admin->email = 'admin@example.com';
        $admin->password = Hash::make('nagoyameshi');
        $admin->save();
     
        $response = $this->post('admin/login', [
            'email' => $admin->email,
            'password' => 'nagoyameshi',
        ]);
        
        $response = $this->actingAs($admin)->get(route('user.edit', ['user' => $admin->id]));

        $response->assertStatus(404);
    }

    // 未ログインのユーザーは会員情報を更新できない
    public function test_unauthenticated_user_cannot_update_user_profile(): void
    {
        $response = $this->patch(route('user.update', ['user' => 99999]));

        $response->assertStatus(302);
        $response->assertRedirect('/login');
    }

    // ログイン済みの一般ユーザーは他人の会員情報を更新できない
    public function test_authenticated_regular_user_cannot_update_other_user_profile(): void
    {
        $user = User::factory()->create();
        $another_user = User::factory()->create();
        
        $response = $this->actingAs($another_user)->patch(route('user.update', ['user' => $user->id]));

        $response->assertStatus(302);
        $response->assertRedirect('/');
    }

    // ログイン済みの一般ユーザーは自身の会員情報を更新できる
    public function test_authenticated_regular_user_can_update_own_profile(): void
    {
        $user = User::factory()->create();
        $new_user = [
            'name' => 'テスト',
            'kana' => 'テスト',
            'email' => 'test111@email.com',
            'postal_code' => '0000000',
            'address' => 'テスト',
            'phone_number' => '00000000000',
        ];

        $response = $this->patch(route('user.update', $user), $new_user);

        $response->assertRedirect();
        $response->assertSessionHasNoErrors();
    }

    // ログイン済みの管理者は会員情報を更新できない
    public function test_authenticated_admin_cannot_update_user_profile(): void
    {
        $admin = new Admin();
        $admin->email = 'admin@example.com';
        $admin->password = Hash::make('nagoyameshi');
        $admin->save();
     
        $response = $this->post('admin/login', [
            'email' => $admin->email,
            'password' => 'nagoyameshi',
        ]);
        
        $response = $this->actingAs($admin)->patch(route('user.update', ['user' => $admin->id]));

        $response->assertStatus(404);
    }
    
}
