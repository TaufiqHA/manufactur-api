const { query, run } = require('../config/db');

class DeliveryOrder {
  static async findAll() {
    const result = await query('SELECT * FROM delivery_orders ORDER BY date DESC');
    return result.rows;
  }

  static async findById(id) {
    const result = await query('SELECT * FROM delivery_orders WHERE id = ?', [id]);
    return result.rows[0];
  }

  static async create(doData) {
    const { id, code, date, customer, address, driverName, vehiclePlate, status } = doData;
    await run(`
      INSERT INTO delivery_orders (id, code, date, customer, address, driverName, vehiclePlate, status)
      VALUES (?, ?, ?, ?, ?, ?, ?, ?)
    `, [id, code, date, customer, address, driverName, vehiclePlate, status]);

    return this.findById(id);
  }

  static async update(id, doData) {
    const { code, date, customer, address, driverName, vehiclePlate, status } = doData;
    await run(`
      UPDATE delivery_orders
      SET code = ?, date = ?, customer = ?, address = ?, driverName = ?, vehiclePlate = ?, status = ?
      WHERE id = ?
    `, [code, date, customer, address, driverName, vehiclePlate, status, id]);

    return this.findById(id);
  }

  static async delete(id) {
    await run('DELETE FROM delivery_orders WHERE id = ?', [id]);
    return true;
  }
}

module.exports = DeliveryOrder;