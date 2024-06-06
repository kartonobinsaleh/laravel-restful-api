<?php

namespace Tests\Feature;

use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ContactTest extends TestCase
{
    public function testCreateSuccess()
    {
        $this->seed(UserSeeder::class);

        $this->post('/api/contacts', [
            'first_name' => 'Kartono',
            'last_name' => 'saleh',
            'email' => 'kartono@gmail.com',
            'phone' => '08666666',
        ], [
            'Authorization' => 'tokentest',
        ])->assertStatus(201)->assertJson([
            'data' => [
                'first_name' => 'Kartono',
                'last_name' => 'saleh',
                'email' => 'kartono@gmail.com',
                'phone' => '08666666',
            ]
        ]);
    }

    public function testCreateFailed()
    {
        $this->seed(UserSeeder::class);

        $this->post('/api/contacts', [
            'first_name' => '',
            'last_name' => 'saleh',
            'email' => 'kartono',
            'phone' => '08666666',
        ], [
            'Authorization' => 'tokentest',
        ])->assertStatus(400)->assertJson([
            'errors' => [
                'first_name' => [
                    'The first name field is required.'
                ],
                'email' => [
                    'The email field must be a valid email address.'
                ],
            ]
        ]);
    }
}
