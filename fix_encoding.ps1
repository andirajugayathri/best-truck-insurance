$file = 'mobile-catering-van-insurance.html'
$content = [System.IO.File]::ReadAllText($file, [System.Text.Encoding]::UTF8)

# Fix Windows-1252 mojibake characters
$content = $content -replace [char]0x0099, "'"      # right single quote
$content = $content -replace [char]0x0098, "'"      # left single quote
$content = $content -replace [char]0x0096, '-'       # en dash
$content = $content -replace [char]0x0097, '-'       # em dash
$content = $content -replace [char]0x0095, '&#8226;' # bullet
$content = $content -replace [char]0x0093, '"'       # left double quote
$content = $content -replace [char]0x0094, '"'       # right double quote

# Also fix Unicode smart quotes/dashes if present
$content = $content -replace [char]0x2019, "'"
$content = $content -replace [char]0x2018, "'"
$content = $content -replace [char]0x2013, '-'
$content = $content -replace [char]0x2014, '-'
$content = $content -replace [char]0x2022, '&#8226;'

[System.IO.File]::WriteAllText($file, $content, [System.Text.Encoding]::UTF8)
Write-Host 'Encoding fixed successfully.'
