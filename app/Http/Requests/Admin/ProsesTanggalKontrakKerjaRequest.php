<?php

namespace App\Http\Requests\Admin;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class ProsesTanggalKontrakKerjaRequest extends FormRequest
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
            'tanggal_akhir_kontrak'         => 'required|date',
            'tanggal_awal_perpanjang'       => 'required|date',
            'tanggal_akhir_perpanjang'      => 'required|date'
        ];
    }
}
