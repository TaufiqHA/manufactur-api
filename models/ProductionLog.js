const { query, run } = require('../config/db');

class ProductionLog {
  static async findAll() {
    const result = await query('SELECT * FROM production_logs ORDER BY timestamp DESC');
    return result.rows;
  }

  static async findById(id) {
    const result = await query('SELECT * FROM production_logs WHERE id = ?', [id]);
    return result.rows[0];
  }

  static async findByTaskId(taskId) {
    const result = await query('SELECT * FROM production_logs WHERE taskId = ? ORDER BY timestamp DESC', [taskId]);
    return result.rows;
  }

  static async findByProjectId(projectId) {
    const result = await query('SELECT * FROM production_logs WHERE projectId = ? ORDER BY timestamp DESC', [projectId]);
    return result.rows;
  }

  static async create(logData) {
    const { id, taskId, machineId, itemId, subAssemblyId, projectId, step, shift, goodQty, defectQty, operator, type } = logData;
    await run(`
      INSERT INTO production_logs (id, taskId, machineId, itemId, subAssemblyId, projectId, step, shift, goodQty, defectQty, operator, type)
      VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
    `, [id, taskId, machineId, itemId, subAssemblyId, projectId, step, shift, goodQty, defectQty, operator, type]);

    return this.findById(id);
  }

  static async update(id, logData) {
    const { taskId, machineId, itemId, subAssemblyId, projectId, step, shift, goodQty, defectQty, operator, type } = logData;
    await run(`
      UPDATE production_logs
      SET taskId = ?, machineId = ?, itemId = ?, subAssemblyId = ?, projectId = ?,
          step = ?, shift = ?, goodQty = ?, defectQty = ?, operator = ?, type = ?
      WHERE id = ?
    `, [taskId, machineId, itemId, subAssemblyId, projectId, step, shift, goodQty, defectQty, operator, type, id]);

    return this.findById(id);
  }

  static async delete(id) {
    await run('DELETE FROM production_logs WHERE id = ?', [id]);
    return true;
  }
}

module.exports = ProductionLog;