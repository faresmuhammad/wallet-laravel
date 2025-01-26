<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Statistic extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class, 'category_statistic_pivot', 'statistic_id', 'category_id');
    }
    public function labels(): BelongsToMany
    {
        return $this->belongsToMany(Label::class, 'label_statistic_pivot', 'statistic_id', 'label_id');
    }
}
