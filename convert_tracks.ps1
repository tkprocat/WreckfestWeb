$source = "H:\SteamLibrary\steamapps\common\Wreckfest\data\menu\textures"
$dest = "F:\Projects\Web\WreckfestWeb\public\images\tracks"

Get-ChildItem "$source\event_*_small_250x116_raw.raw.PNG" | ForEach-Object {
    $trackname = $_.Name -replace 'event_', '' -replace '_small_250x116_raw\.raw\.PNG$', ''
    $newname = "$trackname.png"
    Copy-Item $_.FullName -Destination "$dest\$newname" -Force
    Write-Host "Copied: $trackname"
}

Write-Host "`nTotal tracks copied: $(Get-ChildItem "$dest\*.png" | Measure-Object | Select-Object -ExpandProperty Count)"
