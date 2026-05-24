<?php

namespace App\Http\Requests\Admin;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class SafetyRequest extends FormRequest
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
            'employees_id'          => 'required|integer|exists:employees,id',
            'tanggal_kecelakaan'    => 'required|date',
            'lokasi_kecelakaan'     => 'required',
            'jenis_kecelakaan'      => 'required',
            'kategori_kecelakaan'   => 'required|string|in:Fatality,LWD,Non LWD,Traffic Accident',
            'hari_hilang'           => 'required',
            'status'                => 'required|string|in:Sembuh,Gangguan Kesehatan,Cacat,Meninggal'            
        ];
    }
}
