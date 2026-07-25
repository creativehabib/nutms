<?php

use App\Livewire\CollegeLabSummary;
use App\Models\Teacher;
use Livewire\Livewire;
use Maatwebsite\Excel\Facades\Excel;

test('each college lab tab can be exported to its own spreadsheet', function (string $tab, string $filename, string $hasComputerLab) {
    Excel::fake();

    Teacher::query()->create([
        'name' => 'Lab Teacher',
        'college_code' => '1001',
        'college_name' => 'Export College',
        'has_computer_lab' => $hasComputerLab,
        'computer_count' => $hasComputerLab === 'yes' ? 20 : null,
    ]);

    Livewire::test(CollegeLabSummary::class)->call('export', $tab);

    Excel::assertDownloaded($filename);
})->with([
    ['with_lab', 'colleges-with-computer-lab.xlsx', 'yes'],
    ['without_lab', 'colleges-without-computer-lab.xlsx', 'no'],
]);
