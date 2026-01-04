# Database Schema for GondolaFlow MES

## Tables

### 1. projects
- id: VARCHAR(255) PRIMARY KEY
- code: VARCHAR(255) NOT NULL
- name: VARCHAR(255) NOT NULL
- customer: VARCHAR(255) NOT NULL
- startDate: DATE NOT NULL
- deadline: DATE NOT NULL
- status: VARCHAR(50) NOT NULL CHECK (status IN ('PLANNED', 'IN_PROGRESS', 'COMPLETED', 'ON_HOLD'))
- progress: INTEGER NOT NULL DEFAULT 0
- qtyPerUnit: INTEGER NOT NULL
- procurementQty: INTEGER NOT NULL
- totalQty: INTEGER NOT NULL
- unit: VARCHAR(50) NOT NULL
- isLocked: BOOLEAN NOT NULL DEFAULT FALSE

### 2. materials
- id: VARCHAR(255) PRIMARY KEY
- code: VARCHAR(255) NOT NULL
- name: VARCHAR(255) NOT NULL
- unit: VARCHAR(50) NOT NULL
- currentStock: INTEGER NOT NULL DEFAULT 0
- safetyStock: INTEGER NOT NULL DEFAULT 0
- pricePerUnit: DECIMAL(10,2) NOT NULL
- category: VARCHAR(50) NOT NULL CHECK (category IN ('RAW', 'FINISHING', 'HARDWARE'))

### 3. unit_masters
- id: VARCHAR(255) PRIMARY KEY
- name: VARCHAR(255) NOT NULL

### 4. machines
- id: VARCHAR(255) PRIMARY KEY
- code: VARCHAR(255) NOT NULL
- name: VARCHAR(255) NOT NULL
- type: VARCHAR(50) NOT NULL CHECK (type IN ('POTONG', 'PLONG', 'PRESS', 'LAS', 'PHOSPHATING', 'CAT', 'PACKING'))
- capacityPerHour: INTEGER NOT NULL
- status: VARCHAR(50) NOT NULL CHECK (status IN ('IDLE', 'RUNNING', 'MAINTENANCE', 'OFFLINE', 'DOWNTIME'))
- personnel: JSONB NOT NULL DEFAULT '[]'  -- Array of personnel
- isMaintenance: BOOLEAN NOT NULL DEFAULT FALSE

### 5. project_items
- id: VARCHAR(255) PRIMARY KEY
- projectId: VARCHAR(255) NOT NULL REFERENCES projects(id)
- name: VARCHAR(255) NOT NULL
- dimensions: VARCHAR(255)
- thickness: VARCHAR(255)
- qtySet: INTEGER NOT NULL
- quantity: INTEGER NOT NULL
- unit: VARCHAR(50) NOT NULL
- isBomLocked: BOOLEAN NOT NULL DEFAULT FALSE
- isWorkflowLocked: BOOLEAN NOT NULL DEFAULT FALSE
- flowType: VARCHAR(10) NOT NULL CHECK (flowType IN ('OLD', 'NEW')) DEFAULT 'NEW'
- warehouseQty: INTEGER NOT NULL DEFAULT 0
- shippedQty: INTEGER NOT NULL DEFAULT 0
- assemblyStats: JSONB NOT NULL DEFAULT '{}'  -- Partial<Record<ProcessStep, { produced: number, available: number }>>

### 6. sub_assemblies
- id: VARCHAR(255) PRIMARY KEY
- itemId: VARCHAR(255) NOT NULL REFERENCES project_items(id)
- name: VARCHAR(255) NOT NULL
- qtyPerParent: INTEGER NOT NULL
- totalNeeded: INTEGER NOT NULL
- completedQty: INTEGER NOT NULL DEFAULT 0
- totalProduced: INTEGER NOT NULL DEFAULT 0
- consumedQty: INTEGER NOT NULL DEFAULT 0
- materialId: VARCHAR(255) NOT NULL REFERENCES materials(id)
- processes: TEXT[] NOT NULL  -- Array of ProcessStep
- stepStats: JSONB NOT NULL DEFAULT '{}'  -- Partial<Record<ProcessStep, { produced: number, available: number }>>
- isLocked: BOOLEAN NOT NULL DEFAULT FALSE

### 7. bom_items
- id: VARCHAR(255) PRIMARY KEY
- itemId: VARCHAR(255) NOT NULL REFERENCES project_items(id)
- materialId: VARCHAR(255) NOT NULL REFERENCES materials(id)
- quantityPerUnit: INTEGER NOT NULL
- totalRequired: INTEGER NOT NULL
- allocated: INTEGER NOT NULL DEFAULT 0
- realized: INTEGER NOT NULL DEFAULT 0

### 8. item_step_configs
- id: VARCHAR(255) PRIMARY KEY
- itemId: VARCHAR(255) NOT NULL REFERENCES project_items(id)
- step: VARCHAR(50) NOT NULL CHECK (step IN ('POTONG', 'PLONG', 'PRESS', 'LAS', 'PHOSPHATING', 'CAT', 'PACKING'))
- sequence: INTEGER NOT NULL

### 9. machine_allocations
- id: VARCHAR(255) PRIMARY KEY
- stepConfigId: VARCHAR(255) NOT NULL REFERENCES item_step_configs(id)
- machineId: VARCHAR(255) REFERENCES machines(id)
- targetQty: INTEGER NOT NULL
- note: TEXT

### 10. tasks
- id: VARCHAR(255) PRIMARY KEY
- projectId: VARCHAR(255) NOT NULL REFERENCES projects(id)
- projectName: VARCHAR(255) NOT NULL
- itemId: VARCHAR(255) NOT NULL REFERENCES project_items(id)
- itemName: VARCHAR(255) NOT NULL
- subAssemblyId: VARCHAR(255) REFERENCES sub_assemblies(id)
- subAssemblyName: VARCHAR(255)
- step: VARCHAR(50) NOT NULL CHECK (step IN ('POTONG', 'PLONG', 'PRESS', 'LAS', 'PHOSPHATING', 'CAT', 'PACKING'))
- machineId: VARCHAR(255) REFERENCES machines(id)
- targetQty: INTEGER NOT NULL
- dailyTarget: INTEGER
- completedQty: INTEGER NOT NULL DEFAULT 0
- defectQty: INTEGER NOT NULL DEFAULT 0
- status: VARCHAR(50) NOT NULL CHECK (status IN ('PENDING', 'IN_PROGRESS', 'PAUSED', 'COMPLETED', 'DOWNTIME'))
- note: TEXT
- totalDowntimeMinutes: INTEGER NOT NULL DEFAULT 0

### 11. production_logs
- id: VARCHAR(255) PRIMARY KEY
- taskId: VARCHAR(255) NOT NULL REFERENCES tasks(id)
- machineId: VARCHAR(255) REFERENCES machines(id)
- itemId: VARCHAR(255) NOT NULL REFERENCES project_items(id)
- subAssemblyId: VARCHAR(255) REFERENCES sub_assemblies(id)
- projectId: VARCHAR(255) NOT NULL REFERENCES projects(id)
- step: VARCHAR(50) NOT NULL CHECK (step IN ('POTONG', 'PLONG', 'PRESS', 'LAS', 'PHOSPHATING', 'CAT', 'PACKING'))
- shift: VARCHAR(50) NOT NULL CHECK (shift IN ('SHIFT_1', 'SHIFT_2', 'SHIFT_3'))
- goodQty: INTEGER NOT NULL
- defectQty: INTEGER NOT NULL
- operator: VARCHAR(255) NOT NULL
- timestamp: TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
- type: VARCHAR(50) NOT NULL CHECK (type IN ('OUTPUT', 'DOWNTIME_START', 'DOWNTIME_END', 'WAREHOUSE_ENTRY'))

### 12. users
- id: VARCHAR(255) PRIMARY KEY
- name: VARCHAR(255) NOT NULL
- username: VARCHAR(255) NOT NULL UNIQUE
- role: VARCHAR(50) NOT NULL CHECK (role IN ('ADMIN', 'OPERATOR', 'MANAGER'))
- permissions: JSONB NOT NULL DEFAULT '{}'  -- PermissionMap structure

### 13. suppliers
- id: VARCHAR(255) PRIMARY KEY
- name: VARCHAR(255) NOT NULL
- address: TEXT
- contact: VARCHAR(255)

### 14. rfqs
- id: VARCHAR(255) PRIMARY KEY
- code: VARCHAR(255) NOT NULL
- date: DATE NOT NULL
- description: TEXT
- items: JSONB NOT NULL DEFAULT '[]'  -- Array of ProcurementItem
- status: VARCHAR(50) NOT NULL CHECK (status IN ('DRAFT', 'PO_CREATED')) DEFAULT 'DRAFT'

### 15. purchase_orders
- id: VARCHAR(255) PRIMARY KEY
- code: VARCHAR(255) NOT NULL
- date: DATE NOT NULL
- supplierId: VARCHAR(255) NOT NULL REFERENCES suppliers(id)
- description: TEXT
- items: JSONB NOT NULL DEFAULT '[]'  -- Array of ProcurementItem
- status: VARCHAR(50) NOT NULL CHECK (status IN ('OPEN', 'RECEIVED')) DEFAULT 'OPEN'
- grandTotal: DECIMAL(12,2) NOT NULL DEFAULT 0

### 16. receiving_goods
- id: VARCHAR(255) PRIMARY KEY
- code: VARCHAR(255) NOT NULL
- date: DATE NOT NULL
- poId: VARCHAR(255) NOT NULL REFERENCES purchase_orders(id)
- items: JSONB NOT NULL DEFAULT '[]'  -- Array of ProcurementItem

### 17. delivery_order_items
- id: VARCHAR(255) PRIMARY KEY
- deliveryOrderId: VARCHAR(255) NOT NULL REFERENCES delivery_orders(id)
- projectId: VARCHAR(255) NOT NULL REFERENCES projects(id)
- projectName: VARCHAR(255) NOT NULL
- itemId: VARCHAR(255) NOT NULL REFERENCES project_items(id)
- itemName: VARCHAR(255) NOT NULL
- qty: INTEGER NOT NULL
- unit: VARCHAR(50) NOT NULL

### 18. delivery_orders
- id: VARCHAR(255) PRIMARY KEY
- code: VARCHAR(255) NOT NULL
- date: DATE NOT NULL
- customer: VARCHAR(255) NOT NULL
- address: TEXT
- driverName: VARCHAR(255)
- vehiclePlate: VARCHAR(255)
- status: VARCHAR(50) NOT NULL CHECK (status IN ('DRAFT', 'VALIDATED')) DEFAULT 'DRAFT'