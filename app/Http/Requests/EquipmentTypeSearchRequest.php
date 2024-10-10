<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Foundation\Http\FormRequest;

class EquipmentTypeSearchRequest extends FormRequest
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
        return [
            'q' => 'string',
            'id' => 'integer',
            'equipment_type_name' => 'string',
            'serial_number_mask' => 'string',
            'perPage' => 'integer',
        ];
    }

    public function messages(): array
    {
        return [
            'q.string' => 'Поисковая строка должна быть представлена строкой',
            'perPage.integer' => 'Количество записей на странице должно быть целым числом',
            'serial_number_mask.string' => 'Маска серийного номера должна быть представлена строкой',
            'equipment_type_name.string' => 'Название типа оборудования должно быть представлено строкой',
            'id.integer' => 'ID типа оборудования должно быть целым числом',
        ];
    }

    protected function failedValidation(Validator $validator) {
        throw new HttpResponseException(response()->json($validator->errors(), 422));
    }
}
