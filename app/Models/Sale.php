<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
    use HasFactory;

    protected $table = 'sales';
    protected $fillable = [
        'invoice_no',
        'customer_id',
        'sale_date',
        'sub_total',
        'discount',
        'vat_percent',
        'vat_amount',
        'grand_total',
        'paid_amount',
        'due_amount',
    ];

    protected $casts = [
        'sale_date' => 'date',
        'sub_total' => 'decimal:2',
        'discount' => 'decimal:2',
        'vat_percent' => 'decimal:2',
        'vat_amount' => 'decimal:2',
        'grand_total' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'due_amount' => 'decimal:2',
    ];

    public static function rules($id = null): array
    {
        return [
            'invoice_no' => 'required|string|unique:sales,invoice_no,' . $id,
            'customer_id' => 'required|exists:customers,id',
            'sale_date' => 'required|date',

            'discount' => 'nullable|numeric|min:0',
            'vat_percent' => 'nullable|numeric|min:0',

            'paid_amount' => 'nullable|numeric|min:0',

            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
        ];
    }

    public function items()
    {
        return $this->hasMany(SaleItem::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
}
