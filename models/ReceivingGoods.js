const { query, run } = require('../config/db');
const PurchaseOrder = require('./PurchaseOrder');
const Material = require('./Material');

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

    // Update material stock based on received items
    try {
      await this.updateMaterialStock(poId, items);
    } catch (error) {
      // If stock update fails, we might want to rollback the receiving goods creation
      // For now, we'll log the error and continue
      console.error('Error updating material stock:', error);
      // Optionally, we could delete the receiving goods record if stock update fails
      // await this.delete(id); // This would require adjusting the method to not throw on error
    }

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

  static async updateMaterialStock(poId, receivedItems) {
    // Get the original PO to match material IDs with received quantities
    const po = await PurchaseOrder.findById(poId);

    if (!po) {
      throw new Error(`Purchase Order with ID ${poId} not found`);
    }

    // The items field is stored as a JSON string in the database, so we need to parse it
    const poItems = typeof po.items === 'string' ? JSON.parse(po.items) : po.items;

    // Process each received item and update corresponding material stock
    for (const receivedItem of receivedItems) {
      // Find the matching item in the PO based on material ID (primary matching criterion)
      let poItem = poItems.find(item => item.materialId === receivedItem.materialId);

      // If not found by materialId, try matching by material name or code
      if (!poItem) {
        poItem = poItems.find(item =>
          item.materialName === receivedItem.materialName ||
          item.materialCode === receivedItem.materialCode
        );
      }

      if (poItem) {
        // Use the received quantity if available, otherwise fall back to qty
        const receivedQty = receivedItem.receivedQty !== undefined ? receivedItem.receivedQty :
                           receivedItem.qty !== undefined ? receivedItem.qty : 0;

        // Update the material stock by adding the received quantity
        await Material.adjustStock(poItem.materialId, receivedQty);
      } else {
        console.warn(`Could not find matching material in PO ${poId} for received item:`, receivedItem);
      }
    }
  }
}

module.exports = ReceivingGoods;