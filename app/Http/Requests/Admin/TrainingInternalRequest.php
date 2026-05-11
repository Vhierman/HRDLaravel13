<?php

namespace App\Http\Requests\Admin;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class TrainingInternalRequest extends FormRequest
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
            'employees_id'              => 'required',
            'tanggal_training_internal' => 'required|date',
            'jam_training_internal'     => 'required',
            'lokasi_training_internal'  => 'required',
            'materi_training_internal'  => 'required',
            'trainer_training_internal' => 'required'
        ];
    }
}
