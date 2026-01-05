const { body, param, validationResult } = require('express-validator');

// Validation rules for Project
const validateProject = [
  body('id').notEmpty().withMessage('ID is required'),
  body('code').notEmpty().withMessage('Code is required'),
  body('name').notEmpty().withMessage('Name is required'),
  body('customer').notEmpty().withMessage('Customer is required'),
  body('startDate').isISO8601().withMessage('Start date must be a valid date'),
  body('deadline').isISO8601().withMessage('Deadline must be a valid date'),
  body('status').isIn(['PLANNED', 'IN_PROGRESS', 'COMPLETED', 'ON_HOLD']).withMessage('Status must be one of: PLANNED, IN_PROGRESS, COMPLETED, ON_HOLD'),
  body('progress').isInt({ min: 0, max: 100 }).withMessage('Progress must be an integer between 0 and 100'),
  body('qtyPerUnit').isInt({ min: 0 }).withMessage('Qty per unit must be a non-negative integer'),
  body('procurementQty').isInt({ min: 0 }).withMessage('Procurement qty must be a non-negative integer'),
  body('totalQty').isInt({ min: 0 }).withMessage('Total qty must be a non-negative integer'),
  body('unit').notEmpty().withMessage('Unit is required'),
  body('isLocked').isBoolean().withMessage('Is locked must be a boolean')
];

// Validation rules for Material
const validateMaterial = [
  body('id').notEmpty().withMessage('ID is required'),
  body('code').notEmpty().withMessage('Code is required'),
  body('name').notEmpty().withMessage('Name is required'),
  body('unit').notEmpty().withMessage('Unit is required'),
  body('currentStock').isInt({ min: 0 }).withMessage('Current stock must be a non-negative integer'),
  body('safetyStock').isInt({ min: 0 }).withMessage('Safety stock must be a non-negative integer'),
  body('pricePerUnit').isFloat({ min: 0 }).withMessage('Price per unit must be a non-negative number'),
  body('category').isIn(['RAW', 'FINISHING', 'HARDWARE']).withMessage('Category must be one of: RAW, FINISHING, HARDWARE')
];

// Validation rules for Machine
const validateMachine = [
  body('id').notEmpty().withMessage('ID is required'),
  body('code').notEmpty().withMessage('Code is required'),
  body('name').notEmpty().withMessage('Name is required'),
  body('type').isIn(['POTONG', 'PLONG', 'PRESS', 'LAS', 'PHOSPHATING', 'CAT', 'PACKING']).withMessage('Type must be one of: POTONG, PLONG, PRESS, LAS, PHOSPHATING, CAT, PACKING'),
  body('capacityPerHour').isInt({ min: 0 }).withMessage('Capacity per hour must be a non-negative integer'),
  body('status').isIn(['IDLE', 'RUNNING', 'MAINTENANCE', 'OFFLINE', 'DOWNTIME']).withMessage('Status must be one of: IDLE, RUNNING, MAINTENANCE, OFFLINE, DOWNTIME'),
  body('personnel').isArray().withMessage('Personnel must be an array'),
  body('isMaintenance').isBoolean().withMessage('Is maintenance must be a boolean')
];

// Validation rules for Project Item
const validateProjectItem = [
  body('id').notEmpty().withMessage('ID is required'),
  body('projectId').notEmpty().withMessage('Project ID is required'),
  body('name').notEmpty().withMessage('Name is required'),
  body('qtySet').isInt({ min: 0 }).withMessage('Qty set must be a non-negative integer'),
  body('quantity').isInt({ min: 0 }).withMessage('Quantity must be a non-negative integer'),
  body('unit').notEmpty().withMessage('Unit is required'),
  body('isBomLocked').isBoolean().withMessage('Is BOM locked must be a boolean'),
  body('isWorkflowLocked').isBoolean().withMessage('Is workflow locked must be a boolean'),
  body('flowType').isIn(['OLD', 'NEW']).withMessage('Flow type must be one of: OLD, NEW'),
  body('warehouseQty').isInt({ min: 0 }).withMessage('Warehouse qty must be a non-negative integer'),
  body('shippedQty').isInt({ min: 0 }).withMessage('Shipped qty must be a non-negative integer'),
  body('assemblyStats').isObject().withMessage('Assembly stats must be an object')
];

// Validation rules for Task
const validateTask = [
  body('id').notEmpty().withMessage('ID is required'),
  body('projectId').notEmpty().withMessage('Project ID is required'),
  body('projectName').notEmpty().withMessage('Project name is required'),
  body('itemId').notEmpty().withMessage('Item ID is required'),
  body('itemName').notEmpty().withMessage('Item name is required'),
  body('step').isIn(['POTONG', 'PLONG', 'PRESS', 'LAS', 'PHOSPHATING', 'CAT', 'PACKING']).withMessage('Step must be one of: POTONG, PLONG, PRESS, LAS, PHOSPHATING, CAT, PACKING'),
  body('targetQty').isInt({ min: 0 }).withMessage('Target qty must be a non-negative integer'),
  body('completedQty').isInt({ min: 0 }).withMessage('Completed qty must be a non-negative integer'),
  body('defectQty').isInt({ min: 0 }).withMessage('Defect qty must be a non-negative integer'),
  body('status').isIn(['PENDING', 'IN_PROGRESS', 'PAUSED', 'COMPLETED', 'DOWNTIME']).withMessage('Status must be one of: PENDING, IN_PROGRESS, PAUSED, COMPLETED, DOWNTIME'),
  body('totalDowntimeMinutes').isInt({ min: 0 }).withMessage('Total downtime minutes must be a non-negative integer')
];

// Validation rules for User creation
const validateUser = [
  body('id').notEmpty().withMessage('ID is required'),
  body('name').notEmpty().withMessage('Name is required'),
  body('username').notEmpty().withMessage('Username is required'),
  body('password').notEmpty().withMessage('Password is required'),
  body('role').isIn(['ADMIN', 'OPERATOR', 'MANAGER']).withMessage('Role must be one of: ADMIN, OPERATOR, MANAGER'),
  body('permissions').isObject().withMessage('Permissions must be an object')
];

// Validation rules for User update
const validateUserUpdate = [
  body('id').optional().notEmpty().withMessage('ID cannot be empty if provided'),
  body('name').optional().notEmpty().withMessage('Name cannot be empty if provided'),
  body('username').optional().notEmpty().withMessage('Username cannot be empty if provided'),
  body('password').optional().notEmpty().withMessage('Password cannot be empty if provided'),
  body('role').optional().isIn(['ADMIN', 'OPERATOR', 'MANAGER']).withMessage('Role must be one of: ADMIN, OPERATOR, MANAGER'),
  body('permissions').optional().isObject().withMessage('Permissions must be an object')
];

// Validation rules for Production Log
const validateProductionLog = [
  body('id').notEmpty().withMessage('ID is required'),
  body('taskId').notEmpty().withMessage('Task ID is required'),
  body('itemId').notEmpty().withMessage('Item ID is required'),
  body('projectId').notEmpty().withMessage('Project ID is required'),
  body('step').isIn(['POTONG', 'PLONG', 'PRESS', 'LAS', 'PHOSPHATING', 'CAT', 'PACKING']).withMessage('Step must be one of: POTONG, PLONG, PRESS, LAS, PHOSPHATING, CAT, PACKING'),
  body('shift').isIn(['SHIFT_1', 'SHIFT_2', 'SHIFT_3']).withMessage('Shift must be one of: SHIFT_1, SHIFT_2, SHIFT_3'),
  body('goodQty').isInt({ min: 0 }).withMessage('Good qty must be a non-negative integer'),
  body('defectQty').isInt({ min: 0 }).withMessage('Defect qty must be a non-negative integer'),
  body('operator').notEmpty().withMessage('Operator is required'),
  body('type').isIn(['OUTPUT', 'DOWNTIME_START', 'DOWNTIME_END', 'WAREHOUSE_ENTRY']).withMessage('Type must be one of: OUTPUT, DOWNTIME_START, DOWNTIME_END, WAREHOUSE_ENTRY')
];

// Validation rules for Supplier
const validateSupplier = [
  body('id').notEmpty().withMessage('ID is required'),
  body('name').notEmpty().withMessage('Name is required')
];

// Validation rules for RFQ
const validateRFQ = [
  body('id').notEmpty().withMessage('ID is required'),
  body('code').notEmpty().withMessage('Code is required'),
  body('date').isISO8601().withMessage('Date must be a valid date'),
  body('status').isIn(['DRAFT', 'PO_CREATED']).withMessage('Status must be one of: DRAFT, PO_CREATED')
];

// Validation rules for Purchase Order
const validatePurchaseOrder = [
  body('id').notEmpty().withMessage('ID is required'),
  body('code').notEmpty().withMessage('Code is required'),
  body('date').isISO8601().withMessage('Date must be a valid date'),
  body('supplierId').notEmpty().withMessage('Supplier ID is required'),
  body('status').isIn(['OPEN', 'RECEIVED']).withMessage('Status must be one of: OPEN, RECEIVED'),
  body('grandTotal').isFloat({ min: 0 }).withMessage('Grand total must be a non-negative number')
];

// Validation rules for Receiving Goods
const validateReceivingGoods = [
  body('id').notEmpty().withMessage('ID is required'),
  body('code').notEmpty().withMessage('Code is required'),
  body('date').isISO8601().withMessage('Date must be a valid date'),
  body('poId').notEmpty().withMessage('PO ID is required')
];

// Validation rules for Delivery Order
const validateDeliveryOrder = [
  body('id').notEmpty().withMessage('ID is required'),
  body('code').notEmpty().withMessage('Code is required'),
  body('date').isISO8601().withMessage('Date must be a valid date'),
  body('customer').notEmpty().withMessage('Customer is required'),
  body('status').isIn(['DRAFT', 'VALIDATED']).withMessage('Status must be one of: DRAFT, VALIDATED')
];

// Validation rules for Sub-assembly
const validateSubAssembly = [
  body('id').notEmpty().withMessage('ID is required'),
  body('itemId').notEmpty().withMessage('Item ID is required'),
  body('name').notEmpty().withMessage('Name is required'),
  body('qtyPerParent').isInt({ min: 0 }).withMessage('Qty per parent must be a non-negative integer'),
  body('totalNeeded').isInt({ min: 0 }).withMessage('Total needed must be a non-negative integer'),
  body('completedQty').isInt({ min: 0 }).withMessage('Completed qty must be a non-negative integer'),
  body('totalProduced').isInt({ min: 0 }).withMessage('Total produced must be a non-negative integer'),
  body('consumedQty').isInt({ min: 0 }).withMessage('Consumed qty must be a non-negative integer'),
  body('materialId').notEmpty().withMessage('Material ID is required'),
  body('processes').optional().isArray().withMessage('Processes must be an array'),
  body('stepStats').optional().isObject().withMessage('Step stats must be an object'),
  body('isLocked').optional().isBoolean().withMessage('Is locked must be a boolean')
];

// Validation rules for Sub-assembly locking
const validateSubAssemblyLock = [
  body('isLocked').isBoolean().withMessage('Is locked must be a boolean').notEmpty().withMessage('isLocked field is required')
];

// Validation rules for BOM Item
const validateBomItem = [
  body('id').notEmpty().withMessage('ID is required'),
  body('itemId').notEmpty().withMessage('Item ID is required'),
  body('materialId').notEmpty().withMessage('Material ID is required'),
  body('quantityPerUnit').isInt({ min: 0 }).withMessage('Quantity per unit must be a non-negative integer'),
  body('totalRequired').isInt({ min: 0 }).withMessage('Total required must be a non-negative integer'),
  body('allocated').isInt({ min: 0 }).withMessage('Allocated must be a non-negative integer'),
  body('realized').isInt({ min: 0 }).withMessage('Realized must be a non-negative integer')
];

// Validation rules for Item Step Config
const validateItemStepConfig = [
  body('id').notEmpty().withMessage('ID is required'),
  body('itemId').notEmpty().withMessage('Item ID is required'),
  body('step').isIn(['POTONG', 'PLONG', 'PRESS', 'LAS', 'PHOSPHATING', 'CAT', 'PACKING']).withMessage('Step must be one of: POTONG, PLONG, PRESS, LAS, PHOSPHATING, CAT, PACKING'),
  body('sequence').isInt({ min: 0 }).withMessage('Sequence must be a non-negative integer')
];

// Validation rules for Machine Allocation
const validateMachineAllocation = [
  body('id').notEmpty().withMessage('ID is required'),
  body('stepConfigId').notEmpty().withMessage('Step config ID is required'),
  body('targetQty').isInt({ min: 0 }).withMessage('Target qty must be a non-negative integer')
];

module.exports = {
  validateProject,
  validateMaterial,
  validateMachine,
  validateProjectItem,
  validateTask,
  validateUser,
  validateUserUpdate,
  validateProductionLog,
  validateSupplier,
  validateRFQ,
  validatePurchaseOrder,
  validateReceivingGoods,
  validateDeliveryOrder,
  validateSubAssembly,
  validateSubAssemblyLock,
  validateBomItem,
  validateItemStepConfig,
  validateMachineAllocation
};