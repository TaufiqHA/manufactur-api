const PurchaseOrder = require('../models/PurchaseOrder');
const { validationResult } = require('express-validator');

const PurchaseOrderController = {
  // Get all purchase orders
  async getAllPurchaseOrders(req, res) {
    try {
      const pos = await PurchaseOrder.findAll();
      res.json(pos);
    } catch (error) {
      console.error('Error fetching purchase orders:', error);
      res.status(500).json({ error: 'Internal server error' });
    }
  },

  // Get purchase order by ID
  async getPurchaseOrderById(req, res) {
    try {
      const { id } = req.params;
      const po = await PurchaseOrder.findById(id);
      
      if (!po) {
        return res.status(404).json({ error: 'Purchase order not found' });
      }
      
      res.json(po);
    } catch (error) {
      console.error('Error fetching purchase order:', error);
      res.status(500).json({ error: 'Internal server error' });
    }
  },

  // Create a new purchase order
  async createPurchaseOrder(req, res) {
    try {
      const errors = validationResult(req);
      if (!errors.isEmpty()) {
        return res.status(400).json({ errors: errors.array() });
      }

      const po = await PurchaseOrder.create(req.body);
      res.status(201).json(po);
    } catch (error) {
      console.error('Error creating purchase order:', error);
      res.status(500).json({ error: 'Internal server error' });
    }
  },

  // Update a purchase order
  async updatePurchaseOrder(req, res) {
    try {
      const errors = validationResult(req);
      if (!errors.isEmpty()) {
        return res.status(400).json({ errors: errors.array() });
      }

      const { id } = req.params;
      const po = await PurchaseOrder.update(id, req.body);
      
      if (!po) {
        return res.status(404).json({ error: 'Purchase order not found' });
      }
      
      res.json(po);
    } catch (error) {
      console.error('Error updating purchase order:', error);
      res.status(500).json({ error: 'Internal server error' });
    }
  },

  // Delete a purchase order
  async deletePurchaseOrder(req, res) {
    try {
      const { id } = req.params;
      const deleted = await PurchaseOrder.delete(id);
      
      if (!deleted) {
        return res.status(404).json({ error: 'Purchase order not found' });
      }
      
      res.status(204).send();
    } catch (error) {
      console.error('Error deleting purchase order:', error);
      res.status(500).json({ error: 'Internal server error' });
    }
  }
};

module.exports = PurchaseOrderController;