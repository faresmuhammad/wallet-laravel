<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'description', 'color'
    ];

    public function parent()
    {
        return $this->belongsTo(Category::class);
    }

    public function subcategories()
    {
        return $this->hasMany(Category::class,'parent_id');
    }

    public function records()
    {
        return $this->hasMany(Record::class);

    }
}
