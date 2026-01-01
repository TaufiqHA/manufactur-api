<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_list_users()
    {
        // Create some users
        User::factory()->count(3)->create();

        $user = User::factory()->create([
            'email' => 'admin@example.com',
        ]);
        $token = $user->createToken('test_token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
                         ->getJson('/api/users');

        $response->assertStatus(200);
    }

    public function test_user_can_create_new_user()
    {
        $admin = User::factory()->create([
            'email' => 'admin@example.com',
        ]);
        $token = $admin->createToken('test_token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
                         ->postJson('/api/users', [
                             'name' => 'New User',
                             'username' => 'newuser',
                             'email' => 'newuser@example.com',
                             'password' => 'password123',
                             'password_confirmation' => 'password123',
                             'role' => 'user',
                         ]);

        $response->assertStatus(201)
                 ->assertJson([
                     'message' => 'User created successfully'
                 ]);

        $this->assertDatabaseHas('users', [
            'name' => 'New User',
            'email' => 'newuser@example.com',
            'username' => 'newuser',
            'role' => 'user',
        ]);
    }

    public function test_user_creation_fails_with_invalid_data()
    {
        $admin = User::factory()->create([
            'email' => 'admin@example.com',
        ]);
        $token = $admin->createToken('test_token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
                         ->postJson('/api/users', [
                             'name' => '', // Invalid: empty name
                             'username' => 'testuser',
                             'email' => 'invalid-email', // Invalid: not an email
                             'password' => '123', // Invalid: too short
                             'password_confirmation' => '123',
                             'role' => 'user',
                         ]);

        $response->assertStatus(422)
                 ->assertJson([
                     'message' => 'Validation failed'
                 ]);
    }

    public function test_user_can_retrieve_single_user()
    {
        $admin = User::factory()->create([
            'email' => 'admin@example.com',
        ]);
        $token = $admin->createToken('test_token')->plainTextToken;

        $user = User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
                         ->getJson("/api/users/{$user->id}");

        $response->assertStatus(200)
                 ->assertJson([
                     'user' => [
                         'id' => $user->id,
                         'name' => $user->name,
                         'email' => $user->email,
                     ]
                 ]);
    }

    public function test_retrieving_nonexistent_user_returns_404()
    {
        $admin = User::factory()->create([
            'email' => 'admin@example.com',
        ]);
        $token = $admin->createToken('test_token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
                         ->getJson('/api/users/999');

        $response->assertStatus(404)
                 ->assertJson([
                     'message' => 'User not found'
                 ]);
    }

    public function test_user_can_update_existing_user()
    {
        $admin = User::factory()->create([
            'email' => 'admin@example.com',
        ]);
        $token = $admin->createToken('test_token')->plainTextToken;

        $user = User::factory()->create([
            'name' => 'Original Name',
            'email' => 'original@example.com',
            'username' => 'originaluser',
            'role' => 'user',
        ]);

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
                         ->putJson("/api/users/{$user->id}", [
                             'name' => 'Updated Name',
                             'email' => 'updated@example.com',
                             'role' => 'admin',
                         ]);

        $response->assertStatus(200)
                 ->assertJson([
                     'message' => 'User updated successfully'
                 ]);

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'Updated Name',
            'email' => 'updated@example.com',
            'role' => 'admin',
        ]);
    }

    public function test_user_update_fails_with_invalid_data()
    {
        $admin = User::factory()->create([
            'email' => 'admin@example.com',
        ]);
        $token = $admin->createToken('test_token')->plainTextToken;

        $user = User::factory()->create([
            'name' => 'Original Name',
            'email' => 'original@example.com',
        ]);

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
                         ->putJson("/api/users/{$user->id}", [
                             'name' => '', // Invalid: empty name
                             'email' => 'invalid-email', // Invalid: not an email
                         ]);

        $response->assertStatus(422)
                 ->assertJson([
                     'message' => 'Validation failed'
                 ]);
    }

    public function test_user_can_delete_existing_user()
    {
        $admin = User::factory()->create([
            'email' => 'admin@example.com',
        ]);
        $token = $admin->createToken('test_token')->plainTextToken;

        $user = User::factory()->create([
            'name' => 'User to Delete',
            'email' => 'delete@example.com',
        ]);

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
                         ->deleteJson("/api/users/{$user->id}");

        $response->assertStatus(200)
                 ->assertJson([
                     'message' => 'User deleted successfully'
                 ]);

        $this->assertDatabaseMissing('users', [
            'id' => $user->id,
        ]);
    }

    public function test_deleting_nonexistent_user_returns_404()
    {
        $admin = User::factory()->create([
            'email' => 'admin@example.com',
        ]);
        $token = $admin->createToken('test_token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
                         ->deleteJson('/api/users/999');

        $response->assertStatus(404)
                 ->assertJson([
                     'message' => 'User not found'
                 ]);
    }

    public function test_unauthorized_user_cannot_access_user_endpoints()
    {
        $user = User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        // Test index without token
        $response = $this->getJson('/api/users');
        $response->assertStatus(401);

        // Test store without token
        $response = $this->postJson('/api/users', [
            'name' => 'New User',
            'username' => 'newuser',
            'email' => 'newuser@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'role' => 'user',
        ]);
        $response->assertStatus(401);

        // Test show without token
        $response = $this->getJson("/api/users/{$user->id}");
        $response->assertStatus(401);

        // Test update without token
        $response = $this->putJson("/api/users/{$user->id}", [
            'name' => 'Updated Name',
        ]);
        $response->assertStatus(401);

        // Test delete without token
        $response = $this->deleteJson("/api/users/{$user->id}");
        $response->assertStatus(401);
    }
}