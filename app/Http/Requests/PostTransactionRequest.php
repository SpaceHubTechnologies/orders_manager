<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Symfony\Component\HttpFoundation\JsonResponse;

class PostTransactionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'customer_id' => ['required'],
            'code_sale_master' => ['unique:transactions', 'required'],
            'status' => ['required'],
            'last_update' => ['required'],
            'payment_method' => ['required'],
            'total_value' => ['required'],
            'total_paid' => ['required'],
            'sale_type' => ['required'],
            'customerIdType' => ['required'],
            'customerId' => ['required'],
            'customerName' => ['required'],
            'mobileNumber' => ['required'],
            'description' => ['required'],
        ];
    }

    public function failedValidation(Validator $validator)
    {
        $messageBag = collect($validator->errors()->messages());
        $message = implode('|', $messageBag->flatten()->toArray());
        throw new HttpResponseException(response()->json(['error' => true, 'message' => $message], JsonResponse::HTTP_UNPROCESSABLE_ENTITY));
    }
}
