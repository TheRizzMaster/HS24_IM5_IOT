# HS24_IM5_IOT – Taim.ing

## Überblick

**Taim.ing** ist ein innovatives IoT-Projekt zur Zeiterfassung, speziell entwickelt für Freelancer und Selbstständige. Es kombiniert RFID-Technologie mit einer benutzerfreundlichen Web-Oberfläche zur Visualisierung der erfassten Zeiten. Das System gibt visuelles und auditives Feedback und ermöglicht das Hinzufügen neuer Mitarbeiterkarten über einen Pairing-Modus.

[taim.ing](https://taim.ing)

![Modell](/git_image/Modell_Animation.gif)
---

## Inhalt

1. [UX Dokumentation](#ux-dokumentation)
   - Inspirationen
   - Designentscheidungen
   - Prozess und Vorgehensweise
2. [Technische Dokumentation](#technische-dokumentation)
   - Verbindungsschema
   - Kommunikationsprozess
   - Umsetzungsprozess
   - Known Bugs
   - Lernerfolg
3. [Aufgabenaufteilung](#aufgabenaufteilung)
4. [Learning & Herausforderungen](#learning--herausforderungen)

---

## UX Dokumentation

### Inspirationen
Für die Gestaltung der Benutzeroberfläche haben wir uns von minimalistischen und modernen Designs inspirieren lassen. Die Farbpalette wurde bewusst in dezenten, beruhigenden Farben gehalten, um ein professionelles Erscheinungsbild zu gewährleisten. 

### Designentscheidungen
- **Login-Screen**: Klare und einfache Struktur, um die User direkt zu empfangen.
- **Dashboard-Ansicht**: Die Kombination aus Kalender und Tabelle erlaubt eine schnelle Übersicht über gearbeitete Zeiten.
- **Card Manager**: Ermöglicht eine intuitive Verwaltung von Karten (RFID), sowie das Zuweisen neuer Karten.

### Prozess und Vorgehensweise
Taim.ing inspiriert sich an führenden Zeiterfassungs-Tools und vereint die Besten Aspekte derer. Nach Erstellung des Look and Feel der Seite konnten Elemente sowie Raster definiert werden. Die Umsetzung im Code konnte dieser Vorlagen abgenommen werden.

**Screenshots**:
![Login](/git_image/Login.png)
![Dashboard](/git_image/Dashboard.png)  
![Kartenverwaltung](/git_image/Kartenverwaltung.png)  

[Link to FIGMA](https://www.figma.com/design/aJm9YomMdNfLTVVhvq0MdX/Taim.ing?node-id=0-1&t=sUuOW3RuCRC4nPaW-1)

---

## Technische Dokumentation

### Verbindungsschema
![Steckplan](/git_image/Steckplan_taiming.png)  

![Steckplan](/git_image/Steckplan.gif) 

Das System basiert auf einem ESP32S3-Mikrocontroller, einem RFID-Reader, RGB-LED, einem Piezo-Summer sowie einem Button. Der RFID-Leser kommuniziert mit dem Mikrocontroller und überträgt die UID der Karten, welche anschliessend verarbeitet und in einer Tabelle gespeichert werden.

### Kommunikationsprozess
1. **RFID-Reader**: Liest die UID der Mitarbeiterkarte.
2. **Microcontroller (ESP32S3)**: Erkennt das Signal, prüft die UID und speichert die Zeitdaten.
3. **Visuelles und Auditives Feedback**: LED und Piezo-Summer informieren den Nutzer.
4. **Web-Server**: Sendet die Daten zur Visualisierung an die Website.


### Umsetzungsprozess
1. **Hardware-Einrichtung**:
   - Initiale Verkabelung der Komponenten (RFID, LEDs, Button, Piezo-Summer).
   - Test der Spannungsversorgung (Probleme mit dem SD-Card-Reader).
2. **Software-Entwicklung**:
   - WiFi-Einrichtung für die Web-Kommunikation.
   - Speichern der Netzwerk-Konfigurationen in einem Array.
   - Programmierung des RFID-Reader.
   - Programmierung des pairing Modus
   - Programmierung der Regeln für Erfolg und Error (LED, Piezo-Summer)
3. **Testing & Debugging**:
   - Pairing-Modus implementiert und verfeinert.
   - Testläufe zur Datenübertragung und Fehlersuche.
4. **Verworfene Lösungsansätze**:
   - Der ursprünglich geplante Einsatz eines SD-Card-Readers scheiterte am fehlenden 5V-Pin.

### Known Bugs
- Doom-Searching bei schlechten WiFi Signalen.
- Kein Handling für doppelte UID-Einträge implementiert.

### Lernerfolg
Wir haben gelernt, wie wichtig die Hardware-Auswahl für die Projektplanung ist. Zudem konnten wir wertvolle Erfahrungen in der Nutzung von IoT-Komponenten, insbesondere mit dem ESP32S3 und dem RFID-Reader, sammeln. Der Umgang mit Problemen wie der SD-Card-Reader-Herausforderung stärkte unsere Problemlösungsfähigkeiten.

---

## Aufgabenaufteilung
- **Umsetzungsplanung**: Teamarbeit
- **Hardware-Setup und Verkabelung**: Teamarbeit
- **Lötarbeiten**: Siro
- **Zusammensetzen der Komponenten**: Teamarbeit
- **Arduino-Programmierung**: Siro
- **UX-Design**: Joel
- **Backend**: Siro
- **Frontend**: Teamarbeit
- **3D-Model und Steckpläne**: Joel

---

## Learning & Herausforderungen
Geplant war der Einsatz eines Micro SD-Card-Readers zur Speicherung der WiFi-Konfiguration. Da unser Microcontroller jedoch keinen 5V-Pin bereitstellt, mussten wir diesen Ansatz aufgeben und die Netzwerkkonfiguration in einem Array ablegen. Diese Lösung ermöglichte uns Flexibilität, jedoch mit Einschränkungen bei der Skalierbarkeit.

---

**Credits**  
Dieses Projekt wurde mithilfe von KI-Hilfsmitteln wie ChatGPT und GitHub Copilot erstellt, um Dokumentation, Programmierung und Debugging zu erleichtern.

