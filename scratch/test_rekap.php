<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$users = DB::table('user')->where('role', 'admin_outsourcing')->get();
foreach ($users as $u) {
    $arr = (array)$u;
    echo "ID: {$arr['id_user']}, Role: {$arr['role']}, Outsourcing ID: {$arr['outsourcing_id']}\n";
}
