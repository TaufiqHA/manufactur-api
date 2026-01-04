const fs = require('fs');
const path = require('path');
const { query, run } = require('../config/db');

// Migration runner to execute migrations in order
class MigrationRunner {
  constructor() {
    this.migrationsDir = path.join(__dirname);
    this.appliedMigrationsTable = 'applied_migrations';
  }

  // Initialize the applied migrations table
  async init() {
    try {
      await run(`
        CREATE TABLE IF NOT EXISTS ${this.appliedMigrationsTable} (
          id INTEGER PRIMARY KEY AUTOINCREMENT,
          migration_name TEXT NOT NULL UNIQUE,
          executed_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP
        )
      `);
      console.log('Applied migrations table created successfully');
    } catch (error) {
      console.error('Error initializing migration table:', error);
      throw error;
    }
  }

  // Get all migration files from the migrations directory
  getMigrationFiles() {
    const files = fs.readdirSync(this.migrationsDir);
    return files
      .filter(file => file.endsWith('.js') && file !== 'migrationRunner.js')
      .sort(); // Sort to ensure migrations run in order
  }

  // Get list of already applied migrations
  async getAppliedMigrations() {
    try {
      const result = await query(`SELECT migration_name FROM ${this.appliedMigrationsTable} ORDER BY id`);
      return result.rows.map(row => row.migration_name);
    } catch (error) {
      console.error('Error getting applied migrations:', error);
      return [];
    }
  }

  // Mark a migration as applied
  async markApplied(migrationName) {
    try {
      await run(
        `INSERT INTO ${this.appliedMigrationsTable} (migration_name) VALUES (?)`,
        [migrationName]
      );
    } catch (error) {
      console.error(`Error marking migration as applied: ${migrationName}`, error);
      throw error;
    }
  }

  // Execute all pending migrations
  async runMigrations() {
    await this.init();
    
    const migrationFiles = this.getMigrationFiles();
    const appliedMigrations = await this.getAppliedMigrations();
    
    console.log(`Found ${migrationFiles.length} migration files`);
    console.log(`Already applied: ${appliedMigrations.length} migrations`);
    
    for (const file of migrationFiles) {
      if (!appliedMigrations.includes(file)) {
        console.log(`Running migration: ${file}`);
        
        try {
          // Dynamically import and run the migration
          const migration = require(path.join(this.migrationsDir, file));
          
          // Check if the migration has an up function
          if (typeof migration.up === 'function') {
            await migration.up();
          } else if (typeof migration.createTables === 'function') {
            // For backward compatibility with existing migrations
            await migration.createTables();
          } else if (typeof migration.seedUsers === 'function') {
            // For backward compatibility with existing migrations
            await migration.seedUsers();
          } else if (typeof migration.addPasswordColumn === 'function') {
            // For backward compatibility with existing migrations
            await migration.addPasswordColumn();
          } else {
            console.warn(`Migration file ${file} does not have a recognized function`);
            continue;
          }
          
          // Mark migration as applied
          await this.markApplied(file);
          console.log(`Migration ${file} applied successfully`);
        } catch (error) {
          console.error(`Error running migration ${file}:`, error);
          throw error;
        }
      } else {
        console.log(`Skipping already applied migration: ${file}`);
      }
    }
    
    console.log('All migrations completed successfully');
  }
}

module.exports = MigrationRunner;