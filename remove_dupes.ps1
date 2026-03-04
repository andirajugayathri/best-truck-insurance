# Remove lines 1080 to 1497 from the HTML file (old duplicate corrupted FAQ content)
$file = 'mobile-catering-van-insurance.html'
$lines = [System.IO.File]::ReadAllLines($file, [System.Text.Encoding]::UTF8)

# Keep lines 1 to 1079 (0-indexed: 0 to 1078) and 1498 onwards (0-indexed: 1497+)
$kept = $lines[0..1078] + $lines[1497..($lines.Length - 1)]

[System.IO.File]::WriteAllLines($file, $kept, [System.Text.Encoding]::UTF8)
Write-Host "Done. Removed $(1497 - 1079) duplicate lines."
