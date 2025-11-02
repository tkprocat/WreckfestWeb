# Game Mode Migration Plan

## Overview
Migrating from boolean `is_derby` field to enum `game_mode` field in the `track_variants` table.

**Date:** 2025-11-02
**Status:** ✅ Complete

## Migration Strategy

### Database Changes
- **Add:** `game_mode` enum column with values: 'Racing', 'Derby'
- **Migrate:** Convert `is_derby = true` → `game_mode = 'Derby'`
- **Migrate:** Convert `is_derby = false` → `game_mode = 'Racing'`
- **Drop:** `is_derby` boolean column

### Benefits
1. **Explicit positive values** - No negation needed (`game_mode = 'Racing'` vs `is_derby = false`)
2. **Self-documenting** - Code and queries are more readable
3. **Extensible** - Future game modes can be added without schema changes
4. **Better API responses** - `{"game_mode": "Racing"}` is clearer than `{"is_derby": false}`

## Task Checklist

### ✅ Completed Tasks

1. **Create migration to add game_mode column** - `2025_11_02_121450_add_game_mode_to_track_variants_table.php`
   - Added `game_mode` enum column
   - Migrated existing data from `is_derby`

2. **Migrate existing is_derby data to game_mode values**
   - Data migration handled in same migration file

3. **Drop is_derby column** - `2025_11_02_121524_drop_is_derby_from_track_variants_table.php`
   - Removed `is_derby` column from table

4. **Update TrackVariant model**
   - Replaced `is_derby` in `$fillable` with `game_mode`
   - Removed `is_derby` from `$casts`

5. **Update TrackSeeder**
   - Changed to use `game_mode` field
   - Converts `derby` config flag to `'Derby'` or `'Racing'`

6. **Search and update all code references to is_derby**
   - Updated `app/Mcp/Tools/ListTracks.php` (line 36)
   - Updated `app/Filament/Pages/TrackBrowser.php` (lines 108, 117)
   - Updated `app/Helpers/TrackHelper.php` (lines 35, 75, 109)

7. **Update MCP ListTracks tool**
   - Changed to use `strtolower($variant->game_mode)` for type field

8. **Remove Derby tag from track-tags-proposal.md**
   - ✅ Removed Derby from Track Layout Tags
   - ✅ Removed Derby tag from all 17 track assignments
   - ✅ Updated tag count from 20 to 19

9. **Test migration and verify data integrity**
   - ✅ Migrations ran successfully
   - ✅ Verified data: 17 Derby tracks, 98 Racing tracks (Total: 115)
   - ✅ Sample data confirms correct migration

## Files Modified

### Migrations
- `database/migrations/2025_11_02_121450_add_game_mode_to_track_variants_table.php`
- `database/migrations/2025_11_02_121524_drop_is_derby_from_track_variants_table.php`

### Models
- `app/Models/TrackVariant.php`

### Seeders
- `database/seeders/TrackSeeder.php`

### Tools/Helpers
- `app/Mcp/Tools/ListTracks.php`
- `app/Helpers/TrackHelper.php`

### Pages
- `app/Filament/Pages/TrackBrowser.php`

### Documentation (pending)
- `docs/track-tags-proposal.md` (need to remove Derby tag)

## Migration Commands

```bash
# Run migrations
php artisan migrate

# Rollback if needed
php artisan migrate:rollback --step=2

# Fresh migration (development only)
php artisan migrate:fresh --seed
```

## Testing Checklist

- [x] Run migrations successfully
- [x] Verify data migration (Derby tracks = 'Derby', Racing tracks = 'Racing')
- [ ] Test TrackBrowser page (pending user testing)
- [ ] Test track filters (pending user testing)
- [x] Test MCP ListTracks tool (5/5 tests passing)
- [ ] Test track rotation API (pending user testing)
- [x] Test TrackHelper methods (10/10 tests passing)
- [x] Verify no references to `is_derby` remain in codebase
- [x] Update tests to use game_mode (TrackHelperTest updated with database seeding)

## Migration Results

**Verification completed:** 2025-11-02

```
Derby tracks: 17
Racing tracks: 98
Total tracks: 115

Sample Derby tracks:
  - bigstadium_demolition_arena: Madman Stadium - Demolition Arena (Derby)
  - smallstadium_demolition_arena: Fairfield County - Demolition Arena (Derby)
  - mudpit_demolition_arena: Fairfield Mud Pit - Demolition Arena (Derby)
  - grass_arena_demolition_arena: Fairfield Grass Field - Demolition Arena (Derby)
  - field_derby_arena: Glendale Countryside - Field Arena (Derby)

Sample Racing tracks:
  - bigstadium_figure_8: Madman Stadium - Figure 8 (Racing)
  - dirt_speedway_dirt_oval: Bloomfield Speedway - Dirt Oval (Racing)
  - dirt_speedway_figure_8: Bloomfield Speedway - Figure 8 (Racing)
  - bonebreaker_valley_main_circuit: Bonebreaker Valley - Main Circuit (Racing)
  - crash_canyon_main_circuit: Crash Canyon - Main Circuit (Racing)
```

✓ All database migrations completed successfully
✓ Data integrity verified
✓ Code references updated
✓ All migration-related tests passing (15/15)

## Test Results

**TrackHelperTest** (10/10 passing):
- Derby gamemode identification ✓
- Racing gamemode identification ✓
- Derby-only track detection ✓
- Track-gamemode compatibility ✓
- Weather support functionality ✓

**ListTracksTest** (5/5 passing):
- Track listing with metadata ✓
- Location grouping ✓
- Derby vs Racing track distinction ✓

**Test Updates:**
- Added `RefreshDatabase` trait to TrackHelperTest
- Added database seeding in `beforeEach()` hook

## Rollback Plan

If issues occur:
1. Run `php artisan migrate:rollback --step=2`
2. This will restore `is_derby` column and remove `game_mode` column
3. All code changes have been version controlled via git

## Notes

- The `derby` flag in `config/wreckfest.php` is still used to determine game mode during seeding
- Existing API responses maintain backward compatibility by including `derby: boolean` field
- Game mode values are case-sensitive: 'Racing' and 'Derby' (capitalized)
