# Store Order & Inventory Mini-System

A small Laravel application for a retail counter. It records customer orders against a
product catalog and keeps stock in sync, with a queued order-confirmation job and
protection against overselling under concurrent requests.

> Take-home assignment for Mallow Technologies (Laravel Developer).

- **Repository:** [[your GitHub link](https://github.com/ashabyayush/store-inventory-task.git)]
- **Screen recording:** [[your video link](https://drive.google.com/file/d/1zYbS3KhTKR7JJC7wea1adQg6ijpP4ziF/view?usp=drive_link)]


---

## Features

| Area | What it does |
|---|---|
| Orders | Create an order with one or more product lines. Validates stock, computes subtotal, tax and grand total, deducts stock, returns the created order. |
| Order history | Grid of all orders (10 per page, newest first). Filter by customer email. Click a row to see its items. |
| Customers | List with order count and total spent. Search by name or email. |
| Products | Add, edit, delete, filter by category. |
| Categories | Add, rename, delete. |
| Low stock | Products below a configurable threshold. |
| Queued job | Order-confirmation "email" is dispatched to the queue on order creation. |
| Configuration | Choose display currency (INR, USD, EUR, GBP). Edit the store login name, email and password. |
| Auth | Session login, no public registration. |
| UI | Bootstrap 5, responsive. Fixed sidebar on desktop, slide-in menu on mobile. |

## Tech stack

- PHP 8.2+, Laravel 11+
- MySQL (row locking relies on InnoDB)
- Database queue driver
- Bootstrap 5 and Bootstrap Icons, bundled locally in `public/css` and `public/js` (works offline)
- PHPUnit for tests

---

## Setup

### Requirements
PHP 8.2+, Composer, MySQL 8 (or MariaDB 10.4+, e.g. XAMPP).

### Steps

```bash
git clone [repo-url]
cd [repo-folder]

composer install
cp .env.example .env          # Windows: copy .env.example .env
php artisan key:generate
```

Create an empty database (for example `store_inventory`) and set it in `.env`:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=store_inventory
DB_USERNAME=root
DB_PASSWORD=

QUEUE_CONNECTION=database
LOW_STOCK_THRESHOLD=10
```

Then:

```bash
php artisan migrate --seed
```

### Run

Use two terminals:

```bash
php artisan serve          # terminal 1 -> http://127.0.0.1:8000
php artisan queue:work     # terminal 2 -> processes the confirmation-email job
```

### Login (seeded)

| Email | Password |
|---|---|
| `admin@store.test` | `password` |

Change these from **Configuration** after the first login. These are demo credentials for local use only.

The seeder also creates sample categories, products and customers (one customer is `asha@example.com`).

### Tests

```bash
php artisan test
```

---

## API

All endpoints require a logged-in session (JSON, `Accept: application/json`).

| Method | Endpoint | Purpose |
|---|---|---|
| POST | `/api/orders` | Create an order |
| GET | `/api/orders?email=&page=` | Paginated order list, optional email filter |
| GET | `/api/customers/orders?email=` | One customer's full order history (404 if unknown) |
| GET | `/api/customers?search=` | Customers with order count and total spent |
| GET | `/api/products/low-stock?threshold=` | Products below the threshold |
| GET / POST | `/api/products` | List (optional `category_id`) / create |
| PUT / DELETE | `/api/products/{id}` | Update / delete |
| GET / POST | `/api/categories` | List / create |
| PUT / DELETE | `/api/categories/{id}` | Rename / delete |
| GET | `/api/settings` | Current currency and account |
| PUT | `/api/settings/currency` | Change currency |
| PUT | `/api/settings/account` | Change login name, email, password |

### Create order

```http
POST /api/orders
Content-Type: application/json

{
  "customer_name": "Asha Raman",
  "customer_email": "asha@example.com",
  "items": [
    { "product_id": 1, "quantity": 3 },
    { "product_id": 2, "quantity": 2 }
  ]
}
```

- `201` with the order, its items, and the customer when successful.
- `422` with a message when validation fails or stock is insufficient.

---

## Database design

```
categories 1 ──< products
customers  1 ──< orders 1 ──< order_items >── 1 products
settings (key / value)
users (login)
```

| Table | Key columns |
|---|---|
| `categories` | `name` (unique) |
| `products` | `category_id`, `name`, `code` (unique), `price`, `tax_percent`, `stock` |
| `customers` | `name`, `email` (unique) |
| `orders` | `order_number` (unique, e.g. `ORD-000001`), `customer_id`, `subtotal`, `tax_total`, `grand_total`, `email_status` (`queued` / `emailed`) |
| `order_items` | `order_id`, `product_id`, `quantity`, `unit_price`, `tax_percent`, `tax_amount`, `line_total` |
| `settings` | `key` (unique), `value` |

Money is stored as `DECIMAL`, never float.

---

## Design decisions

**Thin controllers, logic in a service.** Controllers only validate (Form Requests) and
delegate. All order logic lives in `App\Services\OrderService`.

**Price snapshot on order items.** `order_items` stores the unit price and tax percent
at the time of sale, so changing a product's price later does not alter past orders.

**Tax is calculated per line and rounded to 2 decimals.** Order totals are the sum of the line values.

**Queued job after commit.** `SendOrderConfirmation` is dispatched only after the order
transaction commits, so the job never runs for an order that was rolled back. It writes a
log entry (the simulated email) and flips `email_status` from `queued` to `emailed`.

**Guarded deletes.** A category with products cannot be deleted. A product that appears in
past orders cannot be deleted (it would break order history). The UI shows a popup
explaining why.

**Auth.** Standard Laravel session auth. The `/api` routes use the `web` + `auth`
middleware so the same session protects both pages and API.

## Concurrency (no overselling)

Stock check and deduction happen inside one database transaction:

1. Requested lines are merged per product (the same product twice on one order is summed).
2. The product rows are loaded with `lockForUpdate()`, ordered by id to avoid deadlocks.
3. Stock is checked against the locked rows. If any line is short, an
   `InsufficientStockException` is thrown, the whole transaction rolls back, and the API
   returns `422`. No partial order, no partial stock deduction.
4. Otherwise the order is created and stock is decremented before the lock is released.

If two requests compete for the last unit, the second one waits for the lock, then sees
stock `0` and fails cleanly with `422`. Exactly one succeeds.

**Testing note:** the PHPUnit suite runs on in-memory SQLite, which ignores row locks.
The tests therefore verify the logic (rollback, merged lines, "last unit can only be sold
once" sequentially). The locking itself depends on MySQL/InnoDB.

**Manual check:** set a product's stock to 1, open **New Order** in two browser tabs,
select that product in both, and click **Place order** in both as close together as
possible. One order succeeds and the other shows the insufficient-stock message.

## Tests

`php artisan test` covers:

- Order creation: totals, tax, stock deduction, job dispatched
- Insufficient stock (rejected, stock unchanged, nothing queued)
- Rollback of other lines when one line fails
- Duplicate lines merged before the stock check
- Last unit sold only once
- Form validation errors
- Low-stock and order-history endpoints
- Order list filter by email, customers search
- Category and product edit and delete rules
- Currency and account settings (current password required)
- Login, logout, and guest redirects

---

## Assumptions

The brief left these open, so I made these calls:

1. **Same product on several lines** is merged into one line before the stock check.
2. **Existing customer email** reuses that customer. The name is not overwritten.
3. **Low stock** means `stock < threshold` (strictly below). Default 10 via
   `LOW_STOCK_THRESHOLD`, overridable with `?threshold=`.
4. **Tax** is a per-product percentage, applied to the line subtotal.
5. **Confirmation email** is simulated by a log entry in a queued job (`storage/logs/laravel.log`). No SMTP.
6. **Currency setting changes the display symbol only.** Stored amounts are not converted.
7. **Products and categories are hard-deleted** only when safe (see guarded deletes).
8. **Registration is not public.** Users are created by seeder or tinker:
   `php artisan tinker --execute="App\Models\User::create(['name'=>'Ravi','email'=>'ravi@store.test','password'=>'secret123']);"`
9. **Extras beyond the brief:** login, dashboard, categories, customers, configuration
   page, edit and delete screens.

## Known limitations

- Editing a product's **stock** from the Products screen sets an absolute value. If an
  order is placed at the same instant, the edit can overwrite that deduction. A production
  version would use stock-adjustment records (restock / correction) instead.
- Currency has no exchange-rate conversion.
- Single role: every logged-in user is an admin.
- Email is simulated only.

## Project structure

```
app/
  Exceptions/InsufficientStockException.php
  Http/Controllers/Api/        # Order, Customer, Product, Category, Setting controllers
  Http/Requests/               # StoreOrderRequest, StoreProductRequest, UpdateProductRequest
  Http/Middleware/NoCache.php  # stops Back button showing a cached page after logout
  Jobs/SendOrderConfirmation.php
  Models/                      # Category, Product, Customer, Order, OrderItem, Setting
  Services/OrderService.php    # order creation, locking, totals
database/                      # migrations, factories, seeders
resources/views/               # Blade: layout, sidebar component, pages, login
public/css, public/js          # Bootstrap (local) and the app's own CSS/JS
tests/Feature/                 # OrderCreationTest, ManagementTest, AuthTest
```

## AI-assisted development

The brief encourages AI tools, so I used **Claude** as a coding assistant.
Screenshots of the actual prompts are in [`/prompts`](./prompts).

**How I worked**
- I set up the Laravel project and created the models and migration files myself.
- I shared the brief and the wireframe with Claude, asked for a UI like the wireframe,
  and asked for the next steps with the files and commands.
- Claude generated the code from those prompts. I ran each step locally on
  Windows/XAMPP and pasted back the errors I hit, then applied the fixes.
- I then iterated feature by feature: categories, a reusable sidebar and external CSS,
  login and dashboard, customers, order history grid with filter, configuration,
  edit and delete screens, responsive Bootstrap layout, and colour changes.

**Decisions I made**
- Reusable sidebar and layout as Blade components, with CSS and JS in external files.
- A categories page, and a customers menu.
- No public registration; the login is managed from the Configuration page.
- Order history shows all orders in a grid by default; the email filter narrows it down.
- Categories and products cannot be deleted when that would break data
  (active products / past orders), and a popup explains why.
- Low stock means strictly below the threshold.
- Currency changes the display symbol only, with no conversion.
- Bootstrap 5 files are bundled locally so the app works offline.
- A dark-blue theme shared by the sidebar and the login page.

**Problems I worked through**
- Bash commands from the first answer failed on Windows PowerShell; switched to PowerShell equivalents.
- `optimize:clear` failed before the cache table existed on a fresh database; reordered the commands.
- Checked that migration files run in the right order for the foreign keys.
- Logging out and pressing Back showed a stale page and a 419 "Page Expired";
  fixed with a no-cache middleware and a redirect to the login page.


**Verification**
- `php artisan test` passes (22 tests).
✓ low stock and history endpoints                                                0.06s  
  ✓ category with products cannot be deleted                                       0.06s  

  Tests:    22 passed (84 assertions)
  Duration: 3.68s
- I checked each screen on desktop and mobile width in the browser.