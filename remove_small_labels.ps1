$file = 'mobile-catering-van-insurance.html'
$lines = [System.IO.File]::ReadAllLines($file, [System.Text.Encoding]::UTF8)

# Lines to remove (1-indexed → 0-indexed): 81,82,519,520,614,615,776,777
$removeLines = @(80, 81, 518, 519, 613, 614, 775, 776)

$kept = for ($i = 0; $i -lt $lines.Count; $i++) {
    if ($removeLines -notcontains $i) { $lines[$i] }
}

[System.IO.File]::WriteAllLines($file, $kept, [System.Text.Encoding]::UTF8)
Write-Host "Done. Removed $($removeLines.Count) lines (4 small label elements)."
