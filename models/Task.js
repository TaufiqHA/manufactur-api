const { query, run } = require('../config/db');

class Task {
  static async findAll() {
    const result = await query('SELECT * FROM tasks ORDER BY id ASC');
    return result.rows;
  }

  static async findById(id) {
    const result = await query('SELECT * FROM tasks WHERE id = ?', [id]);
    return result.rows[0];
  }

  static async findByProjectId(projectId) {
    const result = await query('SELECT * FROM tasks WHERE projectId = ? ORDER BY id ASC', [projectId]);
    return result.rows;
  }

  static async findByItemId(itemId) {
    const result = await query('SELECT * FROM tasks WHERE itemId = ?', [itemId]);
    return result.rows;
  }

  static async create(taskData) {
    const { id, projectId, projectName, itemId, itemName, subAssemblyId, subAssemblyName, step, machineId, targetQty, dailyTarget, completedQty, defectQty, status, note, totalDowntimeMinutes } = taskData;
    await run(`
      INSERT INTO tasks (id, projectId, projectName, itemId, itemName, subAssemblyId, subAssemblyName, step, machineId, targetQty, dailyTarget, completedQty, defectQty, status, note, totalDowntimeMinutes)
      VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
    `, [id, projectId, projectName, itemId, itemName, subAssemblyId, subAssemblyName, step, machineId, targetQty, dailyTarget, completedQty, defectQty, status, note, totalDowntimeMinutes]);

    return this.findById(id);
  }

  static async update(id, taskData) {
    const { projectId, projectName, itemId, itemName, subAssemblyId, subAssemblyName, step, machineId, targetQty, dailyTarget, completedQty, defectQty, status, note, totalDowntimeMinutes } = taskData;
    await run(`
      UPDATE tasks
      SET projectId = ?, projectName = ?, itemId = ?, itemName = ?, subAssemblyId = ?, subAssemblyName = ?,
          step = ?, machineId = ?, targetQty = ?, dailyTarget = ?, completedQty = ?, defectQty = ?,
          status = ?, note = ?, totalDowntimeMinutes = ?
      WHERE id = ?
    `, [projectId, projectName, itemId, itemName, subAssemblyId, subAssemblyName, step, machineId, targetQty, dailyTarget, completedQty, defectQty, status, note, totalDowntimeMinutes, id]);

    return this.findById(id);
  }

  static async delete(id) {
    await run('DELETE FROM tasks WHERE id = ?', [id]);
    return true;
  }
}

module.exports = Task;