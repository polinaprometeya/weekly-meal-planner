# Weekly Meal Planner — Data Plan

Action plan for **data structure**, **recipe & ingredient collection**, and **supermarket pack sizes** (Denmark, 2026).

This document reflects the intended approach for `api-wmp` (Laravel API) and future UI in `web-mp`.

---

## 1. Goals

| Goal | Description |
|------|-------------|
| **Meal planning** | Assign common Danish dishes to days in a week |
| **Shopping lists** | Aggregate ingredient amounts across planned recipes |
| **Cooking instructions** | Show how to make each dish (reviewed text, no photos) |
| **Pack-aware shopping** (later) | Convert totals (e.g. 1.2 L milk) into “buy 2 × 1 L” using typical DK pack sizes |

**Principle:** Build value from the **recipe + staple** side first. Add supermarket pack data when shopping-list math needs it.

---

## 2. Core principles

### Licensing

- Use only **openly licensed** or **public domain** recipe sources.
- Record **source URL**, **license**, and **attribution** on every recipe.
- Prefer **your own wording** for instructions after review; keep structured ingredient amounts as your curated data.
- **Do not include photos** (copyright and storage complexity).
- Avoid bulk scraping of commercial recipe sites.

### Licenses to prefer

| License | Notes |
|---------|--------|
| **CC0** | Public domain; minimal obligations |
| **CC BY** | Free use; **credit author/source** |
| **CC BY-SA** | Same as BY; derivatives must use same license |
| **Public domain** (e.g. pre-1928 US scans, EU life+70y) | Verify per title; keep attribution where requested |

**Avoid or restrict:** “All rights reserved” blogs; **CC BY-NC** if the app may become commercial.

### Data quality

- **Manual review** before a recipe is published (`is_reviewed`).
- Store both **structured ingredients** (for math) and **prose instructions** (for humans).
- Optional: keep **raw scraped text** until review is complete.

---

## 3. Data architecture

### 3.1 Entity overview

```
staples ─────────────┐
                     ├── recipe_ingredients ─── recipes
food_pack_norms ─────┘         (staple_id)      (metadata + prose)

meal_plans ─── meal_plan_entries ─── recipes
```

### 3.2 Tables (logical model)

#### `staples` (canonical ingredients)

Single source of truth for shopping and recipe lines.

| Column | Type | Notes |
|--------|------|--------|
| `id` | PK | |
| `slug` | string, unique | e.g. `milk-whole`, `minced-beef` |
| `name_da` | string | Display name (Danish) |
| `category` | string, nullable | e.g. `dairy`, `meat`, `produce` |
| `default_unit` | enum | `g`, `ml`, `stk`, `tsk`, `dl` — canonical unit for aggregation |
| `notes` | text, nullable | e.g. “typisk 8–12% fedt” |
| `created_at`, `updated_at` | timestamps | |

**Action:** Seed ~50–150 staples before scaling recipes.

---

#### `recipes`

| Column | Type | Notes |
|--------|------|--------|
| `id` | PK | |
| `slug` | string, unique | e.g. `boller-i-karry` |
| `name_da` | string | |
| `servings_default` | integer | Usually `4` |
| `instructions` | text | **Reviewed** steps (markdown or plain text) |
| `source_instructions_raw` | text, nullable | Scraped/historical text before cleanup |
| `source_type` | enum | `manual`, `wikibooks`, `public_domain_book`, `other_open` |
| `source_title` | string, nullable | Book/site title |
| `source_url` | string, nullable | |
| `publication_year` | integer, nullable | |
| `license` | string | e.g. `CC-BY-4.0`, `CC0`, `PUBLIC_DOMAIN` |
| `attribution_text` | string, nullable | Required for CC BY |
| `prep_minutes` | integer, nullable | Optional |
| `cook_minutes` | integer, nullable | Optional |
| `tags` | json, nullable | e.g. `["hverdagsmad", "kylling"]` |
| `is_reviewed` | boolean | Default `false` |
| `reviewed_at` | timestamp, nullable | |
| `created_at`, `updated_at` | timestamps | |

**Rule:** API and UI only expose recipes where `is_reviewed = true` (until admin tools exist).

---

#### `recipe_ingredients`

Structured lines linked to staples — powers meal plans and shopping lists.

| Column | Type | Notes |
|--------|------|--------|
| `id` | PK | |
| `recipe_id` | FK → recipes | |
| `staple_id` | FK → staples | |
| `amount` | decimal | Quantity in recipe |
| `unit` | string | `g`, `ml`, `stk`, `tsk`, `dl`, etc. |
| `note` | string, nullable | e.g. “til saucen”, “finthakket” |
| `sort_order` | integer | Display order |
| `created_at`, `updated_at` | timestamps | |

**Action:** Normalize all units to staples’ `default_unit` where possible (e.g. convert dl → ml for liquids) via app logic or seed scripts.

---

#### `food_pack_norms` (supermarket — phase 2)

Typical Danish pack sizes per staple (not live prices).

| Column | Type | Notes |
|--------|------|--------|
| `id` | PK | |
| `staple_id` | FK → staples | |
| `pack_amount` | decimal | e.g. `1.0`, `500` |
| `pack_unit` | string | `L`, `g`, `stk` |
| `label_da` | string, nullable | e.g. “standard mælk 1 L” |
| `source` | enum | `manual`, `openfoodfacts`, `scraped`, `receipts` |
| `confidence` | enum | `high`, `medium`, `low` |
| `last_verified` | date, nullable | |
| `notes` | text, nullable | |
| `created_at`, `updated_at` | timestamps | |

**Action:** Start with **manual** norms for top ~80 staples; scraping is optional later.

---

#### `meal_plans` & `meal_plan_entries` (phase 3)

| Table | Purpose |
|-------|---------|
| `meal_plans` | Week for a user/household: `start_date`, `name`, etc. |
| `meal_plan_entries` | `meal_plan_id`, `recipe_id`, `day_of_week`, `meal_slot` (optional) |

Shopping list = sum `recipe_ingredients` for all entries, scaled by servings, grouped by `staple_id`, then optionally divided by `food_pack_norms`.

---

## 4. Implementation phases (Laravel)

### Phase A — Foundation

- [ ] Migrations: `staples`, `recipes`, `recipe_ingredients`
- [ ] Eloquent models + relationships
- [ ] Seeders: starter staples (50+) and 3–5 example recipes (fully reviewed)
- [ ] API endpoints: list staples, list/show reviewed recipes

### Phase B — Recipe workflow

- [ ] Admin or internal import: CSV/JSON → recipes + ingredients
- [ ] Fields: `source_instructions_raw` → manual edit → `instructions`
- [ ] `is_reviewed` gate on public API
- [ ] Document attribution in API response or “About this recipe” UI

### Phase C — Meal planning

- [ ] `meal_plans`, `meal_plan_entries`
- [ ] Endpoint: generate shopping list for a plan (aggregate by staple)

### Phase D — Supermarket packs

- [ ] Migration: `food_pack_norms`
- [ ] Manual seed JSON for DK defaults
- [ ] Shopping list v2: “need 1.2 L → buy 2 × 1 L”

### Phase E — Optional automation

- [ ] Open Food Facts lookup by barcode (packaged goods only)
- [ ] Single-retailer scrape prototype (low priority; high maintenance)

---

## 5. Data collection — recipes & ingredients

### 5.1 Recommended order

1. **Staples** — define canonical names and units  
2. **Recipes (structured)** — 20–40 common Danish weeknight dishes  
3. **Instructions (prose)** — reviewed text per recipe  
4. **Meal plans** — wire aggregation  
5. **Pack norms** — when shopping “buy N packs” matters  

### 5.2 Manual curation (primary method)

| Step | Action |
|------|--------|
| 1 | Pick dish; confirm open license or write original structured data |
| 2 | Enter `recipe_ingredients` mapped to `staple_id` + amounts for `servings_default` |
| 3 | Paste source text into `source_instructions_raw` if applicable |
| 4 | Write clear `instructions` in modern Danish |
| 5 | Fill `source_url`, `license`, `attribution_text` |
| 6 | Set `is_reviewed = true`, `reviewed_at` |

**Starter dish ideas:** boller i karry, hakkebøf, kylling i karry, pasta med kødsovs, grøntsagssuppe, æggekage, fiskefrikadeller, rugbrød + pålæg-style plates, etc.

### 5.3 Open sources (recipes)

| Source | Best for | License / notes |
|--------|----------|-----------------|
| [Wikibooks Cookbook](https://en.wikibooks.org/wiki/Cookbook) | Modern-ish steps; practice attribution | CC BY-SA |
| [Project Gutenberg](https://www.gutenberg.org/) | Old cookbooks, English often | Public domain (per title) |
| [Internet Archive](https://archive.org/) | Scanned books | Check per-item usage |
| [Det Kgl. Bibliotek](https://www.kb.dk/en/collections/digital-collections) | Historical Danish (e.g. 1600s–1700s) | Many PD; verify PDF terms |
| [Madhistorie.nu – digitised cookbooks](http://madhistorie.nu/digitaliserede-kogeboeger/) | Links to early DK cookbooks | Follow linked KB/license |
| [Feeding America](https://d.lib.msu.edu/fa) | US historical reference | Academic/open access |

**Expectation:** Old books are rich for **prose and history**, weak for modern staples (units, language, portions). Use for inspiration; **you** produce structured lines and modern instructions.

### 5.4 Scraping (optional, careful)

- Only sites with **explicit open license**.
- Prefer **Schema.org `Recipe` JSON-LD** on product pages when allowed.
- Store scrape in `source_instructions_raw`; never auto-publish.
- Rate-limit requests; cache locally; respect `robots.txt`.

### 5.5 What not to do

- Bulk scrape commercial recipe sites (DR, major blogs, etc.).
- Ship unreviewed recipes to users.
- Rely on photos or copyrighted long-form text without permission.

---

## 6. Data collection — supermarket pack sizes

### 6.1 Purpose

Convert aggregated staple totals into practical purchases:

> Need **1.2 L** milk + **0.5 L** milk elsewhere → **2 × 1 L** cartons (given norm 1 L).

### 6.2 Recommended approach (v1)

| Priority | Method | Effort | Stability |
|----------|--------|--------|-----------|
| 1 | **Manual DK defaults** table | Low | High |
| 2 | User/household overrides (future) | Medium | High |
| 3 | [Open Food Facts](https://world.openfoodfacts.org) | Medium | Patchy for fresh food |
| 4 | Retailer scraping (one chain) | High | Low (breaks often) |

### 6.3 Example manual norms (seed data)

| Staple | Typical pack | Unit |
|--------|----------------|------|
| Milk (whole/semi) | 1 | L |
| Minced beef | 400–500 | g |
| Butter | 200 | g |
| Eggs | 6 or 10 | stk |
| Pasta | 500 | g |
| Chicken breast | 400–600 | g |
| Canned tomatoes | 400 | g |
| Rice | 1 | kg |

**Action:** Create `database/seeders/data/food_pack_norms.json` and review against how you actually shop in DK supermarkets (Netto, Rema, Bilka, etc.).

### 6.4 Scraping supermarkets (defer)

Challenges: many sites, JavaScript, anti-bot, ToS, changing layouts, ambiguous “average” pack.

If pursued later: one retailer, product search for staple keywords, parse weight/volume from title or JSON-LD, store median per staple with `source = scraped` and `confidence = medium`.

---

## 7. Unit normalization

Recipes will mix **g, kg, ml, dl, tsk, spsk, stk, pakke**.

| Rule | Example |
|------|---------|
| Liquids → **ml** in DB when staple `default_unit` is `ml` | 2 dl → 200 ml |
| Solids → **g** when default is `g` | 0.5 kg → 500 g |
| Countables → **stk** | 2 eggs → 2 stk |
| Vague amounts | Use `note`; optional default amount for shopping estimates |

Document conversion helpers in application code (not in DB per row).

---

## 8. Review checklist (per recipe)

- [ ] All ingredients map to existing `staple_id`
- [ ] Amounts match `servings_default`
- [ ] Units consistent or converted
- [ ] `instructions` readable without source jargon
- [ ] `license`, `source_url`, `attribution_text` filled
- [ ] No photos stored
- [ ] `is_reviewed` set only when complete

---

## 9. File & import conventions

| Asset | Location (suggested) |
|-------|---------------------|
| Staple seed | `api-wmp/database/seeders/data/staples.json` |
| Recipe seed | `api-wmp/database/seeders/data/recipes/*.json` |
| Pack norms | `api-wmp/database/seeders/data/food_pack_norms.json` |
| Import script | Artisan command or seeder class |

**Recipe JSON shape (example):**

```json
{
  "slug": "boller-i-karry",
  "name_da": "Boller i karry",
  "servings_default": 4,
  "license": "CC-BY-SA-4.0",
  "source_url": "https://en.wikibooks.org/wiki/...",
  "attribution_text": "Adapted from Wikibooks contributors",
  "instructions": "1. ...\n2. ...",
  "ingredients": [
    { "staple_slug": "minced-pork", "amount": 500, "unit": "g" },
    { "staple_slug": "onion", "amount": 1, "unit": "stk" }
  ]
}
```

---

## 10. Success metrics (MVP)

| Milestone | Target |
|-----------|--------|
| Staples | ≥ 50 defined |
| Reviewed recipes | ≥ 20 Danish weeknight dishes |
| Meal plan | 1 week → shopping list by staple (grams/ml) |
| Pack norms | ≥ 30 staples with `high` confidence manual norms |
| Attribution | 100% of published recipes have license + source |

---

## 11. Summary

| Track | Start with | Defer |
|-------|------------|--------|
| **Recipes** | Manual + open licenses (Wikibooks, PD books via KB) | Commercial scraping |
| **Ingredients** | Staples + `recipe_ingredients` | AI-only generation without review |
| **Supermarket** | Manual `food_pack_norms` | Multi-store scraping |

The two ends of the spectrum (**structured recipes** ↔ **pack sizes**) meet in the **shopping list**: sum ingredients by staple, then divide by pack norms. Building staples and reviewed recipes first delivers a working meal planner before any supermarket automation.

---

*Last updated: 2026-05-15*
