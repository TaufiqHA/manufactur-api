const { query, run } = require('../config/db');

class Material {
  static async findAll() {
    const result = await query('SELECT * FROM materials ORDER BY name');
    return result.rows;
  }

  static async findById(id) {
    const result = await query('SELECT * FROM materials WHERE id = ?', [id]);
    return result.rows[0];
  }

  static async create(materialData) {
    const { id, code, name, unit, currentStock, safetyStock, pricePerUnit, category } = materialData;
    await run(`
      INSERT INTO materials (id, code, name, unit, currentStock, safetyStock, pricePerUnit, category)
      VALUES (?, ?, ?, ?, ?, ?, ?, ?)
    `, [id, code, name, unit, currentStock, safetyStock, pricePerUnit, category]);

    return this.findById(id);
  }

  static async update(id, materialData) {
    const { code, name, unit, currentStock, safetyStock, pricePerUnit, category } = materialData;
    await run(`
      UPDATE materials
      SET code = ?, name = ?, unit = ?, currentStock = ?, safetyStock = ?, pricePerUnit = ?, category = ?
      WHERE id = ?
    `, [code, name, unit, currentStock, safetyStock, pricePerUnit, category, id]);

    return this.findById(id);
  }

  static async delete(id) {
    await run('DELETE FROM materials WHERE id = ?', [id]);
    return true;
  }

  static async adjustStock(id, amount) {
    await run(`
      UPDATE materials
      SET currentStock = currentStock + ?
      WHERE id = ?
    `, [amount, id]);
    return this.findById(id);
  }
}

module.exports = Material;