// Error handling middleware
const errorHandler = (err, req, res, next) => {
  console.error(err.stack);
  
  // Handle validation errors from express-validator
  if (err.validation) {
    return res.status(400).json({ 
      error: 'Validation Error',
      details: err.validation 
    });
  }
  
  // Handle specific error types
  if (err.name === 'ValidationError') {
    return res.status(400).json({ 
      error: 'Validation Error',
      details: err.message 
    });
  }
  
  if (err.name === 'CastError') {
    return res.status(400).json({ 
      error: 'Invalid ID format',
      details: 'The provided ID format is invalid' 
    });
  }
  
  // Default error response
  res.status(500).json({ 
    error: 'Internal Server Error',
    details: process.env.NODE_ENV === 'development' ? err.message : undefined
  });
};

// Not found middleware
const notFound = (req, res, next) => {
  res.status(404).json({ 
    error: 'Route not found',
    path: req.originalUrl 
  });
};

module.exports = {
  errorHandler,
  notFound
};