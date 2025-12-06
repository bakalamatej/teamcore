<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Member;
use App\Models\EventType;
use App\Models\Club;
use App\Models\Event;
use App\Models\SportField;
use App\Models\Address;
use App\Models\EventClub;
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
        // Addresses
        // -----------------------
        $address1 = Address::create([
            'country' => 'Slovakia',
            'city' => 'Bratislava',
            'street' => 'Hlavná 1',
            'zip_code' => '81101',
        ]);

        $address2 = Address::create([
            'country' => 'Slovakia',
            'city' => 'Košice',
            'street' => 'Športová 5',
            'zip_code' => '04001',
        ]);

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
            ['name' => 'Tournament'],
        ]);

        $type1 = EventType::where('name', 'Training')->first();
        $type2 = EventType::where('name', 'Match')->first();

        // -----------------------
        // Sport fields
        // -----------------------
        $field1 = SportField::create([
            'name' => 'AC Sparta Stadium',
            'field_type' => 'Football Stadium',
            'address_id' => $address1->id,
        ]);

        $field2 = SportField::create([
            'name' => 'FC Dynamo Arena',
            'field_type' => 'Football Field',
            'address_id' => $address2->id,
        ]);

        // -----------------------
        // Clubs
        // -----------------------
        $club1 = Club::create([
            'name' => 'AC Sparta',
            'phone' => '0900111222',
            'email' => 'info@acsparta.com',
            'webpage' => 'https://acsparta.com',
            'address_id' => $address1->id,
        ]);

        $club2 = Club::create([
            'name' => 'FC Dynamo',
            'phone' => '0900333444',
            'email' => 'info@fcdynamo.com',
            'webpage' => 'https://fcdynamo.com',
            'address_id' => $address2->id,
        ]);

        // -----------------------
        // Events
        // -----------------------
        $event1 = Event::create([
            'title' => 'Morning Training',
            'description' => 'Regular morning training session for all players.',
            'event_type_id' => $type1->id,
            'status' => Event::STATUS_SCHEDULED,
            'start_date' => now()->addDays(1)->setHour(9),
            'end_date' => now()->addDays(1)->setHour(11),
            'sport_field_id' => $field1->id,
        ]);
 
        $event2 = Event::create([
            'title' => 'Friendly Match',
            'description' => 'Friendly match against local rivals.',
            'event_type_id' => $type2->id,
            'status' => Event::STATUS_SCHEDULED,
            'start_date' => now()->addDays(3)->setHour(15),
            'end_date' => now()->addDays(3)->setHour(17),
            'sport_field_id' => $field2->id,
        ]);

        // -----------------------
        // Event club associations
        // -----------------------
        $club_event1 = EventClub::create([
            'event_id' => $event2->id,
            'club_id' => $club1->id,
        ]);

        // -----------------------
        // Attach members to clubs
        // -----------------------
        $member->clubs()->attach($club1->id, ['joined_at' => now()]);
        $coachMember->clubs()->attach($club2->id, ['joined_at' => now()]);
    }
}
