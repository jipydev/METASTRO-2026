<?php

namespace Tests\Feature;

use App\Models\Divisi;
use App\Models\Pengumuman;
use App\Models\User;
use App\Notifications\ReminderNotification;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class PengumumanPublishTest extends TestCase
{
    use RefreshDatabase;

    public function test_published_status_sets_tanggal_publish_to_now(): void
    {
        Notification::fake();

        Carbon::setTestNow('2026-08-17 15:00:00');

        $user = User::factory()->admin()->create();
        $divisi = Divisi::create(['nama' => 'Informer']);
        User::factory()->panitia()->create(['divisi_id' => $divisi->id]);

        $this->actingAs($user)
            ->post(route('pengumuman.store'), [
                'judul' => 'Pengumuman Langsung',
                'isi' => 'Isi pengumuman langsung.',
                'status' => 'published',
            ])
            ->assertRedirect()
            ->assertSessionHas('success');

        $pengumuman = Pengumuman::query()->firstOrFail();

        $this->assertSame('published', $pengumuman->status);
        $this->assertTrue($pengumuman->tanggal_publish->equalTo(now()));
        Notification::assertSentTimes(ReminderNotification::class, 1);
    }

    public function test_draft_with_future_date_stays_scheduled(): void
    {
        Notification::fake();

        Carbon::setTestNow('2026-08-17 15:00:00');

        $user = User::factory()->admin()->create();
        $future = now()->addHours(2)->format('Y-m-d H:i:s');

        $this->actingAs($user)
            ->post(route('pengumuman.store'), [
                'judul' => 'Pengumuman Terjadwal',
                'isi' => 'Isi draft terjadwal.',
                'status' => 'draft',
                'tanggal_publish' => $future,
            ])
            ->assertRedirect()
            ->assertSessionHas('success');

        $pengumuman = Pengumuman::query()->firstOrFail();

        $this->assertSame('draft', $pengumuman->status);
        $this->assertTrue($pengumuman->isScheduled());
        Notification::assertNothingSent();
    }

    public function test_scheduled_draft_is_auto_published_when_due(): void
    {
        Notification::fake();

        Carbon::setTestNow('2026-08-17 15:00:00');

        $user = User::factory()->admin()->create();
        $divisi = Divisi::create(['nama' => 'Informer']);
        User::factory()->panitia()->create(['divisi_id' => $divisi->id]);

        Pengumuman::create([
            'judul' => 'Pengumuman Due',
            'isi' => 'Akan publish otomatis.',
            'status' => 'draft',
            'tanggal_publish' => now()->subMinute(),
            'pembuat_id' => $user->id,
        ]);

        Carbon::setTestNow('2026-08-17 15:05:00');

        $this->artisan('pengumuman:publish-scheduled')->assertSuccessful();

        $pengumuman = Pengumuman::query()->firstOrFail();

        $this->assertSame('published', $pengumuman->status);
        $this->assertTrue($pengumuman->isPublished());
        Notification::assertSentTimes(ReminderNotification::class, 1);
    }

    public function test_tanggal_publish_cannot_be_in_the_past_for_draft(): void
    {
        Carbon::setTestNow('2026-08-17 15:00:00');

        $user = User::factory()->admin()->create();

        $this->actingAs($user)
            ->from(route('pengumuman.index'))
            ->post(route('pengumuman.store'), [
                'judul' => 'Pengumuman Invalid',
                'isi' => 'Tanggal lampau.',
                'status' => 'draft',
                'tanggal_publish' => now()->subHour()->format('Y-m-d H:i:s'),
            ])
            ->assertSessionHasErrors('tanggal_publish');
    }

    protected function tearDown(): void
    {
        Carbon::setTestNow();

        parent::tearDown();
    }
}
