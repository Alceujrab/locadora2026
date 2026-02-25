<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$branch = \App\Models\Branch::firstOrCreate(['name' => 'Sede Principal'], ['phone' => '11999999999', 'active' => true, 'matrix' => true, 'cnpj' => '00.000.000/0001-00']);

$user = \App\Models\User::firstOrNew(['email' => 'alceujr.ab@gmail.com']);
$user->name = 'Alceu Admin';
$user->password = bcrypt('256010');
$user->branch_id = $branch->id;
$user->save();

$user->assignRole('super-admin');

echo "Admin user created successfully.\n";
