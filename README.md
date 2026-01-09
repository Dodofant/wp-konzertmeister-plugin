# Konzertmeister Events

Holt die Konzertmeister-Termine als HTML (ohne iFrame) und rendert sie sauber in deinem WordPress-Theme.  
Das Design steuerst du bequem im Admin-Menü **KM Events**: Farben, Trenner, Rahmen, Hover-Effekte, Presets, Standort-Link – alles mit Live-Vorschau.

- **Version:** 2.6.0  
- **Erfordert WordPress:** 6.5+  
- **Erfordert PHP:** 8.0+  
- **Lizenz:** GPLv2 oder später  
- **Autor:** Pascal Heitzmann – [heizi.ch](https://heizi.ch/)

---

## Features

- 🔌 **Shortcode** `[km_events]` – Termine überall einbinden  
- 🎨 **Live-Styling im Backend**: Text, Hintergrund (inkl. Transparenz via RGBA/HSL), Badge, Trenner, Rahmen, Hover  
- ↔️ **Vertikale & horizontale Trenner** einzeln steuerbar (ein-/ausblenden, Breite in px)  
- 🟣 **Presets** (Konzertmeister hell/dunkel, Violett, Cream Brass, Dark Stage) – 1 Klick  
- 📌 **Sticky-Vorschau** im Admin, max. 600 px breit, scrollt mit  
- 🧭 **Standort-Link** ein-/ausblendbar  
- 🧩 **CSS-Variablen** → extra fein anpassbar im Theme  

---

## Installation

1. Ordner **`konzertmeister-events`** nach `wp-content/plugins/` kopieren.  
   Struktur:
   ```
   konzertmeister-events/
   ├─ km-events.php
   ├─ admin.php
   ├─ km-events.css
   ├─ admin.css
   └─ assets/
      └─ menu-icon.svg
   ```
2. In WordPress **aktivieren**.  
3. Menü **KM Events** öffnen → **Konzertmeister-URL** eintragen (vollständige Embed-URL inkl. `hash`).  
4. Farben/Optionen einstellen → **Speichern**.  
5. Shortcode in Seite oder Beitrag einfügen:

   ```txt
   [km_events]
   ```

---

## Einstellungen

### Presets
- Dropdown **Stilvorlage** + Button „Anwenden“.  
- Enthalten:  
  - Konzertmeister hell  
  - Konzertmeister dunkel  
  - Violett Light  
  - Violett Dark  
  - Cream Brass  
  - Dark Stage  

### Allgemeine Farben
- **Textfarbe**  
- **Hintergrund** (inkl. Transparenz via RGBA)  
- **Hintergrund aktivieren**  
- **Badge (Jahr/Wochentag)**

### Trenner
- **Trennerfarbe** (gemeinsam für vertikal & horizontal)  
- **Horizontale Linie** (Toggle) + **Linienstärke**  
- **Vertikale Linie** (Toggle) + **Linienstärke**

### Rahmen
- **Rahmen aktivieren**  
- **Rahmenfarbe**, **Breite (px)**, **Radius (px)**

### Hover-Effekt
- Auswahl: Kein, Glow, Lift, Shade, Underline

### Weitere Optionen
- **Eventstandort anzeigen** (zeigt `.km-location`)

### Quelle
- **Konzertmeister-URL** – vollständige Embed-URL (HTTPS, inkl. Hash)

---

## Shortcode

```txt
[km_events]
```

*(Keine Attribute nötig – alles via Admin-Menü konfiguriert.)*

---

## Styling & CSS-Variablen

Das Plugin nutzt Variablen, die du im Theme überschreiben kannst:

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

## Sicherheit & Datenschutz

- HTML von Konzertmeister wird **bereinigt** (`wp_kses`).  
- Branding-Footer (`.list-footer`) wird ausgeblendet.  
- **Keine personenbezogenen Daten** gespeichert. Nur die URL liegt in den Plugin-Optionen.  

---

## Changelog

**2.6.0**
- Sticky-Vorschau im Admin  
- Neue Presets (Violett Light/Dark, Cream Brass, Dark Stage)  
- Transparenz bei Farben möglich  
- Vertikaler Trenner ein-/ausblendbar  
- Admin-UI überarbeitet, deutschsprachige Labels

**2.5.x**
- Rahmen: aktivierbar, Farbe, Breite, Radius  
- Hover-Effekte: Glow, Lift, Shade, Underline  
- Standort-Link ein-/ausblendbar  
- Live-Vorschau eingeführt  

---

## Lizenz

GPLv2 oder später – freie Nutzung, Änderung und Weitergabe.

---

## Haftungsausschluss

Dieses Plugin hängt vom Markup der Konzertmeister-Embed-Ausgabe ab.  
Falls Konzertmeister Klassen oder Struktur ändert, muss CSS/Parsing ggf. angepasst werden.
