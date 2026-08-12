<?php

namespace Database\Seeders;

use App\Enums\ApprovalStatus;
use App\Models\College;
use App\Models\Designation;
use App\Models\District;
use App\Models\Division;
use App\Models\Employment;
use App\Models\Subject;
use App\Models\SystemSetting;
use App\Models\Teacher;
use App\Models\TeacherLevel;
use App\Models\Thana;
use App\Models\TrainingInstitute;
use App\Models\TrainingType;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        SystemSetting::query()->updateOrCreate(['key' => SystemSetting::RETIREMENT_AGE], ['value' => '59']);
        $this->call([
            DivisionSeeder::class,
            DistrictSeeder::class,
            ThanaSeeder::class,
            UnionSeeder::class,
            AffiliatedCollegeSeeder::class,
            ProgramLevelSeeder::class,
        ]);

        $division = Division::query()->where('name', 'Dhaka')->firstOrFail();
        $district = District::query()->where('division_id', $division->id)->where('name', 'Dhaka')->firstOrFail();
        $thana = Thana::query()->where('district_id', $district->id)->firstOrFail();

        $subject = Subject::query()->create(['name' => 'Information and Communication Technology']);
        $designation = Designation::query()->create(['name' => 'Lecturer']);
        $teacherLevel = TeacherLevel::query()->create(['name' => 'College']);
        $employment = Employment::query()->create(['name' => 'Permanent']);
        $trainingInstitute = TrainingInstitute::query()->create(['name' => 'National Academy for Educational Management']);
        $trainingType = TrainingType::query()->create([
            'training_institute_id' => $trainingInstitute->id,
            'name' => 'ICT Basic Training',
            'duration_value' => 7,
            'duration_unit' => 'days',
        ]);

        $admin = User::query()->forceCreate([
            'name' => 'Demo Admin',
            'email' => 'admin@example.com',
            'mobile_no' => '01700000001',
            'email_verified_at' => now(),
            'password' => Hash::make('password'),
            'approval_status' => ApprovalStatus::Approved,
            'approved_at' => now(),
        ]);
        $admin->syncRoles(['admin']);

        $college = College::query()->create([
            'college_code' => 'DEMO-001',
            'name' => 'Demo Government College',
            'division_id' => $division->id,
            'district_id' => $district->id,
            'thana_id' => $thana->id,
            'address' => 'Dhanmondi, Dhaka',
            'principal_name' => 'Demo Principal',
            'college_type' => 'government',
            'has_computer_lab' => true,
            'lab_equipment_type' => 'both',
            'desktop_count' => 20,
            'laptop_count' => 5,
            'approval_status' => ApprovalStatus::Approved,
            'approved_by' => $admin->id,
            'approved_at' => now(),
        ]);

        $principal = $this->createDemoUser(
            'Demo Principal',
            'principal@example.com',
            '01700000002',
            'principal',
            $college,
            $admin,
        );
        $teacherUser = $this->createDemoUser(
            'Demo Teacher',
            'teacher@example.com',
            '01700000003',
            'teacher',
            $college,
            $admin,
        );

        $this->createDemoTeacher($principal, $college, $division, $district, $thana, $subject, $designation, $teacherLevel, $employment, $admin);
        $teacher = $this->createDemoTeacher($teacherUser, $college, $division, $district, $thana, $subject, $designation, $teacherLevel, $employment, $admin);
        $teacher->trainingTypes()->attach($trainingType->id, ['training_year' => (int) date('Y')]);

        $college->update(['submitted_by' => $principal->id]);
        $college->programs()->create(['level' => 'honours', 'name' => 'Honours', 'items' => ['Information and Communication Technology']]);
    }

    private function createDemoUser(string $name, string $email, string $mobileNumber, string $role, College $college, User $admin): User
    {
        $user = User::query()->forceCreate([
            'name' => $name,
            'email' => $email,
            'mobile_no' => $mobileNumber,
            'email_verified_at' => now(),
            'password' => Hash::make('password'),
            'college_id' => $college->id,
            'approval_status' => ApprovalStatus::Approved,
            'approved_by' => $admin->id,
            'approved_at' => now(),
        ]);

        $user->syncRoles([$role]);

        return $user;
    }

    private function createDemoTeacher(
        User $user,
        College $college,
        Division $division,
        District $district,
        Thana $thana,
        Subject $subject,
        Designation $designation,
        TeacherLevel $teacherLevel,
        Employment $employment,
        User $admin,
    ): Teacher {
        return Teacher::query()->create([
            'user_id' => $user->id,
            'college_id' => $college->id,
            'name' => $user->name,
            'subject_id' => $subject->id,
            'designation_id' => $designation->id,
            'teacher_level_id' => $teacherLevel->id,
            'employment_id' => $employment->id,
            'division_id' => $division->id,
            'district_id' => $district->id,
            'thana_id' => $thana->id,
            'present_address' => 'Dhanmondi, Dhaka',
            'permanent_address' => 'Dhanmondi, Dhaka',
            'bank_name' => 'Demo Bank',
            'bank_branch_name' => 'Dhaka Branch',
            'bank_account_number' => $user->isPrincipal() ? '100000000001' : '100000000002',
            'bank_routing_number' => '123456789',
            'approval_status' => ApprovalStatus::Approved,
            'approved_by' => $admin->id,
            'approved_at' => now(),
        ]);
    }
}
