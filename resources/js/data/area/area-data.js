// ============================================
// GEOGRAPHICAL AREA MANAGEMENT - MASTER DATA
// ============================================

console.log('Loading area management module...');

// ============================================
// STATUS CONSTANTS
// ============================================

const AREA_STATUSES = {
    ACTIVE: 1,
    INACTIVE: 0
};

// ============================================
// PROVINCE DATA
// ============================================

const provinces = [
    { prov_id: 1, prov_desc: 'Misamis Oriental', stat_id: AREA_STATUSES.ACTIVE },
    { prov_id: 2, prov_desc: 'Misamis Occidental', stat_id: AREA_STATUSES.ACTIVE }
];

// ============================================
// TOWN/MUNICIPALITY DATA
// ============================================

const towns = [
    { t_id: 1, t_desc: 'Initao', prov_id: 1, stat_id: AREA_STATUSES.ACTIVE },
    { t_id: 2, t_desc: 'Cagayan de Oro', prov_id: 1, stat_id: AREA_STATUSES.ACTIVE },
    { t_id: 3, t_desc: 'Jasaan', prov_id: 1, stat_id: AREA_STATUSES.ACTIVE }
];

// ============================================
// BARANGAY DATA
// ============================================

const barangays = [
    { b_id: 1, b_desc: 'Poblacion', t_id: 1, stat_id: AREA_STATUSES.ACTIVE },
    { b_id: 2, b_desc: 'San Roque', t_id: 1, stat_id: AREA_STATUSES.ACTIVE },
    { b_id: 3, b_desc: 'Riverside', t_id: 1, stat_id: AREA_STATUSES.ACTIVE },
    { b_id: 4, b_desc: 'Centro', t_id: 1, stat_id: AREA_STATUSES.ACTIVE },
    { b_id: 5, b_desc: 'Maharlika', t_id: 1, stat_id: AREA_STATUSES.ACTIVE },
    { b_id: 6, b_desc: 'Dampol', t_id: 1, stat_id: AREA_STATUSES.ACTIVE }
];

// ============================================
// PUROK DATA
// ============================================

const puroks = [
    { p_id: 1, p_desc: 'Purok 1', b_id: 1, stat_id: AREA_STATUSES.ACTIVE },
    { p_id: 2, p_desc: 'Purok 2', b_id: 1, stat_id: AREA_STATUSES.ACTIVE },
    { p_id: 3, p_desc: 'Purok 3', b_id: 1, stat_id: AREA_STATUSES.ACTIVE },
    { p_id: 4, p_desc: 'Purok 4', b_id: 1, stat_id: AREA_STATUSES.ACTIVE },
    { p_id: 5, p_desc: 'Purok 5', b_id: 1, stat_id: AREA_STATUSES.ACTIVE },
    { p_id: 6, p_desc: 'Purok 6', b_id: 1, stat_id: AREA_STATUSES.ACTIVE },
    { p_id: 7, p_desc: 'Purok 1', b_id: 2, stat_id: AREA_STATUSES.ACTIVE },
    { p_id: 8, p_desc: 'Purok 2', b_id: 2, stat_id: AREA_STATUSES.ACTIVE },
    { p_id: 9, p_desc: 'Purok 1', b_id: 3, stat_id: AREA_STATUSES.ACTIVE },
    { p_id: 10, p_desc: 'Purok 2', b_id: 3, stat_id: AREA_STATUSES.ACTIVE }
];

// ============================================
// SERVICE AREA/ZONE DATA
// ============================================

const areas = [
    { 
        a_id: 1, 
        a_desc: 'Zone A - North District', 
        stat_id: AREA_STATUSES.ACTIVE,
        barangays: [1, 2],
        total_consumers: 320,
        coverage_area: '5.2 sq km'
    },
    { 
        a_id: 2, 
        a_desc: 'Zone B - South District', 
        stat_id: AREA_STATUSES.ACTIVE,
        barangays: [3, 4],
        total_consumers: 285,
        coverage_area: '4.8 sq km'
    },
    { 
        a_id: 3, 
        a_desc: 'Zone C - East District', 
        stat_id: AREA_STATUSES.ACTIVE,
        barangays: [5],
        total_consumers: 195,
        coverage_area: '3.5 sq km'
    },
    { 
        a_id: 4, 
        a_desc: 'Zone D - West District', 
        stat_id: AREA_STATUSES.ACTIVE,
        barangays: [6],
        total_consumers: 450,
        coverage_area: '6.8 sq km'
    }
];

// ============================================
// AREA ASSIGNMENTS (Meter Reader to Area)
// ============================================

const areaAssignments = [
    { 
        area_assignment_id: 1, 
        area_id: 1, 
        meter_reader_id: 1, 
        effective_from: '2024-01-01', 
        effective_to: null,
        is_primary: true
    },
    { 
        area_assignment_id: 2, 
        area_id: 2, 
        meter_reader_id: 2, 
        effective_from: '2024-01-01', 
        effective_to: null,
        is_primary: true
    },
    { 
        area_assignment_id: 3, 
        area_id: 3, 
        meter_reader_id: 3, 
        effective_from: '2024-01-01', 
        effective_to: null,
        is_primary: true
    },
    { 
        area_assignment_id: 4, 
        area_id: 4, 
        meter_reader_id: 4, 
        effective_from: '2024-01-01', 
        effective_to: null,
        is_primary: true
    },
    // Backup assignments
    { 
        area_assignment_id: 5, 
        area_id: 1, 
        meter_reader_id: 2, 
        effective_from: '2024-01-01', 
        effective_to: null,
        is_primary: false
    },
    { 
        area_assignment_id: 6, 
        area_id: 2, 
        meter_reader_id: 1, 
        effective_from: '2024-01-01', 
        effective_to: null,
        is_primary: false
    }
];

// ============================================
// CONSUMER ADDRESS DATA
// ============================================

const consumerAddresses = [
    { 
        ca_id: 323, 
        p_id: 1, 
        b_id: 1, 
        t_id: 1, 
        prov_id: 1, 
        stat_id: AREA_STATUSES.ACTIVE,
        street_address: 'Main Street',
        landmark: 'Near the Red Post Office'
    },
    { 
        ca_id: 324, 
        p_id: 2, 
        b_id: 1, 
        t_id: 1, 
        prov_id: 1, 
        stat_id: AREA_STATUSES.ACTIVE,
        street_address: 'Oak Avenue',
        landmark: 'Beside the Church'
    },
    { 
        ca_id: 325, 
        p_id: 3, 
        b_id: 1, 
        t_id: 1, 
        prov_id: 1, 
        stat_id: AREA_STATUSES.ACTIVE,
        street_address: 'Pine Road',
        landmark: 'Near Elementary School'
    },
    { 
        ca_id: 326, 
        p_id: 7, 
        b_id: 2, 
        t_id: 1, 
        prov_id: 1, 
        stat_id: AREA_STATUSES.ACTIVE,
        street_address: 'Market Street',
        landmark: 'Public Market Area'
    },
    { 
        ca_id: 327, 
        p_id: 9, 
        b_id: 3, 
        t_id: 1, 
        prov_id: 1, 
        stat_id: AREA_STATUSES.ACTIVE,
        street_address: 'Industrial Zone A',
        landmark: 'Near Factory Complex'
    },
    { 
        ca_id: 328, 
        p_id: 4, 
        b_id: 5, 
        t_id: 1, 
        prov_id: 1, 
        stat_id: AREA_STATUSES.ACTIVE,
        street_address: 'Sunset Boulevard',
        landmark: 'Corner of Highway'
    }
];

// ============================================
// HELPER FUNCTIONS
// ============================================

function getProvinceById(provId) {
    return provinces.find(p => p.prov_id === provId);
}

function getTownById(townId) {
    return towns.find(t => t.t_id === townId);
}

function getBarangayById(barangayId) {
    return barangays.find(b => b.b_id === barangayId);
}

function getPurokById(purokId) {
    return puroks.find(p => p.p_id === purokId);
}

function getAreaById(areaId) {
    return areas.find(a => a.a_id === areaId);
}

function getAddressById(addressId) {
    return consumerAddresses.find(ca => ca.ca_id === addressId);
}

function getFullAddress(addressId) {
    const address = getAddressById(addressId);
    if (!address) return 'Unknown Address';
    
    const purok = getPurokById(address.p_id);
    const barangay = getBarangayById(address.b_id);
    const town = getTownById(address.t_id);
    const province = getProvinceById(address.prov_id);
    
    return `${address.street_address}, ${purok?.p_desc || ''}, ${barangay?.b_desc || ''}, ${town?.t_desc || ''}, ${province?.prov_desc || ''}`;
}

function getBarangaysByTown(townId) {
    return barangays.filter(b => b.t_id === townId && b.stat_id === AREA_STATUSES.ACTIVE);
}

function getPuroksByBarangay(barangayId) {
    return puroks.filter(p => p.b_id === barangayId && p.stat_id === AREA_STATUSES.ACTIVE);
}

function getAreasByMeterReader(meterReaderId) {
    const assignments = areaAssignments.filter(aa => aa.meter_reader_id === meterReaderId && aa.effective_to === null);
    return assignments.map(aa => getAreaById(aa.area_id)).filter(a => a);
}

function getMeterReadersByArea(areaId) {
    const assignments = areaAssignments.filter(aa => aa.area_id === areaId && aa.effective_to === null);
    return assignments.map(aa => ({
        meter_reader_id: aa.meter_reader_id,
        is_primary: aa.is_primary
    }));
}

function getAreaStatistics() {
    return {
        totalAreas: areas.length,
        totalBarangays: barangays.length,
        totalPuroks: puroks.length,
        totalConsumers: areas.reduce((sum, a) => sum + a.total_consumers, 0),
        totalCoverage: areas.reduce((sum, a) => sum + parseFloat(a.coverage_area), 0).toFixed(1) + ' sq km'
    };
}

// ============================================
// EXPORT DATA
// ============================================

window.areaData = {
    provinces,
    towns,
    barangays,
    puroks,
    areas,
    areaAssignments,
    consumerAddresses,
    AREA_STATUSES,
    getProvinceById,
    getTownById,
    getBarangayById,
    getPurokById,
    getAreaById,
    getAddressById,
    getFullAddress,
    getBarangaysByTown,
    getPuroksByBarangay,
    getAreasByMeterReader,
    getMeterReadersByArea,
    getAreaStatistics
};

console.log('Area management module loaded successfully');
console.log(`Total areas: ${areas.length}`);
console.log(`Total barangays: ${barangays.length}`);
console.log(`Total puroks: ${puroks.length}`);
console.log(`Total consumer addresses: ${consumerAddresses.length}`);
