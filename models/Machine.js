const { query, run } = require('../config/db');

class Machine {
  static async findAll() {
    const result = await query('SELECT * FROM machines ORDER BY name');
    return result.rows;
  }

  static async findById(id) {
    const result = await query('SELECT * FROM machines WHERE id = ?', [id]);
    return result.rows[0];
  }

  static async create(machineData) {
    const { id, code, name, type, capacityPerHour, status, personnel, isMaintenance } = machineData;
    await run(`
      INSERT INTO machines (id, code, name, type, capacityPerHour, status, personnel, isMaintenance)
      VALUES (?, ?, ?, ?, ?, ?, ?, ?)
    `, [id, code, name, type, capacityPerHour, status, JSON.stringify(personnel), isMaintenance ? 1 : 0]);

    return this.findById(id);
  }

  static async update(id, machineData) {
    const { code, name, type, capacityPerHour, status, personnel, isMaintenance } = machineData;
    await run(`
      UPDATE machines
      SET code = ?, name = ?, type = ?, capacityPerHour = ?, status = ?, personnel = ?, isMaintenance = ?
      WHERE id = ?
    `, [code, name, type, capacityPerHour, status, JSON.stringify(personnel), isMaintenance ? 1 : 0, id]);

    return this.findById(id);
  }

  static async delete(id) {
    await run('DELETE FROM machines WHERE id = ?', [id]);
    return true;
  }

  static async toggleMaintenance(id) {
    const machine = await this.findById(id);
    if (!machine) return null;

    await run(`
      UPDATE machines
      SET isMaintenance = ?
      WHERE id = ?
    `, [machine.isMaintenance ? 0 : 1, id]);

    return this.findById(id);
  }
}

module.exports = Machine;