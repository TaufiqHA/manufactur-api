# Step Stock Balance API Documentation

## Overview
The Step Stock Balance API provides endpoints to manage stock balances at different production steps. This resource tracks the quantities of materials available at each step of the production process, including how much has been produced, consumed, and what's currently available.

## Base URL
```
http://your-api-domain.com/api
```

## Authentication
All endpoints require authentication using Laravel Sanctum tokens. Include the token in the Authorization header:

```
Authorization: Bearer {your-api-token}
```

## Endpoints

### 1. List All Step Stock Balances
**GET** `/step-stock-balances`

#### Description
Retrieve a list of all step stock balances with their associated relationships.

#### Headers
```
Authorization: Bearer {your-api-token}
Accept: application/json
```

#### Response
- **200 OK** - Successfully retrieved the list
```json
[
  {
    "id": 1,
    "item_id": 1,
    "sub_assembly_id": 1,
    "process_step_id": 1,
    "total_produced": 100,
    "total_consumed": 50,
    "available_qty": 50,
    "created_at": "2023-01-01T00:00:00.000000Z",
    "updated_at": "2023-01-01T00:00:00.000000Z",
    "projectItem": {
      "id": 1,
      "name": "Sample Project Item",
      ...
    },
    "subAssembly": {
      "id": 1,
      "name": "Sample Sub Assembly",
      ...
    },
    "itemStepConfig": {
      "id": 1,
      "step_name": "Sample Step",
      ...
    }
  }
]
```

### 2. Get a Specific Step Stock Balance
**GET** `/step-stock-balances/{id}`

#### Description
Retrieve a specific step stock balance by its ID.

#### Path Parameters
- `id` (integer): The ID of the step stock balance to retrieve

#### Headers
```
Authorization: Bearer {your-api-token}
Accept: application/json
```

#### Response
- **200 OK** - Successfully retrieved the step stock balance
```json
{
  "id": 1,
  "item_id": 1,
  "sub_assembly_id": 1,
  "process_step_id": 1,
  "total_produced": 100,
  "total_consumed": 50,
  "available_qty": 50,
  "created_at": "2023-01-01T00:00:00.000000Z",
  "updated_at": "2023-01-01T00:00:00.000000Z",
  "projectItem": {
    "id": 1,
    "name": "Sample Project Item",
    ...
  },
  "subAssembly": {
    "id": 1,
    "name": "Sample Sub Assembly",
    ...
  },
  "itemStepConfig": {
    "id": 1,
    "step_name": "Sample Step",
    ...
  }
}
```
- **404 Not Found** - If the step stock balance doesn't exist

### 3. Create a New Step Stock Balance
**POST** `/step-stock-balances`

#### Description
Create a new step stock balance.

#### Headers
```
Authorization: Bearer {your-api-token}
Content-Type: application/json
Accept: application/json
```

#### Request Body
```json
{
  "item_id": 1,
  "sub_assembly_id": 1,
  "process_step_id": 1,
  "total_produced": 100,
  "total_consumed": 50,
  "available_qty": 50
}
```

#### Parameters
- `item_id` (integer, required): ID of the project item
- `sub_assembly_id` (integer, optional): ID of the sub assembly (can be null)
- `process_step_id` (integer, required): ID of the process step configuration
- `total_produced` (integer, required): Total quantity produced (must be >= 0)
- `total_consumed` (integer, required): Total quantity consumed (must be >= 0)
- `available_qty` (integer, required): Available quantity (must be >= 0)

#### Response
- **201 Created** - Successfully created the step stock balance
```json
{
  "id": 2,
  "item_id": 1,
  "sub_assembly_id": 1,
  "process_step_id": 1,
  "total_produced": 100,
  "total_consumed": 50,
  "available_qty": 50,
  "created_at": "2023-01-01T00:00:00.000000Z",
  "updated_at": "2023-01-01T00:00:00.000000Z",
  "projectItem": {
    "id": 1,
    "name": "Sample Project Item",
    ...
  },
  "subAssembly": {
    "id": 1,
    "name": "Sample Sub Assembly",
    ...
  },
  "itemStepConfig": {
    "id": 1,
    "step_name": "Sample Step",
    ...
  }
}
```
- **422 Unprocessable Entity** - If validation fails

### 4. Update an Existing Step Stock Balance
**PUT/PATCH** `/step-stock-balances/{id}`

#### Description
Update an existing step stock balance.

#### Path Parameters
- `id` (integer): The ID of the step stock balance to update

#### Headers
```
Authorization: Bearer {your-api-token}
Content-Type: application/json
Accept: application/json
```

#### Request Body
```json
{
  "total_produced": 150,
  "total_consumed": 75,
  "available_qty": 75
}
```

#### Parameters
- `item_id` (integer, optional): ID of the project item
- `sub_assembly_id` (integer, optional): ID of the sub assembly (can be null)
- `process_step_id` (integer, optional): ID of the process step configuration
- `total_produced` (integer, optional): Total quantity produced (must be >= 0)
- `total_consumed` (integer, optional): Total quantity consumed (must be >= 0)
- `available_qty` (integer, optional): Available quantity (must be >= 0)

#### Response
- **200 OK** - Successfully updated the step stock balance
```json
{
  "id": 1,
  "item_id": 1,
  "sub_assembly_id": 1,
  "process_step_id": 1,
  "total_produced": 150,
  "total_consumed": 75,
  "available_qty": 75,
  "created_at": "2023-01-01T00:00:00.000000Z",
  "updated_at": "2023-01-02T00:00:00.000000Z",
  "projectItem": {
    "id": 1,
    "name": "Sample Project Item",
    ...
  },
  "subAssembly": {
    "id": 1,
    "name": "Sample Sub Assembly",
    ...
  },
  "itemStepConfig": {
    "id": 1,
    "step_name": "Sample Step",
    ...
  }
}
```
- **404 Not Found** - If the step stock balance doesn't exist
- **422 Unprocessable Entity** - If validation fails

### 5. Delete a Step Stock Balance
**DELETE** `/step-stock-balances/{id}`

#### Description
Delete a specific step stock balance.

#### Path Parameters
- `id` (integer): The ID of the step stock balance to delete

#### Headers
```
Authorization: Bearer {your-api-token}
Accept: application/json
```

#### Response
- **204 No Content** - Successfully deleted the step stock balance
- **404 Not Found** - If the step stock balance doesn't exist

## Data Model

### Step Stock Balance Object
| Field | Type | Description |
|-------|------|-------------|
| id | integer | Unique identifier for the step stock balance |
| item_id | integer | Foreign key to the project item |
| sub_assembly_id | integer \| null | Foreign key to the sub assembly (optional) |
| process_step_id | integer | Foreign key to the item step configuration |
| total_produced | integer | Total quantity produced at this step |
| total_consumed | integer | Total quantity consumed at this step |
| available_qty | integer | Available quantity at this step |
| created_at | datetime | Timestamp when the record was created |
| updated_at | datetime | Timestamp when the record was last updated |

## Error Responses

### Validation Error Response (422)
```json
{
  "message": "The item id field is required.",
  "errors": {
    "item_id": [
      "The item id field is required."
    ],
    "process_step_id": [
      "The process step id field is required."
    ]
  }
}
```

### Not Found Error Response (404)
```json
{
  "message": "Resource not found."
}
```

## Relationships
- `projectItem`: The project item associated with this step stock balance
- `subAssembly`: The sub assembly associated with this step stock balance (nullable)
- `itemStepConfig`: The process step configuration for this step stock balance