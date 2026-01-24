# AGENT INSTRUCTIONS

## Core Philosophy

This agent operates as a PHP/Full-Stack developer with strict adherence to DRY (Don't Repeat Yourself) principles.

## Pre-Implementation Checklist

Before writing ANY code, the agent MUST:

1. **Consult the Codemap First**
   - Check `data/metadata/utility.metadata.json` for existing utility functions
   - Check `data/metadata/article.metadata.json` for content patterns
   - Check `data/metadata/project.metadata.json` for project templates
   - Review `includes/` directory for reusable PHP components

2. **Search for Similar Implementations**
   - Query existing functions in `includes/util.php`
   - Check template components: `header.php`, `footer.php`, `navbar.php`
   - Review JavaScript utilities in `js/` directory
   - Examine existing database patterns in `data/database/`

3. **Evaluate Before Creating**
   - Can an existing function be extended?
   - Can an existing component be parameterized?
   - Is there a template that can be adapted?

## Implementation Rules

### Rule 1: Extend, Don't Duplicate
```php
// ❌ BAD: Creating new similar function
function generateNewID() { ... }

// ✅ GOOD: Use existing generateUniqueID() from util.php
require_once 'includes/util.php';
$id = generateUniqueID();
```

### Rule 2: Parameterize Components
```php
// ❌ BAD: Hardcoded includes
include 'header.php';

// ✅ GOOD: Use variables for flexibility
$page_title = "Blog";
include 'includes/header.php';
```

### Rule 3: Centralize Configuration
- Database config lives in `includes/db_config.php`
- Metadata lives in `data/metadata/`
- Styles consolidated in `css/style-main.css`

### Rule 4: Document New Code in Codemap
When creating new utilities, immediately update:
- `utility.metadata.json` for functions/components
- `article.metadata.json` for content structures
- `project.metadata.json` for project patterns

## Response Protocol

1. **State what was searched** in the codemap
2. **Identify matches** or similar existing code
3. **Propose adaptation** of existing code OR justify new implementation
4. **Provide minimal, non-redundant code** that integrates with existing structure
5. **Include codemap update** if new patterns are introduced

## Existing Assets Reference

| Asset | Location | Purpose |
|-------|----------|---------|
| `generateUniqueID()` | `includes/util.php` | 6-digit unique IDs |
| Header template | `includes/header.php` | Page head, meta, CSS/JS |
| Footer template | `includes/footer.php` | Copyright, closing tags |
| Navbar | `includes/navbar.php` | Navigation component |
| Canvas particles | `js/canvas.js` | Interactive backgrounds |
| DB config | `includes/db_config.php` | MySQL connection |

