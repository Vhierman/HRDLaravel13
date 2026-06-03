<?php

namespace App\Http\Requests\Admin;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class FaskesRequest extends FormRequest
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
            'nama_faskes'   => 'required',
            'alamat'        => 'required',
            'rt'            => 'required',
            'rw'            => 'required',
            'kelurahan'     => 'required',
            'kecamatan'     => 'required',
            'kota'          => 'required',
            'provinsi'      => 'required',
            'kode_pos'      => 'required',
        ];
    }
}
