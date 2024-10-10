<?php

namespace App\Http\Controllers;

use App\Http\Resources\EquipmentTypeResource;
use App\Models\EquipmentType;
use Illuminate\Http\Request;
use App\Http\Requests\EquipmentTypeRequest;
use App\Http\Requests\EquipmentTypeSearchRequest;

use App\Services\EquipmentTypeService;

class EquipmentTypeController
{
    private $equipmentTypeService;

    /**
     * Создает экземпляр контроллера
     * 
     * @param  \App\Services\EquipmentTypeService  $equipmentTypeService
     * 
     */
    public function __construct(EquipmentTypeService $equipmentTypeService)
    {
        $this->equipmentTypeService = $equipmentTypeService;
    }
    /**
     * Возвращает список типов оборудования
     * 
     * @param  \App\Http\Requests\EquipmentTypeSearchRequest  $request
     * 
     * @return \Illuminate\Pagination\LengthAwarePaginator | \App\Http\Resources\EquipmentTypeCollection | \Illuminate\Http\Response
     */
    public function index(EquipmentTypeSearchRequest $request)
    {
        $equipmentTypes =  $this->equipmentTypeService->index($request->validated());

        if (!$equipmentTypes) {
            return response()->json('Ошибка при получении типов оборудования', 500);
        }
        return $equipmentTypes;
    }

    public function indexwp(Request $request)
    {
        $equipmentTypes =  $this->equipmentTypeService->indexwp();

        if (!$equipmentTypes) {
            return response()->json('Ошибка при получении типов оборудования', 500);
        }
        return $equipmentTypes;
    }

    /**
     * Создает запись о типе оборудования в бд
     * 
     * @param  \App\Http\Requests\EquipmentTypeRequest  $request
     * 
     * @return \Illuminate\Http\Response
     */
    public function store(EquipmentTypeRequest $request)
    {
        $created = $this->equipmentTypeService->create($request->validated());
        
        if (!$created) {
            return response()->json('Тип оборудования не был создан', 500);
        }
        return response()->json($created, 200);
    }

    public function show(EquipmentType $equipment_type)
    {
        return new EquipmentTypeResource($equipment_type);
    }

    /**
     * Удаляет запись об оборудовании из бд
     * 
     * @param  int  $id
     * 
     * @return \Illuminate\Http\Response
     */
    public function destroy(EquipmentType $equipment_type)
    {
        $result = $equipment_type->delete();
        return response()->json('Запись была удалена', 200);
    }
}
