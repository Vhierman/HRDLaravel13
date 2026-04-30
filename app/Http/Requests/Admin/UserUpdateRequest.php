<?php

namespace App\Http\Requests\Admin;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UserUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $userId = $this->user()->id;

        return [
            //
            'name'      => 'required|string',
            'roles'     => 'required|string|in:admin,karyawan,hrd,accounting,leader,supervisor,manager',
            'nik'       => 'required|string|min:16',
            'email'     => 'required|email',
        ];
    }
}
