<?php

namespace App\Models;

use App\Enums\BudgetPeriod;
use App\Enums\BudgetStatus;
use App\Enums\BudgetType;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Budget extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'period' => BudgetPeriod::class,
        'status' => BudgetStatus::class,
        'type' => BudgetType::class
    ];


    public function records(): HasMany
    {
        return $this->hasMany(Record::class);
    }


    public function master(): BelongsTo
    {
        return $this->belongsTo(self::class, 'master_id');
    }

    public function repeats(): HasMany
    {
        return $this->hasMany(self::class, 'master_id');
    }

    public function wallets(): BelongsToMany
    {
        return $this->belongsToMany(Wallet::class, 'budget_wallet_pivot');
    }

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class, 'budget_category_pivot');
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', 'Active');
    }

    public function scopeRepeatable(Builder $query): Builder
    {
        return $query->where('type', 'Repeatable');
    }

    public function scopeIsMaster(Builder $query): Builder
    {
        return $query->where('type', BudgetType::Master->value);
    }

    public function scopeLinkedWith(Builder $query, Wallet $wallet, Category $category)
    {
        return $query->whereHas('wallets', function ($query) use ($wallet) {
            $query->where('wallets.id', $wallet->id);
        })->whereHas('categories', function ($query) use ($category) {
            $query->where('categories.id', $category->id);
        })->active()->repeatable();
    }

    protected function remaining(): Attribute
    {
        return Attribute::get(fn() => $this->target_amount - $this->current_amount);
    }
}
