const { query, run } = require('../config/db');

const updateStepStatsForExistingSubAssemblies = async () => {
  try {
    console.log('Updating stepStats for existing sub-assemblies...');
    
    // Get all sub-assemblies
    const result = await query('SELECT * FROM sub_assemblies');
    const subAssemblies = result.rows;
    
    for (const sa of subAssemblies) {
      // Parse processes and stepStats
      const processes = typeof sa.processes === 'string' ? JSON.parse(sa.processes) : sa.processes;
      const currentStepStats = typeof sa.stepStats === 'string' ? JSON.parse(sa.stepStats) : sa.stepStats;
      
      // Initialize stepStats with default values based on processes
      const allProcessSteps = ['POTONG', 'PLONG', 'PRESS', 'LAS', 'PHOSPHATING', 'CAT', 'PACKING'];
      const updatedStepStats = { ...currentStepStats };
      
      processes.forEach(step => {
        if (allProcessSteps.includes(step) && !updatedStepStats[step]) {
          updatedStepStats[step] = {
            produced: 0,
            available: 0
          };
        }
      });
      
      // For the first step in the process, set available to totalNeeded if it's the first step
      if (processes.length > 0) {
        const firstStep = processes[0];
        if (updatedStepStats[firstStep]) {
          updatedStepStats[firstStep].available = sa.totalNeeded || 0;
        }
      }
      
      // Update the sub-assembly with the new stepStats
      await run(`
        UPDATE sub_assemblies
        SET stepStats = ?
        WHERE id = ?
      `, [JSON.stringify(updatedStepStats), sa.id]);
    }
    
    console.log(`Updated stepStats for ${subAssemblies.length} sub-assemblies`);
  } catch (error) {
    console.error('Error updating stepStats for existing sub-assemblies:', error);
    throw error;
  }
};

// Rollback function - no specific rollback needed as we're just updating existing data
const rollbackUpdateStepStats = async () => {
  console.log('Rollback for stepStats update: No specific rollback needed');
};

module.exports = { 
  up: updateStepStatsForExistingSubAssemblies,
  down: rollbackUpdateStepStats,
  updateStepStatsForExistingSubAssemblies 
};