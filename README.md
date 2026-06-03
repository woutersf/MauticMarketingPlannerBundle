# MauticMarketingPlannerBundle

A shared marketing calendar plugin for Mautic. Plan and track marketing activities across your team with month, year and list views.

Created by [Dropsolid](https://dropsolid.com) · [Frederik Wouters](https://frederikwouters.be/)

---

## Screenshots

![Month view](Assets/img/month-view.png)
![List view](Assets/img/list-view.png)

---

## Requirements

- Mautic 5.x / 7.x
- PHP 8.0+

---

## Installation

1. Copy the plugin into `docroot/plugins/MauticMarketingPlannerBundle/`
2. Clear the cache: `php bin/console cache:clear`
3. Install the plugin: `php bin/console mautic:plugins:install`
4. Create the database table:

```sql
CREATE TABLE IF NOT EXISTS `mtc_planner_items` (
    `id`             INT NOT NULL AUTO_INCREMENT,
    `name`           VARCHAR(255) NOT NULL,
    `description`    LONGTEXT,
    `created_at`     DATETIME NOT NULL,
    `deadline`       DATE NOT NULL,
    `done_at`        DATE DEFAULT NULL,
    `assigned_to_id` INT DEFAULT NULL,
    PRIMARY KEY (`id`),
    INDEX `idx_planner_deadline` (`deadline`),
    INDEX `idx_planner_assigned` (`assigned_to_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

*(Replace `mtc_` with your configured table prefix if different.)*

---

## Features

**Three views** — switch with the toolbar buttons:

| View | Description |
|---|---|
| List | All items sorted by deadline. Overdue dates highlighted red. |
| Month | Calendar grid (Mon–Sun). Items appear as chips on their deadline day. |
| Year | All 12 months, each listing that month's items. |

**Planning items** have:
- Name
- Description
- Deadline (the date shown in the calendar)
- Assigned to (any Mautic user)
- Done date (set via the done button or manually in the edit form)

**Done toggle** — one-click mark done/undo from the list and year views. Done items appear greyed out with a strikethrough in all views.

**Shared** — all logged-in users with the appropriate permission see and can manage all items.

**Permissions** — the plugin registers `plugin:marketingplanner:items` in Mautic's role system. Configure per role at Admin → Roles.

---

## Navigation

The planner is accessible via the calendar icon at the bottom of the left sidebar, or directly at `/s/planner`.

---

## Demo data

A migration at `app/migrations/Version20260601120000.php` creates the table and inserts 14 sample items across June–August 2026.

Run it with:

```bash
php bin/console doctrine:migrations:migrate --no-interaction
```
