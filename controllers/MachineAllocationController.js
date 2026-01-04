const MachineAllocation = require('../models/MachineAllocation');
const { validationResult } = require('express-validator');

const MachineAllocationController = {
  // Get all machine allocations
  async getAllMachineAllocations(req, res) {
    try {
      const allocations = await MachineAllocation.findAll();
      res.json(allocations);
    } catch (error) {
      console.error('Error fetching machine allocations:', error);
      res.status(500).json({ error: 'Internal server error' });
    }
  },

  // Get machine allocation by ID
  async getMachineAllocationById(req, res) {
    try {
      const { id } = req.params;
      const allocation = await MachineAllocation.findById(id);
      
      if (!allocation) {
        return res.status(404).json({ error: 'Machine allocation not found' });
      }
      
      res.json(allocation);
    } catch (error) {
      console.error('Error fetching machine allocation:', error);
      res.status(500).json({ error: 'Internal server error' });
    }
  },

  // Get machine allocations by step config ID
  async getMachineAllocationsByStepConfigId(req, res) {
    try {
      const { stepConfigId } = req.params;
      const allocations = await MachineAllocation.findByStepConfigId(stepConfigId);
      
      res.json(allocations);
    } catch (error) {
      console.error('Error fetching machine allocations by step config ID:', error);
      res.status(500).json({ error: 'Internal server error' });
    }
  },

  // Create a new machine allocation
  async createMachineAllocation(req, res) {
    try {
      const errors = validationResult(req);
      if (!errors.isEmpty()) {
        return res.status(400).json({ errors: errors.array() });
      }

      const allocation = await MachineAllocation.create(req.body);
      res.status(201).json(allocation);
    } catch (error) {
      console.error('Error creating machine allocation:', error);
      res.status(500).json({ error: 'Internal server error' });
    }
  },

  // Update a machine allocation
  async updateMachineAllocation(req, res) {
    try {
      const errors = validationResult(req);
      if (!errors.isEmpty()) {
        return res.status(400).json({ errors: errors.array() });
      }

      const { id } = req.params;
      const allocation = await MachineAllocation.update(id, req.body);
      
      if (!allocation) {
        return res.status(404).json({ error: 'Machine allocation not found' });
      }
      
      res.json(allocation);
    } catch (error) {
      console.error('Error updating machine allocation:', error);
      res.status(500).json({ error: 'Internal server error' });
    }
  },

  // Delete a machine allocation
  async deleteMachineAllocation(req, res) {
    try {
      const { id } = req.params;
      const deleted = await MachineAllocation.delete(id);
      
      if (!deleted) {
        return res.status(404).json({ error: 'Machine allocation not found' });
      }
      
      res.status(204).send();
    } catch (error) {
      console.error('Error deleting machine allocation:', error);
      res.status(500).json({ error: 'Internal server error' });
    }
  }
};

module.exports = MachineAllocationController;