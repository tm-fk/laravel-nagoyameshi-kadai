<?php

namespace Tests\Feature\Admin;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Admin;
use App\Models\Category;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;



class CategoryTest extends TestCase
{
   
    // 未ログインのユーザーは管理者側のカテゴリ一覧ページにアクセスできない
    public function test_unauthenticated_user_cannnot_access_admin_category_list_page(): void 
    {
        $response = $this->get('admin.categories.index');

        $response->assertStatus(404);
    }
   
     // ログイン済みの一般ユーザーは管理者側のカテゴリ一覧ページにアクセスできない
     public function test_authenticated_regular_user_cannot_access_admin_category_list_page(): void
     {
         $user = User::factory()->create();
 
         $response = $this->actingAs($user)->get('admin.categories.index');
 
         $response->assertStatus(404);
     }
   
     // ログイン済みの管理者は管理者側のカテゴリ一覧ページにアクセスできる
    public function test_authenticated_admin_can_access_admin_category_list_page(): void
    {
    $admin = new Admin();
    $admin->email = 'admin@example.com';
    $admin->password = Hash::make('nagoyameshi');
    $admin->save();

    $category = Category::factory()->create();

    // 管理者としてログイン
    $this->actingAs($admin);

    // カテゴリ一覧ページにアクセス
    $response = $this->get(route('admin.categories.index', ['categories' => $category]));

    // リダイレクトされることを確認
    $response->assertStatus(302);
    }
 
     // 未ログインのユーザーはカテゴリを登録できない
     public function test_unauthenticated_user_cannnot_register_category(): void
     {
         $category = Category::factory()->create();
         $data = $category->toArray();
 
         $response = $this->post(route('admin.categories.store'), $data);
 
         $this->assertDatabaseMissing('categories', $data);
         $response->assertRedirect(route('admin.login'));
     }
 
     // ログイン済みの一般ユーザーはカテゴリを登録できない
     public function test_authenticated_regular_user_cannot_register_category(): void
     {
         $user = User::factory()->create();
         $category = Category::factory()->create();
         $data = $category->toArray();
 
         $response = $this->actingAs($user)->post(route('admin.categories.store'), $data);
  
         $this->assertDatabaseMissing('categories', $data);
         $response->assertRedirect(route('admin.login'));
 
     }
     // ログイン済みの管理者はカテゴリを登録できる
     public function test_authenticated_admin_can_register_category(): void
     {
     $admin = new Admin();
     $admin->email = 'admin@example.com';
     $admin->password = Hash::make('nagoyameshi');
     $admin->save();
 
     $category = Category::factory()->create();
     $data = $category->toArray();
 
     unset($data['created_at'], $data['updated_at']);
 
     $this->actingAs($admin);
     $response = $this->post(route('admin.categories.store'), $data);
 
     $this->assertDatabaseHas('categories', $data);
     $response->assertStatus(302);
     }
 
     // 未ログインのユーザーはカテゴリを更新できない
     public function test_unauthenticated_user_cannnot_update_category(): void
     {
         $old_category = Category::factory()->create();
         $new_category = Category::factory()->create();
 
         $new_data = $new_category->toArray();
 
         $response = $this->patch(route('admin.categories.update', $old_category), $new_data);
 
         $this->assertDatabaseMissing('categories', $new_data);
         $response->assertRedirect(route('admin.login'));
     }
 
     // ログイン済みの一般ユーザーはカテゴリを更新できない
     public function test_authenticated_regular_user_cannot_update_category(): void
     {
         $user = User::factory()->create();
         $old_category = Category::factory()->create();
         $new_category = Category::factory()->create();
 
         $new_data = $new_category->toArray();
 
         $response = $this->actingAs($user)->patch(route('admin.categories.update', $old_category), $new_data);
 
         $this->assertDatabaseMissing('categories', $new_data);
         $response->assertRedirect(route('admin.login'));
 
     }
 
     // ログイン済みの管理者はカテゴリを更新できる
     public function test_authenticated_admin_can_update_category(): void
     {
         $admin = new Admin();
         $admin->email = 'admin@example.com';
         $admin->password = Hash::make('nagoyameshi');
         $admin->save();
 
         $old_category = Category::factory()->create();
         $new_category = Category::factory()->create();
         $new_data = $new_category->toArray();
 
         unset($new_data['created_at'], $new_data['updated_at']);
 
         $response = $this->actingAs($admin)->patch(route('admin.categories.update', $old_category), $new_data);
 
         $this->assertDatabaseHas('categories', $new_data);
         $response->assertStatus(302);
     }
 
     // 未ログインのユーザーはカテゴリを削除できない
     public function test_unauthenticated_user_cannnot_delete_category(): void
     {
         $category = category::factory()->create();
         $data = $category->toArray();
 
         unset($data['created_at'], $data['updated_at']);
 
         $response = $this->delete(route('admin.categories.destroy', $category), $data);
  
         $this->assertDatabaseHas('categories', $data);
         $response->assertRedirect(route('admin.login'));
     }
 
     // ログイン済みの一般ユーザーはカテゴリを削除できない
     public function test_authenticated_regular_user_cannot_delete_category(): void
     {
         $user = User::factory()->create();
 
         $category = Category::factory()->create();
         $data = $category->toArray();
 
         unset($data['created_at'], $data['updated_at']);
 
         $response = $this->actingAs($user)->delete(route('admin.categories.destroy', $category), $data);
  
         $this->assertDatabaseHas('categories', $data);
         $response->assertRedirect(route('admin.login'));
     }
     // ログイン済みの管理者はカテゴリを削除できる
     public function test_authenticated_admin_can_delete_restaurant(): void
     {
         $admin = new Admin();
         $admin->email = 'admin@example.com';
         $admin->password = Hash::make('nagoyameshi');
         $admin->save();
 
         $category = Category::factory()->create();
         $data = $category->toArray();
 
         $response = $this->actingAs($admin)->delete(route('admin.categories.destroy', $category), $data);
 
         $this->assertDatabaseMissing('categories', $data);
         $response->assertStatus(302);
     }
     

   
}
