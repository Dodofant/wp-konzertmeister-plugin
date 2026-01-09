# Konzertmeister Events

Holt die Konzertmeister-Termine als HTML (ohne iFrame) und rendert sie sauber in deinem WordPress-Theme.  
Das Design steuerst du vollständig im Admin-Menü **KM Events** – inklusive Live-Vorschau, Presets und Mobile-spezifischen Optionen.

- **Version:** 3.0.0  
- **Erfordert WordPress:** 6.5+  
- **Erfordert PHP:** 8.0+  
- **Lizenz:** GPLv3  
- **Autor:** Pascal Heitzmann – https://heizi.ch/

---

## Highlights

- 🔌 **Shortcode** `[km_events]`
- 🎨 **Live-Styling im Backend** (Farben, Transparenz, Trenner, Rahmen, Hover)
- 🧩 **CSS-Variablen-basiert** – ideal für Theme-Overrides
- 🟣 **Design-Presets** mit einem Klick
- 📌 **Sticky Live-Vorschau** im Admin (scrollt mit)
- 📱 **Mobile-Optimierungen**
  - Externe Links auf Mobile ausblendbar
  - Separate Toggle-Logik für Desktop vs. Mobile
- 🧭 **Standort-Link** separat ein-/ausblendbar
- 🧼 **Sauberes HTML ohne iFrame**
- 🛡️ **Sicheres Sanitizing** der Inhalte

---

## Installation

1. Ordner **`konzertmeister-events`** nach  
   `wp-content/plugins/` kopieren

2. Plugin in WordPress aktivieren

3. Menü **KM Events** öffnen  
   → Konzertmeister-URL (vollständige Embed-URL inkl. `hash`) eintragen

4. Design & Optionen konfigurieren  
   → **Speichern**

5. Shortcode einfügen:

```txt
[km_events]
```

---

## Backend-Einstellungen

### Presets
Vordefinierte Stilvorlagen, z. B.

- Konzertmeister hell / dunkel
- Violett Light / Dark
- Cream Brass
- Dark Stage

Presets füllen alle Felder automatisch aus (wirksam nach Speichern).

---

### Allgemeine Farben
- Textfarbe
- Hintergrundfarbe (inkl. RGBA / Transparenz)
- Hintergrund aktivieren
- Badge-Farbe (Jahr / Wochentag)

---

### Trenner
- Gemeinsame Trennerfarbe
- Horizontale Trenner
  - Ein / Aus
  - Linienstärke (px)
- Vertikaler Trenner
  - Ein / Aus
  - Linienstärke (px)

---

### Rahmen der Liste
- Rahmen aktivieren
- Rahmenfarbe
- Breite (px)
- Radius (px)

---

### Hover-Effekt
- Kein
- Glow
- Lift
- Shade
- Underline

---

### Weitere Optionen
- Standort-Link anzeigen (`.km-location`)
- URL-Link anzeigen (`.km-external-link`)
- URL-Link auf Mobile automatisch ausblenden

---

### Quelle
- Konzertmeister-URL  
  (HTTPS, vollständige Embed-URL inkl. Hash)

---

## CSS-Variablen

Alle Styles werden über CSS-Variablen gesteuert:

```css
:root {
  --kme-text: #0E1111;
  --kme-bg: rgba(233,230,237,0.9);
  --kme-badge: #9B82D9;
  --kme-sep: #9B82D9;
  --kme-border-color: #0E1111;
  --kme-border-width: 1px;
  --kme-border-radius: 12px;
  --kme-sep-h-width: 1px;
  --kme-sep-v-width: 5px;
}
```

---

## Mobile-Verhalten

| Element | Desktop | Mobile |
|-------|--------|--------|
| Standort-Link | konfigurierbar | konfigurierbar |
| Externer Link | sichtbar | optional ausblendbar |
| Hover-Effekte | aktiv | deaktiviert / neutral |

---

## Sicherheit & Datenschutz

- Inhalte werden per `wp_kses` bereinigt
- Kein Tracking, keine Cookies
- Keine personenbezogenen Daten
- Nur die Konzertmeister-URL wird gespeichert

---

## Inhalt
   konzertmeister-events/
   ├─ km-events.php
   ├─ admin.php
   ├─ km-events.css
   ├─ admin.css
   └─ assets/
      └─ menu-icon.svg

---

## Changelog

### 3.0.0
- Mobile-spezifische Steuerung für externe Links
- Separate Toggles für Desktop / Mobile
- Erweiterte Presets
- Layout-Feinschliff (Standort unter Eventnamen)
- Admin-UI stabilisiert
- Vorbereitung für zukünftige Erweiterungen

### 2.6.x
- Sticky Admin-Vorschau
- Presets
- Rahmen-, Trenner- und Hover-Optionen
- Live-Vorschau

---

## Lizenz

Dieses Plugin ist freie Software und steht unter der  
**GNU General Public License Version 3 (GPLv3)**.

Du darfst es verwenden, verändern und weitergeben,  
sofern die Lizenzbedingungen eingehalten werden.

---

## Haftungsausschluss

Dieses Plugin basiert auf dem aktuellen HTML-Markup der Konzertmeister-Embed-Ausgabe.  
Änderungen seitens Konzertmeister können Anpassungen im Plugin erforderlich machen.
