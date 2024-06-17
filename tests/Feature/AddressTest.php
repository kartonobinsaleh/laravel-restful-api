<?php

namespace Tests\Feature;

use App\Models\Address;
use App\Models\Contact;
use Database\Seeders\AddressSeeder;
use Database\Seeders\ContactSeeder;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class AddressTest extends TestCase
{
    public function testCreateSuccess()
    {
        $this->seed([UserSeeder::class, ContactSeeder::class]);

        $contact = Contact::query()->limit(1)->first();

        $this->post('/api/contacts/' . $contact->id . '/addresses', [
            "street" => "test",
            "city" => "test",
            "province" => "test",
            "country" => "test",
            "postal_code" => "4444",
        ], [
            'Authorization' => 'tokentest',
        ])->assertStatus(201)->assertJson([
            'data' => [
                "street" => "test",
                "city" => "test",
                "province" => "test",
                "country" => "test",
                "postal_code" => "4444",
            ]
        ]);
    }

    public function testCreateFailed()
    {
        $this->seed([UserSeeder::class, ContactSeeder::class]);

        $contact = Contact::query()->limit(1)->first();

        $this->post('/api/contacts/' . $contact->id . '/addresses', [
            "street" => "test",
            "city" => "test",
            "province" => "test",
            "country" => "",
            "postal_code" => "4444",
        ], [
            'Authorization' => 'tokentest',
        ])->assertStatus(400)->assertJson([
            "errors" => [
                "country" => [
                    "The country field is required."
                ]
            ]
        ]);
    }

    public function testCreateContactNotFound()
    {
        $this->seed([UserSeeder::class, ContactSeeder::class]);

        $contact = Contact::query()->limit(1)->first();

        $this->post('/api/contacts/' . ($contact->id + 2) . '/addresses', [
            "street" => "test",
            "city" => "test",
            "province" => "test",
            "country" => "test",
            "postal_code" => "4444",
        ], [
            'Authorization' => 'tokentest',
        ])->assertStatus(404)->assertJson([
            "errors" => [
                "message" => [
                    "not found"
                ]
            ]
        ]);
    }

    public function testGetSuccess()
    {
        $this->seed([UserSeeder::class, ContactSeeder::class, AddressSeeder::class]);
        $address = Address::query()->limit(1)->first();

        $this->get(
            '/api/contacts/' . $address->contact_id . '/addresses/' . $address->id,
            [
                'Authorization' => 'tokentest',
            ]
        )->assertStatus(200)->assertJson([
            'data' => [
                'street' => 'test',
                'city' => 'test',
                'province' => 'test',
                'country' => 'test',
                'postal_code' => '4444',
            ]
        ]);
    }

    public function testGetNotFound()
    {
        $this->seed([UserSeeder::class, ContactSeeder::class, AddressSeeder::class]);
        $address = Address::query()->limit(1)->first();

        $this->get(
            '/api/contacts/' . $address->contact_id . '/addresses/' . ($address->id + 1),
            [
                'Authorization' => 'tokentest',
            ]
        )->assertStatus(404)->assertJson([
            "errors" => [
                "message" => [
                    "not found"
                ]
            ]
        ]);
    }

    public function testUpdateSuccess()
    {
        $this->seed([UserSeeder::class, ContactSeeder::class, AddressSeeder::class]);
        $address = Address::query()->limit(1)->first();

        $this->put(
            '/api/contacts/' . $address->contact_id . '/addresses/' . $address->id,
            [
                "street" => "update",
                "city" => "update",
                "province" => "update",
                "country" => "update",
                "postal_code" => "4444",
            ],
            [
                'Authorization' => 'tokentest',
            ]
        )->assertStatus(200)->assertJson([
            'data' => [
                "street" => "update",
                "city" => "update",
                "province" => "update",
                "country" => "update",
                "postal_code" => "4444",
            ]
        ]);
    }

    public function testUpdateFailed()
    {
        $this->seed([UserSeeder::class, ContactSeeder::class, AddressSeeder::class]);
        $address = Address::query()->limit(1)->first();

        $this->put(
            '/api/contacts/' . $address->contact_id . '/addresses/' . $address->id,
            [
                "street" => "update",
                "city" => "update",
                "province" => "update",
                "country" => "",
                "postal_code" => "4444",
            ],
            [
                'Authorization' => 'tokentest',
            ]
        )->assertStatus(400)->assertJson([
            "errors" => [
                "country" => [
                    "The country field is required."
                ]
            ]
        ]);
    }

    public function testUpdateNotFound()
    {
        $this->seed([UserSeeder::class, ContactSeeder::class, AddressSeeder::class]);
        $address = Address::query()->limit(1)->first();

        $this->put(
            '/api/contacts/' . $address->contact_id . '/addresses/' . ($address->id + 1),
            [
                "street" => "update",
                "city" => "update",
                "province" => "update",
                "country" => "update",
                "postal_code" => "4444",
            ],
            [
                'Authorization' => 'tokentest',
            ]
        )->assertStatus(404)->assertJson([
            "errors" => [
                "message" => [
                    "not found"
                ]
            ]
        ]);
    }

    public function testDeleteSuccess()
    {
        $this->seed([UserSeeder::class, ContactSeeder::class, AddressSeeder::class]);
        $address = Address::query()->limit(1)->first();

        $this->delete(
            '/api/contacts/' . $address->contact_id . '/addresses/' . $address->id,
            [],
            [
                'Authorization' => 'tokentest',
            ]
        )->assertStatus(200)->assertJson([
            'data' => true
        ]);
    }

    public function testDeleteNotFound()
    {
        $this->seed([UserSeeder::class, ContactSeeder::class, AddressSeeder::class]);
        $address = Address::query()->limit(1)->first();

        $this->delete(
            '/api/contacts/' . $address->contact_id . '/addresses/' . ($address->id + 1),
            [],
            [
                'Authorization' => 'tokentest',
            ]
        )->assertStatus(404)->assertJson([
            "errors" => [
                "message" => [
                    "not found"
                ]
            ]
        ]);
    }
}
