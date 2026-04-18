<?php
/* -----------------------------------------------------------------------------------------
   $Id: /lang/german/extra/admin/bx_mpi.php 1000 2026-01-02 13:00:00Z benax $
    _                           
   | |__   ___ _ __   __ ___  __
   | '_ \ / _ \ '_ \ / _ \ \/ /
   | |_) |  __/ | | | (_| |>  < 
   |_.__/ \___|_| |_|\__,_/_/\_\
   xxxxxxxxxxxxxxxxxxxxxxxxxxxxx

   modified eCommerce Shopsoftware
   http://www.modified-shop.org

   Copyright (c) 2009 - 2026 [www.modified-shop.org]
   -----------------------------------------------------------------------------------------
   Released under the GNU General Public License
   ---------------------------------------------------------------------------------------*/

 // Globale Konstanten
 define('BX_MPI_EXAMPLE_PREFIX', 'z.B. ');
 define('BX_MPI_ADMIN_CURRENT_CATEGORY', 'Aktuelle Kategorie');
 
 // Konstanten aus categories.php (falls nicht bereits geladen)
 //defined('HEADING_TITLE_GOTO') OR define('HEADING_TITLE_GOTO', 'Gehe zu:');
 defined('TEXT_BX_TOP') OR define('TEXT_BX_TOP', 'Top');
 defined('HEADING_TITLE_BX_GOTO') OR define('HEADING_TITLE_BX_GOTO', 'Gehe zu:');
 
 define('SKU_EAN_VARIANT_GENERATOR', 'SKU/EAN Varianten-Generator');
 define('SKU_EAN_VARIANT_COMBINATIONS', 'Kombinationen');
 define('SKU_EAN_VARIANT_GENERATOR_TOOLTIP', 'Generiert automatisch SKUs für alle Attribut-Kombinationen und ermöglicht EAN-Zuweisung');
 define('SKU_EAN_VARIANT_EAN_POOL_AVAILABLE', 'EANs im Pool verfügbar');
 define('SKU_EAN_VARIANT_EAN_POOL_NOT_AVAILABLE', 'Pool leer - Pseudo-EAN oder manuelle Eingabe verwenden');
 define('SKU_EAN_VARIANT_MANAGE_POOL', 'Pool verwalten');
 define('SKU_EAN_VARIANT_NOTE', 'Hinweis');
 define('SKU_EAN_VARIANT_SAVE_PRODUCT_FIRST', 'Bitte speichern Sie das Produkt, bevor Sie SKU/EAN zuweisen können.');
 define('SKU_EAN_VARIANT_SIMPLE_PRODUCT', 'Einfachprodukt (keine Varianten)');
 define('SKU_EAN_VARIANT_NO_ATTRIBUTES', 'Dieses Produkt hat keine Attribute - eine Basis-SKU wird verwendet');
 define('SKU_EAN_VARIANT_BASIS_SKU', 'Basis-SKU');
 define('SKU_EAN_VARIANT_SAVED', 'Gespeichert');
 define('SKU_EAN_VARIANT_AUTO_GENERATED', 'Wird beim Speichern automatisch generiert');
 define('SKU_EAN_VARIANT_PSEUDO_CURRENT_EAN', 'Aktuelle EAN');
 define('SKU_EAN_VARIANT_PSEUDO_EAN', 'Pseudo-EAN (Instore)');
 define('SKU_EAN_VARIANT_FROM_POOL', 'Aus Pool');
 define('SKU_EAN_VARIANT_MANUAL', 'Manuell');
 define('SKU_EAN_VARIANT_NONE', 'Keine');
 define('SKU_EAN_VARIANT_NO_EAN_ASSIGNED', 'Keine EAN zugewiesen');
 define('SKU_EAN_VARIANT_VARIANTS_TEXT', 'Varianten');
 define('SKU_EAN_VARIANT_VARIANTS_GENERATED_FROM', 'generiert aus Produkt-Attributen');
 define('SKU_EAN_VARIANT_SELECT_ALL', 'Alle auswählen');
 define('SKU_EAN_VARIANT_EAN_SOURCE_FOR_ALL', 'EAN-Quelle für alle');
 define('SKU_EAN_VARIANT_EAN_SOURCE_LABEL', 'EAN-Quelle auswählen');
 define('SKU_EAN_VARIANT_SELECT_FROM_POOL', 'Aus Pool zuweisen');
 define('SKU_EAN_VARIANT_AVAILABLE', 'verfügbar');
 define('SKU_EAN_VARIANT_EMPTY', 'leer');
 define('SKU_EAN_VARIANT_GENERATE_PSEUDO_EAN', 'Pseudo-EAN generieren (Instore)');
 define('SKU_EAN_VARIANT_MANUAL_ENTRY', 'Manuell eingeben');
 define('SKU_EAN_VARIANT_NO_EAN', 'Keine EAN');
 define('SKU_EAN_VARIANT_REMOVE_EAN', 'EAN entfernen');
 define('SKU_EAN_VARIANT_NO_EAN_PRESENT', 'keine EAN vorhanden');
 define('SKU_EAN_VARIANT_EAN_INPUT_LABEL', 'EAN eingeben');
 define('SKU_EAN_VARIANT_EAN_PLACEHOLDER', 'EAN eingeben (13 oder 14 Stellen)');
 define('SKU_EAN_VARIANT_EAN_FORMAT_INFO', 'Format: 13-stellige GTIN-13 oder 14-stellige GTIN-14');
 define('SKU_EAN_VARIANT_ASSIGN_EAN_BUTTON', 'EAN zuweisen');
 define('SKU_EAN_VARIANT_TABLE_VARIANTS', 'Varianten');
 define('SKU_EAN_VARIANT_TABLE_VARIANT', 'Variante');
 define('SKU_EAN_VARIANT_TABLE_GENERATED_SKU', 'Generierte SKU');
 define('SKU_EAN_VARIANT_TABLE_EAN', 'EAN');
 define('SKU_EAN_VARIANT_TABLE_EAN_SOURCE', 'EAN-Quelle');
 define('SKU_EAN_VARIANT_BULK_ACTIONS', 'Bulk-Aktionen');
 define('SKU_EAN_VARIANT_SELECT_VARIANTS_DESC', 'Wählen Sie Varianten aus und speichern Sie alle gleichzeitig');
 define('SKU_EAN_VARIANT_SAVE_SELECTED_BUTTON', 'Ausgewählte speichern & EANs zuweisen');
 define('SKU_EAN_VARIANT_BULK_TIP', 'Tipp: Mit diesen Buttons können Sie die EAN-Quelle für alle Varianten gleichzeitig festlegen');
 define('SKU_EAN_VARIANT_WILL_BE_GENERATED', 'wird beim Speichern generiert');
 define('SKU_EAN_VARIANT_SAVED_STATUS', 'Gespeichert');
 define('SKU_EAN_VARIANT_NEW_STATUS', 'Neu');
 define('SKU_EAN_VARIANT_SELECTION_CHECKBOX', 'Kontrollkästchen');
 define('SKU_EAN_VARIANT_JS_POOL_EMPTY', 'Pool ist leer - bitte wählen Sie eine andere Quelle');
 define('SKU_EAN_VARIANT_JS_ALL_VARIANTS_SET', 'Alle Varianten auf "%s" gesetzt');
 define('SKU_EAN_VARIANT_JS_PLEASE_ENTER_EAN', 'Bitte geben Sie eine EAN ein');
 define('SKU_EAN_VARIANT_JS_INVALID_EAN', 'Ungültige EAN - verwenden Sie 13 oder 14 Ziffern');
 define('SKU_EAN_VARIANT_JS_CONFIRM_REMOVE_EAN', 'EAN wirklich entfernen?');
 define('SKU_EAN_VARIANT_JS_CONFIRM_ASSIGN_POOL', 'EAN aus Pool zuweisen?');
 define('SKU_EAN_VARIANT_JS_CONFIRM_GENERATE_PSEUDO', 'Pseudo-EAN generieren?');
 define('SKU_EAN_VARIANT_JS_CONFIRM_MANUAL_EAN', 'Manuelle EAN "%s" zuweisen?');
 define('SKU_EAN_VARIANT_JS_PROCESSING', 'Verarbeite...');
 define('SKU_EAN_VARIANT_JS_SELECT_VARIANT', 'Bitte wählen Sie mindestens eine Variante aus');
 define('SKU_EAN_VARIANT_JS_CONFIRM_SAVE', 'Wirklich %d Varianten speichern?
 SKUs werden generiert und EANs zugewiesen.');
 define('SKU_EAN_VARIANT_JS_SAVING', 'Speichern... <span id=\'save-progress\'>0/%d</span>');
 define('SKU_EAN_VARIANT_JS_SAVING_SUCCESS', ' Varianten erfolgreich gespeichert');
 define('SKU_EAN_VARIANT_JS_ERROR_PREFIX', 'Fehler: ');
 define('SKU_EAN_VARIANT_JS_CONNECTION_ERROR', 'Verbindungsfehler: ');

 // Settings Tab
 define('BX_MPI_SETTINGS_MODULE_SETTINGS', 'Modul-Einstellungen');
 define('BX_MPI_SETTINGS_CONFIG_ID', 'Konfigurations-ID');
 define('BX_MPI_SETTINGS_ADVANCED_CONFIG', 'Erweiterte Konfiguration');
 define('BX_MPI_SETTINGS_INTRO', 'Hier können Sie die wichtigsten Einstellungen des BX MPI Moduls anpassen.');
 define('BX_MPI_SETTINGS_INTRO_ADVANCED', 'Für erweiterte Optionen nutzen Sie die Konfigurations-Seite.');
define('BX_MPI_SETTINGS_AT', 'Bei');

// SKU Format Info
define('BX_MPI_SETTINGS_SKU_FORMAT_TITLE', 'SKU-Format');
define('BX_MPI_SETTINGS_SKU_FORMAT_FIXED', 'Festes Format');
define('BX_MPI_SETTINGS_SKU_FORMAT_EXAMPLE', 'Beispiel');
define('BX_MPI_SETTINGS_SKU_FORMAT_SEPARATOR_INFO', 'Trennzeichen sind fest: "_" (nach PID), "-" (zwischen Option/Value), "x" (zwischen Attributen)');
define('BX_MPI_SETTINGS_SKU_FORMAT_PADDING_INFO', 'Alle IDs sind 4-stellig mit führenden Nullen (Zero-Padded)');
define('BX_MPI_SETTINGS_SKU_FORMAT_OPTIMIZED_INFO', 'Format ist optimiert für Lagerverwaltung, Scanner und BX Stockmanager Integration');
 
 // Auto Create SKU
 define('BX_MPI_SETTINGS_AUTO_CREATE_TITLE', 'Automatische SKU-Generierung');
 define('BX_MPI_SETTINGS_AUTO_CREATE_DESC', 'Soll automatisch eine eindeutige SKU erstellt werden, wenn ein Produkt mit Attributen bestellt wird?');
 define('BX_MPI_SETTINGS_RECOMMENDED', 'Empfohlen');
 define('BX_MPI_SETTINGS_ACTIVATED', 'Aktiviert');
 define('BX_MPI_SETTINGS_DEACTIVATED', 'Deaktiviert');
 
 // SKU Prefix
 define('BX_MPI_SETTINGS_SKU_PREFIX_TITLE', 'SKU-Präfix');
 define('BX_MPI_SETTINGS_SKU_PREFIX_DESC', 'Optionaler Präfix für alle generierten SKUs. Kann leer gelassen werden.');
 define('BX_MPI_SETTINGS_SKU_PREFIX_PLACEHOLDER', 'SKU oder ART');
 define('BX_MPI_SETTINGS_SKU_PREFIX_EXAMPLE_WITH', 'Beispiel mit "SKU"');
 define('BX_MPI_SETTINGS_SKU_PREFIX_EXAMPLE_WITHOUT', 'Ohne Präfix');
 define('BX_MPI_SETTINGS_SKU_PREFIX_NOTE', 'Hinweis: Trennzeichen sind fest definiert und können nicht geändert werden (siehe Info-Box oben).');
 
 // History
 define('BX_MPI_SETTINGS_HISTORY_TITLE', 'Änderungs-Historie');
 define('BX_MPI_SETTINGS_HISTORY_DESC', 'Sollen alle Änderungen an Produktidentifikatoren protokolliert werden?');
 define('BX_MPI_SETTINGS_HISTORY_RECOMMENDED', 'Aktiviert für Audit-Trail');
 
 // EAN Mode
 define('BX_MPI_SETTINGS_EAN_MODE_TITLE', 'EAN-Generierung');
 define('BX_MPI_SETTINGS_EAN_MODE_MANUAL', 'Manuell');
 define('BX_MPI_SETTINGS_EAN_MODE_AUTO_PSEUDO', 'Auto Pseudo-EAN (Prefix 2)');
 define('BX_MPI_SETTINGS_EAN_MODE_AUTO_GS1', 'Auto GS1-EAN (handelbar)');
 define('BX_MPI_SETTINGS_EAN_MODE_DETAIL_TITLE', 'Modi im Detail');
 define('BX_MPI_SETTINGS_EAN_MODE_MANUAL_DESC', 'Keine automatische Generierung (volle Kontrolle)');
 define('BX_MPI_SETTINGS_EAN_MODE_PSEUDO_DESC', 'Prefix "2" + ID + Checksum (Scanner-kompatibel, NICHT handelbar)');
 define('BX_MPI_SETTINGS_EAN_MODE_GS1_DESC', 'Echter EAN-Code (handelbar, erfordert GS1-Mitgliedschaft)');
 
 // GS1 Prefix
 define('BX_MPI_SETTINGS_GS1_PREFIX_TITLE', 'GS1-Präfix');
 define('BX_MPI_SETTINGS_GS1_PREFIX_ONLY_FOR', 'nur für GS1-EAN');
 define('BX_MPI_SETTINGS_GS1_PREFIX_DESC', 'Ihr registrierter GS1-Präfix (7-10 Stellen). Nur erforderlich bei Modus "Auto GS1-EAN".');
 define('BX_MPI_SETTINGS_GS1_PREFIX_IMPORTANT', 'Wichtig');
 define('BX_MPI_SETTINGS_GS1_PREFIX_MEMBERSHIP', 'Erfordert GS1-Mitgliedschaft (ca. 100-300€/Jahr)');
 define('BX_MPI_SETTINGS_GS1_PREFIX_INFO', 'Weitere Informationen zur GS1-Mitgliedschaft');
 
 // Save Button
 define('BX_MPI_SETTINGS_SAVE_BUTTON', 'Einstellungen speichern');
 
 // Allgemeine Meldungen
 define('BX_MPI_MSG_IDENTIFIER_UPDATED', 'Identifier erfolgreich aktualisiert!');
 define('BX_MPI_MSG_IDENTIFIER_DELETED', 'Identifier erfolgreich gelöscht!');
 define('BX_MPI_MSG_BLOCK_IMPORTED', 'Block "%s" mit %d EANs erfolgreich importiert');
 define('BX_MPI_MSG_BLOCK_DELETED', 'Block erfolgreich gelöscht');
 define('BX_MPI_MSG_EAN_ASSIGNED', 'EAN erfolgreich zugewiesen');
 define('BX_MPI_MSG_EAN_RELEASED', 'EAN zurück in Pool gelegt');
 define('BX_MPI_MSG_EAN_REMOVED', 'EAN erfolgreich entfernt');
 define('BX_MPI_MSG_EANS_ASSIGNED', '%d EANs erfolgreich zugewiesen');
 define('BX_MPI_MSG_VARIANTS_SAVED', '%d Varianten erfolgreich gespeichert');
 
 // Fehlermeldungen
 define('BX_MPI_ERR_BLOCK_NUMBER_MISSING', 'Block-Nummer fehlt');
 define('BX_MPI_ERR_INVALID_BLOCK_SIZE', 'Ungültige Block-Größe');
 define('BX_MPI_ERR_FILE_UPLOAD', 'Fehler beim Datei-Upload');
 define('BX_MPI_ERR_COLUMN_NOT_FOUND', 'Spalte "Gtin" nicht gefunden');
 define('BX_MPI_ERR_NO_VALID_EANS', 'Keine gültigen EANs gefunden');
 define('BX_MPI_ERR_IMPORT_FAILED', 'Fehler beim Import: %s');
 define('BX_MPI_ERR_BLOCK_HAS_ASSIGNED_EANS', 'Block kann nicht gelöscht werden: %d EANs sind bereits zugewiesen');
 define('BX_MPI_ERR_IDENTIFIER_NOT_FOUND', 'Identifier nicht gefunden');
 define('BX_MPI_ERR_IDENTIFIER_HAS_EAN', 'Identifier hat bereits eine EAN: %s');
 define('BX_MPI_ERR_IDENTIFIER_NO_EAN', 'Identifier hat keine EAN');
 define('BX_MPI_ERR_NO_POOL_EANS', 'Keine EANs im Pool verfügbar');
 define('BX_MPI_ERR_NO_IDENTIFIERS_WITHOUT_EAN', 'Keine Identifiers ohne EAN gefunden');
 define('BX_MPI_ERR_SKU_CREATION_FAILED', 'Fehler beim Erstellen der SKU');
 define('BX_MPI_ERR_IDENTIFIER_CREATION_FAILED', 'Identifier konnte nicht erstellt werden');
 define('BX_MPI_ERR_PSEUDO_EAN_FAILED', 'Fehler beim Generieren der Pseudo-EAN');
 define('BX_MPI_ERR_INVALID_EAN_FORMAT', 'Ungültige EAN - verwenden Sie 13 oder 14 Ziffern');
 define('BX_MPI_ERR_EAN_ALREADY_ASSIGNED', 'EAN bereits vergeben an anderen Identifier');
 define('BX_MPI_ERR_INVALID_VARIANT_DATA', 'Ungültige Varianten-Daten');
 define('BX_MPI_ERR_SKU_CREATION_VARIANT_FAILED', 'Fehler beim Erstellen der SKU für Variante');
 define('BX_MPI_ERR_IDENTIFIER_ID_NOT_FOUND', 'Identifier-ID konnte nicht ermittelt werden');
 define('BX_MPI_ERR_INVALID_EAN_FORMAT_VALUE', 'Ungültige EAN-Format: %s');
 define('BX_MPI_ERR_EAN_ALREADY_ASSIGNED_VALUE', 'EAN bereits vergeben: %s');
 define('BX_MPI_ERR_UNKNOWN_AJAX_ACTION', 'Unbekannte AJAX-Action');
 
 // Admin UI Texte
 define('BX_MPI_PAGE_TITLE', 'BX Modified Product Identifier');
 define('BX_MPI_PAGE_DESCRIPTION', 'Zentrale Verwaltung eindeutiger Produktidentifikatoren');
 define('BX_MPI_TAB_DASHBOARD', 'Dashboard');
 define('BX_MPI_TAB_ADMIN', 'SKU/EAN-Verwaltung');
 define('BX_MPI_TAB_POOL', 'EAN-Pool');
 define('BX_MPI_TAB_HISTORY', 'Historie');
 define('BX_MPI_TAB_SETTINGS', 'Einstellungen');
 define('BX_MPI_EDIT_IDENTIFIER', 'Identifier bearbeiten');
 define('BX_MPI_CLOSE_BUTTON', 'Schließen');
 define('BX_MPI_PRODUCT_ID_LABEL', 'Produkt-ID');
 define('BX_MPI_PRODUCT_NAME_LABEL', 'Produktname');
 define('BX_MPI_SKU_READONLY_LABEL', 'SKU (Read-only)');
 define('BX_MPI_ATTRIBUTES_LABEL', 'Attribute');
 define('BX_MPI_DELETE_CONFIRM', 'Identifier wirklich löschen? Dies kann nicht rückgängig gemacht werden!');
 define('BX_MPI_DELETE_BLOCK_CONFIRM', 'Block wirklich löschen?');
 define('BX_MPI_DISPLAY_COUNT', 'Zeige <strong>%d</strong> bis <strong>%d</strong> (von <strong>%d</strong> Einträgen)');
 define('BX_MPI_RESET_BUTTON', 'Zurücksetzen');
 define('BX_MPI_BACK_OVERVIEW', 'Zurück zur Übersicht');
 define('BX_MPI_DEBUG_NO_BLOCKS', 'Keine Blöcke in Datenbank gefunden!');
 define('BX_MPI_LOW_EANS_WARNING', 'Nur noch <strong>%d</strong> EANs verfügbar');
 define('BX_MPI_BLOCK_DEPLETED', 'Erschöpft');
 define('BX_MPI_NEXT_PAGE', 'Nächste »');
 define('BX_MPI_RESET_FILTER', 'Filter zurücksetzen');
 define('BX_MPI_CONFIG_STATUS', 'Aktuelle Konfiguration');
 define('BX_MPI_CURRENT_VIEW', 'Aktuelle Ansicht');
 define('BX_MPI_QUICK_FILTER', 'Quick Filter');
 define('BX_MPI_QUICK_ACTIONS', 'Quick Actions');
 define('BX_MPI_POOL_STATUS', 'Pool-Status');
 define('BX_MPI_CSV_FILE_ERROR', 'CSV-Datei konnte nicht geöffnet werden');
 define('BX_MPI_SETTINGS_SAVED', '✓ Einstellungen erfolgreich gespeichert!');
 define('BX_MPI_ACTIVE_FILTER_TITLE', 'Aktiver Filter');
 define('BX_MPI_FILTER_ACTIVE_INFO', 'Sie sehen nur die gefilterten Ergebnisse. Nutzen Sie "Zurücksetzen" für die Gesamtansicht.');
 define('BX_MPI_NO_EAN_TITLE', 'Produkte ohne EAN');
 define('BX_MPI_NO_EAN_DESC', 'Diese Produkte haben noch keine EAN-Nummer. Bei aktivierter Auto-Generierung wird die EAN beim nächsten Zugriff erstellt.');
 define('BX_MPI_PSEUDO_EAN_TITLE', 'Pseudo-EAN (Prefix 2)');
 define('BX_MPI_PSEUDO_EAN_DESC', 'Für interne Zwecke und Lager. Nicht für Marktplätze wie Amazon/eBay geeignet!');
 define('BX_MPI_GS1_EAN_TITLE', 'GS1-EAN Codes');
 define('BX_MPI_GS1_EAN_DESC', 'Diese EANs sind handelbar und können auf Marktplätzen verwendet werden.');
 define('BX_MPI_NO_WAREHOUSE_TITLE', 'Ohne Lagerplatz');
 define('BX_MPI_NO_WAREHOUSE_DESC', 'Tragen Sie Lagerplätze ein, um die Kommissionierung zu beschleunigen.');
 define('BX_MPI_SKU_FORMAT_TITLE', 'SKU-Format');
 define('BX_MPI_SKU_FORMAT_DESC', 'Numerisch: <code style="background: #f0f0f0; padding: 2px 4px; border-radius: 2px;">SKU-[PID]-[Attr1]-[Attr2]</code><br><small style="color: #666;">Konstante Länge, Scanner-kompatibel</small>');
 define('BX_MPI_POOL_STATUS_TITLE', '📊 Pool-Status');
 define('BX_MPI_AVAILABLE_EANS', 'verfügbare EANs');
 define('BX_MPI_AVAILABLE_LABEL', 'Verfügbar');
 define('BX_MPI_TOTAL_LABEL', 'Gesamt:');
 define('BX_MPI_USED_LABEL', 'Benutzt:');
 define('BX_MPI_LOW_STOCK_WARNING', '⚠️ Warnung');
 define('BX_MPI_LOW_STOCK_TEXT', 'Weniger als 100 EANs verfügbar!');
 define('BX_MPI_NO_BLOCKS_IMPORTED', 'Noch keine Blöcke importiert');
 define('BX_MPI_GS1_INFO_TITLE', '💡 GS1 Info');
 define('BX_MPI_BUY_EAN_BLOCKS', 'EAN-Blöcke kaufen:');
 define('BX_MPI_GS1_PRICES', 'Preise (ca.):');
 define('BX_MPI_GS1_PRICE_10', '• 10 EANs: ~40€');
 define('BX_MPI_GS1_PRICE_100', '• 100 EANs: ~150€');
 define('BX_MPI_GS1_PRICE_1000', '• 1.000 EANs: ~300€');
 define('BX_MPI_GS1_PRICE_NOTE', 'Preise Stand 2024, zzgl. jährlicher Grundgebühr');
 define('BX_MPI_CONFIG_HISTORY_DISABLED_TITLE', 'Historie deaktiviert');
 define('BX_MPI_CONFIG_HISTORY_DISABLED_DESC', 'Änderungen werden nicht protokolliert');
 define('BX_MPI_CONFIG_AUTO_CREATE_DISABLED_TITLE', 'Auto-Create deaktiviert');
 define('BX_MPI_CONFIG_AUTO_CREATE_DISABLED_DESC', 'SKUs müssen manuell erstellt werden');
 define('BX_MPI_DASHBOARD_TOTAL_IDENTIFIERS', 'Gesamt Identifiers');
 define('BX_MPI_DASHBOARD_WITH_EAN', 'Mit EAN');
 define('BX_MPI_DASHBOARD_WITHOUT_EAN', 'Ohne EAN');
 define('BX_MPI_DASHBOARD_PSEUDO_EAN', 'Pseudo-EAN (Prefix 2)');
 define('BX_MPI_DASHBOARD_GS1_EAN', 'GS1-EAN');
 define('BX_MPI_LATEST_ACTIVITIES_TITLE', 'Letzte Aktivitäten');
 define('BX_MPI_ACTIVITIES_TABLE_TIME', 'Zeitpunkt');
 define('BX_MPI_ACTIVITIES_TABLE_SKU', 'SKU');
 define('BX_MPI_ACTIVITIES_TABLE_PRODUCT_ID', 'Produkt-ID');
 define('BX_MPI_ACTIVITIES_TABLE_FIELD', 'Feld');
 define('BX_MPI_ACTIVITIES_TABLE_OLD_VALUE', 'Alter Wert');
 define('BX_MPI_ACTIVITIES_TABLE_NEW_VALUE', 'Neuer Wert');
 define('BX_MPI_ACTIVITIES_TABLE_REASON', 'Grund');
 define('BX_MPI_HISTORY_DISABLED_OR_EMPTY', 'Historie ist deaktiviert oder keine Aktivitäten vorhanden.');
 define('BX_MPI_ADMIN_SEARCH_FILTER', 'Suchfilter');
 define('BX_MPI_ADMIN_PRODUCT_ID_SEARCH', 'Produkt-ID:');
 define('BX_MPI_ADMIN_SKU_SEARCH', 'SKU:');
 define('BX_MPI_ADMIN_EAN_SEARCH', 'EAN:');
 define('BX_MPI_ADMIN_SEARCH_BUTTON', 'Suchen');
 define('BX_MPI_ADMIN_RESET_FILTER', 'Zurücksetzen');
 define('BX_MPI_ADMIN_RESULTS_TITLE', 'SKU/EAN-Verwaltung');
 define('BX_MPI_ADMIN_ENTRIES_FOUND', '%d Einträge gefunden');
 define('BX_MPI_ADMIN_NO_ENTRIES', 'Keine Einträge gefunden.');
 define('BX_MPI_ADMIN_TABLE_ID', 'ID');
 define('BX_MPI_ADMIN_TABLE_PRODUCT_ID', 'Produkt-ID');
 define('BX_MPI_ADMIN_TABLE_PRODUCT_NAME', 'Produktname');
 define('BX_MPI_ADMIN_TABLE_SKU', 'SKU');
 define('BX_MPI_ADMIN_TABLE_EAN', 'EAN');
 define('BX_MPI_ADMIN_TABLE_WWS_NR', 'WWS-Nr');
 define('BX_MPI_ADMIN_TABLE_WAREHOUSE', 'Lagerplatz');
 define('BX_MPI_ADMIN_TABLE_ACTIONS', 'Aktionen');
 define('BX_MPI_ADMIN_EDIT_BUTTON', 'Bearbeiten');
 define('BX_MPI_ADMIN_DELETE_BUTTON', '🗑 Löschen');
 define('BX_MPI_ADMIN_PRODUCT_INFO_HEADER', 'Produkt-Info (Read-only)');
 define('BX_MPI_ADMIN_PRODUCT_ID_LABEL', 'Produkt-ID:');
 define('BX_MPI_ADMIN_PRODUCT_NAME_LABEL', 'Produktname:');
 define('BX_MPI_ADMIN_SKU_READONLY_LABEL', 'SKU (Read-only):');
 define('BX_MPI_ADMIN_ATTRIBUTES_LABEL', 'Attribute:');
 define('BX_MPI_ADMIN_EAN_LABEL', 'EAN / GTIN:');
 define('BX_MPI_ADMIN_EAN_HELP', '13-stellige EAN oder 14-stellige GTIN');
 define('BX_MPI_ADMIN_WWS_ARTICLE_NR_LABEL', 'WWS-Artikelnummer:');
 define('BX_MPI_ADMIN_WWS_ARTICLE_NR_HELP', 'Artikelnummer aus Warenwirtschaft');
 define('BX_MPI_ADMIN_WWS_SYSTEM_LABEL', 'WWS-System:');
 define('BX_MPI_ADMIN_WWS_SYSTEM_HELP', 'Name des Warenwirtschaftssystems');
 define('BX_MPI_ADMIN_WAREHOUSE_LABEL', 'Lagerplatz:');
 define('BX_MPI_ADMIN_WAREHOUSE_HELP', 'Physischer Lagerort');
 define('BX_MPI_ADMIN_SAVE_BUTTON', '✓ Speichern');
 define('BX_MPI_POOL_DETAILS_HEADER', '📦 Block-Details:');
 define('BX_MPI_POOL_BACK_OVERVIEW', '← Zurück zur Übersicht');
 define('BX_MPI_POOL_BLOCK_SIZE', 'Block-Größe');
 define('BX_MPI_POOL_PURCHASED_AT', 'Gekauft am');
 define('BX_MPI_POOL_IMPORTED_AT', 'Importiert am');
 define('BX_MPI_POOL_AVAILABLE', 'Verfügbar');
 define('BX_MPI_POOL_USED', 'Benutzt');
 define('BX_MPI_POOL_USAGE', 'Auslastung');
 define('BX_MPI_POOL_NOTES_TITLE', '📝 Notizen:');
 define('BX_MPI_POOL_FILTER_STATUS', 'Status filtern:');
 define('BX_MPI_POOL_ALL_STATUS', 'Alle Status');
 define('BX_MPI_POOL_STATUS_ASSIGNED', 'Zugewiesen');
 define('BX_MPI_POOL_STATUS_RESERVED', 'Reserviert');
 define('BX_MPI_POOL_EAN_SEARCH', 'EAN suchen:');
 define('BX_MPI_POOL_EAN_SEARCH_PLACEHOLDER', 'EAN-13 oder GTIN-14');
 define('BX_MPI_POOL_FILTER_BUTTON', 'Filtern');
 define('BX_MPI_POOL_EAN_LIST_TITLE', 'EAN-Liste');
 define('BX_MPI_POOL_EAN_FOUND', 'EAN%s gefunden');
 define('BX_MPI_POOL_PAGE_OF', 'Seite %d von %d');
 define('BX_MPI_POOL_TABLE_ID', 'ID');
 define('BX_MPI_POOL_TABLE_EAN', 'EAN / GTIN');
 define('BX_MPI_POOL_TABLE_STATUS', 'Status');
 define('BX_MPI_POOL_TABLE_ASSIGNED_TO', 'Zugewiesen an');
 define('BX_MPI_POOL_TABLE_ASSIGNED_AT', 'Zugewiesen am');
 define('BX_MPI_POOL_STATUS_LABEL_ASSIGNED', '⊗ Zugewiesen');
 define('BX_MPI_POOL_STATUS_LABEL_RESERVED', '⊙ Reserviert');
 define('BX_MPI_POOL_NO_EANS_FOUND', 'Keine EANs gefunden.');
 define('BX_MPI_POOL_PREV_PAGE', '« Vorherige');
 define('BX_MPI_POOL_OVERVIEW_TITLE', 'Gesamtübersicht');
 define('BX_MPI_POOL_TOTAL_EANS', 'Gesamt EANs');
 define('BX_MPI_POOL_TOTAL_BLOCKS', 'Blöcke');
 define('BX_MPI_POOL_LOW_STOCK_WARNING', '⚠️ Warnung: Niedriger EAN-Bestand');
 define('BX_MPI_POOL_IMPORT_BUTTON', '➕ Neuen Block importieren');
 define('BX_MPI_POOL_BLOCKS_TITLE', '📦 Importierte EAN-Blöcke');
 define('BX_MPI_POOL_BLOCK_NR', 'Block-Nr');
 define('BX_MPI_POOL_BLOCK_SIZE_LABEL', 'Größe');
 define('BX_MPI_POOL_BLOCK_PURCHASED', 'Gekauft');
 define('BX_MPI_POOL_BLOCK_IMPORTED', 'Importiert');
 define('BX_MPI_POOL_BLOCK_TOTAL', 'Gesamt');
 define('BX_MPI_POOL_BLOCK_FREE', 'Frei');
 define('BX_MPI_POOL_BLOCK_USED', 'Benutzt');
 define('BX_MPI_POOL_BLOCK_USAGE', 'Auslastung');
 define('BX_MPI_POOL_BLOCK_STATUS', 'Status');
 define('BX_MPI_POOL_BLOCK_ACTIONS', 'Aktionen');
 define('BX_MPI_POOL_BLOCK_ACTIVE', '🟢 Aktiv');
 define('BX_MPI_POOL_BLOCK_DEPLETED_LABEL', '🔴 Erschöpft');
 define('BX_MPI_POOL_BLOCK_ARCHIVED', '⚫ Archiviert');
 define('BX_MPI_POOL_BLOCK_DETAILS_BUTTON', 'Details');
 define('BX_MPI_POOL_BLOCK_DELETE_BUTTON', 'Löschen');
 define('BX_MPI_POOL_IMPORT_FIRST_BLOCK', '➕ Ersten Block importieren');
 define('BX_MPI_POOL_NO_BLOCKS', 'Noch keine EAN-Blöcke importiert');
 define('BX_MPI_POOL_NO_BLOCKS_TEXT', 'Importieren Sie Ihre von GS1 Germany gekauften EAN-Blöcke als CSV-Datei.');
 define('BX_MPI_POOL_IMPORT_MODAL_TITLE', '📥 GS1-Block importieren');
 define('BX_MPI_POOL_CSV_FILE_LABEL', 'CSV-Datei von GS1 Germany:');
 define('BX_MPI_POOL_CSV_FILE_HELP', 'Die Spalte "Gtin" wird automatisch erkannt');
 define('BX_MPI_POOL_BLOCK_NUMBER_LABEL', 'Block-Nummer:');
 define('BX_MPI_POOL_BLOCK_NUMBER_PLACEHOLDER', BX_MPI_EXAMPLE_PREFIX . 'Block-2024-001');
 define('BX_MPI_POOL_BLOCK_NUMBER_HELP', 'Eindeutige Bezeichnung für diesen Block');
 define('BX_MPI_POOL_BLOCK_SIZE_LABEL_SELECT', 'Block-Größe:');
 define('BX_MPI_POOL_PURCHASE_DATE_LABEL', 'Kaufdatum:');
 define('BX_MPI_POOL_NOTES_LABEL', 'Notizen (optional):');
 define('BX_MPI_CSV_PLACEHOLDER', 'Besonderheiten, Rabatte, Besitzer etc.');
 define('BX_MPI_POOL_IMPORT_INFO_TITLE', 'ℹ️ Hinweis:');
 define('BX_MPI_POOL_IMPORT_INFO_TEXT', 'Die CSV-Datei können Sie nach dem Kauf direkt von GS1 Germany herunterladen. Pro 10er-Block: ca. 40€ | 100er-Block: ca. 150€ | 1000er-Block: ca. 300€ (Stand 2024)');
 define('BX_MPI_POOL_IMPORT_CANCEL_BUTTON', 'Abbrechen');
 define('BX_MPI_POOL_IMPORT_SUBMIT_BUTTON', '✓ Importieren');
 
 // History Tab
 define('BX_MPI_HISTORY_FILTER_PRODUCT_ID_LABEL', 'Produkt-ID:');
 define('BX_MPI_HISTORY_FILTER_PRODUCT_ID_PLACEHOLDER', BX_MPI_EXAMPLE_PREFIX . '143');
 define('BX_MPI_HISTORY_FILTER_FIELD_LABEL', 'Feld:');
 define('BX_MPI_HISTORY_FILTER_FIELD_ALL', 'Alle Felder');
 define('BX_MPI_HISTORY_FILTER_FIELD_SKU', 'SKU');
 define('BX_MPI_HISTORY_FILTER_FIELD_EAN', 'EAN');
 define('BX_MPI_HISTORY_FILTER_FIELD_WWS', 'WWS-Nr');
 define('BX_MPI_HISTORY_FILTER_FIELD_WAREHOUSE', 'Lagerplatz');
 define('BX_MPI_HISTORY_FILTER_TIMEFRAME_LABEL', 'Zeitraum:');
 define('BX_MPI_HISTORY_FILTER_TIMEFRAME_ALL', 'Gesamter Zeitraum');
 define('BX_MPI_HISTORY_FILTER_TIMEFRAME_TODAY', 'Heute');
 define('BX_MPI_HISTORY_FILTER_TIMEFRAME_WEEK', 'Letzte 7 Tage');
 define('BX_MPI_HISTORY_FILTER_TIMEFRAME_MONTH', 'Letzter Monat');
 define('BX_MPI_HISTORY_FILTER_BUTTON', '🔍 Filtern');
 define('BX_MPI_HISTORY_RECORDS_FOUND', '📊 <strong>%d</strong> Einträge gefunden');
 define('BX_MPI_HISTORY_PAGE_INFO', 'Seite <strong>%d</strong> von <strong>%d</strong>');
 define('BX_MPI_HISTORY_TABLE_TIMESTAMP', 'Zeitstempel');
 define('BX_MPI_HISTORY_TABLE_SKU', 'SKU');
 define('BX_MPI_HISTORY_TABLE_PRODUCT_ID', 'Produkt-ID');
 define('BX_MPI_HISTORY_TABLE_FIELD', 'Feld');
 define('BX_MPI_HISTORY_TABLE_OLD_VALUE', 'Alt');
 define('BX_MPI_HISTORY_TABLE_NEW_VALUE', 'Neu');
 define('BX_MPI_HISTORY_TABLE_REASON', 'Grund');
 define('BX_MPI_HISTORY_PREV_PAGE', '« Vorherige');
 define('BX_MPI_HISTORY_NEXT_PAGE', 'Nächste »');
 define('BX_MPI_HISTORY_NO_ENTRIES', 'Keine Einträge gefunden');
 define('BX_MPI_HISTORY_NO_ENTRIES_FILTERED', 'Versuchen Sie andere Filterkriterien.');
 define('BX_MPI_HISTORY_NO_ENTRIES_DEFAULT', 'Es wurden noch keine Änderungen protokolliert.');
 
 // Disabled History Notice
 define('BX_MPI_HISTORY_DISABLED_TITLE', 'Historie-Funktion ist deaktiviert');
 define('BX_MPI_HISTORY_DISABLED_DESC', 'Um die Änderungshistorie zu nutzen, aktivieren Sie diese in den Einstellungen.');
 define('BX_MPI_HISTORY_SETTINGS_LINK', '⚙️ Zu den Einstellungen');
 
 // Settings Tab - Statistics
 define('BX_MPI_SETTINGS_STATISTICS_TITLE', '📊 Modul-Statistiken');
 
 // Sidebar Box - Configuration
 define('BX_MPI_SIDEBAR_CONFIG_TITLE', '⚙️ Aktuelle Konfiguration');
 define('BX_MPI_SIDEBAR_MODULE_STATUS', 'Modul-Status');
 define('BX_MPI_SIDEBAR_AUTO_CREATE', 'Auto-Create');
 define('BX_MPI_SIDEBAR_EAN_MODE', 'EAN-Modus');
 define('BX_MPI_SIDEBAR_ACTIVE', '✓ AKTIV');
 define('BX_MPI_SIDEBAR_INACTIVE', '✘ INAKTIV');
 define('BX_MPI_SIDEBAR_ENABLED', '✓ EIN');
 define('BX_MPI_SIDEBAR_DISABLED', '✘ AUS');
 
 // Sidebar Box - Statistics
 define('BX_MPI_SIDEBAR_STATS_DISPLAYED', 'Angezeigt');
 define('BX_MPI_SIDEBAR_STATS_WITH_EAN', 'Mit EAN');
 define('BX_MPI_SIDEBAR_STATS_WITHOUT_EAN', 'Ohne EAN');
 define('BX_MPI_SIDEBAR_STATS_PSEUDO', '🏷️ Pseudo');
 define('BX_MPI_SIDEBAR_STATS_GS1', '✅ GS1');
 define('BX_MPI_SIDEBAR_STATS_WITHOUT_WAREHOUSE', '📦 Ohne Lagerplatz');
 define('BX_MPI_SIDEBAR_STATS_NO_DATA', 'Keine Daten');
// Sidebar Box - Filters
define('BX_MPI_SIDEBAR_FILTER_NO_EAN', '🔍 Ohne EAN');
define('BX_MPI_SIDEBAR_FILTER_PSEUDO_EAN', '🏷️ Nur Pseudo-EAN');
define('BX_MPI_SIDEBAR_FILTER_GS1_EAN', '✅ Nur GS1-EAN');
define('BX_MPI_SIDEBAR_FILTER_NO_WAREHOUSE', '📦 Ohne Lagerplatz');
define('BX_MPI_SIDEBAR_FILTER_RESET', '↺ Zurücksetzen');
define('BX_MPI_SIDEBAR_TIP_TITLE', '💡 Tipp');

// History Sidebar - Statistics & Actions
define('BX_MPI_HISTORY_SIDEBAR_STATS_TITLE', 'Statistiken');
define('BX_MPI_HISTORY_SIDEBAR_CHANGES_TODAY', 'Änderungen heute');
define('BX_MPI_HISTORY_SIDEBAR_LAST_7_DAYS', 'Letzte 7 Tage');
define('BX_MPI_HISTORY_SIDEBAR_LAST_ACTIVITIES', 'Letzte Aktivitäten');
define('BX_MPI_HISTORY_SIDEBAR_TOP_FIELDS', 'Top Felder');
define('BX_MPI_HISTORY_SIDEBAR_QUICK_ACTIONS', 'Quick Actions');
define('BX_MPI_HISTORY_SIDEBAR_TODAY', 'Heute');
define('BX_MPI_HISTORY_SIDEBAR_THIS_WEEK', 'Diese Woche');
define('BX_MPI_HISTORY_SIDEBAR_ONLY_EAN_CHANGES', 'Nur EAN-Änderungen');
define('BX_MPI_HISTORY_SIDEBAR_SHOW_ALL', 'Alle anzeigen');
define('BX_MPI_HISTORY_SIDEBAR_DISABLED_TITLE', 'Historie deaktiviert');
define('BX_MPI_HISTORY_SIDEBAR_DISABLED_TEXT', 'Die Historie-Funktion ist deaktiviert.');

// Settings Sidebar - Setup Check
define('BX_MPI_SETUP_CHECK_TITLE', 'Setup-Check');
define('BX_MPI_SETUP_CHECK_ALL_OPTIMAL', 'Alles optimal!');
define('BX_MPI_SETUP_CHECK_CONFIG_OK', 'Ihre Konfiguration ist in Ordnung');
define('BX_MPI_SETUP_CHECK_GS1_MISSING', 'GS1-Präfix fehlt');
define('BX_MPI_SETUP_CHECK_GS1_MISSING_DESC', 'Auto GS1-EAN aktiviert, aber kein Präfix eingetragen');
define('BX_MPI_SETUP_CHECK_LEGACY_SKU', 'Legacy SKU-Modus');
define('BX_MPI_SETUP_CHECK_LEGACY_SKU_DESC', 'Model-basiert kann zu variablen Längen führen');
define('BX_MPI_SETUP_CHECK_LONG_SEPARATOR', 'Langer Separator');
define('BX_MPI_SETUP_CHECK_LONG_SEPARATOR_DESC', 'verlängert SKUs');

// Settings Sidebar - Configuration Tips
define('BX_MPI_CONFIG_TIP_TITLE', 'Konfigurations-Tipp');
define('BX_MPI_CONFIG_TIP_RECOMMENDED_ACTION', 'Empfohlene Aktion:');
define('BX_MPI_CONFIG_TIP_GS1_ACTION1', 'Tragen Sie Ihren GS1-Präfix ein, ODER');
define('BX_MPI_CONFIG_TIP_GS1_ACTION2', 'Wechseln Sie zu "Pseudo-EAN" (Prefix 2)');
define('BX_MPI_CONFIG_TIP_HISTORY_ACTION1', 'Aktivieren Sie die Historie für Audit-Trail');
define('BX_MPI_CONFIG_TIP_HISTORY_ACTION2', 'Besonders wichtig bei Team-Nutzung');
define('BX_MPI_CONFIG_TIP_MANUAL_MODE', 'Manueller EAN-Modus aktiv');
define('BX_MPI_CONFIG_TIP_MANUAL_DESC', 'Sie haben volle Kontrolle über EAN-Nummern. Beachten Sie: Manuelle Eingabe erfordert mehr Aufwand bei vielen Produkten.');
define('BX_MPI_CONFIG_TIP_PSEUDO_MODE', 'Pseudo-EAN (Prefix 2)');
define('BX_MPI_CONFIG_TIP_PSEUDO_DESC_GOOD', 'Perfekt für interne Nutzung und Lager');
define('BX_MPI_CONFIG_TIP_PSEUDO_DESC_WARNING', 'NICHT für Marktplätze wie Amazon/eBay geeignet!');
define('BX_MPI_CONFIG_TIP_GS1_MODE', 'GS1-EAN aktiv');
define('BX_MPI_CONFIG_TIP_GS1_DESC1', 'Handelbar auf allen Marktplätzen');
define('BX_MPI_CONFIG_TIP_GS1_DESC2', 'International anerkannter Standard');
define('BX_MPI_CONFIG_TIP_GS1_DESC3', 'Erfordert GS1-Mitgliedschaft');
define('BX_MPI_CONFIG_TIP_PERFORMANCE', 'Performance-Tipp:');
define('BX_MPI_CONFIG_TIP_PERFORMANCE_DESC', 'Identifiern empfehlen wir die Historie-Funktion für besseres Tracking und Fehlersuche.');

// Settings Sidebar - Help & Tools
define('BX_MPI_HELP_TOOLS_TITLE', 'Hilfe & Tools');
define('BX_MPI_HELP_CONFIG_TEMPLATES', 'Konfigurations-Vorlagen:');
define('BX_MPI_HELP_TEMPLATE_STANDARD', 'Standard:');
define('BX_MPI_HELP_TEMPLATE_STANDARD_DESC', 'Numerische SKU + Pseudo-EAN');
define('BX_MPI_HELP_TEMPLATE_WAREHOUSE', 'Warehouse:');
define('BX_MPI_HELP_TEMPLATE_WAREHOUSE_DESC', 'Mit Lagerplatz-Integration');
define('BX_MPI_HELP_TEMPLATE_MARKETPLACE', 'Marktplatz:');
define('BX_MPI_HELP_TEMPLATE_MARKETPLACE_DESC', 'GS1-EAN + WWS-Nummer');
define('BX_MPI_HELP_TEMPLATE_MINIMAL', 'Minimal:');
define('BX_MPI_HELP_TEMPLATE_MINIMAL_DESC', 'Nur SKU, keine EAN');
define('BX_MPI_HELP_FAQ', 'Häufige Fragen (FAQ):');
define('BX_MPI_HELP_FAQ_Q1', 'Pseudo-EAN vs. GS1-EAN?');
define('BX_MPI_HELP_FAQ_A1_PSEUDO', 'Pseudo-EAN (Prefix 2):');
define('BX_MPI_HELP_FAQ_A1_PSEUDO_DESC', 'Für interne Nutzung, Lager & Scanner. NICHT für Marktplätze (Amazon/eBay).');
define('BX_MPI_HELP_FAQ_A1_GS1', 'GS1-EAN:');
define('BX_MPI_HELP_FAQ_A1_GS1_DESC', 'Offiziell registriert, handelbar, erfordert GS1-Mitgliedschaft (ca. 100-300€/Jahr).');
define('BX_MPI_HELP_FAQ_Q2', 'Wann Historie aktivieren?');
define('BX_MPI_HELP_FAQ_A2', 'Empfohlen bei: Team-Nutzung, Audit-Anforderungen, häufigen Änderungen. Protokolliert alle Änderungen mit Zeitstempel & Grund.');
define('BX_MPI_HELP_FAQ_Q3', 'SKU-Format ändern?');
define('BX_MPI_HELP_FAQ_A3_WARNING', 'Achtung:');
define('BX_MPI_HELP_FAQ_A3', 'Änderungen betreffen nur NEU erstellte SKUs! Bestehende SKUs bleiben unverändert. Backup empfohlen.');
define('BX_MPI_HELP_FAQ_Q4', 'EAN nachträglich generieren?');
define('BX_MPI_HELP_FAQ_A4', 'Ja! Aktivieren Sie Auto-EAN (Pseudo oder GS1). Bei nächstem Zugriff wird fehlende EAN automatisch erstellt.');
define('BX_MPI_HELP_BACKUP_TIP', 'Tipp:');
define('BX_MPI_HELP_BACKUP_TIP_DESC', 'Erstellen Sie ein Backup vor größeren Änderungen!');