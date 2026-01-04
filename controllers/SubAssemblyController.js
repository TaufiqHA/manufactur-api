const SubAssembly = require('../models/SubAssembly');
const { validationResult } = require('express-validator');

const SubAssemblyController = {
  // Get all sub-assemblies
  async getAllSubAssemblies(req, res) {
    try {
      const subAssemblies = await SubAssembly.findAll();
      res.json(subAssemblies);
    } catch (error) {
      console.error('Error fetching sub-assemblies:', error);
      res.status(500).json({ error: 'Internal server error' });
    }
  },

  // Get sub-assembly by ID
  async getSubAssemblyById(req, res) {
    try {
      const { id } = req.params;
      const subAssembly = await SubAssembly.findById(id);
      
      if (!subAssembly) {
        return res.status(404).json({ error: 'Sub-assembly not found' });
      }
      
      res.json(subAssembly);
    } catch (error) {
      console.error('Error fetching sub-assembly:', error);
      res.status(500).json({ error: 'Internal server error' });
    }
  },

  // Get sub-assemblies by item ID
  async getSubAssembliesByItemId(req, res) {
    try {
      const { itemId } = req.params;
      const subAssemblies = await SubAssembly.findByItemId(itemId);
      
      res.json(subAssemblies);
    } catch (error) {
      console.error('Error fetching sub-assemblies by item ID:', error);
      res.status(500).json({ error: 'Internal server error' });
    }
  },

  // Create a new sub-assembly
  async createSubAssembly(req, res) {
    try {
      const errors = validationResult(req);
      if (!errors.isEmpty()) {
        return res.status(400).json({ errors: errors.array() });
      }

      const subAssembly = await SubAssembly.create(req.body);
      res.status(201).json(subAssembly);
    } catch (error) {
      console.error('Error creating sub-assembly:', error);
      res.status(500).json({ error: 'Internal server error' });
    }
  },

  // Update a sub-assembly
  async updateSubAssembly(req, res) {
    try {
      const errors = validationResult(req);
      if (!errors.isEmpty()) {
        return res.status(400).json({ errors: errors.array() });
      }

      const { id } = req.params;
      const subAssembly = await SubAssembly.update(id, req.body);
      
      if (!subAssembly) {
        return res.status(404).json({ error: 'Sub-assembly not found' });
      }
      
      res.json(subAssembly);
    } catch (error) {
      console.error('Error updating sub-assembly:', error);
      res.status(500).json({ error: 'Internal server error' });
    }
  },

  // Delete a sub-assembly
  async deleteSubAssembly(req, res) {
    try {
      const { id } = req.params;
      const deleted = await SubAssembly.delete(id);

      if (!deleted) {
        return res.status(404).json({ error: 'Sub-assembly not found' });
      }

      res.status(204).send();
    } catch (error) {
      console.error('Error deleting sub-assembly:', error);
      res.status(500).json({ error: 'Internal server error' });
    }
  },

  // Lock sub-assemblies by item ID
  async lockSubAssembliesByItemId(req, res) {
    try {
      const errors = validationResult(req);
      if (!errors.isEmpty()) {
        return res.status(400).json({ errors: errors.array() });
      }

      const { itemId } = req.params;
      const { isLocked } = req.body;

      // Validate that isLocked is provided
      if (typeof isLocked !== 'boolean') {
        return res.status(400).json({ error: 'isLocked field is required and must be a boolean' });
      }

      const updatedSubAssemblies = await SubAssembly.updateByItemId(itemId, { isLocked });

      if (updatedSubAssemblies.length === 0) {
        return res.status(404).json({ error: 'No sub-assemblies found for the given item ID' });
      }

      res.json({
        message: `Sub-assemblies for item ${itemId} ${isLocked ? 'locked' : 'unlocked'} successfully`,
        count: updatedSubAssemblies.length,
        subAssemblies: updatedSubAssemblies
      });
    } catch (error) {
      console.error('Error updating sub-assembly locks by item ID:', error);
      res.status(500).json({ error: 'Internal server error' });
    }
  }
};

module.exports = SubAssemblyController;