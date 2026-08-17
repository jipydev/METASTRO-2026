<?php

namespace Tests\Unit;

use App\Models\Divisi;
use PHPUnit\Framework\TestCase;

class DivisiBadgeTest extends TestCase
{
    public function test_each_known_divisi_has_distinct_badge_classes(): void
    {
        $divisis = [
            'Archivist', 'Chef', 'Chiper', 'Documenter', 'Fundkeeper', 'Gearmaster',
            'Guardian', 'Guider', 'Informer', 'Pathfinder', 'Ranger', 'Rescuer',
            'Scribe', 'Stakeholder',
        ];

        $classes = array_map(fn (string $nama) => Divisi::badgeClassesFor($nama), $divisis);

        $this->assertCount(count($divisis), array_unique($classes));
    }

    public function test_unknown_divisi_uses_fallback_badge(): void
    {
        $this->assertStringContainsString('bg-slate-100', Divisi::badgeClassesFor('Unknown'));
    }
}
