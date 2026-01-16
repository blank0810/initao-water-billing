/**
 * ========================================
 * PRINT FORMS SYSTEM
 * Separate Application Form & MEEDO Contract printing utilities
 * ========================================
 */

// Import both print functions
import { printServiceApplicationForm } from './application-form.js';
import { printWaterServiceContract } from './meedo-contract.js';

// Export both functions
export { printServiceApplicationForm, printWaterServiceContract };

// Make them globally available
window.printServiceApplicationForm = printServiceApplicationForm;
window.printWaterServiceContract = printWaterServiceContract;
