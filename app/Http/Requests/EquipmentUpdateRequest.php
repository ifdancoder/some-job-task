<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Models\Equipment;
use App\Models\EquipmentType;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

use App\Rules\SerialNumber;

class EquipmentUpdateRequest extends FormRequest
{
    /**
     * Определяет, имеет ли пользователь право выполнять этот запрос.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Возвращает правила валидации, применяемые к запросу.
     *
     * @return array
     */
    public function rules(): array
    {
        $equipment = $this->route('equipment');
        return [
            'equipment_type_id' => [
                'sometimes',
                'integer',
                Rule::exists('equipment_types', 'id'),
            ],
            'serial_number' => [
                'sometimes',
                'string',
                Rule::unique('equipment')
                    ->where(function ($query) {
                        return $query->where('equipment_type_id', $this->input('equipment_type_id'));
                    })
                    ->ignore($equipment->id, 'id'),
                new SerialNumber(equipmentTypeId: $this->input('equipment_type_id'), equipment: $equipment),
            ],
            'description' => 'nullable|string',
        ];
    }

    /**
     * Возвращает сообщения об ошибках валидации.
     *
     * @return array
     */
    public function messages(): array
    {
        return [
            'equipment_type_id.integer' => 'Тип оборудования должен быть целым числом',
            'equipment_type_id.exists' => 'Указанного типа оборудования не существует',
            'serial_number.string' => 'Серийный номер должен быть строкой',
            'serial_number.unique' => 'Оборудование указанного типа с указанным серийным номером уже существует',
            'description.string' => 'Описание должно быть строкой',
        ];
    }

    protected function failedValidation(Validator $validator) {
        throw new HttpResponseException(response()->json($validator->errors(), 422));
    }
}
