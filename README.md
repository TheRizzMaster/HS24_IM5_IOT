# HS24_IM5_IOT – Taim.ing

## Projektbeschreibung
**HS24_IM5_IOT – Taim.ing** ist ein IoT-Projekt, das ein Zeiterfassungsgerät mit integriertem RFID-Leser entwickelt. Das System ermöglicht es Benutzern, sich mit RFID-Karten zu identifizieren und ihre An- und Abwesenheitszeiten zu erfassen. Ziel des Projekts ist die effiziente und kontaktlose Zeitverwaltung, die einfach in Arbeitsumgebungen oder Zugangskontrollsysteme integriert werden kann.

## Funktionen
- **RFID-Leser**: Erfasst RFID-Karten zur Identifikation von Benutzern.
- **Zeitstempel-Funktion**: Automatische Protokollierung von An- und Abmeldezeiten.
- **Datenspeicherung**: Speicherung von Zeiterfassungsdaten zur späteren Analyse.
- **Web-Dashboard (optional)**: Visualisierung und Export der erfassten Daten (wenn implementiert).

## Systemarchitektur
Das Projekt umfasst:
- **Hardware**: Mikrocontroller (z.B. ESP32S3), RFID-Lesemodul (MFRC522), LEDs und gegebenenfalls ein Piezo Summer
- **Software**: Firmware für die Steuerung des RFID-Lesers, Feedback Signalen und HTTP-Requests.

## Voraussetzungen
### Hardware
- RFID-Lesemodul (z.B. MFRC522)
- Mikrocontroller (z.B. ESP32S3)
- Breadboard und Verkabelung
- LED-Anzeigen und ggf. ein Piezo Summer
