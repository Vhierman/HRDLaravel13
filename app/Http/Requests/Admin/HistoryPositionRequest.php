<?php

namespace App\Http\Requests\Admin;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class HistoryPositionRequest extends FormRequest
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
            'employees_id'      => 'required',
            'nik_karyawan'      => 'required',
            'companies_id'      => 'required',
            'areas_id'          => 'required',
            'divisions_id'      => 'required',
            'positions_id'      => 'required',
            'tanggal_mutasi'    => 'required|date'
        ];
    }
}
