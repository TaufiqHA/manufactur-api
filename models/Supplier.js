const { query, run } = require('../config/db');

class Supplier {
  static async findAll() {
    const result = await query('SELECT * FROM suppliers ORDER BY name');
    return result.rows;
  }

  static async findById(id) {
    const result = await query('SELECT * FROM suppliers WHERE id = ?', [id]);
    return result.rows[0];
  }

  static async create(supplierData) {
    const { id, name, address, contact } = supplierData;
    await run(`
      INSERT INTO suppliers (id, name, address, contact)
      VALUES (?, ?, ?, ?)
    `, [id, name, address, contact]);

    return this.findById(id);
  }

  static async update(id, supplierData) {
    const { name, address, contact } = supplierData;
    await run(`
      UPDATE suppliers
      SET name = ?, address = ?, contact = ?
      WHERE id = ?
    `, [name, address, contact, id]);

    return this.findById(id);
  }

  static async delete(id) {
    await run('DELETE FROM suppliers WHERE id = ?', [id]);
    return true;
  }
}

module.exports = Supplier;