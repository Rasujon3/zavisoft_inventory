<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $table = 'products';
    protected $fillable = [
        'name',
        'sku',
        'purchase_price',
        'sell_price',
        'opening_stock',
        'current_stock',
    ];
    protected $casts = [
        'purchase_price' => 'decimal:2',
        'sell_price' => 'decimal:2',
    ];
    public static function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'sku' => 'required|string|max:100|unique:products,sku',
            'purchase_price' => 'required|numeric|min:0',
            'sell_price' => 'required|numeric|min:0|gte:purchase_price',
            'opening_stock' => 'required|integer|min:0',
        ];
    }
    public static function updateRules($id = null): array
    {
        return [
            'name' => 'required|string|max:255',
            'sku' => 'required|string|max:100|unique:products,sku,' . $id,
            'purchase_price' => 'required|numeric|min:0',
            'sell_price' => 'required|numeric|min:0|gte:purchase_price',
            'opening_stock' => 'required|integer|min:0',
        ];
    }
}
