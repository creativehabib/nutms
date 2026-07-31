<?php

namespace App\Actions\Fortify;

use App\Concerns\PasswordValidationRules;
use App\Concerns\ProfileValidationRules;
use App\Enums\ApprovalStatus;
use App\Enums\UserRole;
use App\Models\College;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Laravel\Fortify\Contracts\CreatesNewUsers;

class CreateNewUser implements CreatesNewUsers
{
    use PasswordValidationRules, ProfileValidationRules;

    /**
     * Validate and create a newly registered user.
     *
     * @param  array<string, string>  $input
     */
    public function create(array $input): User
    {
        Validator::make($input, [
            ...$this->profileRules(),
            'password' => $this->passwordRules(),
            'role' => ['required', Rule::enum(UserRole::class)->only([UserRole::Principal, UserRole::Teacher])],
            'college_id' => [Rule::requiredIf(($input['role'] ?? null) === UserRole::Principal->value), 'nullable', 'integer'],
        ])->validate();

        return DB::transaction(function () use ($input): User {
            $collegeId = null;
            if ($input['role'] === UserRole::Principal->value) {
                $college = College::query()->lockForUpdate()->find($input['college_id']);
                if ($college === null || ! $college->is_active || $college->approval_status !== ApprovalStatus::Approved || User::query()->where('role', UserRole::Principal->value)->where('college_id', $college->id)->exists()) {
                    throw ValidationException::withMessages(['college_id' => 'কলেজটি নির্বাচনযোগ্য নয় অথবা ইতোমধ্যে অন্য প্রিন্সিপালের সাথে যুক্ত।']);
                }
                $collegeId = $college->id;
            }

            return User::create([
                'name' => $input['name'],
                'email' => $input['email'],
                'password' => $input['password'],
                'role' => $input['role'],
                'college_id' => $collegeId,
                'approval_status' => $input['role'] === UserRole::Principal->value ? ApprovalStatus::Pending : ApprovalStatus::Approved,
            ]);
        });
    }
}
