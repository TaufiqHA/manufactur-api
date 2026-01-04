const ProductionLog = require('../models/ProductionLog');
const { validationResult } = require('express-validator');
const { formatProductionLog, formatResponse, createPagination } = require('../utils/responseFormatter');

const ProductionLogController = {
  // Get all production logs with pagination
  async getAllProductionLogs(req, res) {
    try {
      const page = parseInt(req.query.page) || 1;
      const limit = parseInt(req.query.limit) || 10;
      const offset = (page - 1) * limit;

      // For now, we'll fetch all logs and implement pagination in the response
      // In a real implementation, you would add LIMIT and OFFSET to your SQL query
      const allLogs = await ProductionLog.findAll();
      const total = allLogs.length;
      const logs = allLogs.slice(offset, offset + limit);

      // Format each log to match frontend structure
      const formattedLogs = logs.map(formatProductionLog);

      const pagination = createPagination(page, limit, total);
      res.json(formatResponse(formattedLogs, pagination));
    } catch (error) {
      console.error('Error fetching production logs:', error);
      res.status(500).json({ error: 'Internal server error' });
    }
  },

  // Get production log by ID
  async getProductionLogById(req, res) {
    try {
      const { id } = req.params;
      const log = await ProductionLog.findById(id);

      if (!log) {
        return res.status(404).json({ error: 'Production log not found' });
      }

      res.json(formatProductionLog(log));
    } catch (error) {
      console.error('Error fetching production log:', error);
      res.status(500).json({ error: 'Internal server error' });
    }
  },

  // Get production logs by task ID
  async getProductionLogsByTaskId(req, res) {
    try {
      const { taskId } = req.params;
      const logs = await ProductionLog.findByTaskId(taskId);

      // Format each log to match frontend structure
      const formattedLogs = logs.map(formatProductionLog);
      res.json(formattedLogs);
    } catch (error) {
      console.error('Error fetching production logs by task ID:', error);
      res.status(500).json({ error: 'Internal server error' });
    }
  },

  // Get production logs by project ID
  async getProductionLogsByProjectId(req, res) {
    try {
      const { projectId } = req.params;
      const logs = await ProductionLog.findByProjectId(projectId);

      // Format each log to match frontend structure
      const formattedLogs = logs.map(formatProductionLog);
      res.json(formattedLogs);
    } catch (error) {
      console.error('Error fetching production logs by project ID:', error);
      res.status(500).json({ error: 'Internal server error' });
    }
  },

  // Create a new production log
  async createProductionLog(req, res) {
    try {
      const errors = validationResult(req);
      if (!errors.isEmpty()) {
        return res.status(400).json({ errors: errors.array() });
      }

      const log = await ProductionLog.create(req.body);
      res.status(201).json(formatProductionLog(log));
    } catch (error) {
      console.error('Error creating production log:', error);
      res.status(500).json({ error: 'Internal server error' });
    }
  },

  // Update a production log
  async updateProductionLog(req, res) {
    try {
      const errors = validationResult(req);
      if (!errors.isEmpty()) {
        return res.status(400).json({ errors: errors.array() });
      }

      const { id } = req.params;
      const log = await ProductionLog.update(id, req.body);

      if (!log) {
        return res.status(404).json({ error: 'Production log not found' });
      }

      res.json(formatProductionLog(log));
    } catch (error) {
      console.error('Error updating production log:', error);
      res.status(500).json({ error: 'Internal server error' });
    }
  },

  // Delete a production log
  async deleteProductionLog(req, res) {
    try {
      const { id } = req.params;
      const deleted = await ProductionLog.delete(id);

      if (!deleted) {
        return res.status(404).json({ error: 'Production log not found' });
      }

      res.status(204).send();
    } catch (error) {
      console.error('Error deleting production log:', error);
      res.status(500).json({ error: 'Internal server error' });
    }
  }
};

module.exports = ProductionLogController;