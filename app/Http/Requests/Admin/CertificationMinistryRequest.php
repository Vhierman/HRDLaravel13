<?php

namespace App\Http\Requests\Admin;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class CertificationMinistryRequest extends FormRequest
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
            'employees_id'                        => 'required',
            'nomor_sertifikat_kementrian'         => 'required',
            'jenis_sertifikat_kementrian'         => 'required',
            'masa_berlaku_sertifikat_kementrian'  => 'required',
            'tanggal_terbit_kementrian'           => 'required|date',
            'sampai_tanggal_kementrian'           => 'required|date',
            'lsp_kementrian'                      => 'required'
        ];
    }
}
