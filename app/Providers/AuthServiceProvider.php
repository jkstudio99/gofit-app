<?php

namespace App\Providers;

// use Illuminate\Support\Facades\Gate;
use App\Models\Activity;
use App\Models\ActivityGoal;
use App\Policies\ActivityPolicy;
use App\Policies\ActivityGoalPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        // 'App\Models\Model' => 'App\Policies\ModelPolicy',
        Activity::class => ActivityPolicy::class,
        ActivityGoal::class => ActivityGoalPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();

        //
    }
}
