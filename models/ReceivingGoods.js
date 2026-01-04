const { query, run } = require('../config/db');

class ReceivingGoods {
  static async findAll() {
    const result = await query('SELECT * FROM receiving_goods ORDER BY date DESC');
    return result.rows;
  }

  static async findById(id) {
    const result = await query('SELECT * FROM receiving_goods WHERE id = ?', [id]);
    return result.rows[0];
  }

  static async create(receivingData) {
    const { id, code, date, poId, items } = receivingData;
    await run(`
      INSERT INTO receiving_goods (id, code, date, poId, items)
      VALUES (?, ?, ?, ?, ?)
    `, [id, code, date, poId, JSON.stringify(items)]);

    return this.findById(id);
  }

  static async update(id, receivingData) {
    const { code, date, poId, items } = receivingData;
    await run(`
      UPDATE receiving_goods
      SET code = ?, date = ?, poId = ?, items = ?
      WHERE id = ?
    `, [code, date, poId, JSON.stringify(items), id]);

    return this.findById(id);
  }

  static async delete(id) {
    await run('DELETE FROM receiving_goods WHERE id = ?', [id]);
    return true;
  }
}

module.exports = ReceivingGoods;