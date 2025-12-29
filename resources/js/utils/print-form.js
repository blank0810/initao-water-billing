/**
 * ========================================
 * UNIFIED PRINT FORMS SYSTEM
 * Application Form & MEEDO Contract printing utilities
 * ========================================
 */

// Import both print systems
import { UnifiedPrintSystem } from './application-form.js';
import { MEEDOContractPrint } from './meedo-contract.js';

// Export both classes
export { UnifiedPrintSystem, MEEDOContractPrint };

// Make them globally available
window.UnifiedPrintSystem = UnifiedPrintSystem;
window.MEEDOContractPrint = MEEDOContractPrint;
