const Task = require('../models/Task');
const { validationResult } = require('express-validator');
const { formatTask, formatResponse, createPagination } = require('../utils/responseFormatter');

const TaskController = {
  // Get all tasks with pagination
  async getAllTasks(req, res) {
    try {
      const page = parseInt(req.query.page) || 1;
      const limit = parseInt(req.query.limit) || 10;
      const offset = (page - 1) * limit;

      // For now, we'll fetch all tasks and implement pagination in the response
      // In a real implementation, you would add LIMIT and OFFSET to your SQL query
      const allTasks = await Task.findAll();
      const total = allTasks.length;
      const tasks = allTasks.slice(offset, offset + limit);

      // Format each task to match frontend structure
      const formattedTasks = tasks.map(formatTask);

      const pagination = createPagination(page, limit, total);
      res.json(formatResponse(formattedTasks, pagination));
    } catch (error) {
      console.error('Error fetching tasks:', error);
      res.status(500).json({ error: 'Internal server error' });
    }
  },

  // Get task by ID
  async getTaskById(req, res) {
    try {
      const { id } = req.params;
      const task = await Task.findById(id);

      if (!task) {
        return res.status(404).json({ error: 'Task not found' });
      }

      res.json(formatTask(task));
    } catch (error) {
      console.error('Error fetching task:', error);
      res.status(500).json({ error: 'Internal server error' });
    }
  },

  // Get tasks by project ID
  async getTasksByProjectId(req, res) {
    try {
      const { projectId } = req.params;
      const tasks = await Task.findByProjectId(projectId);

      // Format each task to match frontend structure
      const formattedTasks = tasks.map(formatTask);
      res.json(formattedTasks);
    } catch (error) {
      console.error('Error fetching tasks by project ID:', error);
      res.status(500).json({ error: 'Internal server error' });
    }
  },

  // Get tasks by item ID
  async getTasksByItemId(req, res) {
    try {
      const { itemId } = req.params;
      const tasks = await Task.findByItemId(itemId);

      // Format each task to match frontend structure
      const formattedTasks = tasks.map(formatTask);
      res.json(formattedTasks);
    } catch (error) {
      console.error('Error fetching tasks by item ID:', error);
      res.status(500).json({ error: 'Internal server error' });
    }
  },

  // Create a new task
  async createTask(req, res) {
    try {
      const errors = validationResult(req);
      if (!errors.isEmpty()) {
        return res.status(400).json({ errors: errors.array() });
      }

      const task = await Task.create(req.body);
      res.status(201).json(formatTask(task));
    } catch (error) {
      console.error('Error creating task:', error);
      res.status(500).json({ error: 'Internal server error' });
    }
  },

  // Update a task
  async updateTask(req, res) {
    try {
      const errors = validationResult(req);
      if (!errors.isEmpty()) {
        return res.status(400).json({ errors: errors.array() });
      }

      const { id } = req.params;
      const task = await Task.update(id, req.body);

      if (!task) {
        return res.status(404).json({ error: 'Task not found' });
      }

      res.json(formatTask(task));
    } catch (error) {
      console.error('Error updating task:', error);
      res.status(500).json({ error: 'Internal server error' });
    }
  },

  // Delete a task
  async deleteTask(req, res) {
    try {
      const { id } = req.params;
      const deleted = await Task.delete(id);

      if (!deleted) {
        return res.status(404).json({ error: 'Task not found' });
      }

      res.status(204).send();
    } catch (error) {
      console.error('Error deleting task:', error);
      res.status(500).json({ error: 'Internal server error' });
    }
  }
};

module.exports = TaskController;