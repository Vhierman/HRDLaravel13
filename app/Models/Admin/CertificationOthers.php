<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class CertificationOthers extends Model
{
    //
    use HasFactory;
    use SoftDeletes;

    protected $table = 'certification_others';

    protected $fillable = [
        'employees_id',
        'nik_karyawan',
        'jumlah_sertifikat_lain',
        'nomor_sertifikat_lain',
        'jenis_sertifikat_lain',
        'tanggal_terbit_lain',
        'input_oleh',
        'edit_oleh',
        'hapus_oleh'
    ];

    protected $hidden =[
        
    ];

    //To Table Certification Others
    public function employees(){
        return $this->belongsTo(Employees::class,'employees_id','id');
    }
}
