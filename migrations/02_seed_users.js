const { run, query } = require('../config/db');
const bcrypt = require('bcryptjs');

const seedUsers = async () => {
  try {
    // Check if admin user already exists
    const existingAdmin = await query('SELECT * FROM users WHERE username = ?', ['admin']);

    if (existingAdmin.rows.length === 0) {
      // Hash the default password
      const password = 'admin123'; // Default password
      const hashedPassword = await bcrypt.hash(password, 10);

      // Create admin user
      const adminPermissions = {
        PROJECTS: { view: true, create: true, edit: true, delete: true },
        MATERIALS: { view: true, create: true, edit: true, delete: true },
        MACHINES: { view: true, create: true, edit: true, delete: true },
        USERS: { view: true, create: true, edit: true, delete: true },
        DASHBOARD: { view: true, create: false, edit: false, delete: false },
        REPORTS: { view: true, create: false, edit: false, delete: false },
        PROCUREMENT: { view: true, create: true, edit: true, delete: true },
        SJ: { view: true, create: true, edit: true, delete: true },
        WAREHOUSE: { view: true, create: true, edit: true, delete: true },
        EXECUTIVE: { view: true, create: false, edit: false, delete: false },
        BULK_ENTRY: { view: true, create: true, edit: true, delete: true }
      };

      await run(`
        INSERT INTO users (id, name, username, role, permissions, password)
        VALUES (?, ?, ?, ?, ?, ?)
      `, [
        'usr-admin-001',
        'Admin User',
        'admin',
        'ADMIN',
        JSON.stringify(adminPermissions),
        hashedPassword
      ]);

      console.log('Admin user created successfully');
      console.log('Username: admin');
      console.log('Password: admin123');
    } else {
      // Update existing admin user with password if it doesn't have one
      if (!existingAdmin.rows[0].password || existingAdmin.rows[0].password === '') {
        const password = 'admin123';
        const hashedPassword = await bcrypt.hash(password, 10);
        await run(`
          UPDATE users
          SET password = ?
          WHERE username = ?
        `, [hashedPassword, 'admin']);
        console.log('Admin user password updated');
      }
      console.log('Admin user already exists');
    }

    // Check if user user already exists
    const existingUser = await query('SELECT * FROM users WHERE username = ?', ['user']);

    if (existingUser.rows.length === 0) {
      // Hash the default password
      const password = 'user123'; // Default password for regular user
      const hashedPassword = await bcrypt.hash(password, 10);

      // Create regular user
      const userPermissions = {
        PROJECTS: { view: true, create: false, edit: false, delete: false },
        MATERIALS: { view: true, create: false, edit: false, delete: false },
        MACHINES: { view: true, create: false, edit: false, delete: false },
        USERS: { view: false, create: false, edit: false, delete: false },
        DASHBOARD: { view: true, create: false, edit: false, delete: false },
        REPORTS: { view: true, create: false, edit: false, delete: false },
        PROCUREMENT: { view: false, create: false, edit: false, delete: false },
        SJ: { view: false, create: false, edit: false, delete: false },
        WAREHOUSE: { view: false, create: false, edit: false, delete: false },
        EXECUTIVE: { view: true, create: false, edit: false, delete: false },
        BULK_ENTRY: { view: true, create: false, edit: false, delete: false }
      };

      await run(`
        INSERT INTO users (id, name, username, role, permissions, password)
        VALUES (?, ?, ?, ?, ?, ?)
      `, [
        'usr-user-001',
        'Regular User',
        'user',
        'OPERATOR',
        JSON.stringify(userPermissions),
        hashedPassword
      ]);

      console.log('Regular user created successfully');
      console.log('Username: user');
      console.log('Password: user123');
    } else {
      // Update existing user with password if it doesn't have one
      if (!existingUser.rows[0].password || existingUser.rows[0].password === '') {
        const password = 'user123';
        const hashedPassword = await bcrypt.hash(password, 10);
        await run(`
          UPDATE users
          SET password = ?
          WHERE username = ?
        `, [hashedPassword, 'user']);
        console.log('Regular user password updated');
      }
      console.log('Regular user already exists');
    }
  } catch (error) {
    console.error('Error seeding users:', error);
    throw error;
  }
};

// Rollback function to remove seeded users
const removeSeededUsers = async () => {
  try {
    // Remove the default admin and user accounts
    await run('DELETE FROM users WHERE username IN (?, ?)', ['admin', 'user']);
    console.log('Default users removed successfully');
  } catch (error) {
    console.error('Error removing seeded users:', error);
    throw error;
  }
};

// Export both up and down functions for the migration system
module.exports = {
  up: seedUsers,
  down: removeSeededUsers,
  seedUsers
};