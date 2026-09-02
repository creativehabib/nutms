<?php

namespace App\Livewire\SalaryCalculator;

use Livewire\Component;

class SalaryCalculator extends Component
{
    public $grade = '';
    public $step = '';
    public $location = 1;
    public $result = null;

    // ২০১৫ সালের গেজেট অনুযায়ী গ্রেডভিত্তিক সম্পূর্ণ নিখুঁত ধাপসমূহ (১ থেকে ২০ গ্রেড)
    protected $payScale2015Steps = [
        1 => [78000],
        2 => [66000, 68460, 71050, 73720, 76490], // ৬৮৪৬০ ঠিক করা হয়েছে
        3 => [56500, 58760, 61120, 63570, 66120, 68770, 71530, 74400],
        4 => [50000, 52000, 54080, 56250, 58500, 60840, 63280, 65820, 68460, 71200],
        5 => [43000, 44940, 46970, 49090, 51300, 53610, 56030, 58560, 61200, 63960, 66840, 69850],
        6 => [35500, 37280, 39150, 41110, 43170, 45330, 47600, 49980, 52480, 55110, 57870, 60770, 63810, 67010],
        7 => [29000, 30450, 31980, 33580, 35260, 37030, 38890, 40840, 42890, 45040, 47300, 49670, 52160, 54770, 57510, 60390, 63410],
        8 => [23000, 24150, 25360, 26630, 27970, 29370, 30840, 32390, 34010, 35720, 37510, 39390, 41360, 43430, 45610, 47900, 50300, 52820, 55470],
        9 => [22000, 23100, 24260, 25480, 26760, 28100, 29510, 30990, 32540, 34170, 35880, 37680, 39570, 41550, 43630, 45820, 48120, 50530, 53060],
        10 => [16000, 16800, 17640, 18530, 19460, 20440, 21470, 22550, 23680, 24870, 26120, 27430, 28810, 30260, 31780, 33370, 35040, 36800, 38640],
        11 => [12500, 13130, 13790, 14480, 15210, 15980, 16780, 17620, 18510, 19440, 20420, 21450, 22530, 23660, 24850, 26100, 27410, 28790, 30230],
        12 => [11300, 11870, 12470, 13100, 13760, 14450, 15180, 15940, 16740, 17580, 18460, 19390, 20360, 21380, 22450, 23580, 24760, 26000, 27300],
        13 => [11000, 11550, 12130, 12740, 13380, 14050, 14760, 15500, 16280, 17100, 17960, 18860, 19810, 20810, 21860, 22960, 24110, 25320, 26590],
        14 => [10200, 10710, 11250, 11820, 12420, 13050, 13710, 14400, 15120, 15880, 16680, 17520, 18400, 19320, 20290, 21310, 22380, 23500, 24680],
        15 => [9700, 10190, 10700, 11240, 11810, 12410, 13040, 13700, 14390, 15110, 15870, 16670, 17510, 18390, 19310, 20280, 21300, 22370, 23490],
        16 => [9300, 9770, 10260, 10780, 11320, 11890, 12490, 13120, 13780, 14470, 15200, 15960, 16760, 17600, 18480, 19410, 20390, 21410, 22490],
        17 => [9000, 9450, 9930, 10430, 10960, 11510, 12090, 12700, 13340, 14010, 14720, 15460, 16240, 17060, 17920, 18820, 19770, 20760, 21800],
        18 => [8800, 9240, 9710, 10200, 10710, 11250, 11820, 12420, 13050, 13710, 14400, 15120, 15880, 16680, 17520, 18400, 19320, 20290, 21310],
        19 => [8500, 8930, 9380, 9850, 10350, 10870, 11420, 12000, 12600, 13230, 13900, 14600, 15330, 16100, 16910, 17760, 18650, 19590, 20570],
        20 => [8250, 8670, 9110, 9570, 10050, 10560, 11090, 11650, 12240, 12860, 13510, 14190, 14900, 15650, 16440, 17270, 18140, 19050, 20010]
    ];

    public function updatedGrade()
    {
        $this->step = '';
        $this->result = null;
    }

    public function getStepsProperty()
    {
        if (!$this->grade) return [];
        return $this->payScale2015Steps[$this->grade] ?? [];
    }

    public function calculate()
    {
        $this->validate([
            'grade' => 'required|numeric',
            'step' => 'required|numeric',
            'location' => 'required|numeric',
        ]);

        $grade = (int) $this->grade;
        $current_basic = (int) $this->step;

        $old_base = $this->payScale2015Steps[$grade][0];
        $new_base = $old_base * 2; // ২০২৬ সালের জন্য বেসিক দ্বিগুণ ধরা হয়েছে

        $earned_inc = $current_basic - $old_base; // অর্জিত ইনক্রিমেন্ট
        $adjusted_basic = $new_base + $earned_inc; // সমন্বিত বেসিক

        // ২০২৬ সালের নতুন স্কেলের ধাপসমূহ (৫% ফর্মুলায় তৈরি করা হয়েছে)
        $new_steps = [$new_base];
        $temp_basic = $new_base;
        $next_step_new = $new_base;
        $next_step_index = 1;

        if ($grade > 1) {
            for ($i = 1; $i <= 30; $i++) {
                $temp_basic = ceil(($temp_basic * 1.05) / 10) * 10;
                $new_steps[] = $temp_basic;
            }
        }

        // পরবর্তী উচ্চতর ধাপ বের করা
        foreach ($new_steps as $idx => $step_val) {
            if ($step_val >= $adjusted_basic) {
                $next_step_new = $step_val;
                $next_step_index = $idx + 1;
                break;
            }
        }

        $basic_diff = $next_step_new - $current_basic; // বেসিকের পার্থক্য

        // গ্রেড অনুযায়ী পার্সেন্টেজ নির্ধারণ (১-৯ গ্রেডে ৪০%, ১০-২০ গ্রেডে ৫০%)
        $percentage = ($grade <= 9) ? 40 : 50;
        $benefit = round($basic_diff * ($percentage / 100));

        $final_basic = $current_basic + $benefit; // চূড়ান্ত নতুন মূল বেতন

        // বাড়ি ভাড়া ভাতা হিসাব (নতুন বেসিকের উপর ভিত্তি করে)
        $location = (int) $this->location;
        $houseRent = 0;

        if ($final_basic <= 9700) {
            if ($location === 1) $houseRent = max(5600, $final_basic * 0.65);
            elseif ($location === 2) $houseRent = max(5000, $final_basic * 0.60);
            else $houseRent = max(4500, $final_basic * 0.55);
        } elseif ($final_basic <= 16000) {
            if ($location === 1) $houseRent = max(6400, $final_basic * 0.60);
            elseif ($location === 2) $houseRent = max(5400, $final_basic * 0.55);
            else $houseRent = max(4800, $final_basic * 0.50);
        } elseif ($final_basic <= 35500) {
            if ($location === 1) $houseRent = max(9600, $final_basic * 0.55);
            elseif ($location === 2) $houseRent = max(8000, $final_basic * 0.50);
            else $houseRent = max(7000, $final_basic * 0.45);
        } else {
            if ($location === 1) $houseRent = max(19500, $final_basic * 0.50);
            elseif ($location === 2) $houseRent = max(16000, $final_basic * 0.45);
            else $houseRent = max(13800, $final_basic * 0.40);
        }

        $medical = 1500;
        // ১১-২০ গ্রেডের জন্য ২০০ টাকা টিফিন ভাতা নির্ধারণ
        $tiffin = ($grade >= 11 && $grade <= 20) ? 200 : 0;

        // োট বেতনের সাথে টিফিন ভাতা যোগ করা হয়েছে
        $totalGross = $final_basic + $houseRent + $medical + $tiffin;

        $this->result = [
            'grade' => $grade,
            'current_basic' => $current_basic,
            'old_base' => $old_base,
            'earned_inc' => $earned_inc,
            'new_base' => $new_base,
            'adjusted_basic' => $adjusted_basic,
            'next_step_new' => $next_step_new,
            'next_step_index' => $next_step_index,
            'basic_diff' => $basic_diff,
            'percentage' => $percentage,
            'benefit' => $benefit,
            'final_basic' => $final_basic,
            'house_rent' => round($houseRent),
            'medical' => $medical,
            'tiffin' => $tiffin,
            'total' => round($totalGross),
        ];
    }

    public function toBengali($number)
    {
        $english = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9'];
        $bengali = ['০', '১', '২', '৩', '৪', '৫', '৬', '৭', '৮', '৯'];
        return str_replace($english, $bengali, $number);
    }
    public function render()
    {
        return view('livewire.salary-calculator.salary-calculator')->layout('layouts.frontend',['title'=> 'Salary Calculator']);
    }
}
