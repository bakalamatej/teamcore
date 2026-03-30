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
use App\Enums\MemberClubRole;
use App\Enums\ReservationStatus;
use App\Enums\ResultType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        // -----------------------
        // Field Types (5 concrete)
        // -----------------------
        $fieldTypeIndoor = FieldType::firstOrCreate(['name' => 'indoor']);
        $fieldTypeOutdoor = FieldType::firstOrCreate(['name' => 'outdoor']);
        $fieldTypeGrass = FieldType::firstOrCreate(['name' => 'grass']);
        $fieldTypeTurf = FieldType::firstOrCreate(['name' => 'artificial turf']);
        $fieldTypeMixed = FieldType::firstOrCreate(['name' => 'mixed']);

        // -----------------------
        // File Categories
        // -----------------------
        $catCertificate = FileCategory::firstOrCreate(['name' => 'certificate']);
        $catContract = FileCategory::firstOrCreate(['name' => 'contract']);
        $catPhoto = FileCategory::firstOrCreate(['name' => 'photo']);
        $catDocument = FileCategory::firstOrCreate(['name' => 'document']);
        $catReport = FileCategory::firstOrCreate(['name' => 'report']);

        // -----------------------
        // Sports (6 concrete)
        // -----------------------
        $sports = collect(['Football', 'Basketball', 'Hockey', 'Floorball', 'Handball', 'Volleyball'])
            ->map(fn($name) => Sport::firstOrCreate(['name' => $name]));

        // Inverse map by name
        $sportsByName = $sports->keyBy(fn($sport) => $sport->name);


        // -----------------------
        // Addresses
        // -----------------------
        $address1 = Address::create([
            'country' => 'Slovakia',
            'city' => 'Bratislava',
            'street' => 'Hlavná 1',
            'zip_code' => '81101'
        ]);
        $address2 = Address::create([
            'country' => 'Slovakia',
            'city' => 'Košice',
            'street' => 'Športová 5',
            'zip_code' => '04001'
        ]);
        $address3 = Address::create([
            'country' => 'Slovakia',
            'city' => 'Žilina',
            'street' => 'Námestie Slobody 3',
            'zip_code' => '01001'
        ]);
        $address4 = Address::create([
            'country' => 'Slovakia',
            'city' => 'Prešov',
            'street' => 'Hlavná 22',
            'zip_code' => '08001'
        ]);
        $address5 = Address::create([
            'country' => 'Slovakia',
            'city' => 'Lokca',
            'street' => 'Lokca 123',
            'zip_code' => '02952'
        ]);

        // -----------------------
        // Users
        // -----------------------
        $userJohn = User::firstOrCreate(
            ['email' => 'test@example.com'],
            ['password_hash' => 'password', 'is_admin' => false]
        );
        $userCoach = User::firstOrCreate(
            ['email' => 'coach@example.com'],
            ['password_hash' => 'password', 'is_admin' => false]
        );
        $userAdmin = User::firstOrCreate(
            ['email' => 'admin@example.com'],
            ['password_hash' => 'password', 'is_admin' => true]
        );
        $userPeter = User::firstOrCreate(
            ['email' => 'peter@example.com'],
            ['password_hash' => 'password', 'is_admin' => false]
        );
        $userLucia = User::firstOrCreate(
            ['email' => 'lucia@example.com'],
            ['password_hash' => 'password', 'is_admin' => false]
        );
        $userMartin = User::firstOrCreate(
            ['email' => 'martin@example.com'],
            ['password_hash' => 'password', 'is_admin' => false]
        );
        $userKarol = User::firstOrCreate(
            ['email' => 'karol@example.com'],
            ['password_hash' => 'password', 'is_admin' => false]
        );
        $userCoach2 = User::firstOrCreate(
            ['email' => 'coach2@example.com'],
            ['password_hash' => 'password', 'is_admin' => false]
        );

        // -----------------------
        // Members
        // -----------------------
        $member = Member::firstOrCreate([
            'user_id' => $userJohn->user_id,
        ], [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'phone' => '0900123456',
            'date_of_birth' => '1990-05-15'
        ]);
        $coachMember = Member::firstOrCreate([
            'user_id' => $userCoach->user_id,
        ], [
            'first_name' => 'Jane',
            'last_name' => 'Smith',
            'phone' => '0900654321',
            'date_of_birth' => '1988-03-20'
        ]);
        $adminMember = Member::firstOrCreate([
            'user_id' => $userAdmin->user_id,
        ], [
            'first_name' => 'Admin',
            'last_name' => 'User',
            'phone' => '0900999999',
            'date_of_birth' => '1985-01-10'
        ]);
        $memberPeter = Member::firstOrCreate([
            'user_id' => $userPeter->user_id,
        ], [
            'first_name' => 'Peter',
            'last_name' => 'Novák',
            'phone' => '0901111222',
            'date_of_birth' => '1995-07-22'
        ]);
        $memberLucia = Member::firstOrCreate([
            'user_id' => $userLucia->user_id,
        ], [
            'first_name' => 'Lucia',
            'last_name' => 'Kováčová',
            'phone' => '0902222333',
            'date_of_birth' => '1993-11-30'
        ]);
        $memberMartin = Member::firstOrCreate([
            'user_id' => $userMartin->user_id,
        ], [
            'first_name' => 'Martin',
            'last_name' => 'Horváth',
            'phone' => '0903333444',
            'date_of_birth' => '1997-02-14'
        ]);
        $memberKarol = Member::firstOrCreate([
            'user_id' => $userKarol->user_id,
        ], [
            'first_name' => 'Karol',
            'last_name' => 'Varga',
            'phone' => '0904444555',
            'date_of_birth' => '1992-08-05'
        ]);
        $coachMember2 = Member::firstOrCreate([
            'user_id' => $userCoach2->user_id,
        ], [
            'first_name' => 'Tomáš',
            'last_name' => 'Baláž',
            'phone' => '0905555666',
            'date_of_birth' => '1980-06-18'
        ]);

        // -----------------------
        // Sport fields
        // -----------------------
        $field1 = SportField::create([
            'name' => 'AC Sparta Stadium',
            'field_type_id' => $fieldTypeOutdoor->field_type_id,
            'address_id' => $address1->address_id
        ]);
        $field2 = SportField::create([
            'name' => 'FC Dynamo Arena',
            'field_type_id' => $fieldTypeOutdoor->field_type_id,
            'address_id' => $address2->address_id
        ]);
        $field3 = SportField::create([
            'name' => 'Žilina Sports Hall',
            'field_type_id' => $fieldTypeIndoor->field_type_id,
            'address_id' => $address3->address_id
        ]);
        $field4 = SportField::create([
            'name' => 'Prešov Basketball Court',
            'field_type_id' => $fieldTypeIndoor->field_type_id,
            'address_id' => $address4->address_id
        ]);

        $field5 = SportField::create([
            'name' => 'Hockey Arena',
            'field_type_id' => $fieldTypeIndoor->field_type_id,
            'address_id' => $address4->address_id
        ]);

        $field6 = SportField::create([
            'name' => 'Floorball Arena',
            'field_type_id' => $fieldTypeIndoor->field_type_id,
            'address_id' => $address3->address_id
        ]);

        $field7 = SportField::create([
            'name' => 'Handball Center',
            'field_type_id' => $fieldTypeOutdoor->field_type_id,
            'address_id' => $address2->address_id
        ]);

        $field8 = SportField::create([
            'name' => 'Volleyball Arena',
            'field_type_id' => $fieldTypeOutdoor->field_type_id,
            'address_id' => $address1->address_id
        ]);

        $field1->sports()->attach($sportsByName['Football']->sport_id);
        $field2->sports()->attach($sportsByName['Football']->sport_id);
        $field3->sports()->attach($sportsByName['Basketball']->sport_id);
        $field4->sports()->attach($sportsByName['Basketball']->sport_id);
        $field5->sports()->attach($sportsByName['Hockey']->sport_id);
        $field6->sports()->attach($sportsByName['Floorball']->sport_id);
        $field7->sports()->attach($sportsByName['Handball']->sport_id);
        $field8->sports()->attach($sportsByName['Volleyball']->sport_id);

        $sportFieldsBySport = [
            'Football' => $field1->sport_field_id,
            'Basketball' => $field3->sport_field_id,
            'Hockey' => $field5->sport_field_id,
            'Floorball' => $field6->sport_field_id,
            'Handball' => $field7->sport_field_id,
            'Volleyball' => $field8->sport_field_id,
        ];

        // -----------------------
        // Event types
        // -----------------------
        $eventTypeMap = [];
        foreach ($sports as $sport) {
            foreach (['Training', 'Match', 'Tournament'] as $typeName) {
                $key = "{$sport->name}-{$typeName}";
                $eventTypeMap[$key] = EventType::firstOrCreate([
                    'name' => $typeName,
                    'sport_id' => $sport->sport_id,
                ]);
            }
        }

        // -----------------------
        // Clubs
        // -----------------------
        $club1 = Club::firstOrCreate([
            'name' => 'AC Sparta'
        ], [
            'phone' => '0900111222',
            'email' => 'info@acsparta.com',
            'webpage' => 'https://acsparta.com',
            'address_id' => $address1->address_id,
            'sport_id' => $sportsByName['Football']->sport_id,
        ]);
        $club2 = Club::firstOrCreate([
            'name' => 'FC Dynamo'
        ], [
            'phone' => '0900333444',
            'email' => 'info@fcdynamo.com',
            'webpage' => 'https://fcdynamo.com',
            'address_id' => $address2->address_id,
            'sport_id' => $sportsByName['Football']->sport_id,
        ]);
        $club3 = Club::firstOrCreate([
            'name' => 'Žilina Ballers'
        ], [
            'phone' => '0900555666',
            'email' => 'info@zilinaballers.com',
            'webpage' => 'https://zilinaballers.com',
            'address_id' => $address3->address_id,
            'sport_id' => $sportsByName['Basketball']->sport_id,
        ]);

        // 3x more clubs (at least 9 clubs total)
        $extraClubs = [
            ['name' => 'Bratislava United', 'phone' => '0901000001', 'email' => 'bratislava@example.com', 'webpage' => 'https://bratislava.local', 'address_id' => $address1->address_id, 'sport_id' => $sportsByName['Football']->sport_id],
            ['name' => 'Košice Stars', 'phone' => '0901000002', 'email' => 'kosice@example.com', 'webpage' => 'https://kosice.local', 'address_id' => $address2->address_id, 'sport_id' => $sportsByName['Floorball']->sport_id],
            ['name' => 'Žilina Smash', 'phone' => '0901000003', 'email' => 'zilina@example.com', 'webpage' => 'https://zilina.local', 'address_id' => $address3->address_id, 'sport_id' => $sportsByName['Basketball']->sport_id],
            ['name' => 'Prešov Power', 'phone' => '0901000004', 'email' => 'presov@example.com', 'webpage' => 'https://presov.local', 'address_id' => $address4->address_id, 'sport_id' => $sportsByName['Volleyball']->sport_id],
            ['name' => 'Lokca Legends', 'phone' => '0901000005', 'email' => 'lokca@example.com', 'webpage' => 'https://lokca.local', 'address_id' => $address5->address_id, 'sport_id' => $sportsByName['Handball']->sport_id],
            ['name' => 'Hockey Heroes', 'phone' => '0901000006', 'email' => 'hockey@example.com', 'webpage' => 'https://hockey.local', 'address_id' => $address1->address_id, 'sport_id' => $sportsByName['Hockey']->sport_id],
        ];

        foreach ($extraClubs as $clubData) {
            Club::firstOrCreate(['name' => $clubData['name']], $clubData);
        }

        // -----------------------
        // Member club memberships
        // -----------------------
        $member->clubs()->attach($club1->club_id, [
            'joined_at' => now()->subMonths(6),
            'role' => MemberClubRole::PLAYER->value
        ]);
        $coachMember->clubs()->attach($club2->club_id, [
            'joined_at' => now()->subYear(),
            'role' => MemberClubRole::COACH->value
        ]);
        $adminMember->clubs()->attach($club1->club_id, [
            'joined_at' => now()->subYears(2),
            'role' => MemberClubRole::COACH->value
        ]);
        $memberPeter->clubs()->attach($club1->club_id, [
            'joined_at' => now()->subMonths(4),
            'role' => MemberClubRole::PLAYER->value
        ]);
        $memberLucia->clubs()->attach($club2->club_id, [
            'joined_at' => now()->subMonths(3),
            'role' => MemberClubRole::PLAYER->value
        ]);
        $memberMartin->clubs()->attach($club1->club_id, [
            'joined_at' => now()->subMonths(8),
            'role' => MemberClubRole::PLAYER->value
        ]);
        $memberKarol->clubs()->attach($club2->club_id, [
            'joined_at' => now()->subMonths(5),
            'role' => MemberClubRole::PLAYER->value
        ]);
        $coachMember2->clubs()->attach($club1->club_id, [
            'joined_at' => now()->subYears(3),
            'role' => MemberClubRole::COACH->value
        ]);
        $memberPeter->clubs()->attach($club3->club_id, [
            'joined_at' => now()->subMonths(2),
            'role' => MemberClubRole::PLAYER->value
        ]);

        $mc1 = MemberClub::where('member_id', $member->member_id)
            ->where('club_id', $club1->club_id)
            ->first();

        $mcCoach = MemberClub::where('member_id', $coachMember->member_id)
            ->where('club_id', $club2->club_id)
            ->first();

        $mcAdmin = MemberClub::where('member_id', $adminMember->member_id)
            ->where('club_id', $club1->club_id)
            ->first();

        $mcPeter1 = MemberClub::where('member_id', $memberPeter->member_id)
            ->where('club_id', $club1->club_id)
            ->first();

        $mcLucia = MemberClub::where('member_id', $memberLucia->member_id)
            ->where('club_id', $club2->club_id)
            ->first();

        $mcMartin = MemberClub::where('member_id', $memberMartin->member_id)
            ->where('club_id', $club1->club_id)
            ->first();

        $mcKarol = MemberClub::where('member_id', $memberKarol->member_id)
            ->where('club_id', $club2->club_id)
            ->first();

        $mcCoach2 = MemberClub::where('member_id', $coachMember2->member_id)
            ->where('club_id', $club1->club_id)
            ->first();

        // -----------------------
        // Events - finished (past)
        // -----------------------
        $eventFinished1 = Event::create([
            'title' => 'League Match - Round 1',
            'description' => 'First round of the league season.',
            'event_type_id' => $eventTypeMap['Football-Match']->event_type_id,
            'status' => EventStatus::SCHEDULED->value,
            'start_date' => now()->subDays(30)->setHour(15),
            'end_date' => now()->subDays(30)->setHour(17),
            'sport_field_id' => $sportFieldsBySport['Football'],
        ]);

        $eventFinished2 = Event::create([
            'title' => 'Spring Tournament 2025',
            'description' => 'Annual spring football tournament.',
            'event_type_id' => $eventTypeMap['Football-Tournament']->event_type_id,
            'status' => EventStatus::SCHEDULED->value,
            'start_date' => now()->subDays(14)->setHour(10),
            'end_date' => now()->subDays(14)->setHour(18),
            'sport_field_id' => $sportFieldsBySport['Football'],
        ]);

        EventClub::create(['event_id' => $eventFinished2->event_id, 'club_id' => $club1->club_id]);
        EventClub::create(['event_id' => $eventFinished2->event_id, 'club_id' => $club2->club_id]);

        $springMatch1 = Event::create([
            'title' => 'Spring Tournament 2025 - Match 1',
            'description' => 'Quarterfinal match 1',
            'event_type_id' => $eventTypeMap['Football-Match']->event_type_id,
            'status' => EventStatus::SCHEDULED->value,
            'start_date' => now()->subDays(14)->setHour(12),
            'end_date' => now()->subDays(14)->setHour(14),
            'sport_field_id' => $sportFieldsBySport['Football'],
            'parent_event_id' => $eventFinished2->event_id,
        ]);

        $springMatch2 = Event::create([
            'title' => 'Spring Tournament 2025 - Match 2',
            'description' => 'Quarterfinal match 2',
            'event_type_id' => $eventTypeMap['Football-Match']->event_type_id,
            'status' => EventStatus::SCHEDULED->value,
            'start_date' => now()->subDays(14)->setHour(14),
            'end_date' => now()->subDays(14)->setHour(16),
            'sport_field_id' => $sportFieldsBySport['Football'],
            'parent_event_id' => $eventFinished2->event_id,
        ]);

        $springMatch3 = Event::create([
            'title' => 'Spring Tournament 2025 - Match 3',
            'description' => 'Semifinal match',
            'event_type_id' => $eventTypeMap['Football-Match']->event_type_id,
            'status' => EventStatus::SCHEDULED->value,
            'start_date' => now()->subDays(14)->setHour(16),
            'end_date' => now()->subDays(14)->setHour(18),
            'sport_field_id' => $sportFieldsBySport['Football'],
            'parent_event_id' => $eventFinished2->event_id,
        ]);

        $springMatch4 = Event::create([
            'title' => 'Spring Tournament 2025 - Match 4',
            'description' => 'Final match',
            'event_type_id' => $eventTypeMap['Football-Match']->event_type_id,
            'status' => EventStatus::SCHEDULED->value,
            'start_date' => now()->subDays(14)->setHour(10),
            'end_date' => now()->subDays(14)->setHour(12),
            'sport_field_id' => $sportFieldsBySport['Football'],
            'parent_event_id' => $eventFinished2->event_id,
        ]);

        foreach ([$springMatch1, $springMatch2, $springMatch3, $springMatch4] as $ev) {
            EventClub::create(['event_id' => $ev->event_id, 'club_id' => $club1->club_id]);
            EventClub::create(['event_id' => $ev->event_id, 'club_id' => $club2->club_id]);
        }

        foreach ([$springMatch1, $springMatch2, $springMatch3, $springMatch4] as $match) {
            MemberEvent::create(['member_club_id' => $mc1->member_club_id, 'event_id' => $match->event_id]);
            MemberEvent::create(['member_club_id' => $mcPeter1->member_club_id, 'event_id' => $match->event_id]);
            MemberEvent::create(['member_club_id' => $mcMartin->member_club_id, 'event_id' => $match->event_id]);
            MemberEvent::create(['member_club_id' => $mcKarol->member_club_id, 'event_id' => $match->event_id]);
        }

        $eventFinished3 = Event::create([
            'title' => 'Pre-season Training Camp',
            'description' => 'Intensive pre-season training for all players.',
            'event_type_id' => $eventTypeMap['Football-Training']->event_type_id,
            'status' => EventStatus::SCHEDULED->value,
            'start_date' => now()->subDays(60)->setHour(9),
            'end_date' => now()->subDays(60)->setHour(12),
            'sport_field_id' => $sportFieldsBySport['Football'],
        ]);

        // -----------------------
        // Events - ongoing
        // -----------------------
        $eventOngoing = Event::create([
            'title' => 'Weekly Training Session',
            'description' => 'Regular weekly training.',
            'event_type_id' => $eventTypeMap['Football-Training']->event_type_id,
            'status' => EventStatus::SCHEDULED->value,
            'start_date' => now()->subHours(1),
            'end_date' => now()->addHours(2),
            'sport_field_id' => $sportFieldsBySport['Football'],
        ]);

        // -----------------------
        // Events - scheduled (future)
        // -----------------------
        $eventScheduled1 = Event::create([
            'title' => 'Morning Training',
            'description' => 'Regular morning training session.',
            'event_type_id' => $eventTypeMap['Football-Training']->event_type_id,
            'status' => EventStatus::SCHEDULED->value,
            'start_date' => now()->addDays(1)->setHour(9),
            'end_date' => now()->addDays(1)->setHour(11),
            'sport_field_id' => $sportFieldsBySport['Football'],
        ]);

        $eventScheduled2 = Event::create([
            'title' => 'Friendly Match',
            'description' => 'Friendly match against FC Dynamo.',
            'event_type_id' => $eventTypeMap['Football-Match']->event_type_id,
            'status' => EventStatus::SCHEDULED->value,
            'start_date' => now()->addDays(3)->setHour(15),
            'end_date' => now()->addDays(3)->setHour(17),
            'sport_field_id' => $sportFieldsBySport['Football'],
        ]);

        $eventScheduled3 = Event::create([
            'title' => 'Summer Cup Preparation',
            'description' => 'Preparation for summer cup tournament.',
            'event_type_id' => $eventTypeMap['Football-Training']->event_type_id,
            'status' => EventStatus::SCHEDULED->value,
            'start_date' => now()->addDays(7)->setHour(10),
            'end_date' => now()->addDays(7)->setHour(13),
            'sport_field_id' => $sportFieldsBySport['Football'],
        ]);

        // -----------------------
        // Events - canceled
        // -----------------------
        $eventCanceled = Event::create([
            'title' => 'Canceled Evening Match',
            'description' => 'This event was canceled.',
            'event_type_id' => $eventTypeMap['Football-Match']->event_type_id,
            'status' => EventStatus::SCHEDULED->value,
            'start_date' => now()->addDays(6)->setHour(18),
            'end_date' => now()->addDays(6)->setHour(20),
            'sport_field_id' => $sportFieldsBySport['Football'],
        ]);

        // -----------------------
        // Event club associations
        // -----------------------
        foreach ([$eventFinished1, $eventFinished3, $eventOngoing, $eventScheduled1, $eventScheduled3] as $ev) {
            EventClub::create([
                'event_id' => $ev->event_id,
                'club_id' => $club1->club_id
            ]);
        }

        foreach ([$eventScheduled2, $eventCanceled] as $ev) {
            EventClub::create([
                'event_id' => $ev->event_id,
                'club_id' => $club1->club_id
            ]);
            EventClub::create([
                'event_id' => $ev->event_id,
                'club_id' => $club2->club_id
            ]);
        }

        // -----------------------
        // Add 4x more events (non-tournament, round-robin across sports and types)
        // -----------------------
        $allClubIds = Club::pluck('club_id')->all();
        $clubsBySport = Club::all()->groupBy('sport_id')->map(fn($g) => $g->pluck('club_id')->all())->toArray();
        $allFieldIds = SportField::pluck('sport_field_id')->all();
        $eventTypes = ['Match', 'Training', 'Tournament'];

        for ($i = 0; $i < 36; $i++) {
            $sport = $sports[$i % $sports->count()];
            $type = $eventTypes[$i % count($eventTypes)];
            $eventTypeKey = "{$sport->name}-{$type}";
            $eventTypeId = $eventTypeMap[$eventTypeKey]->event_type_id ?? null;

            if (!$eventTypeId) {
                continue;
            }

            $event = Event::firstOrCreate([
                'title' => "{$sport->name} {$type} Event " . ($i + 1),
            ], [
                'description' => "Automatically generated {$type} event for {$sport->name}",
                'event_type_id' => $eventTypeId,
                'status' => EventStatus::SCHEDULED->value,
                'start_date' => now()->addDays(10 + $i),
                'end_date' => now()->addDays(10 + $i)->addHours(2),
                'sport_field_id' => $sportFieldsBySport[$sport->name] ?? $allFieldIds[$i % count($allFieldIds)],
            ]);

            $clubCandidates = $clubsBySport[$sport->sport_id] ?? $allClubIds;
            $clubId = $clubCandidates[$i % count($clubCandidates)];

            EventClub::firstOrCreate([
                'event_id' => $event->event_id,
                'club_id' => $clubId,
            ]);
        }

        // -----------------------
        // Add 10 tournaments and 5 matches each (parent_event_id)
        // -----------------------
        $sportsList = $sports->values();

        for ($t = 1; $t <= 10; $t++) {
            $sport = $sportsList[$t % count($sportsList)];
            $tournamentType = $eventTypeMap["{$sport->name}-Tournament"];
            $matchType = $eventTypeMap["{$sport->name}-Match"];

            $tournamentStart = now()->addDays(100 + $t * 3)->setHour(9)->setMinute(0)->setSecond(0);
            $tournament = Event::create([
                'title' => "Seed Tournament {$t}",
                'description' => "Tournament event {$t}",
                'event_type_id' => $tournamentType->event_type_id,
                'status' => EventStatus::SCHEDULED->value,
                'start_date' => $tournamentStart,
                'end_date' => $tournamentStart->copy()->addHours(8),
                'sport_field_id' => $sportFieldsBySport[$sport->name] ?? $allFieldIds[$t % count($allFieldIds)],
            ]);

            $clubCandidates = $clubsBySport[$sport->sport_id] ?? $allClubIds;
            $parentClubId = $clubCandidates[($t * 2) % count($clubCandidates)];
            EventClub::firstOrCreate([
                'event_id' => $tournament->event_id,
                'club_id' => $parentClubId,
            ]);

            for ($m = 1; $m <= 5; $m++) {
                $matchStart = $tournamentStart->copy()->addHours($m * 1); // within parent interval
                $match = Event::create([
                    'title' => "Tournament {$t} - Match {$m}",
                    'description' => "Group match {$m} for tournament {$t}",
                    'event_type_id' => $matchType->event_type_id,
                    'status' => EventStatus::SCHEDULED->value,
                    'start_date' => $matchStart,
                    'end_date' => $matchStart->copy()->addHours(1),
                    'sport_field_id' => $sportFieldsBySport[$sport->name] ?? $allFieldIds[($t + $m) % count($allFieldIds)],
                    'parent_event_id' => $tournament->event_id,
                ]);

                EventClub::firstOrCreate([
                    'event_id' => $match->event_id,
                    'club_id' => $parentClubId,
                ]);
            }
        }

        // -----------------------
        // Add 3x more reservations
        // -----------------------
        $allReservationOwners = MemberClub::with('club')->get();

        for ($r = 1; $r <= 10; $r++) {
            $owner = $allReservationOwners->random();
            $clubSportId = $owner->club->sport_id;
            $validFieldIds = SportField::whereHas('sports', function ($q) use ($clubSportId) {
                $q->where('sports.sport_id', $clubSportId);
            })->pluck('sport_field_id')->all();

            if (empty($validFieldIds)) {
                continue;
            }

            $fieldId = $validFieldIds[$r % count($validFieldIds)];
            Reservation::firstOrCreate([
                'title' => "Generated Reservation {$r}",
            ], [
                'sport_field_id' => $fieldId,
                'created_by_member_club_id' => $owner->member_club_id,
                'description' => "Auto reservation {$r}",
                'status' => ReservationStatus::APPROVED->value,
                'start_date' => now()->addDays(365 + $r),
                'end_date' => now()->addDays(365 + $r)->addHours(2),
            ]);
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
            MemberEvent::create([
                'member_club_id' => $mc->member_club_id,
                'event_id' => $ev->event_id
            ]);
        }

        // -----------------------
        // Event member results (finished events only)
        // -----------------------
        $memberResults = [
            [$eventFinished1->event_id, $mc1->member_club_id, 1, '85', ResultType::POINTS, 'Excellent performance.'],
            [$eventFinished1->event_id, $mcPeter1->member_club_id, 2, '78', ResultType::POINTS, 'Good effort.'],
            [$eventFinished1->event_id, $mcMartin->member_club_id, 3, '72', ResultType::POINTS, 'Needs improvement.'],
            [$eventFinished1->event_id, $mcAdmin->member_club_id, 1, '80', ResultType::POINTS, 'Solid coaching.'],

            [$eventFinished2->event_id, $mc1->member_club_id, 1, '92', ResultType::POINTS, 'Man of the match.'],
            [$eventFinished2->event_id, $mcPeter1->member_club_id, 2, '88', ResultType::POINTS, 'Strong performance.'],
            [$eventFinished2->event_id, $mcLucia->member_club_id, 3, '75', ResultType::POINTS, 'Good game.'],
            [$eventFinished2->event_id, $mcKarol->member_club_id, 4, '70', ResultType::POINTS, 'Average performance.'],

            [$springMatch1->event_id, $mc1->member_club_id, 1, '90', ResultType::POINTS, 'Strong offensive play.'],
            [$springMatch1->event_id, $mcPeter1->member_club_id, 2, '84', ResultType::POINTS, 'Defensive pressure.'],
            [$springMatch2->event_id, $mc1->member_club_id, 1, '91', ResultType::POINTS, 'Excellent passing.'],
            [$springMatch2->event_id, $mcPeter1->member_club_id, 2, '83', ResultType::POINTS, 'Close match.'],
            [$springMatch3->event_id, $mc1->member_club_id, 1, '93', ResultType::POINTS, 'Tactical dominance.'],
            [$springMatch3->event_id, $mcMartin->member_club_id, 2, '80', ResultType::POINTS, 'Great energy.'],
            [$springMatch4->event_id, $mcPeter1->member_club_id, 1, '89', ResultType::POINTS, 'Strong leadership.'],
            [$springMatch4->event_id, $mcMartin->member_club_id, 2, '82', ResultType::POINTS, 'Solid team work.'],

            [$eventFinished3->event_id, $mc1->member_club_id, 1, '88', ResultType::POINTS, 'Best in camp.'],
            [$eventFinished3->event_id, $mcMartin->member_club_id, 2, '82', ResultType::POINTS, 'Good training.'],
            [$eventFinished3->event_id, $mcAdmin->member_club_id, 2, '79', ResultType::POINTS, 'Consistent.'],
            [$eventFinished3->event_id, $mcCoach2->member_club_id, 1, '85', ResultType::POINTS, 'Great coaching.'],
        ];

        foreach ($memberResults as [$eventId, $mcId, $ranking, $value, $resultType, $note]) {
            EventMemberResult::create([
                'event_id' => $eventId,
                'member_club_id' => $mcId,
                'ranking' => $ranking,
                'value' => $value,
                'result_type' => $resultType->value,
                'note' => $note,
            ]);
        }

        // -----------------------
        // Event club results (finished events only)
        // -----------------------
        EventClubResult::create([
            'event_id' => $eventFinished1->event_id,
            'club_id' => $club1->club_id,
            'ranking' => 1,
            'value' => '2',
            'result_type' => ResultType::SCORE->value,
            'note' => 'Home win.',
        ]);

        EventClubResult::create([
            'event_id' => $eventFinished2->event_id,
            'club_id' => $club1->club_id,
            'ranking' => 1,
            'value' => '3',
            'result_type' => ResultType::SCORE->value,
            'note' => 'Tournament winners.',
        ]);

        EventClubResult::create([
            'event_id' => $eventFinished2->event_id,
            'club_id' => $club2->club_id,
            'ranking' => 2,
            'value' => '1',
            'result_type' => ResultType::SCORE->value,
            'note' => 'Runners up.',
        ]);

        // Spring Tournament match results (club level)
        EventClubResult::create([
            'event_id' => $springMatch1->event_id,
            'club_id' => $club1->club_id,
            'ranking' => 1,
            'value' => '2',
            'result_type' => ResultType::SCORE->value,
            'note' => 'Home team win.',
        ]);
        EventClubResult::create([
            'event_id' => $springMatch1->event_id,
            'club_id' => $club2->club_id,
            'ranking' => 2,
            'value' => '1',
            'result_type' => ResultType::SCORE->value,
            'note' => 'Away team loss.',
        ]);

        EventClubResult::create([
            'event_id' => $springMatch2->event_id,
            'club_id' => $club2->club_id,
            'ranking' => 1,
            'value' => '2',
            'result_type' => ResultType::SCORE->value,
            'note' => 'Away team victory.',
        ]);
        EventClubResult::create([
            'event_id' => $springMatch2->event_id,
            'club_id' => $club1->club_id,
            'ranking' => 2,
            'value' => '1',
            'result_type' => ResultType::SCORE->value,
            'note' => 'Home team defeat.',
        ]);

        EventClubResult::create([
            'event_id' => $springMatch3->event_id,
            'club_id' => $club1->club_id,
            'ranking' => 1,
            'value' => '3',
            'result_type' => ResultType::SCORE->value,
            'note' => 'Dominant performance.',
        ]);
        EventClubResult::create([
            'event_id' => $springMatch3->event_id,
            'club_id' => $club2->club_id,
            'ranking' => 2,
            'value' => '0',
            'result_type' => ResultType::SCORE->value,
            'note' => 'Shutout loss.',
        ]);

        EventClubResult::create([
            'event_id' => $springMatch4->event_id,
            'club_id' => $club2->club_id,
            'ranking' => 1,
            'value' => '2',
            'result_type' => ResultType::SCORE->value,
            'note' => 'Close win.',
        ]);
        EventClubResult::create([
            'event_id' => $springMatch4->event_id,
            'club_id' => $club1->club_id,
            'ranking' => 2,
            'value' => '1',
            'result_type' => ResultType::SCORE->value,
            'note' => 'Narrow loss.',
        ]);

        // Update final statuses after all events are created
        $eventFinished1->update(['status' => EventStatus::FINISHED->value]);
        $eventFinished2->update(['status' => EventStatus::FINISHED->value]);
        $springMatch1->update(['status' => EventStatus::FINISHED->value]);
        $springMatch2->update(['status' => EventStatus::FINISHED->value]);
        $springMatch3->update(['status' => EventStatus::FINISHED->value]);
        $springMatch4->update(['status' => EventStatus::FINISHED->value]);
        $eventFinished3->update(['status' => EventStatus::FINISHED->value]);

        $eventOngoing->update(['status' => EventStatus::ONGOING->value]);
        $eventCanceled->update(['status' => EventStatus::CANCELED->value]);

        // -----------------------
        // Reservations
        // -----------------------
        $res1 = Reservation::create([
            'sport_field_id' => $field1->sport_field_id,
            'created_by_member_club_id' => $mc1->member_club_id,
            'title' => 'Training Session - AC Sparta',
            'description' => 'Regular training.',
            'status' => ReservationStatus::APPROVED->value,
            'start_date' => now()->addDays(2),
            'end_date' => now()->addDays(2)->addHours(2),
        ]);

        $res2 = Reservation::create([
            'sport_field_id' => $field2->sport_field_id,
            'created_by_member_club_id' => $mcCoach->member_club_id,
            'title' => 'Match Preparation - FC Dynamo',
            'description' => 'Match prep.',
            'status' => ReservationStatus::APPROVED->value,
            'start_date' => now()->addDays(5),
            'end_date' => now()->addDays(5)->addHours(3),
        ]);

        $res3 = Reservation::create([
            'sport_field_id' => $field1->sport_field_id,
            'created_by_member_club_id' => $mcAdmin->member_club_id,
            'title' => 'Youth Tournament Prep',
            'description' => 'Youth prep.',
            'status' => ReservationStatus::CONVERTED->value,
            'start_date' => now()->addDays(7)->setHour(14),
            'end_date' => now()->addDays(7)->setHour(18),
        ]);

        $res4 = Reservation::create([
            'sport_field_id' => $field2->sport_field_id,
            'created_by_member_club_id' => $mcKarol->member_club_id,
            'title' => 'Evening Practice',
            'description' => 'Evening training.',
            'status' => ReservationStatus::CANCELED->value,
            'start_date' => now()->addDays(4),
            'end_date' => now()->addDays(4)->addHours(2),
        ]);

        $res5 = Reservation::create([
            'sport_field_id' => $field1->sport_field_id,
            'created_by_member_club_id' => $mcCoach2->member_club_id,
            'title' => 'Weekend Drill',
            'description' => 'Weekend drill session.',
            'status' => ReservationStatus::APPROVED->value,
            'start_date' => now()->addDays(9),
            'end_date' => now()->addDays(9)->addHours(3),
        ]);

        // -----------------------
        // Coach Evaluations
        // -----------------------
        $evaluations = [
            [$mcCoach->member_id, $member->member_id, 5, 'Excellent coach, very professional.'],
            [$mcCoach->member_id, $adminMember->member_id, 4, 'Good coaching, needs to improve communication.'],
            [$mcCoach->member_id, $memberLucia->member_id, 5, 'Great motivator.'],
            [$mcAdmin->member_id, $member->member_id, 4, 'Solid coaching skills.'],
            [$mcAdmin->member_id, $memberPeter->member_id, 3, 'Average, needs more energy.'],
            [$mcCoach2->member_id, $member->member_id, 5, 'Best coach I have had.'],
            [$mcCoach2->member_id, $memberMartin->member_id, 4, 'Very knowledgeable.'],
        ];

        foreach ($evaluations as [$coachId, $evaluatedById, $rating, $comment]) {
            CoachEvaluation::create([
                'coach_member_id' => $coachId,
                'evaluated_by_member_id' => $evaluatedById,
                'rating' => $rating,
                'comment' => $comment
            ]);
        }

        // Ensure at least 20 coach evaluations (prefer 30 for margin)
        $allCoachMemberIds = MemberClub::where('role', MemberClubRole::COACH->value)
            ->pluck('member_id')->unique()->all();
        $allMemberIds = Member::pluck('member_id')->all();
        $currentCount = CoachEvaluation::count();
        $target = max(20, 30); // keep safe above 20
        $required = max(0, $target - $currentCount);

        for ($i = 0; $i < $required; $i++) {
            if (count($allCoachMemberIds) === 0 || count($allMemberIds) === 0) {
                break;
            }

            $coachId = $allCoachMemberIds[$i % count($allCoachMemberIds)];
            $evaluatedById = $allMemberIds[($i + 1) % count($allMemberIds)];

            // do not allow coach to evaluate themselves
            if ($coachId === $evaluatedById) {
                $evaluatedById = $allMemberIds[($i + 2) % count($allMemberIds)];
            }

            CoachEvaluation::firstOrCreate([
                'coach_member_id' => $coachId,
                'evaluated_by_member_id' => $evaluatedById,
            ], [
                'rating' => rand(2, 5),
                'comment' => "Auto-generated evaluation #{$i}",
            ]);
        }

        // -----------------------
        // Expand seeding 10× (no File records)
        // -----------------------
        $multiplier = 10;

        // Additional field types
        for ($i = 1; $i < $multiplier; $i++) {
            FieldType::firstOrCreate(['name' => "indoor-{$i}"]);
            FieldType::firstOrCreate(['name' => "outdoor-{$i}"]);
        }

        // Additional file categories
        $categories = ['certificate', 'contract', 'photo', 'document', 'report'];

        // Additional sports:
        // Keep only the 6 configured sports (Football, Basketball, Hockey, Floorball, Handball, Volleyball).

        // Additional addresses
        $addresses = [
            ['city' => 'Bratislava', 'street' => 'Hlavná'],
            ['city' => 'Košice', 'street' => 'Športová'],
            ['city' => 'Žilina', 'street' => 'Námestie Slobody'],
            ['city' => 'Prešov', 'street' => 'Hlavná'],
            ['city' => 'Lokca', 'street' => 'Lokca']
        ];
        for ($i = 2; $i <= $multiplier; $i++) {
            foreach ($addresses as $idx => $addressTemplate) {
                Address::firstOrCreate([
                    'country' => 'Slovakia',
                    'city' => $addressTemplate['city'],
                    'street' => "{$addressTemplate['street']} {$i}",
                ], [
                    'zip_code' => str_pad(80000 + $i * 100 + $idx, 5, '0', STR_PAD_LEFT),
                ]);
            }
        }

        // Additional users and members
        for ($i = 1; $i < $multiplier; $i++) {
            $newUsers = User::factory(8)->create(['is_admin' => false]);
            foreach ($newUsers as $newUser) {
                Member::create([
                    'user_id' => $newUser->user_id,
                    'first_name' => "Seed{$i}",
                    'last_name' => 'Generated',
                    'phone' => '090' . str_pad((int)($newUser->user_id % 1000000), 7, '0', STR_PAD_LEFT),
                    'date_of_birth' => now()->subYears(20 + $i)->format('Y-m-d'),
                ]);
            }
        }

        // Additional sport fields
        $fieldTypeIds = FieldType::pluck('field_type_id')->all();
        $addressIds = Address::pluck('address_id')->all();
        $sportIds = Sport::pluck('sport_id')->all();

        for ($i = 1; $i < $multiplier; $i++) {
            $field = SportField::firstOrCreate([
                'name' => "Seed SportField {$i}",
            ], [
                'field_type_id' => $fieldTypeIds[$i % count($fieldTypeIds)],
                'address_id' => $addressIds[$i % count($addressIds)],
            ]);
            $field->sports()->syncWithoutDetaching([$sportIds[$i % count($sportIds)]]);
        }

        // Additional event types:
        // Keep only the 3 configured types (Match, Tournament, Training) for each sport.

        // Additional clubs
        for ($i = 1; $i < $multiplier; $i++) {
            $idx = ($i - 1) % count($addressIds);
            $sportId = $sportIds[$i % count($sportIds)];
            Club::firstOrCreate([
                'name' => "Seed Club {$i}",
            ], [
                'phone' => '090' . str_pad($i, 7, '0', STR_PAD_LEFT),
                'email' => "seedclub{$i}@example.com",
                'webpage' => "https://seedclub{$i}.local",
                'address_id' => $addressIds[$idx],
                'sport_id' => $sportId,
            ]);
        }

        // Member-Club linkage expansion
        $allMembers = Member::pluck('member_id');
        $allClubs = Club::pluck('club_id');
        foreach ($allMembers as $memberId) {
            $clubId = $allClubs[$memberId % $allClubs->count()];
            MemberClub::firstOrCreate([
                'member_id' => $memberId,
                'club_id' => $clubId,
            ], [
                'joined_at' => now()->subMonths(1),
                'role' => MemberClubRole::PLAYER->value,
            ]);
        }

        // minimal event expansion (optional)
        $eventTypeIds = EventType::pluck('event_type_id')->all();

        for ($i = 1; $i < $multiplier; $i++) {
            $eventTypeId = $eventTypeIds[$i % count($eventTypeIds)];
            $eventType = EventType::find($eventTypeId);

            if (!$eventType) {
                continue;
            }

            $sportFieldId = SportField::whereHas('sports', fn($q) =>
                $q->where('sports.sport_id', $eventType->sport_id)
            )->inRandomOrder()->value('sport_field_id');

            if (!$sportFieldId) {
                continue;
            }

            Event::firstOrCreate([
                'title' => "Seed Event {$i}",
            ], [
                'description' => "Seed event {$i}",
                'event_type_id' => $eventTypeId,
                'status' => EventStatus::SCHEDULED->value,
                'start_date' => now()->addDays(30 + $i * 2),
                'end_date' => now()->addDays(30 + $i * 2)->addHours(2),
                'sport_field_id' => $sportFieldId,
            ]);
        }
    }
}