#include <WiFi.h>
#include <HTTPClient.h>
#include <Adafruit_Fingerprint.h>
#include <LiquidCrystal_I2C.h>
#include <Wire.h>
#include <ArduinoJson.h>

#define RX_PIN 16
#define TX_PIN 17

const char* ssid = "Ody";
const char* password = "11332244";

String API_BASE = "http://10.228.191.203:8000/api";

LiquidCrystal_I2C lcd(0x27, 16, 2);
HardwareSerial fingerSerial(2);
Adafruit_Fingerprint finger = Adafruit_Fingerprint(&fingerSerial);

enum Mode { MODE_NORMAL, MODE_ENROLL };
Mode currentMode = MODE_NORMAL;

String nisnEnroll = "";
int enrollID = -1;

unsigned long lastEnrollCheck = 0;
const unsigned long enrollCheckInterval = 5000;

/* ===== TAMBAHAN (ANTI AUTO ABSEN SETELAH ENROLL) ===== */
unsigned long lastEnrollTime = 0;
const unsigned long enrollCooldown = 10000; // 10 detik
/* ==================================================== */

void setup() {
  Serial.begin(115200);

  Wire.begin(21, 22);
  Wire.setClock(50000);

  lcd.init();
  lcd.backlight();
  lcd.clear();

  lcd.setCursor(0, 0);
  lcd.print("ABSENSI SISWA");
  lcd.setCursor(0, 1);
  lcd.print("Booting...");
  delay(1500);

  fingerSerial.begin(57600, SERIAL_8N1, RX_PIN, TX_PIN);
  finger.begin(57600);

  if (!finger.verifyPassword()) {
    lcd.clear();
    lcd.print("Finger ERROR");
    while (1);
  }

  lcd.clear();
  lcd.print("Connect WiFi");
  WiFi.begin(ssid, password);
  while (WiFi.status() != WL_CONNECTED) {
    delay(500);
    lcd.print(".");
  }

  lcd.clear();
  lcd.print("WiFi OK");
  lcd.setCursor(0, 1);
  lcd.print(WiFi.localIP());
  delay(2000);

  tampilStandby();
}

void loop() {
  if (millis() - lastEnrollCheck > enrollCheckInterval) {
    cekEnroll();
    lastEnrollCheck = millis();
  }

  if (currentMode == MODE_ENROLL) {
    prosesEnroll();
  } else {
    prosesAbsensi();
  }

  delay(50);
}

/* ================= ABSENSI ================= */

void prosesAbsensi() {

  // ⛔ BLOK ABSENSI SETELAH ENROLL
  if (millis() - lastEnrollTime < enrollCooldown) {
    return;
  }

  uint8_t p = finger.getImage();
  if (p != FINGERPRINT_OK) return;

  p = finger.image2Tz();
  if (p != FINGERPRINT_OK) return;

  p = finger.fingerSearch();
  if (p != FINGERPRINT_OK) {
    lcd.clear();
    lcd.print("Jari");
    lcd.setCursor(0, 1);
    lcd.print("Tidak dikenal");
    delay(2000);
    tampilStandby();
    return;
  }

  kirimAbsensi(finger.fingerID);
  delay(3000);
  tampilStandby();
}

void kirimAbsensi(int id) {
  if (WiFi.status() != WL_CONNECTED) return;

  HTTPClient http;
  http.begin(API_BASE + "/fingerprint/scan");
  http.addHeader("Content-Type", "application/json");

  String body = "{\"id_fingerprint\":" + String(id) + "}";
  int code = http.POST(body);

  if (code == 200 || code == 201) {
    String res = http.getString();
    Serial.println(res);

    StaticJsonDocument<512> doc;
    if (deserializeJson(doc, res) == DeserializationError::Ok) {
      const char* nama = doc["siswa"]["nama"];

      lcd.clear();
      lcd.print("ABSENSI OK");
      lcd.setCursor(0, 1);
      lcd.print(String(nama).substring(0, 16));
    }
  }

  http.end();
}

/* ================= ENROLL ================= */

void cekEnroll() {
  if (WiFi.status() != WL_CONNECTED) return;
  if (currentMode == MODE_ENROLL) return;

  HTTPClient http;
  http.begin(API_BASE + "/fingerprint/get-nisn-enroll");
  int code = http.GET();

  if (code == 200) {
    String res = http.getString();
    Serial.println("Enroll: " + res);

    StaticJsonDocument<256> doc;
    if (deserializeJson(doc, res) == DeserializationError::Ok) {
      const char* nisn = doc["nisn"];

      if (nisn != nullptr && strlen(nisn) > 0) {
        nisnEnroll = String(nisn);
        enrollID = getFreeID();
        if (enrollID == -1) return;

        currentMode = MODE_ENROLL;

        lcd.clear();
        lcd.print("MODE ENROLL");
        lcd.setCursor(0, 1);
        lcd.print("NISN:");
        lcd.print(nisnEnroll);
        delay(1500);
      }
    }
  }
  http.end();
}

void prosesEnroll() {
  lcd.clear();
  lcd.print("Tempel Jari");
  while (finger.getImage() != FINGERPRINT_OK);
  finger.image2Tz(1);

  lcd.clear();
  lcd.print("Lepas Jari");
  delay(2000);
  while (finger.getImage() == FINGERPRINT_OK);

  lcd.clear();
  lcd.print("Tempel Lagi");
  while (finger.getImage() != FINGERPRINT_OK);
  finger.image2Tz(2);

  if (finger.createModel() != FINGERPRINT_OK ||
      finger.storeModel(enrollID) != FINGERPRINT_OK) {
    lcd.clear();
    lcd.print("Enroll Gagal");
    delay(2000);
    resetEnroll();
    return;
  }

  kirimEnroll(nisnEnroll, enrollID);

  /* ⬇️ PENTING: tandai waktu enroll */
  lastEnrollTime = millis();

  delay(3000);
  resetEnroll();
}

void kirimEnroll(String nisn, int id) {
  HTTPClient http;
  http.begin(API_BASE + "/fingerprint/confirm-enroll");
  http.addHeader("Content-Type", "application/json");

  String body = "{\"nisn\":\"" + nisn + "\",\"id_fingerprint\":" + String(id) + "}";
  http.POST(body);
  http.end();
}

/* ================= UTIL ================= */

int getFreeID() {
  for (int i = 1; i <= 127; i++) {
    if (finger.loadModel(i) != FINGERPRINT_OK) return i;
  }
  return -1;
}

void resetEnroll() {
  nisnEnroll = "";
  enrollID = -1;
  currentMode = MODE_NORMAL;
  tampilStandby();
}

void tampilStandby() {
  lcd.clear();
  lcd.print("SIAP ABSENSI");
  lcd.setCursor(0, 1);
  lcd.print("Tempel Jari");
}
