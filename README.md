## ТЗ выполнено с использованием Laravel, Vite, Vue, MariaDB (MySQL), Docker, Docker-Compose. Это мой первый проект с применением Vue

## Используемые таблицы в бд
- equipments (id, equipment_type_id, serial_number, description, created_at, updated_at)
- equipment_types (id, equipment_type_name, serial_number_mask, created_at, updated_at)

## Реализованные методы API в Laravel и их параметры. ? означает опциональность параметра. При передаче атрибутов и значений для поиска поиск идет не по полному совпадению. Поиск по q (в Vue-решении идет только по полям, обозначенным в ТЗ). Обновление идет только по тем полям, которые допустимы для сущности. Остальные параметры игнорируются.
| Метод                   | Тип метода | Path-параметры | Параметры запроса                                           | Body-параметры                                                                       |
| :---------------------- | :--------- |:--------------:|:-----------------------------------------------------------:|:------------------------------------------------------------------------------------:|
| api/equipment           | GET        |                | q? : Str, serial_number? : Str, perPage? : Int, атрибуты, совпадающие по названиям с колонками таблицы, относящейся к этой : Str сущности              |                                                                                      |
| api/equipment/(id)      | GET        | id             |                                                             |                                                                                      |
| api/equipment           | POST       |                |                                                             | equipments : Array (equipment_type_id : Int, serial_number : Str, description : Str) |
| api/equipment/(id)      | PUT        | id             |                                                             | equipment_type_id? : Int, serial_number? : Str, description? : Str                   |
| api/equipment/(id)      | DELETE     | id             |                                                             |                                                                                      |
|                         |            |                |                                                             |                                                                                      |
| api/equipment-type      | GET        |                | q? : Str, serial_number? : Str, perPage? : Str, атрибуты, совпадающие по названиям с колонками таблицы, относящейся к этой сущности : Str              |                                                                                      |
| api/equipment-type      | POST       |                |                                                             | equipment_type_name : Str, serial_number_mask : Str                                                                                      |
| api/equipment-type/(id) | DELETE     | id             |                                                             |                                                                                      |

## Некоторые моменты
- PHPDocs написаны для контроллеров и сервисов
- Все оговоренные в тз запросы обрабатываются посредством Form request, но проход по содержимому массива в запросе для создания оборудования пачками реализуется кастомным валидатором уже после базовой обработки Form request

## Чтобы поднять контейнеры Docker для работы API (в системе нужен docker и docker-compose), нужно:
- Клонировать репозиторий.
- Выдать права на все файлы для обычного пользователя и его группы.
- Зайти в папку проекта.
- Прописать в консоли: `make build`. Подождать.
- Прописать в консоли: `make up`. Подождать.
- Gользоваться решением по адресу localhost.