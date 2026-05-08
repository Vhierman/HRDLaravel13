<?php

namespace App\Http\Requests\Admin;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class EmployeeOutUpdateRequest extends FormRequest
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
            'nik_karyawan_keluar'               => 'required',
            'nama_karyawan_keluar'              => 'required|string',
            'keterangan_keluar'                 => 'required',
            'tanggal_keluar_karyawan_keluar'    => 'required|date',
        ];
    }
}
