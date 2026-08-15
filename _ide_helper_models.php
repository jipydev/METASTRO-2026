<?php

// @formatter:off
// phpcs:ignoreFile
/**
 * A helper file for your Eloquent Models
 * Copy the phpDocs from this file to the correct Model,
 * And remove them from this file, to prevent double declarations.
 *
 * @author Barry vd. Heuvel <barryvdh@gmail.com>
 */


namespace App\Models{
/**
 * @property-read \App\Models\User|null $koordinator
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\User> $users
 * @property-read int|null $users_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Divisi newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Divisi newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Divisi query()
 */
	class Divisi extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $nama_jabatan
 * @property string|null $deskripsi
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\User> $users
 * @property-read int|null $users_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Jabatan newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Jabatan newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Jabatan query()
 */
	class Jabatan extends \Eloquent {}
}

namespace App\Models{
/**
 * @property-read \App\Models\User|null $creator
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Notulensi> $notulensis
 * @property-read int|null $notulensis_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Presensi> $presensis
 * @property-read int|null $presensis_count
 * @method static \Database\Factories\KegiatanFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Kegiatan newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Kegiatan newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Kegiatan query()
 */
	class Kegiatan extends \Eloquent {}
}

namespace App\Models{
/**
 * @property-read \App\Models\Kegiatan|null $kegiatan
 * @property-read \App\Models\User|null $pembuat
 * @method static \Database\Factories\NotulensiFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Notulensi newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Notulensi newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Notulensi query()
 */
	class Notulensi extends \Eloquent {}
}

namespace App\Models{
/**
 * @property-read \App\Models\Kegiatan|null $kegiatan
 * @property-read \App\Models\Presensi|null $presensi
 * @property-read \App\Models\User|null $reviewerKoordinator
 * @property-read \App\Models\User|null $reviewerRanger
 * @property-read \App\Models\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PengajuanIzin newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PengajuanIzin newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PengajuanIzin query()
 */
	class PengajuanIzin extends \Eloquent {}
}

namespace App\Models{
/**
 * @property-read \App\Models\User|null $pembuat
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Pengumuman forUser(\App\Models\User $user)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Pengumuman newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Pengumuman newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Pengumuman published()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Pengumuman query()
 */
	class Pengumuman extends \Eloquent {}
}

namespace App\Models{
/**
 * @property-read \App\Models\PengajuanIzin|null $pengajuanIzin
 * @property-read \App\Models\User|null $scanner
 * @property-read \App\Models\User|null $user
 * @method static \Database\Factories\PresensiFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Presensi newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Presensi newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Presensi query()
 */
	class Presensi extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $nim
 * @property string $nama
 * @property string|null $email
 * @property \Carbon\CarbonImmutable|null $email_verified_at
 * @property string $password
 * @property string|null $nomor_hp
 * @property \Carbon\CarbonImmutable|null $tanggal_lahir
 * @property string|null $jenis_kelamin
 * @property string|null $foto
 * @property bool $status
 * @property int|null $divisi_id
 * @property int|null $jabatan_id
 * @property string|null $remember_token
 * @property \Carbon\CarbonImmutable|null $created_at
 * @property \Carbon\CarbonImmutable|null $updated_at
 * @property-read \App\Models\Divisi|null $divisi
 * @property-read \App\Models\Jabatan|null $jabatan
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Notulensi> $notulensi
 * @property-read int|null $notulensi_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Laravel\Passkeys\Passkey> $passkeys
 * @property-read int|null $passkeys_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\PengajuanIzin> $pengajuanIzin
 * @property-read int|null $pengajuan_izin_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Pengumuman> $pengumuman
 * @property-read int|null $pengumuman_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Permission\Models\Permission> $permissions
 * @property-read int|null $permissions_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Presensi> $presensis
 * @property-read int|null $presensis_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Permission\Models\Role> $roles
 * @property-read int|null $roles_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Permission\Models\Permission> $teams
 * @property-read int|null $teams_count
 * @method static \Database\Factories\UserFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User permission($permissions, bool $without = false)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User role($roles, ?string $guard = null, bool $without = false)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User team($teams, bool $without = false)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereDivisiId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmailVerifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereFoto($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereJabatanId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereJenisKelamin($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereNama($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereNim($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereNomorHp($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereTanggalLahir($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User withoutPermission($permissions)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User withoutRole($roles, ?string $guard = null)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User withoutTeam($teams)
 */
	class User extends \Eloquent implements \Laravel\Fortify\Contracts\PasskeyUser, \Laravel\Passkeys\Contracts\PasskeyUser {}
}

