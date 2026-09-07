<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\Auditable;
class Unit extends Model
{
    use Auditable;
    public $timestamps = false;

    protected $fillable = ['code', 'name', 'name_en', 'is_active'];
    protected $casts = ['is_active' => 'boolean'];

    public function items() { return $this->hasMany(Item::class); }
}