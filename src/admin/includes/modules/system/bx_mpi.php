<?php
/**
 * ██████╗  ███████╗ ███╗   ██╗  █████╗  ██╗  ██╗
 * ██╔══██╗ ██╔════╝ ████╗  ██║ ██╔══██╗ ╚██╗██╔╝
 * ██████╔╝ █████╗   ██╔██╗ ██║ ███████║  ╚███╔╝
 * ██╔══██╗ ██╔══╝   ██║╚██╗██║ ██╔══██║  ██╔██╗
 * ██████╔╝ ███████╗ ██║ ╚████║ ██║  ██║ ██╔╝ ██╗
 * ╚═════╝  ╚══════╝ ╚═╝  ╚═══╝ ╚═╝  ╚═╝ ╚═╝  ╚═╝
 * 
 * BX Modified Product Identifier (MPI) - System Module
 * 
 * Zentrale Verwaltung eindeutiger Produktidentifikatoren (SKU/EAN/GTIN)
 * für modified eCommerce Shopsoftware. Ermöglicht die eindeutige Identifikation
 * von Produkten mit Attributen für Warenwirtschafts-Integration.
 * 
 * Kern-Features:
 * • Eindeutige SKU-Generierung für Produkt-Varianten (numerisches Format)
 * • EAN/GTIN/UPC/ISBN-Verwaltung pro Variante
 * • EAN-Pool-Management für GS1-Blöcke (CSV-Import)
 * • Warenwirtschafts-Nummern-Mapping (JTL, SAP, Lexware, etc.)
 * • Lagerplatz-Verwaltung und Bestandsführung
 * • Änderungs-Historie (optional)
 * • API für Drittmodule (RMA, Versand, Export)
 * 
 * Technische Details:
 * • Gemeinsame Tabelle bx_product_variants mit BX Stockmanager Pro
 * • UNIQUE KEY (products_id, attributes_hash) verhindert Duplikate
 * • ON DUPLICATE KEY UPDATE für idempotente SKU/EAN-Zuweisung
 * • Foreign Keys mit CASCADE/SET NULL für Datenkonsistenz
 * • MySQL Trigger für automatische Block-Statistik-Aktualisierung
 * 
 * Konsistenz-Regeln:
 * • Produkte mit Attributen: products_ean in products-Tabelle MUSS NULL sein
 * • Einfachprodukte (ohne Attribute): products_ean wird in beide Tabellen geschrieben
 * • ProductIdentifier::createSKU() erzwingt diese Regeln automatisch
 * 
 * @package    BX Modified Product Identifier
 * @subpackage System Module
 * @category   Product Management
 * @author     Axel Benkert
 * @version    1.0.0
 * @since      1.0.0
 * @date       2026-01-06
 * @updated    2026-01-06
 * @copyright  2025-2026 Axel Benkert
 * @license    GNU General Public License v2.0
 * 
 * @see ProductIdentifier Class for API usage
 * @see BX_STOCKMANAGER_MPI_INTEGRATION.md for Stockmanager integration
 * @see BX_MPI_DEVELOPER_GUIDE.md for development documentation
 */

defined('_VALID_XTC') or die('Direct Access to this location is not allowed.');

class bx_mpi {
  var $code, $title, $description, $enabled, $version, $sort_order, $_check;

  function __construct() {
    $this->version     = '1.0.0';
    $this->code        = 'bx_mpi';
    $this->title       = MODULE_BX_MPI_TEXT_TITLE;
    $this->description = MODULE_BX_MPI_TEXT_DESCRIPTION . '<p><strong>Version: ' . $this->version . '</strong></p>';
    $this->sort_order  = defined('MODULE_BX_MPI_SORT_ORDER') ? MODULE_BX_MPI_SORT_ORDER : 0;
    $this->enabled     = defined('MODULE_BX_MPI_STATUS') ? ((MODULE_BX_MPI_STATUS == 'true') ? true : false) : 0;
  }

  function process($file) {
    return true;
  }

  function display() {
    return array('text' => '<div style="text-align: center;">' . xtc_button(BUTTON_SAVE) . xtc_button_link(BUTTON_CANCEL, xtc_href_link(FILENAME_MODULE_EXPORT, 'set=' . $_GET['set'] . '&module=' . $this->code)) . "</div>");
  }

  function check() {
    if (!isset($this->_check)) {
      $check_query = xtc_db_query("SELECT configuration_value 
                                     FROM " . TABLE_CONFIGURATION . "
                                    WHERE configuration_key = 'MODULE_BX_MPI_STATUS'");
      $this->_check = xtc_db_num_rows($check_query);
    }
    return $this->_check;
  }

  function install() {
    // Admin-Access hinzufügen
    xtc_db_query("ALTER TABLE " . TABLE_ADMIN_ACCESS . " ADD bx_mpi INTEGER(1)");
    xtc_db_query("UPDATE " . TABLE_ADMIN_ACCESS . " SET bx_mpi = 1");

    // Freie Konfigurations-Gruppe-ID finden
    $freeId_query = xtc_db_query("SELECT (configuration_group_id+1) AS id 
                                    FROM " . TABLE_CONFIGURATION_GROUP . " 
                                   WHERE (configuration_group_id+1) NOT IN (SELECT configuration_group_id FROM " . TABLE_CONFIGURATION_GROUP . " WHERE configuration_group_id IS NOT NULL) 
                                   LIMIT 1");
    $freeId = xtc_db_fetch_array($freeId_query);

    // Freie Sort-Order finden
    $freeSort_query = xtc_db_query("SELECT (sort_order+1) AS sort_order 
                                      FROM " . TABLE_CONFIGURATION_GROUP . " 
                                     WHERE (sort_order+1) NOT IN (SELECT sort_order FROM " . TABLE_CONFIGURATION_GROUP . " WHERE sort_order IS NOT NULL) 
                                     LIMIT 1");
    $freeSort = xtc_db_fetch_array($freeSort_query);

    // Konfigurations-Gruppe erstellen
    xtc_db_query("INSERT INTO " . TABLE_CONFIGURATION_GROUP . " (
                    configuration_group_id,
                    configuration_group_title, 
                    configuration_group_description, 
                    sort_order, 
                    visible
                  ) VALUES (
                    " . $freeId["id"] . ", 
                    'BX Product Identifier (MPI)', 
                    'Einstellungen für das BX Modified Product Identifier Modul', 
                    " . $freeSort["sort_order"] . ", 
                    1
                  )");

    // Konfigurationen erstellen
    $group_id = $freeId["id"];

    // 1. Status
    xtc_db_query("INSERT INTO " . TABLE_CONFIGURATION . " (
                    configuration_id,
                    configuration_key, 
                    configuration_value, 
                    configuration_group_id, 
                    sort_order, 
                    date_added, 
                    use_function, 
                    set_function
                  ) VALUES (
                    '', 
                    'MODULE_BX_MPI_STATUS',
                    'true', 
                    '" . $group_id . "', 
                    '1', 
                    NOW(), 
                    '', 
                    'xtc_cfg_select_option(array(\'true\', \'false\'), '
                  )");

    // 2. Sort Order
    xtc_db_query("INSERT INTO " . TABLE_CONFIGURATION . " (
                    configuration_id, 
                    configuration_key, 
                    configuration_value, 
                    configuration_group_id, 
                    sort_order, 
                    date_added, 
                    use_function, 
                    set_function
                  ) VALUES (
                    '', 
                    'MODULE_BX_MPI_SORT_ORDER',
                    '2', 
                    '" . $group_id . "', 
                    '2', 
                    NOW(), 
                    '', 
                    ''
                  )");

    // 3. Config Group ID (versteckt)
    xtc_db_query("INSERT INTO " . TABLE_CONFIGURATION . " (
                    configuration_id, 
                    configuration_key, 
                    configuration_value, 
                    configuration_group_id, 
                    sort_order, 
                    date_added, 
                    use_function, 
                    set_function
                  ) VALUES (
                    '', 
                    'MODULE_BX_MPI_CONFIG_ID',
                    '" . $group_id . "', 
                    '" . $group_id . "', 
                    '3', 
                    NOW(), 
                    '', 
                    'xtc_convert_value('
                  )");

    // 3. Config Group ID (versteckt)
    xtc_db_query("INSERT INTO " . TABLE_CONFIGURATION . " (
                    configuration_id, 
                    configuration_key, 
                    configuration_value, 
                    configuration_group_id, 
                    sort_order, 
                    date_added, 
                    use_function, 
                    set_function
                  ) VALUES (
                    '', 
                    'MAX_DISPLAY_MPI_RESULTS',
                    '20', 
                    '" . $group_id . "',
                    '4', 
                    NOW(), 
                    '', 
                    ''
                  )");

    // 4. Auto-Create SKU
    xtc_db_query("INSERT INTO " . TABLE_CONFIGURATION . " (
                    configuration_id, 
                    configuration_key, 
                    configuration_value, 
                    configuration_group_id, 
                    sort_order, 
                    date_added, 
                    use_function, 
                    set_function
                  ) VALUES (
                    '', 
                    'MODULE_BX_MPI_AUTO_CREATE',
                    'true', 
                    '" . $group_id . "', 
                    '5', 
                    NOW(), 
                    '', 
                    'xtc_cfg_select_option(array(\'true\', \'false\'), '
                  )");

    // 5. SKU Prefix
    // Format: PREFIX + PID + "_" + OID + "-" + VID + "x" + ...
    // Separatoren sind hart-codiert für maximale Konsistenz
    xtc_db_query("INSERT INTO " . TABLE_CONFIGURATION . " (
                    configuration_id, 
                    configuration_key, 
                    configuration_value, 
                    configuration_group_id, 
                    sort_order, 
                    date_added, 
                    use_function, 
                    set_function
                  ) VALUES (
                    '', 
                    'MODULE_BX_MPI_SKU_PREFIX',
                    'SKU', 
                    '" . $group_id . "', 
                    '8', 
                    NOW(), 
                    '', 
                    ''
                  )");

    // 6. Enable History
    xtc_db_query("INSERT INTO " . TABLE_CONFIGURATION . " (
                    configuration_id, 
                    configuration_key, 
                    configuration_value, 
                    configuration_group_id, 
                    sort_order, 
                    date_added, 
                    use_function, 
                    set_function
                  ) VALUES (
                    '', 
                    'MODULE_BX_MPI_ENABLE_HISTORY',
                    'false', 
                    '" . $group_id . "', 
                    '7', 
                    NOW(), 
                    '', 
                    'xtc_cfg_select_option(array(\'true\', \'false\'), '
                  )");

    // 7. EAN Mode
    xtc_db_query("INSERT INTO " . TABLE_CONFIGURATION . " (
                    configuration_id, 
                    configuration_key, 
                    configuration_value, 
                    configuration_group_id, 
                    sort_order, 
                    date_added, 
                    use_function, 
                    set_function
                  ) VALUES (
                    '', 
                    'MODULE_BX_MPI_EAN_MODE',
                    'manual', 
                    '" . $group_id . "', 
                    '8', 
                    NOW(), 
                    '', 
                    'xtc_cfg_select_option(array(\'manual\', \'auto_pseudo\', \'auto_gs1\'), '
                  )");

    // 8. GS1 Prefix
    xtc_db_query("INSERT INTO " . TABLE_CONFIGURATION . " (
                    configuration_id, 
                    configuration_key, 
                    configuration_value, 
                    configuration_group_id, 
                    sort_order, 
                    date_added, 
                    use_function, 
                    set_function
                  ) VALUES (
                    '', 
                    'MODULE_BX_MPI_GS1_PREFIX',
                    '', 
                    '" . $group_id . "', 
                    '9',
                    NOW(), 
                    '', 
                    ''
                  )");

    // 3. Config Group ID (versteckt)
    xtc_db_query("INSERT INTO " . TABLE_CONFIGURATION . " (
                    configuration_id, 
                    configuration_key, 
                    configuration_value, 
                    configuration_group_id, 
                    sort_order, 
                    date_added, 
                    use_function, 
                    set_function
                  ) VALUES (
                    '', 
                    'MAX_DISPLAY_MPI_RESULTS',
                    '20', 
                    '" . $group_id . "',
                    '10', 
                    NOW(), 
                    '', 
                    ''
                  )");


    // Datenbank-Tabellen erstellen
    $this->createTables();
    
    // Sprachdateien dynamisch erstellen
    $this->createLanguageFiles($group_id);
  }

  /**
   * Erstelle Sprachdateien für Konfigurationsseite
   * @param int $group_id Die ermittelte Konfigurations-Gruppen-ID
   */
  private function createLanguageFiles($group_id) {
    // Deutsche Sprachdatei
    $lang_de = "<?php\n";
    $lang_de .= "/**\n";
    $lang_de .= " * BX Modified Product Identifier (MPI)\n";
    $lang_de .= " * Deutsche Sprachkonstanten für System-Modul\n";
    $lang_de .= " * \n";
    $lang_de .= " * @package    BX Modified Product Identifier\n";
    $lang_de .= " * @subpackage Language\n";
    $lang_de .= " * @language   Deutsch\n";
    $lang_de .= " * @author     Axel Benkert\n";
    $lang_de .= " * @version    1.0.0\n";
    $lang_de .= " * @date       2025-01-16\n";
    $lang_de .= " * @copyright  2025 Axel Benkert\n";
    $lang_de .= " * @license    GNU General Public License v2.0\n";
    $lang_de .= " */\n\n";
    $lang_de .= "defined('_VALID_XTC') or die('Direct Access to this location is not allowed.');\n\n";
    $lang_de .= "// Konfigurationsoptionen\n\n";
    
    $lang_de .= "// 1. Status\n";
    $lang_de .= "define('MODULE_BX_MPI_STATUS_TITLE', 'Modul aktivieren');\n";
    $lang_de .= "define('MODULE_BX_MPI_STATUS_DESC', 'Möchten Sie das BX Product Identifier Modul aktivieren?');\n\n";
    
    $lang_de .= "// 2. Config ID (versteckt)\n";
    $lang_de .= "define('MODULE_BX_MPI_CONFIG_ID_TITLE', 'Konfigurations-Gruppen-ID');\n";
    $lang_de .= "define('MODULE_BX_MPI_CONFIG_ID_DESC', 'Interne ID der Konfigurationsgruppe (nicht ändern)');\n\n";
    
    $lang_de .= "// 3. Auto-Create\n";
    $lang_de .= "define('MODULE_BX_MPI_AUTO_CREATE_TITLE', 'Automatische SKU-Generierung');\n";
    $lang_de .= "define('MODULE_BX_MPI_AUTO_CREATE_DESC', '\n";
    $lang_de .= "  Soll automatisch eine eindeutige SKU erstellt werden, wenn ein Produkt mit Attributen bestellt wird?<br>\n";
    $lang_de .= "  <small><strong>true:</strong> SKU wird bei erster Bestellung generiert (empfohlen)<br>\n";
    $lang_de .= "  <strong>false:</strong> SKUs müssen manuell gepflegt werden</small>\n";
    $lang_de .= "');\n\n";
    
    $lang_de .= "// 4. SKU Prefix\n";
    $lang_de .= "define('MODULE_BX_MPI_SKU_PREFIX_TITLE', 'SKU-Präfix');\n";
    $lang_de .= "define('MODULE_BX_MPI_SKU_PREFIX_DESC', '\n";
    $lang_de .= "  Präfix für numerische SKUs.<br>\n";
    $lang_de .= "  <small><strong>Festes Format:</strong> PREFIX + PID(4) + \"_\" + OID(4) + \"-\" + VID(4) + \"x\" + ...<br>\n";
    $lang_de .= "  <strong>Beispiel mit \"SKU\":</strong> <code>SKU0042_0003-0045x0006-0195</code><br>\n";
    $lang_de .= "  <strong>Leer lassen:</strong> <code>0042_0003-0045x0006-0195</code><br>\n";
    $lang_de .= "  Separatoren sind fest: \"_\" nach PID, \"-\" zwischen Option/Value, \"x\" zwischen Attributen</small>\n";
    $lang_de .= "');\n\n";
    
    $lang_de .= "// 5. Enable History\n";
    $lang_de .= "define('MODULE_BX_MPI_ENABLE_HISTORY_TITLE', 'Änderungs-Historie aktivieren');\n";
    $lang_de .= "define('MODULE_BX_MPI_ENABLE_HISTORY_DESC', '\n";
    $lang_de .= "  Sollen alle Änderungen an Produktidentifikatoren protokolliert werden?<br>\n";
    $lang_de .= "  <small><strong>true:</strong> Alle Änderungen werden in Historie-Tabelle gespeichert (empfohlen für Audit)<br>\n";
    $lang_de .= "  <strong>false:</strong> Keine Protokollierung (spart Speicherplatz)</small>\n";
    $lang_de .= "');\n\n";
    
    $lang_de .= "// 6. EAN Mode\n";
    $lang_de .= "define('MODULE_BX_MPI_EAN_MODE_TITLE', 'EAN-Generierung');\n";
    $lang_de .= "define('MODULE_BX_MPI_EAN_MODE_DESC', '\n";
    $lang_de .= "  <strong>Wie sollen EAN-Codes generiert werden?</strong><br><br>\n";
    $lang_de .= "  \n";
    $lang_de .= "  <strong style=\"color: #333;\">manual:</strong> Keine automatische Generierung (Admin pflegt manuell)<br>\n";
    $lang_de .= "  <small style=\"color: #666;\">→ Beste Kontrolle, geeignet für kleine Shops oder wenn Lieferanten-EANs vorhanden</small><br><br>\n";
    $lang_de .= "  \n";
    $lang_de .= "  <strong style=\"color: #333;\">auto_pseudo:</strong> Pseudo-EAN mit Prefix \"2\" (Instore-Code)<br>\n";
    $lang_de .= "  <small style=\"color: #666;\">→ Automatisch generiert, Scanner-kompatibel, NICHT für externen Handel (Amazon/eBay)<br>\n";
    $lang_de .= "  → Ideal für interne Prozesse: Lager, RMA, Kommissionierung</small><br><br>\n";
    $lang_de .= "  \n";
    $lang_de .= "  <strong style=\"color: #333;\">auto_gs1:</strong> Echte EAN mit GS1-Präfix<br>\n";
    $lang_de .= "  <small style=\"color: #666;\">→ Handelbar auf Amazon/eBay/Kaufland, erfordert GS1-Mitgliedschaft (ca. 100-300€/Jahr)<br>\n";
    $lang_de .= "  → GS1-Präfix muss unten eingetragen werden</small><br><br>\n";
    $lang_de .= "  \n";
    $lang_de .= "  <div style=\"background: #fff3cd; padding: 10px; border-radius: 4px; margin-top: 10px;\">\n";
    $lang_de .= "    <strong>💡 Hinweis zu Multi-Attribut-Produkten:</strong><br>\n";
    $lang_de .= "    <small>Bei Produkten mit mehreren Attributen (z.B. T-Shirt Größe M + Farbe Rot) gibt es in modified keine eindeutige EAN.\n";
    $lang_de .= "    BX MPI generiert dann automatisch eine eindeutige EAN pro Variante - abhängig vom gewählten Modus.</small>\n";
    $lang_de .= "  </div>\n";
    $lang_de .= "');\n\n";
    
    $lang_de .= "// 7. GS1 Prefix\n";
    $lang_de .= "define('MODULE_BX_MPI_GS1_PREFIX_TITLE', 'GS1-Präfix');\n";
    $lang_de .= "define('MODULE_BX_MPI_GS1_PREFIX_DESC', '\n";
    $lang_de .= "  <strong>GS1-Präfix für EAN-Generierung</strong> (nur bei Modus <strong>auto_gs1</strong> erforderlich)<br><br>\n";
    $lang_de .= "  \n";
    $lang_de .= "  Geben Sie Ihren registrierten GS1-Präfix ein (7-10 Stellen).<br>\n";
    $lang_de .= "  <small><strong>Beispiel:</strong> <code>4004332</code></small><br><br>\n";
    $lang_de .= "  \n";
    $lang_de .= "  <div style=\"background: #f8d7da; padding: 10px; border-radius: 4px; border-left: 4px solid #dc3545;\">\n";
    $lang_de .= "    <strong>⚠️ Wichtig:</strong> GS1-Mitgliedschaft erforderlich!<br>\n";
    $lang_de .= "    <small>Ohne gültigen Präfix können keine handelbaren EANs generiert werden.<br>\n";
    $lang_de .= "    Kosten: ca. 100-300€/Jahr je nach Land und Kontingent.</small>\n";
    $lang_de .= "  </div>\n";
    $lang_de .= "  \n";
    $lang_de .= "  <div style=\"margin-top: 10px;\">\n";
    $lang_de .= "    <small>ℹ️ Weitere Informationen: \n";
    $lang_de .= "    <a href=\"https://www.gs1-germany.de\" target=\"_blank\" style=\"color: #0066cc;\">www.gs1-germany.de</a></small>\n";
    $lang_de .= "  </div>\n";
    $lang_de .= "');\n\n";
    
    $lang_de .= "// 8. Sort Order\n";
    $lang_de .= "define('MODULE_BX_MPI_SORT_ORDER_TITLE', 'Sortierung');\n";
    $lang_de .= "define('MODULE_BX_MPI_SORT_ORDER_DESC', 'Sortierreihenfolge in der Modul-Übersicht');\n\n";
    
    $lang_de .= "// 9. Maximale Anzeigeergebnisse\n";
    $lang_de .= "defined('MAX_DISPLAY_MPI_RESULTS_TITLE') or define('MAX_DISPLAY_MPI_RESULTS_TITLE', 'Maximale Anzeigeergebnisse');\n";
    $lang_de .= "defined('MAX_DISPLAY_MPI_RESULTS_DESC') or define('MAX_DISPLAY_MPI_RESULTS_DESC', 'Anzahl der gleichzeitig angezeigten Ergebnisse');\n";
    
    // Deutsche Datei schreiben
    $file_path_de = DIR_FS_CATALOG . 'lang/german/admin/configuration_' . $group_id . '.php';
    @file_put_contents($file_path_de, $lang_de);
    
    // Englische Sprachdatei
    $lang_en = "<?php\n";
    $lang_en .= "/**\n";
    $lang_en .= " * BX Modified Product Identifier (MPI)\n";
    $lang_en .= " * English Language Constants for System Module\n";
    $lang_en .= " * \n";
    $lang_en .= " * @package    BX Modified Product Identifier\n";
    $lang_en .= " * @subpackage Language\n";
    $lang_en .= " * @language   English\n";
    $lang_en .= " * @author     Axel Benkert\n";
    $lang_en .= " * @version    1.0.0\n";
    $lang_en .= " * @date       2025-01-16\n";
    $lang_en .= " * @copyright  2025 Axel Benkert\n";
    $lang_en .= " * @license    GNU General Public License v2.0\n";
    $lang_en .= " */\n\n";
    $lang_en .= "defined('_VALID_XTC') or die('Direct Access to this location is not allowed.');\n\n";
    $lang_en .= "// Configuration Options\n\n";
    
    $lang_en .= "// 1. Status\n";
    $lang_en .= "define('MODULE_BX_MPI_STATUS_TITLE', 'Enable Module');\n";
    $lang_en .= "define('MODULE_BX_MPI_STATUS_DESC', 'Do you want to enable the BX Product Identifier module?');\n\n";
    
    $lang_en .= "// 2. Config ID (hidden)\n";
    $lang_en .= "define('MODULE_BX_MPI_CONFIG_ID_TITLE', 'Configuration Group ID');\n";
    $lang_en .= "define('MODULE_BX_MPI_CONFIG_ID_DESC', 'Internal configuration group ID (do not change)');\n\n";
    
    $lang_en .= "// 3. Auto-Create\n";
    $lang_en .= "define('MODULE_BX_MPI_AUTO_CREATE_TITLE', 'Automatic SKU Generation');\n";
    $lang_en .= "define('MODULE_BX_MPI_AUTO_CREATE_DESC', '\n";
    $lang_en .= "  Should unique SKUs be created automatically when a product with attributes is ordered?<br>\n";
    $lang_en .= "  <small><strong>true:</strong> SKU generated on first order (recommended)<br>\n";
    $lang_en .= "  <strong>false:</strong> SKUs must be managed manually</small>\n";
    $lang_en .= "');\n\n";
    
    $lang_en .= "// 4. SKU Prefix\n";
    $lang_en .= "define('MODULE_BX_MPI_SKU_PREFIX_TITLE', 'SKU Prefix');\n";
    $lang_en .= "define('MODULE_BX_MPI_SKU_PREFIX_DESC', '\n";
    $lang_en .= "  Prefix for numeric SKUs.<br>\n";
    $lang_en .= "  <small><strong>Fixed format:</strong> PREFIX + PID(4) + \"_\" + OID(4) + \"-\" + VID(4) + \"x\" + ...<br>\n";
    $lang_en .= "  <strong>Example with \"SKU\":</strong> <code>SKU0042_0003-0045x0006-0195</code><br>\n";
    $lang_en .= "  <strong>Leave empty:</strong> <code>0042_0003-0045x0006-0195</code><br>\n";
    $lang_en .= "  Separators are fixed: \"_\" after PID, \"-\" between option/value, \"x\" between attributes</small>\n";
    $lang_en .= "');\n\n";
    
    $lang_en .= "// 5. Enable History\n";
    $lang_en .= "define('MODULE_BX_MPI_ENABLE_HISTORY_TITLE', 'Enable Change History');\n";
    $lang_en .= "define('MODULE_BX_MPI_ENABLE_HISTORY_DESC', '\n";
    $lang_en .= "  Should all changes to product identifiers be logged?<br>\n";
    $lang_en .= "  <small><strong>true:</strong> All changes saved to history table (recommended for audit)<br>\n";
    $lang_en .= "  <strong>false:</strong> No logging (saves storage space)</small>\n";
    $lang_en .= "');\n\n";
    
    $lang_en .= "// 6. EAN Mode\n";
    $lang_en .= "define('MODULE_BX_MPI_EAN_MODE_TITLE', 'EAN Generation');\n";
    $lang_en .= "define('MODULE_BX_MPI_EAN_MODE_DESC', '\n";
    $lang_en .= "  <strong>How should EAN codes be generated?</strong><br><br>\n";
    $lang_en .= "  \n";
    $lang_en .= "  <strong style=\"color: #333;\">manual:</strong> No automatic generation (admin manages manually)<br>\n";
    $lang_en .= "  <small style=\"color: #666;\">→ Best control, suitable for small shops or when supplier EANs exist</small><br><br>\n";
    $lang_en .= "  \n";
    $lang_en .= "  <strong style=\"color: #333;\">auto_pseudo:</strong> Pseudo-EAN with prefix \"2\" (in-store code)<br>\n";
    $lang_en .= "  <small style=\"color: #666;\">→ Automatically generated, scanner-compatible, NOT for external trade (Amazon/eBay)<br>\n";
    $lang_en .= "  → Ideal for internal processes: warehouse, RMA, picking</small><br><br>\n";
    $lang_en .= "  \n";
    $lang_en .= "  <strong style=\"color: #333;\">auto_gs1:</strong> Real EAN with GS1 prefix<br>\n";
    $lang_en .= "  <small style=\"color: #666;\">→ Tradeable on Amazon/eBay/marketplaces, requires GS1 membership (approx. 100-300€/year)<br>\n";
    $lang_en .= "  → GS1 prefix must be entered below</small><br><br>\n";
    $lang_en .= "  \n";
    $lang_en .= "  <div style=\"background: #fff3cd; padding: 10px; border-radius: 4px; margin-top: 10px;\">\n";
    $lang_en .= "    <strong>💡 Note on multi-attribute products:</strong><br>\n";
    $lang_en .= "    <small>For products with multiple attributes (e.g. T-shirt size M + color red), modified has no unique EAN.\n";
    $lang_en .= "    BX MPI automatically generates a unique EAN per variant - depending on the selected mode.</small>\n";
    $lang_en .= "  </div>\n";
    $lang_en .= "');\n\n";
    
    $lang_en .= "// 7. GS1 Prefix\n";
    $lang_en .= "define('MODULE_BX_MPI_GS1_PREFIX_TITLE', 'GS1 Prefix');\n";
    $lang_en .= "define('MODULE_BX_MPI_GS1_PREFIX_DESC', '\n";
    $lang_en .= "  <strong>GS1 prefix for EAN generation</strong> (only required for <strong>auto_gs1</strong> mode)<br><br>\n";
    $lang_en .= "  \n";
    $lang_en .= "  Enter your registered GS1 prefix (7-10 digits).<br>\n";
    $lang_en .= "  <small><strong>Example:</strong> <code>4004332</code></small><br><br>\n";
    $lang_en .= "  \n";
    $lang_en .= "  <div style=\"background: #f8d7da; padding: 10px; border-radius: 4px; border-left: 4px solid #dc3545;\">\n";
    $lang_en .= "    <strong>⚠️ Important:</strong> GS1 membership required!<br>\n";
    $lang_en .= "    <small>Without valid prefix, tradeable EANs cannot be generated.<br>\n";
    $lang_en .= "    Cost: approx. 100-300€/year depending on country and quota.</small>\n";
    $lang_en .= "  </div>\n";
    $lang_en .= "  \n";
    $lang_en .= "  <div style=\"margin-top: 10px;\">\n";
    $lang_en .= "    <small>ℹ️ More information: \n";
    $lang_en .= "    <a href=\"https://www.gs1.org\" target=\"_blank\" style=\"color: #0066cc;\">www.gs1.org</a></small>\n";
    $lang_en .= "  </div>\n";
    $lang_en .= "');\n\n";
    
    $lang_en .= "// 8. Sort Order\n";
    $lang_en .= "define('MODULE_BX_MPI_SORT_ORDER_TITLE', 'Sort Order');\n";
    $lang_en .= "define('MODULE_BX_MPI_SORT_ORDER_DESC', 'Sort order in module overview');\n\n";
    
    $lang_en .= "// 9. Maximum Display Results\n";
    $lang_en .= "defined('MAX_DISPLAY_MPI_RESULTS_TITLE') or define('MAX_DISPLAY_MPI_RESULTS_TITLE', 'Maximum Display Results');\n";
    $lang_en .= "defined('MAX_DISPLAY_MPI_RESULTS_DESC') or define('MAX_DISPLAY_MPI_RESULTS_DESC', 'Number of results displayed simultaneously');\n";
    
    // Englische Datei schreiben
    $file_path_en = DIR_FS_CATALOG . 'lang/english/admin/configuration_' . $group_id . '.php';
    @file_put_contents($file_path_en, $lang_en);
  }

  function remove() {
    // Gruppen-ID aus Datenbank holen (für Sprachdatei-Löschung)
    $config_query = xtc_db_query("SELECT configuration_value FROM " . TABLE_CONFIGURATION . " WHERE configuration_key = 'MODULE_BX_MPI_CONFIG_ID'");
    if ($config = xtc_db_fetch_array($config_query)) {
      $group_id = (int)$config['configuration_value'];
      
      // Sprachdateien löschen
      @unlink(DIR_FS_CATALOG . 'lang/german/admin/configuration_' . $group_id . '.php');
      @unlink(DIR_FS_CATALOG . 'lang/english/admin/configuration_' . $group_id . '.php');
    }
    
    // Konfigurationen löschen
    xtc_db_query("DELETE FROM " . TABLE_CONFIGURATION . " WHERE configuration_key IN ('" . implode("', '", $this->keys()) . "')");
    xtc_db_query("DELETE FROM " . TABLE_CONFIGURATION . " WHERE configuration_key IN ('" . implode("', '", $this->keys2()) . "')");    
    // Konfigurations-Gruppe löschen
    xtc_db_query("DELETE FROM " . TABLE_CONFIGURATION_GROUP . " WHERE configuration_group_title = 'BX Product Identifier (MPI)'");
    
    // Admin-Access entfernen
    xtc_db_query("ALTER TABLE " . TABLE_ADMIN_ACCESS . " DROP bx_mpi");

    // Tabellen löschen
    $this->dropTables();
  }

  function keys() {
    $key = array(
      'MODULE_BX_MPI_STATUS',
      'MODULE_BX_MPI_SORT_ORDER',
      'MODULE_BX_MPI_CONFIG_ID',
      'MAX_DISPLAY_MPI_RESULTS',
    );
    return $key;
  }

  function keys2() {
    $key = array(
      'MODULE_BX_MPI_AUTO_CREATE',
      'MODULE_BX_MPI_SKU_PREFIX',
      'MODULE_BX_MPI_ENABLE_HISTORY',
      'MODULE_BX_MPI_EAN_MODE',
      'MODULE_BX_MPI_GS1_PREFIX',
    );
    return $key;
  }

  /**
   * Erstelle Datenbank-Tabellen
   */
  private function createTables() {
		$table_check = xtc_db_query("SHOW TABLES LIKE 'bx_product_variants'");
		if (xtc_db_num_rows($table_check) == 0) {
			// Haupttabelle: bx_product_variants
			// 
			// Diese Tabelle ist das Herzstück von BX MPI und wird gemeinsam mit
			// BX Stockmanager Pro genutzt. Sie speichert eindeutige Identifikatoren
			// für jede Produkt-Variante (Kombination aus Produkt + Attributen).
			//
			// Wichtige Designentscheidungen:
			// 1. UNIQUE KEY (products_id, attributes_hash) verhindert Duplikate
			// 2. attributes_hash = MD5 der sortierten Attribut-IDs für Konsistenz
			// 3. products_ean ist NULL für Varianten, gefüllt nur für Einfachprodukte
			// 4. products_stock_attributes enthält serialisierte Daten für Stockmanager
			// 5. created_at/updated_at für Audit-Trail und Synchronisation
			//
			// Konsistenz-Regel (erzwungen in ProductIdentifier::createSKU()):
			// • Hat Produkt Attribute → products.products_ean MUSS NULL sein
			// • Hat Produkt KEINE Attribute → products.products_ean = bx_product_variants.products_ean
			//
			// Integration mit BX Stockmanager Pro:
			// • Gemeinsame Nutzung dieser Tabelle (daher Schutz bei Deinstallation)
			// • products_stock_quantity für Lagerbestandsführung
			// • products_stock_attributes für erweiterte Stockmanager-Daten
			xtc_db_query("CREATE TABLE IF NOT EXISTS bx_product_variants (
				identifier_id int(11) UNSIGNED NOT NULL COMMENT 'Auto Increment Id',
				products_id int(11) UNSIGNED NOT NULL COMMENT 'Eindeutige Produkt Id',
				attributes_hash varchar(32) NOT NULL COMMENT 'MD5 der Attribut-Kombination',
				products_sku varchar(100) DEFAULT NULL COMMENT 'Artikelnummer/SKU',
				products_ean varchar(50) DEFAULT NULL COMMENT 'EAN-13, GTIN',
				products_upc varchar(50) DEFAULT NULL COMMENT 'UPC (US-Format)',
				products_isbn varchar(50) DEFAULT NULL COMMENT 'ISBN (Bücher)',
				wws_artikel_nr varchar(100) DEFAULT NULL COMMENT 'Externe Warenwirtschafts-Nummer',
				wws_system varchar(50) DEFAULT NULL COMMENT 'WWS-System (JTL, SAP, Lexware, etc.)',
				warehouse_location varchar(100) DEFAULT NULL COMMENT 'Lagerplatz (z.B. A-12-03)',
				products_stock_attributes text DEFAULT NULL COMMENT 'Serialisierte Attribut-Daten',
				products_stock_quantity decimal(15,4) NOT NULL DEFAULT 0.0000 COMMENT 'Lagerbestand',
				bx_exported varchar(1) NOT NULL DEFAULT 'n' COMMENT 'Billbee Exportstatus',
				created_at datetime NOT NULL DEFAULT current_timestamp(),
				updated_at datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
			) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Gemeinsame Varianten-Tabelle für BX MPI, BX Stockmanager und Billbee'");

			// Indizes für Performance und Datenkonsistenz
			//
			// PRIMARY KEY: Auto-Increment ID für interne Referenzierung
			// UNIQUE KEY idx_product_variant_unique: Verhindert doppelte Einträge
			//   → Ermöglicht INSERT ... ON DUPLICATE KEY UPDATE in ProductIdentifier::createSKU()
			//   → Idempotente SKU/EAN-Zuweisung ohne Race Conditions
			// KEY idx_attributes_hash: Beschleunigt Lookups nach Produkt+Hash Kombination
			xtc_db_query("ALTER TABLE bx_product_variants 
				ADD PRIMARY KEY (identifier_id), 
				ADD UNIQUE KEY idx_product_variant_unique (products_id, attributes_hash),
				ADD KEY idx_attributes_hash (products_id, attributes_hash)");
			xtc_db_query("ALTER TABLE bx_product_variants MODIFY identifier_id int(11) UNSIGNED NOT NULL AUTO_INCREMENT;");
		}

    // Attribut-Mapping-Tabelle
    xtc_db_query("CREATE TABLE IF NOT EXISTS product_identifier_attributes (
      id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
      identifier_id INT(11) UNSIGNED NOT NULL,
      
      -- Attribut-Details
      products_options_id INT(11) NOT NULL COMMENT 'z.B. 1 (Größe)',
      products_options_values_id INT(11) NOT NULL COMMENT 'z.B. 3 (M)',
      options_values_price DECIMAL(15,4) DEFAULT 0.0000 COMMENT 'Aufpreis',
      price_prefix CHAR(1) DEFAULT '+',
      
      -- Sortierung für SKU-Generierung
      sort_order INT(11) DEFAULT 0,
      
      INDEX idx_identifier (identifier_id),
      INDEX idx_option_combo (products_options_id, products_options_values_id),
      
      CONSTRAINT fk_identifier_attributes 
        FOREIGN KEY (identifier_id) 
        REFERENCES product_identifiers(identifier_id) 
        ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='BX MPI: Attribut-Mapping für Identifier'");

    // History-Tabelle (optional)
    xtc_db_query("CREATE TABLE IF NOT EXISTS product_identifier_history (
      history_id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
      identifier_id INT(11) UNSIGNED NOT NULL,
      
      -- Änderungen
      field_name VARCHAR(50) NOT NULL COMMENT 'Geändertes Feld',
      old_value TEXT COMMENT 'Alter Wert',
      new_value TEXT COMMENT 'Neuer Wert',
      
      -- Kontext
      changed_by INT(11) DEFAULT NULL COMMENT 'User-ID (Admin)',
      changed_by_type ENUM('admin', 'system', 'import') DEFAULT 'admin',
      change_reason VARCHAR(255) DEFAULT NULL,
      changed_at DATETIME DEFAULT CURRENT_TIMESTAMP,
      
      INDEX idx_identifier (identifier_id),
      INDEX idx_changed_at (changed_at)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='BX MPI: Änderungs-Historie'");

    // EAN-Pool: Block-Tabelle (GS1 purchased blocks)
    xtc_db_query("CREATE TABLE IF NOT EXISTS bx_ean_blocks (
      block_id INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
      block_number VARCHAR(50) NOT NULL COMMENT 'User-defined block identifier e.g. Block-2024-001',
      block_size ENUM('10','100','1000') NOT NULL COMMENT 'Number of EANs in block',
      purchased_at DATE NOT NULL COMMENT 'Date when block was purchased from GS1',
      imported_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Date/time of CSV import',
      total_eans INT(11) NOT NULL COMMENT 'Total EANs in this block',
      used_eans INT(11) NOT NULL DEFAULT 0 COMMENT 'Number of assigned EANs',
      available_eans INT(11) GENERATED ALWAYS AS (total_eans - used_eans) STORED COMMENT 'Calculated: remaining free EANs',
      status ENUM('active','depleted','archived') NOT NULL DEFAULT 'active' COMMENT 'Block status',
      notes TEXT COMMENT 'Optional notes e.g. Block for electronics range',
      
      UNIQUE KEY block_number (block_number),
      INDEX status (status),
      INDEX available_eans (available_eans)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='BX MPI: GS1 EAN blocks purchased from GS1 Germany'");

    // EAN-Pool: Individual EANs from imported GS1 blocks
    xtc_db_query("CREATE TABLE IF NOT EXISTS bx_ean_pool (
      pool_id INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
      block_id INT(11) NOT NULL COMMENT 'Reference to bx_ean_blocks',
      ean VARCHAR(14) NOT NULL COMMENT 'EAN-13 or GTIN-14 code',
      status ENUM('available','assigned','reserved') NOT NULL DEFAULT 'available' COMMENT 'Current status of EAN',
      identifier_id INT(11) UNSIGNED DEFAULT NULL COMMENT 'Reference to product_identifiers when assigned',
      assigned_at DATETIME DEFAULT NULL COMMENT 'Timestamp when EAN was assigned to product',
      created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Import timestamp',
      
      UNIQUE KEY ean (ean),
      INDEX block_id (block_id),
      INDEX status (status),
      INDEX identifier_id (identifier_id)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='BX MPI: Individual EANs from GS1 blocks'");

    // Foreign Keys separat hinzufügen (nach Tabellenerstellung)
    // Prüfen ob Foreign Keys bereits existieren
    $fk_check = xtc_db_query("
      SELECT COUNT(*) as fk_count 
      FROM information_schema.TABLE_CONSTRAINTS 
      WHERE CONSTRAINT_SCHEMA = DATABASE() 
      AND TABLE_NAME = 'bx_ean_pool' 
      AND CONSTRAINT_TYPE = 'FOREIGN KEY'
    ");
    $fk_data = xtc_db_fetch_array($fk_check);
    
    if ($fk_data['fk_count'] == 0) {
      xtc_db_query("
        ALTER TABLE bx_ean_pool
        ADD CONSTRAINT fk_ean_pool_block 
          FOREIGN KEY (block_id) 
          REFERENCES bx_ean_blocks(block_id) 
          ON DELETE CASCADE
      ");
      
      xtc_db_query("
        ALTER TABLE bx_ean_pool
        ADD CONSTRAINT fk_ean_pool_identifier 
          FOREIGN KEY (identifier_id) 
          REFERENCES product_identifiers(identifier_id) 
          ON DELETE SET NULL
      ");
    };

    // Trigger: Automatische Block-Statistik-Aktualisierung
    // Prüfen ob Trigger bereits existiert
    $trigger_check = xtc_db_query("SHOW TRIGGERS LIKE 'bx_ean_pool'");
    if (xtc_db_num_rows($trigger_check) == 0) {
      xtc_db_query("
        CREATE TRIGGER update_block_stats_after_assign 
        AFTER UPDATE ON bx_ean_pool
        FOR EACH ROW
        BEGIN
          -- EAN was assigned (available -> assigned)
          IF NEW.status = 'assigned' AND OLD.status != 'assigned' THEN
            UPDATE bx_ean_blocks 
            SET used_eans = used_eans + 1
            WHERE block_id = NEW.block_id;
            
            -- Check if block is now depleted
            UPDATE bx_ean_blocks
            SET status = 'depleted'
            WHERE block_id = NEW.block_id
            AND used_eans >= total_eans;
          END IF;
          
          -- EAN was released (assigned -> available)
          IF NEW.status != 'assigned' AND OLD.status = 'assigned' THEN
            UPDATE bx_ean_blocks 
            SET used_eans = used_eans - 1
            WHERE block_id = NEW.block_id;
            
            -- Re-activate block if it was depleted
            UPDATE bx_ean_blocks
            SET status = 'active'
            WHERE block_id = NEW.block_id
            AND status = 'depleted'
            AND used_eans < total_eans;
          END IF;
        END
      ");
    }
  }

  /**
   * Lösche Datenbank-Tabellen (nur für Deinstallation)
   */
  private function dropTables() {
    // Trigger zuerst löschen
    xtc_db_query("DROP TRIGGER IF EXISTS update_block_stats_after_assign");
    
    // Tabellen in richtiger Reihenfolge löschen (wegen Foreign Keys)
    xtc_db_query("DROP TABLE IF EXISTS product_identifier_history");
    xtc_db_query("DROP TABLE IF EXISTS product_identifier_attributes");
    xtc_db_query("DROP TABLE IF EXISTS bx_ean_pool");
    xtc_db_query("DROP TABLE IF EXISTS bx_ean_blocks");

		if(!defined("MODULE_BX_STOCKMANAGER_STATUS") && !defined("MODULE_BILLBEE_STATUS")) {
			xtc_db_query("DROP TABLE IF EXISTS bx_product_variants;");
		}
  }
}
