const { query, run } = require('../config/db');

class Project {
  static async findAll() {
    const result = await query('SELECT * FROM projects ORDER BY id DESC');
    return result.rows;
  }

  static async findById(id) {
    const result = await query('SELECT * FROM projects WHERE id = ?', [id]);
    return result.rows[0];
  }

  static async create(projectData) {
    const { id, code, name, customer, startDate, deadline, status, progress, qtyPerUnit, procurementQty, totalQty, unit, isLocked } = projectData;
    await run(`
      INSERT INTO projects (id, code, name, customer, startDate, deadline, status, progress, qtyPerUnit, procurementQty, totalQty, unit, isLocked)
      VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
    `, [id, code, name, customer, startDate, deadline, status, progress, qtyPerUnit, procurementQty, totalQty, unit, isLocked ? 1 : 0]);

    return this.findById(id);
  }

  static async update(id, projectData) {
    const { code, name, customer, startDate, deadline, status, progress, qtyPerUnit, procurementQty, totalQty, unit, isLocked } = projectData;
    await run(`
      UPDATE projects
      SET code = ?, name = ?, customer = ?, startDate = ?, deadline = ?, status = ?,
          progress = ?, qtyPerUnit = ?, procurementQty = ?, totalQty = ?, unit = ?, isLocked = ?
      WHERE id = ?
    `, [code, name, customer, startDate, deadline, status, progress, qtyPerUnit, procurementQty, totalQty, unit, isLocked ? 1 : 0, id]);

    return this.findById(id);
  }

  static async delete(id) {
    await run('DELETE FROM projects WHERE id = ?', [id]);
    return true;
  }
}

module.exports = Project;