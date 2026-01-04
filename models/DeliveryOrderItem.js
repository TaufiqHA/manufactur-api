const { query, run } = require('../config/db');

class DeliveryOrderItem {
  static async findAll() {
    const result = await query('SELECT * FROM delivery_order_items ORDER BY deliveryOrderId');
    return result.rows;
  }

  static async findById(id) {
    const result = await query('SELECT * FROM delivery_order_items WHERE id = ?', [id]);
    return result.rows[0];
  }

  static async findByDeliveryOrderId(deliveryOrderId) {
    const result = await query('SELECT * FROM delivery_order_items WHERE deliveryOrderId = ?', [deliveryOrderId]);
    return result.rows;
  }

  static async create(itemData) {
    const { id, deliveryOrderId, projectId, projectName, itemId, itemName, qty, unit } = itemData;
    await run(`
      INSERT INTO delivery_order_items (id, deliveryOrderId, projectId, projectName, itemId, itemName, qty, unit)
      VALUES (?, ?, ?, ?, ?, ?, ?, ?)
    `, [id, deliveryOrderId, projectId, projectName, itemId, itemName, qty, unit]);

    return this.findById(id);
  }

  static async update(id, itemData) {
    const { deliveryOrderId, projectId, projectName, itemId, itemName, qty, unit } = itemData;
    await run(`
      UPDATE delivery_order_items
      SET deliveryOrderId = ?, projectId = ?, projectName = ?, itemId = ?, itemName = ?, qty = ?, unit = ?
      WHERE id = ?
    `, [deliveryOrderId, projectId, projectName, itemId, itemName, qty, unit, id]);

    return this.findById(id);
  }

  static async delete(id) {
    await run('DELETE FROM delivery_order_items WHERE id = ?', [id]);
    return true;
  }
}

module.exports = DeliveryOrderItem;