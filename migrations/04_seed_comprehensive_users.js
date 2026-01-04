const { run, query } = require('../config/db');
const bcrypt = require('bcryptjs');

const seedComprehensiveUsers = async () => {
  try {
    // Define different user types with appropriate permissions
    const usersToCreate = [
      {
        id: 'usr-admin-001',
        name: 'System Administrator',
        username: 'admin',
        role: 'ADMIN',
        permissions: {
          PROJECTS: { view: true, create: true, edit: true, delete: true },
          MATERIALS: { view: true, create: true, edit: true, delete: true },
          MACHINES: { view: true, create: true, edit: true, delete: true },
          USERS: { view: true, create: true, edit: true, delete: true },
          DASHBOARD: { view: true, create: false, edit: false, delete: false },
          REPORTS: { view: true, create: true, edit: true, delete: true },
          PROCUREMENT: { view: true, create: true, edit: true, delete: true },
          SJ: { view: true, create: true, edit: true, delete: true },
          WAREHOUSE: { view: true, create: true, edit: true, delete: true },
          EXECUTIVE: { view: true, create: false, edit: false, delete: false },
          BULK_ENTRY: { view: true, create: true, edit: true, delete: true }
        }
      },
      {
        id: 'usr-manager-001',
        name: 'Production Manager',
        username: 'manager',
        role: 'MANAGER',
        permissions: {
          PROJECTS: { view: true, create: true, edit: true, delete: false },
          MATERIALS: { view: true, create: true, edit: true, delete: false },
          MACHINES: { view: true, create: false, edit: false, delete: false },
          USERS: { view: false, create: false, edit: false, delete: false },
          DASHBOARD: { view: true, create: false, edit: false, delete: false },
          REPORTS: { view: true, create: true, edit: true, delete: false },
          PROCUREMENT: { view: true, create: false, edit: false, delete: false },
          SJ: { view: true, create: true, edit: true, delete: false },
          WAREHOUSE: { view: true, create: true, edit: true, delete: false },
          EXECUTIVE: { view: true, create: false, edit: false, delete: false },
          BULK_ENTRY: { view: true, create: true, edit: true, delete: false }
        }
      },
      {
        id: 'usr-operator-001',
        name: 'Production Operator',
        username: 'operator',
        role: 'OPERATOR',
        permissions: {
          PROJECTS: { view: true, create: false, edit: false, delete: false },
          MATERIALS: { view: true, create: false, edit: false, delete: false },
          MACHINES: { view: true, create: false, edit: false, delete: false },
          USERS: { view: false, create: false, edit: false, delete: false },
          DASHBOARD: { view: true, create: false, edit: false, delete: false },
          REPORTS: { view: false, create: false, edit: false, delete: false },
          PROCUREMENT: { view: false, create: false, edit: false, delete: false },
          SJ: { view: false, create: false, edit: false, delete: false },
          WAREHOUSE: { view: true, create: false, edit: false, delete: false },
          EXECUTIVE: { view: false, create: false, edit: false, delete: false },
          BULK_ENTRY: { view: true, create: false, edit: false, delete: false }
        }
      },
      {
        id: 'usr-operator-002',
        name: 'Assembly Operator',
        username: 'assembly',
        role: 'OPERATOR',
        permissions: {
          PROJECTS: { view: true, create: false, edit: false, delete: false },
          MATERIALS: { view: true, create: false, edit: false, delete: false },
          MACHINES: { view: true, create: false, edit: false, delete: false },
          USERS: { view: false, create: false, edit: false, delete: false },
          DASHBOARD: { view: true, create: false, edit: false, delete: false },
          REPORTS: { view: false, create: false, edit: false, delete: false },
          PROCUREMENT: { view: false, create: false, edit: false, delete: false },
          SJ: { view: false, create: false, edit: false, delete: false },
          WAREHOUSE: { view: true, create: false, edit: false, delete: false },
          EXECUTIVE: { view: false, create: false, edit: false, delete: false },
          BULK_ENTRY: { view: true, create: false, edit: false, delete: false }
        }
      },
      {
        id: 'usr-operator-003',
        name: 'Warehouse Operator',
        username: 'warehouse',
        role: 'OPERATOR',
        permissions: {
          PROJECTS: { view: true, create: false, edit: false, delete: false },
          MATERIALS: { view: true, create: false, edit: false, delete: false },
          MACHINES: { view: false, create: false, edit: false, delete: false },
          USERS: { view: false, create: false, edit: false, delete: false },
          DASHBOARD: { view: true, create: false, edit: false, delete: false },
          REPORTS: { view: false, create: false, edit: false, delete: false },
          PROCUREMENT: { view: false, create: false, edit: false, delete: false },
          SJ: { view: true, create: false, edit: false, delete: false },
          WAREHOUSE: { view: true, create: true, edit: true, delete: false },
          EXECUTIVE: { view: false, create: false, edit: false, delete: false },
          BULK_ENTRY: { view: true, create: false, edit: false, delete: false }
        }
      }
    ];

    // Create each user if they don't already exist
    for (const userData of usersToCreate) {
      // Check if user already exists
      const existingUser = await query('SELECT * FROM users WHERE username = ?', [userData.username]);

      if (existingUser.rows.length === 0) {
        // Hash the default password
        const password = 'password123'; // Default password for all users
        const hashedPassword = await bcrypt.hash(password, 10);

        await run(`
          INSERT INTO users (id, name, username, role, permissions, password)
          VALUES (?, ?, ?, ?, ?, ?)
        `, [
          userData.id,
          userData.name,
          userData.username,
          userData.role,
          JSON.stringify(userData.permissions),
          hashedPassword
        ]);

        console.log(`User ${userData.username} created successfully`);
        console.log(`Username: ${userData.username}, Password: password123`);
      } else {
        // Update existing user with password if it doesn't have one
        if (!existingUser.rows[0].password || existingUser.rows[0].password === '') {
          const password = 'password123';
          const hashedPassword = await bcrypt.hash(password, 10);
          await run(`
            UPDATE users
            SET password = ?
            WHERE username = ?
          `, [hashedPassword, userData.username]);
          console.log(`User ${userData.username} password updated`);
        }
        console.log(`User ${userData.username} already exists`);
      }
    }
  } catch (error) {
    console.error('Error seeding comprehensive users:', error);
    throw error;
  }
};

// Rollback function to remove seeded comprehensive users
const removeComprehensiveUsers = async () => {
  try {
    // Remove the comprehensive users we created
    const usernames = ['admin', 'manager', 'operator', 'assembly', 'warehouse'];
    const placeholders = usernames.map(() => '?').join(',');
    
    await run(`DELETE FROM users WHERE username IN (${placeholders})`, usernames);
    console.log('Comprehensive users removed successfully');
  } catch (error) {
    console.error('Error removing comprehensive users:', error);
    throw error;
  }
};

// Export both up and down functions for the migration system
module.exports = { 
  up: seedComprehensiveUsers,
  down: removeComprehensiveUsers,
  seedComprehensiveUsers 
};