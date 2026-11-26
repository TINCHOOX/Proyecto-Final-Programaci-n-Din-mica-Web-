<?php

use App\Models\TrafficReport;
use App\Models\User;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Schedule::command('model:prune', ['--model' => [TrafficReport::class]])->everyMinute()->withoutOverlapping();

Artisan::command('dinomaps:administrador {email}', function (): int {
    $email = mb_strtolower(trim($this->argument('email')));
    $user = User::where('email', $email)->first();

    if (!$user) {
        $this->error('Primero registrá la cuenta que va a administrar el catálogo.');

        return 1;
    }

    $user->is_admin = true;
    $user->save();
    $this->info('La cuenta ya puede gestionar lugares.');

    return 0;
})->purpose('Dar acceso de administrador a una cuenta existente');
