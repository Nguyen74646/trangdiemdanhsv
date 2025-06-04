<?php

namespace App\Providers;

use App\Models\User;
use App\Models\Course;
use Illuminate\Support\Facades\Gate;
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
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();

        // Admin Gates
        Gate::define('manage-users', fn(User $user) => $user->role === 'admin');
        Gate::define('manage-courses', fn(User $user) => $user->role === 'admin');

        // Lecturer Gates
        Gate::define('view-lecturer-courses', fn(User $user) => $user->role === 'giangvien');
        Gate::define('mark-attendance', function (User $user, Course $course) {
            return $user->role === 'giangvien' && $course->lecturer_id == $user->getKey();
        });
        Gate::define('view-course-attendance-history', function (User $user, Course $course) {
            return ($user->role === 'giangvien' && $course->lecturer_id == $user->getKey()) || $user->role === 'admin';
        });


        // Student Gates
        Gate::define('view-student-attendance', fn(User $user) => $user->role === 'sinhvien');

        // General report gate (can be refined)
        Gate::define('view-attendance-reports', fn(User $user) => in_array($user->role, ['admin', 'giangvien']));
    }
}