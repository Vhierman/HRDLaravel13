<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Employees extends Model
{
    //
    use HasFactory;
    use SoftDeletes;

    protected $table = 'employees';

    protected $fillable = [
        'nik_karyawan',
        'input_oleh',
        'edit_oleh',
        'hapus_oleh'
    ];

    protected $hidden =[
        
    ];

    // To Table Karyawan
    public function golongans() {
        return $this->belongsTo(Golongans::class,'golongans_id','id');
    }
    public function companies() {
        return $this->belongsTo(Companies::class,'companies_id','id');
    }
    public function areas() {
        return $this->belongsTo(Areas::class,'areas_id','id');
    }
    public function divisions() {
        return $this->belongsTo(Divisions::class,'divisions_id','id');
    }
    public function positions() {
        return $this->belongsTo(Positions::class,'positions_id','id');
    }
    public function working_hours() {
        return $this->belongsTo(WorkingHours::class,'working_hours_id','id');
    }
    // To Table Karyawan
}
