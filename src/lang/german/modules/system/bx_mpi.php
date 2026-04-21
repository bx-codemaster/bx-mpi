<?php
/**
 * BX Modified Product Identifier (MPI)
 * Deutsche Sprachkonstanten für System-Modul
 * 
 * @package    BX Modified Product Identifier
 * @subpackage Language
 * @language   Deutsch
 * @author     Axel Benkert
 * @version    1.0.0
 * @date       2025-01-16
 * @copyright  2025 Axel Benkert
 * @license    GNU General Public License v2.0
 */

defined('_VALID_XTC') or die('Direct Access to this location is not allowed.');

// Modul-Titel & Beschreibung
define('MODULE_BX_MPI_TEXT_TITLE', 'BX Product Identifier (MPI)');
define('MODULE_BX_MPI_TEXT_DESCRIPTION', '
  <h3 style="margin-top: 0; display:flex; align-items:center; gap:8px;">'.xtc_image(DIR_WS_ICONS.'heading/bx_mpi.png', 'BX Product Identifier', '', '', 'style="max-height: 32px;"').' BX Product Identifier (MPI)</h3>
  <div style="background: #f8f9fa; padding: 5px 20px; border-radius: 8px; margin: 10px 0;">    
    <p>Dieses Modul ermöglicht die eindeutige Identifikation von Produkten mit Attributen für die Warenwirtschafts-Integration.</p>
    <p>Zentrale Verwaltung eindeutiger Produktidentifikatoren</p>
    
    <h4 style="color: #333; margin-top: 20px;">📦 Hauptfunktionen:</h4>
    <ul>
      <li><strong>SKU-Generierung:</strong> Automatische Erstellung eindeutiger SKUs für Produkt-Varianten</li>
      <li><strong>EAN/GTIN-Verwaltung:</strong> Verwaltung von EAN-13, UPC, ISBN pro Variante</li>
      <li><strong>WWS-Integration:</strong> Mapping zu externen Warenwirtschafts-Artikelnummern</li>
      <li><strong>Lagerplatz-Verwaltung:</strong> Optional: Regal/Fach-Zuordnung</li>
      <li><strong>API für Drittmodule:</strong> RMA, Versand, Export-Module können darauf zugreifen</li>
      <li><strong>Änderungs-Historie:</strong> Optional: Protokollierung aller Änderungen</li>
    </ul>
    
    <h4 style="color: #333; margin-top: 20px;">💡 Anwendungsbeispiele:</h4>
    <ul>
      <li><strong>T-Shirt Rot M:</strong> TSHIRT-001-ROT-M (aus Basis + Attribute)</li>
      <li><strong>RMA-Modul:</strong> Eindeutige Identifikation reklamierter Varianten</li>
      <li><strong>Versand-Label:</strong> Barcode-Druck mit EAN</li>
      <li><strong>Lager-Scanner:</strong> Schnelle Produkterkennung via EAN</li>
      <li><strong>WWS-Export:</strong> Übergabe an JTL, SAP, Lexware</li>
    </ul>
    
    <h4 style="color: #333; margin-top: 20px;">🔧 Konfiguration:</h4>
    <ul>
      <li><strong>Auto-Create:</strong> Automatische SKU-Generierung bei Bestellung</li>
      <li><strong>SKU-Separator:</strong> Trennzeichen für SKU-Teile (Standard: "-")</li>
      <li><strong>Historie:</strong> Änderungen protokollieren (für Audit)</li>
    </ul>
  </div>
');

// Konfigurationsoptionen

// 1. Status
define('MODULE_BX_MPI_STATUS_TITLE', 'Modul aktivieren');
define('MODULE_BX_MPI_STATUS_DESC', 'Möchten Sie das BX Product Identifier Modul aktivieren?');

// 2. Config ID (versteckt)
define('MODULE_BX_MPI_CONFIG_ID_TITLE', 'Konfigurations-Gruppen-ID');
define('MODULE_BX_MPI_CONFIG_ID_DESC', 'Interne ID der Konfigurationsgruppe (nicht ändern)');

// 3. Auto-Create
define('MODULE_BX_MPI_AUTO_CREATE_TITLE', 'Automatische SKU-Generierung');
define('MODULE_BX_MPI_AUTO_CREATE_DESC', '
  Soll automatisch eine eindeutige SKU erstellt werden, wenn ein Produkt mit Attributen bestellt wird?<br>
  <small><strong>true:</strong> SKU wird bei erster Bestellung generiert (empfohlen)<br>
  <strong>false:</strong> SKUs müssen manuell gepflegt werden</small>
');

// 4. SKU Separator
define('MODULE_BX_MPI_SKU_SEPARATOR_TITLE', 'SKU-Trennzeichen');
define('MODULE_BX_MPI_SKU_SEPARATOR_DESC', '
  Trennzeichen für SKU-Bestandteile.<br>
  <small>Beispiel mit "-": <code>TSHIRT-001-ROT-M</code><br>
  Beispiel mit "_": <code>TSHIRT_001_ROT_M</code></small>
');

// 5. Enable History
define('MODULE_BX_MPI_ENABLE_HISTORY_TITLE', 'Änderungs-Historie aktivieren');
define('MODULE_BX_MPI_ENABLE_HISTORY_DESC', '
  Sollen alle Änderungen an Produktidentifikatoren protokolliert werden?<br>
  <small><strong>true:</strong> Alle Änderungen werden in Historie-Tabelle gespeichert (empfohlen für Audit)<br>
  <strong>false:</strong> Keine Protokollierung (spart Speicherplatz)</small>
');

// 6. EAN Mode
define('MODULE_BX_MPI_EAN_MODE_TITLE', 'EAN-Generierung');
define('MODULE_BX_MPI_EAN_MODE_DESC', '
  <strong>Wie sollen EAN-Codes generiert werden?</strong><br><br>
  
  <strong style="color: #333;">manual:</strong> Keine automatische Generierung (Admin pflegt manuell)<br>
  <small style="color: #666;">→ Beste Kontrolle, geeignet für kleine Shops oder wenn Lieferanten-EANs vorhanden</small><br><br>
  
  <strong style="color: #333;">auto_pseudo:</strong> Pseudo-EAN mit Prefix "2" (Instore-Code)<br>
  <small style="color: #666;">→ Automatisch generiert, Scanner-kompatibel, NICHT für externen Handel (Amazon/eBay)<br>
  → Ideal für interne Prozesse: Lager, RMA, Kommissionierung</small><br><br>
  
  <strong style="color: #333;">auto_gs1:</strong> Echte EAN mit GS1-Präfix<br>
  <small style="color: #666;">→ Handelbar auf Amazon/eBay/Kaufland, erfordert GS1-Mitgliedschaft (ca. 100-300€/Jahr)<br>
  → GS1-Präfix muss unten eingetragen werden</small><br><br>
  
  <div style="background: #fff3cd; padding: 10px; border-radius: 4px; margin-top: 10px;">
    <strong>💡 Hinweis zu Multi-Attribut-Produkten:</strong><br>
    <small>Bei Produkten mit mehreren Attributen (z.B. T-Shirt Größe M + Farbe Rot) gibt es in modified keine eindeutige EAN.
    BX MPI generiert dann automatisch eine eindeutige EAN pro Variante - abhängig vom gewählten Modus.</small>
  </div>
');

// 7. GS1 Prefix
define('MODULE_BX_MPI_GS1_PREFIX_TITLE', 'GS1-Präfix');
define('MODULE_BX_MPI_GS1_PREFIX_DESC', '
  <strong>GS1-Präfix für EAN-Generierung</strong> (nur bei Modus <strong>auto_gs1</strong> erforderlich)<br><br>
  
  Geben Sie Ihren registrierten GS1-Präfix ein (7-10 Stellen).<br>
  <small><strong>Beispiel:</strong> <code>4004332</code></small><br><br>
  
  <div style="background: #f8d7da; padding: 10px; border-radius: 4px; border-left: 4px solid #dc3545;">
    <strong>⚠️ Wichtig:</strong> GS1-Mitgliedschaft erforderlich!<br>
    <small>Ohne gültigen Präfix können keine handelbaren EANs generiert werden.<br>
    Kosten: ca. 100-300€/Jahr je nach Land und Kontingent.</small>
  </div>
  
  <div style="margin-top: 10px;">
    <small>ℹ️ Weitere Informationen: 
    <a href="https://www.gs1-germany.de" target="_blank" style="color: #0066cc;">www.gs1-germany.de</a></small>
  </div>
');

// 8. Sort Order
define('MODULE_BX_MPI_SORT_ORDER_TITLE', 'Sortierung');
define('MODULE_BX_MPI_SORT_ORDER_DESC', 'Sortierreihenfolge in der Modul-Übersicht');

// 9. Maximale Anzeigeergebnisse
defined('MAX_DISPLAY_MPI_RESULTS_TITLE') or define('MAX_DISPLAY_MPI_RESULTS_TITLE', 'Maximale Anzeigeergebnisse');
defined('MAX_DISPLAY_MPI_RESULTS_DESC') or define('MAX_DISPLAY_MPI_RESULTS_DESC', 'Anzahl der gleichzeitig angezeigten Ergebnisse');

// Admin-Interface Texte (für spätere Verwendung)
define('TEXT_BX_MPI_HEADING', 'Product Identifier (SKU/EAN)');
define('TEXT_BX_MPI_OVERVIEW', 'Übersicht');
define('TEXT_BX_MPI_EDIT', 'Bearbeiten');
define('TEXT_BX_MPI_DELETE', 'Löschen');
define('TEXT_BX_MPI_ADD', 'Hinzufügen');

define('TEXT_BX_MPI_SKU_COMPLETE', 'Komplette SKU');
define('TEXT_BX_MPI_EAN', 'EAN-13 / GTIN');
define('TEXT_BX_MPI_UPC', 'UPC');
define('TEXT_BX_MPI_ISBN', 'ISBN');
define('TEXT_BX_MPI_WWS_NR', 'Warenwirtschafts-Nr.');
define('TEXT_BX_MPI_WWS_SYSTEM', 'WWS-System');
define('TEXT_BX_MPI_WAREHOUSE_LOCATION', 'Lagerplatz');

define('TEXT_BX_MPI_PRODUCT', 'Produkt');
define('TEXT_BX_MPI_ATTRIBUTES', 'Attribute');
define('TEXT_BX_MPI_NO_ATTRIBUTES', 'Basis-Artikel (ohne Attribute)');

define('TEXT_BX_MPI_HISTORY', 'Änderungs-Historie');
define('TEXT_BX_MPI_CHANGED_BY', 'Geändert von');
define('TEXT_BX_MPI_CHANGED_AT', 'Geändert am');
define('TEXT_BX_MPI_FIELD_NAME', 'Feld');
define('TEXT_BX_MPI_OLD_VALUE', 'Alter Wert');
define('TEXT_BX_MPI_NEW_VALUE', 'Neuer Wert');

define('TEXT_BX_MPI_SAVE_SUCCESS', 'Produktidentifikator erfolgreich gespeichert');
define('TEXT_BX_MPI_DELETE_SUCCESS', 'Produktidentifikator erfolgreich gelöscht');
define('TEXT_BX_MPI_ERROR', 'Fehler beim Speichern');

define('TEXT_BX_MPI_CONFIRM_DELETE', 'Wirklich löschen? Diese Aktion kann nicht rückgängig gemacht werden.');

// API-Nachrichten
define('TEXT_BX_MPI_API_NOT_AVAILABLE', 'BX Product Identifier Modul ist nicht installiert oder deaktiviert');
define('TEXT_BX_MPI_API_SKU_CREATED', 'SKU automatisch erstellt');
define('TEXT_BX_MPI_API_SKU_EXISTS', 'SKU bereits vorhanden');
