<?php

use App\Models\User;
use App\Domain\Organization\Models\Organization;
use Illuminate\Support\Facades\Hash;

$org = Organization::first();
$user = User::create([
    'name' => 'Budi Santoso',
    'email' => 'budi@example.com',
    'password' => Hash::make('password'),
    'organization_id' => $org->id,
    'locale' => 'id',
]);

$user->assignRole('Learner');

echo "User berhasil dibuat:\n";
echo "Email: budi@example.com\n";
echo "Password: password\n";
echo "Role: Learner\n";
