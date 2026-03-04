$file = 'mobile-catering-van-insurance.html'

# Read as Latin-1 (Windows-1252) to preserve exact byte values
$content = [System.IO.File]::ReadAllText($file, [System.Text.Encoding]::GetEncoding(1252))

# Replace Windows-1252 special chars with plain ASCII/HTML equivalents
$content = $content.Replace([char]0x2019, "'")   # right single quote '
$content = $content.Replace([char]0x2018, "'")   # left single quote '
$content = $content.Replace([char]0x2013, '-')   # en dash
$content = $content.Replace([char]0x2014, '-')   # em dash
$content = $content.Replace([char]0x2022, '*')   # bullet
$content = $content.Replace([char]0x0092, "'")   # Windows-1252 right apostrophe
$content = $content.Replace([char]0x0091, "'")   # Windows-1252 left apostrophe
$content = $content.Replace([char]0x0096, '-')   # Windows-1252 en dash
$content = $content.Replace([char]0x0097, '-')   # Windows-1252 em dash
$content = $content.Replace([char]0x0095, '•')   # Windows-1252 bullet
$content = $content.Replace([char]0x0093, '"')   # Windows-1252 left quote
$content = $content.Replace([char]0x0094, '"')   # Windows-1252 right quote

# Write back as UTF-8
[System.IO.File]::WriteAllText($file, $content, [System.Text.Encoding]::UTF8)
Write-Host 'Done. All encoding issues fixed.'
