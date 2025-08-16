param(
    [int]$Times = 100,
    [string]$Target = "https://camo.githubusercontent.com/62cc6d4c18c13fc882328f0d709656f424a3f5a72aed768e7b60b2142646eaef/68747470733a2f2f6b6f6d617265762e636f6d2f67687076632f3f757365726e616d653d727a6b796e74266c6162656c3d50726f66696c65253230766965777326636f6c6f723d306537356236267374796c653d666c6174",
    [int]$DelaySeconds = 1,
    [int]$MaxInstances = 30,  # Jumlah maksimal PowerShell window yang bisa buka bersamaan
    [int]$CurlsPerInstance = 100000,  # Berapa kali setiap instance melakukan curl
    [int]$CurlDelay = 0  # Jeda antar curl dalam setiap instance (detik)
)

# Script dengan multiple instances PowerShell berjalan bersamaan
$runningProcesses = @()
$completed = 0

for ($i = 1; $i -le $Times; $i++) {
    # Tunggu jika sudah mencapai batas maksimal instances
    while ($runningProcesses.Count -ge $MaxInstances) {
        Write-Host "Waiting for available slot... (Running: $($runningProcesses.Count)/$MaxInstances)" -ForegroundColor Yellow

        # Cek process yang sudah selesai
        $finishedProcesses = $runningProcesses | Where-Object { $_.HasExited }

        foreach ($process in $finishedProcesses) {
            $completed++
            Write-Host "[$completed/$Times] ✅ Instance completed (Exit Code: $($process.ExitCode))" -ForegroundColor Green
            $process.Dispose()
        }

        # Hapus process yang sudah selesai dari list
        $runningProcesses = $runningProcesses | Where-Object { -not $_.HasExited }

        if ($runningProcesses.Count -ge $MaxInstances) {
            Start-Sleep -Milliseconds 500
        }
    }

    # Buka PowerShell window baru dengan loop curl
    Write-Host "[$i/$Times] Opening new PowerShell window (Instance: $($runningProcesses.Count + 1)/$MaxInstances)" -ForegroundColor Cyan

    # Command untuk setiap instance: loop curl sebanyak CurlsPerInstance kali
    $command = @"
Write-Host 'Instance $i - Starting continuous curl loop ($CurlsPerInstance times)...' -ForegroundColor Green
for (`$j = 1; `$j -le $CurlsPerInstance; `$j++) {
    Write-Host "Instance $i - Curl `$j/$CurlsPerInstance" -ForegroundColor Cyan
    curl '$Target' | Out-Null
    Write-Host "Instance $i - Curl `$j/$CurlsPerInstance completed" -ForegroundColor Yellow
    if (`$j -lt $CurlsPerInstance) { Start-Sleep $CurlDelay }
}
Write-Host 'Instance $i - All curls completed! Closing in 3 seconds...' -ForegroundColor Green
Start-Sleep 3
exit
"@

    $process = Start-Process -FilePath "powershell.exe" -ArgumentList "-Command", $command -PassThru

    $runningProcesses += $process

    # Jeda kecil sebelum membuka instance berikutnya
    Start-Sleep -Seconds $DelaySeconds
}

# Tunggu semua process selesai
Write-Host "Waiting for all remaining instances to complete..." -ForegroundColor Yellow
while ($runningProcesses.Count -gt 0) {
    $finishedProcesses = $runningProcesses | Where-Object { $_.HasExited }

    foreach ($process in $finishedProcesses) {
        $completed++
        Write-Host "[$completed/$Times] ✅ Instance completed (Exit Code: $($process.ExitCode))" -ForegroundColor Green
        $process.Dispose()
    }

    $runningProcesses = $runningProcesses | Where-Object { -not $_.HasExited }

    if ($runningProcesses.Count -gt 0) {
        Start-Sleep -Milliseconds 500
    }
}

Write-Host "🎉 All $Times curl requests completed!" -ForegroundColor Green
