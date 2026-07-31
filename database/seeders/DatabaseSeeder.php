<?php

namespace Database\Seeders;

use App\Enums\ApprovalStatus;
use App\Enums\UserRole;
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

        $admin = User::query()->create([
            'name' => 'Demo Admin',
            'email' => 'admin@example.com',
            'email_verified_at' => now(),
            'password' => Hash::make('password'),
            'role' => UserRole::Admin,
            'approval_status' => ApprovalStatus::Approved,
            'approved_at' => now(),
        ]);

        $college = College::query()->create([
            'code' => 'DEMO-001',
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

        $principal = $this->createDemoUser('Demo Principal', 'principal@example.com', UserRole::Principal, $college, $admin);
        $teacherUser = $this->createDemoUser('Demo Teacher', 'teacher@example.com', UserRole::Teacher, $college, $admin);

        $this->createDemoTeacher($principal, $college, $division, $district, $thana, $subject, $designation, $teacherLevel, $employment, $admin);
        $teacher = $this->createDemoTeacher($teacherUser, $college, $division, $district, $thana, $subject, $designation, $teacherLevel, $employment, $admin);
        $teacher->trainingTypes()->attach($trainingType->id, ['training_year' => (int) date('Y')]);

        $college->update(['submitted_by' => $principal->id]);
        $college->programs()->create(['level' => 'honours', 'name' => 'Honours', 'items' => ['Information and Communication Technology']]);
    }

    private function createDemoUser(string $name, string $email, UserRole $role, College $college, User $admin): User
    {
        return User::query()->create([
            'name' => $name,
            'email' => $email,
            'email_verified_at' => now(),
            'password' => Hash::make('password'),
            'role' => $role,
            'college_id' => $college->id,
            'approval_status' => ApprovalStatus::Approved,
            'approved_by' => $admin->id,
            'approved_at' => now(),
        ]);
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
            'college_code' => $college->code,
            'college_name' => $college->name,
            'name' => $user->name,
            'subject' => $subject->name,
            'subject_id' => $subject->id,
            'designation' => $designation->name,
            'designation_id' => $designation->id,
            'teacher_level' => $teacherLevel->name,
            'teacher_level_id' => $teacherLevel->id,
            'employment_type' => $employment->name,
            'employment_id' => $employment->id,
            'division_id' => $division->id,
            'district_id' => $district->id,
            'thana_id' => $thana->id,
            'present_address' => 'Dhanmondi, Dhaka',
            'permanent_address' => 'Dhanmondi, Dhaka',
            'mobile_number' => $user->role === UserRole::Principal ? '01700000001' : '01700000002',
            'email' => $user->email,
            'bank_name' => 'Demo Bank',
            'bank_branch_name' => 'Dhaka Branch',
            'bank_account_number' => $user->role === UserRole::Principal ? '100000000001' : '100000000002',
            'bank_routing_number' => '123456789',
            'approval_status' => ApprovalStatus::Approved,
            'approved_by' => $admin->id,
            'approved_at' => now(),
        ]);
    }
}
