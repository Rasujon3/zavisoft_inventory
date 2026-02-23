<?php

namespace App\Http\Requests;

use App\Models\Product;
use Illuminate\Foundation\Http\FormRequest;

class ProductRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        // Get the route name and apply null-safe operator
        $routeName = $this->route()?->getName();

        $data = $this->route('product');
        $id = $data?->id ?? null;

        if ($routeName === 'products.update') {
            return Product::updateRules($id);
        }

        return Product::rules($id);
    }
}
