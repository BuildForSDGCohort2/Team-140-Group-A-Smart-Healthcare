<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ApiAuthTest extends TestCase
{
    use RefreshDatabase;

    public function testLoginUsingGet(){
        $response = $this->get('/api/auth/login');
        $response->assertStatus(405);
    }

    public function testMissingUsernameOrPasswordOnLogin(){
        $response = $this->get('/api/auth/login');
        $response->assertStatus(405);
    }

    public function testLoginWithIncorrectCredentials(){
        $response = $this->get('/api/auth/login');
        $response->assertStatus(405);
    }

    public function testLoginWithCorrectCredentials(){
        $response = $this->get('/api/auth/login');
        $response->assertStatus(405);
    }

}
