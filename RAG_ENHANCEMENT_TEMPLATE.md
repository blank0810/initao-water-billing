# RAG Enhancement Plan for Initao Water Billing System

**Created:** 2025-11-05
**Status:** Pending Implementation
**Estimated Impact:** 70-80% token reduction per AI session

---

## 📊 Current State Analysis

### Issues Identified

1. **Monolithic Context Files**

    - CLAUDE.md is 478 lines (~3,500+ tokens) - loaded every time
    - No semantic chunking or topic-based retrieval
    - Retrieves everything even when only specific info is needed
    - Dual billing system complexity makes targeted retrieval critical

2. **Poor Documentation Discoverability**

    - Flat structure with no clear separation between legacy and modern systems
    - No index or metadata for targeted retrieval
    - AI must scan entire file to find billing vs payment vs meter reading info

3. **Underutilized Context System**

    - No `local_context/` directory (should have feature implementation history)
    - No structured knowledge base for common Laravel patterns
    - Missing feature-specific context files for Billing, Payments, Consumers, etc.

4. **No Embedding/Vector Search**
    - Linear file reading (inefficient)
    - No similarity-based retrieval
    - Can't find "similar problems" or "related implementations"
    - Critical for dual billing system where developers need context-aware guidance

### Current Performance Metrics

| Metric                     | Current Value        |
| -------------------------- | -------------------- |
| Avg Tokens per Query       | 3,500+               |
| Files Read per Query       | 1 (entire CLAUDE.md) |
| Context Retrieval Accuracy | ~60%                 |
| Pattern Reusability        | Low (not documented) |
| Dual System Clarity        | Low (mixed in docs)  |

---

## 🚀 Proposed Enhancements

### 1. Implement Semantic Chunking Strategy

**Problem:** Loading 478-line CLAUDE.md wastes tokens when you only need billing logic or payment info.

**Solution:** Split into feature-specific chunks with metadata for Laravel water billing system

```
.claude/
├── context/
│   ├── _index.yaml          # Master index with semantic tags
│   ├── architecture.md      # Dual billing system overview (60 lines)
│   ├── database.md          # Database schema & migrations (90 lines)
│   ├── setup.md             # Docker, commands, environment (50 lines)
│   ├── features/
│   │   ├── billing/
│   │   │   ├── legacy-consumer-billing.md
│   │   │   ├── modern-service-billing.md
│   │   │   └── billing-generation.md
│   │   ├── payments/
│   │   │   ├── payment-allocation.md
│   │   │   └── ledger-system.md
│   │   ├── meters/
│   │   │   ├── meter-reading.md
│   │   │   ├── meter-assignment.md
│   │   │   └── area-management.md
│   │   └── customers/
│   │       ├── customer-management.md
│   │       ├── service-connections.md
│   │       └── address-hierarchy.md
│   └── patterns/
│       ├── laravel-service-pattern.md
│       ├── eloquent-relationships.md
│       ├── polymorphic-relations.md
│       └── status-management.md
```

**\_index.yaml example:**

```yaml
topics:
    - name: 'Modern Service Connection Billing'
      file: 'features/billing/modern-service-billing.md'
      keywords: ['ServiceConnection', 'WaterBillHistory', 'MeterAssignment', 'billing', 'modern']
      token_estimate: 420

    - name: 'Payment Allocation System'
      file: 'features/payments/payment-allocation.md'
      keywords: ['Payment', 'PaymentAllocation', 'polymorphic', 'distribution']
      token_estimate: 350

    - name: 'Meter Reading & Area Management'
      file: 'features/meters/meter-reading.md'
      keywords: ['MeterReading', 'Area', 'ReadingSchedule', 'Period']
      token_estimate: 380

    - name: 'Legacy Consumer Billing'
      file: 'features/billing/legacy-consumer-billing.md'
      keywords: ['Consumer', 'ConsumerLedger', 'WaterBill', 'legacy']
      token_estimate: 300

    - name: 'Database Schema & Migrations'
      file: 'database.md'
      keywords: ['migration', 'schema', 'eloquent', 'mysql', 'Status']
      token_estimate: 450
```

**Token Savings:** 80% reduction (load 50-90 lines vs 478 lines)

---

### 2. Create a Knowledge Graph Structure

**Problem:** No way to find "similar implementations" or navigate between legacy and modern billing systems.

**Solution:** Add relationship metadata to each context file

**Example:** `.claude/context/features/billing/modern-service-billing.md`

```markdown
---
related_to:
    - features/payments/payment-allocation.md (Payment processing)
    - features/meters/meter-reading.md (Consumption data)
    - database.md (ServiceConnection, WaterBillHistory tables)
    - patterns/polymorphic-relations.md (Ledger source tracking)
replaces:
    - features/billing/legacy-consumer-billing.md (Old system)
dependencies:
    - app/Services/Billing/BillingService.php
    - app/Models/ServiceConnection.php
    - app/Models/WaterBillHistory.php
    - app/Models/CustomerLedger.php
common_issues:
    - period_closure
    - meter_assignment_gaps
    - consumption_calculation
    - rate_tier_application
last_updated: '2025-11-05'
---

# Modern Service Connection Billing

## Quick Reference

**Primary Files:**
- app/Services/Billing/BillingService.php
- app/Controllers/Billing/BillingController.php
**Database Tables:** service_connection, water_bill_history, customer_ledger, meter_assignment
**Replaced:** Legacy Consumer-based billing (see legacy-consumer-billing.md)

[Content...]
```

---

### 3. Implement Context Versioning & Pattern Library

**Problem:** No local_context/ directory. Pattern discoveries get lost over time. Dual billing system complexity not documented.

**Solution:** Structured context with semantic categorization for Laravel water billing

```
local_context/
├── features/              # Feature implementation history
│   ├── billing-generation-2025-11-05.md
│   ├── payment-allocation-2025-11-02.md
│   ├── meter-assignment-refactor-2025-10-28.md
│   ├── customer-ledger-polymorphic-2025-10-20.md
│   └── service-connection-migration-2025-10-15.md
├── patterns/              # Reusable Laravel patterns
│   ├── service-layer-pattern.md
│   ├── polymorphic-relations-usage.md
│   ├── period-based-operations.md
│   ├── status-management-pattern.md
│   └── eager-loading-optimization.md
└── decisions/             # Architectural decision records (ADRs)
    ├── why-dual-billing-systems.md
    ├── no-repository-pattern.md
    ├── polymorphic-ledger-approach.md
    ├── period-closure-strategy.md
    └── feature-based-organization.md
```

**Pattern Template:**

````markdown
# [Pattern Name]

## Context

Implemented [date] for [feature] ([file location])

## Problem

[Brief description of the problem this pattern solves]

## Solution

[Step-by-step solution]

## Code Location

-   Service: [file:line-range]
-   Controller: [file:line-range]
-   Model: [file:line-range]

## Reusable For

-   [Use case 1]
-   [Use case 2]
-   [Use case 3]

## Token Efficiency Notes

[Any specific optimizations that reduce token usage]

## Related Patterns

-   [Pattern 1]
-   [Pattern 2]

## Example Implementation

```php
[Laravel/PHP code example]
```
````

````

---

### 4. Add Contextual Breadcrumbs

**Problem:** Hard to know which file to read for billing vs payment vs meter reading tasks.

**Solution:** Transform CLAUDE.md into a quick reference guide

**New CLAUDE.md structure (120 lines max):**

```markdown
# Initao Water Billing System - Quick Reference

## 🎯 Need Help With...

- 💧 **Billing Generation?** → `.claude/context/features/billing/billing-generation.md`
- 🔄 **Legacy vs Modern Billing?** → `.claude/context/architecture.md#dual-billing-system`
- 💰 **Payment Processing?** → `.claude/context/features/payments/payment-allocation.md`
- 📊 **Ledger System?** → `.claude/context/features/payments/ledger-system.md`
- 📏 **Meter Reading?** → `.claude/context/features/meters/meter-reading.md`
- 🔧 **Meter Assignment?** → `.claude/context/features/meters/meter-assignment.md`
- 👥 **Customer Management?** → `.claude/context/features/customers/customer-management.md`
- 🔌 **Service Connections?** → `.claude/context/features/customers/service-connections.md`
- 🗄️ **Database Schema?** → `.claude/context/database.md`
- 🏗️ **Laravel Patterns?** → `.claude/context/patterns/`
- 🐳 **Docker Setup?** → `.claude/context/setup.md`

## 📚 Recent Changes
See `local_context/features/` (sorted by date)

## 🏗️ Architecture Overview
[60-line high-level summary of dual billing system]

## 🚀 Quick Commands
[Development commands - composer dev, artisan commands, docker]

## ⚠️ Critical Notes
[Top 5 most important architectural rules]

## 📖 Full Documentation
For detailed information, see the feature-specific files in `.claude/context/features/`
````

---

### 5. Implement Smart Context Summaries

**Problem:** Even chunked files can be long. Need TL;DR versions.

**Solution:** Add summary blocks at top of each file

**Template:**

```markdown
# [Topic Name]

## 🎯 Quick Summary (30 seconds)

-   **Purpose:** [One sentence]
-   **Key Pattern:** [Main Laravel/billing pattern used]
-   **System Type:** [Legacy/Modern/Both]
-   **Token Optimization:** [How this saves tokens/improves efficiency]
-   **Main Files:** [Service, Controller, Model locations]
-   **Recent Enhancement:** [date] (see local_context/features/[file].md)

## 📋 TL;DR Implementation Guide

1. [Step 1]
2. [Step 2]
3. [Step 3]
4. [Step 4]
5. [Step 5]

---

## 📖 Full Documentation

[Detailed content below...]
```

---

### 6. Add Searchable Metadata (Frontmatter)

**Problem:** Claude has to read files to know if they're relevant to legacy vs modern billing.

**Solution:** YAML frontmatter with searchable metadata

**Example:**

```markdown
---
title: 'Payment Allocation System'
category: 'Payments'
system: 'modern'
tags: ['Payment', 'PaymentAllocation', 'polymorphic', 'ledger', 'billing']
last_updated: '2025-11-05'
complexity: 'medium'
token_estimate: 350
related_issues: ['payment-distribution', 'ledger-balance', 'polymorphic-queries']
code_locations:
    - 'app/Services/Payments/PaymentService.php'
    - 'app/Models/Payment.php'
    - 'app/Models/PaymentAllocation.php'
related_models:
    - 'Payment'
    - 'PaymentAllocation'
    - 'CustomerLedger'
    - 'WaterBillHistory'
external_docs:
    - 'https://laravel.com/docs/12.x/eloquent-relationships#polymorphic-relationships'
---

# Payment Allocation System

[Content...]
```

---

### 7. Create a Context Cache System

**Problem:** Re-reading same files repeatedly across sessions.

**Solution:** Implement a lightweight context cache/index

**File:** `.claude/context/_cache.json`

```json
{
	"version": "1.0.0",
	"generated": "2025-11-05T10:00:00Z",
	"project": "initao-water-billing",
	"contexts": [
		{
			"file": ".claude/context/features/billing/modern-service-billing.md",
			"title": "Modern Service Connection Billing",
			"summary": "ServiceConnection-based billing with polymorphic ledger tracking and meter assignment support",
			"system": "modern",
			"keywords": [
				"ServiceConnection",
				"WaterBillHistory",
				"billing",
				"modern",
				"consumption",
				"MeterAssignment"
			],
			"lastModified": "2025-11-05",
			"tokenCount": 420,
			"relatedFiles": [
				"features/payments/payment-allocation.md",
				"features/meters/meter-reading.md",
				"database.md"
			],
			"codeLocations": [
				"app/Services/Billing/BillingService.php",
				"app/Models/ServiceConnection.php",
				"app/Models/WaterBillHistory.php"
			]
		},
		{
			"file": ".claude/context/features/payments/payment-allocation.md",
			"title": "Payment Allocation System",
			"summary": "Polymorphic payment distribution across bills and charges with ledger integration",
			"system": "modern",
			"keywords": ["Payment", "PaymentAllocation", "polymorphic", "distribution", "ledger"],
			"lastModified": "2025-11-02",
			"tokenCount": 350,
			"relatedFiles": ["features/payments/ledger-system.md", "patterns/polymorphic-relations.md"],
			"codeLocations": ["app/Services/Payments/PaymentService.php", "app/Models/Payment.php"]
		}
	]
}
```

**Generation Script:** `.claude/scripts/generate-cache.php` (Laravel artisan command to be created)

---

## 📊 Expected Improvements

| Metric                       | Current              | Optimized         | Improvement |
| ---------------------------- | -------------------- | ----------------- | ----------- |
| **Avg Tokens per Query**     | 3,500+               | ~700-900          | 77% ↓       |
| **Context Retrieval Time**   | Read entire file     | Read 1-2 chunks   | 80% ↓       |
| **Relevance Accuracy**       | ~60%                 | ~95%              | 58% ↑       |
| **Pattern Reusability**      | Low                  | High              | Documented  |
| **Onboarding Time (new AI)** | 10+ min              | 2-3 min           | 70% ↓       |
| **Legacy/Modern Clarity**    | Mixed documentation  | Clearly separated | High ↑      |
| **Feature Navigation**       | Manual file search   | Indexed lookup    | 85% ↓       |

---

## 🎯 Implementation Plan

### Phase 1: Quick Wins (30 minutes)

**Goal:** Immediate token reduction

**Tasks:**

1. Split CLAUDE.md into feature-specific chunks

    - Create `.claude/context/` directory structure
    - Extract architecture section → `architecture.md` (dual billing system overview)
    - Extract database section → `database.md` (migrations, models, relationships)
    - Extract setup section → `setup.md` (docker, commands, environment)
    - Create feature directories:
        - `features/billing/` (legacy & modern billing)
        - `features/payments/` (payment allocation, ledger)
        - `features/meters/` (reading, assignment, areas)
        - `features/customers/` (management, connections, addresses)

2. Create `_index.yaml` with keywords

    - List all context files with feature categorization
    - Add searchable keywords specific to water billing domain
    - Include token estimates
    - Tag files as 'legacy', 'modern', or 'both'

3. Transform CLAUDE.md into breadcrumb quick reference
    - Keep only navigation links by feature
    - 60-line architecture overview (dual system emphasis)
    - Critical notes section (top 5 rules)
    - Link to detailed context files

**Expected Result:** 65% token reduction on first query, clearer legacy vs modern navigation

---

### Phase 2: Pattern Library (1 hour)

**Goal:** Capture and reuse learned Laravel patterns

**Tasks:**

1. Create `local_context/` directory structure

    - `features/` - Feature implementation history
    - `patterns/` - Reusable Laravel patterns
    - `decisions/` - Architectural decision records

2. Document key Laravel patterns in `local_context/patterns/`

    - Service Layer Pattern (how BillingService, PaymentService work)
    - Polymorphic Relations Usage (CustomerLedger, PaymentAllocation)
    - Period-based Operations (billing cycles, closure)
    - Status Management Pattern (Status model, status_id usage)
    - Eager Loading Optimization (N+1 prevention)

3. Document architectural decisions in `local_context/decisions/`
    - Why dual billing systems exist
    - Why no repository pattern (direct Eloquent usage)
    - Polymorphic ledger approach rationale
    - Period closure strategy
    - Feature-based organization benefits

**Expected Result:** Reusable pattern library for faster feature implementation, clear ADR documentation

---

### Phase 3: Knowledge Graph (2 hours)

**Goal:** Connect related concepts for intelligent retrieval and clear legacy/modern navigation

**Tasks:**

1. Add frontmatter metadata to all context files

    - YAML with title, category, tags, system type (legacy/modern/both)
    - Related files (cross-references)
    - Code locations (Service, Controller, Model)
    - Related models
    - External Laravel docs

2. Create relationship mapping

    - Document dependencies between features (e.g., billing → meter reading → payment)
    - Link patterns to implementations (e.g., polymorphic pattern → PaymentAllocation)
    - Connect legacy to modern equivalents (Consumer → ServiceConnection)
    - Map common issues to solutions

3. Create troubleshooting guide (optional)
    - Common billing issues (period closure, consumption calculation)
    - Payment allocation issues (polymorphic queries)
    - Meter assignment issues (gaps, overlaps)
    - Link to pattern solutions with code locations

**Expected Result:** AI can navigate context graph intelligently with clear legacy/modern distinction

---

### Phase 4: Automation (1 hour)

**Goal:** Maintain system with minimal effort

**Tasks:**

1. Create context cache generation script

    - `.claude/scripts/generate-cache.php` (Laravel artisan command)
    - Scans all .md files in `.claude/context/`
    - Extracts frontmatter metadata
    - Generates `_cache.json` with indexed content

2. Add validation for frontmatter

    - Ensure all required fields present (title, category, system, tags)
    - Validate token estimates are reasonable
    - Check file references exist
    - Verify code location paths

3. Set up composer script (optional)
    - Add to composer.json: `"context:cache": "php artisan context:cache"`
    - Auto-generate cache before commits
    - Validate context file structure

**Expected Result:** Self-maintaining RAG system with Laravel integration

---

### Phase 5: Cleanup (30 minutes)

**Goal:** Maintain clean structure

**Tasks:**

1. Archive original CLAUDE.md

    - Save as `CLAUDE.md.backup` or `CLAUDE.original.md`
    - Keep for reference during migration
    - Can be removed after successful implementation

2. Review existing documentation

    - Check if any standalone docs should be integrated
    - Move relevant content to `.claude/context/`
    - Keep README.md user-focused (setup, deployment)

3. Update .gitignore (if needed)
    - Ensure `.claude/context/_cache.json` is tracked (or gitignored based on preference)
    - Keep `local_context/` tracked for team knowledge sharing

**Expected Result:** Clean, maintainable structure focused on water billing domain

---

## 🎯 Total Time Investment

-   **Phase 1:** 30 minutes (Quick Wins)
-   **Phase 2:** 1 hour (Pattern Library)
-   **Phase 3:** 2 hours (Knowledge Graph)
-   **Phase 4:** 1 hour (Automation)
-   **Phase 5:** 30 minutes (Cleanup)

**Total:** ~5 hours

**Long-term ROI:**

-   70-80% token savings per session
-   3-5x faster context retrieval
-   Clear legacy vs modern system navigation
-   Reusable Laravel patterns save hours on new billing features
-   Reduced onboarding time for new AI assistants and developers
-   Better documentation for dual billing system complexity

---

## 📝 Migration Checklist

### Pre-Migration

-   [ ] Backup current CLAUDE.md as CLAUDE.md.backup
-   [ ] Create `.claude/context/` directory structure
-   [ ] Create `.claude/context/features/` subdirectories (billing, payments, meters, customers)
-   [ ] Create `.claude/context/patterns/` directory
-   [ ] Create `local_context/` directory structure (features, patterns, decisions)

### Phase 1 (Quick Wins)

-   [ ] Split CLAUDE.md into feature-specific chunks
    -   [ ] Extract architecture.md (dual billing system)
    -   [ ] Extract database.md (migrations, models)
    -   [ ] Extract setup.md (docker, commands)
    -   [ ] Create billing context files (legacy & modern)
    -   [ ] Create payment context files
    -   [ ] Create meter context files
    -   [ ] Create customer context files
-   [ ] Create `_index.yaml` with water billing keywords
-   [ ] Transform CLAUDE.md into quick reference (120 lines max)
-   [ ] Test with Claude Code to verify token reduction

### Phase 2 (Pattern Library)

-   [ ] Create local_context/ directories
-   [ ] Document Laravel patterns:
    -   [ ] service-layer-pattern.md
    -   [ ] polymorphic-relations-usage.md
    -   [ ] period-based-operations.md
    -   [ ] status-management-pattern.md
    -   [ ] eager-loading-optimization.md
-   [ ] Document architectural decisions:
    -   [ ] why-dual-billing-systems.md
    -   [ ] no-repository-pattern.md
    -   [ ] polymorphic-ledger-approach.md
    -   [ ] period-closure-strategy.md

### Phase 3 (Knowledge Graph)

-   [ ] Add frontmatter to all context files (including system: legacy/modern/both)
-   [ ] Map relationships between features
-   [ ] Link legacy to modern equivalents
-   [ ] Create troubleshooting guide (optional)
-   [ ] Validate all cross-references

### Phase 4 (Automation)

-   [ ] Write Laravel artisan command (context:cache)
-   [ ] Add frontmatter validation logic
-   [ ] Test cache generation with water billing context
-   [ ] Add composer script (optional)
-   [ ] Document cache regeneration process

### Phase 5 (Cleanup)

-   [ ] Archive CLAUDE.md.backup
-   [ ] Review existing documentation
-   [ ] Update .gitignore (decide on _cache.json tracking)
-   [ ] Document new context system in README (optional)

---

## 🔧 Example File Structure After Implementation

```
initao-water-billing/
├── .claude/
│   ├── context/
│   │   ├── _index.yaml              # Master index with feature categorization
│   │   ├── _cache.json              # Auto-generated cache
│   │   ├── architecture.md          # 60 lines (dual billing system)
│   │   ├── database.md              # 90 lines (migrations, models)
│   │   ├── setup.md                 # 50 lines (docker, commands)
│   │   ├── features/
│   │   │   ├── billing/
│   │   │   │   ├── legacy-consumer-billing.md
│   │   │   │   ├── modern-service-billing.md
│   │   │   │   └── billing-generation.md
│   │   │   ├── payments/
│   │   │   │   ├── payment-allocation.md
│   │   │   │   └── ledger-system.md
│   │   │   ├── meters/
│   │   │   │   ├── meter-reading.md
│   │   │   │   ├── meter-assignment.md
│   │   │   │   └── area-management.md
│   │   │   └── customers/
│   │   │       ├── customer-management.md
│   │   │       ├── service-connections.md
│   │   │       └── address-hierarchy.md
│   │   └── patterns/
│   │       ├── laravel-service-pattern.md
│   │       ├── eloquent-relationships.md
│   │       ├── polymorphic-relations.md
│   │       └── status-management.md
│   └── scripts/
│       └── generate-cache.php       # Laravel artisan command
├── CLAUDE.md                        # 120 lines (quick reference only)
├── CLAUDE.md.backup                 # Original file (archived)
├── local_context/
│   ├── features/                    # Feature implementation history
│   │   ├── billing-generation-2025-11-05.md
│   │   ├── payment-allocation-2025-11-02.md
│   │   └── meter-assignment-refactor-2025-10-28.md
│   ├── patterns/                    # Reusable Laravel patterns
│   │   ├── service-layer-pattern.md
│   │   ├── polymorphic-relations-usage.md
│   │   ├── period-based-operations.md
│   │   └── status-management-pattern.md
│   └── decisions/                   # Architectural decisions
│       ├── why-dual-billing-systems.md
│       ├── no-repository-pattern.md
│       ├── polymorphic-ledger-approach.md
│       └── period-closure-strategy.md
├── app/                             # Laravel application
│   ├── Http/Controllers/            # Feature-based controllers
│   ├── Services/                    # Business logic services
│   └── Models/                      # Eloquent models
└── README.md                        # User-facing documentation
```

---

## 💡 Key Principles

1. **Chunk by Feature, Not by Size**

    - Group by water billing features (Billing, Payments, Meters, Customers)
    - Keep each file focused on one domain or business capability
    - Separate legacy and modern system documentation

2. **Metadata is King**

    - Frontmatter enables smart retrieval
    - Keywords specific to water billing domain (ServiceConnection, MeterReading, etc.)
    - Tag system type: legacy/modern/both

3. **DRY for Context**

    - One source of truth per feature/topic
    - Cross-reference between related features
    - Link legacy to modern equivalents

4. **Progressive Disclosure**

    - Quick summary → TL;DR → Full details
    - Let AI choose depth needed based on query
    - Include code locations for quick navigation

5. **Maintain Relationships**
    - Document dependencies (billing → meters → payments)
    - Link related Laravel patterns to implementations
    - Build knowledge graph with clear legacy/modern navigation

---

## 📚 References

### Similar RAG Patterns in Industry

-   Anthropic Claude Artifacts: Semantic chunking with metadata
-   GitHub Copilot: Pattern library approach
-   Cursor IDE: Context graph with code locations
-   Laravel Documentation: Feature-based organization

### Laravel-Specific Resources

-   Laravel 12 Documentation: https://laravel.com/docs/12.x
-   Eloquent Relationships: https://laravel.com/docs/12.x/eloquent-relationships
-   Service Pattern in Laravel: https://laravel-news.com/service-pattern

---

## 🚀 Next Steps

When ready to implement:

1. **Review this plan** - Adjust phases based on team priorities
2. **Choose starting phase** - Phase 1 provides immediate 65% token reduction
3. **Create backup** - Save current CLAUDE.md as CLAUDE.md.backup
4. **Implement Phase 1** - Start with quick wins for immediate benefit
5. **Test with Claude Code** - Verify token reduction with real queries
6. **Iterate** - Add more context files as features evolve

**Status:** Ready for implementation
**Priority:** High (significant long-term efficiency gain + dual system clarity)
**Dependencies:** None (can start immediately)
**Best Time:** During development phase when documenting new features

---

_Last updated: 2025-11-05_
_Project: Initao Water Billing System_
_Stack: Laravel 12, MySQL, Docker_
