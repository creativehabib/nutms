<?php

use App\Enums\ApprovalStatus;
use App\Models\College;
use App\Models\Teacher;
use App\Models\Training;

it('renders live platform statistics and the next training', function () {
    College::query()->create([
        'college_code' => '1001',
        'name' => 'Dynamic College',
        'is_active' => true,
    ]);
    Teacher::query()->create(['name' => 'Dynamic Teacher']);
    Training::factory()->create([
        'title' => 'Dynamic Teacher Training',
        'capacity' => 50,
        'status' => 'Upcoming',
    ]);

    $response = $this->get(route('home'));

    $response
        ->assertOk()
        ->assertSee('Dynamic Teacher Training')
        ->assertSee('১')
        ->assertSee('নিবন্ধিত শিক্ষক')
        ->assertSee('প্রশিক্ষণ কার্যক্রম')
        ->assertSee('অধিভুক্ত কলেজ')
        ->assertSee('৫০ জন');
});

it('lets public users browse affiliated colleges and their subjects', function () {
    $college = College::query()->create([
        'college_code' => '2020',
        'name' => 'Public Affiliated College',
        'principal_name' => 'Public Principal',
        'is_active' => true,
        'approval_status' => ApprovalStatus::Approved,
    ]);
    $college->programs()->create([
        'level' => 'honours',
        'name' => 'Honours',
        'items' => ['বাংলা', 'ইতিহাস'],
    ]);

    $this->get(route('home'))
        ->assertOk()
        ->assertSee('Public Affiliated College')
        ->assertSee('বাংলা')
        ->assertSee(route('public.colleges.show', $college));

    $this->get(route('public.colleges.show', $college))
        ->assertOk()
        ->assertSee('Public Principal')
        ->assertSee('বাংলা')
        ->assertSee('ইতিহাস');
});

it('only exposes active approved colleges to public users', function () {
    $college = College::query()->create([
        'name' => 'Pending Private College',
        'is_active' => true,
        'approval_status' => ApprovalStatus::Pending,
    ]);

    $this->get(route('home'))
        ->assertOk()
        ->assertDontSee('Pending Private College');

    $this->get(route('public.colleges.show', $college))->assertNotFound();
});

it('renders an empty state when no public training is available', function () {
    $this->get(route('home'))
        ->assertOk()
        ->assertSee('এই মুহূর্তে কোনো আসন্ন প্রশিক্ষণ নেই')
        ->assertSee('নতুন কোনো নোটিশ প্রকাশিত হয়নি');
});
