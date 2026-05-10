<?php

namespace App\Http\Requests\Admin;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class InventoryMotorcycleRequest extends FormRequest
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
            'employees_id'                  => 'required|integer|exists:employees,id',
            'merk_motor'                    => 'required',
            'type_motor'                    => 'required',
            'nomor_polisi'                  => 'required',
            'warna_motor'                   => 'required',
            'nomor_rangka_motor'            => 'required',
            'nomor_mesin_motor'             => 'required',
            'tanggal_akhir_pajak_motor'     => 'required|date',
            'tanggal_akhir_plat_motor'      => 'required|date'
        ];
    }
}
