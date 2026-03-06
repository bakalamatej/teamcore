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
use App\Models\MemberStatistic;
use App\Models\Reservation;
use App\Models\EventStatistic;
use App\Models\CoachEvaluation;
use App\Models\ClubStatistic;
use App\Models\FieldType;
use App\Models\FileCategory;
use App\Enums\EventStatus;
use App\Enums\ReservationStatus;
use App\Enums\MemberClubRole;
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
        // Field Types
        // -----------------------
        $fieldTypeIndoor = FieldType::create(['name' => 'indoor']);
        $fieldTypeOutdoor = FieldType::create(['name' => 'outdoor']);

        // -----------------------
        // File Categories
        // -----------------------
        $catCertificate = FileCategory::create(['name' => 'certificate']);
        $catContract = FileCategory::create(['name' => 'contract']);
        $catPhoto = FileCategory::create(['name' => 'photo']);
        $catDocument = FileCategory::create(['name' => 'document']);
        $catReport = FileCategory::create(['name' => 'report']);

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
            'user_id' => $user->user_id,
            'first_name' => 'John',
            'last_name' => 'Doe',
            'phone' => '0900123456',
            'date_of_birth' => '1990-05-15',
        ]);

        $coachMember = Member::create([
            'user_id' => $coach->user_id,
            'first_name' => 'Jane',
            'last_name' => 'Smith',
            'phone' => '0900654321',
            'date_of_birth' => '1988-03-20',
        ]);

        $adminMember = Member::create([
            'user_id' => $admin->user_id,
            'first_name' => 'Admin',
            'last_name' => 'User',
            'phone' => '0900999999',
            'date_of_birth' => '1985-01-10',
        ]);

        // -----------------------
        // Sport fields
        // -----------------------
        $field1 = SportField::create([
            'name' => 'AC Sparta Stadium',
            'field_type_id' => $fieldTypeOutdoor->field_type_id,
            'address_id' => $address1->address_id,
        ]);

        $field2 = SportField::create([
            'name' => 'FC Dynamo Arena',
            'field_type_id' => $fieldTypeOutdoor->field_type_id,
            'address_id' => $address2->address_id,
        ]);

        // -----------------------
        // Sport fields - sports associations
        // -----------------------
        $field1->sports()->attach($sportFootball->sport_id);
        $field2->sports()->attach($sportFootball->sport_id);

        // -----------------------  
        // Event types
        // -----------------------
        $type1 = EventType::create([
            'name' => 'Training',
            'sport_id' => $sportFootball->sport_id,
        ]);

        $type2 = EventType::create([
            'name' => 'Match',
            'sport_id' => $sportFootball->sport_id,
        ]);

        $type3 = EventType::create([
            'name' => 'Tournament',
            'sport_id' => $sportFootball->sport_id,
        ]);

        // -----------------------
        // Clubs
        // -----------------------
        $club1 = Club::create([
            'name' => 'AC Sparta',
            'phone' => '0900111222',
            'email' => 'info@acsparta.com',
            'webpage' => 'https://acsparta.com',
            'address_id' => $address1->address_id,
            'sport_id' => $sportFootball->sport_id,
        ]);

        $club2 = Club::create([
            'name' => 'FC Dynamo',
            'phone' => '0900333444',
            'email' => 'info@fcdynamo.com',
            'webpage' => 'https://fcdynamo.com',
            'address_id' => $address2->address_id,
            'sport_id' => $sportFootball->sport_id,
        ]);

        // -----------------------
        // Attach sports to clubs
        // -----------------------
        $club1->sports()->attach($sportFootball->sport_id);
        $club2->sports()->attach($sportFootball->sport_id);

        // -----------------------
        // Events
        // -----------------------
        $event1 = Event::create([
            'title' => 'Morning Training',
            'description' => 'Regular morning training session for all players.',
            'sport_id' => $sportFootball->sport_id,
            'event_type_id' => $type1->event_type_id,
            'status' => EventStatus::SCHEDULED->value,
            'start_date' => now()->addDays(1)->setHour(9),
            'end_date' => now()->addDays(1)->setHour(11),
            'sport_field_id' => $field1->sport_field_id,
        ]);
 
        $event2 = Event::create([
            'title' => 'Friendly Match',
            'description' => 'Friendly match against local rivals.',
            'sport_id' => $sportFootball->sport_id,
            'event_type_id' => $type2->event_type_id,
            'status' => EventStatus::SCHEDULED->value,
            'start_date' => now()->addDays(3)->setHour(15),
            'end_date' => now()->addDays(3)->setHour(17),
            'sport_field_id' => $field2->sport_field_id,
        ]);

        // -----------------------
        // Event club associations
        // -----------------------
        EventClub::create([
            'event_id' => $event1->event_id,
            'club_id' => $club1->club_id,
        ]);

        EventClub::create([
            'event_id' => $event2->event_id,
            'club_id' => $club1->club_id,
        ]);

        EventClub::create([
            'event_id' => $event2->event_id,
            'club_id' => $club2->club_id,
        ]);

        // -----------------------
        // Attach members to clubs
        // -----------------------
        $member->clubs()->attach($club1->club_id, ['joined_at' => now(), 'role' => MemberClubRole::PLAYER->value]);
        $coachMember->clubs()->attach($club2->club_id, ['joined_at' => now(), 'role' => MemberClubRole::COACH->value]);
        $adminMember->clubs()->attach($club1->club_id, ['joined_at' => now(), 'role' => MemberClubRole::COACH->value]);

        $memberClub1 = MemberClub::where('member_id', $member->member_id)
                                 ->where('club_id', $club1->club_id)
                                 ->first();
        
        $memberClubCoach = MemberClub::where('member_id', $coachMember->member_id)
                                     ->where('club_id', $club2->club_id)
                                     ->first();

        $memberClubAdmin = MemberClub::where('member_id', $adminMember->member_id)
                                    ->where('club_id', $club1->club_id)
                                    ->first();

        // -----------------------
        // Member - event associations
        // -----------------------
        MemberEvent::create([
            'member_club_id' => $memberClub1->member_club_id,
            'event_id' => $event1->event_id,
        ]);

        MemberEvent::create([
            'member_club_id' => $memberClub1->member_club_id,
            'event_id' => $event2->event_id,
        ]);

        MemberEvent::create([
            'member_club_id' => $memberClubCoach->member_club_id,
            'event_id' => $event2->event_id,
        ]);

        // -----------------------
        // Event member results
        // -----------------------
        EventMemberResult::create([
            'event_id' => $event1->event_id,
            'member_club_id' => $memberClub1->member_club_id,
            'score' => 85,
            'ranking' => 1,
            'note' => 'Excellent performance during training.',
        ]);

        EventMemberResult::create([
            'event_id' => $event2->event_id,
            'member_club_id' => $memberClub1->member_club_id,
            'score' => 92,
            'ranking' => 1,
            'note' => 'Man of the match.',
        ]);

        EventMemberResult::create([
            'event_id' => $event2->event_id,
            'member_club_id' => $memberClubCoach->member_club_id,
            'score' => 88,
            'ranking' => 2,
            'note' => 'Good performance as goalkeeper.',
        ]);

        // -----------------------
        // Event club results
        // -----------------------
        EventClubResult::create([
            'event_id' => $event2->event_id,
            'club_id' => $club1->club_id,
            'score' => 3,
            'ranking' => 1,
            'note' => 'Victory against FC Dynamo.',
        ]);

        EventClubResult::create([
            'event_id' => $event2->event_id,
            'club_id' => $club2->club_id,
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
            'uploaded_by_user_id' => $admin->user_id,
        ]);

        $file2 = File::create([
            'file_name' => 'club_document.pdf',
            'file_path' => 'files/club_document_1739816493_5678.pdf',
            'file_type' => 'application/pdf',
            'file_size' => 1024000,
            'uploaded_by_user_id' => $admin->user_id,
        ]);

        // -----------------------
        // Attach files using the new pivot tables
        // -----------------------
        // Club files
        $club1->clubFiles()->attach($file2->file_id, ['file_category_id' => $catDocument->category_id]);
        $club2->clubFiles()->attach($file1->file_id, ['file_category_id' => $catPhoto->category_id]);

        // Event files
        $event1->eventFiles()->attach($file1->file_id, ['file_category_id' => $catPhoto->category_id]);
        $event2->eventFiles()->attach($file2->file_id, ['file_category_id' => $catDocument->category_id]);

        // Member club files
        if ($memberClub1) {
            $memberClub1->memberClubFiles()->attach($file1->file_id, ['file_category_id' => $catDocument->category_id]);
        }
        
        if ($memberClubCoach) {
            $memberClubCoach->memberClubFiles()->attach($file2->file_id, ['file_category_id' => $catPhoto->category_id]);
        }

        // -----------------------
        // Reservations
        // -----------------------
        $reservation1 = Reservation::create([
            'sport_id' => $sportFootball->sport_id,
            'sport_field_id' => $field1->sport_field_id,
            'club_id' => $club1->club_id,
            'created_by_member_club_id' => $memberClub1->member_club_id,
            'title' => 'Training Session - AC Sparta',
            'description' => 'Regular training session for all players of AC Sparta.',
            'start_date' => now()->addDays(2),
            'end_date' => now()->addDays(2)->addHours(2),
            'status' => ReservationStatus::APPROVED->value,
        ]);

        $reservation2 = Reservation::create([
            'sport_id' => $sportFootball->sport_id,
            'sport_field_id' => $field2->sport_field_id,
            'club_id' => $club2->club_id,
            'created_by_member_club_id' => $memberClubCoach->member_club_id,
            'title' => 'Match Preparation - FC Dynamo',
            'description' => 'Match preparation and training for upcoming friendly match.',
            'start_date' => now()->addDays(5),
            'end_date' => now()->addDays(5)->addHours(3),
            'status' => ReservationStatus::PENDING->value,
        ]);

        $reservation3 = Reservation::create([
            'sport_id' => $sportFootball->sport_id,
            'sport_field_id' => $field1->sport_field_id,
            'club_id' => $club1->club_id,
            'created_by_member_club_id' => $memberClubAdmin->member_club_id,
            'title' => 'Youth Tournament Preparation',
            'description' => 'Training and preparation for upcoming youth tournament.',
            'start_date' => now()->addDays(7),
            'end_date' => now()->addDays(7)->addHours(4),
            'status' => ReservationStatus::APPROVED->value,
        ]);

        // -----------------------
        // Coach Evaluations
        // -----------------------
        CoachEvaluation::create([
            'coach_member_club_id' => $coachMember->member_id,
            'evaluated_by_member_id' => $member->member_id,
            'reservation_id' => $reservation1->reservation_id,
            'rating' => 5,
            'comment' => 'Excellent coach, very professional and dedicated to player development.',
        ]);

        CoachEvaluation::create([
            'coach_member_club_id' => $coachMember->member_id,
            'evaluated_by_member_id' => $adminMember->member_id,
            'reservation_id' => $reservation2->reservation_id,
            'rating' => 4,
            'comment' => 'Good coaching, needs to improve communication with players.',
        ]);

        CoachEvaluation::create([
            'coach_member_club_id' => $adminMember->member_id,
            'evaluated_by_member_id' => $member->member_id,
            'reservation_id' => $reservation3->reservation_id,
            'rating' => 4,
            'comment' => 'Solid coaching skills, good tactical knowledge.',
        ]);
    }
}