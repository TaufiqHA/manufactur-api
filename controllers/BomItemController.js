const BomItem = require('../models/BomItem');
const { validationResult } = require('express-validator');

const BomItemController = {
  // Get all BOM items
  async getAllBomItems(req, res) {
    try {
      const bomItems = await BomItem.findAll();
      res.json(bomItems);
    } catch (error) {
      console.error('Error fetching BOM items:', error);
      res.status(500).json({ error: 'Internal server error' });
    }
  },

  // Get BOM item by ID
  async getBomItemById(req, res) {
    try {
      const { id } = req.params;
      const bomItem = await BomItem.findById(id);
      
      if (!bomItem) {
        return res.status(404).json({ error: 'BOM item not found' });
      }
      
      res.json(bomItem);
    } catch (error) {
      console.error('Error fetching BOM item:', error);
      res.status(500).json({ error: 'Internal server error' });
    }
  },

  // Get BOM items by item ID
  async getBomItemsByItemId(req, res) {
    try {
      const { itemId } = req.params;
      const bomItems = await BomItem.findByItemId(itemId);
      
      res.json(bomItems);
    } catch (error) {
      console.error('Error fetching BOM items by item ID:', error);
      res.status(500).json({ error: 'Internal server error' });
    }
  },

  // Create a new BOM item
  async createBomItem(req, res) {
    try {
      const errors = validationResult(req);
      if (!errors.isEmpty()) {
        return res.status(400).json({ errors: errors.array() });
      }

      const bomItem = await BomItem.create(req.body);
      res.status(201).json(bomItem);
    } catch (error) {
      console.error('Error creating BOM item:', error);
      res.status(500).json({ error: 'Internal server error' });
    }
  },

  // Update a BOM item
  async updateBomItem(req, res) {
    try {
      const errors = validationResult(req);
      if (!errors.isEmpty()) {
        return res.status(400).json({ errors: errors.array() });
      }

      const { id } = req.params;
      const bomItem = await BomItem.update(id, req.body);
      
      if (!bomItem) {
        return res.status(404).json({ error: 'BOM item not found' });
      }
      
      res.json(bomItem);
    } catch (error) {
      console.error('Error updating BOM item:', error);
      res.status(500).json({ error: 'Internal server error' });
    }
  },

  // Delete a BOM item
  async deleteBomItem(req, res) {
    try {
      const { id } = req.params;
      const deleted = await BomItem.delete(id);
      
      if (!deleted) {
        return res.status(404).json({ error: 'BOM item not found' });
      }
      
      res.status(204).send();
    } catch (error) {
      console.error('Error deleting BOM item:', error);
      res.status(500).json({ error: 'Internal server error' });
    }
  }
};

module.exports = BomItemController;