<?php

namespace App\Providers;

// use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        //
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();
        Gate::define('spAdmin', function ($user) {
            $userWorkspaces = $user->current_workspace;
            $roleid = DB::table('user_workspaces')->where('user_id', $user->id)->where('workspace_id', $userWorkspaces)->first()->roleid;
            if ($roleid == 1) {
                return true;
            }
            return false;
        });
        Gate::define('isAdmin', function ($user) {
            $userWorkspaces = $user->current_workspace;
            $roleid = DB::table('user_workspaces')->where('user_id', $user->id)->where('workspace_id', $userWorkspaces)->first()->roleid;
            if ($roleid == 2) {
                return true;
            }
            return false;
        });
        Gate::define('isSale', function ($user) {
            $userWorkspaces = $user->current_workspace;
            $roleid = DB::table('user_workspaces')->where('user_id', $user->id)->where('workspace_id', $userWorkspaces)->first()->roleid;
            if ($roleid == 4) {
                return true;
            }
            return false;
        });
    }
}
