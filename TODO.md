# TODO

## Database & Schema Cleanup

### Weather Conditions - Switch from JSON to Relational
**Status:** Pending
**Priority:** Medium

Currently, `track_variants.weather_conditions` stores weather as a JSON array (e.g., `["clear", "overcast"]`). However, there's already a proper relational structure in place:
- `weather_conditions` table with weather types
- `track_weather_condition` pivot table (for track locations)

**What needs to be done:**
1. Create `track_variant_weather_condition` pivot table
2. Migrate data from `track_variants.weather_conditions` JSON column to pivot table
3. Update `TrackVariant` model to use `belongsToMany` relationship
4. Update queries in `TrackBrowser.php` to use proper joins instead of JSON queries
5. Remove `weather_conditions` column from `track_variants` table
6. Remove `'weather_conditions' => 'array'` cast from `TrackVariant` model

**Benefits:**
- Better performance with proper indexes
- Normalized data structure
- Cleaner queries (no JSON parsing)
- Consistent with tags relationship pattern

**Current workaround:**
Using SQLite JSON functions to query the array in database queries.
