<?php

namespace Tests\Feature\Admin;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Admin;
use Illuminate\Support\Facades\Hash;

class UserTest extends TestCase
{
    
    use RefreshDatabase;
    
    public function test_user_not_login_administrator(): void
    {
        $response = $this->get('/admin/index');

        $response->assertStatus(404);
        }

    public function testAuthenticatedUserCannotAccessAdminMemberList()
    {
        $user = new User();
        $user->name = "testman";
        $user->kana = "テストマン";
        $user->email = "user@example.com";
        $user->password = Hash::make('nagoyameshi');
        $user->postal_code = "123-4567";
        $user->address = "aaa";
        $user->phone_number = "060-4444-4444";
        $user->birthday = "2024-02-02";
        $user->occupation = "無職";
        $user->save();
     
        $response = $this->post('/admin/login', [
            'email' => $user->email,
            'password' => 'nagoyameshi',
        ]);

        $response->assertRedirect('/');
    }

    public function test_authenticated_admin_can_access_admin_member_detail_page(): void
    {
        $admin = new Admin();
        $admin->email = 'admin@example.com';
        $admin->password = Hash::make('nagoyameshi');
        $admin->save();
     
        $response = $this->post('admin/login', [
            'email' => $admin->email,
            'password' => 'nagoyameshi',
        ]);
        $response->assertRedirect(route('admin.home'));
    }
    
}
