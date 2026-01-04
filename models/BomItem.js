const { query, run } = require('../config/db');

class BomItem {
  static async findAll() {
    const result = await query('SELECT * FROM bom_items ORDER BY itemId');
    return result.rows;
  }

  static async findById(id) {
    const result = await query('SELECT * FROM bom_items WHERE id = ?', [id]);
    return result.rows[0];
  }

  static async findByItemId(itemId) {
    const result = await query('SELECT * FROM bom_items WHERE itemId = ?', [itemId]);
    return result.rows;
  }

  static async create(bomData) {
    const { id, itemId, materialId, quantityPerUnit, totalRequired, allocated, realized } = bomData;
    await run(`
      INSERT INTO bom_items (id, itemId, materialId, quantityPerUnit, totalRequired, allocated, realized)
      VALUES (?, ?, ?, ?, ?, ?, ?)
    `, [id, itemId, materialId, quantityPerUnit, totalRequired, allocated, realized]);

    return this.findById(id);
  }

  static async update(id, bomData) {
    const { itemId, materialId, quantityPerUnit, totalRequired, allocated, realized } = bomData;
    await run(`
      UPDATE bom_items
      SET itemId = ?, materialId = ?, quantityPerUnit = ?, totalRequired = ?, allocated = ?, realized = ?
      WHERE id = ?
    `, [itemId, materialId, quantityPerUnit, totalRequired, allocated, realized, id]);

    return this.findById(id);
  }

  static async delete(id) {
    await run('DELETE FROM bom_items WHERE id = ?', [id]);
    return true;
  }
}

module.exports = BomItem;