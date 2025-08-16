param(
    [int]$Times = 50,
    [string]$Target = "https://github.com/RzkyNT",
    [int]$DelayMinMs = 200,
    [int]$DelayMaxMs = 800,
    [switch]$UseCurl
)

$target = $Target
$times = $Times

for ($i = 1; $i -le $times; $i++) {
    try {
        if ($UseCurl) {
            # Pakai curl.exe dan pastikan stdout dibaca penuh (bukan dibuang ke NUL)
            $curl = Get-Command curl.exe -ErrorAction Stop
            # Jalankan curl dengan follow-redirect dan baca semua output di PowerShell
            $null = (& $curl.Path "-L" $target 2>$null | Out-String)
            Write-Output "[$i] OK via curl.exe"
        } else {
            # Pakai Invoke-WebRequest (seperti alias 'curl' di PowerShell) dan baca body penuh
            $session = New-Object Microsoft.PowerShell.Commands.WebRequestSession
            $r = Invoke-WebRequest -Uri $target -Method Get -MaximumRedirection 10 -WebSession $session
            $null = $r.Content  # akses untuk memastikan body dibaca tuntas
            Write-Output "[$i] OK via Invoke-WebRequest (Status: $($r.StatusCode))"
        }
    } catch {
        Write-Output "[$i] ❌ Error: $_"
    }

    Start-Sleep -Milliseconds (Get-Random -Minimum $DelayMinMs -Maximum $DelayMaxMs)
}
