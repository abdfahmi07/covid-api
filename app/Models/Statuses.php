<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Statuses extends Model
{
    use HasFactory;

    protected $hidden = ['created_at', 'updated_at'];
    // protected $fillable = ['name'];

    public function patients() {
        return $this->hasOne(Patients::class);
    }
}