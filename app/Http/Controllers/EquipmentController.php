<?php

namespace App\Http\Controllers;

use App\Http\Resources\EquipmentResource;
use App\Models\Equipment;
use Illuminate\Http\Request;
use App\Http\Requests\EquipmentArrayRequest;
use App\Http\Requests\EquipmentSearchRequest;
use App\Http\Requests\EquipmentUpdateRequest;

use App\Services\EquipmentService;

class EquipmentController
{
    private $equipmentService;

    /**
     * Создает экземпляр контроллера
     * 
     * @param  \App\Services\EquipmentService  $equipmentService
     * 
     */
    public function __construct(EquipmentService $equipmentService)
    {
        $this->equipmentService = $equipmentService;
    }

    /**
     * Отображает список оборудования
     * 
     * @param  \App\Http\Requests\EquipmentSearchRequest  $request
     * 
     * @return \Illuminate\Pagination\LengthAwarePaginator | \App\Http\Resources\EquipmentCollection | \Illuminate\Http\Response
     */
    public function index(EquipmentSearchRequest $request)
    {
        $equipments =  $this->equipmentService->index($request->validated());
        
        if (!$equipments) {
            return response()->json('Ошибка при получении оборудования', 500);
        }

        return $equipments;
    }

    /**
     * Создает записи об оборудовании в бд
     * 
     * @param  \App\Http\Requests\EquipmentArrayRequest  $request
     * 
     * @return \Illuminate\Http\Response
     */
    public function store(EquipmentArrayRequest $request)
    {
        $validationResult = $request->result();
        $result = $this->equipmentService->create($validationResult);

        if (!$result || !array_key_exists('errors', $result)) {
            return response()->json((object) $result, 200);
        }

        return response()->json((object) $result, 500);
    }

    /**
     * Отображает запись об оборудовании из бд
     * 
     * @param  int  $id
     * 
     * @return \Illuminate\Http\Response
     */
    public function show(Equipment $equipment)
    {
        return response()->json(new EquipmentResource($equipment), 200);
    }

    /**
     * Обновляет запись об оборудовании в бд
     * 
     * @param  \App\Http\Requests\EquipmentUpdateRequest  $request
     * @param  int  $id
     * 
     * @return \Illuminate\Http\Response
     */
    public function update(EquipmentUpdateRequest $request, Equipment $equipment)
    {
        $equipment = $this->equipmentService->update($equipment, $request->validated());
        
        return response()->json(new EquipmentResource($equipment), 200);
    }

    /**
     * Удаляет запись об оборудовании из бд
     * 
     * @param  int  $id
     * 
     * @return \Illuminate\Http\Response
     */
    public function destroy(Equipment $equipment)
    {
        $result = $equipment->delete();
        
        return response()->json('Запись данного оборудования была удалена', 200);
    }
}
