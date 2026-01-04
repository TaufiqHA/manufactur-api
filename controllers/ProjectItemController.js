const ProjectItem = require('../models/ProjectItem');
const { validationResult } = require('express-validator');

const ProjectItemController = {
  // Get all project items
  async getAllProjectItems(req, res) {
    try {
      const items = await ProjectItem.findAll();
      res.json(items);
    } catch (error) {
      console.error('Error fetching project items:', error);
      res.status(500).json({ error: 'Internal server error' });
    }
  },

  // Get project item by ID
  async getProjectItemById(req, res) {
    try {
      const { id } = req.params;
      const item = await ProjectItem.findById(id);
      
      if (!item) {
        return res.status(404).json({ error: 'Project item not found' });
      }
      
      res.json(item);
    } catch (error) {
      console.error('Error fetching project item:', error);
      res.status(500).json({ error: 'Internal server error' });
    }
  },

  // Get project items by project ID
  async getProjectItemsByProjectId(req, res) {
    try {
      const { projectId } = req.params;
      const items = await ProjectItem.findByProjectId(projectId);
      
      res.json(items);
    } catch (error) {
      console.error('Error fetching project items by project ID:', error);
      res.status(500).json({ error: 'Internal server error' });
    }
  },

  // Create a new project item
  async createProjectItem(req, res) {
    try {
      const errors = validationResult(req);
      if (!errors.isEmpty()) {
        return res.status(400).json({ errors: errors.array() });
      }

      const item = await ProjectItem.create(req.body);
      res.status(201).json(item);
    } catch (error) {
      console.error('Error creating project item:', error);
      res.status(500).json({ error: 'Internal server error' });
    }
  },

  // Update a project item
  async updateProjectItem(req, res) {
    try {
      const errors = validationResult(req);
      if (!errors.isEmpty()) {
        return res.status(400).json({ errors: errors.array() });
      }

      const { id } = req.params;
      const item = await ProjectItem.update(id, req.body);
      
      if (!item) {
        return res.status(404).json({ error: 'Project item not found' });
      }
      
      res.json(item);
    } catch (error) {
      console.error('Error updating project item:', error);
      res.status(500).json({ error: 'Internal server error' });
    }
  },

  // Delete a project item
  async deleteProjectItem(req, res) {
    try {
      const { id } = req.params;
      const deleted = await ProjectItem.delete(id);
      
      if (!deleted) {
        return res.status(404).json({ error: 'Project item not found' });
      }
      
      res.status(204).send();
    } catch (error) {
      console.error('Error deleting project item:', error);
      res.status(500).json({ error: 'Internal server error' });
    }
  }
};

module.exports = ProjectItemController;