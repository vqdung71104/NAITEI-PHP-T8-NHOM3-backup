<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Order;

class OrderRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; 
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $rules = [
            'address_id' => 'required|exists:addresses,id',
            'status' => 'sometimes|in:' . implode(',', Order::getStatuses()),
            'order_items' => 'required|array|min:1',
            'order_items.*.product_id' => 'required|exists:products,id',
            'order_items.*.quantity' => 'required|integer|min:1',
        ];

        if ($this->isMethod('PUT') || $this->isMethod('PATCH')) {
            if (!auth()->user() || !auth()->user()->isAdmin()) {
                unset($rules['status']);
            }
        }

        return $rules;
    }

    /**
     * Get custom error messages for validation
     */
    public function messages(): array
    {
        return [
            'address_id.required' => 'Địa chỉ giao hàng là bắt buộc.',
            'address_id.exists' => 'Địa chỉ giao hàng không tồn tại.',
            'status.in' => 'Trạng thái đơn hàng không hợp lệ.',
            'order_items.required' => 'Đơn hàng phải có ít nhất một sản phẩm.',
            'order_items.array' => 'Dữ liệu sản phẩm không hợp lệ.',
            'order_items.min' => 'Đơn hàng phải có ít nhất một sản phẩm.',
            'order_items.*.product_id.required' => 'ID sản phẩm là bắt buộc.',
            'order_items.*.product_id.exists' => 'Sản phẩm không tồn tại.',
            'order_items.*.quantity.required' => 'Số lượng sản phẩm là bắt buộc.',
            'order_items.*.quantity.integer' => 'Số lượng sản phẩm phải là số nguyên.',
            'order_items.*.quantity.min' => 'Số lượng sản phẩm phải lớn hơn 0.',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        if ($this->isMethod('POST') && !$this->has('status')) {
            $this->merge([
                'status' => Order::STATUS_PENDING
            ]);
        }

        if (auth()->check()) {
            $this->merge([
                'user_id' => auth()->id()
            ]);
        }
    }
}