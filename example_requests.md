# GondolaFlow MES API - Example Requests & Responses

## Base URL
`http://localhost:5000/api`

## Authentication
Most endpoints require authentication. Use the `/login` endpoint to get a JWT token, then include it in the Authorization header:
```
Authorization: Bearer <token>
```

## Common Response Format

### Success Response
```json
{
  "data": [/* resource data */],
  "pagination": {
    "page": 1,
    "limit": 10,
    "total": 100,
    "totalPages": 10,
    "hasNext": true,
    "hasPrev": false
  }
}
```

### Error Response
```json
{
  "error": "Error message",
  "details": "Additional details (only in development)"
}
```

## API Endpoints

### Projects

#### GET /projects
**Description:** Get all projects with pagination
**Headers:** 
- Authorization: Bearer <token>
**Query Parameters:**
- page: Page number (default: 1)
- limit: Items per page (default: 10)

**Response:**
```json
{
  "data": [
    {
      "id": "p1",
      "code": "PRJ-GONDOLA",
      "name": "Project Gondola Toko",
      "customer": "Toko Maju Jaya",
      "startDate": "2024-01-01",
      "deadline": "2024-12-31",
      "status": "IN_PROGRESS",
      "progress": 50,
      "qtyPerUnit": 3,
      "procurementQty": 12155,
      "totalQty": 36465,
      "unit": "Set",
      "isLocked": true
    }
  ],
  "pagination": {
    "page": 1,
    "limit": 10,
    "total": 1,
    "totalPages": 1,
    "hasNext": false,
    "hasPrev": false
  }
}
```

#### GET /projects/:id
**Description:** Get a specific project by ID
**Headers:** 
- Authorization: Bearer <token>

**Response:**
```json
{
  "id": "p1",
  "code": "PRJ-GONDOLA",
  "name": "Project Gondola Toko",
  "customer": "Toko Maju Jaya",
  "startDate": "2024-01-01",
  "deadline": "2024-12-31",
  "status": "IN_PROGRESS",
  "progress": 50,
  "qtyPerUnit": 3,
  "procurementQty": 12155,
  "totalQty": 36465,
  "unit": "Set",
  "isLocked": true
}
```

#### POST /projects
**Description:** Create a new project
**Headers:** 
- Authorization: Bearer <token>
- Content-Type: application/json

**Request Body:**
```json
{
  "id": "p2",
  "code": "PRJ-NEW",
  "name": "New Project",
  "customer": "New Customer",
  "startDate": "2024-06-01",
  "deadline": "2024-12-31",
  "status": "PLANNED",
  "progress": 0,
  "qtyPerUnit": 5,
  "procurementQty": 1000,
  "totalQty": 5000,
  "unit": "Set",
  "isLocked": false
}
```

**Response:**
```json
{
  "id": "p2",
  "code": "PRJ-NEW",
  "name": "New Project",
  "customer": "New Customer",
  "startDate": "2024-06-01",
  "deadline": "2024-12-31",
  "status": "PLANNED",
  "progress": 0,
  "qtyPerUnit": 5,
  "procurementQty": 1000,
  "totalQty": 5000,
  "unit": "Set",
  "isLocked": false
}
```

#### PUT /projects/:id
**Description:** Update an existing project
**Headers:** 
- Authorization: Bearer <token>
- Content-Type: application/json

**Request Body:**
```json
{
  "code": "PRJ-UPDATED",
  "name": "Updated Project",
  "customer": "Updated Customer",
  "startDate": "2024-06-01",
  "deadline": "2025-01-31",
  "status": "IN_PROGRESS",
  "progress": 25,
  "qtyPerUnit": 5,
  "procurementQty": 1250,
  "totalQty": 6250,
  "unit": "Set",
  "isLocked": false
}
```

**Response:**
```json
{
  "id": "p2",
  "code": "PRJ-UPDATED",
  "name": "Updated Project",
  "customer": "Updated Customer",
  "startDate": "2024-06-01",
  "deadline": "2025-01-31",
  "status": "IN_PROGRESS",
  "progress": 25,
  "qtyPerUnit": 5,
  "procurementQty": 1250,
  "totalQty": 6250,
  "unit": "Set",
  "isLocked": false
}
```

#### DELETE /projects/:id
**Description:** Delete a project
**Headers:** 
- Authorization: Bearer <token>

**Response:** 204 No Content

### Materials

#### GET /materials
**Description:** Get all materials with pagination
**Headers:** 
- Authorization: Bearer <token>
**Query Parameters:**
- page: Page number (default: 1)
- limit: Items per page (default: 10)

**Response:**
```json
{
  "data": [
    {
      "id": "m1",
      "code": "ST-SHEET-2MM",
      "name": "Steel Sheet 2mm",
      "unit": "Lembar",
      "currentStock": 450,
      "safetyStock": 100,
      "pricePerUnit": 50,
      "category": "RAW"
    }
  ],
  "pagination": {
    "page": 1,
    "limit": 10,
    "total": 1,
    "totalPages": 1,
    "hasNext": false,
    "hasPrev": false
  }
}
```

#### PUT /materials/:id/adjust-stock
**Description:** Adjust material stock
**Headers:** 
- Authorization: Bearer <token>
- Content-Type: application/json

**Request Body:**
```json
{
  "amount": 50
}
```

**Response:**
```json
{
  "id": "m1",
  "code": "ST-SHEET-2MM",
  "name": "Steel Sheet 2mm",
  "unit": "Lembar",
  "currentStock": 500,
  "safetyStock": 100,
  "pricePerUnit": 50,
  "category": "RAW"
}
```

### Tasks

#### GET /tasks
**Description:** Get all tasks with pagination
**Headers:** 
- Authorization: Bearer <token>
**Query Parameters:**
- page: Page number (default: 1)
- limit: Items per page (default: 10)

**Response:**
```json
{
  "data": [
    {
      "id": "task1",
      "projectId": "p1",
      "projectName": "Project Gondola Toko",
      "itemId": "i1",
      "itemName": "Tiang Gondola 2m",
      "subAssemblyId": "sa1",
      "subAssemblyName": "Sub Assembly 1",
      "step": "LAS",
      "machineId": "mac2",
      "targetQty": 100,
      "dailyTarget": 20,
      "completedQty": 50,
      "defectQty": 2,
      "status": "IN_PROGRESS",
      "note": "Quality check required",
      "totalDowntimeMinutes": 30
    }
  ],
  "pagination": {
    "page": 1,
    "limit": 10,
    "total": 1,
    "totalPages": 1,
    "hasNext": false,
    "hasPrev": false
  }
}
```

### Production Logs

#### POST /production-logs
**Description:** Create a new production log
**Headers:** 
- Authorization: Bearer <token>
- Content-Type: application/json

**Request Body:**
```json
{
  "id": "log1",
  "taskId": "task1",
  "machineId": "mac2",
  "itemId": "i1",
  "subAssemblyId": "sa1",
  "projectId": "p1",
  "step": "LAS",
  "shift": "SHIFT_1",
  "goodQty": 20,
  "defectQty": 1,
  "operator": "John Doe",
  "type": "OUTPUT"
}
```

**Response:**
```json
{
  "id": "log1",
  "taskId": "task1",
  "machineId": "mac2",
  "itemId": "i1",
  "subAssemblyId": "sa1",
  "projectId": "p1",
  "step": "LAS",
  "shift": "SHIFT_1",
  "goodQty": 20,
  "defectQty": 1,
  "operator": "John Doe",
  "timestamp": "2024-06-15T10:30:00.000Z",
  "type": "OUTPUT"
}
```

### Users

#### POST /login
**Description:** Authenticate user and get JWT token
**Headers:** 
- Content-Type: application/json

**Request Body:**
```json
{
  "username": "admin"
}
```

**Response:**
```json
{
  "token": "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9...",
  "user": {
    "id": "u1",
    "name": "Super Admin",
    "username": "admin",
    "role": "ADMIN",
    "permissions": {
      "PROJECTS": {
        "view": true,
        "create": true,
        "edit": true,
        "delete": true
      },
      "MATERIALS": {
        "view": true,
        "create": true,
        "edit": true,
        "delete": true
      }
    }
  }
}
```

### Delivery Orders

#### GET /delivery-orders/:id
**Description:** Get a delivery order with its items
**Headers:** 
- Authorization: Bearer <token>

**Response:**
```json
{
  "id": "do1",
  "code": "DO-001",
  "date": "2024-06-15",
  "customer": "Customer ABC",
  "address": "Jl. Example 123",
  "driverName": "Driver Name",
  "vehiclePlate": "B 1234 ABC",
  "items": [
    {
      "id": "doi1",
      "deliveryOrderId": "do1",
      "projectId": "p1",
      "projectName": "Project Gondola Toko",
      "itemId": "i1",
      "itemName": "Tiang Gondola 2m",
      "qty": 50,
      "unit": "Pcs"
    }
  ],
  "status": "DRAFT"
}
```

## Error Responses

### 400 Bad Request - Validation Error
```json
{
  "error": "Validation Error",
  "details": [
    {
      "value": "",
      "msg": "Name is required",
      "param": "name",
      "location": "body"
    }
  ]
}
```

### 401 Unauthorized
```json
{
  "error": "Unauthorized access"
}
```

### 404 Not Found
```json
{
  "error": "Project not found"
}
```

### 500 Internal Server Error
```json
{
  "error": "Internal Server Error"
}
```

## Common Data Types

### ProcessStep
One of: `POTONG`, `PLONG`, `PRESS`, `LAS`, `PHOSPHATING`, `CAT`, `PACKING`

### ProjectStatus
One of: `PLANNED`, `IN_PROGRESS`, `COMPLETED`, `ON_HOLD`

### MachineStatus
One of: `IDLE`, `RUNNING`, `MAINTENANCE`, `OFFLINE`, `DOWNTIME`

### TaskStatus
One of: `PENDING`, `IN_PROGRESS`, `PAUSED`, `COMPLETED`, `DOWNTIME`

### Shift
One of: `SHIFT_1`, `SHIFT_2`, `SHIFT_3`

### MaterialCategory
One of: `RAW`, `FINISHING`, `HARDWARE`

### UserRole
One of: `ADMIN`, `OPERATOR`, `MANAGER`

### DeliveryOrderStatus
One of: `DRAFT`, `VALIDATED`

### PurchaseOrderStatus
One of: `OPEN`, `RECEIVED`

### RFQStatus
One of: `DRAFT`, `PO_CREATED`