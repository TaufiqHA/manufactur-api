const Machine = require('../models/Machine');
const { validationResult } = require('express-validator');

const MachineController = {
  // Get all machines
  async getAllMachines(req, res) {
    try {
      const machines = await Machine.findAll();
      res.json(machines);
    } catch (error) {
      console.error('Error fetching machines:', error);
      res.status(500).json({ error: 'Internal server error' });
    }
  },

  // Get machine by ID
  async getMachineById(req, res) {
    try {
      const { id } = req.params;
      const machine = await Machine.findById(id);
      
      if (!machine) {
        return res.status(404).json({ error: 'Machine not found' });
      }
      
      res.json(machine);
    } catch (error) {
      console.error('Error fetching machine:', error);
      res.status(500).json({ error: 'Internal server error' });
    }
  },

  // Create a new machine
  async createMachine(req, res) {
    try {
      const errors = validationResult(req);
      if (!errors.isEmpty()) {
        return res.status(400).json({ errors: errors.array() });
      }

      const machine = await Machine.create(req.body);
      res.status(201).json(machine);
    } catch (error) {
      console.error('Error creating machine:', error);
      res.status(500).json({ error: 'Internal server error' });
    }
  },

  // Update a machine
  async updateMachine(req, res) {
    try {
      const errors = validationResult(req);
      if (!errors.isEmpty()) {
        return res.status(400).json({ errors: errors.array() });
      }

      const { id } = req.params;
      const machine = await Machine.update(id, req.body);
      
      if (!machine) {
        return res.status(404).json({ error: 'Machine not found' });
      }
      
      res.json(machine);
    } catch (error) {
      console.error('Error updating machine:', error);
      res.status(500).json({ error: 'Internal server error' });
    }
  },

  // Delete a machine
  async deleteMachine(req, res) {
    try {
      const { id } = req.params;
      const deleted = await Machine.delete(id);
      
      if (!deleted) {
        return res.status(404).json({ error: 'Machine not found' });
      }
      
      res.status(204).send();
    } catch (error) {
      console.error('Error deleting machine:', error);
      res.status(500).json({ error: 'Internal server error' });
    }
  },

  // Toggle maintenance status
  async toggleMaintenance(req, res) {
    try {
      const { id } = req.params;
      const machine = await Machine.toggleMaintenance(id);
      
      if (!machine) {
        return res.status(404).json({ error: 'Machine not found' });
      }
      
      res.json(machine);
    } catch (error) {
      console.error('Error toggling maintenance:', error);
      res.status(500).json({ error: 'Internal server error' });
    }
  }
};

module.exports = MachineController;