const { query, run } = require('../config/db');

class MachineAllocation {
  static async findAll() {
    const result = await query('SELECT * FROM machine_allocations ORDER BY stepConfigId');
    return result.rows;
  }

  static async findById(id) {
    const result = await query('SELECT * FROM machine_allocations WHERE id = ?', [id]);
    return result.rows[0];
  }

  static async findByStepConfigId(stepConfigId) {
    const result = await query('SELECT * FROM machine_allocations WHERE stepConfigId = ?', [stepConfigId]);
    return result.rows;
  }

  static async create(allocData) {
    const { id, stepConfigId, machineId, targetQty, note } = allocData;
    await run(`
      INSERT INTO machine_allocations (id, stepConfigId, machineId, targetQty, note)
      VALUES (?, ?, ?, ?, ?)
    `, [id, stepConfigId, machineId, targetQty, note]);

    return this.findById(id);
  }

  static async update(id, allocData) {
    const { stepConfigId, machineId, targetQty, note } = allocData;
    await run(`
      UPDATE machine_allocations
      SET stepConfigId = ?, machineId = ?, targetQty = ?, note = ?
      WHERE id = ?
    `, [stepConfigId, machineId, targetQty, note, id]);

    return this.findById(id);
  }

  static async delete(id) {
    await run('DELETE FROM machine_allocations WHERE id = ?', [id]);
    return true;
  }
}

module.exports = MachineAllocation;