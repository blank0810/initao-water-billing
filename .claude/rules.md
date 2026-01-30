# Laravel Water Billing System — Developer Rules & Best Practices (Feature-Based Modular Approach)

> **Goal:** Deliver a maintainable, modular, and production-ready Laravel Water Billing System in ≤90 days using clean service-layer architecture — without unnecessary abstractions like full DDD or repository layers.

---
# Code Efficiency Guidelines

<token_optimization>
When writing code:
- **Plan first**: Outline the most direct path to the solution
- **Minimize verbosity**: Remove unnecessary comments, whitespace, and redundant code
- **Leverage built-ins**: Prefer standard library over custom implementations
- **Avoid repetition**: Extract common patterns into reusable functions/methods
- **Optimal algorithms**: Choose O(n) over O(n²) where practical
- **Context-aware**: Provide only requested code sections, not entire files unless needed

Balance: Optimize for tokens while maintaining code clarity and correctness.
</token_optimization>

--

## 🧭 1. Core Development Philosophy

-   Build fast, maintain clean.
-   Organize by **feature** (Billing, Auth, Payments, Consumers).
-   Keep logic out of controllers — **Services handle business rules.**
-   Use **Eloquent models** directly for data access (no repository abstraction).
-   Use **Events** or **Observers** only when logic must be decoupled (e.g., audit logs, ledger updates).
-   Write code as if someone else will maintain it in a year.

---

## 🧩 2. Folder & Module Structure

```
app/
  Http/
    Controllers/
      Auth/
        AuthController.php
        RegisterController.php
      Billing/
        BillingController.php
        AdjustmentController.php
      Payments/
        PaymentController.php
      Consumers/
        ConsumerController.php
  Services/
      Auth/
        AuthService.php
      Billing/
        BillingService.php
        AdjustmentService.php
      Payments/
        PaymentService.php
      Consumers/
        ConsumerService.php
  Models/
      User.php
      Consumer.php
      WaterBill.php
      MeterReading.php
      PaymentTransaction.php
  Rules/
  Traits/
  Events/
  Listeners/
```

-   **Controllers/** handle requests, validation, and responses.
-   **Services/** encapsulate logic specific to each domain.
-   **Models/** represent entities and relationships (Eloquent).
-   **Events/** and **Listeners/** handle asynchronous or side-effect flows.

---

## ⚙️ 3. Controller Rules

**Purpose:** Orchestrate flow between request → service → response.

-   **No business logic** inside controllers.
-   **Use FormRequest** classes for validation.
-   **Sanitize input** where needed (trim, normalize).
-   **Return JSON** for APIs or use Blade for small admin pages.

✅ Example:

```php
public function generate(BillingRequest $request, BillingService $billing)
{
    $count = $billing->generateForPeriod($request->period_id);

    return response()->json([
        'message' => "Billing generated for {$count} consumers."
    ]);
}
```

---

## 🧠 4. Service Layer Rules

**Purpose:** Handle all business logic for a specific feature.

-   Services should be **stateless** and **self-contained**.
-   Services may directly use **Eloquent models**.
-   If a process spans multiple models (e.g., billing + payments), orchestrate it here.
-   Keep helper logic reusable in `Traits` or `Support` classes.

✅ Example:

```php
class BillingService
{
    public function generateForPeriod(int $periodId): int
    {
        $consumers = Consumer::with('latestReading')->get();
        $count = 0;

        foreach ($consumers as $consumer) {
            $usage = $this->calculateUsage($consumer);
            $rate = $this->getRate($consumer);

            WaterBill::create([
                'consumer_id' => $consumer->id,
                'period_id' => $periodId,
                'consumption' => $usage,
                'amount' => $usage * $rate,
            ]);

            $count++;
        }

        return $count;
    }

    protected function calculateUsage(Consumer $consumer): float
    {
        $current = $consumer->latestReading->value ?? 0;
        $previous = $consumer->previousReading()?->value ?? 0;
        return max(0, $current - $previous);
    }
}
```

---

## 💾 5. Models (Eloquent)

**Purpose:** Represent business entities and handle persistence.

-   Define **relationships**, **casts**, and **accessors** clearly.
-   Use **scopes** for common filters (`active()`, `forPeriod()`).
-   Do **not** include business logic (keep it in services).

✅ Example:

```php
class Consumer extends Model
{
    public function latestReading()
    {
        return $this->hasOne(MeterReading::class)->latestOfMany();
    }

    public function previousReading()
    {
        return $this->hasMany(MeterReading::class)
            ->orderByDesc('reading_date')
            ->skip(1)
            ->first();
    }
}
```

---

## 🔐 6. Validation & Requests

-   Always validate incoming requests using `FormRequest` classes.
-   Place requests near their related feature folder.

✅ Example:

```
app/Http/Requests/Billing/BillingRequest.php
```

Rules should be expressive, and avoid inline validation logic in controllers.

---

## 🔁 7. Events & Listeners (Optional but Encouraged)

Use when side effects must be handled asynchronously or independently:

-   Example: after `WaterBill` is generated → create `LedgerEntry`, or log audit.

✅ Example:

```php
event(new BillGenerated($consumer, $periodId));
```

Keep event listeners short and focused.

---

## 🧪 8. Testing Rules

-   Use **Feature tests** for API endpoints and full flows.
-   Use **Unit tests** for core service logic (e.g., billing calculations).
-   Prefer **SQLite in-memory DB** for faster test runs.

✅ Command:

```
php artisan test --parallel
```

---

## 🚀 9. Deployment Rules

-   The project must run on **a single Laravel instance** (no separate FE).
-   Use **Docker Compose** for consistency across machines.
-   `.env` values must never be committed.
-   Local queue: Redis + `php artisan queue:work` or `horizon`.

✅ Example docker services:

```
app, nginx, mysql, redis, phpmyadmin
```

---

## 🔒 10. Security & Integrity Rules

-   Always use **prepared queries** (Eloquent or Query Builder).
-   **Never trust user input**; sanitize all external data.
-   Protect sensitive routes with **policies** or **gates**.
-   **Rate-limit** authentication and payment endpoints.
-   Use `bcrypt` or `argon2` for passwords (Laravel default).
-   Ensure audit logging for billing, payments, and adjustments.

---

## ⚡ 11. Performance Rules

-   Always **paginate** data on listing endpoints.
-   Use **eager loading** to avoid N+1 queries.
-   Add DB **indexes** for:

    -   `consumer_id`
    -   `reading_date`
    -   `bill_status`

-   Cache heavy reports or analytics queries with Redis.
-   Enable **OPcache** in production.

---

## 📊 12. Naming & Coding Conventions

| Item               | Convention          | Example                                  |
| ------------------ | ------------------- | ---------------------------------------- |
| **Controller**     | `FeatureController` | `BillingController`, `PaymentController` |
| **Service**        | `FeatureService`    | `BillingService`, `AuthService`          |
| **Request**        | `FeatureRequest`    | `BillingRequest`                         |
| **Event**          | `ActionOccurred`    | `BillGenerated`, `PaymentReceived`       |
| **Model**          | Singular            | `WaterBill`, `Consumer`                  |
| **Database table** | snake_case plural   | `water_bills`, `consumers`               |

Follow **PSR-12** + Laravel naming + trailing commas + strict typing when possible.

---

## 🧱 13. Commit & Git Rules

-   Each commit = one logical change.
-   Prefix commits by type:

    -   `feat(billing): add billing generation logic`
    -   `fix(payments): correct rounding issue`
    -   `chore: update composer dependencies`

-   Run `php artisan test` before pushing.
-   Never commit `.env` or credentials.

---

## 🧭 14. Summary — Development Principles

| Principle         | Description                                    |
| ----------------- | ---------------------------------------------- |
| **KISS**          | Keep It Simple, Scalable                       |
| **DRY**           | Reuse services and traits                      |
| **SRP**           | One class = one responsibility                 |
| **Separation**    | Controllers handle flow, Services handle logic |
| **Fast Delivery** | Prioritize completion, not abstraction         |
| **Readability**   | Code should explain itself                     |

---

## ✅ 15. Quick Setup Checklist

-   [ ] `composer install`
-   [ ] `cp .env.example .env` + configure
-   [ ] `php artisan migrate --seed`
-   [ ] `php artisan key:generate`
-   [ ] `php artisan serve`
-   [ ] Verify `BillingController`, `PaymentController` endpoints working
