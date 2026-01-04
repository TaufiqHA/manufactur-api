const Material = require('../models/Material');
const { validationResult } = require('express-validator');
const { formatMaterial, formatResponse, createPagination } = require('../utils/responseFormatter');

const MaterialController = {
  // Get all materials with pagination
  async getAllMaterials(req, res) {
    try {
      const page = parseInt(req.query.page) || 1;
      const limit = parseInt(req.query.limit) || 10;
      const offset = (page - 1) * limit;

      // For now, we'll fetch all materials and implement pagination in the response
      // In a real implementation, you would add LIMIT and OFFSET to your SQL query
      const allMaterials = await Material.findAll();
      const total = allMaterials.length;
      const materials = allMaterials.slice(offset, offset + limit);

      // Format each material to match frontend structure
      const formattedMaterials = materials.map(formatMaterial);

      const pagination = createPagination(page, limit, total);
      res.json(formatResponse(formattedMaterials, pagination));
    } catch (error) {
      console.error('Error fetching materials:', error);
      res.status(500).json({ error: 'Internal server error' });
    }
  },

  // Get material by ID
  async getMaterialById(req, res) {
    try {
      const { id } = req.params;
      const material = await Material.findById(id);

      if (!material) {
        return res.status(404).json({ error: 'Material not found' });
      }

      res.json(formatMaterial(material));
    } catch (error) {
      console.error('Error fetching material:', error);
      res.status(500).json({ error: 'Internal server error' });
    }
  },

  // Create a new material
  async createMaterial(req, res) {
    try {
      const errors = validationResult(req);
      if (!errors.isEmpty()) {
        return res.status(400).json({ errors: errors.array() });
      }

      const material = await Material.create(req.body);
      res.status(201).json(formatMaterial(material));
    } catch (error) {
      console.error('Error creating material:', error);
      res.status(500).json({ error: 'Internal server error' });
    }
  },

  // Update a material
  async updateMaterial(req, res) {
    try {
      const errors = validationResult(req);
      if (!errors.isEmpty()) {
        return res.status(400).json({ errors: errors.array() });
      }

      const { id } = req.params;
      const material = await Material.update(id, req.body);

      if (!material) {
        return res.status(404).json({ error: 'Material not found' });
      }

      res.json(formatMaterial(material));
    } catch (error) {
      console.error('Error updating material:', error);
      res.status(500).json({ error: 'Internal server error' });
    }
  },

  // Delete a material
  async deleteMaterial(req, res) {
    try {
      const { id } = req.params;
      const deleted = await Material.delete(id);

      if (!deleted) {
        return res.status(404).json({ error: 'Material not found' });
      }

      res.status(204).send();
    } catch (error) {
      console.error('Error deleting material:', error);
      res.status(500).json({ error: 'Internal server error' });
    }
  },

  // Adjust stock for a material
  async adjustStock(req, res) {
    try {
      const { id } = req.params;
      const { amount } = req.body;

      if (typeof amount !== 'number') {
        return res.status(400).json({ error: 'Amount must be a number' });
      }

      const material = await Material.adjustStock(id, amount);

      if (!material) {
        return res.status(404).json({ error: 'Material not found' });
      }

      res.json(formatMaterial(material));
    } catch (error) {
      console.error('Error adjusting stock:', error);
      res.status(500).json({ error: 'Internal server error' });
    }
  }
};

module.exports = MaterialController;