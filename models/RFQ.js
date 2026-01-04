const { query, run } = require('../config/db');

class RFQ {
  static async findAll() {
    const result = await query('SELECT * FROM rfqs ORDER BY date DESC');
    return result.rows;
  }

  static async findById(id) {
    const result = await query('SELECT * FROM rfqs WHERE id = ?', [id]);
    return result.rows[0];
  }

  static async create(rfqData) {
    const { id, code, date, description, items, status } = rfqData;
    await run(`
      INSERT INTO rfqs (id, code, date, description, items, status)
      VALUES (?, ?, ?, ?, ?, ?)
    `, [id, code, date, description, JSON.stringify(items), status]);

    return this.findById(id);
  }

  static async update(id, rfqData) {
    const { code, date, description, items, status } = rfqData;
    await run(`
      UPDATE rfqs
      SET code = ?, date = ?, description = ?, items = ?, status = ?
      WHERE id = ?
    `, [code, date, description, JSON.stringify(items), status, id]);

    return this.findById(id);
  }

  static async delete(id) {
    await run('DELETE FROM rfqs WHERE id = ?', [id]);
    return true;
  }
}

module.exports = RFQ;