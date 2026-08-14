<?php

use Illuminate\Database\Migrations\Migration;
use App\Models\User;
use Spatie\Permission\Models\Role;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Ensure the core roles exist (they should be seeded, but guard just in case)
        $adminRole   = Role::firstOrCreate(['name' => 'admin',   'guard_name' => 'web']);
        $panitiaRole = Role::firstOrCreate(['name' => 'panitia', 'guard_name' => 'web']);
        $pesertaRole = Role::firstOrCreate(['name' => 'peserta', 'guard_name' => 'web']);

        // Update users in chunks to avoid memory overload
        User::with('divisi')->chunk(200, function ($users) use ($adminRole, $panitiaRole, $pesertaRole) {
            foreach ($users as $user) {
                // Skip users who are already explicitly assigned the admin role
                // (e.g., the AdminSeeder user) — do not downgrade them
                if ($user->hasRole('admin')) {
                    continue;
                }

                $divisionName = $user->divisi ? strtolower($user->divisi->nama_divisi) : null;

                if (empty($divisionName)) {
                    // No division → peserta
                    $user->syncRoles([$pesertaRole]);
                } elseif ($divisionName === 'chiper') {
                    // CHIPER division → admin
                    $user->syncRoles([$adminRole]);
                } else {
                    // All other divisions → panitia
                    $user->syncRoles([$panitiaRole]);
                }
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * The role assignment is destructive; reversal must be handled manually.
     */
    public function down(): void
    {
        // No automatic rollback – manual intervention required if needed.
    }
};
?>
