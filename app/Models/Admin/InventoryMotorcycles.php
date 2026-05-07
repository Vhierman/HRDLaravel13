<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class InventoryMotorcycles extends Model
{
    //
    use HasFactory;
    use SoftDeletes;

    protected $table = 'inventory_motorcycles';

    protected $fillable = [
        'employees_id',
        'nik_karyawan',
        'merk_motor',
        'type_motor',
        'nomor_polisi',
        'warna_motor',
        'nomor_rangka_motor',
        'nomor_mesin_motor',
        'tanggal_akhir_pajak_motor',
        'tanggal_akhir_plat_motor',
        'input_oleh',
        'edit_oleh',
        'hapus_oleh'
    ];

    protected $hidden =[
        
    ];

    //To Table Inventory Motorcycles
    public function employees(){
        return $this->belongsTo(Employees::class,'employees_id','id');
    }
}
