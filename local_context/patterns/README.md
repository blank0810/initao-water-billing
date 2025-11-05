# Reusable Laravel Patterns

This directory stores documented patterns that can be reused across features.

## Purpose

Extract and document reusable patterns discovered during development. This helps with:
- Consistency across features
- Faster implementation of new features
- Code quality and maintainability
- Knowledge sharing across team

## File Naming Convention

Use descriptive pattern names:

```
<pattern-name>.md
```

## Examples

```
local_context/patterns/
├── service-layer-pattern.md
├── polymorphic-relations.md
├── period-based-operations.md
├── status-management.md
└── eager-loading-optimization.md
```

## Template

When documenting a pattern, include:

```markdown
# [Pattern Name]

**Category:** [Architecture/Data/Performance/Security]
**Complexity:** [Simple/Medium/Complex]
**First Used:** [Feature name and date]

## Problem

[What problem does this pattern solve?]

## Solution

[How does this pattern solve it?]

## Code Example

```php
// Example implementation
class ExampleService
{
    public function exampleMethod()
    {
        // Pattern code here
    }
}
```

## When to Use

- Use case 1
- Use case 2
- Use case 3

## When NOT to Use

- Scenario 1
- Scenario 2

## Code Locations

Examples in the codebase:
- `app/Services/Billing/BillingService.php:123-145`
- `app/Services/Payments/PaymentService.php:67-89`

## Related Patterns

- [Link to related pattern]
- [Link to related pattern]

## Laravel Documentation

- [Link to relevant Laravel docs]

## Benefits

- Benefit 1
- Benefit 2

## Trade-offs

- Trade-off 1
- Trade-off 2

## Testing Strategy

[How to test code using this pattern]
```

## Common Pattern Categories

### Architecture Patterns
- Service Layer Pattern
- Repository Pattern (Note: We don't use this!)
- Event-Driven Architecture
- Feature-Based Organization

### Data Patterns
- Polymorphic Relations
- Eager Loading Optimization
- Query Scopes
- Model Relationships

### Business Logic Patterns
- Period-Based Operations
- Status Management
- Resolution Number Generation
- Billing Calculations

### Performance Patterns
- N+1 Query Prevention
- Caching Strategies
- Batch Operations
- Database Indexing

### Security Patterns
- Input Validation
- Authorization Policies
- Audit Logging
- Rate Limiting

## Tips

- Extract patterns after using them 2-3 times
- Include real code examples from the codebase
- Document both benefits AND trade-offs
- Link to Laravel documentation
- Keep examples simple and focused
- Update patterns as they evolve

---

Start documenting patterns as you discover them!
