/**
 * PhilSys National ID QR Code Parser
 *
 * Attempts to parse the raw QR string from a Philippine National ID.
 * Tries JSON first, then delimiter-based formats.
 *
 * Returns: { firstName, middleName, lastName, suffix, idNumber } or null
 */

const KNOWN_SUFFIXES = ['JR', 'JR.', 'SR', 'SR.', 'II', 'III', 'IV', 'V'];

/**
 * Parse a full name string into parts: firstName, middleName, lastName, suffix
 * Assumes format: "LAST NAME, FIRST NAME MIDDLE NAME SUFFIX"
 * or: "FIRST NAME MIDDLE NAME LAST NAME SUFFIX"
 */
function parseFullName(fullName) {
    if (!fullName || typeof fullName !== 'string') return null;

    const cleaned = fullName.trim().toUpperCase();

    // Format 1: "LAST, FIRST MIDDLE" (common in PH government IDs)
    if (cleaned.includes(',')) {
        const [lastPart, ...restParts] = cleaned.split(',').map(s => s.trim());
        const rest = restParts.join(' ').trim().split(/\s+/);

        let suffix = '';
        const lastWord = rest[rest.length - 1];
        if (rest.length > 1 && KNOWN_SUFFIXES.includes(lastWord)) {
            suffix = lastWord;
            rest.pop();
        }

        return {
            firstName: rest[0] || '',
            middleName: rest.slice(1).join(' '),
            lastName: lastPart,
            suffix: suffix
        };
    }

    // Format 2: "FIRST MIDDLE LAST SUFFIX" (space separated)
    const parts = cleaned.split(/\s+/);
    let suffix = '';
    if (parts.length > 2 && KNOWN_SUFFIXES.includes(parts[parts.length - 1])) {
        suffix = parts.pop();
    }

    if (parts.length === 1) {
        return { firstName: parts[0], middleName: '', lastName: '', suffix };
    }
    if (parts.length === 2) {
        return { firstName: parts[0], middleName: '', lastName: parts[1], suffix };
    }

    return {
        firstName: parts[0],
        middleName: parts.slice(1, -1).join(' '),
        lastName: parts[parts.length - 1],
        suffix
    };
}

/**
 * Attempt to parse as JSON
 */
function tryParseJSON(raw) {
    try {
        const data = JSON.parse(raw);
        if (typeof data !== 'object' || data === null) return null;

        // Try common field names (case-insensitive search)
        const keys = Object.keys(data);
        const find = (patterns) => {
            for (const pattern of patterns) {
                const key = keys.find(k => k.toLowerCase().includes(pattern));
                if (key) return data[key];
            }
            return '';
        };

        // Check if name is a single field or separate fields
        const fullName = find(['full_name', 'fullname', 'name']);
        const firstName = find(['first_name', 'firstname', 'given']);
        const lastName = find(['last_name', 'lastname', 'surname', 'family']);
        const middleName = find(['middle_name', 'middlename', 'middle']);
        const suffix = find(['suffix', 'ext', 'extension']);
        const pcn = find(['pcn', 'card_number', 'id_number', 'idnumber', 'number', 'phil_sys', 'philsys']);

        if (firstName || lastName) {
            return {
                firstName: String(firstName).toUpperCase(),
                middleName: String(middleName).toUpperCase(),
                lastName: String(lastName).toUpperCase(),
                suffix: String(suffix).toUpperCase(),
                idNumber: String(pcn)
            };
        }

        if (fullName) {
            const parsed = parseFullName(String(fullName));
            return parsed ? { ...parsed, idNumber: String(pcn) } : null;
        }

        return null;
    } catch {
        return null;
    }
}

/**
 * Attempt to parse as delimited string (pipe, newline, tab)
 */
function tryParseDelimited(raw) {
    const delimiters = ['|', '\n', '\t'];

    for (const delim of delimiters) {
        const parts = raw.split(delim).map(s => s.trim()).filter(Boolean);
        if (parts.length >= 2) {
            // Heuristic: look for a part that looks like a PCN (alphanumeric, 12+ chars)
            const pcnIndex = parts.findIndex(p => /^[A-Z0-9-]{8,}$/i.test(p));
            const namePart = pcnIndex === 0 ? parts[1] : parts[0];
            const pcn = pcnIndex >= 0 ? parts[pcnIndex] : '';

            const parsed = parseFullName(namePart);
            if (parsed && (parsed.firstName || parsed.lastName)) {
                return { ...parsed, idNumber: pcn };
            }
        }
    }

    return null;
}

/**
 * Main parse function. Tries all strategies.
 * Always logs raw data for debugging.
 *
 * @param {string} rawData - The raw string from QR code scan
 * @returns {{ firstName: string, middleName: string, lastName: string, suffix: string, idNumber: string } | null}
 */
export function parsePhilSysQR(rawData) {
    if (!rawData || typeof rawData !== 'string') return null;

    const raw = rawData.trim();
    console.log('[PhilSys QR] Raw scan data:', raw);

    // Strategy 1: JSON
    const jsonResult = tryParseJSON(raw);
    if (jsonResult) {
        console.log('[PhilSys QR] Parsed as JSON:', jsonResult);
        return jsonResult;
    }

    // Strategy 2: Delimited
    const delimitedResult = tryParseDelimited(raw);
    if (delimitedResult) {
        console.log('[PhilSys QR] Parsed as delimited:', delimitedResult);
        return delimitedResult;
    }

    // Strategy 3: Treat entire string as a name (last resort, no PCN)
    if (raw.length > 2 && raw.length < 100 && !/^[{[]/.test(raw)) {
        const nameResult = parseFullName(raw);
        if (nameResult && (nameResult.firstName || nameResult.lastName)) {
            console.log('[PhilSys QR] Parsed as plain name:', nameResult);
            return { ...nameResult, idNumber: '' };
        }
    }

    console.warn('[PhilSys QR] Could not parse QR data:', raw);
    return null;
}
