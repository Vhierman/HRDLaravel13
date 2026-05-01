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
        'input_oleh',
        'edit_oleh',
        'hapus_oleh'
    ];

    protected $hidden =[
        
    ];
}
