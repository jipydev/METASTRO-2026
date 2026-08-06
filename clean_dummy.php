<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$names = ['Medis'];
foreach($names as $n) {
    $d = \App\Models\Divisi::where('nama_divisi', $n)->first();
    if($d) {
        \App\Models\User::where('divisi_id', $d->id)->delete();
        $d->delete();
        echo "Deleted divisi " . $n . " and its users\n";
    }

    \App\Models\User::where('name', 'like', '%' . $n . '%')->delete();
}
echo "Done.\n";
