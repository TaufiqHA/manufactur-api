const DeliveryOrder = require('../models/DeliveryOrder');
const DeliveryOrderItem = require('../models/DeliveryOrderItem');
const { validationResult } = require('express-validator');
const { formatDeliveryOrder, formatResponse, createPagination } = require('../utils/responseFormatter');

const DeliveryOrderController = {
  // Get all delivery orders with pagination
  async getAllDeliveryOrders(req, res) {
    try {
      const page = parseInt(req.query.page) || 1;
      const limit = parseInt(req.query.limit) || 10;
      const offset = (page - 1) * limit;

      // For now, we'll fetch all delivery orders and implement pagination in the response
      // In a real implementation, you would add LIMIT and OFFSET to your SQL query
      const allDeliveryOrders = await DeliveryOrder.findAll();
      const total = allDeliveryOrders.length;
      const deliveryOrders = allDeliveryOrders.slice(offset, offset + limit);

      // Format each delivery order to match frontend structure
      const formattedDeliveryOrders = await Promise.all(
        deliveryOrders.map(async (order) => {
          // Get associated items for this delivery order
          const items = await DeliveryOrderItem.findByDeliveryOrderId(order.id);
          order.items = items;
          return formatDeliveryOrder(order);
        })
      );

      const pagination = createPagination(page, limit, total);
      res.json(formatResponse(formattedDeliveryOrders, pagination));
    } catch (error) {
      console.error('Error fetching delivery orders:', error);
      res.status(500).json({ error: 'Internal server error' });
    }
  },

  // Get delivery order by ID
  async getDeliveryOrderById(req, res) {
    try {
      const { id } = req.params;
      const deliveryOrder = await DeliveryOrder.findById(id);

      if (!deliveryOrder) {
        return res.status(404).json({ error: 'Delivery order not found' });
      }

      // Get associated items for this delivery order
      const items = await DeliveryOrderItem.findByDeliveryOrderId(id);
      deliveryOrder.items = items;

      res.json(formatDeliveryOrder(deliveryOrder));
    } catch (error) {
      console.error('Error fetching delivery order:', error);
      res.status(500).json({ error: 'Internal server error' });
    }
  },

  // Create a new delivery order
  async createDeliveryOrder(req, res) {
    try {
      const errors = validationResult(req);
      if (!errors.isEmpty()) {
        return res.status(400).json({ errors: errors.array() });
      }

      const deliveryOrder = await DeliveryOrder.create(req.body);
      res.status(201).json(formatDeliveryOrder(deliveryOrder));
    } catch (error) {
      console.error('Error creating delivery order:', error);
      res.status(500).json({ error: 'Internal server error' });
    }
  },

  // Update a delivery order
  async updateDeliveryOrder(req, res) {
    try {
      const errors = validationResult(req);
      if (!errors.isEmpty()) {
        return res.status(400).json({ errors: errors.array() });
      }

      const { id } = req.params;
      const deliveryOrder = await DeliveryOrder.update(id, req.body);

      if (!deliveryOrder) {
        return res.status(404).json({ error: 'Delivery order not found' });
      }

      res.json(formatDeliveryOrder(deliveryOrder));
    } catch (error) {
      console.error('Error updating delivery order:', error);
      res.status(500).json({ error: 'Internal server error' });
    }
  },

  // Delete a delivery order
  async deleteDeliveryOrder(req, res) {
    try {
      const { id } = req.params;
      const deleted = await DeliveryOrder.delete(id);

      if (!deleted) {
        return res.status(404).json({ error: 'Delivery order not found' });
      }

      res.status(204).send();
    } catch (error) {
      console.error('Error deleting delivery order:', error);
      res.status(500).json({ error: 'Internal server error' });
    }
  }
};

module.exports = DeliveryOrderController;