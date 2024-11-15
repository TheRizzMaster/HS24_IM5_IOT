#include <WiFi.h>
#include <HTTPClient.h>
#include <MFRC522v2.h>
#include <MFRC522DriverSPI.h>
#include <MFRC522DriverPinSimple.h>
#include <MFRC522Debug.h>
#include <SPI.h>
#include <Arduino_JSON.h>

// LED pin definitions
#define LED_RED 2
#define LED_BLUE 4
#define LED_GREEN 3

// Button pin definition
#define BUTTON_PIN 7

#define SOUND_MODULE_PIN 6

// RFID pin definitions
#define RFID_SDA_PIN 10    // Chip Select for RFID
#define RFID_RST_PIN 38    // Reset Pin for RFID (if used)
#define RFID_SCK_PIN 18    // SPI Clock for RFID
#define RFID_MISO_PIN 19   // SPI MISO for RFID
#define RFID_MOSI_PIN 11   // SPI MOSI for RFID

// Create the SPI instance
SPIClass spi;

// RFID driver instances
MFRC522DriverPinSimple ss_pin(RFID_SDA_PIN);
MFRC522DriverSPI rfidDriver(ss_pin, spi);
MFRC522 rfid(rfidDriver);  // Create MFRC522 instance


// WiFi credentials
const char* ssid = "yallo_2335951";
const char* password = "vwYrp4pyj5wjvfYx";

const char* serverURL = "https://taim.ing/php/";  // Replace with your server endpoint


void setup() {
  // Initialize serial monitor
  Serial.begin(115200);

  // Initialize LEDs
  pinMode(LED_RED, OUTPUT);
  pinMode(LED_BLUE, OUTPUT);
  pinMode(LED_GREEN, OUTPUT);

  pinMode(SOUND_MODULE_PIN, OUTPUT);
  digitalWrite(SOUND_MODULE_PIN, LOW);

  // Initialize button
  pinMode(BUTTON_PIN, INPUT_PULLUP);  // Use internal pull-up resistor

  // Turn off all LEDs initially (set HIGH for common anode configuration)
  digitalWrite(LED_RED, HIGH);
  digitalWrite(LED_BLUE, HIGH);
  digitalWrite(LED_GREEN, HIGH);

  // Initialize SPI for RFID reader
  spi.begin(RFID_SCK_PIN, RFID_MISO_PIN, RFID_MOSI_PIN, RFID_SDA_PIN);
  rfid.PCD_Init();
  Serial.println("RFID reader initialized");

  // Initialize WiFi and indicate with the blue LED
  digitalWrite(LED_BLUE, LOW);  // Blue LED glows while connecting
  WiFi.begin(ssid, password);
  while (WiFi.status() != WL_CONNECTED) {
    delay(500);
    Serial.print(".");
  }
  digitalWrite(LED_BLUE, HIGH);  // Turn off blue LED after connection
  Serial.println("\nConnected to WiFi");
}


void loop() {
  if (digitalRead(BUTTON_PIN) == LOW) {
    digitalWrite(LED_BLUE, LOW);
    if (rfid.PICC_IsNewCardPresent() && rfid.PICC_ReadCardSerial()) {
      String cardID = "";

      // Read the card ID
      for (byte i = 0; i < rfid.uid.size; i++) {
        cardID += String(rfid.uid.uidByte[i], HEX);
      }

      Serial.println("Card detected, ID: " + cardID);
      sendAddCardRequest(cardID);

      delay(1000);
    }
  } else {
    digitalWrite(LED_BLUE, HIGH);
    if (rfid.PICC_IsNewCardPresent() && rfid.PICC_ReadCardSerial()) {
      String cardID = "";

      // Read the card ID
      for (byte i = 0; i < rfid.uid.size; i++) {
        cardID += String(rfid.uid.uidByte[i], HEX);
      }

      Serial.println("Card detected, ID: " + cardID);
      sendStampRequest(cardID);

      delay(1000);
    }
  }
  // put your main code here, to run repeatedly:

}


void sendAddCardRequest(String cardID) {
  if (WiFi.status() == WL_CONNECTED) {
    HTTPClient http;
    String url = String(serverURL) + "registerChip.php";  // Correct string concatenation
    http.begin(url);
    http.addHeader("Content-Type", "application/json");

    // Create JSON payload
    JSONVar dataObject;
    dataObject["action"] = "add_card";
    dataObject["card_id"] = cardID;

    // Convert JSON object to string
    String jsonString = JSON.stringify(dataObject);

    // Send the POST request
    int httpResponseCode = http.POST(jsonString);

    // Handle the response
    if (httpResponseCode > 0) {
      String response = http.getString();
      Serial.println("HTTP Response code: " + String(httpResponseCode));
      Serial.println("Response: " + response);

      // Parse JSON response
      JSONVar responseObject = JSON.parse(response);
      if (JSON.typeof(responseObject) == "undefined") {
        Serial.println("Failed to parse JSON response");
        // Blink red and blue for error
        blinkLEDs(LED_RED, LED_BLUE, 3, 300);
      } else {
        // Check the "status" field in the response
        String status = (const char*) responseObject["status"];
        if (status == "success") {
          // Blink green and blue for success
          digitalWrite(SOUND_MODULE_PIN, HIGH);
          blinkLEDs(LED_GREEN, LED_BLUE, 3, 300);
          digitalWrite(SOUND_MODULE_PIN, LOW);
        } else {
          // Blink red and blue for error
          for (int i = 0; i < 3; i++) {
            digitalWrite(SOUND_MODULE_PIN, HIGH);
            digitalWrite(LED_BLUE, HIGH); // Turn off LED
            digitalWrite(LED_RED, LOW);  // Turn on LED (LOW for common anode)
            delay(300);
            digitalWrite(LED_BLUE, LOW);  // Turn on LED (LOW for common anode)
            digitalWrite(LED_RED, HIGH); // Turn off LED
            digitalWrite(SOUND_MODULE_PIN, LOW);
            delay(300);
          }
          digitalWrite(LED_BLUE, HIGH);
        }
      }
    } else {
      Serial.println("Error on sending POST: " + String(httpResponseCode));
      // Blink red and blue for error
      blinkLEDs(LED_RED, LED_BLUE, 3, 300);
    }

    http.end();  // Free resources
  } else {
    Serial.println("WiFi not connected");
    // Blink red and blue for error
    blinkLEDs(LED_RED, LED_BLUE, 3, 300);
  }
}

void sendStampRequest(String cardID) {
  if (WiFi.status() == WL_CONNECTED) {
    HTTPClient http;
    String url = String(serverURL) + "stamp.php";  // Correct string concatenation for the URL
    http.begin(url);
    http.addHeader("Content-Type", "application/json");

    // Create JSON payload
    JSONVar dataObject;
    dataObject["card_id"] = cardID;

    // Convert JSON object to string
    String jsonString = JSON.stringify(dataObject);

    // Send the POST request
    int httpResponseCode = http.POST(jsonString);

    // Handle the response
    if (httpResponseCode > 0) {
      String response = http.getString();
      Serial.println("HTTP Response code: " + String(httpResponseCode));
      Serial.println("Response: " + response);

      // Parse JSON response
      JSONVar responseObject = JSON.parse(response);
      if (JSON.typeof(responseObject) == "undefined") {
        Serial.println("Failed to parse JSON response");
        // Blink red for error
        for (int i = 0; i < 5; i++) {
          digitalWrite(SOUND_MODULE_PIN, HIGH);
          digitalWrite(LED_RED, LOW);
          delay(500);
          digitalWrite(SOUND_MODULE_PIN, LOW);
          digitalWrite(LED_RED, HIGH);
          delay(500);
        }
      } else {
        // Check the "status" field in the response
        String status = (const char*) responseObject["status"];
        String message = (const char*) responseObject["message"];
        if (status == "success") {
          if (message == "Session ended") {
            // Blink green LED twice
            for (int i = 0; i < 2; i++) {
              digitalWrite(SOUND_MODULE_PIN, HIGH);
              digitalWrite(LED_GREEN, LOW);
              delay(600);
              digitalWrite(SOUND_MODULE_PIN, LOW);
              digitalWrite(LED_GREEN, HIGH);
              delay(600);
            }
          } else if (message == "New session started") {
            // Blink green LED once
            digitalWrite(SOUND_MODULE_PIN, HIGH);
            digitalWrite(LED_GREEN, LOW);
            delay(2000);
            digitalWrite(SOUND_MODULE_PIN, LOW);
            digitalWrite(LED_GREEN, HIGH);
            delay(1000);
          } else {
            // Unknown success message - just blink green and blue as a fallback
            blinkLEDs(LED_GREEN, LED_BLUE, 3, 300);
          }
        } else {
          // Blink red for error
          for (int i = 0; i < 5; i++) {
            digitalWrite(SOUND_MODULE_PIN, HIGH);
            digitalWrite(LED_RED, LOW);
            delay(500);
            digitalWrite(SOUND_MODULE_PIN, LOW);
            digitalWrite(LED_RED, HIGH);
            delay(500);
          }
        }
      }
    } else {
      Serial.println("Error on sending POST: " + String(httpResponseCode));
      // Blink red and blue for error
      blinkLEDs(LED_RED, LED_BLUE, 3, 300);
    }

    http.end();  // Free resources
  } else {
    Serial.println("WiFi not connected");
    // Blink red and blue for error
    blinkLEDs(LED_RED, LED_BLUE, 3, 300);
  }
}



void blinkLEDs(int led1, int led2, int times, int duration) {
  for (int i = 0; i < times; i++) {
    digitalWrite(led1, HIGH); // Turn off LED
    digitalWrite(led2, LOW);  // Turn on LED (LOW for common anode)
    delay(duration);
    digitalWrite(led1, LOW);  // Turn on LED (LOW for common anode)
    digitalWrite(led2, HIGH); // Turn off LED
    delay(duration);
  }
  digitalWrite(led1, HIGH);
}

// Function to control the sound module with a specific pattern
void playSound(int times, int duration, int pause) {
  for (int i = 0; i < times; i++) {
    digitalWrite(SOUND_MODULE_PIN, HIGH);  // Turn on sound
    delay(duration);
    digitalWrite(SOUND_MODULE_PIN, LOW);   // Turn off sound
    delay(pause);
  }
}
