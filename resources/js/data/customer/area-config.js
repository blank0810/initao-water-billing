/**
 * Area and Meter Reader Configuration
 */

export const areaConfig = [
    { area: 'Zone A', meterReader: 'John Smith', readingSchedule: 5 },
    { area: 'Zone B', meterReader: 'Jane Doe', readingSchedule: 10 },
    { area: 'Zone C', meterReader: 'Mike Johnson', readingSchedule: 15 },
    { area: 'Zone D', meterReader: 'Sarah Williams', readingSchedule: 20 },
    { area: 'Zone E', meterReader: 'Tom Brown', readingSchedule: 25 }
];

export function getMeterReaderByArea(area) {
    const config = areaConfig.find(c => c.area === area);
    return config ? config.meterReader : '';
}

export function getAreaByMeterReader(meterReader) {
    const config = areaConfig.find(c => c.meterReader === meterReader);
    return config ? config.area : '';
}

export function getReadingScheduleByArea(area) {
    const config = areaConfig.find(c => c.area === area);
    if (!config) return '';
    
    const today = new Date();
    const nextMonth = new Date(today.getFullYear(), today.getMonth() + 1, config.readingSchedule);
    return nextMonth.toISOString().split('T')[0];
}
