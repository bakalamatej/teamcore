<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Member;
use App\Models\EventType;
use App\Models\Club;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // -----------------------
        // Users
        // -----------------------
        $user = User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'role' => 'player',
            'password' => bcrypt('password'),
        ]);

        $coach = User::factory()->create([
            'name' => 'Coach User',
            'email' => 'coach@example.com',
            'role' => 'coach',
            'password' => bcrypt('password'),
        ]);

        $admin = User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'role' => 'admin',
            'password' => bcrypt('password'),
        ]);

        // -----------------------
        // Members
        // -----------------------
        $member = Member::create([
            'user_id' => $user->id,
            'name' => 'John',
            'surname' => 'Doe',
            'email' => 'john.doe@example.com',
            'phone' => '0900123456',
        ]);

        $coachMember = Member::create([
            'user_id' => $coach->id,
            'name' => 'Jane',
            'surname' => 'Smith',
            'email' => 'jane.smith@example.com',
            'phone' => '0900654321',
        ]);

        // -----------------------
        // Event types
        // -----------------------
        EventType::insert([
            ['name' => 'Training'],
            ['name' => 'Match'],
            ['name' => 'Competition'],
        ]);

        // -----------------------
        // Clubs
        // -----------------------
        $club1 = Club::create([
            'name' => 'AC Sparta',
            'phone' => '0900111222',
            'email' => 'info@acsparta.com',
            'webpage' => 'https://acsparta.com',
        ]);

        $club2 = Club::create([
            'name' => 'FC Dynamo',
            'phone' => '0900333444',
            'email' => 'info@fcdynamo.com',
            'webpage' => 'https://fcdynamo.com',
        ]);

        // -----------------------
        // Attach members to clubs
        // -----------------------
        $member->clubs()->attach($club1->id, ['joined_at' => now()]);
        $coachMember->clubs()->attach($club2->id, ['joined_at' => now()]);
    }
}
