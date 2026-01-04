const fs = require('fs');
const path = require('path');
const { query, run } = require('../config/db');

// Migration rollback system to undo migrations
class RollbackRunner {
  constructor() {
    this.migrationsDir = path.join(__dirname);
    this.appliedMigrationsTable = 'applied_migrations';
  }

  // Get all applied migrations in reverse order
  async getAppliedMigrations() {
    try {
      const result = await query(`SELECT id, migration_name FROM ${this.appliedMigrationsTable} ORDER BY id DESC`);
      return result.rows;
    } catch (error) {
      console.error('Error getting applied migrations:', error);
      return [];
    }
  }

  // Unmark a migration as applied
  async unmarkApplied(migrationId) {
    try {
      await run(
        `DELETE FROM ${this.appliedMigrationsTable} WHERE id = ?`,
        [migrationId]
      );
    } catch (error) {
      console.error(`Error unmarking migration as applied: ${migrationId}`, error);
      throw error;
    }
  }

  // Rollback a single migration
  async rollbackMigration(migrationName) {
    console.log(`Rolling back migration: ${migrationName}`);
    
    try {
      // Dynamically import and run the rollback
      const migration = require(path.join(this.migrationsDir, migrationName));
      
      // Check if the migration has a down function
      if (typeof migration.down === 'function') {
        await migration.down();
        console.log(`Migration ${migrationName} rolled back successfully`);
        return true;
      } else {
        console.warn(`Migration file ${migrationName} does not have a down function for rollback`);
        return false;
      }
    } catch (error) {
      console.error(`Error rolling back migration ${migrationName}:`, error);
      throw error;
    }
  }

  // Rollback the last N migrations
  async rollbackMigrations(count = 1) {
    const appliedMigrations = await this.getAppliedMigrations();
    
    if (appliedMigrations.length === 0) {
      console.log('No migrations to rollback');
      return;
    }
    
    const migrationsToRollback = appliedMigrations.slice(0, count);
    console.log(`Rolling back ${migrationsToRollback.length} migrations`);
    
    for (const migration of migrationsToRollback) {
      const { id, migration_name } = migration;
      
      try {
        const success = await this.rollbackMigration(migration_name);
        if (success) {
          await this.unmarkApplied(id);
        }
      } catch (error) {
        console.error(`Failed to rollback migration ${migration_name}:`, error);
        break; // Stop rollback if one fails
      }
    }
    
    console.log('Rollback completed');
  }

  // Rollback all migrations
  async rollbackAllMigrations() {
    const appliedMigrations = await this.getAppliedMigrations();
    console.log(`Rolling back all ${appliedMigrations.length} migrations`);
    
    for (const migration of appliedMigrations) {
      const { id, migration_name } = migration;
      
      try {
        const success = await this.rollbackMigration(migration_name);
        if (success) {
          await this.unmarkApplied(id);
        }
      } catch (error) {
        console.error(`Failed to rollback migration ${migration_name}:`, error);
        break; // Stop rollback if one fails
      }
    }
    
    console.log('All migrations rolled back');
  }
}

module.exports = RollbackRunner;