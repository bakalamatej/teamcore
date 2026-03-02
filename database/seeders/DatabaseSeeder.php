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
use App\Models\Sport;
use App\Models\MemberEvent;
use App\Models\EventMemberResult;
use App\Models\EventClubResult;
use App\Models\File;
use App\Models\MemberClub;
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
        // Sports
        // -----------------------
        $sportFootball = Sport::create(['name' => 'Football']);
        $sportBasketball = Sport::create(['name' => 'Basketball']);
        $sportVolleyball = Sport::create(['name' => 'Volleyball']);

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
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
            'is_admin' => false,
        ]);

        $coach = User::factory()->create([
            'email' => 'coach@example.com',
            'password' => bcrypt('password'),
            'is_admin' => false,
        ]);

        $admin = User::factory()->create([
            'email' => 'admin@example.com',
            'password' => bcrypt('password'),
            'is_admin' => true,
        ]);

        // -----------------------
        // Members
        // -----------------------
        $member = Member::create([
            'user_id' => $user->id,
            'first_name' => 'John',
            'last_name' => 'Doe',
            'phone_number' => '0900123456',
            'date_of_birth' => '1990-05-15',
        ]);

        $coachMember = Member::create([
            'user_id' => $coach->id,
            'first_name' => 'Jane',
            'last_name' => 'Smith',
            'phone_number' => '0900654321',
            'date_of_birth' => '1988-03-20',
        ]);

        $adminMember = Member::create([
            'user_id' => $admin->id,
            'first_name' => 'Admin',
            'last_name' => 'User',
            'phone_number' => '0900999999',
            'date_of_birth' => '1985-01-10',
        ]);

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
        // Sport fields - sports associations
        // -----------------------
        $field1->sports()->attach($sportFootball->id);
        $field2->sports()->attach($sportFootball->id);

        // -----------------------  
        // Event types
        // -----------------------
        $type1 = EventType::create([
            'name' => 'Training',
            'sport_id' => $sportFootball->id,
        ]);

        $type2 = EventType::create([
            'name' => 'Match',
            'sport_id' => $sportFootball->id,
        ]);

        $type3 = EventType::create([
            'name' => 'Tournament',
            'sport_id' => $sportFootball->id,
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
            'sport_id' => $sportFootball->id,
        ]);

        $club2 = Club::create([
            'name' => 'FC Dynamo',
            'phone' => '0900333444',
            'email' => 'info@fcdynamo.com',
            'webpage' => 'https://fcdynamo.com',
            'address_id' => $address2->id,
            'sport_id' => $sportFootball->id,
        ]);

        // -----------------------
        // Events
        // -----------------------
        $event1 = Event::create([
            'title' => 'Morning Training',
            'description' => 'Regular morning training session for all players.',
            'event_type_id' => $type1->id,
            'status' => 'scheduled',
            'start_date' => now()->addDays(1)->setHour(9),
            'end_date' => now()->addDays(1)->setHour(11),
            'sport_field_id' => $field1->id,
        ]);
 
        $event2 = Event::create([
            'title' => 'Friendly Match',
            'description' => 'Friendly match against local rivals.',
            'event_type_id' => $type2->id,
            'status' => 'scheduled',
            'start_date' => now()->addDays(3)->setHour(15),
            'end_date' => now()->addDays(3)->setHour(17),
            'sport_field_id' => $field2->id,
        ]);

        // -----------------------
        // Event club associations
        // -----------------------
        EventClub::create([
            'event_id' => $event1->id,
            'club_id' => $club1->id,
        ]);

        EventClub::create([
            'event_id' => $event2->id,
            'club_id' => $club1->id,
        ]);

        EventClub::create([
            'event_id' => $event2->id,
            'club_id' => $club2->id,
        ]);

        // -----------------------
        // Member - event associations
        // -----------------------
        MemberEvent::create([
            'member_id' => $member->id,
            'event_id' => $event1->id,
        ]);

        MemberEvent::create([
            'member_id' => $member->id,
            'event_id' => $event2->id,
        ]);

        MemberEvent::create([
            'member_id' => $coachMember->id,
            'event_id' => $event2->id,
        ]);

        // -----------------------
        // Attach members to clubs
        // -----------------------
        $member->clubs()->attach($club1->id, ['joined_at' => now(), 'role' => 'player']);
        $coachMember->clubs()->attach($club2->id, ['joined_at' => now(), 'role' => 'coach']);
        $adminMember->clubs()->attach($club1->id, ['joined_at' => now(), 'role' => 'admin']);

        // -----------------------
        // Event member results
        // -----------------------
        EventMemberResult::create([
            'event_id' => $event1->id,
            'member_id' => $member->id,
            'score' => 85,
            'ranking' => 1,
            'note' => 'Excellent performance during training.',
        ]);

        EventMemberResult::create([
            'event_id' => $event2->id,
            'member_id' => $member->id,
            'score' => 92,
            'ranking' => 1,
            'note' => 'Man of the match.',
        ]);

        EventMemberResult::create([
            'event_id' => $event2->id,
            'member_id' => $coachMember->id,
            'score' => 88,
            'ranking' => 2,
            'note' => 'Good performance as goalkeeper.',
        ]);

        // -----------------------
        // Event club results
        // -----------------------
        EventClubResult::create([
            'event_id' => $event2->id,
            'club_id' => $club1->id,
            'score' => 3,
            'ranking' => 1,
            'note' => 'Victory against FC Dynamo.',
        ]);

        EventClubResult::create([
            'event_id' => $event2->id,
            'club_id' => $club2->id,
            'score' => 1,
            'ranking' => 2,
            'note' => 'Second place in friendly match.',
        ]);

        // -----------------------
        // Files
        // -----------------------
        $file1 = File::create([
            'file_name' => 'event_photo.jpg',
            'file_path' => 'files/event_photo_1739816492_1234.jpg',
            'file_type' => 'image/jpeg',
            'file_size' => 2048576,
        ]);

        $file2 = File::create([
            'file_name' => 'club_document.pdf',
            'file_path' => 'files/club_document_1739816493_5678.pdf',
            'file_type' => 'application/pdf',
            'file_size' => 1024000,
        ]);

        // -----------------------
        // Attach files using the new pivot tables
        // -----------------------
        // Club files
        $club1->clubFiles()->attach($file2->id, ['file_category' => 'document']);
        $club2->clubFiles()->attach($file1->id, ['file_category' => 'photo']);

        // Event files
        $event1->eventFiles()->attach($file1->id, ['file_category' => 'photo']);
        $event2->eventFiles()->attach($file2->id, ['file_category' => 'document']);

        // Member club files
        $memberClub1 = MemberClub::where('member_id', $member->id)
                                 ->where('club_id', $club1->id)
                                 ->first();
        
        if ($memberClub1) {
            $memberClub1->memberClubFiles()->attach($file1->id, ['file_category' => 'document']);
        }

        $memberClubCoach = MemberClub::where('member_id', $coachMember->id)
                                     ->where('club_id', $club2->id)
                                     ->first();
        
        if ($memberClubCoach) {
            $memberClubCoach->memberClubFiles()->attach($file2->id, ['file_category' => 'photo']);
        }
    }
}
