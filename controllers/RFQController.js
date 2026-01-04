const RFQ = require('../models/RFQ');
const { validationResult } = require('express-validator');

const RFQController = {
  // Get all RFQs
  async getAllRFQs(req, res) {
    try {
      const rfqs = await RFQ.findAll();
      res.json(rfqs);
    } catch (error) {
      console.error('Error fetching RFQs:', error);
      res.status(500).json({ error: 'Internal server error' });
    }
  },

  // Get RFQ by ID
  async getRFQById(req, res) {
    try {
      const { id } = req.params;
      const rfq = await RFQ.findById(id);
      
      if (!rfq) {
        return res.status(404).json({ error: 'RFQ not found' });
      }
      
      res.json(rfq);
    } catch (error) {
      console.error('Error fetching RFQ:', error);
      res.status(500).json({ error: 'Internal server error' });
    }
  },

  // Create a new RFQ
  async createRFQ(req, res) {
    try {
      const errors = validationResult(req);
      if (!errors.isEmpty()) {
        return res.status(400).json({ errors: errors.array() });
      }

      const rfq = await RFQ.create(req.body);
      res.status(201).json(rfq);
    } catch (error) {
      console.error('Error creating RFQ:', error);
      res.status(500).json({ error: 'Internal server error' });
    }
  },

  // Update an RFQ
  async updateRFQ(req, res) {
    try {
      const errors = validationResult(req);
      if (!errors.isEmpty()) {
        return res.status(400).json({ errors: errors.array() });
      }

      const { id } = req.params;
      const rfq = await RFQ.update(id, req.body);
      
      if (!rfq) {
        return res.status(404).json({ error: 'RFQ not found' });
      }
      
      res.json(rfq);
    } catch (error) {
      console.error('Error updating RFQ:', error);
      res.status(500).json({ error: 'Internal server error' });
    }
  },

  // Delete an RFQ
  async deleteRFQ(req, res) {
    try {
      const { id } = req.params;
      const deleted = await RFQ.delete(id);
      
      if (!deleted) {
        return res.status(404).json({ error: 'RFQ not found' });
      }
      
      res.status(204).send();
    } catch (error) {
      console.error('Error deleting RFQ:', error);
      res.status(500).json({ error: 'Internal server error' });
    }
  }
};

module.exports = RFQController;