const Project = require('../models/Project');
const { validationResult } = require('express-validator');
const { formatProject, formatResponse, createPagination } = require('../utils/responseFormatter');

const ProjectController = {
  // Get all projects with pagination
  async getAllProjects(req, res) {
    try {
      const page = parseInt(req.query.page) || 1;
      const limit = parseInt(req.query.limit) || 10;
      const offset = (page - 1) * limit;

      // For now, we'll fetch all projects and implement pagination in the response
      // In a real implementation, you would add LIMIT and OFFSET to your SQL query
      const allProjects = await Project.findAll();
      const total = allProjects.length;
      const projects = allProjects.slice(offset, offset + limit);

      // Format each project to match frontend structure
      const formattedProjects = projects.map(formatProject);

      const pagination = createPagination(page, limit, total);
      res.json(formatResponse(formattedProjects, pagination));
    } catch (error) {
      console.error('Error fetching projects:', error);
      res.status(500).json({ error: 'Internal server error' });
    }
  },

  // Get project by ID
  async getProjectById(req, res) {
    try {
      const { id } = req.params;
      const project = await Project.findById(id);

      if (!project) {
        return res.status(404).json({ error: 'Project not found' });
      }

      res.json(formatProject(project));
    } catch (error) {
      console.error('Error fetching project:', error);
      res.status(500).json({ error: 'Internal server error' });
    }
  },

  // Create a new project
  async createProject(req, res) {
    try {
      const errors = validationResult(req);
      if (!errors.isEmpty()) {
        return res.status(400).json({ errors: errors.array() });
      }

      const project = await Project.create(req.body);
      res.status(201).json(formatProject(project));
    } catch (error) {
      console.error('Error creating project:', error);
      res.status(500).json({ error: 'Internal server error' });
    }
  },

  // Update a project
  async updateProject(req, res) {
    try {
      const errors = validationResult(req);
      if (!errors.isEmpty()) {
        return res.status(400).json({ errors: errors.array() });
      }

      const { id } = req.params;
      const project = await Project.update(id, req.body);

      if (!project) {
        return res.status(404).json({ error: 'Project not found' });
      }

      res.json(formatProject(project));
    } catch (error) {
      console.error('Error updating project:', error);
      res.status(500).json({ error: 'Internal server error' });
    }
  },

  // Delete a project
  async deleteProject(req, res) {
    try {
      const { id } = req.params;
      const deleted = await Project.delete(id);

      if (!deleted) {
        return res.status(404).json({ error: 'Project not found' });
      }

      res.status(204).send();
    } catch (error) {
      console.error('Error deleting project:', error);
      res.status(500).json({ error: 'Internal server error' });
    }
  }
};

module.exports = ProjectController;