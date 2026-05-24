<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Safetys extends Model
{
    //
    use HasFactory;
    use SoftDeletes;

    protected $table = 'safetys';

    protected $fillable = [
        'employees_id',
        'nik_karyawan',
        'tanggal_kecelakaan',
        'lokasi_kecelakaan',
        'jenis_kecelakaan',
        'kategori_kecelakaan',
        'hari_hilang',
        'status',
        'input_oleh',
        'edit_oleh',
        'hapus_oleh'
    ];

    protected $hidden =[
        
    ];

    //To Table History Salaries
    public function employees(){
        return $this->belongsTo(Employees::class,'employees_id','id');
    }
}
