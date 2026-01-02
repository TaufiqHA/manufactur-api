# Receiving Good API Documentation

This document provides comprehensive documentation for the Receiving Good API endpoints.

## Base URL
All API endpoints are accessible under the base URL: `/api`

## Authentication
All endpoints require authentication using Sanctum tokens. Include the token in the Authorization header:
```
Authorization: Bearer {token}
```

## Endpoints

### 1. Get All Receiving Goods
**GET** `/api/receiving-goods`

Retrieve a paginated list of all receiving goods.

#### Headers
- `Authorization: Bearer {token}`

#### Query Parameters
- `page` (optional): Page number for pagination (default: 1)
- `per_page` (optional): Number of items per page (default: 10)

#### Response
- **200 OK**: Successfully retrieved the list of receiving goods
```json
{
  "current_page": 1,
  "data": [
    {
      "id": 1,
      "code": "RG-ABC123",
      "date": "2023-12-01",
      "po_id": 1,
      "created_at": "2023-12-01T10:00:00.000000Z",
      "updated_at": "2023-12-01T10:00:00.000000Z",
      "purchase_order": {
        "id": 1,
        "code": "PO-001",
        "date": "2023-11-15T00:00:00.000000Z",
        "supplier_id": 1,
        "rfq_id": 1,
        "description": "Sample Purchase Order",
        "status": "OPEN",
        "grand_total": "1250.00",
        "created_at": "2023-11-15T09:00:00.000000Z",
        "updated_at": "2023-11-15T09:00:00.000000Z",
        "supplier": {
          "id": 1,
          "name": "Supplier Name",
          "contact_person": "Contact Person",
          "phone": "123456789",
          "email": "supplier@example.com",
          "address": "Supplier Address",
          "created_at": "2023-11-01T08:00:00.000000Z",
          "updated_at": "2023-11-01T08:00:00.000000Z"
        }
      }
    }
  ],
  "first_page_url": "http://localhost:8000/api/receiving-goods?page=1",
  "from": 1,
  "last_page": 1,
  "last_page_url": "http://localhost:8000/api/receiving-goods?page=1",
  "links": [
    {
      "url": null,
      "label": "&laquo; Previous",
      "page": null,
      "active": false
    },
    {
      "url": "http://localhost:8000/api/receiving-goods?page=1",
      "label": "1",
      "page": 1,
      "active": true
    },
    {
      "url": null,
      "label": "Next &raquo;",
      "page": null,
      "active": false
    }
  ],
  "next_page_url": null,
  "path": "http://localhost:8000/api/receiving-goods",
  "per_page": 10,
  "prev_page_url": null,
  "to": 1,
  "total": 1
}
```

### 2. Get Single Receiving Good
**GET** `/api/receiving-goods/{id}`

Retrieve a specific receiving good by its ID.

#### Path Parameters
- `id` (required): The ID of the receiving good

#### Headers
- `Authorization: Bearer {token}`

#### Response
- **200 OK**: Successfully retrieved the receiving good
```json
{
  "id": 1,
  "code": "RG-ABC123",
  "date": "2023-12-01",
  "po_id": 1,
  "created_at": "2023-12-01T10:00:00.000000Z",
  "updated_at": "2023-12-01T10:00:00.000000Z",
  "purchase_order": {
    "id": 1,
    "code": "PO-001",
    "date": "2023-11-15T00:00:00.000000Z",
    "supplier_id": 1,
    "rfq_id": 1,
    "description": "Sample Purchase Order",
    "status": "OPEN",
    "grand_total": "1250.00",
    "created_at": "2023-11-15T09:00:00.000000Z",
    "updated_at": "2023-11-15T09:00:00.000000Z",
    "supplier": {
      "id": 1,
      "name": "Supplier Name",
      "contact_person": "Contact Person",
      "phone": "123456789",
      "email": "supplier@example.com",
      "address": "Supplier Address",
      "created_at": "2023-11-01T08:00:00.000000Z",
      "updated_at": "2023-11-01T08:00:00.000000Z"
    }
  }
}
```
- **404 Not Found**: Receiving good not found
- **401 Unauthorized**: Invalid or missing authentication token

### 3. Create Receiving Good
**POST** `/api/receiving-goods`

Create a new receiving good.

#### Headers
- `Authorization: Bearer {token}`
- `Content-Type: application/json`

#### Request Body
```json
{
  "code": "RG-NEW123",
  "date": "2023-12-15",
  "po_id": 1
}
```

#### Fields
- `code` (required, string): The code for the receiving good (max 255 characters)
- `date` (required, date): The date of the receiving good
- `po_id` (required, integer): The ID of the associated purchase order

#### Response
- **201 Created**: Successfully created the receiving good
```json
{
  "success": true,
  "message": "Receiving Good created successfully.",
  "data": {
    "id": 2,
    "code": "RG-NEW123",
    "date": "2023-12-15",
    "po_id": 1,
    "created_at": "2023-12-15T10:00:00.000000Z",
    "updated_at": "2023-12-15T10:00:00.000000Z"
  }
}
```
- **422 Unprocessable Entity**: Validation errors
```json
{
  "success": false,
  "message": "Validation failed",
  "errors": {
    "field_name": [
      "error_message"
    ]
  }
}
```
- **401 Unauthorized**: Invalid or missing authentication token

### 4. Update Receiving Good
**PUT** `/api/receiving-goods/{id}` or **PATCH** `/api/receiving-goods/{id}`

Update an existing receiving good.

#### Path Parameters
- `id` (required): The ID of the receiving good to update

#### Headers
- `Authorization: Bearer {token}`
- `Content-Type: application/json`

#### Request Body
```json
{
  "code": "RG-UPDATED123",
  "date": "2023-12-20",
  "po_id": 2
}
```

#### Fields
- `code` (required, string): The code for the receiving good (max 255 characters)
- `date` (required, date): The date of the receiving good
- `po_id` (required, integer): The ID of the associated purchase order

#### Response
- **200 OK**: Successfully updated the receiving good
```json
{
  "success": true,
  "message": "Receiving Good updated successfully.",
  "data": {
    "id": 1,
    "code": "RG-UPDATED123",
    "date": "2023-12-20",
    "po_id": 2,
    "created_at": "2023-12-15T10:00:00.000000Z",
    "updated_at": "2023-12-20T10:00:00.000000Z"
  }
}
```
- **422 Unprocessable Entity**: Validation errors
```json
{
  "success": false,
  "message": "Validation failed",
  "errors": {
    "field_name": [
      "error_message"
    ]
  }
}
```
- **404 Not Found**: Receiving good not found
- **401 Unauthorized**: Invalid or missing authentication token

### 5. Delete Receiving Good
**DELETE** `/api/receiving-goods/{id}`

Delete a receiving good.

#### Path Parameters
- `id` (required): The ID of the receiving good to delete

#### Headers
- `Authorization: Bearer {token}`

#### Response
- **200 OK**: Successfully deleted the receiving good
```json
{
  "success": true,
  "message": "Receiving Good deleted successfully."
}
```
- **404 Not Found**: Receiving good not found
- **401 Unauthorized**: Invalid or missing authentication token

## Error Responses

### 401 Unauthorized
```json
{
  "message": "Unauthenticated."
}
```

### 404 Not Found
```json
{
  "message": "The requested resource was not found."
}
```

### 500 Internal Server Error
```json
{
  "success": false,
  "message": "Failed to retrieve receiving goods",
  "error": "Error message details"
}
```

## Data Models

### Receiving Good Object
| Field | Type | Description |
|-------|------|-------------|
| id | integer | Unique identifier for the receiving good |
| code | string | Code for the receiving good |
| date | string | Date of the receiving good (format: YYYY-MM-DD) |
| po_id | integer | ID of the associated purchase order |
| created_at | string | Creation timestamp |
| updated_at | string | Last update timestamp |

### Purchase Order Object
| Field | Type | Description |
|-------|------|-------------|
| id | integer | Unique identifier for the purchase order |
| code | string | Code for the purchase order |
| date | string | Date of the purchase order |
| supplier_id | integer | ID of the associated supplier |
| rfq_id | integer | ID of the associated RFQ |
| description | string | Description of the purchase order |
| status | string | Status of the purchase order |
| grand_total | string | Grand total of the purchase order |
| created_at | string | Creation timestamp |
| updated_at | string | Last update timestamp |

### Supplier Object
| Field | Type | Description |
|-------|------|-------------|
| id | integer | Unique identifier for the supplier |
| name | string | Name of the supplier |
| contact_person | string | Contact person at the supplier |
| phone | string | Phone number of the supplier |
| email | string | Email address of the supplier |
| address | string | Address of the supplier |
| created_at | string | Creation timestamp |
| updated_at | string | Last update timestamp |