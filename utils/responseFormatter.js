// Utility functions for API responses

// Format response to match frontend expectations
const formatResponse = (data, pagination = null) => {
  if (pagination) {
    return {
      data,
      pagination
    };
  }
  return data;
};

// Create pagination object
const createPagination = (page, limit, total) => {
  const totalPages = Math.ceil(total / limit);
  return {
    page: parseInt(page),
    limit: parseInt(limit),
    total,
    totalPages,
    hasNext: page < totalPages,
    hasPrev: page > 1
  };
};

// Format project response to match frontend structure
const formatProject = (project) => {
  return {
    id: project.id,
    code: project.code,
    name: project.name,
    customer: project.customer,
    startDate: project.startDate,
    deadline: project.deadline,
    status: project.status,
    progress: project.progress,
    qtyPerUnit: project.qtyPerUnit,
    procurementQty: project.procurementQty,
    totalQty: project.totalQty,
    unit: project.unit,
    isLocked: project.isLocked
  };
};

// Format material response to match frontend structure
const formatMaterial = (material) => {
  return {
    id: material.id,
    code: material.code,
    name: material.name,
    unit: material.unit,
    currentStock: material.currentStock,
    safetyStock: material.safetyStock,
    pricePerUnit: material.pricePerUnit,
    category: material.category
  };
};

// Format machine response to match frontend structure
const formatMachine = (machine) => {
  return {
    id: machine.id,
    code: machine.code,
    name: machine.name,
    type: machine.type,
    capacityPerHour: machine.capacityPerHour,
    status: machine.status,
    personnel: machine.personnel || [],
    isMaintenance: machine.isMaintenance
  };
};

// Format project item response to match frontend structure
const formatProjectItem = (item) => {
  return {
    id: item.id,
    projectId: item.projectId,
    name: item.name,
    dimensions: item.dimensions,
    thickness: item.thickness,
    qtySet: item.qtySet,
    quantity: item.quantity,
    unit: item.unit,
    isBomLocked: item.isBomLocked,
    isWorkflowLocked: item.isWorkflowLocked,
    flowType: item.flowType,
    subAssemblies: item.subAssemblies || [],
    bom: item.bom || [],
    workflow: item.workflow || [],
    warehouseQty: item.warehouseQty || 0,
    shippedQty: item.shippedQty || 0,
    assemblyStats: item.assemblyStats || {}
  };
};

// Format task response to match frontend structure
const formatTask = (task) => {
  return {
    id: task.id,
    projectId: task.projectId,
    projectName: task.projectName,
    itemId: task.itemId,
    itemName: task.itemName,
    subAssemblyId: task.subAssemblyId,
    subAssemblyName: task.subAssemblyName,
    step: task.step,
    machineId: task.machineId,
    targetQty: task.targetQty,
    dailyTarget: task.dailyTarget,
    completedQty: task.completedQty,
    defectQty: task.defectQty,
    status: task.status,
    note: task.note,
    totalDowntimeMinutes: task.totalDowntimeMinutes
  };
};

// Format user response to match frontend structure
const formatUser = (user) => {
  return {
    id: user.id,
    name: user.name,
    username: user.username,
    role: user.role,
    permissions: user.permissions
  };
};

// Format production log response to match frontend structure
const formatProductionLog = (log) => {
  return {
    id: log.id,
    taskId: log.taskId,
    machineId: log.machineId,
    itemId: log.itemId,
    subAssemblyId: log.subAssemblyId,
    projectId: log.projectId,
    step: log.step,
    shift: log.shift,
    goodQty: log.goodQty,
    defectQty: log.defectQty,
    operator: log.operator,
    timestamp: log.timestamp,
    type: log.type
  };
};

// Format supplier response to match frontend structure
const formatSupplier = (supplier) => {
  return {
    id: supplier.id,
    name: supplier.name,
    address: supplier.address,
    contact: supplier.contact
  };
};

// Format RFQ response to match frontend structure
const formatRFQ = (rfq) => {
  return {
    id: rfq.id,
    code: rfq.code,
    date: rfq.date,
    description: rfq.description,
    items: rfq.items || [],
    status: rfq.status
  };
};

// Format purchase order response to match frontend structure
const formatPurchaseOrder = (po) => {
  return {
    id: po.id,
    code: po.code,
    date: po.date,
    supplierId: po.supplierId,
    description: po.description,
    items: po.items || [],
    status: po.status,
    grandTotal: po.grandTotal
  };
};

// Format receiving goods response to match frontend structure
const formatReceivingGoods = (receiving) => {
  return {
    id: receiving.id,
    code: receiving.code,
    date: receiving.date,
    poId: receiving.poId,
    items: receiving.items || []
  };
};

// Format delivery order response to match frontend structure
const formatDeliveryOrder = (deliveryOrder) => {
  return {
    id: deliveryOrder.id,
    code: deliveryOrder.code,
    date: deliveryOrder.date,
    customer: deliveryOrder.customer,
    address: deliveryOrder.address,
    driverName: deliveryOrder.driverName,
    vehiclePlate: deliveryOrder.vehiclePlate,
    items: deliveryOrder.items || [],
    status: deliveryOrder.status
  };
};

// Format sub-assembly response to match frontend structure
const formatSubAssembly = (subAssembly) => {
  return {
    id: subAssembly.id,
    name: subAssembly.name,
    qtyPerParent: subAssembly.qtyPerParent,
    totalNeeded: subAssembly.totalNeeded,
    completedQty: subAssembly.completedQty,
    totalProduced: subAssembly.totalProduced,
    consumedQty: subAssembly.consumedQty,
    materialId: subAssembly.materialId,
    processes: subAssembly.processes || [],
    stepStats: subAssembly.stepStats || {},
    isLocked: subAssembly.isLocked
  };
};

// Format BOM item response to match frontend structure
const formatBomItem = (bomItem) => {
  return {
    id: bomItem.id,
    itemId: bomItem.itemId,
    materialId: bomItem.materialId,
    quantityPerUnit: bomItem.quantityPerUnit,
    totalRequired: bomItem.totalRequired,
    allocated: bomItem.allocated,
    realized: bomItem.realized
  };
};

// Format item step config response to match frontend structure
const formatItemStepConfig = (config) => {
  return {
    step: config.step,
    sequence: config.sequence,
    allocations: config.allocations || []
  };
};

// Format machine allocation response to match frontend structure
const formatMachineAllocation = (allocation) => {
  return {
    id: allocation.id,
    machineId: allocation.machineId,
    targetQty: allocation.targetQty,
    note: allocation.note
  };
};

module.exports = {
  formatResponse,
  createPagination,
  formatProject,
  formatMaterial,
  formatMachine,
  formatProjectItem,
  formatTask,
  formatUser,
  formatProductionLog,
  formatSupplier,
  formatRFQ,
  formatPurchaseOrder,
  formatReceivingGoods,
  formatDeliveryOrder,
  formatSubAssembly,
  formatBomItem,
  formatItemStepConfig,
  formatMachineAllocation
};