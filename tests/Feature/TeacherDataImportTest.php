<?php

use App\Livewire\TeacherDataImport;
use App\Models\Teacher;
use Illuminate\Http\UploadedFile;
use Livewire\Livewire;

it('renders the professional import form', function () {
    Livewire::test(TeacherDataImport::class)
        ->assertSee('শিক্ষকের ডেটা ইম্পোর্ট')
        ->assertSee('সর্বোচ্চ ১০ MB')
        ->assertSeeHtml('border-dashed');
});

it('notifies the user instead of importing the same college again', function () {
    Teacher::query()->create([
        'college_code' => '126',
        'college_name' => 'FAKIRHAT GOVT. COLLEGE',
        'name' => 'Existing Teacher',
    ]);

    $file = UploadedFile::fake()->create(
        '126 - FAKIRHAT GOVT. COLLEGE - teachers.csv',
        10,
        'text/csv',
    );

    Livewire::test(TeacherDataImport::class)
        ->set('file', $file)
        ->call('import')
        ->assertSet('messageType', 'warning')
        ->assertSee('FAKIRHAT GOVT. COLLEGE (126) কলেজের শিক্ষক তথ্য ইতোমধ্যে ইম্পোর্ট করা হয়েছে।')
        ->assertNotDispatched('close-modal');

    expect(Teacher::query()->count())->toBe(1);
});
