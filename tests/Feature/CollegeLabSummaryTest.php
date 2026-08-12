<?php

use App\Livewire\CollegeLabSummary;
use App\Models\College;
use Livewire\Livewire;
use Maatwebsite\Excel\Facades\Excel;

test('college lab summary paginates colleges and only loads the active tab', function () {
    foreach (range(1, 51) as $index) {
        College::query()->create([
            'name' => "Lab College {$index}",
            'college_code' => (string) (1000 + $index),
            'has_computer_lab' => true,
            'is_active' => true,
        ]);
    }

    College::query()->create([
        'name' => 'College Without Lab',
        'college_code' => '2000',
        'has_computer_lab' => false,
        'is_active' => true,
    ]);

    Livewire::test(CollegeLabSummary::class)
        ->assertViewHas('colleges', fn ($colleges): bool => $colleges->count() === 50 && $colleges->total() === 51)
        ->assertDontSee('College Without Lab')
        ->call('showTab', 'without_lab')
        ->assertSet('activeTab', 'without_lab')
        ->assertViewHas('colleges', fn ($colleges): bool => $colleges->count() === 1 && $colleges->total() === 1)
        ->assertSee('College Without Lab');
});

test('each college lab tab can be exported to its own spreadsheet', function (string $tab, string $filename, bool $hasComputerLab) {
    Excel::fake();

    College::query()->create([
        'name' => 'Export College',
        'college_code' => '1001',
        'has_computer_lab' => $hasComputerLab,
        'desktop_count' => $hasComputerLab ? 12 : 0,
        'laptop_count' => $hasComputerLab ? 8 : 0,
        'is_active' => true,
    ]);

    Livewire::test(CollegeLabSummary::class)->call('export', $tab);

    Excel::assertDownloaded($filename);
})->with([
    ['with_lab', 'colleges-with-computer-lab.xlsx', true],
    ['without_lab', 'colleges-without-computer-lab.xlsx', false],
]);

test('college lab summary uses the canonical college lab status and computer counts', function () {
    College::query()->create([
        'name' => 'Canonical Lab College',
        'college_code' => 'LAB-01',
        'has_computer_lab' => true,
        'desktop_count' => 12,
        'laptop_count' => 8,
        'is_active' => true,
    ]);

    College::query()->create([
        'name' => 'Canonical No Lab College',
        'college_code' => 'NO-LAB-01',
        'has_computer_lab' => false,
        'desktop_count' => 99,
        'laptop_count' => 99,
        'is_active' => true,
    ]);

    College::query()->create([
        'name' => 'Inactive Lab College',
        'college_code' => 'INACTIVE-01',
        'has_computer_lab' => true,
        'is_active' => false,
    ]);

    Livewire::test(CollegeLabSummary::class)
        ->assertSee('Canonical Lab College')
        ->assertSee('20 টি')
        ->assertDontSee('Canonical No Lab College')
        ->assertDontSee('Inactive Lab College')
        ->call('showTab', 'without_lab')
        ->assertSee('Canonical No Lab College')
        ->assertDontSee('Canonical Lab College')
        ->assertDontSee('Inactive Lab College');
});
