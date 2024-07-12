<?php

namespace Tests\Feature\Admin;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Admin;
use App\Models\Term;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class TermTest extends TestCase
{
    //未ログインのユーザーは管理者側の利用規約ページにアクセスできない
    public function test_unauthenticated_user_cannot_access_admin_company_term_page(): void 
    {
        $response = $this->get('admin.terms.index');

        $response->assertStatus(404);
    }
    
    // ログイン済みの一般ユーザーは管理者側の利用規約ページにアクセスできない
    public function test_regular_user_cannot_access_admin_company_term_page(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('admin.terms.index');

        $response->assertStatus(404);
    }

    // ログイン済みの管理者は管理者側の利用規約ページにアクセスできる
    public function test_admin_can_access_admin_company_term_page(): void
    {
        $admin = new Admin();
        $admin->email = 'admin@example.com';
        $admin->password = Hash::make('nagoyameshi');
        $admin->save();

        $term = Term::factory()->create();
        
        $response = $this->actingAs($admin, 'admin')->get(route('admin.terms.index', $term));
    
        $response->assertStatus(200);
    }

    //未ログインのユーザーは管理者側の利用規約編集ページにアクセスできない
    public function test_unauthenticated_user_cannot_access_admin_company_term_edit_page(): void 
    {
        $response = $this->get('admin.terms.edit');

        $response->assertStatus(404);
    }
    
    // ログイン済みの一般ユーザーは管理者側の利用規約編集ページにアクセスできない
    public function test_regular_user_cannot_access_admin_company_term_edit_page(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('admin.terms.edit');

        $response->assertStatus(404);
    }

    // ログイン済みの管理者は管理者側の利用規約編集ページにアクセスできる
    public function test_admin_can_access_admin_company_term_edit_page(): void
    {
        $admin = new Admin();
        $admin->email = 'admin@example.com';
        $admin->password = Hash::make('nagoyameshi');
        $admin->save();

        $term = Term::factory()->create();
        
        $response = $this->actingAs($admin, 'admin')->get(route('admin.terms.edit', $term));
    
        $response->assertStatus(200);
    }

    // 未ログインのユーザーは利用規約を更新できない
    public function test_unauthenticated_user_cannot_update_company_term(): void
    {
        $old_term = Term::factory()->create();
        $new_term = Term::factory()->make();

        $response = $this->patch(route('admin.terms.update', $old_term), $new_term->toArray());

        $response->assertRedirect(route('admin.login'));
    }

    // ログイン済みの一般ユーザーは利用規約を更新できない
    public function test_regular_user_cannot_update_company_term(): void
    {
        $user = User::factory()->create();

        $old_term = Term::factory()->create();
        $new_term = Term::factory()->make();

        $response = $this->actingAs($user)->patch(route('admin.terms.update', $old_term), $new_term->toArray());

        $response->assertRedirect(route('admin.login'));
    }

    // ログイン済みの管理者は利用規約を更新できる
    public function test_admin_can_update_company_term(): void
    {
        $admin = new Admin();
        $admin->email = 'admin@example.com';
        $admin->password = Hash::make('nagoyameshi');
        $admin->save();

        $old_term = Term::factory()->create();
        $new_term = Term::factory()->make();

        $response = $this->actingAs($admin, 'admin')->patch(route('admin.terms.update', $old_term), $new_term->toArray());

        $this->assertDatabaseHas('terms', $new_term->toArray());

        $response->assertStatus(302);
    }
}
