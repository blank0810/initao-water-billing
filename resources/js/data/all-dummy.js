// resources/js/data/consumerCustomerData.js

import rawData from '@/data/dummy.json';

// Normalize Consumers
export const consumerAllData = rawData.consumers.map(consumer => ({
  ...consumer,
  id: String(consumer.id),
  ConsuId: String(consumer.ConsuId),
  ConsuName: String(consumer.ConsuName),
  DateInstalled: String(consumer.DateInstalled),
  SubClassName: String(consumer.SubClassName),
  AreaId: String(consumer.AreaId),
  AreaCode: String(consumer.AreaCode),
  ConsuNo: String(consumer.ConsuNo),
  PersonId: String(consumer.PersonId),
  PersonName: String(consumer.PersonName),
  TradeId: String(consumer.TradeId),
  TradeName: String(consumer.TradeName),
  ClassId: String(consumer.ClassId),
  SubClassId: String(consumer.SubClassId),
  LocaId: String(consumer.LocaId),
  LocaName: String(consumer.LocaName),
  MeterId: String(consumer.MeterId),
  MeterName: String(consumer.MeterName),
  Active: Boolean(consumer.Active),
  LastOfStatDate: String(consumer.LastOfStatDate),
  LastOfStatId: String(consumer.LastOfStatId),
  LastOfStatName: String(consumer.LastOfStatName),
  CreationDate: String(consumer.CreationDate),
}));

// Normalize Customers
export const customerAllData = rawData.customers.map(customer => ({
  ...customer,
  id: String(customer.id),
  CustomerName: String(customer.CustomerName),
  DateApplied: String(customer.DateApplied),
  AreaId: String(customer.AreaId),
  AreaCode: String(customer.AreaCode),
  ContactNo: customer.ContactNo ? String(customer.ContactNo) : '',
  Email: customer.Email ? String(customer.Email) : '',
  Status: customer.Status, // 'Pending', 'Approved', 'Rejected'
}));
