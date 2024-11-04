<?php

namespace App\Models;

use App\Enums\Currency;
use App\Services\Money\Money;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Product extends Model
{
    use HasFactory;

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = ['name', 'price', 'currency'];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            if (empty($model->id)) {
                $model->id = Str::uuid();
            }
        });
    }

    /**
     * Get the Money object representation of the product's price.
     */
    public function getMoney(): Money
    {
        return Money::fromStoredAmount($this->price, Currency::from($this->currency));
    }
}
