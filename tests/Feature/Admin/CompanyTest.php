<?php

namespace Tests\Feature\Admin;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Admin;
use App\Models\Company;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class CompanyTest extends TestCase
{
    use RefreshDatabase;

    //未ログインのユーザーは管理者側の会社概要ページにアクセスできない
    public function test_unauthenticated_user_cannot_access_admin_company_page(): void 
    {
        $response = $this->get('admin.company.index');

        $response->assertStatus(404);
    }


     // ログイン済みの一般ユーザーは管理者側の会社概要ページにアクセスできない
     public function test_regular_user_cannot_access_admin_company_page(): void
     {
         $user = User::factory()->create();
 
         $response = $this->actingAs($user)->get('admin.company.index');
 
         $response->assertStatus(404);
     }
 
     // ログイン済みの管理者は管理者側の会社概要ページにアクセスできる
     public function test_admin_can_access_admin_company_page(): void
     {
         $admin = new Admin();
         $admin->email = 'admin@example.com';
         $admin->password = Hash::make('nagoyameshi');
         $admin->save();
 
         $company = Company::factory()->create();
         
         $response = $this->actingAs($admin, 'admin')->get(route('admin.company.index', $company));
     
         $response->assertStatus(200);
     }
 
     //未ログインのユーザーは管理者側の会社概要編集ページにアクセスできない
     public function test_unauthenticated_user_cannot_access_admin_company_edit_page(): void 
     {
         $response = $this->get('admin.company.edit');
 
         $response->assertStatus(404);
     }
     
     // ログイン済みの一般ユーザーは管理者側の会社概要編集ページにアクセスできない
     public function test_regular_user_cannot_access_admin_company_edit_page(): void
     {
         $user = User::factory()->create();
 
         $response = $this->actingAs($user)->get('admin.company.edit');
 
         $response->assertStatus(404);
     }
 
     // ログイン済みの管理者は管理者側の会社概要編集ページにアクセスできる
     public function test_admin_can_access_admin_company_edit_page(): void
     {
         $admin = new Admin();
         $admin->email = 'admin@example.com';
         $admin->password = Hash::make('nagoyameshi');
         $admin->save();
 
         $company = Company::factory()->create();
         
         $response = $this->actingAs($admin, 'admin')->get(route('admin.company.edit', $company));
     
         $response->assertStatus(200);
     }
 
     // 未ログインのユーザーは会社概要を更新できない
     public function test_unauthenticated_user_cannot_update_company_profile(): void
     {
         $old_company = Company::factory()->create();
         $new_company = Company::factory()->create();
         $data = $new_company->toArray();
 
         $response = $this->patch(route('admin.company.update', $old_company), $data);
 
         $response->assertRedirect(route('admin.login'));
     }
 
     // ログイン済みの一般ユーザーは会社概要を更新できない
     public function test_regular_user_cannot_update_company_profile(): void
     {
         $user = User::factory()->create();
 
         $old_company = Company::factory()->create();
         $new_company = Company::factory()->create();
         $data = $new_company->toArray();
 
         $response = $this->actingAs($user)->patch(route('admin.company.update', $old_company), $data);
 
         $response->assertRedirect(route('admin.login'));
     }
 
     // ログイン済みの管理者は会社概要を更新できる
     public function test_admin_can_update_company_profile(): void
     {
         $admin = new Admin();
         $admin->email = 'admin@example.com';
         $admin->password = Hash::make('nagoyameshi');
         $admin->save();
 
         $old_company = Company::factory()->create();
         $new_company = Company::factory()->make();
 
         $response = $this->actingAs($admin, 'admin')->patch(route('admin.company.update', $old_company), $new_company->toArray());
 
         $this->assertDatabaseHas('companies', $new_company->toArray());
 
         $response->assertStatus(302);
     }

}
