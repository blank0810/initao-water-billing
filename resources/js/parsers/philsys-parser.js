/**
 * PhilSys National ID QR Code Parser
 *
 * Handles two formats:
 *   1. Physical ID  — { subject: { fName, mName, lName, Suffix, PCN, ... }, ... }
 *   2. Digital ID   — { n_f, n_m, n_l, n_s, pcn, iss: "national-id.gov.ph", ... }
 *
 * @param {string} rawData - Raw QR code string
 * @returns {{ firstName: string, middleName: string, lastName: string, suffix: string, idNumber: string } | null}
 */
export function parsePhilSysQR(rawData) {
    if (!rawData || typeof rawData !== 'string') return null;

    let data;
    try {
        data = JSON.parse(rawData.trim());
    } catch {
        console.warn('[PhilSys QR] Not valid JSON:', rawData);
        return null;
    }

    if (typeof data !== 'object' || data === null) return null;

    let result = null;

    if (data.subject) {
        // Physical ID format
        const s = data.subject;
        result = {
            firstName: str(s.fName),
            middleName: str(s.mName),
            lastName: str(s.lName),
            suffix: str(s.Suffix),
            idNumber: str(s.PCN),
        };
    } else if (data.n_f || data.n_l) {
        // Digital ID format
        result = {
            firstName: str(data.n_f),
            middleName: str(data.n_m),
            lastName: str(data.n_l),
            suffix: str(data.n_s),
            idNumber: str(data.pcn),
        };
    }

    // Must have at least a first name or last name to be useful
    if (!result || (!result.firstName && !result.lastName)) {
        console.warn('[PhilSys QR] No usable name data:', data);
        return null;
    }

    console.log('[PhilSys QR] Parsed:', result);
    return result;
}

function str(val) {
    return val ? String(val).trim().toUpperCase() : '';
}
