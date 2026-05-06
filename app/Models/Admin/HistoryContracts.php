<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class HistoryContracts extends Model
{
    //
    use HasFactory;
    use SoftDeletes;

    protected $table = 'history_contracts';

    protected $fillable = [
        'employees_id',
        'nik_karyawan',
        'tanggal_awal_kontrak',
        'tanggal_akhir_kontrak',
        'status_kontrak_kerja',
        'masa_kontrak',
        'jumlah_kontrak',
        'input_oleh',
        'edit_oleh',
        'hapus_oleh'
    ];

    protected $hidden =[
        
    ];

    public function employees(){
        return $this->belongsTo(Employees::class,'employees_id','id');
    }
}
