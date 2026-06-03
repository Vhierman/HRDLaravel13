<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Faskes extends Model
{
    //
    use HasFactory;
    use SoftDeletes;

    protected $table = 'faskes';

    protected $fillable = [
        'nama_faskes',
        'alamat',
        'rt',
        'rw',
        'kelurahan',
        'kecamatan',
        'kota',
        'provinsi',
        'kode_pos',
        'input_oleh',
        'edit_oleh',
        'hapus_oleh'
    ];

    protected $hidden =[
        
    ];

    // From Table Faskes
    public function health_employees() {
        return $this->hasMany(PemeriksaanKaryawan::class,'faskes_id','id');
    }
}
