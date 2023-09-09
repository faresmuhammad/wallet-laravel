<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'description', 'color'
    ];

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function subcategories(): HasMany
    {
        return $this->hasMany(Category::class,'parent_id');
    }

    public function records(): HasMany
    {
        return $this->hasMany(Record::class);

    }

    public function budgets()
    {
        return $this->belongsToMany(Budget::class,'budget_category_pivot');
    }
}
