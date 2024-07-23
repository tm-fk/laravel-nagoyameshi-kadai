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

class ReservationTest extends TestCase
{
    use RefreshDatabase;

    public function test_unauthenticated_users_cannot_access_member_side_reservation_list_page()
{
    $response = $this->get('/member/reservations');
    $response->assertRedirect('/login');
}

public function test_logged_in_free_members_cannot_access_member_side_reservation_list_page()
{
    $user = User::factory()->create(['membership_type' => 'free']);
    $this->actingAs($user);
    
    $response = $this->get('/member/reservations');
    $response->assertStatus(403); // Assuming forbidden status for free members
}

public function test_logged_in_paid_members_can_access_member_side_reservation_list_page()
{
    $user = User::factory()->create(['membership_type' => 'paid']);
    $this->actingAs($user);
    
    $response = $this->get('/member/reservations');
    $response->assertStatus(200);
}

public function test_logged_in_administrators_cannot_access_member_side_reservation_list_page()
{
    $admin = User::factory()->create(['is_admin' => true]);
    $this->actingAs($admin);
    
    $response = $this->get('/member/reservations');
    $response->assertStatus(403); // Assuming forbidden status for admins
}

public function test_unauthenticated_users_cannot_access_member_side_reservation_create_page()
{
    $response = $this->get('/member/reservations/create');
    $response->assertRedirect('/login');
}

public function test_logged_in_free_members_cannot_access_member_side_reservation_create_page()
{
    $user = User::factory()->create(['membership_type' => 'free']);
    $this->actingAs($user);
    
    $response = $this->get('/member/reservations/create');
    $response->assertStatus(403); // Assuming forbidden status for free members
}

public function test_logged_in_paid_members_can_access_member_side_reservation_create_page()
{
    $user = User::factory()->create(['membership_type' => 'paid']);
    $this->actingAs($user);
    
    $response = $this->get('/member/reservations/create');
    $response->assertStatus(200);
}

public function test_logged_in_administrators_cannot_access_member_side_reservation_create_page()
{
    $admin = User::factory()->create(['is_admin' => true]);
    $this->actingAs($admin);
    
    $response = $this->get('/member/reservations/create');
    $response->assertStatus(403); // Assuming forbidden status for admins
}

public function test_unauthenticated_users_cannot_store_reservation()
{
    $reservationData = [
        'date' => '2024-08-01',
        'time' => '18:00',
        'number_of_people' => 2,
        // 他の必要なデータ
    ];

    $response = $this->post('/member/reservations', $reservationData);
    $response->assertRedirect('/login');
}

public function test_logged_in_free_members_cannot_store_reservation()
{
    $user = User::factory()->create(['membership_type' => 'free']);
    $this->actingAs($user);

    $reservationData = [
        'date' => '2024-08-01',
        'time' => '18:00',
        'number_of_people' => 2,
        // 他の必要なデータ
    ];

    $response = $this->post('/member/reservations', $reservationData);
    $response->assertStatus(403); // Assuming forbidden status for free members
}

public function test_logged_in_paid_members_can_store_reservation()
{
    $user = User::factory()->create(['membership_type' => 'paid']);
    $this->actingAs($user);

    $reservationData = [
        'date' => '2024-08-01',
        'time' => '18:00',
        'number_of_people' => 2,
        // 他の必要なデータ
    ];

    $response = $this->post('/member/reservations', $reservationData);
    $response->assertStatus(201); // Assuming successful creation status
}

public function test_logged_in_administrators_cannot_store_reservation()
{
    $admin = User::factory()->create(['is_admin' => true]);
    $this->actingAs($admin);

    $reservationData = [
        'date' => '2024-08-01',
        'time' => '18:00',
        'number_of_people' => 2,
        // 他の必要なデータ
    ];

}

public function test_unauthenticated_users_cannot_destroy_reservation()
{
    $reservation = Reservation::factory()->create();

    $response = $this->delete('/member/reservations/' . $reservation->id);
    $response->assertRedirect('/login');
}

public function test_logged_in_free_members_cannot_destroy_reservation()
{
    $user = User::factory()->create(['membership_type' => 'free']);
    $this->actingAs($user);

    $reservation = Reservation::factory()->create();

    $response = $this->delete('/member/reservations/' . $reservation->id);
    $response->assertStatus(403); // Assuming forbidden status for free members
}

public function test_logged_in_paid_members_cannot_destroy_others_reservation()
{
    $user = User::factory()->create(['membership_type' => 'paid']);
    $this->actingAs($user);

    $otherUser = User::factory()->create();
    $reservation = Reservation::factory()->create(['user_id' => $otherUser->id]);

    $response = $this->delete('/member/reservations/' . $reservation->id);
    $response->assertStatus(403); // Assuming forbidden status for canceling others' reservations
}

public function test_logged_in_paid_members_can_destroy_their_own_reservation()
{
    $user = User::factory()->create(['membership_type' => 'paid']);
    $this->actingAs($user);

    $reservation = Reservation::factory()->create(['user_id' => $user->id]);

    $response = $this->delete('/member/reservations/' . $reservation->id);
    $response->assertStatus(200); // Assuming successful deletion status
    $this->assertDatabaseMissing('reservations', ['id' => $reservation->id]);
}

public function test_logged_in_administrators_cannot_destroy_reservation()
{
    $admin = User::factory()->create(['is_admin' => true]);
    $this->actingAs($admin);

    $reservation = Reservation::factory()->create();

    $response = $this->delete('/member/reservations/' . $reservation->id);
    $response->assertStatus(403); // Assuming forbidden status for admins
}

}