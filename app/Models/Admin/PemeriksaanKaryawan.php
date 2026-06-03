<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class PemeriksaanKaryawan extends Model
{
    //
    use HasFactory;
    use SoftDeletes;

    protected $table = 'health_employees';

    protected $fillable = [
        'employees_id',
        'faskes_id',
        'nik_karyawan',
        'nomor_mcu',
        'tanggal_pemeriksaan',
        'dokter_pemeriksa',
        'berat_badan',
        'tinggi_badan',
        'tekanan_darah',
        'gula_darah',
        'ekg',
        'jenis_pemeriksaan',
        'status_kelayakan',
        'catatan_dokter',
        'tanggal_pemeriksaan_berikutnya',
        'input_oleh',
        'edit_oleh',
        'hapus_oleh'
    ];

    protected $hidden =[
        
    ];

    //To Table Health Employees
    public function employees(){
        return $this->belongsTo(Employees::class,'employees_id','id');
    }
    public function faskes(){
        return $this->belongsTo(Faskes::class,'faskes_id','id');
    }
}
