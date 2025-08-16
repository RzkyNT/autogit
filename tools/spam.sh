#!/bin/bash

# Konfigurasi (bisa kamu ubah sesuai kebutuhan)
TIMES=100                 # Berapa banyak instance (proses paralel)
TARGET="https://camo.githubusercontent.com/62cc6d4c18c13fc882328f0d709656f424a3f5a72aed768e7b60b2142646eaef/68747470733a2f2f6b6f6d617265762e636f6d2f67687076632f3f757365726e616d653d727a6b796e74266c6162656c3d50726f66696c65253230766965777326636f6c6f723d306537356236267374796c653d666c6174"
DELAY_SECONDS=1           # Jeda antar membuka instance
MAX_INSTANCES=10          # Jumlah maksimal instance paralel
CURLS_PER_INSTANCE=1000   # Berapa kali setiap instance melakukan curl
CURL_DELAY=0              # Jeda antar curl di 1 instance

running=0
completed=0

run_instance() {
    id=$1
    echo "[Instance $id] Start (Loop $CURLS_PER_INSTANCE times)..."
    for ((j=1; j<=CURLS_PER_INSTANCE; j++)); do
        curl -s "$TARGET" > /dev/null
        echo "[Instance $id] Curl $j/$CURLS_PER_INSTANCE done"
        [ $CURL_DELAY -gt 0 ] && sleep $CURL_DELAY
    done
    echo "[Instance $id] Completed!"
}

for ((i=1; i<=TIMES; i++)); do
    # Tunggu kalau sudah mencapai batas MAX_INSTANCES
    while [ $running -ge $MAX_INSTANCES ]; do
        wait -n   # tunggu satu proses selesai
        ((running--))
        ((completed++))
        echo "[+] $completed/$TIMES instance selesai"
    done

    run_instance $i &   # jalankan background job
    ((running++))
    sleep $DELAY_SECONDS
done

# Tunggu semua sisa proses
wait
echo "🎉 Semua $TIMES instance selesai!"
