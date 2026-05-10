<?php

namespace App\Http\Requests\Admin;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class InventoryCarRequest extends FormRequest
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
            'merk_mobil'                    => 'required',
            'type_mobil'                    => 'required',
            'nomor_polisi'                  => 'required',
            'warna_mobil'                   => 'required',
            'nomor_rangka_mobil'            => 'required',
            'nomor_mesin_mobil'             => 'required',
            'tanggal_akhir_pajak_mobil'     => 'required|date',
            'tanggal_akhir_plat_mobil'      => 'required|date'
        ];
    }
}
