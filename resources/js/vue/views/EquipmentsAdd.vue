<template>
    <div class="m-3">
        <div class="container">
            <div class="d-flex justify-content-center mb-4">
                <h2 class="page-title">Добавление оборудования</h2>
            </div>

            <button class="btn btn-outline-primary mb-3" @click="toggleForm">
                {{ isEditing ? 'Редактировать оборудование' : 'Добавить оборудование' }}
            </button>

            <div v-if="showForm" class="card mt-2">
                <form @submit.prevent="isEditing ? updateEquipment() : addEquipment()">
                    <div class="card-body">
                        <h2 class="card-title text-center mb-4">
                            {{ isEditing ? 'Редактирование оборудования' : 'Добавить оборудование' }}
                        </h2>
                        <div class="mb-3 row">
                            <label class="col-sm-3 col-form-label">Примечание</label>
                            <div class="col-sm-9">
                                <textarea type="text" :class="{ 'is-invalid': form_errors.description }"
                                    v-model="equipment.description" class="form-control"
                                    placeholder="Описание"></textarea>
                                <span v-if="form_errors.description" class="invalid-feedback" role="alert">
                                    <strong>
                                        {{ form_errors.description }}
                                    </strong>
                                </span>
                            </div>
                        </div>
                        <div class="mb-3 row">
                            <label class="col-sm-3 col-form-label">Серийный номер</label>
                            <div class="col-sm-9">
                                <input type="text" :class="{ 'is-invalid': form_errors.serial_number }"
                                    v-model="equipment.serial_number" class="form-control" placeholder="Описание">
                                <span v-if="form_errors.serial_number" class="invalid-feedback" role="alert">
                                    <strong>
                                        {{ form_errors.serial_number }}
                                    </strong>
                                </span>
                            </div>
                        </div>
                        <div class="mb-3 row">
                            <div class="form-label">Тип оборудования</div>
                            <select :class="{ 'is-invalid': form_errors.equipment_type_id }"
                                v-model="equipment.equipment_type_id" class="form-select">
                                <option v-for="type in equipment_types" :key="type.id" :value="type.id">{{ type.name + ' (' + type.mask + ')' }}</option>
                            </select>
                            <span v-if="form_errors.equipment_type_id" class="invalid-feedback" role="alert">
                                <strong>
                                    {{ form_errors.equipment_type_id }}
                                </strong>
                            </span>
                        </div>
                        <div class="mb-1">
                            <button type="submit" class="btn btn-outline-primary w-100">
                                {{ isEditing ? 'Сохранить изменения' : 'Добавить в список' }}
                            </button>
                        </div>
                    </div>
                </form>
            </div>

            <div v-if="form.length" class="mt-4">
                <div class="card">
                    <div class="card-header border-0">
                        <div class="card-title">Временная таблица оборудования</div>
                    </div>
                    <div class="card-table table-responsive"></div>
                    <table class="table table-vcenter">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Описание</th>
                                <th>Тип оборудования</th>
                                <th>Серийный номер</th>
                                <th v-if="Object.values(form).some(item => 'errors' in item)">
                                    Статус отправки
                                </th>
                                <th>Действия</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="(item, index) in form" :key="index">
                                <td>{{ index + 1 }}</td>
                                <td>{{ item.description }}</td>
                                <td>{{ item.equipment_type_id }}</td>
                                <td>{{ item.serial_number }}</td>
                                <td>
                                    <div v-if="item.errors && Array.isArray(item.errors)">
                                        <span class="badge bg-danger me-1"></span>
                                        {{ item.errors.join('. ') }}
                                    </div>
                                </td>
                                <td>
                                    <div class="btn-list flex-nowrap">
                                        <div class="dropdown">
                                            <button class="btn dropdown-toggle align-text-top"
                                                data-bs-toggle="dropdown">
                                                Действия
                                            </button>
                                            <div class="dropdown-menu dropdown-menu-end">
                                                <button @click="editEquipment(index)" class="dropdown-item">
                                                    Редактировать
                                                </button>
                                                <button @click="removeEquipment(index)" class="dropdown-item">
                                                    Удалить
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="mt-4">
                    <button class="btn btn-success" @click="submitEquipments">Отправить список на сервер</button>
                </div>
            </div>

            <div v-if="!!Object.keys(AllSuccess).length" class="mt-4">
                <div class="card">
                    <div class="card-header border-0">
                        <div class="card-title">Успешно созданные записи</div>
                    </div>
                    <div class="card-table table-responsive"></div>
                    <table class="table table-vcenter">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Тип оборудования</th>
                                <th>Серийный номер</th>
                                <th>Примечания</th>
                                <th>Создан</th>
                                <th>Обновлен</th>
                                <th class="w-1"></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="(record, index) in AllSuccess" :key="index">
                                <td class="w-1">
                                    {{ record.id }}
                                </td>
                                <td class="td-truncate">
                                    {{ record.equipment_type.name + ' (ID: ' + record.equipment_type.id + ', маска серийного номера: ' + record.equipment_type.mask + ') ' }}
                                </td>
                                <td class="text-nowrap text-secondary">
                                    {{ record.serial_number }}
                                </td>
                                <td class="text-nowrap text-secondary">
                                    {{ record.description }}
                                </td>
                                <td class="text-nowrap">
                                    {{ new Date(record.created_at).toLocaleString() }}
                                </td>
                                <td class="text-nowrap">
                                    {{ new Date(record.updated_at).toLocaleString() }}
                                </td>
                                <td>
                                    <div class="btn-list flex-nowrap">
                                        <div class="dropdown">
                                            <button class="btn dropdown-toggle align-text-top"
                                                data-bs-toggle="dropdown">
                                                Действия
                                            </button>
                                            <div class="dropdown-menu dropdown-menu-end">
                                                <router-link
                                                    :to="{ name: 'equipments_edit', params: { id: record.id } }"
                                                    class="dropdown-item">Редактировать</router-link>
                                                <button @click="deleteEquipment(record.id)" class="dropdown-item">
                                                    Удалить
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</template>


<script>
import { ref } from 'vue';
import apiClient from '../api';

export default {
    name: 'EquipmentsAdd',
    data() {
        return {
            showForm: false,
            isEditing: false,
            editingIndex: null,
            equipment: {
                equipment_type_id: 1,
                serial_number: '',
                description: ''
            },
            submitErrors: {},
            submitSuccess: {},
            AllSuccess: {},
            form_errors: {},
            equipment_types: [],
            form: []
        };
    },
    methods: {
        toggleForm() {
            this.showForm = !this.showForm;
            if (!this.showForm) {
                this.clearForm();
                this.isEditing = false;
                this.editingIndex = null;
            }
        },
        addEquipment() {
            if (this.equipment.serial_number && this.equipment.equipment_type_id) {
                this.form.push({ ...this.equipment });
                this.clearForm();
                this.showForm = false;
            } else {
                this.checkFormErrors();
            }
        },
        editEquipment(index) {
            this.showForm = true;
            this.isEditing = true;
            this.editingIndex = index;
            this.equipment = { ...this.form[index] };
        },
        updateEquipment() {
            if (this.equipment.serial_number && this.equipment.equipment_type_id) {
                this.form.splice(this.editingIndex, 1, { ...this.equipment });
                this.isEditing = false;
                this.editingIndex = null;
                this.clearForm();
                this.showForm = false;
            } else {
                this.checkFormErrors();
            }
        },
        removeEquipment(index) {
            this.form.splice(index, 1);
            this.submitSuccess = {};
            this.submitErrors = {};
        },
        clearErrors() {
            Object.entries(this.form).forEach(([key, value]) => {
                if ('errors' in value) {
                    delete value.errors;
                }
            });
        },
        async deleteEquipment(id) {
            this.errors = {};
            try {
                const response = await apiClient.delete(
                    '/equipment/' + id,
                    { vueComponentInstance: ref(this) }
                ).then(
                    (response) => {
                        delete this.AllSuccess[id];
                    }
                ).catch(
                    (error) => {
                        console.log("Ошибка была обработана: ", error);
                    }
                );
            } catch (error) {
                console.error("Ошибка при получении оборудования:", error);
            }
        },
        async submitEquipments() {
            this.submitErrors = {};
            this.submitSuccess = {};
            this.clearErrors();
            try {
                const response = await apiClient.post(
                    '/equipment',
                    { equipments: this.form },
                    { errors: ref(this.submitErrors), success: ref(this.submitSuccess) }
                ).then(
                    (response) => {
                        this.form = [];
                    }
                ).catch(
                    (error) => {
                        console.log("Ошибка была обработана: ", error);
                    }
                );
                for (const [key, value] of Object.entries(this.submitErrors)) {
                    this.form[key].errors = value;
                }
                this.submitErrors = {};
                for (const [key, value] of Object.entries(this.submitSuccess)) {
                    this.AllSuccess[value.id] = value;
                    this.removeEquipment(key);
                }
                this.submitSuccess = {};
            } catch (error) {
                console.log('Ошибка при отправке списка на сервер:', error);
            }
        },
        clearForm() {
            this.equipment = {
                equipment_type_id: this.equipment_types[0]?.id || 1,
                serial_number: '',
                description: ''
            };
            this.form_errors = {};
        },
        checkFormErrors() {
            if (!this.equipment.equipment_type_id) this.form_errors.equipment_type_id = 'Обязательное поле';
            if (!this.equipment.serial_number) this.form_errors.serial_number = 'Обязательное поле';
        },
    },
    async mounted() {
        this.errors = {};
        this.success = {};
        try {
            const response = await apiClient.get(
                '/equipment-type/without-pagination',
                {},
                { vueComponentInstance: ref(this) }
            ).then(
                (response) => {
                    this.equipment_types = response.data;
                    this.equipment.equipment_type_id = this.equipment_types[0].id || 1;
                }
            ).catch(
                (error) => {
                    console.log("Ошибка была обработана: ", error);
                }
            );
        } catch (error) {
            console.error("Ошибка при получении типов оборудования:", error);
        }
    }
};
</script>
