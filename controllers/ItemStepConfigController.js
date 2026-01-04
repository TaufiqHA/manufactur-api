const ItemStepConfig = require('../models/ItemStepConfig');
const { validationResult } = require('express-validator');

const ItemStepConfigController = {
  // Get all item step configs
  async getAllItemStepConfigs(req, res) {
    try {
      const configs = await ItemStepConfig.findAll();
      res.json(configs);
    } catch (error) {
      console.error('Error fetching item step configs:', error);
      res.status(500).json({ error: 'Internal server error' });
    }
  },

  // Get item step config by ID
  async getItemStepConfigById(req, res) {
    try {
      const { id } = req.params;
      const config = await ItemStepConfig.findById(id);
      
      if (!config) {
        return res.status(404).json({ error: 'Item step config not found' });
      }
      
      res.json(config);
    } catch (error) {
      console.error('Error fetching item step config:', error);
      res.status(500).json({ error: 'Internal server error' });
    }
  },

  // Get item step configs by item ID
  async getItemStepConfigsByItemId(req, res) {
    try {
      const { itemId } = req.params;
      const configs = await ItemStepConfig.findByItemId(itemId);
      
      res.json(configs);
    } catch (error) {
      console.error('Error fetching item step configs by item ID:', error);
      res.status(500).json({ error: 'Internal server error' });
    }
  },

  // Create a new item step config
  async createItemStepConfig(req, res) {
    try {
      const errors = validationResult(req);
      if (!errors.isEmpty()) {
        return res.status(400).json({ errors: errors.array() });
      }

      const config = await ItemStepConfig.create(req.body);
      res.status(201).json(config);
    } catch (error) {
      console.error('Error creating item step config:', error);
      res.status(500).json({ error: 'Internal server error' });
    }
  },

  // Update an item step config
  async updateItemStepConfig(req, res) {
    try {
      const errors = validationResult(req);
      if (!errors.isEmpty()) {
        return res.status(400).json({ errors: errors.array() });
      }

      const { id } = req.params;
      const config = await ItemStepConfig.update(id, req.body);
      
      if (!config) {
        return res.status(404).json({ error: 'Item step config not found' });
      }
      
      res.json(config);
    } catch (error) {
      console.error('Error updating item step config:', error);
      res.status(500).json({ error: 'Internal server error' });
    }
  },

  // Delete an item step config
  async deleteItemStepConfig(req, res) {
    try {
      const { id } = req.params;
      const deleted = await ItemStepConfig.delete(id);
      
      if (!deleted) {
        return res.status(404).json({ error: 'Item step config not found' });
      }
      
      res.status(204).send();
    } catch (error) {
      console.error('Error deleting item step config:', error);
      res.status(500).json({ error: 'Internal server error' });
    }
  }
};

module.exports = ItemStepConfigController;