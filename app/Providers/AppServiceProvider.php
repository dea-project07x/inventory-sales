<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Paginator::useBootstrapFive();

        // Authorization di sisi server (dipakai middleware "can:" dan Gate::authorize()).
        Gate::define('manage-master', fn (User $u) => $u->isAdmin());
        Gate::define('manage-users', fn (User $u) => $u->isAdmin());
        Gate::define('manage-transactions', fn (User $u) => in_array($u->role, User::ROLES, true));
        Gate::define('cancel-transaction', fn (User $u) => $u->isAdmin());

        // Admin boleh melihat semua transaksi; Staff hanya transaksi miliknya.
        Gate::define('view-transaction', fn (User $u, $transaction) => $u->isAdmin() || $transaction->user_id === $u->id);
    }
}
