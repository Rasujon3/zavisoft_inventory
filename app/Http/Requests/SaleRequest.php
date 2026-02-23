<?php

namespace App\Http\Requests;

use App\Models\Sale;
use Illuminate\Foundation\Http\FormRequest;

class SaleRequest extends FormRequest
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

        $data = $this->route('sale');
        $id = $data?->id ?? null;

//        if ($routeName === 'sales.update') {
//            return Sale::updateRules($id);
//        }

        return Sale::rules($id);
    }
}
