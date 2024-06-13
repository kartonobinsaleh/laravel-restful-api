<?php

namespace Tests\Feature;

use App\Models\Contact;
use Database\Seeders\ContactSeeder;
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

    public function testGetSuccess()
    {
        $this->seed([UserSeeder::class, ContactSeeder::class]);

        $contact = Contact::query()->limit(1)->first();

        $this->get('/api/contacts/' . $contact->id, [
            'Authorization' => 'tokentest',
        ])->assertStatus(200)->assertJson([
            'data' => [
                'first_name' => 'test',
                'last_name' => 'test',
                'email' => 'test@gmail.com',
                'phone' => '08666',
            ]
        ]);
    }

    public function testGetNotFound()
    {
        $this->seed([UserSeeder::class, ContactSeeder::class]);

        $contact = Contact::query()->limit(1)->first();

        $this->get('/api/contacts/' . ($contact->id + 1), [
            'Authorization' => 'tokentest',
        ])->assertStatus(404)->assertJson([
            'errors' => [
                'message' => [
                    'not found'
                ]
            ]
        ]);
    }

    public function testGetOtherUserContact()
    {
        $this->seed([UserSeeder::class, ContactSeeder::class]);

        $contact = Contact::query()->limit(1)->first();

        $this->get('/api/contacts/' . $contact->id, [
            'Authorization' => 'tokentest2',
        ])->assertStatus(404)->assertJson([
            'errors' => [
                'message' => [
                    'not found'
                ]
            ]
        ]);
    }


    public function testUpdateSuccess()
    {
        $this->seed([UserSeeder::class, ContactSeeder::class]);
        $contact = Contact::query()->limit(1)->first();

        $this->put('/api/contacts/' . $contact->id, [
            'first_name' => 'test update',
            'last_name' => 'test update',
            'email' => 'testupdate@gmail.com',
            'phone' => '0866699',
        ], [
            'Authorization' => 'tokentest',
        ])->assertStatus(200)->assertJson([
            'data' => [
                'first_name' => 'test update',
                'last_name' => 'test update',
                'email' => 'testupdate@gmail.com',
                'phone' => '0866699',
            ]
        ]);
    }

    public function testUpdateValidationError()
    {
        $this->seed([UserSeeder::class, ContactSeeder::class]);
        $contact = Contact::query()->limit(1)->first();

        $this->put('/api/contacts/' . $contact->id, [
            'first_name' => '',
            'last_name' => 'test update',
            'email' => 'testupdate@gmail.com',
            'phone' => '0866699',
        ], [
            'Authorization' => 'tokentest',
        ])->assertStatus(400)->assertJson([
            'errors' => [
                'first_name' => [
                    'The first name field is required.'
                ]
            ]
        ]);
    }
}
