# Track Preview Images

This directory contains preview images for Wreckfest tracks displayed in the Track Browser.

## Image Source

Track images are extracted directly from the Wreckfest game installation files. The game includes official track thumbnails in `.bmap` format that can be converted to PNG.

## Image Naming Convention

Track images are named using the **variant ID** (all lowercase with underscores):
- All letters are lowercase
- Forward slashes (`/`) are replaced with underscores (`_`)
- Spaces are replaced with underscores (`_`)
- Extension is `.png`

### Examples:

| Variant ID | Image Filename |
|-----------|----------------|
| `speedway2_figure_8` | `speedway2_figure_8.png` |
| `sandpit1_long_loop` | `sandpit1_long_loop.png` |
| `bigstadium_demolition_arena` | `bigstadium_demolition_arena.png` |

## Extracting Images from Game Installation

All 154 track thumbnails have been extracted from the Wreckfest game installation.

### Source Location
```
H:\SteamLibrary\steamapps\common\Wreckfest\data\menu\textures\
```

### Files
The game includes `.bmap` files named:
```
event_{trackname}_small_250x116_raw.bmap
```

### Conversion Process
1. Locate the `.bmap` files in your Wreckfest installation
2. Use the Breckfest converter tool: `C:\temp\breckfest.exe {filename.bmap}`
3. This converts `.bmap` files to `.PNG` format
4. Rename by removing the `event_` prefix and `_small_250x116_raw.raw.PNG` suffix
5. Copy to `public/images/tracks/` directory

### Automated Script
The `convert_tracks.ps1` PowerShell script in the project root automates this process:

```powershell
$source = "H:\SteamLibrary\steamapps\common\Wreckfest\data\menu\textures"
$dest = "F:\Projects\Web\WreckfestWeb\public\images\tracks"

Get-ChildItem "$source\event_*_small_250x116_raw.raw.PNG" | ForEach-Object {
    $trackname = $_.Name -replace 'event_', '' -replace '_small_250x116_raw\.raw\.PNG$', ''
    $newname = "$trackname.png"
    Copy-Item $_.FullName -Destination "$dest\$newname" -Force
    Write-Host "Copied: $trackname"
}
```

## Image Specifications

- **Format:** PNG
- **Resolution:** 250x116 pixels (official game thumbnails)
- **Display:** 16:9 aspect ratio in Track Browser
- **Total images:** 154 (includes singleplayer/campaign tracks)
- **Multiplayer tracks:** 115 (used by dedicated servers)

## Fallback Behavior

If an image is not found for a track, the Track Browser will display a placeholder with a "No preview available" message using JavaScript error handling.
