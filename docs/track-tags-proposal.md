# Wreckfest Track Tagging Proposal

## Tag Definitions

Below are the 19 essential tags proposed for categorizing track variants. Edit this section to add, remove, or rename tags.

### Track Layout Tags

| Tag Name     | Description                                        | Slug     |
|--------------|----------------------------------------------------|----------|
| **Oval**     | Oval-shaped racing tracks                          | oval     |
| **Figure 8** | Complete figure 8 loop layouts with crossing point | figure-8 |
| **Circuit**  | Point-to-point racing circuits                     | circuit  |
| **Speedway** | Traditional speedway layouts                       | speedway |
| **Wild**     | Layouts that have jumps added to it                | wild     |

### Surface Type Tags

| Tag Name   | Description                           | Slug   |
|------------|---------------------------------------|--------|
| **Gravel** | Unpaved gravel, dirt, and sand tracks | gravel |
| **Tarmac** | Paved tarmac and asphalt circuits     | tarmac |
| **Mud**    | Muddy terrain                         | mud    |

### Specialty Tags

| Tag Name            | Description                                         | Slug            |
|---------------------|-----------------------------------------------------|-----------------|
| **Rally**           | Rally-style tracks with technical sections          | rally           |
| **Stadium**         | Stadium venues                                      | stadium         |
| **Urban**           | City/urban settings with street circuits            | urban           |
| **Forest**          | Forest settings                                     | forest          |
| **Bump**            | Obstacles causing brief airborne moments            | bump            |
| **Jump**            | Tracks featuring jumps and elevation changes        | jump            |
| **Loop**            | Tracks featuring a loop                             | loop            |
| **Two-way Traffic** | Tracks where racers encounter oncoming traffic      | two-way-traffic |
| **Intersection**    | Tracks with crossing sections but not full figure-8 | intersection    |
| **Split Path**      | Tracks with splitting paths                         | split-path      |
| **Reversed**        | Tracks with reversed driving direction              | reversed        |
| **Short**           | Tracks with shorten tracks                          | short           |
| **Wall Ride**       | Tracks with opportunity to wall ride                | wallride        |

---

## Auto-Tagging Proposals

Below are all 115 track variants with proposed tags. Edit the tags for each track as needed.

**Format:** `Location - Variant Name | Proposed Tags | Reasoning`

### Big Valley Speedway

- **Big Valley Speedway - Demolition Arena** (`speedway2_demolition_arena`)
  - **Tags:** Tarmac, Stadium

- **Big Valley Speedway - Figure 8** (`speedway2_figure_8`)
  - **Tags:** Figure 8, Tarmac, Stadium, Intersection

- **Big Valley Speedway - Inner Oval** (`speedway2_inner_oval`)
  - **Tags:** Oval, Tarmac, Stadium

- **Big Valley Speedway - Open Demolition Arena** (`speedway2_classic_arena`)
  - **Tags:** Tarmac, Stadium

- **Big Valley Speedway - Outer Oval** (`speedway2_outer_oval`)
  - **Tags:** Oval, Tarmac, Stadium

- **Big Valley Speedway - Outer Oval Loop** (`speedway2_oval_loop`)
  - **Tags:** Oval, Tarmac, Two-way Traffic, Stadium

### Bleak City

- **Bleak City - Demolition Arena** (`crm01_3`)
  - **Tags:** Urban, Gravel

- **Bleak City - Free Roam** (`crm01_2`)
  - **Tags:** Urban, Gravel

- **Bleak City - Race Track** (`crm01_1`)
  - **Tags:** Urban, Tarmac

- **Bleak City - Race Track Reverse** (`crm01_5`)
  - **Tags:** Urban, Tarmac, Reversed

### Bloomfield Speedway

- **Bloomfield Speedway - Dirt Oval** (`dirt_speedway_dirt_oval`)
  - **Tags:** Oval, Gravel

- **Bloomfield Speedway - Figure 8** (`dirt_speedway_figure_8`)
  - **Tags:** Figure 8, Gravel, Intersection

### Bonebreaker Valley

- **Bonebreaker Valley - Main Circuit** (`bonebreaker_valley_main_circuit`)
  - **Tags:** Gravel, Jump, Two-way Traffic, Forest

### Boulder Bank Circuit

- **Boulder Bank Circuit - Main Route** (`dirtpit3_long_loop`)
  - **Tags:** Tarmac, Gravel, Intersection, Forest

- **Boulder Bank Circuit - Main Route Reverse** (`dirtpit3_long_loop_rev`)
  - **Tags:** Tarmac, Gravel, Intersection, Reversed, Forest

- **Boulder Bank Circuit - Short Route** (`dirtpit3_short_loop`)
  - **Tags:** Tarmac, Gravel, Intersection, Forest

- **Boulder Bank Circuit - Short Route Reverse** (`dirtpit3_short_loop_rev`)
  - **Tags:** Tarmac, Gravel, Intersection, Reversed, Forest

### Clayridge Circuit

- **Clayridge Circuit - Main Circuit** (`mixed9_r1`)
  - **Tags:** Tarmac, Gravel, Split Path, Forest

- **Clayridge Circuit - Main Circuit Reverse** (`mixed9_r1_rev`)
  - **Tags:** Tarmac, Gravel, Split Path, Reversed, Forest

### Crash Canyon

- **Crash Canyon - Main Circuit** (`crash_canyon_main_circuit`)
  - **Tags:** Gravel, Two-way Traffic, Bump

### Deathloop

- **Deathloop - Main Circuit** (`loop`)
  - **Tags:** Tarmac, Loop, Split Path, Intersection, Jump, Bump

### Devil's Canyon

- **Devil's Canyon - Free Roam** (`crm02_2`)
  - **Tags:** Tarmac, Gravel
- **Devil's Canyon - Race Track** (`crm02_1`)
  - **Tags:** Tarmac, Gravel

### Dirt Devil Stadium

- **Dirt Devil Stadium - Demolition Arena** (`triangle_r2`)
  - **Tags:** Stadium, Gravel, Bump

- **Dirt Devil Stadium - Dirt Speedway** (`triangle_r1`)
  - **Tags:** Stadium, Gravel, Bump

### Drytown Desert Circuit

- **Drytown Desert Circuit - Main Circuit** (`fields08_1`)
  - **Tags:** Gravel, Intersection

- **Drytown Desert Circuit - Main Circuit Reverse** (`fields08_1_rev`)
  - **Tags:** Gravel, Intersection, Reversed

### Eagles Peak Motorpark

- **Eagles Peak Motorpark - Demolition Arena** (`fields13_2`)
  - **Tags:** Tarmac, Gravel

- **Eagles Peak Motorpark - Racing Track** (`fields13_1`)
  - **Tags:** Tarmac, Gravel, Jump, Intersection

- **Eagles Peak Motorpark - Racing Track Reverse** (`fields13_1_rev`)
  - **Tags:** Tarmac, Gravel, Jump, Intersection, Reversed

### Espedalen Raceway

- **Espedalen Raceway - Main Circuit** (`tarmac3_main_circuit`)
  - **Tags:** Tarmac

- **Espedalen Raceway - Main Circuit Reverse** (`tarmac3_main_circuit_rev`)
  - **Tags:** Tarmac

- **Espedalen Raceway - Short Circuit** (`tarmac3_short_circuit`)
  - **Tags:** Tarmac, Short

- **Espedalen Raceway - Short Circuit Reverse** (`tarmac3_short_circuit_rev`)
  - **Tags:** Tarmac, Short

### Fairfield County

- **Fairfield County - Demolition Arena** (`smallstadium_demolition_arena`)
  - **Tags:** Stadium, Tarmac

### Fairfield Grass Field

- **Fairfield Grass Field - Demolition Arena** (`grass_arena_demolition_arena`)
  - **Tags:** Gravel

### Fairfield Mud Pit

- **Fairfield Mud Pit - Demolition Arena** (`mudpit_demolition_arena`)
  - **Tags:** Mud

### Finncross Circuit

- **Finncross Circuit - Main Circuit** (`mixed1_main_circuit`)
  - **Tags:** Tarmac, Gravel, Forest

- **Finncross Circuit - Main Circuit Reverse** (`mixed1_main_circuit_rev`)
  - **Tags:** Tarmac, Gravel, Reversed, Forest

### Fire Rock Raceway

- **Fire Rock Raceway - Full Circuit** (`tarmac1_main_circuit`)
  - **Tags:** Tarmac, Gravel

- **Fire Rock Raceway - Full Circuit Reverse** (`tarmac1_main_circuit_rev`)
  - **Tags:** Tarmac, Gravel. Reversed

- **Fire Rock Raceway - Short Circuit** (`tarmac1_short_circuit`)
  - **Tags:** Tarmac, Gravel, Short

- **Fire Rock Raceway - Short Circuit Reverse** (`tarmac1_short_circuit_rev`)
  - **Tags:** Tarmac, Gravel, Short, Reversed

### Firwood Motocenter

- **Firwood Motocenter - Main Circuit** (`mixed7_r1`)
  - **Tags:** Tarmac, Gravel

- **Firwood Motocenter - Main Circuit Reverse** (`mixed7_r1_rev`)
  - **Tags:** Tarmac, Gravel, Reversed

- **Firwood Motocenter - Rally Circuit** (`mixed7_r2`)
  - **Tags:** Tarmac, Gravel

- **Firwood Motocenter - Rally Circuit Reverse** (`mixed7_r2_rev`)
  - **Tags:** Tarmac, Gravel, Reversed

- **Firwood Motocenter - Short Circuit** (`mixed7_r3`)
  - **Tags:** Tarmac, Gravel, Reversed, Short

- **Firwood Motocenter - Short Circuit Reverse** (`mixed7_r3_rev`)
  - **Tags:** Tarmac, Gravel, Reversed, Short

### Glendale Countryside

- **Glendale Countryside - Field Arena** (`field_derby_arena`)
  - **Tags:** Gravel

### Hellride

- **Hellride - Main Circuit** (`urban06`)
  - **Tags:** Stadium, Tarmac, Jump, Wall Ride

### Hillstreet Circuit

- **Hillstreet Circuit - Race Track** (`urban08_1`)
  - **Tags:** Urban, Tarmac, Intersection

- **Hillstreet Circuit - Race Track Reverse** (`urban08_1_rev`)
  - **Tags:** Urban, Tarmac, Intersection, Reversed

### Hilltop Stadium

- **Hilltop Stadium - Figure 8** (`speedway1_figure_8`)
  - **Tags:** Figure 8, Stadium, Tarmac, Intersection

- **Hilltop Stadium - Oval** (`speedway1_oval`)
  - **Tags:** Oval, Stadium, Tarmac

### Kingston Raceway

- **Kingston Raceway - Asphalt Oval** (`fields12_1`)
  - **Tags:** Oval, Tarmac

- **Kingston Raceway - Asphalt Oval Reverse** (`fields12_1_rev`)
  - **Tags:** Oval, Tarmac, Reversed

- **Kingston Raceway - Figure 8** (`fields12_2`)
  - **Tags:** Figure 8, Gravel, Intersection

### Maasten Motocenter

- **Maasten Motocenter - Main Circuit** (`mixed2_main_circuit`)
  - **Tags:** Gravel, Tarmac

- **Maasten Motocenter - Main Circuit Reverse** (`mixed2_main_circuit_rev`)
  - **Tags:** Gravel, Tarmac, Reversed

### Madman Stadium

- **Madman Stadium - Demolition Arena** (`bigstadium_demolition_arena`)
  - **Tags:** Stadium, Tarmac, Wall Ride

- **Madman Stadium - Figure 8** (`bigstadium_figure_8`)
  - **Tags:** Figure 8, Stadium, Tarmac, Intersection, Bump

### Midwest Motocenter

- **Midwest Motocenter - Main Circuit** (`gravel1_main_loop`)
  - **Tags:** Gravel
  - 
- **Midwest Motocenter - Main Circuit Reverse** (`gravel1_main_loop_rev`)
  - **Tags:** Gravel, Reversed

### Motorcity Circuit

- **Motorcity Circuit - Main Circuit** (`tarmac2_main_circuit`)
  - **Tags:** Tarmac

- **Motorcity Circuit - Main Circuit Reverse** (`tarmac2_main_circuit_rev`)
  - **Tags:** Tarmac, Reversed

- **Motorcity Circuit - Trophy Circuit** (`tarmac2_main_circuit_tourney`)
  - **Tags:**  Tarmac, Jump, Wild
 
### Mudford Motorpark

- **Mudford Motorpark - Mud Oval** (`fields10_1`)
  - **Tags:** Oval, Mud

- **Mudford Motorpark - Mud Pit** (`fields10_2`)
  - **Tags:** Mud

### Northfolk Ring

- **Northfolk Ring - Main Circuit** (`mixed8_r1`)
  - **Tags:** Tarmac, Gravel

- **Northfolk Ring - Main Circuit Reverse** (`mixed8_r3_rev`)
  - **Tags:** Tarmac, Gravel, Reversed

- **Northfolk Ring - Open Circuit** (`mixed8_r2`)
  - **Tags:** Tarmac, Gravel

### Northland Raceway

- **Northland Raceway - Free Route** (`mixed5_free_route`)
  - **Tags:** Tarmac, Gravel, Split Path

- **Northland Raceway - Inner Route** (`mixed5_inner_loop`)
  - **Tags:** Tarmac, Gravel

- **Northland Raceway - Inner Route Reverse** (`mixed5_inner_loop_rev`)
  - **Tags:** Tarmac, Gravel, Reversed

- **Northland Raceway - Outer Route** (`mixed5_outer_loop`)
  - **Tags:** Tarmac, Gravel

- **Northland Raceway - Outer Route Reverse** (`mixed5_outer_loop_rev`)
  - **Tags:** Tarmac, Gravel, Reversed

### Pinehills Raceway

- **Pinehills Raceway - Main Circuit** (`mixed3_long_loop`)
  - **Tags:** Tarmac, Gravel, Forest

- **Pinehills Raceway - Main Circuit Reverse** (`mixed3_long_loop_rev`)
  - **Tags:** Tarmac, Gravel, Reversed, Forest

- **Pinehills Raceway - Rally Circuit** (`mixed3_r3`)
  - **Tags:** Tarmac, Gravel, Forest

- **Pinehills Raceway - Rally Circuit Reverse** (`mixed3_r3_rev`)
  - **Tags:** Tarmac, Gravel, Reversed, Forest

- **Pinehills Raceway - Short Circuit** (`mixed3_short_loop`)
  - **Tags:** Tarmac, Gravel, Short, Forest

- **Pinehills Raceway - Short Circuit Reverse** (`mixed3_short_loop_rev`)
  - **Tags:** Tarmac, Gravel, Reversed, Short, Forest

### Rally Trophy

- **Rally Trophy - Special Stage** (`rt01_1`)
  - **Tags:** Gravel, Forest

### Rattlesnake Racepark

- **Rattlesnake Racepark - Main Circuit** (`fields14_1`)
  - **Tags:** Gravel, Jump, Bump

- **Rattlesnake Racepark - Main Circuit Reverse** (`fields14_2`)
  - **Tags:** Gravel, Jump, Reversed, Bump

### Rockfield Roughspot

- **Rockfield Roughspot - Dirt Oval** (`fields09_1`)
  - **Tags:** Oval, Gravel, Bump, Wall Ride

### Rosenheim Raceway

- **Rosenheim Raceway - Main Circuit** (`mixed4_main_circuit`)
  - **Tags:** Tarmac, Gravel, Split Path

- **Rosenheim Raceway - Main Circuit Reverse** (`mixed4_main_circuit_rev`)
  - **Tags:** Tarmac, Gravel, Reversed, Split Path

### Sandstone Raceway

- **Sandstone Raceway - Alt Route** (`dirtpit1_alt_loop`)
  - **Tags:** Gravel

- **Sandstone Raceway - Alt Route Reverse** (`dirtpit1_alt_loop_rev`)
  - **Tags:** Gravel, Reversed

- **Sandstone Raceway - Main Route** (`dirtpit1_long_loop`)
  - **Tags:** Gravel, Short

- **Sandstone Raceway - Main Route Reverse** (`dirtpit1_long_loop_rev`)
  - **Tags:** Gravel, Reversed, Short

- **Sandstone Raceway - Short Route** (`dirtpit1_short_loop`)
  - **Tags:** Gravel, Intersection, Two-way Traffic

- **Sandstone Raceway - Short Route Reverse** (`dirtpit1_short_loop_rev`)
  - **Tags:** Gravel, Intersection, Two-way Traffic, Reversed


### Savolax Sandpit

- **Savolax Sandpit - Main Route** (`dirtpit2_full_circuit`)
  - **Tags:** Gravel, Tarmac

- **Savolax Sandpit - Main Route Reverse** (`dirtpit2_full_circuit_rev`)
  - **Tags:** Gravel, Tarmac, Reversed

- **Savolax Sandpit - Short Route** (`dirtpit2_2`)
  - **Tags:** Gravel, Tarmac, Short

- **Savolax Sandpit - Short Route Reverse** (`dirtpit2_2_rev`)
  - **Tags:** Gravel, Tarmac, Reversed, Short

### The Maw

- **The Maw - Demolition Arena** (`fields11_1`)
  - **Tags:** Tarmac

### Thunderbowl

- **Thunderbowl - Demolition Arena** (`urban07`)
  - **Tags:** Stadium, Wall Ride

### Torsdalen Circuit

- **Torsdalen Circuit - Main Circuit** (`forest13_1`)
  - **Tags:** Gravel, Tarmac

- **Torsdalen Circuit - Main Circuit Reverse** (`forest13_1_rev`)
  - **Tags:** Gravel, Tarmac, Reversed

- **Torsdalen Circuit - Short Circuit** (`forest13_2`)
  - **Tags:** Gravel, Tarmac, Short

- **Torsdalen Circuit - Short Circuit Reverse** (`forest13_2_rev`)
  - **Tags:** Gravel, Tarmac, Short, Reversed

### Tribend Speedway

- **Tribend Speedway - Main Circuit** (`forest12_1`)
  - **Tags:** Tarmac, Stadium

- **Tribend Speedway - Reverse Circuit** (`forest12_1_rev`)
  - **Tags:** Tarmac, Reversed, Stadium

- **Tribend Speedway - Wild Circuit** (`forest12_2`)
  - **Tags:** Tarmac, Jump, Wild, Stadium

### Vale Falls Circuit

- **Vale Falls Circuit - Main Circuit** (`forest11_1`)
  - **Tags:** Gravel, Tarmac

- **Vale Falls Circuit - Main Circuit Reverse** (`forest11_1_rev`)
  - **Tags:** Gravel, Tarmac, Reversed

- **Vale Falls Circuit - Short Circuit** (`forest11_2`)
  - **Tags:** Gravel, Tarmac, Short

- **Vale Falls Circuit - Short Circuit Reverse** (`forest11_2_rev`)
  - **Tags:** Gravel, Tarmac, Short, Reversed

### Wrecking Playground

- **Wrecking Playground - Main Area** (`wrecker01_1`)
  - **Tags:** Tarmac, Gravel, Jump

### Wrecknado

- **Wrecknado - Demolition Arena** (`urban09_2`)
  - **Tags:** Stadium, Tarmac

- **Wrecknado - Main Circuit** (`urban09_1`)
  - **Tags:** Stadium, Tarmac, Jump, Intersection

---

## Instructions for Editing

### How to Modify Tags:

1. **Add/Remove Tags:** Edit the "Tags:" line for any track
2. **Create New Tags:** Add new rows to the Tag Definitions table above
3. **Rename Tags:** Change tag names in both the definitions and track assignments
4. **Remove Entire Tags:** Delete the row from Tag Definitions and remove from all tracks

### Tag Naming Guidelines:

- Keep tag names concise (1-2 words)
- Use Title Case with capital first letters (e.g., "Mixed", "Figure-8")
- Use hyphens for multi-word tags (e.g., "Figure-8")
- Slugs will be auto-generated from names (lowercase, hyphenated)

### After Editing:

Once you're satisfied with the tags and assignments, save this file and let me know. I'll create a seeder that:
1. Creates all tags from the Tag Definitions table
2. Applies the exact tag assignments you've specified
3. Generates the pivot table relationships

---

**Total Tracks:** 115
**Total Tags Proposed:** 19
**Ready for Review:** ✓
[.gitignore](../.gitignore)
