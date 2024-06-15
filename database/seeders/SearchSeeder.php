<?php

namespace Database\Seeders;

use App\Models\Contact;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SearchSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::where('username', 'usertest')->first();
        for ($i = 0; $i < 20; $i++) {
            Contact::create([
                'first_name' => 'first' . $i,
                'last_name' => 'last' . $i,
                'email' => 'email' . $i . '@gmail.com',
                'phone' => '222' . $i . '@gmail.com',
                'user_id' => $user->id,
            ]);
        }
    }
}
