#!/bin/bash

# Test API Endpoints untuk ESP32 Fingerprint
# Base URL
BASE_URL="http://127.0.0.1:8000/api/fingerprint"

echo "======================================"
echo "Testing API Absensi Siswa"
echo "======================================"
echo ""

# Test 1: Check Enroll
echo "1. Testing: Check Enroll Status"
echo "   GET $BASE_URL/check-enroll"
echo "   Response:"
curl -X GET "$BASE_URL/check-enroll" \
  -H "Content-Type: application/json" | jq '.'
echo ""
echo "--------------------------------------"
echo ""

# Test 2: Get Fingerprint List
echo "2. Testing: Get Fingerprint List"
echo "   GET $BASE_URL/list"
echo "   Response:"
curl -X GET "$BASE_URL/list" \
  -H "Content-Type: application/json" | jq '.'
echo ""
echo "--------------------------------------"
echo ""

# Test 3: Confirm Enroll (akan error jika tidak ada siswa baru)
echo "3. Testing: Confirm Enroll"
echo "   POST $BASE_URL/confirm-enroll"
echo "   Body: {\"siswa_id\": 1, \"id_fingerprint\": 5}"
echo "   Response:"
curl -X POST "$BASE_URL/confirm-enroll" \
  -H "Content-Type: application/json" \
  -d '{
    "siswa_id": 1,
    "id_fingerprint": 5
  }' | jq '.'
echo ""
echo "--------------------------------------"
echo ""

# Test 4: Scan Absensi (akan error jika fingerprint tidak terdaftar)
echo "4. Testing: Scan Absensi"
echo "   POST $BASE_URL/scan"
echo "   Body: {\"id_fingerprint\": 5}"
echo "   Response:"
curl -X POST "$BASE_URL/scan" \
  -H "Content-Type: application/json" \
  -d '{
    "id_fingerprint": 5
  }' | jq '.'
echo ""
echo "--------------------------------------"
echo ""

echo "======================================"
echo "Test Selesai!"
echo "======================================"
echo ""
echo "Catatan:"
echo "- Test 3 & 4 mungkin error karena belum ada data"
echo "- Silakan enroll siswa dulu via web admin"
echo "- Ganti siswa_id dan id_fingerprint sesuai data"
echo ""
