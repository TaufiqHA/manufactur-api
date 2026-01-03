# Stock Movement API Documentation

## Overview
The Stock Movement API provides endpoints for managing stock movements within the manufacturing system. Stock movements track the flow of materials and components between different stages of production, including production, consumption, and adjustments.

## Authentication
All endpoints require authentication using Laravel Sanctum tokens. Include the token in the Authorization header:

```
Authorization: Bearer {your-api-token}
```

## Base URL
```
/api/stock-movements
```

## Endpoints

### 1. Get All Stock Movements
**GET** `/api/stock-movements`

Retrieve a paginated list of all stock movements.

#### Query Parameters
- `item_id` (optional): Filter by item ID
- `sub_assembly_id` (optional): Filter by sub-assembly ID
- `movement_type` (optional): Filter by movement type (PRODUCTION, CONSUMPTION, ADJUSTMENT)
- `task_id` (optional): Filter by task ID
- `per_page` (optional): Number of records per page (default: 15)

#### Response
```json
{
  "data": [
    {
      "id": 1,
      "item_id": 1,
      "sub_assembly_id": 1,
      "source_step_id": 1,
      "target_step_id": 2,
      "task_id": 1,
      "created_by": 1,
      "quantity": 100,
      "good_qty": 95,
      "defect_qty": 5,
      "movement_type": "PRODUCTION",
      "shift": "SHIFT_1",
      "created_at": "2023-01-01T00:00:00.000000Z",
      "updated_at": "2023-01-01T00:00:00.000000Z",
      "item": {
        "id": 1,
        "project_id": 1,
        "name": "Sample Item",
        "dimensions": "10x10x10",
        "thickness": "5mm",
        "qty_set": 1,
        "quantity": 100,
        "unit": "pcs",
        "is_bom_locked": false,
        "is_workflow_locked": false,
        "flow_type": "NEW",
        "warehouse_qty": 0,
        "shipped_qty": 0,
        "created_at": "2023-01-01T00:00:00.000000Z",
        "updated_at": "2023-01-01T00:00:00.000000Z"
      },
      "subAssembly": {
        "id": 1,
        "item_id": 1,
        "name": "Sample Sub Assembly",
        "qty_per_parent": 1,
        "total_needed": 100,
        "completed_qty": 0,
        "total_produced": 0,
        "consumed_qty": 0,
        "material_id": null,
        "processes": [],
        "step_stats": null,
        "is_locked": false,
        "created_at": "2023-01-01T00:00:00.000000Z",
        "updated_at": "2023-01-01T00:00:00.000000Z"
      },
      "sourceStep": {
        "id": 1,
        "item_id": 1,
        "step": "Cutting",
        "sequence": 1,
        "created_at": "2023-01-01T00:00:00.000000Z",
        "updated_at": "2023-01-01T00:00:00.000000Z"
      },
      "targetStep": {
        "id": 2,
        "item_id": 1,
        "step": "Drilling",
        "sequence": 2,
        "created_at": "2023-01-01T00:00:00.000000Z",
        "updated_at": "2023-01-01T00:00:00.000000Z"
      },
      "task": {
        "id": 1,
        "project_id": 1,
        "project_name": "Sample Project",
        "item_id": 1,
        "item_name": "Sample Item",
        "sub_assembly_id": 1,
        "sub_assembly_name": "Sample Sub Assembly",
        "step": "Sample Process Step",
        "machine_id": 1,
        "target_qty": 100,
        "daily_target": 50,
        "completed_qty": 0,
        "defect_qty": 0,
        "status": "PENDING",
        "note": null,
        "total_downtime_minutes": 0,
        "created_at": "2023-01-01T00:00:00.000000Z",
        "updated_at": "2023-01-01T00:00:00.000000Z"
      }
    }
  ],
  "pagination": {
    "current_page": 1,
    "per_page": 15,
    "total": 10,
    "last_page": 1
  }
}
```

### 2. Get Single Stock Movement
**GET** `/api/stock-movements/{id}`

Retrieve a specific stock movement by ID.

#### Path Parameters
- `id`: The ID of the stock movement to retrieve

#### Response
```json
{
  "data": {
    "id": 1,
    "item_id": 1,
    "sub_assembly_id": 1,
    "source_step_id": 1,
    "target_step_id": 2,
    "task_id": 1,
    "created_by": 1,
    "quantity": 100,
    "good_qty": 95,
    "defect_qty": 5,
    "movement_type": "PRODUCTION",
    "shift": "SHIFT_1",
    "created_at": "2023-01-01T00:00:00.000000Z",
    "updated_at": "2023-01-01T00:00:00.000000Z",
    "item": { ... },
    "subAssembly": { ... },
    "sourceStep": { ... },
    "targetStep": { ... },
    "task": { ... }
  }
}
```

### 3. Create Stock Movement
**POST** `/api/stock-movements`

Create a new stock movement record.

#### Request Body
```json
{
  "item_id": 1,
  "sub_assembly_id": 1,
  "source_step_id": 1,
  "target_step_id": 2,
  "task_id": 1,
  "created_by": 1,
  "quantity": 100,
  "good_qty": 95,
  "defect_qty": 5,
  "movement_type": "PRODUCTION",
  "shift": "SHIFT_1"
}
```

#### Request Body Parameters
- `item_id` (required): ID of the project item
- `sub_assembly_id` (optional): ID of the sub assembly
- `source_step_id` (optional): ID of the source step (nullable)
- `target_step_id` (required): ID of the target step
- `task_id` (optional): ID of the task
- `created_by` (optional): ID of the user who created the movement
- `quantity` (required): Total quantity of the movement (integer, min: 1)
- `good_qty` (required): Quantity of good items (integer, min: 0)
- `defect_qty` (required): Quantity of defective items (integer, min: 0)
- `movement_type` (required): Type of movement (PRODUCTION, CONSUMPTION, ADJUSTMENT)
- `shift` (optional): Shift information (SHIFT_1, SHIFT_2, SHIFT_3)

#### Validation Rules
- `good_qty + defect_qty` must equal `quantity`
- All IDs must exist in their respective tables

#### Response
```json
{
  "message": "Stock movement created successfully",
  "data": {
    "id": 1,
    "item_id": 1,
    "sub_assembly_id": 1,
    "source_step_id": 1,
    "target_step_id": 2,
    "task_id": 1,
    "created_by": 1,
    "quantity": 100,
    "good_qty": 95,
    "defect_qty": 5,
    "movement_type": "PRODUCTION",
    "shift": "SHIFT_1",
    "created_at": "2023-01-01T00:00:00.000000Z",
    "updated_at": "2023-01-01T00:00:00.000000Z",
    "item": { ... },
    "subAssembly": { ... },
    "sourceStep": { ... },
    "targetStep": { ... },
    "task": { ... }
  }
}
```

### 4. Update Stock Movement
**PUT** `/api/stock-movements/{id}`

Update an existing stock movement record.

#### Path Parameters
- `id`: The ID of the stock movement to update

#### Request Body
```json
{
  "item_id": 1,
  "sub_assembly_id": 1,
  "source_step_id": 1,
  "target_step_id": 2,
  "task_id": 1,
  "created_by": 1,
  "quantity": 120,
  "good_qty": 115,
  "defect_qty": 5,
  "movement_type": "CONSUMPTION",
  "shift": "SHIFT_2"
}
```

#### Request Body Parameters
- `item_id` (optional): ID of the project item
- `sub_assembly_id` (optional): ID of the sub assembly
- `source_step_id` (optional): ID of the source step (nullable)
- `target_step_id` (optional): ID of the target step
- `task_id` (optional): ID of the task
- `created_by` (optional): ID of the user who created the movement
- `quantity` (optional): Total quantity of the movement (integer, min: 1)
- `good_qty` (optional): Quantity of good items (integer, min: 0)
- `defect_qty` (optional): Quantity of defective items (integer, min: 0)
- `movement_type` (optional): Type of movement (PRODUCTION, CONSUMPTION, ADJUSTMENT)
- `shift` (optional): Shift information (SHIFT_1, SHIFT_2, SHIFT_3)

#### Validation Rules
- `good_qty + defect_qty` must equal `quantity` (if both are provided)
- All IDs must exist in their respective tables

#### Response
```json
{
  "message": "Stock movement updated successfully",
  "data": {
    "id": 1,
    "item_id": 1,
    "sub_assembly_id": 1,
    "source_step_id": 1,
    "target_step_id": 2,
    "task_id": 1,
    "created_by": 1,
    "quantity": 120,
    "good_qty": 115,
    "defect_qty": 5,
    "movement_type": "CONSUMPTION",
    "shift": "SHIFT_2",
    "created_at": "2023-01-01T00:00:00.000000Z",
    "updated_at": "2023-01-01T00:00:00.000000Z",
    "item": { ... },
    "subAssembly": { ... },
    "sourceStep": { ... },
    "targetStep": { ... },
    "task": { ... }
  }
}
```

### 5. Delete Stock Movement
**DELETE** `/api/stock-movements/{id}`

Delete a specific stock movement by ID.

#### Path Parameters
- `id`: The ID of the stock movement to delete

#### Response
```json
{
  "message": "Stock movement deleted successfully"
}
```

## Data Models

### Stock Movement Object
| Field | Type | Description |
|-------|------|-------------|
| id | integer | Unique identifier for the stock movement |
| item_id | integer | ID of the project item |
| sub_assembly_id | integer or null | ID of the sub assembly (nullable) |
| source_step_id | integer or null | ID of the source step (nullable) |
| target_step_id | integer | ID of the target step |
| task_id | integer or null | ID of the task (nullable) |
| created_by | integer or null | ID of the user who created the movement (nullable) |
| quantity | integer | Total quantity of the movement |
| good_qty | integer | Quantity of good items |
| defect_qty | integer | Quantity of defective items |
| movement_type | string | Type of movement (PRODUCTION, CONSUMPTION, ADJUSTMENT) |
| shift | string or null | Shift information (SHIFT_1, SHIFT_2, SHIFT_3) |
| created_at | datetime | Creation timestamp |
| updated_at | datetime | Last update timestamp |

## Error Responses

### Validation Error
**Status Code:** 422 Unprocessable Entity

```json
{
  "errors": {
    "field_name": [
      "Error message 1",
      "Error message 2"
    ]
  }
}
```

### Unauthorized Error
**Status Code:** 401 Unauthorized

```json
{
  "message": "Unauthenticated."
}
```

## Movement Types
- `PRODUCTION`: Stock movement related to production activities
- `CONSUMPTION`: Stock movement related to consumption of materials
- `ADJUSTMENT`: Stock movement for inventory adjustments

## Shift Types
- `SHIFT_1`: First shift
- `SHIFT_2`: Second shift
- `SHIFT_3`: Third shift (if applicable)