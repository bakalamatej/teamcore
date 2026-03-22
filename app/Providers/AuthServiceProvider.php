<?php

namespace App\Providers;

// use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use App\Models\{
    Club, Event, Member, Reservation, File, MemberClub, CoachEvaluation,
    EventType, Sport, Address, SportField, FieldType,
    EventStatistic, ClubStatistic, MemberStatistic, User
};
use App\Policies\{
    ClubPolicy, MemberPolicy, ReservationPolicy, FilePolicy, EventPolicy,
    MemberClubPolicy, CoachEvaluationPolicy,
    EventTypePolicy, SportPolicy, AddressPolicy, SportFieldPolicy, FieldTypePolicy,
    EventStatisticPolicy, ClubStatisticPolicy, MemberStatisticPolicy, UserPolicy
};

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        Club::class => ClubPolicy::class,
        Event::class => EventPolicy::class,
        Member::class => MemberPolicy::class,
        Reservation::class => ReservationPolicy::class,
        File::class => FilePolicy::class,
        MemberClub::class => MemberClubPolicy::class,
        CoachEvaluation::class => CoachEvaluationPolicy::class,
        EventType::class => EventTypePolicy::class,
        Sport::class => SportPolicy::class,
        Address::class => AddressPolicy::class,
        SportField::class => SportFieldPolicy::class,
        FieldType::class => FieldTypePolicy::class,
        EventStatistic::class => EventStatisticPolicy::class,
        ClubStatistic::class => ClubStatisticPolicy::class,
        MemberStatistic::class => MemberStatisticPolicy::class,
        User::class => UserPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();

        // You can define Gates here for more complex authorization logic
        // Gate::define('update-post', function (User $user, Post $post) {
        //     return $user->id === $post->user_id;
        // });
    }
}
