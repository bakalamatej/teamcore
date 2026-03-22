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
use App\Models\Reservation;
use App\Models\CoachEvaluation;
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
        $address1 = Address::create(['country' => 'Slovakia', 'city' => 'Bratislava', 'street' => 'Hlavná 1', 'zip_code' => '81101']);
        $address2 = Address::create(['country' => 'Slovakia', 'city' => 'Košice', 'street' => 'Športová 5', 'zip_code' => '04001']);
        $address3 = Address::create(['country' => 'Slovakia', 'city' => 'Žilina', 'street' => 'Námestie Slobody 3', 'zip_code' => '01001']);
        $address4 = Address::create(['country' => 'Slovakia', 'city' => 'Prešov', 'street' => 'Hlavná 22', 'zip_code' => '08001']);
        $address5 = Address::create(['country' => 'Slovakia', 'city' => 'Lokca', 'street' => 'Lokca 123', 'zip_code' => '02952']);

        // -----------------------
        // Users
        // -----------------------
        $userJohn = User::factory()->create(['email' => 'test@example.com', 'password_hash' => 'password', 'is_admin' => false]);
        $userCoach = User::factory()->create(['email' => 'coach@example.com', 'password_hash' => 'password', 'is_admin' => false]);
        $userAdmin = User::factory()->create(['email' => 'admin@example.com', 'password_hash' => 'password', 'is_admin' => true]);
        $userPeter = User::factory()->create(['email' => 'peter@example.com', 'password_hash' => 'password', 'is_admin' => false]);
        $userLucia = User::factory()->create(['email' => 'lucia@example.com', 'password_hash' => 'password', 'is_admin' => false]);
        $userMartin = User::factory()->create(['email' => 'martin@example.com', 'password_hash' => 'password', 'is_admin' => false]);
        $userKarol = User::factory()->create(['email' => 'karol@example.com', 'password_hash' => 'password', 'is_admin' => false]);
        $userCoach2 = User::factory()->create(['email' => 'coach2@example.com', 'password_hash' => 'password', 'is_admin' => false]);

        // -----------------------
        // Members
        // -----------------------
        $member = Member::create(['user_id' => $userJohn->user_id, 'first_name' => 'John', 'last_name' => 'Doe', 'phone' => '0900123456', 'date_of_birth' => '1990-05-15']);
        $coachMember = Member::create(['user_id' => $userCoach->user_id, 'first_name' => 'Jane', 'last_name' => 'Smith', 'phone' => '0900654321', 'date_of_birth' => '1988-03-20']);
        $adminMember = Member::create(['user_id' => $userAdmin->user_id, 'first_name' => 'Admin', 'last_name' => 'User', 'phone' => '0900999999', 'date_of_birth' => '1985-01-10']);
        $memberPeter = Member::create(['user_id' => $userPeter->user_id, 'first_name' => 'Peter', 'last_name' => 'Novák', 'phone' => '0901111222', 'date_of_birth' => '1995-07-22']);
        $memberLucia = Member::create(['user_id' => $userLucia->user_id, 'first_name' => 'Lucia', 'last_name' => 'Kováčová', 'phone' => '0902222333', 'date_of_birth' => '1993-11-30']);
        $memberMartin = Member::create(['user_id' => $userMartin->user_id, 'first_name' => 'Martin', 'last_name' => 'Horváth', 'phone' => '0903333444', 'date_of_birth' => '1997-02-14']);
        $memberKarol = Member::create(['user_id' => $userKarol->user_id, 'first_name' => 'Karol', 'last_name' => 'Varga', 'phone' => '0904444555', 'date_of_birth' => '1992-08-05']);
        $coachMember2 = Member::create(['user_id' => $userCoach2->user_id, 'first_name' => 'Tomáš', 'last_name' => 'Baláž', 'phone' => '0905555666', 'date_of_birth' => '1980-06-18']);

        // -----------------------
        // Sport fields
        // -----------------------
        $field1 = SportField::create(['name' => 'AC Sparta Stadium', 'field_type_id' => $fieldTypeOutdoor->field_type_id, 'address_id' => $address1->address_id]);
        $field2 = SportField::create(['name' => 'FC Dynamo Arena', 'field_type_id' => $fieldTypeOutdoor->field_type_id, 'address_id' => $address2->address_id]);
        $field3 = SportField::create(['name' => 'Žilina Sports Hall', 'field_type_id' => $fieldTypeIndoor->field_type_id, 'address_id' => $address3->address_id]);
        $field4 = SportField::create(['name' => 'Prešov Basketball Court', 'field_type_id' => $fieldTypeIndoor->field_type_id, 'address_id' => $address4->address_id]);

        $field1->sports()->attach($sportFootball->sport_id);
        $field2->sports()->attach($sportFootball->sport_id);
        $field3->sports()->attach([$sportBasketball->sport_id, $sportVolleyball->sport_id]);
        $field4->sports()->attach($sportBasketball->sport_id);

        // -----------------------
        // Event types
        // -----------------------
        $typeTraining = EventType::create(['name' => 'Training', 'sport_id' => $sportFootball->sport_id]);
        $typeMatch = EventType::create(['name' => 'Match', 'sport_id' => $sportFootball->sport_id]);
        $typeTournament = EventType::create(['name' => 'Tournament', 'sport_id' => $sportFootball->sport_id]);
        $typeBbTraining = EventType::create(['name' => 'Training', 'sport_id' => $sportBasketball->sport_id]);
        $typeBbMatch = EventType::create(['name' => 'Match', 'sport_id' => $sportBasketball->sport_id]);

        // -----------------------
        // Clubs
        // -----------------------
        $club1 = Club::create(['name' => 'AC Sparta', 'phone' => '0900111222', 'email' => 'info@acsparta.com', 'webpage' => 'https://acsparta.com', 'address_id' => $address1->address_id, 'sport_id' => $sportFootball->sport_id]);
        $club2 = Club::create(['name' => 'FC Dynamo', 'phone' => '0900333444', 'email' => 'info@fcdynamo.com', 'webpage' => 'https://fcdynamo.com', 'address_id' => $address2->address_id, 'sport_id' => $sportFootball->sport_id]);
        $club3 = Club::create(['name' => 'Žilina Ballers', 'phone' => '0900555666', 'email' => 'info@zilinaballers.com', 'webpage' => 'https://zilinaballers.com', 'address_id' => $address3->address_id, 'sport_id' => $sportBasketball->sport_id]);

        $club1->sports()->attach($sportFootball->sport_id);
        $club2->sports()->attach($sportFootball->sport_id);
        $club3->sports()->attach($sportBasketball->sport_id);

        // -----------------------
        // Member club memberships
        // -----------------------
        $member->clubs()->attach($club1->club_id, ['joined_at' => now()->subMonths(6), 'role' => MemberClubRole::PLAYER->value, 'sport_id' => $sportFootball->sport_id]);
        $coachMember->clubs()->attach($club2->club_id, ['joined_at' => now()->subYear(), 'role' => MemberClubRole::COACH->value, 'sport_id' => $sportFootball->sport_id]);
        $adminMember->clubs()->attach($club1->club_id, ['joined_at' => now()->subYears(2), 'role' => MemberClubRole::COACH->value, 'sport_id' => $sportFootball->sport_id]);
        $memberPeter->clubs()->attach($club1->club_id, ['joined_at' => now()->subMonths(4), 'role' => MemberClubRole::PLAYER->value, 'sport_id' => $sportFootball->sport_id]);
        $memberLucia->clubs()->attach($club2->club_id, ['joined_at' => now()->subMonths(3), 'role' => MemberClubRole::PLAYER->value, 'sport_id' => $sportFootball->sport_id]);
        $memberMartin->clubs()->attach($club1->club_id, ['joined_at' => now()->subMonths(8), 'role' => MemberClubRole::PLAYER->value, 'sport_id' => $sportFootball->sport_id]);
        $memberKarol->clubs()->attach($club2->club_id, ['joined_at' => now()->subMonths(5), 'role' => MemberClubRole::PLAYER->value, 'sport_id' => $sportFootball->sport_id]);
        $coachMember2->clubs()->attach($club1->club_id, ['joined_at' => now()->subYears(3), 'role' => MemberClubRole::COACH->value, 'sport_id' => $sportFootball->sport_id]);
        $memberPeter->clubs()->attach($club3->club_id, ['joined_at' => now()->subMonths(2), 'role' => MemberClubRole::PLAYER->value, 'sport_id' => $sportBasketball->sport_id]);

        $mc1 = MemberClub::where('member_id', $member->member_id)->where('club_id', $club1->club_id)->first();
        $mcCoach = MemberClub::where('member_id', $coachMember->member_id)->where('club_id', $club2->club_id)->first();
        $mcAdmin = MemberClub::where('member_id', $adminMember->member_id)->where('club_id', $club1->club_id)->first();
        $mcPeter1 = MemberClub::where('member_id', $memberPeter->member_id)->where('club_id', $club1->club_id)->first();
        $mcLucia = MemberClub::where('member_id', $memberLucia->member_id)->where('club_id', $club2->club_id)->first();
        $mcMartin = MemberClub::where('member_id', $memberMartin->member_id)->where('club_id', $club1->club_id)->first();
        $mcKarol = MemberClub::where('member_id', $memberKarol->member_id)->where('club_id', $club2->club_id)->first();
        $mcCoach2 = MemberClub::where('member_id', $coachMember2->member_id)->where('club_id', $club1->club_id)->first();

        // -----------------------
        // Events - finished (past)
        // -----------------------
        $eventFinished1 = Event::create([
            'title' => 'League Match - Round 1',
            'description' => 'First round of the league season.',
            'sport_id' => $sportFootball->sport_id,
            'event_type_id' => $typeMatch->event_type_id,
            'status' => EventStatus::FINISHED->value,
            'start_date' => now()->subDays(30)->setHour(15),
            'end_date' => now()->subDays(30)->setHour(17),
            'sport_field_id' => $field1->sport_field_id,
        ]);

        $eventFinished2 = Event::create([
            'title' => 'Spring Tournament 2025',
            'description' => 'Annual spring football tournament.',
            'sport_id' => $sportFootball->sport_id,
            'event_type_id' => $typeTournament->event_type_id,
            'status' => EventStatus::FINISHED->value,
            'start_date' => now()->subDays(14)->setHour(10),
            'end_date' => now()->subDays(14)->setHour(18),
            'sport_field_id' => $field2->sport_field_id,
        ]);

        $eventFinished3 = Event::create([
            'title' => 'Pre-season Training Camp',
            'description' => 'Intensive pre-season training for all players.',
            'sport_id' => $sportFootball->sport_id,
            'event_type_id' => $typeTraining->event_type_id,
            'status' => EventStatus::FINISHED->value,
            'start_date' => now()->subDays(60)->setHour(9),
            'end_date' => now()->subDays(60)->setHour(12),
            'sport_field_id' => $field1->sport_field_id,
        ]);

        // -----------------------
        // Events - ongoing
        // -----------------------
        $eventOngoing = Event::create([
            'title' => 'Weekly Training Session',
            'description' => 'Regular weekly training.',
            'sport_id' => $sportFootball->sport_id,
            'event_type_id' => $typeTraining->event_type_id,
            'status' => EventStatus::ONGOING->value,
            'start_date' => now()->subHours(1),
            'end_date' => now()->addHours(2),
            'sport_field_id' => $field1->sport_field_id,
        ]);

        // -----------------------
        // Events - scheduled (future)
        // -----------------------
        $eventScheduled1 = Event::create([
            'title' => 'Morning Training',
            'description' => 'Regular morning training session.',
            'sport_id' => $sportFootball->sport_id,
            'event_type_id' => $typeTraining->event_type_id,
            'status' => EventStatus::SCHEDULED->value,
            'start_date' => now()->addDays(1)->setHour(9),
            'end_date' => now()->addDays(1)->setHour(11),
            'sport_field_id' => $field1->sport_field_id,
        ]);

        $eventScheduled2 = Event::create([
            'title' => 'Friendly Match',
            'description' => 'Friendly match against FC Dynamo.',
            'sport_id' => $sportFootball->sport_id,
            'event_type_id' => $typeMatch->event_type_id,
            'status' => EventStatus::SCHEDULED->value,
            'start_date' => now()->addDays(3)->setHour(15),
            'end_date' => now()->addDays(3)->setHour(17),
            'sport_field_id' => $field2->sport_field_id,
        ]);

        $eventScheduled3 = Event::create([
            'title' => 'Summer Cup Preparation',
            'description' => 'Preparation for summer cup tournament.',
            'sport_id' => $sportFootball->sport_id,
            'event_type_id' => $typeTraining->event_type_id,
            'status' => EventStatus::SCHEDULED->value,
            'start_date' => now()->addDays(7)->setHour(10),
            'end_date' => now()->addDays(7)->setHour(13),
            'sport_field_id' => $field1->sport_field_id,
        ]);

        // -----------------------
        // Event club associations
        // -----------------------
        foreach ([$eventFinished1, $eventFinished3, $eventOngoing, $eventScheduled1, $eventScheduled3] as $ev) {
            EventClub::create(['event_id' => $ev->event_id, 'club_id' => $club1->club_id]);
        }
        foreach ([$eventFinished2, $eventScheduled2] as $ev) {
            EventClub::create(['event_id' => $ev->event_id, 'club_id' => $club1->club_id]);
            EventClub::create(['event_id' => $ev->event_id, 'club_id' => $club2->club_id]);
        }

        // -----------------------
        // Member event registrations
        // -----------------------
        $registrations = [
            [$mc1, $eventFinished1], [$mc1, $eventFinished2], [$mc1, $eventFinished3],
            [$mc1, $eventOngoing], [$mc1, $eventScheduled1], [$mc1, $eventScheduled2],
            [$mcPeter1, $eventFinished1], [$mcPeter1, $eventFinished2], [$mcPeter1, $eventScheduled1],
            [$mcMartin, $eventFinished1], [$mcMartin, $eventFinished3], [$mcMartin, $eventOngoing], [$mcMartin, $eventScheduled1],
            [$mcAdmin, $eventFinished1], [$mcAdmin, $eventFinished3], [$mcAdmin, $eventScheduled3],
            [$mcCoach2, $eventFinished1], [$mcCoach2, $eventFinished3],
            [$mcLucia, $eventFinished2], [$mcLucia, $eventScheduled2],
            [$mcKarol, $eventFinished2], [$mcKarol, $eventScheduled2],
            [$mcCoach, $eventFinished2], [$mcCoach, $eventScheduled2],
        ];

        foreach ($registrations as [$mc, $ev]) {
            MemberEvent::create(['member_club_id' => $mc->member_club_id, 'event_id' => $ev->event_id]);
        }

        // -----------------------
        // Event member results (finished events only)
        // -----------------------
        $memberResults = [
            [$eventFinished1->event_id, $mc1->member_club_id, 85, 1, 'Excellent performance.'],
            [$eventFinished1->event_id, $mcPeter1->member_club_id, 78, 2, 'Good effort.'],
            [$eventFinished1->event_id, $mcMartin->member_club_id, 72, 3, 'Needs improvement.'],
            [$eventFinished1->event_id, $mcAdmin->member_club_id, 80, 1, 'Solid coaching.'],
            [$eventFinished2->event_id, $mc1->member_club_id, 92, 1, 'Man of the match.'],
            [$eventFinished2->event_id, $mcPeter1->member_club_id, 88, 2, 'Strong performance.'],
            [$eventFinished2->event_id, $mcLucia->member_club_id, 75, 3, 'Good game.'],
            [$eventFinished2->event_id, $mcKarol->member_club_id, 70, 4, 'Average performance.'],
            [$eventFinished3->event_id, $mc1->member_club_id, 88, 1, 'Best in camp.'],
            [$eventFinished3->event_id, $mcMartin->member_club_id, 82, 2, 'Good training.'],
            [$eventFinished3->event_id, $mcAdmin->member_club_id, 79, 2, 'Consistent.'],
            [$eventFinished3->event_id, $mcCoach2->member_club_id, 85, 1, 'Great coaching.'],
        ];

        foreach ($memberResults as [$eventId, $mcId, $score, $ranking, $note]) {
            EventMemberResult::create(['event_id' => $eventId, 'member_club_id' => $mcId, 'score' => $score, 'ranking' => $ranking, 'note' => $note]);
        }

        // -----------------------
        // Event club results (finished events only)
        // -----------------------
        EventClubResult::create(['event_id' => $eventFinished1->event_id, 'club_id' => $club1->club_id, 'score' => 2, 'ranking' => 1, 'note' => 'Home win.']);
        EventClubResult::create(['event_id' => $eventFinished2->event_id, 'club_id' => $club1->club_id, 'score' => 3, 'ranking' => 1, 'note' => 'Tournament winners.']);
        EventClubResult::create(['event_id' => $eventFinished2->event_id, 'club_id' => $club2->club_id, 'score' => 1, 'ranking' => 2, 'note' => 'Runners up.']);

        // -----------------------
        // Files
        // -----------------------
        $file1 = File::create(['file_name' => 'event_photo.jpg', 'file_path' => 'files/event_photo.jpg', 'file_type' => 'image/jpeg', 'file_size' => 2048576, 'uploaded_by_user_id' => $userAdmin->user_id]);
        $file2 = File::create(['file_name' => 'club_document.pdf', 'file_path' => 'files/club_document.pdf', 'file_type' => 'application/pdf', 'file_size' => 1024000, 'uploaded_by_user_id' => $userAdmin->user_id]);

        $club1->clubFiles()->attach($file2->file_id, ['file_category_id' => $catDocument->file_category_id]);
        $club2->clubFiles()->attach($file1->file_id, ['file_category_id' => $catPhoto->file_category_id]);
        $eventFinished1->eventFiles()->attach($file1->file_id, ['file_category_id' => $catPhoto->file_category_id]);
        $eventScheduled1->eventFiles()->attach($file2->file_id, ['file_category_id' => $catDocument->file_category_id]);
        $mc1->memberClubFiles()->attach($file1->file_id, ['file_category_id' => $catDocument->file_category_id]);

        // -----------------------
        // Reservations
        // -----------------------
        $res1 = Reservation::create(['sport_id' => $sportFootball->sport_id, 'sport_field_id' => $field1->sport_field_id, 'club_id' => $club1->club_id, 'created_by_member_club_id' => $mc1->member_club_id, 'title' => 'Training Session - AC Sparta', 'description' => 'Regular training.', 'start_date' => now()->addDays(2), 'end_date' => now()->addDays(2)->addHours(2), 'status' => ReservationStatus::APPROVED->value]);
        $res2 = Reservation::create(['sport_id' => $sportFootball->sport_id, 'sport_field_id' => $field2->sport_field_id, 'club_id' => $club2->club_id, 'created_by_member_club_id' => $mcCoach->member_club_id, 'title' => 'Match Preparation - FC Dynamo', 'description' => 'Match prep.', 'start_date' => now()->addDays(5), 'end_date' => now()->addDays(5)->addHours(3), 'status' => ReservationStatus::PENDING->value]);
        $res3 = Reservation::create(['sport_id' => $sportFootball->sport_id, 'sport_field_id' => $field1->sport_field_id, 'club_id' => $club1->club_id, 'created_by_member_club_id' => $mcAdmin->member_club_id, 'title' => 'Youth Tournament Prep', 'description' => 'Youth prep.', 'start_date' => now()->addDays(7), 'end_date' => now()->addDays(7)->addHours(4), 'status' => ReservationStatus::APPROVED->value]);
        $res4 = Reservation::create(['sport_id' => $sportFootball->sport_id, 'sport_field_id' => $field2->sport_field_id, 'club_id' => $club2->club_id, 'created_by_member_club_id' => $mcKarol->member_club_id, 'title' => 'Evening Practice', 'description' => 'Evening training.', 'start_date' => now()->addDays(4), 'end_date' => now()->addDays(4)->addHours(2), 'status' => ReservationStatus::PENDING->value]);
        $res5 = Reservation::create(['sport_id' => $sportFootball->sport_id, 'sport_field_id' => $field1->sport_field_id, 'club_id' => $club1->club_id, 'created_by_member_club_id' => $mcCoach2->member_club_id, 'title' => 'Weekend Drill', 'description' => 'Weekend drill session.', 'start_date' => now()->addDays(9), 'end_date' => now()->addDays(9)->addHours(3), 'status' => ReservationStatus::APPROVED->value]);

        // -----------------------
        // Coach Evaluations
        // -----------------------
        $evaluations = [
            [$mcCoach->member_club_id, $member->member_id, 5, 'Excellent coach, very professional.'],
            [$mcCoach->member_club_id, $adminMember->member_id, 4, 'Good coaching, needs to improve communication.'],
            [$mcCoach->member_club_id, $memberLucia->member_id, 5, 'Great motivator.'],
            [$mcAdmin->member_club_id, $member->member_id, 4, 'Solid coaching skills.'],
            [$mcAdmin->member_club_id, $memberPeter->member_id, 3, 'Average, needs more energy.'],
            [$mcCoach2->member_club_id, $member->member_id, 5, 'Best coach I have had.'],
            [$mcCoach2->member_club_id, $memberMartin->member_id, 4, 'Very knowledgeable.'],
        ];

        foreach ($evaluations as [$coachMcId, $evaluatedById, $rating, $comment]) {
            CoachEvaluation::create(['coach_member_club_id' => $coachMcId, 'evaluated_by_member_id' => $evaluatedById, 'rating' => $rating, 'comment' => $comment]);
        }
    }
}