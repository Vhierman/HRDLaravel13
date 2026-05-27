<?php

namespace App\Http\Requests\Admin;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class EmployeeOutRequest extends FormRequest
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
            'employees_id'                      => 'required|integer|exists:employees,id',
            'keterangan_keluar'                 => 'required|string|in:Berakhir Kontrak Kerja,Pengunduran Diri,Pemutusan Hubungan Kerja,Memasuki Usia Pensiun',
            'tanggal_keluar_karyawan_keluar'    => 'required|date',
            'alasan_keluar'                     => 'required'
        ];
    }
}
