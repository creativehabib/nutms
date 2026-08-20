<?php

use App\Livewire\AdmissionSummary;
use App\Models\AdmissionInfo;
use Livewire\Livewire;

it('normalizes a college selection to a scalar value', function (): void {
    AdmissionInfo::query()->create([
        'college_code' => '1001',
        'college_name' => 'Test College',
        'subject_id' => 'ICT-01',
        'subject_name' => 'Information Technology',
        'sess_24_25_total_admited' => 25,
    ]);

    Livewire::test(AdmissionSummary::class)
        ->set('selectedCollege', ['1001'])
        ->assertSet('selectedCollege', '1001')
        ->assertSee('Information Technology')
        ->assertSee('25');
});

it('lists each subject once with its maximum admission total', function (): void {
    AdmissionInfo::query()->insert([
        [
            'college_code' => '1001',
            'college_name' => 'Test College',
            'subject_id' => 'BAN-01',
            'subject_name' => 'Bangla',
            'sess_24_25_total_admited' => 0,
        ],
        [
            'college_code' => '1001',
            'college_name' => 'Test College',
            'subject_id' => 'BAN-02',
            'subject_name' => 'Bangla',
            'sess_24_25_total_admited' => 0,
        ],
        [
            'college_code' => '1001',
            'college_name' => 'Test College',
            'subject_id' => 'ENG-01',
            'subject_name' => 'English',
            'sess_24_25_total_admited' => 0,
        ],
        [
            'college_code' => '1001',
            'college_name' => 'Test College',
            'subject_id' => 'ENG-02',
            'subject_name' => 'English',
            'sess_24_25_total_admited' => 50,
        ],
    ]);

    $component = Livewire::test(AdmissionSummary::class)
        ->set('selectedCollege', '1001')
        ->assertSeeHtmlInOrder(['Bangla', '0 Students', 'English', '50 Students']);

    expect(substr_count($component->html(), 'Bangla'))->toBe(1);
    expect(substr_count($component->html(), 'English'))->toBe(1);
});
