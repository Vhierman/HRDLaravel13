<?php

namespace App\Http\Requests\Admin;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class TrainingEksternalRequest extends FormRequest
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
            'employees_id'                                  => 'required',
            'tanggal_awal_training_eksternal'               => 'required|date',
            'tanggal_akhir_training_eksternal'              => 'required|date',
            'institusi_penyelenggara_training_eksternal'    => 'required',
            'perihal_training_eksternal'                    => 'required',
            'jam_training_eksternal'                        => 'required',
            'lokasi_training_eksternal'                     => 'required',
            'alamat_training_eksternal'                     => 'required'
        ];
    }
}
