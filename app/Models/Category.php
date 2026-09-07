<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\Auditable;

class Category extends Model
{
    use SoftDeletes , Auditable;
   

    protected $fillable = ['parent_id', 'code', 'name', 'description', 'is_active'];
    protected $casts = ['is_active' => 'boolean'];

    public function parent() { return $this->belongsTo(Category::class, 'parent_id'); }
    public function children() { return $this->hasMany(Category::class, 'parent_id'); }
    public function items() { return $this->hasMany(Item::class); }
}