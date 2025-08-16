param(
    [int]$Times = 50,
    [string]$Target = "https://github.com/RzkyNT"
)

# versi curl-equivalent GET (bukan HEAD)
$target = $Target
$times = $Times

# pakai curl.exe biar persis seperti: curl https://github.com/RzkyNT
$curl = Get-Command curl.exe -ErrorAction SilentlyContinue

for ($i = 1; $i -le $times; $i++) {
    try {
        if ($curl) {
            # -s = silent, -L = follow redirects, -o NUL = buang output (Windows)
            $curlArgs = @("--silent", "--location", "--output", "NUL", $target)
            $p = Start-Process -FilePath $curl.Path -ArgumentList $curlArgs -NoNewWindow -PassThru -Wait
            if ($p.ExitCode -eq 0) {
                Write-Output "[$i] OK via curl.exe"
            } else {
                throw "curl.exe exit code $($p.ExitCode)"
            }
        } else {
            throw "curl.exe tidak ditemukan"
        }
    } catch {
        # fallback: Invoke-WebRequest dengan header mirip curl default
        try {
            $headers = @{ "User-Agent" = "curl/8.0.1"; "Accept" = "*/*" }
            $r = Invoke-WebRequest -Uri $target -Method Get -Headers $headers -MaximumRedirection 10 -UseBasicParsing
            Write-Output "[$i] OK via IWR (Status: $($r.StatusCode))"
        } catch {
            Write-Output "[$i] ❌ Error: $_"
        }
    }

    # jeda acak kecil supaya tidak terlalu agresif (opsional)
    Start-Sleep -Milliseconds (Get-Random -Minimum 100 -Maximum 500)
}
