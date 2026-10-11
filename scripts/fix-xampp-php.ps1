param(
    [string] $XamppPath = 'C:\xampp'
)

$ErrorActionPreference = 'Stop'
$XamppPath = [System.IO.Path]::GetFullPath($XamppPath)
$phpIni = Join-Path $XamppPath 'php\php.ini'
$phpSsh2 = Join-Path $XamppPath 'php\libssh2.dll'
$apacheSsh2 = Join-Path $XamppPath 'apache\bin\libssh2.dll'
$phpCurl = Join-Path $XamppPath 'php\ext\php_curl.dll'
$phpMysqli = Join-Path $XamppPath 'php\ext\php_mysqli.dll'
$phpPdoMysql = Join-Path $XamppPath 'php\ext\php_pdo_mysql.dll'

foreach ($file in @($phpIni, $phpSsh2, $apacheSsh2, $phpCurl, $phpMysqli, $phpPdoMysql)) {
    if (-not (Test-Path -LiteralPath $file -PathType Leaf)) {
        throw "Required XAMPP file not found: $file. Check the XAMPP path and PHP installation."
    }
}

$identity = [Security.Principal.WindowsIdentity]::GetCurrent()
$principal = New-Object Security.Principal.WindowsPrincipal($identity)
if (-not $principal.IsInRole([Security.Principal.WindowsBuiltInRole]::Administrator)) {
    throw 'Run PowerShell as Administrator, then run this script again.'
}

$apacheExecutable = Join-Path $XamppPath 'apache\bin\httpd.exe'
$runningApache = Get-CimInstance Win32_Process -Filter "Name='httpd.exe'" |
    Where-Object { $_.ExecutablePath -and $_.ExecutablePath.Equals($apacheExecutable, [StringComparison]::OrdinalIgnoreCase) }
if ($runningApache) {
    throw 'Stop Apache in the XAMPP Control Panel before running this script.'
}

$stamp = Get-Date -Format 'yyyyMMdd-HHmmss'
$iniBackup = "$phpIni.bak-$stamp"
$dllBackup = "$apacheSsh2.bak-$stamp"
Copy-Item -LiteralPath $phpIni -Destination $iniBackup

$bytes = [System.IO.File]::ReadAllBytes($phpIni)
$hasBom = $false
if ($bytes.Length -ge 3 -and $bytes[0] -eq 0xEF -and $bytes[1] -eq 0xBB -and $bytes[2] -eq 0xBF) {
    $encoding = New-Object System.Text.UTF8Encoding($false)
    $hasBom = $true
    $text = $encoding.GetString($bytes, 3, $bytes.Length - 3)
} elseif ($bytes.Length -ge 2 -and $bytes[0] -eq 0xFF -and $bytes[1] -eq 0xFE) {
    $encoding = [System.Text.Encoding]::Unicode
    $hasBom = $true
    $text = $encoding.GetString($bytes, 2, $bytes.Length - 2)
} elseif ($bytes.Length -ge 2 -and $bytes[0] -eq 0xFE -and $bytes[1] -eq 0xFF) {
    $encoding = [System.Text.Encoding]::BigEndianUnicode
    $hasBom = $true
    $text = $encoding.GetString($bytes, 2, $bytes.Length - 2)
} else {
    $encoding = [System.Text.Encoding]::Default
    $text = $encoding.GetString($bytes)
}

$newline = if ($text.Contains("`r`n")) { "`r`n" } else { "`n" }
$lines = [System.Collections.Generic.List[string]]::new()
foreach ($line in ($text -split "\r?\n")) {
    $lines.Add($line)
}

$extensionDir = (Join-Path $XamppPath 'php\ext').Replace('\', '/')
$extensionDirLines = @()
for ($index = 0; $index -lt $lines.Count; $index++) {
    if ($lines[$index] -match '^\s*extension_dir\s*=') {
        $extensionDirLines += $index
    }
}
if ($extensionDirLines.Count -eq 0) {
    $lines.Insert(0, "extension_dir = `"$extensionDir`"")
} else {
    foreach ($index in $extensionDirLines) {
        $lines[$index] = "extension_dir = `"$extensionDir`""
    }
}

foreach ($extension in @('mysqli', 'pdo_mysql')) {
    $directivePattern = '^\s*extension\s*=\s*["'']?(?:php_)?' + [regex]::Escape($extension) + '(?:\.dll)?["'']?\s*(?:;.*)?$'
    $commentedPattern = '^\s*;\s*extension\s*=\s*["'']?(?:php_)?' + [regex]::Escape($extension) + '(?:\.dll)?["'']?\s*(?:;.*)?$'
    $activeFound = $false
    for ($index = 0; $index -lt $lines.Count; $index++) {
        if ($lines[$index] -match $directivePattern) {
            $activeFound = $true
            break
        }
    }

    if (-not $activeFound) {
        for ($index = $lines.Count - 1; $index -ge 0; $index--) {
            if ($lines[$index] -match $commentedPattern) {
                $lines[$index] = $lines[$index] -replace '^(\s*);\s*', '$1'
                $activeFound = $true
                break
            }
        }
    }

    if (-not $activeFound) {
        $lines.Add("extension=$extension")
    }
}

$updatedText = [string]::Join($newline, $lines)
$updatedBytes = $encoding.GetBytes($updatedText)
if ($hasBom) {
    if ($encoding -eq [System.Text.Encoding]::Unicode) {
        $updatedBytes = [byte[]](@(0xFF, 0xFE) + $updatedBytes)
    } elseif ($encoding -eq [System.Text.Encoding]::BigEndianUnicode) {
        $updatedBytes = [byte[]](@(0xFE, 0xFF) + $updatedBytes)
    } else {
        $updatedBytes = [byte[]](@(0xEF, 0xBB, 0xBF) + $updatedBytes)
    }
}
[System.IO.File]::WriteAllBytes($phpIni, $updatedBytes)

$sourceHash = (Get-FileHash -LiteralPath $phpSsh2 -Algorithm SHA256).Hash
$targetHash = (Get-FileHash -LiteralPath $apacheSsh2 -Algorithm SHA256).Hash
if ($sourceHash -ne $targetHash) {
    Copy-Item -LiteralPath $apacheSsh2 -Destination $dllBackup
    Copy-Item -LiteralPath $phpSsh2 -Destination $apacheSsh2 -Force
    $updatedHash = (Get-FileHash -LiteralPath $apacheSsh2 -Algorithm SHA256).Hash
    if ($updatedHash -ne $sourceHash) {
        Copy-Item -LiteralPath $dllBackup -Destination $apacheSsh2 -Force
        Copy-Item -LiteralPath $iniBackup -Destination $phpIni -Force
        throw 'DLL verification failed. The original PHP configuration and Apache DLL were restored.'
    }
}

Write-Output "Updated PHP configuration: $phpIni"
Write-Output "PHP configuration backup: $iniBackup"
if ($sourceHash -ne $targetHash) {
    Write-Output "Apache DLL backup: $dllBackup"
}
Write-Output 'Start Apache again in the XAMPP Control Panel.'
