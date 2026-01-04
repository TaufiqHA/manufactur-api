const { query, run } = require('../config/db');

class ItemStepConfig {
  static async findAll() {
    const result = await query('SELECT * FROM item_step_configs ORDER BY itemId, sequence');
    return result.rows;
  }

  static async findById(id) {
    const result = await query('SELECT * FROM item_step_configs WHERE id = ?', [id]);
    return result.rows[0];
  }

  static async findByItemId(itemId) {
    const result = await query('SELECT * FROM item_step_configs WHERE itemId = ? ORDER BY sequence', [itemId]);
    return result.rows;
  }

  static async create(configData) {
    const { id, itemId, step, sequence } = configData;
    await run(`
      INSERT INTO item_step_configs (id, itemId, step, sequence)
      VALUES (?, ?, ?, ?)
    `, [id, itemId, step, sequence]);

    return this.findById(id);
  }

  static async update(id, configData) {
    const { itemId, step, sequence } = configData;
    await run(`
      UPDATE item_step_configs
      SET itemId = ?, step = ?, sequence = ?
      WHERE id = ?
    `, [itemId, step, sequence, id]);

    return this.findById(id);
  }

  static async delete(id) {
    await run('DELETE FROM item_step_configs WHERE id = ?', [id]);
    return true;
  }
}

module.exports = ItemStepConfig;