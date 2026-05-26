<?php

use App\Models\User;
use App\Models\UserDevice;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $user = User::factory()->create();
    $token = $user->createToken('TestToken')->plainTextToken;

    $this->user = $user;
    $this->withHeaders([
        'Authorization' => 'Bearer '.$token,
    ]);
});

it('saves fcm token', function () {
    $response = $this->postJson('/api/fcm-token', [
        'device_token' => 'test_device_token',
        'device_type' => 'android',
    ]);

    $response->assertCreated()
        ->assertJson([
            'success' => true,
            'message' => 'Device token saved successfully',
        ]);

    $this->assertDatabaseHas('user_devices', [
        'user_id' => $this->user->id,
        'device_token' => 'test_device_token',
    ]);
});

it('updates existing fcm token', function () {
    UserDevice::factory()->create([
        'user_id' => $this->user->id,
        'device_token' => 'existing_token',
        'device_type' => 'ios',
    ]);

    $response = $this->postJson('/api/fcm-token', [
        'device_token' => 'existing_token',
        'device_type' => 'android',
    ]);

    $response->assertCreated();

    $this->assertDatabaseHas('user_devices', [
        'user_id' => $this->user->id,
        'device_token' => 'existing_token',
        'device_type' => 'android',
    ]);
});

it('removes fcm token', function () {
    $device = UserDevice::factory()->create([
        'user_id' => $this->user->id,
        'device_token' => 'token_to_delete',
    ]);

    $response = $this->deleteJson('/api/fcm-token/token_to_delete');

    $response->assertOk()
        ->assertJson([
            'success' => true,
            'message' => 'Device token removed successfully',
        ]);

    $this->assertDatabaseMissing('user_devices', ['id' => $device->id]);
});