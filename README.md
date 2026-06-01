# MauticMarketingPlannerBundle

A shared marketing calendar plugin for [Mautic](https://www.mautic.org/) that lets your whole team plan, track and manage marketing activities in one place.

![Marketing Planner](https://via.placeholder.com/900x400/4e5d6c/ffffff?text=Marketing+Planner+Calendar)

---

## Features

- **Month view** — full calendar grid (Mon–Sun weeks) with items on their deadline day
- **Year view** — month-by-month overview with item counts and quick links
- **List view** — all tasks sorted by deadline; overdue items highlighted in red
- **Quick done toggle** — mark any item done (or undo) with one click from any view
- **Shared** — all logged-in users see and can manage all items
- **Assignable** — items can be assigned to any Mautic user
- **Permissions** — plugs into Mautic's role system (`plugin:marketingplanner:items`)
- **Demo data** — ships with 14 sample items across June–August so you see it working immediately
- **Main menu** — calendar icon at the bottom of the left navigation

---

## Requirements

| Requirement | Version |
|---|---|
| Mautic | 4.x / 5.x |
| PHP | 8.0+ |
| MySQL / MariaDB | 5.7+ / 10.3+ |

---

## Installation

### 1. Copy the plugin

```bash
cp -r MauticMarketingPlannerBundle /path/to/mautic/docroot/plugins/
```

### 2. Clear cache

```bash
php bin/console cache:clear
```

### 3. Install the plugin

```bash
php bin/console mautic:plugins:install
```

### 4. Create the database table

The plugin ships with a Doctrine migration at `app/migrations/Version20260601120000.php`.  
Copy it to your Mautic installation's `app/migrations/` directory, then run:

```bash
php bin/console doctrine:migrations:migrate --no-interaction
```

> **Alternatively**, if migrations are tricky in your setup, run the raw SQL directly:
>
> ```sql
> CREATE TABLE IF NOT EXISTS `mtc_planner_items` (
>     `id`              INT NOT NULL AUTO_INCREMENT,
>     `name`            VARCHAR(255) NOT NULL,
>     `description`     LONGTEXT,
>     `created_at`      DATETIME NOT NULL,
>     `deadline`        DATE NOT NULL,
>     `done_at`         DATE DEFAULT NULL,
>     `assigned_to_id`  INT DEFAULT NULL,
>     PRIMARY KEY (`id`),
>     INDEX `idx_planner_deadline` (`deadline`),
>     INDEX `idx_planner_assigned` (`assigned_to_id`)
> ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
> ```
> *(Replace `mtc_` with your configured table prefix.)*

---

## Usage

Navigate to `/s/planner` or click the **calendar icon** at the bottom of the left sidebar.

### Views

| View | URL | Description |
|---|---|---|
| Month | `/s/planner?view=month&year=2026&month=6` | Calendar grid, Mon–Sun weeks |
| Year | `/s/planner?view=year&year=2026` | All 12 months with activities |
| List | `/s/planner?view=list` | All items sorted by deadline |

Use **← →** arrows to navigate between months/years. The **Today** button returns to the current month.

### Adding items

Click **Add item** (top right of any view) or go to `/s/planner/new`.

Each item has:

| Field | Required | Notes |
|---|---|---|
| Name | Yes | Short task label shown in the calendar |
| Description | No | Longer free-text notes |
| Deadline | Yes | The date shown in the calendar |
| Assigned To | No | Any Mautic user |
| Done Date | No | Set automatically via the Done button; editable in the form |

### Marking items done

Click the **✓** button next to any item in the List or Year view. Done items appear greyed out with a strikethrough. Click again to undo.

---

## Permissions

The plugin registers the permission group `plugin:marketingplanner:items` in Mautic's role system.

Standard permission levels are available:
- `viewown` / `viewother`
- `editown` / `editother`
- `create`
- `deleteown` / `deleteother`

By default, grant all levels to the roles that should have full access. Configure at **Admin → Roles**.

> The main menu item only appears for users who have `viewother` permission.

---

## Development

### Run locally with DDEV

```bash
ddev start
ddev exec php bin/console cache:clear
ddev exec php bin/console mautic:plugins:install
ddev exec php bin/console mautic:uli 1   # get a one-time login link
```

### File structure

```
MauticMarketingPlannerBundle/
├── Config/
│   └── config.php                        Routes, menu, services
├── Controller/
│   └── PlannerController.php             All actions + calendar helpers
├── Entity/
│   ├── PlannerItem.php                   Doctrine entity (ClassMetadataBuilder)
│   └── PlannerItemRepository.php         Query methods
├── Form/
│   └── Type/
│       └── PlannerItemType.php           Symfony form type
├── Integration/
│   └── MarketingPlannerIntegration.php   Mautic integration (icon in plugin list)
├── Security/
│   └── Permissions/
│       └── PlannerPermissions.php        Mautic role permissions
├── Resources/
│   └── views/
│       └── Planner/
│           ├── index.html.twig           Calendar overview (all 3 views)
│           └── form.html.twig            New / edit form
└── README.md
```

The migration lives in the parent Mautic installation at:
```
app/migrations/Version20260601120000.php
```

---

## Changelog

### 1.0.0 (2026-06-01)
- Initial release
- Month, Year and List calendar views
- CRUD for planning items
- Quick done toggle
- Demo data migration
- Mautic role permissions

---

## License

MIT — see [LICENSE](LICENSE)

---

## Author

Built by [Dropsolid](https://dropsolid.com) as part of the Mautic AI & productivity plugin suite.
