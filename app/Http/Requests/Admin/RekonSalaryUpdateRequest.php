<?php

namespace App\Http\Requests\Admin;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class RekonSalaryUpdateRequest extends FormRequest
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
        return [
            //
            'gaji_pokok'            => 'required',
            'uang_makan'            => 'required',
            'uang_transport'        => 'required',
            'tunjangan_tugas'       => 'required',
            'tunjangan_pulsa'       => 'required',
            'tunjangan_jabatan'     => 'required'
            
        ];
    }
}
