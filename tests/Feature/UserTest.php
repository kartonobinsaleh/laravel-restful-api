<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

use function PHPUnit\Framework\assertEquals;

class UserTest extends TestCase
{
    public function testRegisterSuccess()
    {
        $this->post('/api/users', [
            'username' => 'kartono',
            'password' => 'rahasia',
            'name' => 'kartono saleh',
        ])->assertStatus(201)->assertJson([
            "data" => [
                'username' => 'kartono',
                'name' => 'kartono saleh',
            ]
        ]);
    }

    public function testRegisterFailed()
    {
        $this->post('/api/users', [
            'username' => '',
            'password' => '',
            'name' => '',
        ])->assertStatus(400)->assertJson([
            "errors" => [
                'username' => [
                    'The username field is required.'
                ],
                'password' => [
                    'The password field is required.'
                ],
                'name' => [
                    'The name field is required.'
                ],
            ]
        ]);
    }

    public function testRegisterUsernameAlreadyAxists()
    {
        $this->testRegisterSuccess();
        $this->post('/api/users', [
            'username' => 'kartono',
            'password' => 'rahasia',
            'name' => 'kartono saleh',
        ])->assertStatus(400)->assertJson([
            "errors" => [
                'username' => [
                    'username already registered'
                ],
            ]
        ]);
    }

    public function testLoginSuccess()
    {
        $this->seed([UserSeeder::class]);
        $this->post('/api/users/login', [
            'username' => 'usertest',
            'password' => 'password test',
        ])->assertStatus(200)->assertJson([
            "data" => [
                'username' => 'usertest',
                'name' => 'name test',
            ]
        ]);

        $user = User::where('username', 'usertest')->first();
        self::assertNotNull($user->token);
    }

    public function testLoginFailedUsernameNotFound()
    {
        $this->post('/api/users/login', [
            'username' => 'usertest',
            'password' => 'password test',
        ])->assertStatus(401)->assertJson([
            "errors" => [
                'message' => [
                    'username or password wrong'
                ],
            ]
        ]);
    }

    public function testLoginFailedPasswordWrong()
    {
        $this->seed([UserSeeder::class]);
        $this->post('/api/users/login', [
            'username' => 'usertest',
            'password' => 'salah',
        ])->assertStatus(401)->assertJson([
            "errors" => [
                'message' => [
                    'username or password wrong'
                ],
            ]
        ]);
    }

    public function testGetSuccess()
    {
        $this->seed([UserSeeder::class]);
        $this->get('/api/users/current', [
            'Authorization' => 'tokentest'
        ])->assertStatus(200)->assertJson([
            'data' => [
                'username' => 'usertest',
                'name' => 'name test'
            ]
        ]);
    }
    public function testGetUnauthorized()
    {
        $this->seed([UserSeeder::class]);
        $this->get('/api/users/current')->assertStatus(401)->assertJson([
            'errors' => [
                'message' => [
                    'unauthorized'
                ]
            ]
        ]);
    }
    public function testGetInvalidToken()
    {
        $this->seed([UserSeeder::class]);
        $this->get('/api/users/current', [
            'Authorization' => 'token salah'
        ])->assertStatus(401)->assertJson([
            'errors' => [
                'message' => [
                    'unauthorized'
                ]
            ]
        ]);
    }
}
