<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Patients extends Model
{
    use HasFactory;

    // protected $table = 'patients';
    protected $fillable = ['name', 'phone', 'address', 'status_id', 'date_in', 'date_out'];

    public function statuses() {
        return $this->belongsTo(Statuses::class, 'status_id', 'id');
    }
}