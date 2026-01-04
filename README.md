# GondolaFlow MES Backend API

This is the backend API for the GondolaFlow Manufacturing Execution System (MES), designed to work with the existing React frontend that currently uses mock data.

## Features

- Full CRUD operations for all MES entities
- JWT-based authentication
- Input validation
- Error handling
- Response formatting to match frontend expectations
- Pagination for list endpoints
- PostgreSQL database integration

## API Structure

The API follows the same structure as the frontend mock data, ensuring 100% compatibility:

- Projects and Project Items
- Materials and Inventory Management
- Machines and Production Equipment
- Tasks and Work Orders
- Production Logs
- Users and Permissions
- Procurement (RFQs, Purchase Orders, Receiving)
- Delivery Orders
- Sub-assemblies and BOMs

## Getting Started

### Prerequisites

- Node.js (v14 or higher)
- PostgreSQL database
- npm or yarn package manager

### Installation

1. Clone the repository:
   ```bash
   git clone <repository-url>
   cd gondolaflow-mes/backend
   ```

2. Install dependencies:
   ```bash
   npm install
   ```

3. Set up environment variables:
   Create a `.env` file in the backend directory with the following:
   ```
   DB_HOST=localhost
   DB_PORT=5432
   DB_NAME=gondolaflow_dev
   DB_USER=postgres
   DB_PASSWORD=your_password
   JWT_SECRET=your_jwt_secret
   PORT=5000
   ```

4. Run the application:
   ```bash
   npm start
   # or for development with auto-restart:
   npm run dev
   ```

## API Endpoints

All endpoints are documented in [example_requests.md](./example_requests.md)

## Database Schema

The database schema is designed to match the frontend data structures exactly. See [database_schema.md](./database_schema.md) for details.

## Architecture

- **Controllers**: Handle HTTP requests and responses
- **Models**: Interact with the database
- **Routes**: Define API endpoints
- **Middleware**: Handle validation, authentication, and error handling
- **Utils**: Helper functions for response formatting and error handling

## Compatibility

This backend is designed to be a drop-in replacement for the frontend's mock data system. The API responses match the frontend's expected data structures exactly, requiring no changes to the React components or state management.

## Error Handling

The API includes comprehensive error handling with appropriate HTTP status codes and descriptive error messages.

## Security

- Input validation using express-validator
- JWT-based authentication
- Rate limiting to prevent abuse
- Helmet for HTTP header security
- CORS configuration

## Testing

To run tests:
```bash
npm test
```

## Deployment

For production deployment:
1. Set environment variables appropriately
2. Ensure PostgreSQL is properly configured and secured
3. Use a process manager like PM2 for production
4. Set up a reverse proxy (e.g., Nginx)