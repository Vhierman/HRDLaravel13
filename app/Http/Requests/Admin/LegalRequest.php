<?php

namespace App\Http\Requests\Admin;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class LegalRequest extends FormRequest
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
            'nama_perijinan'    => 'required',
            'nomor_perijinan'   => 'required',
            'instansi_penerbit' => 'required',
            'tanggal_berlaku'   => 'required|date',
            'tanggal_habis'     => 'required|date'
        ];
    }
}
