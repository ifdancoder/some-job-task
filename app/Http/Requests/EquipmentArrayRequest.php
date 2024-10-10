<?php

namespace App\Http\Requests;

use App\Rules\SerialNumber;
use App\Rules\EquipmentsRule;
use Illuminate\Foundation\Http\FormRequest;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Support\Facades\Validator as FacadesValidator;
use Illuminate\Http\Exceptions\HttpResponseException;
use \stdClass;

class EquipmentArrayRequest extends FormRequest
{
    public stdClass $postErrors;
    public stdClass $postValidated;

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
            'equipments' => 'array',
        ];
    }

    public function messages(): array
    {
        return [
            'equipments' => 'Атрибут :attribute должен являться массивом',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json($validator->errors(), 422));
    }

    protected function passedValidation(): void
    {
        $this->postErrors = new stdClass();
        $this->postValidated = new stdClass();

        $validator = FacadesValidator::make($this->input(), [
            'equipments.*' => [
                new EquipmentsRule(['equipment_type_id', 'serial_number']),
            ],
            'equipments.*.equipment_type_id' => [
                'required',
                'integer',
                'exists:equipment_types,id',
            ],
            'equipments.*.serial_number' => [
                'required_if:equipments.*.equipment_type_id.exists:,equipment_types.id',
                function ($attribute, $value, $fail) {
                    $index = explode('.', $attribute)[1] ?? null;
        
                    $equipment = $this->input("equipments.$index");
        
                    $equipmentTypeId = $equipment['equipment_type_id'] ?? null;
                    
                    $serialNumberRule = new SerialNumber(equipmentTypeId:$equipmentTypeId);
                    $serialNumberRule->validate($attribute, $value, $fail);
                }
            ],
        ],
        [
            'equipments.*.equipment_type_id.required' => 'Необходимо указать тип оборудования',
            'equipments.*.equipment_type_id.exists' => 'Указанного типа оборудования не существует',
            'equipments.*.equipment_type_id.integer' => 'Тип оборудования был передан неправильно',
            'equipments.*.serial_number.required' => 'Cерийный номер обязателен',
            'equipments.*.serial_number.string' => 'Cерийный номер должен быть представлен строкой',
        ]);

        if ($validator->fails()) {
            foreach ($validator->errors()->messages() as $key => $message) {
                $splitted = explode('.', $key);
                $index = $splitted[1] ?? $key;
                if (!property_exists($this->postErrors, $index)) {
                    $this->postErrors->$index = [];
                }
                if (is_array($message)) {
                    $this->postErrors->$index = array_merge($this->postErrors->$index, $message);
                } else {
                    $this->postErrors->$index[] = $message;
                }
            }
        }
        
        foreach ($this->input('equipments') as $key => $value) {
            if (!property_exists($this->postErrors, $key)) {
                $this->postValidated->$key = $value;
            }
        }
    }

    public function result()
    {
        return [
            'errors' => $this->postErrors,
            'success' => $this->postValidated,
        ];
    }
}