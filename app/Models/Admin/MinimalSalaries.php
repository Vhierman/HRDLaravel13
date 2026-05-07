<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;


class MinimalSalaries extends Model
{
    //
    use HasFactory;
    use SoftDeletes;

    protected $table = 'minimal_salaries';

    protected $fillable = [
        'minimal_upah',
        'areas_id',
        'input_oleh',
        'edit_oleh',
        'hapus_oleh'
    ];

    protected $hidden =[
        
    ];

    // To Table Minimal Salaries
    public function areas() {
        return $this->belongsTo(Areas::class,'areas_id','id');
    }
}
