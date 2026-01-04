const Supplier = require('../models/Supplier');
const { validationResult } = require('express-validator');

const SupplierController = {
  // Get all suppliers
  async getAllSuppliers(req, res) {
    try {
      const suppliers = await Supplier.findAll();
      res.json(suppliers);
    } catch (error) {
      console.error('Error fetching suppliers:', error);
      res.status(500).json({ error: 'Internal server error' });
    }
  },

  // Get supplier by ID
  async getSupplierById(req, res) {
    try {
      const { id } = req.params;
      const supplier = await Supplier.findById(id);
      
      if (!supplier) {
        return res.status(404).json({ error: 'Supplier not found' });
      }
      
      res.json(supplier);
    } catch (error) {
      console.error('Error fetching supplier:', error);
      res.status(500).json({ error: 'Internal server error' });
    }
  },

  // Create a new supplier
  async createSupplier(req, res) {
    try {
      const errors = validationResult(req);
      if (!errors.isEmpty()) {
        return res.status(400).json({ errors: errors.array() });
      }

      const supplier = await Supplier.create(req.body);
      res.status(201).json(supplier);
    } catch (error) {
      console.error('Error creating supplier:', error);
      res.status(500).json({ error: 'Internal server error' });
    }
  },

  // Update a supplier
  async updateSupplier(req, res) {
    try {
      const errors = validationResult(req);
      if (!errors.isEmpty()) {
        return res.status(400).json({ errors: errors.array() });
      }

      const { id } = req.params;
      const supplier = await Supplier.update(id, req.body);
      
      if (!supplier) {
        return res.status(404).json({ error: 'Supplier not found' });
      }
      
      res.json(supplier);
    } catch (error) {
      console.error('Error updating supplier:', error);
      res.status(500).json({ error: 'Internal server error' });
    }
  },

  // Delete a supplier
  async deleteSupplier(req, res) {
    try {
      const { id } = req.params;
      const deleted = await Supplier.delete(id);
      
      if (!deleted) {
        return res.status(404).json({ error: 'Supplier not found' });
      }
      
      res.status(204).send();
    } catch (error) {
      console.error('Error deleting supplier:', error);
      res.status(500).json({ error: 'Internal server error' });
    }
  }
};

module.exports = SupplierController;