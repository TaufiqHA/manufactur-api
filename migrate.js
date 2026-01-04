#!/usr/bin/env node

const MigrationRunner = require('./migrations/migrationRunner');
const RollbackRunner = require('./migrations/rollbackRunner');

async function run() {
  const args = process.argv.slice(2);
  const command = args[0];

  try {
    switch(command) {
      case 'migrate':
      case 'up':
        console.log('Running migrations...');
        const migrationRunner = new MigrationRunner();
        await migrationRunner.runMigrations();
        break;
        
      case 'rollback':
      case 'down':
        console.log('Rolling back migrations...');
        const count = parseInt(args[1]) || 1;
        const rollbackRunner = new RollbackRunner();
        await rollbackRunner.rollbackMigrations(count);
        break;
        
      case 'rollback:all':
        console.log('Rolling back all migrations...');
        const allRollbackRunner = new RollbackRunner();
        await allRollbackRunner.rollbackAllMigrations();
        break;
        
      case 'help':
      case '--help':
      default:
        console.log(`
Usage: node migrate.js [command]

Commands:
  migrate, up     Run pending migrations
  rollback, down  Rollback last migration (specify number to rollback multiple)
  rollback:all    Rollback all migrations
  help            Show this help message

Examples:
  node migrate.js migrate
  node migrate.js rollback
  node migrate.js rollback 3
  node migrate.js rollback:all
        `);
        break;
    }
  } catch (error) {
    console.error('Error running migration command:', error);
    process.exit(1);
  }
}

run();