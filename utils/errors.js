// Custom error class for API errors
class ApiError extends Error {
  constructor(message, statusCode, details = null) {
    super(message);
    this.statusCode = statusCode;
    this.status = `${statusCode}`.startsWith('4') ? 'fail' : 'error';
    this.details = details;

    // Mark this error as operational (not a programming error)
    this.isOperational = true;

    Error.captureStackTrace(this, this.constructor);
  }
}

// Error for resource not found
class NotFoundError extends ApiError {
  constructor(resource = 'Resource') {
    super(`${resource} not found`, 404);
  }
}

// Error for validation failures
class ValidationError extends ApiError {
  constructor(message, details = null) {
    super(message || 'Validation failed', 400, details);
  }
}

// Error for unauthorized access
class UnauthorizedError extends ApiError {
  constructor(message = 'Unauthorized access') {
    super(message, 401);
  }
}

// Error for forbidden access
class ForbiddenError extends ApiError {
  constructor(message = 'Forbidden access') {
    super(message, 403);
  }
}

module.exports = {
  ApiError,
  NotFoundError,
  ValidationError,
  UnauthorizedError,
  ForbiddenError
};