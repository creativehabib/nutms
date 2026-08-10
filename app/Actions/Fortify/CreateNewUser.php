<?php

namespace App\Actions\Fortify;

use App\Concerns\PasswordValidationRules;
use App\Concerns\ProfileValidationRules;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
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
            'mobile_no' => ['required', 'string', 'regex:/^(?:\+88|88)?(01[3-9]\d{8})$/', Rule::unique('users', 'mobile_no')],
            'college_id' => ['required', Rule::exists('colleges', 'id')->where(fn ($query) => $query->where('is_active', true)->where('approval_status', 'approved'))],
        ])->validate();

        return User::create([
            'name' => $input['name'],
            'email' => $input['email'],
            'mobile_no' => $input['mobile_no'],
            'password' => $input['password'],
            'college_id' => $input['college_id'],
        ]);
    }
}
