<?php

namespace App\Http\Requests\Admin;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class EmployeeUpdateRequest extends FormRequest
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
            'companies_id'              => 'required|integer|exists:companies,id',
            'golongans_id'              => 'required|integer|exists:golongans,id',
            'divisions_id'              => 'required|integer|exists:divisions,id',
            'positions_id'              => 'required|integer|exists:positions,id',
            'working_hours_id'          => 'required|integer|exists:working_hours,id',
            'status_kerja'              => 'required|string|in:PKWTT,PKWT,Harian,Outsourcing',
            'tanggal_mulai_kerja'       => 'required|date',
            'tanggal_akhir_kerja'       => 'required|date',
            'nomor_rekening'            => 'required|numeric',
            'nama_bank'                 => 'required',
            'nik_karyawan'              => 'required|numeric|min:16',
            'nama_karyawan'             => 'required|string',
            'email_karyawan'            => 'required|email',
            'nomor_absen'               => 'required',
            'nomor_handphone'           => 'required|numeric',
            'tempat_lahir'              => 'required',
            'tanggal_lahir'             => 'required|date',
            'agama'                     => 'required|string|in:Islam,Kristen Protestan,Kristen Katholik,Hindu,Budha',
            'jenis_kelamin'             => 'required|string|in:Pria,Wanita',
            'pendidikan_terakhir'       => 'required|in:SD,SMP,SMA/SMK,D3,S1,S2,S3',
            'golongan_darah'            => 'required|string|in:A,B,AB,O',
            'status_nikah'              => 'required|string|in:Single,Menikah,Janda,Duda',
            'nama_ayah'                 => 'required|string',
            'nama_ibu'                  => 'required|string',
            'alamat'                    => 'required',
            'rt'                        => 'required|numeric',
            'rw'                        => 'required|numeric',
            'kelurahan'                 => 'required',
            'kecamatan'                 => 'required',
            'kota'                      => 'required',
            'provinsi'                  => 'required',
            'kode_pos'                  => 'required|numeric|min:5'
        ];
    }
}
