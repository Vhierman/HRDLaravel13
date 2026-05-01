<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class MaksimalUpahBpjsKetenagakerjaans extends Model
{
    //
    use HasFactory;
    use SoftDeletes;

    protected $table = 'maksimal_upah_bpjsketenagakerjaans';

    protected $fillable = [
        'maksimal_upah_bpjsketenagakerjaan',
        'input_oleh',
        'edit_oleh',
        'hapus_oleh'
    ];

    protected $hidden =[
        
    ];
}
