<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Admin;
use App\Models\Review;
use App\Models\Restaurant;
use App\Models\Reservation;
use App\Models\Favorite;


class FavoriteTest extends TestCase
{
    use Refreshdatabase;

    public function testIndexForUnauthenticatedUser()
{
    $response = $this->get('/favorites');
    $response->assertRedirect('/login'); // 末ログインのユーザーはログインページにリダイレクトされる
}

public function testIndexForFreeUser()
{
    $user = User::factory()->create(['is_member' => false]); // 無料会員のユーザー作成
    $this->actingAs($user);
    $response = $this->get('/favorites');
    $response->assertStatus(403); // 無料会員のユーザーは403エラーになる
}

public function testIndexForPaidUser()
{
    $user = User::factory()->create(['is_member' => true]); // 有料会員のユーザー作成
    $this->actingAs($user);
    $response = $this->get('/favorites');
    $response->assertStatus(200); // 有料会員のユーザーはアクセスできる
}

public function testIndexForAdmin()
{
    $user = User::factory()->create(['is_admin' => true]); // 管理者ユーザー作成
    $this->actingAs($user);
    $response = $this->get('/favorites');
    $response->assertStatus(200); // 管理者もアクセスできる
}


public function testStoreForUnauthenticatedUser()
{
    $response = $this->post('/favorites', ['item_id' => 1]);
    $response->assertRedirect('/login'); // 末ログインのユーザーはログインページにリダイレクトされる
}

public function testStoreForNonMemberUser()
{
    $user = User::factory()->create(['is_member' => false]); // 無料会員のユーザー作成
    $this->actingAs($user);
    $response = $this->post('/favorites', ['item_id' => 1]);
    $response->assertStatus(403); // 無料会員のユーザーは403エラーになる
}

public function testStoreForFreeUser()
{
    $user = User::factory()->create(['is_member' => false]);
    $this->actingAs($user);
    $response = $this->post('/favorites', ['item_id' => 1]);
    $response->assertStatus(403); // 無料会員のユーザーは403エラーになる
}

public function testStoreForPaidUser()
{
    $user = User::factory()->create(['is_member' => true]); // 有料会員のユーザー作成（有料の会員）
    $this->actingAs($user);
    $response = $this->post('/favorites', ['item_id' => 1]);
    $response->assertStatus(201); // 有料会員のユーザーは追加できる
}

public function testStoreForAdmin()
{
    $user = User::factory()->create(['is_admin' => true]); // 管理者ユーザー作成
    $this->actingAs($user);
    $response = $this->post('/favorites', ['item_id' => 1]);
    $response->assertStatus(201); // 管理者も追加できる
}


public function testDestroyForUnauthenticatedUser()
{
    $response = $this->delete('/favorites/1');
    $response->assertRedirect('/login'); // 末ログインのユーザーはログインページにリダイレクトされる
}

public function testDestroyForNonMemberUser()
{
    $user = User::factory()->create(['is_member' => false]); // 無料会員のユーザー作成
    $this->actingAs($user);
    $response = $this->delete('/favorites/1');
    $response->assertStatus(403); // 無料会員のユーザーは403エラーになる
}

public function testDestroyForPaidUser()
{
    $user = User::factory()->create(['is_member' => true]); // 有料会員のユーザー作成（有料の会員）
    $this->actingAs($user);
    $response = $this->delete('/favorites/1');
    $response->assertStatus(204); // 有料会員のユーザーは解除できる
}

public function testDestroyForAdmin()
{
    $user = User::factory()->create(['is_admin' => true]); // 管理者ユーザー作成
    $this->actingAs($user);
    $response = $this->delete('/favorites/1');
    $response->assertStatus(204); // 管理者も解除できる
}


public function testBackRedirect()
{
    $user = User::factory()->create(['is_member' => true]);
    $this->actingAs($user);

    // アイテム追加後にリダイレクトされるか確認
    $response = $this->post('/favorites', ['item_id' => 1]);
    $response->assertStatus(302); // リダイレクトが実行されることを確認
}

}
