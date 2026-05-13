<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class CertificationMinistries extends Model
{
    //
    use HasFactory;
    use SoftDeletes;

    protected $table = 'certification_ministries';

    protected $fillable = [
        'employees_id',
        'nik_karyawan',
        'jumlah_sertifikat_kementrian',
        'nomor_sertifikat_kementrian',
        'jenis_sertifikat_kementrian',
        'masa_berlaku_sertifikat_kementrian',
        'tanggal_terbit_kementrian',
        'sampai_tanggal_kementrian',
        'lsp_kementrian',
        'input_oleh',
        'edit_oleh',
        'hapus_oleh'
    ];

    protected $hidden =[
        
    ];

    //To Table Certification Ministry
    public function employees(){
        return $this->belongsTo(Employees::class,'employees_id','id');
    }
}
