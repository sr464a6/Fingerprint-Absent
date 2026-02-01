#include <WiFi.h>
#include <HTTPClient.h>
#include <Adafruit_Fingerprint.h>
#include <LiquidCrystal_I2C.h>
#include <Wire.h>
#include <ArduinoJson.h>  

#define RX_PIN 16
#define TX_PIN 17

const char* ssid = "iPhone";
const char* password = "syifara20";

String API_BASE = "http://192.168.1.100:8000/api";  // ⚠️ UBAH SESUAI LAPTOP TRS php artisan serve --host=0.0.0.0 sama APP_URL di file .env

LiquidCrystal_I2C lcd(0x27, 16, 2);
HardwareSerial fingerSerial(2);
Adafruit_Fingerprint finger = Adafruit_Fingerprint(&fingerSerial);

enum Mode { MODE_NORMAL, MODE_ENROLL };
Mode currentMode = MODE_NORMAL;

String nisnEnroll = "";
String namaEnroll = "";  
int enrollID = -1;

unsigned long lastEnrollCheck = 0;
const unsigned long enrollCheckInterval = 5000;

void setup() {
  Serial.begin(115200);

  Wire.begin(21, 22);
  Wire.setClock(50000);
  delay(200);
  lcd.init();
  delay(100);
  lcd.backlight();
  lcd.clear();

  lcd.setCursor(0, 0);
  lcd.print("ABSENSI SISWA");
  lcd.setCursor(0, 1);
  lcd.print("Booting...");
  delay(1500);

  fingerSerial.begin(57600, SERIAL_8N1, RX_PIN, TX_PIN);
  finger.begin(57600);
  if (! finger.verifyPassword()) {
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

void prosesAbsensi() {
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

  int id = finger.fingerID;
  kirimAbsensi(id);  

  delay(3000); 
  tampilStandby();
}

void cekEnroll() {
  if (WiFi.status() != WL_CONNECTED) return;
  if (currentMode == MODE_ENROLL) return;

  HTTPClient http;
  http.begin(API_BASE + "/fingerprint/get-nisn-enroll");
  http.setTimeout(5000);
  int code = http.GET();

  if (code == 200) {
    String res = http.getString();
    Serial.println("Response enroll:  " + res);

    int start = res.indexOf(": \"") + 2;
    int end   = res.indexOf("\"", start);

    if (start > 1 && end > start) {
      String nisn = res.substring(start, end);

      if (nisn.length() > 0) {
        nisnEnroll = nisn;
        enrollID = getFreeID();
        
        if (enrollID == -1) {
          lcd.clear();
          lcd.print("Memori Penuh!");
          delay(2000);
          http.end();
          return;
        }
        
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

  delay(3000); 
  resetEnroll();
}

void kirimAbsensi(int id) {
  if (WiFi.status() != WL_CONNECTED) {
    lcd.clear();
    lcd.print("WiFi Error");
    delay(2000);
    return;
  }
  
  HTTPClient http;
  http.begin(API_BASE + "/fingerprint/scan");
  http.addHeader("Content-Type", "application/json");

  String body = "{\"id_fingerprint\":" + String(id) + "}";
  int code = http.POST(body);
  
  Serial.print("Kirim absensi ID ");
  Serial.print(id);
  Serial.print(" -> HTTP ");
  Serial.println(code);

  if (code == 200 || code == 201) {
    String response = http.getString();
    Serial.println("Response:  " + response);

    StaticJsonDocument<512> doc;
    DeserializationError error = deserializeJson(doc, response);

    if (! error) {
      bool success = doc["success"];
      const char* nama = doc["siswa"]["nama"];
      const char* nisn = doc["siswa"]["nisn"];
      const char* waktu = doc["waktu"];

      if (success && nama != nullptr) {
        lcd.clear();
        lcd.print("ABSENSI OK");
        lcd.setCursor(0, 1);
        
        String namaStr = String(nama);
        if (namaStr. length() <= 16) {
          lcd.print(nama);
        } else {
          lcd.print(namaStr. substring(0, 16));
        }

        Serial.print("Nama: ");
        Serial.println(nama);
        Serial.print("NISN: ");
        Serial.println(nisn);
        Serial.print("Waktu: ");
        Serial.println(waktu);
      } else {
        lcd.clear();
        lcd.print("Absensi Gagal");
      }
    } else {
      Serial.print("JSON Error: ");
      Serial.println(error.c_str());
      lcd.clear();
      lcd.print("Parse Error");
    }
  } else {
    lcd.clear();
    lcd.print("Gagal Kirim");
    lcd.setCursor(0, 1);
    lcd.print("Code: ");
    lcd.print(code);
  }
  
  http.end();
}

void kirimEnroll(String nisn, int id) {
  if (WiFi.status() != WL_CONNECTED) {
    lcd.clear();
    lcd.print("WiFi Error");
    delay(2000);
    return;
  }
  
  HTTPClient http;
  http.begin(API_BASE + "/fingerprint/confirm-enroll");
  http.addHeader("Content-Type", "application/json");

  String body = "{";
  body += "\"nisn\":\"" + nisn + "\",";
  body += "\"id_fingerprint\":" + String(id);
  body += "}";

  int code = http.POST(body);
  Serial.print("Kirim enroll NISN ");
  Serial.print(nisn);
  Serial.print(" -> HTTP ");
  Serial.println(code);

  if (code == 200 || code == 201) {
    String response = http.getString();
    Serial.println("Response: " + response);

    StaticJsonDocument<512> doc;
    DeserializationError error = deserializeJson(doc, response);

    if (!error) {
      bool success = doc["success"];
      const char* nama = doc["siswa"]["nama"];
      const char* message = doc["message"];

      if (success && nama != nullptr) {
        lcd.clear();
        lcd.print("Enroll Sukses");
        lcd.setCursor(0, 1);
        
        String namaStr = String(nama);
        if (namaStr. length() <= 16) {
          lcd.print(nama);
        } else {
          lcd. print(namaStr.substring(0, 16));
        }

        Serial.print("Nama: ");
        Serial.println(nama);
        Serial.print("Message: ");
        Serial.println(message);
      } else {
        lcd.clear();
        lcd.print("Enroll Gagal");
      }
    } else {
      Serial.print("JSON Error: ");
      Serial.println(error.c_str());
      lcd.clear();
      lcd.print("Parse Error");
    }
  } else {
    lcd.clear();
    lcd.print("Gagal Kirim");
    lcd.setCursor(0, 1);
    lcd.print("Code: ");
    lcd.print(code);
  }
  
  http.end();
}

int getFreeID() {
  for (int id = 1; id <= 127; id++) {
    if (finger.loadModel(id) != FINGERPRINT_OK) {
      return id;
    }
  }
  return -1;
}

void resetEnroll() {
  nisnEnroll = "";
  namaEnroll = "";  
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
