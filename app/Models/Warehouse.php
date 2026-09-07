<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\Auditable;
class Warehouse extends Model
{
    use HasFactory, SoftDeletes,Auditable;
 

    protected $fillable = ['code', 'name', 'location', 'manager_name', 'phone', 'is_active', 'notes'];
    protected $casts = ['is_active' => 'boolean'];
}