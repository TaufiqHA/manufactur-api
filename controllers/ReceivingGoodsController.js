const ReceivingGoods = require('../models/ReceivingGoods');
const { validationResult } = require('express-validator');

const ReceivingGoodsController = {
  // Get all receiving goods
  async getAllReceivingGoods(req, res) {
    try {
      const receivings = await ReceivingGoods.findAll();
      res.json(receivings);
    } catch (error) {
      console.error('Error fetching receiving goods:', error);
      res.status(500).json({ error: 'Internal server error' });
    }
  },

  // Get receiving goods by ID
  async getReceivingGoodsById(req, res) {
    try {
      const { id } = req.params;
      const receiving = await ReceivingGoods.findById(id);
      
      if (!receiving) {
        return res.status(404).json({ error: 'Receiving goods not found' });
      }
      
      res.json(receiving);
    } catch (error) {
      console.error('Error fetching receiving goods:', error);
      res.status(500).json({ error: 'Internal server error' });
    }
  },

  // Create new receiving goods
  async createReceivingGoods(req, res) {
    try {
      const errors = validationResult(req);
      if (!errors.isEmpty()) {
        return res.status(400).json({ errors: errors.array() });
      }

      const receiving = await ReceivingGoods.create(req.body);

      // The stock update is handled in the model's create method
      res.status(201).json(receiving);
    } catch (error) {
      console.error('Error creating receiving goods:', error);
      res.status(500).json({ error: 'Internal server error' });
    }
  },

  // Update receiving goods
  async updateReceivingGoods(req, res) {
    try {
      const errors = validationResult(req);
      if (!errors.isEmpty()) {
        return res.status(400).json({ errors: errors.array() });
      }

      const { id } = req.params;
      const receiving = await ReceivingGoods.update(id, req.body);
      
      if (!receiving) {
        return res.status(404).json({ error: 'Receiving goods not found' });
      }
      
      res.json(receiving);
    } catch (error) {
      console.error('Error updating receiving goods:', error);
      res.status(500).json({ error: 'Internal server error' });
    }
  },

  // Delete receiving goods
  async deleteReceivingGoods(req, res) {
    try {
      const { id } = req.params;
      const deleted = await ReceivingGoods.delete(id);
      
      if (!deleted) {
        return res.status(404).json({ error: 'Receiving goods not found' });
      }
      
      res.status(204).send();
    } catch (error) {
      console.error('Error deleting receiving goods:', error);
      res.status(500).json({ error: 'Internal server error' });
    }
  }
};

module.exports = ReceivingGoodsController;