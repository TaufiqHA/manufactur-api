const { query, run } = require('../config/db');

class SubAssembly {
  static async findAll() {
    const result = await query('SELECT * FROM sub_assemblies ORDER BY name');
    const subAssemblies = result.rows;

    // Initialize stepStats for each sub-assembly if not present
    return subAssemblies.map(sa => {
      const processes = typeof sa.processes === 'string' ? JSON.parse(sa.processes) : sa.processes;
      return {
        ...sa,
        processes: processes,
        stepStats: typeof sa.stepStats === 'string' ? this.initializeStepStats(JSON.parse(sa.stepStats), processes) : this.initializeStepStats(sa.stepStats, processes)
      };
    });
  }

  static async findById(id) {
    const result = await query('SELECT * FROM sub_assemblies WHERE id = ?', [id]);
    const subAssembly = result.rows[0];

    if (!subAssembly) return null;

    // Initialize stepStats if not present
    const processes = typeof subAssembly.processes === 'string' ? JSON.parse(subAssembly.processes) : subAssembly.processes;
    return {
      ...subAssembly,
      processes: processes,
      stepStats: typeof subAssembly.stepStats === 'string' ? this.initializeStepStats(JSON.parse(subAssembly.stepStats), processes) : this.initializeStepStats(subAssembly.stepStats, processes)
    };
  }

  static async findByItemId(itemId) {
    const result = await query('SELECT * FROM sub_assemblies WHERE itemId = ?', [itemId]);
    const subAssemblies = result.rows;

    // Initialize stepStats for each sub-assembly if not present
    return subAssemblies.map(sa => {
      const processes = typeof sa.processes === 'string' ? JSON.parse(sa.processes) : sa.processes;
      return {
        ...sa,
        processes: processes,
        stepStats: typeof sa.stepStats === 'string' ? this.initializeStepStats(JSON.parse(sa.stepStats), processes) : this.initializeStepStats(sa.stepStats, processes)
      };
    });
  }

  // Initialize stepStats with default values based on processes
  static initializeStepStats(currentStepStats, processes) {
    // If stepStats is empty or null, initialize with default values
    if (!currentStepStats || Object.keys(currentStepStats).length === 0) {
      const defaultStepStats = {};

      // Define all possible process steps
      const allProcessSteps = ['POTONG', 'PLONG', 'PRESS', 'LAS', 'PHOSPHATING', 'CAT', 'PACKING'];

      // Only initialize steps that are in the processes array
      processes.forEach(step => {
        if (allProcessSteps.includes(step)) {
          defaultStepStats[step] = {
            produced: 0,
            available: 0  // Will be set to totalNeeded when sub-assembly is created
          };
        }
      });

      return defaultStepStats;
    }

    // If stepStats exists but doesn't have entries for all processes, add missing ones
    const allProcessSteps = ['POTONG', 'PLONG', 'PRESS', 'LAS', 'PHOSPHATING', 'CAT', 'PACKING'];
    const updatedStepStats = { ...currentStepStats };

    processes.forEach(step => {
      if (allProcessSteps.includes(step) && !updatedStepStats[step]) {
        updatedStepStats[step] = {
          produced: 0,
          available: 0
        };
      }
    });

    return updatedStepStats;
  }

  static async create(saData) {
    // Initialize stepStats if not provided
    const processes = saData.processes || [];
    let stepStats = saData.stepStats || {};

    // Initialize stepStats with default values
    stepStats = this.initializeStepStats(stepStats, processes);

    // For the first step in the process, set available to totalNeeded
    if (processes.length > 0) {
      const firstStep = processes[0];
      if (stepStats[firstStep]) {
        stepStats[firstStep].available = saData.totalNeeded || 0;
      }
    }

    const { id, itemId, name, qtyPerParent, totalNeeded, completedQty, totalProduced, consumedQty, materialId, isLocked } = saData;
    await run(`
      INSERT INTO sub_assemblies (id, itemId, name, qtyPerParent, totalNeeded, completedQty, totalProduced, consumedQty, materialId, processes, stepStats, isLocked)
      VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
    `, [id, itemId, name, qtyPerParent, totalNeeded, completedQty, totalProduced, consumedQty, materialId, JSON.stringify(processes), JSON.stringify(stepStats), isLocked ? 1 : 0]);

    return this.findById(id);
  }

  static async update(id, saData) {
    // If processes are being updated, we might need to update stepStats accordingly
    const { itemId, name, qtyPerParent, totalNeeded, completedQty, totalProduced, consumedQty, materialId, processes, stepStats, isLocked } = saData;

    // Get current sub-assembly to preserve existing values if not provided in update
    const currentSubAssembly = await this.findById(id);

    // Use provided processes or keep existing ones
    const updatedProcesses = processes !== undefined ? processes : currentSubAssembly.processes || [];

    // Initialize stepStats if not provided, or merge with existing
    let updatedStepStats = stepStats || currentSubAssembly.stepStats || {};

    // Initialize stepStats with default values based on processes
    updatedStepStats = this.initializeStepStats(updatedStepStats, updatedProcesses);

    await run(`
      UPDATE sub_assemblies
      SET itemId = ?, name = ?, qtyPerParent = ?, totalNeeded = ?, completedQty = ?,
          totalProduced = ?, consumedQty = ?, materialId = ?, processes = ?, stepStats = ?, isLocked = ?
      WHERE id = ?
    `, [itemId, name, qtyPerParent, totalNeeded, completedQty, totalProduced, consumedQty, materialId, JSON.stringify(updatedProcesses), JSON.stringify(updatedStepStats), isLocked ? 1 : 0, id]);

    return this.findById(id);
  }

  static async delete(id) {
    await run('DELETE FROM sub_assemblies WHERE id = ?', [id]);
    return true;
  }

  static async updateByItemId(itemId, updateData) {
    const { isLocked } = updateData;

    // Update all sub-assemblies with the given itemId
    await run(`
      UPDATE sub_assemblies
      SET isLocked = ?
      WHERE itemId = ?
    `, [isLocked ? 1 : 0, itemId]);

    // Return the updated sub-assemblies
    return this.findByItemId(itemId);
  }
}

module.exports = SubAssembly;