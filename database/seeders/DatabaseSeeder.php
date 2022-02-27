<?php

namespace Database\Seeders;

use App\Models\AccountDefault;
use App\Models\Entry;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
         $user = \App\Models\User::factory()->create([
             'email' => 'admin@teste.com'
         ]);

         AccountDefault::factory()->create([
             'owner_id' => $user->id
         ]);

        Entry::factory(2)
            ->income()
            ->create([
                'owner_id' => $user->id
            ]);

         Entry::factory(5)->create([
             'owner_id' => $user->id
         ]);
    }
}
