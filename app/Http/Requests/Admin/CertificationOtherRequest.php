<?php

namespace App\Http\Requests\Admin;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class CertificationOtherRequest extends FormRequest
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
            'employees_id'              => 'required',
            'nomor_sertifikat_lain'     => 'required',
            'jenis_sertifikat_lain'     => 'required',
            'tanggal_terbit_lain'       => 'required|date'
        ];
    }
}
