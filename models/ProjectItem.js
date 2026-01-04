const { query, run } = require('../config/db');

class ProjectItem {
  static async findAll() {
    const result = await query('SELECT * FROM project_items ORDER BY name');
    return result.rows.map(item => {
      if (item.assemblyStats) {
        try {
          item.assemblyStats = JSON.parse(item.assemblyStats);
        } catch (e) {
          // If parsing fails, keep the original value
          console.warn('Failed to parse assemblyStats for item', item.id, e.message);
        }
      } else {
        item.assemblyStats = {};
      }
      return item;
    });
  }

  static async findById(id) {
    const result = await query('SELECT * FROM project_items WHERE id = ?', [id]);
    if (result.rows[0]) {
      const item = result.rows[0];
      if (item.assemblyStats) {
        try {
          item.assemblyStats = JSON.parse(item.assemblyStats);
        } catch (e) {
          // If parsing fails, keep the original value
          console.warn('Failed to parse assemblyStats for item', item.id, e.message);
        }
      } else {
        item.assemblyStats = {};
      }
      return item;
    }
    return null;
  }

  static async findByProjectId(projectId) {
    const result = await query('SELECT * FROM project_items WHERE projectId = ?', [projectId]);
    return result.rows.map(item => {
      if (item.assemblyStats) {
        try {
          item.assemblyStats = JSON.parse(item.assemblyStats);
        } catch (e) {
          // If parsing fails, keep the original value
          console.warn('Failed to parse assemblyStats for item', item.id, e.message);
        }
      } else {
        item.assemblyStats = {};
      }
      return item;
    });
  }

  static async create(itemData) {
    const { id, projectId, name, dimensions, thickness, qtySet, quantity, unit, isBomLocked, isWorkflowLocked, flowType, warehouseQty, shippedQty, assemblyStats } = itemData;
    await run(`
      INSERT INTO project_items (id, projectId, name, dimensions, thickness, qtySet, quantity, unit, isBomLocked, isWorkflowLocked, flowType, warehouseQty, shippedQty, assemblyStats)
      VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
    `, [id, projectId, name, dimensions, thickness, qtySet, quantity, unit, isBomLocked ? 1 : 0, isWorkflowLocked ? 1 : 0, flowType, warehouseQty, shippedQty, JSON.stringify(assemblyStats)]);

    return this.findById(id);
  }

  static async update(id, itemData) {
    const { projectId, name, dimensions, thickness, qtySet, quantity, unit, isBomLocked, isWorkflowLocked, flowType, warehouseQty, shippedQty, assemblyStats } = itemData;
    await run(`
      UPDATE project_items
      SET projectId = ?, name = ?, dimensions = ?, thickness = ?, qtySet = ?, quantity = ?,
          unit = ?, isBomLocked = ?, isWorkflowLocked = ?, flowType = ?, warehouseQty = ?, shippedQty = ?, assemblyStats = ?
      WHERE id = ?
    `, [projectId, name, dimensions, thickness, qtySet, quantity, unit, isBomLocked ? 1 : 0, isWorkflowLocked ? 1 : 0, flowType, warehouseQty, shippedQty, JSON.stringify(assemblyStats), id]);

    return this.findById(id);
  }

  static async delete(id) {
    await run('DELETE FROM project_items WHERE id = ?', [id]);
    return true;
  }
}

module.exports = ProjectItem;