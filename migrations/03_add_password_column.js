const { run, query } = require('../config/db');

const addPasswordColumn = async () => {
  try {
    // Check if password column exists
    const tableInfo = await query("PRAGMA table_info(users)");
    const hasPasswordColumn = tableInfo.rows.some(row => row.name === 'password');

    if (!hasPasswordColumn) {
      // Add password column to users table
      await run('ALTER TABLE users ADD COLUMN password TEXT NOT NULL DEFAULT ""');
      console.log('Password column added to users table');
    } else {
      console.log('Password column already exists');
    }
  } catch (error) {
    console.error('Error adding password column:', error);
    throw error;
  }
};

// Rollback function to remove the password column
const removePasswordColumn = async () => {
  try {
    // Note: SQLite doesn't support dropping columns directly
    // We'll need to recreate the table without the password column
    console.log('Removing password column from users table (SQLite limitation - recreating table)');

    // Get all users data
    const usersResult = await query('SELECT id, name, username, role, permissions FROM users');
    const users = usersResult.rows;

    // Drop the current users table
    await run('DROP TABLE users');

    // Recreate the users table without the password column
    await run(`
      CREATE TABLE users (
        id TEXT PRIMARY KEY,
        name TEXT NOT NULL,
        username TEXT NOT NULL UNIQUE,
        role TEXT NOT NULL CHECK (role IN ('ADMIN', 'OPERATOR', 'MANAGER')),
        permissions TEXT NOT NULL DEFAULT '{}'
      );
    `);

    // Insert the users data back without the password
    for (const user of users) {
      await run(`
        INSERT INTO users (id, name, username, role, permissions)
        VALUES (?, ?, ?, ?, ?)
      `, [user.id, user.name, user.username, user.role, user.permissions]);
    }

    console.log('Password column removed from users table');
  } catch (error) {
    console.error('Error removing password column:', error);
    throw error;
  }
};

// Export both up and down functions for the migration system
module.exports = {
  up: addPasswordColumn,
  down: removePasswordColumn,
  addPasswordColumn
};