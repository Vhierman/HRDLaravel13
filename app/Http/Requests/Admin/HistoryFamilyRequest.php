<?php

namespace App\Http\Requests\Admin;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class HistoryFamilyRequest extends FormRequest
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
            'employees_id'                          => 'required|integer|exists:employees,id',
            'nik_karyawan'                          => 'required|numeric|min:16',
            'hubungan_keluarga'                     => 'required|string|in:Suami,Istri,Anak',
            'nik_history_keluarga'                  => 'required|numeric|min:16',
            'nama_history_keluarga'                 => 'required|string',
            'jenis_kelamin_history_keluarga'        => 'required|string|in:Pria,Wanita',
            'tempat_lahir_history_keluarga'         => 'required',
            'tanggal_lahir_history_keluarga'        => 'required|date',
            'golongan_darah_history_keluarga'       => 'required|string|in:A,B,AB,O'
        ];
    }
}
