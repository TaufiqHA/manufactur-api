const { query, run } = require('../config/db');

class PurchaseOrder {
  static async findAll() {
    const result = await query('SELECT * FROM purchase_orders ORDER BY date DESC');
    return result.rows;
  }

  static async findById(id) {
    const result = await query('SELECT * FROM purchase_orders WHERE id = ?', [id]);
    return result.rows[0];
  }

  static async create(poData) {
    const { id, code, date, supplierId, description, items, status, grandTotal } = poData;
    await run(`
      INSERT INTO purchase_orders (id, code, date, supplierId, description, items, status, grandTotal)
      VALUES (?, ?, ?, ?, ?, ?, ?, ?)
    `, [id, code, date, supplierId, description, JSON.stringify(items), status, grandTotal]);

    return this.findById(id);
  }

  static async update(id, poData) {
    const { code, date, supplierId, description, items, status, grandTotal } = poData;
    await run(`
      UPDATE purchase_orders
      SET code = ?, date = ?, supplierId = ?, description = ?, items = ?, status = ?, grandTotal = ?
      WHERE id = ?
    `, [code, date, supplierId, description, JSON.stringify(items), status, grandTotal, id]);

    return this.findById(id);
  }

  static async delete(id) {
    await run('DELETE FROM purchase_orders WHERE id = ?', [id]);
    return true;
  }
}

module.exports = PurchaseOrder;