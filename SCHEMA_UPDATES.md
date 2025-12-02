# Schema Updates - User Feedback Addressed

## Changes Made (November 25, 2025)

### 1. ✅ Consumer Address Model - Clarified

**User Concern:** "A consumer address is different from connection address. Connection might be at location B but consumer lives at location A."

**Resolution:** The enhanced schema **already correctly models this**! Just needed better documentation.

**How it works:**

```sql
-- Customer table
customer:
  customer_id: 1
  first_name: 'Juan'
  last_name: 'Dela Cruz'
  billing_address_id: 101  --> Points to where Juan lives (Location A)

-- Service Connection table
serviceconnection:
  connection_id: 1001
  customer_id: 1  --> Juan's account
  service_address_id: 201  --> Water meter at Location B (rental property)

serviceconnection:
  connection_id: 1002
  customer_id: 1  --> Same Juan
  service_address_id: 301  --> Another water meter at Location C (another rental)
```

**Result:**

-   ✅ Juan lives at **Location A** (billing address)
-   ✅ Has water service at **Location B** (rental property #1)
-   ✅ Has water service at **Location C** (rental property #2)
-   ✅ All bills sent to where Juan lives (Location A)

This is the **correct and common scenario** - landlords with multiple properties!

---

### 2. ✅ Rate Category - Made Flexible

**User Concern:** "Maybe put in config table? We can still put custom category."

**Original Design:**

```sql
rate_category ENUM('residential','commercial','government','institutional')
```

❌ **Problem:** Hardcoded values, can't add custom categories

**Updated Design:**

```sql
rate_category VARCHAR(50) NOT NULL
```

✅ **Solution:** Municipalities can now define ANY custom category!

**Examples of Custom Categories:**

-   "Agricultural"
-   "Industrial"
-   "Wholesale"
-   "Senior Citizen Residential"
-   "Low-Income Housing"
-   Whatever the municipality needs!

**How to use:**

```sql
-- Municipality can create custom account types:
INSERT INTO account_type (type_code, type_name, rate_category) VALUES
('AGRI', 'Agricultural', 'Agricultural'),
('IND', 'Industrial', 'Industrial'),
('SENIOR', 'Senior Citizen', 'Senior Residential');

-- Then define rates for each category:
INSERT INTO water_rates (account_type_id, min_range, max_range, rate_increment) VALUES
(5, 0, 10, 8.50),  -- Agricultural rate
(6, 0, 10, 15.00); -- Industrial rate
```

---

## Summary

Both concerns have been addressed:

1. **Consumer vs Connection Address**: Schema properly separates customer billing address from service locations ✅
2. **Rate Categories**: Changed from restrictive ENUM to flexible VARCHAR ✅

The enhanced schema is now:

-   More flexible for different municipality needs
-   Better documented for clarity
-   Production-ready!
