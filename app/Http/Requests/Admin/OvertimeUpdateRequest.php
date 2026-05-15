<?php

namespace App\Http\Requests\Admin;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class OvertimeUpdateRequest extends FormRequest
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
            'tanggal_lembur'    => 'required|date',
            'keterangan_lembur' => 'required',
            'jam_masuk'         => 'required',
            'jam_istirahat'     => 'required',
            'jam_pulang'        => 'required',
            'jenis_lembur'      => 'required|string|in:Biasa,Libur'
        ];
    }
}
