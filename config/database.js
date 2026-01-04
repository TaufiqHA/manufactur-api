require('dotenv').config();

module.exports = {
  development: {
    storage: process.env.DB_PATH || './database.sqlite',
    dialect: 'sqlite',
  },
  production: {
    storage: process.env.DB_PATH || './database.sqlite',
    dialect: 'sqlite',
  }
};