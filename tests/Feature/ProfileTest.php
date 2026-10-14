<?php

namespace Tests\Feature;

use Tests\TestCase;

class ProfileTest extends TestCase
{
    public function test_profile_management_routes_are_disabled(): void
    {
        $this->get('/profile')->assertNotFound();
        $this->patch('/profile', [
                'name' => 'Test User',
                'email' => 'test@example.com',
            ])->assertNotFound();
        $this->delete('/profile', [
                'password' => 'password',
            ])->assertNotFound();
    }
}
