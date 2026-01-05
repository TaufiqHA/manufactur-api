const express = require('express');
const router = express.Router();

// Import controllers
const ProjectController = require('../controllers/ProjectController');
const MaterialController = require('../controllers/MaterialController');
const MachineController = require('../controllers/MachineController');
const ProjectItemController = require('../controllers/ProjectItemController');
const TaskController = require('../controllers/TaskController');
const UserController = require('../controllers/UserController');
const ProductionLogController = require('../controllers/ProductionLogController');
const SupplierController = require('../controllers/SupplierController');
const RFQController = require('../controllers/RFQController');
const PurchaseOrderController = require('../controllers/PurchaseOrderController');
const ReceivingGoodsController = require('../controllers/ReceivingGoodsController');
const DeliveryOrderController = require('../controllers/DeliveryOrderController');
const SubAssemblyController = require('../controllers/SubAssemblyController');
const BomItemController = require('../controllers/BomItemController');
const ItemStepConfigController = require('../controllers/ItemStepConfigController');
const MachineAllocationController = require('../controllers/MachineAllocationController');

// Import validation middleware
const {
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
} = require('../middleware/validation');

// Project routes
router.get('/projects', ProjectController.getAllProjects);
router.get('/projects/:id', ProjectController.getProjectById);
router.post('/projects', validateProject, ProjectController.createProject);
router.put('/projects/:id', validateProject, ProjectController.updateProject);
router.delete('/projects/:id', ProjectController.deleteProject);

// Material routes
router.get('/materials', MaterialController.getAllMaterials);
router.get('/materials/:id', MaterialController.getMaterialById);
router.post('/materials', validateMaterial, MaterialController.createMaterial);
router.put('/materials/:id', validateMaterial, MaterialController.updateMaterial);
router.delete('/materials/:id', MaterialController.deleteMaterial);
router.put('/materials/:id/adjust-stock', MaterialController.adjustStock);

// Machine routes
router.get('/machines', MachineController.getAllMachines);
router.get('/machines/:id', MachineController.getMachineById);
router.post('/machines', validateMachine, MachineController.createMachine);
router.put('/machines/:id', validateMachine, MachineController.updateMachine);
router.delete('/machines/:id', MachineController.deleteMachine);
router.put('/machines/:id/toggle-maintenance', MachineController.toggleMaintenance);

// Project Item routes
router.get('/project-items', ProjectItemController.getAllProjectItems);
router.get('/project-items/:id', ProjectItemController.getProjectItemById);
router.get('/project-items/project/:projectId', ProjectItemController.getProjectItemsByProjectId);
router.post('/project-items', validateProjectItem, ProjectItemController.createProjectItem);
router.put('/project-items/:id', validateProjectItem, ProjectItemController.updateProjectItem);
router.delete('/project-items/:id', ProjectItemController.deleteProjectItem);

// Task routes
router.get('/tasks', TaskController.getAllTasks);
router.get('/tasks/:id', TaskController.getTaskById);
router.get('/tasks/project/:projectId', TaskController.getTasksByProjectId);
router.get('/tasks/item/:itemId', TaskController.getTasksByItemId);
router.post('/tasks', validateTask, TaskController.createTask);
router.put('/tasks/:id', validateTask, TaskController.updateTask);
router.delete('/tasks/:id', TaskController.deleteTask);

// User routes
router.get('/users', UserController.getAllUsers);
router.get('/users/:id', UserController.getUserById);
router.get('/users/username/:username', UserController.getUserByUsername);
router.post('/users', validateUser, UserController.createUser);
router.put('/users/:id', validateUserUpdate, UserController.updateUser);
router.delete('/users/:id', UserController.deleteUser);
router.post('/login', UserController.login);

// Production Log routes
router.get('/production-logs', ProductionLogController.getAllProductionLogs);
router.get('/production-logs/:id', ProductionLogController.getProductionLogById);
router.get('/production-logs/task/:taskId', ProductionLogController.getProductionLogsByTaskId);
router.get('/production-logs/project/:projectId', ProductionLogController.getProductionLogsByProjectId);
router.post('/production-logs', validateProductionLog, ProductionLogController.createProductionLog);
router.put('/production-logs/:id', validateProductionLog, ProductionLogController.updateProductionLog);
router.delete('/production-logs/:id', ProductionLogController.deleteProductionLog);

// Supplier routes
router.get('/suppliers', SupplierController.getAllSuppliers);
router.get('/suppliers/:id', SupplierController.getSupplierById);
router.post('/suppliers', validateSupplier, SupplierController.createSupplier);
router.put('/suppliers/:id', validateSupplier, SupplierController.updateSupplier);
router.delete('/suppliers/:id', SupplierController.deleteSupplier);

// RFQ routes
router.get('/rfqs', RFQController.getAllRFQs);
router.get('/rfqs/:id', RFQController.getRFQById);
router.post('/rfqs', validateRFQ, RFQController.createRFQ);
router.put('/rfqs/:id', validateRFQ, RFQController.updateRFQ);
router.delete('/rfqs/:id', RFQController.deleteRFQ);

// Purchase Order routes
router.get('/purchase-orders', PurchaseOrderController.getAllPurchaseOrders);
router.get('/purchase-orders/:id', PurchaseOrderController.getPurchaseOrderById);
router.post('/purchase-orders', validatePurchaseOrder, PurchaseOrderController.createPurchaseOrder);
router.put('/purchase-orders/:id', validatePurchaseOrder, PurchaseOrderController.updatePurchaseOrder);
router.delete('/purchase-orders/:id', PurchaseOrderController.deletePurchaseOrder);

// Receiving Goods routes
router.get('/receiving-goods', ReceivingGoodsController.getAllReceivingGoods);
router.get('/receiving-goods/:id', ReceivingGoodsController.getReceivingGoodsById);
router.post('/receiving-goods', validateReceivingGoods, ReceivingGoodsController.createReceivingGoods);
router.put('/receiving-goods/:id', validateReceivingGoods, ReceivingGoodsController.updateReceivingGoods);
router.delete('/receiving-goods/:id', ReceivingGoodsController.deleteReceivingGoods);

// Delivery Order routes
router.get('/delivery-orders', DeliveryOrderController.getAllDeliveryOrders);
router.get('/delivery-orders/:id', DeliveryOrderController.getDeliveryOrderById);
router.post('/delivery-orders', validateDeliveryOrder, DeliveryOrderController.createDeliveryOrder);
router.put('/delivery-orders/:id', validateDeliveryOrder, DeliveryOrderController.updateDeliveryOrder);
router.delete('/delivery-orders/:id', DeliveryOrderController.deleteDeliveryOrder);

// Sub-assembly routes
router.get('/sub-assemblies', SubAssemblyController.getAllSubAssemblies);
router.get('/sub-assemblies/:id', SubAssemblyController.getSubAssemblyById);
router.get('/sub-assemblies/item/:itemId', SubAssemblyController.getSubAssembliesByItemId);
router.post('/sub-assemblies', validateSubAssembly, SubAssemblyController.createSubAssembly);
router.put('/sub-assemblies/:id', validateSubAssembly, SubAssemblyController.updateSubAssembly);
router.put('/sub-assemblies/item/:itemId/lock', validateSubAssemblyLock, SubAssemblyController.lockSubAssembliesByItemId);
router.delete('/sub-assemblies/:id', SubAssemblyController.deleteSubAssembly);

// BOM Item routes
router.get('/bom-items', BomItemController.getAllBomItems);
router.get('/bom-items/:id', BomItemController.getBomItemById);
router.get('/bom-items/item/:itemId', BomItemController.getBomItemsByItemId);
router.post('/bom-items', validateBomItem, BomItemController.createBomItem);
router.put('/bom-items/:id', validateBomItem, BomItemController.updateBomItem);
router.delete('/bom-items/:id', BomItemController.deleteBomItem);

// Item Step Config routes
router.get('/item-step-configs', ItemStepConfigController.getAllItemStepConfigs);
router.get('/item-step-configs/:id', ItemStepConfigController.getItemStepConfigById);
router.get('/item-step-configs/item/:itemId', ItemStepConfigController.getItemStepConfigsByItemId);
router.post('/item-step-configs', validateItemStepConfig, ItemStepConfigController.createItemStepConfig);
router.put('/item-step-configs/:id', validateItemStepConfig, ItemStepConfigController.updateItemStepConfig);
router.delete('/item-step-configs/:id', ItemStepConfigController.deleteItemStepConfig);

// Machine Allocation routes
router.get('/machine-allocations', MachineAllocationController.getAllMachineAllocations);
router.get('/machine-allocations/:id', MachineAllocationController.getMachineAllocationById);
router.get('/machine-allocations/step-config/:stepConfigId', MachineAllocationController.getMachineAllocationsByStepConfigId);
router.post('/machine-allocations', validateMachineAllocation, MachineAllocationController.createMachineAllocation);
router.put('/machine-allocations/:id', validateMachineAllocation, MachineAllocationController.updateMachineAllocation);
router.delete('/machine-allocations/:id', MachineAllocationController.deleteMachineAllocation);

module.exports = router;