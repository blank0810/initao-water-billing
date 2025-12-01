// Enhanced Meter Inventory Data
export const meterInventory = [
    // Available Meters
    { mtr_id: 5001, mtr_serial: 'MTR-XYZ-12345', mtr_brand: 'AquaMeter', stat_id: 1, status: 'available' },
    { mtr_id: 5002, mtr_serial: 'MTR-ABC-67890', mtr_brand: 'FlowTech', stat_id: 1, status: 'available' },
    { mtr_id: 5006, mtr_serial: 'MTR-MNO-99887', mtr_brand: 'AquaMeter', stat_id: 1, status: 'available' },
    { mtr_id: 5009, mtr_serial: 'MTR-VWX-33221', mtr_brand: 'WaterPro', stat_id: 1, status: 'available' },
    { mtr_id: 5010, mtr_serial: 'MTR-YZA-44332', mtr_brand: 'FlowTech', stat_id: 1, status: 'available' },
    
    // Installed Meters
    { mtr_id: 5003, mtr_serial: 'MTR-DEF-11223', mtr_brand: 'AquaMeter', stat_id: 2, status: 'installed' },
    { mtr_id: 5004, mtr_serial: 'MTR-GHI-44556', mtr_brand: 'WaterPro', stat_id: 2, status: 'installed' },
    { mtr_id: 5008, mtr_serial: 'MTR-STU-22110', mtr_brand: 'FlowTech', stat_id: 2, status: 'installed' },
    
    // Faulty Meters (for replacement)
    { mtr_id: 5005, mtr_serial: 'MTR-JKL-77889', mtr_brand: 'FlowTech', stat_id: 3, status: 'faulty' },
    { mtr_id: 5011, mtr_serial: 'MTR-BCD-55443', mtr_brand: 'AquaMeter', stat_id: 3, status: 'faulty' },
    
    // Removed Meters
    { mtr_id: 5007, mtr_serial: 'MTR-PQR-55443', mtr_brand: 'WaterPro', stat_id: 4, status: 'removed', removed_at: '2024-01-10' },
    { mtr_id: 5015, mtr_serial: 'MTR-NOP-99001', mtr_brand: 'AquaMeter', stat_id: 4, status: 'removed', removed_at: '2023-12-15' },
    
    // Replacement Meters (available for replacement)
    { mtr_id: 5012, mtr_serial: 'MTR-EFG-66554', mtr_brand: 'AquaMeter', stat_id: 1, status: 'available' },
    { mtr_id: 5013, mtr_serial: 'MTR-HIJ-77665', mtr_brand: 'FlowTech', stat_id: 1, status: 'available' },
    { mtr_id: 5014, mtr_serial: 'MTR-KLM-88776', mtr_brand: 'WaterPro', stat_id: 1, status: 'available' }
];

// Meter Assignments with history
export const meterAssignments = [
    // Current Active Assignments
    { assignment_id: 301, connection_id: 1001, meter_id: 5003, installed_at: '2024-01-15', removed_at: null, install_read: 0.000, removal_read: null, technician: 'Juan Technician' },
    { assignment_id: 302, connection_id: 1002, meter_id: 5004, installed_at: '2024-01-10', removed_at: null, install_read: 0.000, removal_read: null, technician: 'Maria Installer' },
    { assignment_id: 303, connection_id: 1003, meter_id: 5005, installed_at: '2024-01-05', removed_at: null, install_read: 0.000, removal_read: null, technician: 'Pedro Technician' },
    { assignment_id: 304, connection_id: 1004, meter_id: 5008, installed_at: '2024-01-20', removed_at: null, install_read: 0.000, removal_read: null, technician: 'Ana Installer' },
    
    // Historical Assignments (removed/replaced)
    { assignment_id: 305, connection_id: 1003, meter_id: 5007, installed_at: '2023-06-01', removed_at: '2024-01-05', install_read: 0.000, removal_read: 450.500, technician: 'Carlos Technician', removal_reason: 'Faulty - replaced with MTR-JKL-77889' },
    { assignment_id: 306, connection_id: 1005, meter_id: 5015, installed_at: '2023-01-10', removed_at: '2023-12-15', install_read: 0.000, removal_read: 890.200, technician: 'Juan Technician', removal_reason: 'Meter malfunction' }
];

// Meter Readings with sequential data
export const meterReadings = [
    // Meter 5003 (Gelogo, Norben) - Connection 1001
    { reading_id: 2001, assignment_id: 301, period_id: 1, reading_date: '2024-02-01', reading_value: 12.500, is_estimated: 0, meter_reader_id: 10 },
    { reading_id: 2002, assignment_id: 301, period_id: 2, reading_date: '2024-03-01', reading_value: 25.300, is_estimated: 0, meter_reader_id: 10 },
    { reading_id: 2003, assignment_id: 301, period_id: 3, reading_date: '2024-04-01', reading_value: 38.750, is_estimated: 0, meter_reader_id: 11 },
    { reading_id: 2004, assignment_id: 301, period_id: 4, reading_date: '2024-05-01', reading_value: 52.100, is_estimated: 0, meter_reader_id: 10 },
    
    // Meter 5004 (Sayson, Sarah) - Connection 1002
    { reading_id: 2005, assignment_id: 302, period_id: 1, reading_date: '2024-02-01', reading_value: 8.750, is_estimated: 0, meter_reader_id: 11 },
    { reading_id: 2006, assignment_id: 302, period_id: 2, reading_date: '2024-03-01', reading_value: 18.200, is_estimated: 0, meter_reader_id: 11 },
    { reading_id: 2007, assignment_id: 302, period_id: 3, reading_date: '2024-04-01', reading_value: 27.900, is_estimated: 1, meter_reader_id: 12 },
    { reading_id: 2008, assignment_id: 302, period_id: 4, reading_date: '2024-05-01', reading_value: 37.450, is_estimated: 0, meter_reader_id: 11 },
    
    // Meter 5005 (Apora, Jose - Faulty) - Connection 1003
    { reading_id: 2009, assignment_id: 303, period_id: 1, reading_date: '2024-02-01', reading_value: 15.200, is_estimated: 0, meter_reader_id: 10 },
    { reading_id: 2010, assignment_id: 303, period_id: 2, reading_date: '2024-03-01', reading_value: 30.100, is_estimated: 0, meter_reader_id: 12 },
    { reading_id: 2011, assignment_id: 303, period_id: 3, reading_date: '2024-04-01', reading_value: 45.600, is_estimated: 1, meter_reader_id: 10 },
    { reading_id: 2012, assignment_id: 303, period_id: 4, reading_date: '2024-05-01', reading_value: 60.800, is_estimated: 1, meter_reader_id: 11 },
    
    // Meter 5008 (Ramos, Angela) - Connection 1004
    { reading_id: 2013, assignment_id: 304, period_id: 1, reading_date: '2024-02-01', reading_value: 5.300, is_estimated: 0, meter_reader_id: 12 },
    { reading_id: 2014, assignment_id: 304, period_id: 2, reading_date: '2024-03-01', reading_value: 11.100, is_estimated: 0, meter_reader_id: 12 },
    { reading_id: 2015, assignment_id: 304, period_id: 3, reading_date: '2024-04-01', reading_value: 16.750, is_estimated: 0, meter_reader_id: 10 },
    { reading_id: 2016, assignment_id: 304, period_id: 4, reading_date: '2024-05-01', reading_value: 22.900, is_estimated: 0, meter_reader_id: 12 },
    
    // Historical readings for removed meter 5007
    { reading_id: 2017, assignment_id: 305, period_id: 1, reading_date: '2023-07-01', reading_value: 50.000, is_estimated: 0, meter_reader_id: 10 },
    { reading_id: 2018, assignment_id: 305, period_id: 2, reading_date: '2023-08-01', reading_value: 100.500, is_estimated: 0, meter_reader_id: 11 },
    { reading_id: 2019, assignment_id: 305, period_id: 3, reading_date: '2023-09-01', reading_value: 150.200, is_estimated: 0, meter_reader_id: 10 },
    { reading_id: 2020, assignment_id: 305, period_id: 4, reading_date: '2023-10-01', reading_value: 200.800, is_estimated: 0, meter_reader_id: 12 },
    { reading_id: 2021, assignment_id: 305, period_id: 5, reading_date: '2023-11-01', reading_value: 250.300, is_estimated: 0, meter_reader_id: 11 },
    { reading_id: 2022, assignment_id: 305, period_id: 6, reading_date: '2023-12-01', reading_value: 300.100, is_estimated: 0, meter_reader_id: 10 },
    { reading_id: 2023, assignment_id: 305, period_id: 7, reading_date: '2024-01-05', reading_value: 450.500, is_estimated: 0, meter_reader_id: 10 }
];

// Meter Readers
export const meterReaders = [
    { mr_id: 10, mr_name: 'Alex Reader', stat_id: 1, assigned_area: 'Brgy. 1-3' },
    { mr_id: 11, mr_name: 'Maria Santos', stat_id: 1, assigned_area: 'Brgy. 4-6' },
    { mr_id: 12, mr_name: 'John Cruz', stat_id: 1, assigned_area: 'Brgy. 7-9' }
];

// Consumer/Connection Data
export const consumers = [
    { connection_id: 1001, name: 'Gelogo, Norben', location: 'Brgy. 1, Main St', meter_id: 5003, account_no: 'ACC-2024-1001' },
    { connection_id: 1002, name: 'Sayson, Sarah', location: 'Brgy. 2, Oak Ave', meter_id: 5004, account_no: 'ACC-2024-1002' },
    { connection_id: 1003, name: 'Apora, Jose', location: 'Brgy. 3, Pine Rd', meter_id: 5005, account_no: 'ACC-2024-1003' },
    { connection_id: 1004, name: 'Ramos, Angela', location: 'Brgy. 4, Elm St', meter_id: 5008, account_no: 'ACC-2024-1004' },
    { connection_id: 1005, name: 'Cruz, Manuel', location: 'Brgy. 5, Cedar Ave', meter_id: null, account_no: 'ACC-2024-1005' }
];

// Status mapping
export const statusMap = {
    1: 'Available',
    2: 'Installed',
    3: 'Faulty',
    4: 'Removed'
};
