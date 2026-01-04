const { run, query } = require('../config/db');

const createTables = async () => {
  try {
    // Create unit_masters table
    await run(`
      CREATE TABLE IF NOT EXISTS unit_masters (
        id TEXT PRIMARY KEY,
        name TEXT NOT NULL
      );
    `);

    // Create materials table
    await run(`
      CREATE TABLE IF NOT EXISTS materials (
        id TEXT PRIMARY KEY,
        code TEXT NOT NULL,
        name TEXT NOT NULL,
        unit TEXT NOT NULL,
        currentStock INTEGER NOT NULL DEFAULT 0,
        safetyStock INTEGER NOT NULL DEFAULT 0,
        pricePerUnit REAL NOT NULL,
        category TEXT NOT NULL CHECK (category IN ('RAW', 'FINISHING', 'HARDWARE'))
      );
    `);

    // Create suppliers table
    await run(`
      CREATE TABLE IF NOT EXISTS suppliers (
        id TEXT PRIMARY KEY,
        name TEXT NOT NULL,
        address TEXT,
        contact TEXT
      );
    `);

    // Create projects table
    await run(`
      CREATE TABLE IF NOT EXISTS projects (
        id TEXT PRIMARY KEY,
        code TEXT NOT NULL,
        name TEXT NOT NULL,
        customer TEXT NOT NULL,
        startDate TEXT NOT NULL,
        deadline TEXT NOT NULL,
        status TEXT NOT NULL CHECK (status IN ('PLANNED', 'IN_PROGRESS', 'COMPLETED', 'ON_HOLD')),
        progress INTEGER NOT NULL DEFAULT 0,
        qtyPerUnit INTEGER NOT NULL,
        procurementQty INTEGER NOT NULL,
        totalQty INTEGER NOT NULL,
        unit TEXT NOT NULL,
        isLocked INTEGER NOT NULL DEFAULT 0
      );
    `);

    // Create machines table
    await run(`
      CREATE TABLE IF NOT EXISTS machines (
        id TEXT PRIMARY KEY,
        code TEXT NOT NULL,
        name TEXT NOT NULL,
        type TEXT NOT NULL CHECK (type IN ('POTONG', 'PLONG', 'PRESS', 'LAS', 'PHOSPHATING', 'CAT', 'PACKING')),
        capacityPerHour INTEGER NOT NULL,
        status TEXT NOT NULL CHECK (status IN ('IDLE', 'RUNNING', 'MAINTENANCE', 'OFFLINE', 'DOWNTIME')),
        personnel TEXT NOT NULL DEFAULT '[]',
        isMaintenance INTEGER NOT NULL DEFAULT 0
      );
    `);

    // Create project_items table
    await run(`
      CREATE TABLE IF NOT EXISTS project_items (
        id TEXT PRIMARY KEY,
        projectId TEXT NOT NULL,
        name TEXT NOT NULL,
        dimensions TEXT,
        thickness TEXT,
        qtySet INTEGER NOT NULL,
        quantity INTEGER NOT NULL,
        unit TEXT NOT NULL,
        isBomLocked INTEGER NOT NULL DEFAULT 0,
        isWorkflowLocked INTEGER NOT NULL DEFAULT 0,
        flowType TEXT NOT NULL CHECK (flowType IN ('OLD', 'NEW')) DEFAULT 'NEW',
        warehouseQty INTEGER NOT NULL DEFAULT 0,
        shippedQty INTEGER NOT NULL DEFAULT 0,
        assemblyStats TEXT NOT NULL DEFAULT '{}',
        FOREIGN KEY (projectId) REFERENCES projects(id)
      );
    `);

    // Create sub_assemblies table
    await run(`
      CREATE TABLE IF NOT EXISTS sub_assemblies (
        id TEXT PRIMARY KEY,
        itemId TEXT NOT NULL,
        name TEXT NOT NULL,
        qtyPerParent INTEGER NOT NULL,
        totalNeeded INTEGER NOT NULL,
        completedQty INTEGER NOT NULL DEFAULT 0,
        totalProduced INTEGER NOT NULL DEFAULT 0,
        consumedQty INTEGER NOT NULL DEFAULT 0,
        materialId TEXT NOT NULL,
        processes TEXT NOT NULL,
        stepStats TEXT NOT NULL DEFAULT '{}',
        isLocked INTEGER NOT NULL DEFAULT 0,
        FOREIGN KEY (itemId) REFERENCES project_items(id),
        FOREIGN KEY (materialId) REFERENCES materials(id)
      );
    `);

    // Create bom_items table
    await run(`
      CREATE TABLE IF NOT EXISTS bom_items (
        id TEXT PRIMARY KEY,
        itemId TEXT NOT NULL,
        materialId TEXT NOT NULL,
        quantityPerUnit INTEGER NOT NULL,
        totalRequired INTEGER NOT NULL,
        allocated INTEGER NOT NULL DEFAULT 0,
        realized INTEGER NOT NULL DEFAULT 0,
        FOREIGN KEY (itemId) REFERENCES project_items(id),
        FOREIGN KEY (materialId) REFERENCES materials(id)
      );
    `);

    // Create item_step_configs table
    await run(`
      CREATE TABLE IF NOT EXISTS item_step_configs (
        id TEXT PRIMARY KEY,
        itemId TEXT NOT NULL,
        step TEXT NOT NULL CHECK (step IN ('POTONG', 'PLONG', 'PRESS', 'LAS', 'PHOSPHATING', 'CAT', 'PACKING')),
        sequence INTEGER NOT NULL,
        FOREIGN KEY (itemId) REFERENCES project_items(id)
      );
    `);

    // Create machine_allocations table
    await run(`
      CREATE TABLE IF NOT EXISTS machine_allocations (
        id TEXT PRIMARY KEY,
        stepConfigId TEXT NOT NULL,
        machineId TEXT,
        targetQty INTEGER NOT NULL,
        note TEXT,
        FOREIGN KEY (stepConfigId) REFERENCES item_step_configs(id),
        FOREIGN KEY (machineId) REFERENCES machines(id)
      );
    `);

    // Create tasks table
    await run(`
      CREATE TABLE IF NOT EXISTS tasks (
        id TEXT PRIMARY KEY,
        projectId TEXT NOT NULL,
        projectName TEXT NOT NULL,
        itemId TEXT NOT NULL,
        itemName TEXT NOT NULL,
        subAssemblyId TEXT,
        subAssemblyName TEXT,
        step TEXT NOT NULL CHECK (step IN ('POTONG', 'PLONG', 'PRESS', 'LAS', 'PHOSPHATING', 'CAT', 'PACKING')),
        machineId TEXT,
        targetQty INTEGER NOT NULL,
        dailyTarget INTEGER,
        completedQty INTEGER NOT NULL DEFAULT 0,
        defectQty INTEGER NOT NULL DEFAULT 0,
        status TEXT NOT NULL CHECK (status IN ('PENDING', 'IN_PROGRESS', 'PAUSED', 'COMPLETED', 'DOWNTIME')),
        note TEXT,
        totalDowntimeMinutes INTEGER NOT NULL DEFAULT 0,
        FOREIGN KEY (projectId) REFERENCES projects(id),
        FOREIGN KEY (itemId) REFERENCES project_items(id),
        FOREIGN KEY (subAssemblyId) REFERENCES sub_assemblies(id),
        FOREIGN KEY (machineId) REFERENCES machines(id)
      );
    `);

    // Create production_logs table
    await run(`
      CREATE TABLE IF NOT EXISTS production_logs (
        id TEXT PRIMARY KEY,
        taskId TEXT NOT NULL,
        machineId TEXT,
        itemId TEXT NOT NULL,
        subAssemblyId TEXT,
        projectId TEXT NOT NULL,
        step TEXT NOT NULL CHECK (step IN ('POTONG', 'PLONG', 'PRESS', 'LAS', 'PHOSPHATING', 'CAT', 'PACKING')),
        shift TEXT NOT NULL CHECK (shift IN ('SHIFT_1', 'SHIFT_2', 'SHIFT_3')),
        goodQty INTEGER NOT NULL,
        defectQty INTEGER NOT NULL,
        operator TEXT NOT NULL,
        timestamp TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP,
        type TEXT NOT NULL CHECK (type IN ('OUTPUT', 'DOWNTIME_START', 'DOWNTIME_END', 'WAREHOUSE_ENTRY')),
        FOREIGN KEY (taskId) REFERENCES tasks(id),
        FOREIGN KEY (machineId) REFERENCES machines(id),
        FOREIGN KEY (itemId) REFERENCES project_items(id),
        FOREIGN KEY (subAssemblyId) REFERENCES sub_assemblies(id),
        FOREIGN KEY (projectId) REFERENCES projects(id)
      );
    `);

    // Create users table
    await run(`
      CREATE TABLE IF NOT EXISTS users (
        id TEXT PRIMARY KEY,
        name TEXT NOT NULL,
        username TEXT NOT NULL UNIQUE,
        role TEXT NOT NULL CHECK (role IN ('ADMIN', 'OPERATOR', 'MANAGER')),
        permissions TEXT NOT NULL DEFAULT '{}',
        password TEXT NOT NULL
      );
    `);

    // Create rfqs table
    await run(`
      CREATE TABLE IF NOT EXISTS rfqs (
        id TEXT PRIMARY KEY,
        code TEXT NOT NULL,
        date TEXT NOT NULL,
        description TEXT,
        items TEXT NOT NULL DEFAULT '[]',
        status TEXT NOT NULL CHECK (status IN ('DRAFT', 'PO_CREATED')) DEFAULT 'DRAFT'
      );
    `);

    // Create purchase_orders table
    await run(`
      CREATE TABLE IF NOT EXISTS purchase_orders (
        id TEXT PRIMARY KEY,
        code TEXT NOT NULL,
        date TEXT NOT NULL,
        supplierId TEXT NOT NULL,
        description TEXT,
        items TEXT NOT NULL DEFAULT '[]',
        status TEXT NOT NULL CHECK (status IN ('OPEN', 'RECEIVED')) DEFAULT 'OPEN',
        grandTotal REAL NOT NULL DEFAULT 0,
        FOREIGN KEY (supplierId) REFERENCES suppliers(id)
      );
    `);

    // Create receiving_goods table
    await run(`
      CREATE TABLE IF NOT EXISTS receiving_goods (
        id TEXT PRIMARY KEY,
        code TEXT NOT NULL,
        date TEXT NOT NULL,
        poId TEXT NOT NULL,
        items TEXT NOT NULL DEFAULT '[]',
        FOREIGN KEY (poId) REFERENCES purchase_orders(id)
      );
    `);

    // Create delivery_orders table
    await run(`
      CREATE TABLE IF NOT EXISTS delivery_orders (
        id TEXT PRIMARY KEY,
        code TEXT NOT NULL,
        date TEXT NOT NULL,
        customer TEXT NOT NULL,
        address TEXT,
        driverName TEXT,
        vehiclePlate TEXT,
        status TEXT NOT NULL CHECK (status IN ('DRAFT', 'VALIDATED')) DEFAULT 'DRAFT'
      );
    `);

    // Create delivery_order_items table
    await run(`
      CREATE TABLE IF NOT EXISTS delivery_order_items (
        id TEXT PRIMARY KEY,
        deliveryOrderId TEXT NOT NULL,
        projectId TEXT NOT NULL,
        projectName TEXT NOT NULL,
        itemId TEXT NOT NULL,
        itemName TEXT NOT NULL,
        qty INTEGER NOT NULL,
        unit TEXT NOT NULL,
        FOREIGN KEY (deliveryOrderId) REFERENCES delivery_orders(id),
        FOREIGN KEY (projectId) REFERENCES projects(id),
        FOREIGN KEY (itemId) REFERENCES project_items(id)
      );
    `);

    console.log('All tables created successfully');
  } catch (error) {
    console.error('Error creating tables:', error);
    throw error;
  }
};

// Rollback function to drop all tables
const dropTables = async () => {
  try {
    // Get all table names
    const result = await query(`
      SELECT name FROM sqlite_master
      WHERE type='table'
      AND name NOT IN ('sqlite_sequence', 'applied_migrations')
    `);

    const tableNames = result.rows.map(row => row.name);

    // Drop tables in reverse order to handle foreign key constraints
    for (const tableName of tableNames.reverse()) {
      await run(`DROP TABLE IF EXISTS ${tableName}`);
      console.log(`Table ${tableName} dropped successfully`);
    }

    console.log('All tables dropped successfully');
  } catch (error) {
    console.error('Error dropping tables:', error);
    throw error;
  }
};

// Export both up and down functions for the migration system
module.exports = {
  up: createTables,
  down: dropTables,
  createTables
};