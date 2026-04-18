<?php
/**
 * ██████╗  ███████╗ ███╗   ██╗  █████╗  ██╗  ██╗
 * ██╔══██╗ ██╔════╝ ████╗  ██║ ██╔══██╗ ╚██╗██╔╝
 * ██████╔╝ █████╗   ██╔██╗ ██║ ███████║  ╚███╔╝
 * ██╔══██╗ ██╔══╝   ██║╚██╗██║ ██╔══██║  ██╔██╗
 * ██████╔╝ ███████╗ ██║ ╚████║ ██║  ██║ ██╔╝ ██╗
 * ╚═════╝  ╚══════╝ ╚═╝  ╚═══╝ ╚═╝  ╚═╝ ╚═╝  ╚═╝
 * BX Modified Product Identifier (MPI) - API Class
 * 
 * Zentrale API-Klasse für den Zugriff auf eindeutige Produktidentifikatoren.
 * Diese Klasse wird von allen Modulen (RMA, Versand, Export) verwendet.
 * 
 * Features:
 * - SKU-Generierung für Produkt-Varianten
 * - EAN/GTIN/UPC/ISBN-Verwaltung
 * - Warenwirtschafts-Nummern-Mapping
 * - Attribut-Hash-Berechnung
 * - Änderungs-Historie (optional)
 * 
 * Usage Example:
 * php
 * // SKU für Produkt mit Attributen holen
 * $attributes = [1 => 3, 2 => 5]; // [options_id => values_id]
 * $sku = ProductIdentifier::getSKU(123, $attributes);
 * 
 * // EAN für Variante holen
 * $ean = ProductIdentifier::getEAN(123, $attributes);
 * 
 * // Neue SKU erstellen
 * $new_sku = ProductIdentifier::createSKU(123, $attributes);
 * 
 * 
 * @package    BX Modified Product Identifier
 * @subpackage API
 * @category   Product Management
 * @author     Axel Benkert
 * @version    1.0.0
 * @since      1.0.0
 * @date       2025-01-16
 * @copyright  2025 Axel Benkert
 * @license    GNU General Public License v2.0
 */

defined('_VALID_XTC') or die('Direct Access to this location is not allowed.');

class ProductIdentifier {
    
    /**
     * Prüfe ob Modul verfügbar ist
     * 
     * @return bool
     */
    public static function isAvailable() {
        return defined('MODULE_BX_MPI_STATUS') && MODULE_BX_MPI_STATUS == 'true';
    }
    
    /**
     * Hole SKU für Produkt mit Attributen
     * 
     * @param int   $products_id    Produkt-ID
     * @param array $attributes     Attribute als [options_id => values_id]
     * @param bool  $auto_create    Automatisch erstellen wenn nicht vorhanden
     * @return string|false         SKU oder false
     */
    public static function getSKU($products_id, $attributes = [], $auto_create = null) {
        // NICHT zu früh abbrechen - Modul könnte nicht geladen sein, aber wir wollen trotzdem SKU holen
        // if (!self::isAvailable()) {
        //     return false;
        // }
        
        // Default aus Config
        if ($auto_create === null) {
            $auto_create = defined('MODULE_BX_MPI_AUTO_CREATE') && MODULE_BX_MPI_AUTO_CREATE == 'true';
        }
        
        $attributes_hash = self::generateAttributesHash($attributes);
        
        // Prüfe ob bereits vorhanden
        $query = xtc_db_query("
            SELECT products_sku 
            FROM " . TABLE_PRODUCT_IDENTIFIERS . "
            WHERE products_id = '" . (int)$products_id . "'
              AND attributes_hash = '" . xtc_db_input($attributes_hash) . "'
        ");
        
        if ($row = xtc_db_fetch_array($query)) {
            return $row['products_sku'];
        }
        
        // Auto-Create
        if ($auto_create) {
            return self::createSKU($products_id, $attributes);
        }
        
        return false;
    }
    
    /**
     * Erstelle neue SKU für Produkt-Variante
     * 
     * @param int   $products_id    Produkt-ID
     * @param array $attributes     Attribute als [options_id => values_id]
     * @param int   $orders_id      Bestellungs-ID (optional, für historische Daten)
     * @return string|false         Generierte SKU oder false
     */
    public static function createSKU($products_id, $attributes = [], $orders_id = null) {
        // NICHT zu früh abbrechen - Modul könnte nicht geladen sein, aber wir wollen trotzdem SKU generieren
        // if (!self::isAvailable()) {
        //     return false;
        // }
        
        // Basis-Model holen (für Fallback)
        $product = self::getProduct($products_id);
        if (!$product) {
            // Produkt nicht gefunden - trotzdem SKU generieren
            // aber ohne EAN aus products Tabelle
            $product = ['products_model' => '', 'products_ean' => null];
        }
        
        // Festes SKU-FORMAT: PREFIX + PID + "_" + OID + "-" + VID + "x" + ...
        // Separatoren sind hart-codiert für Konsistenz zwischen MPI und Stockmanager
        // Nur numerischer Modus wird unterstützt
        $sku_prefix = defined('MODULE_BX_MPI_SKU_PREFIX') ? MODULE_BX_MPI_SKU_PREFIX : 'SKU';
        
        // NUMERISCHE SKU: PREFIX + PID + "_" + OID + "-" + VID + "x" + ...
        // Format: SKU0042_0003-0045x0006-0195
        $sku = $sku_prefix . str_pad((int)$products_id, 4, '0', STR_PAD_LEFT);
        
        // Attribute anhängen (sortiert für Konsistenz)
        if (!empty($attributes)) {
            $sku .= '_';  // Hart-codiert: Trenner nach Produkt-ID
            $i = 0;
            ksort($attributes);
            foreach ($attributes as $option_id => $value_id) {
                if ($i > 0) {
                    $sku .= 'x';  // Hart-codiert: Trenner zwischen Attributen
                }
                $sku .= str_pad((int)$option_id, 4, '0', STR_PAD_LEFT);
                $sku .= '-' . str_pad((int)$value_id, 4, '0', STR_PAD_LEFT);  // Hart-codiert: Trenner Option-Value
                $i++;
            }
        }
        
        $attributes_hash = self::generateAttributesHash($attributes);
        
        // Prüfe ob EAN-Auto-Generierung aktiviert ist (VOR dem INSERT!)
        $auto_generate_ean = false;
        $ean_mode = null;
        if (defined('MODULE_BX_MPI_EAN_MODE') && in_array(MODULE_BX_MPI_EAN_MODE, ['auto_pseudo', 'auto_gs1'])) {
            // Nur auto-generieren wenn Produkt keine EAN hat
            if (empty($product['products_ean'])) {
                $auto_generate_ean = true;
                $ean_mode = MODULE_BX_MPI_EAN_MODE;
            }
        }
        
        // WICHTIG: EAN aus products Tabelle NUR für Einfachprodukte übernehmen
        // Wenn Produkt Attribute hat, darf products.products_ean NICHT in Identifier kopiert werden
        $use_product_ean = false;
        if (empty($attributes) && !empty($product['products_ean'])) {
            // Nur für Einfachprodukte (keine Attribute) die EAN übernehmen
            $use_product_ean = true;
        }
        
        // In DB speichern
        $insert_query = "
            INSERT INTO " . TABLE_PRODUCT_IDENTIFIERS . " 
            (products_id, attributes_hash, products_sku, products_ean, created_at, updated_at)
            VALUES (
                '" . (int)$products_id . "',
                '" . xtc_db_input($attributes_hash) . "',
                '" . xtc_db_input($sku) . "',
                " . ($use_product_ean ? "'" . xtc_db_input($product['products_ean']) . "'" : "NULL") . ",
                NOW(),
                NOW()
            )
            ON DUPLICATE KEY UPDATE 
                products_sku = VALUES(products_sku),
                products_ean = VALUES(products_ean),
                updated_at = NOW()
        ";
        
        $insert_result = xtc_db_query($insert_query);
        if (!$insert_result) {
            // Fehler beim INSERT - detaillierte Diagnostik
            $error_msg = "INSERT-Query fehlgeschlagen für Produkt $products_id mit Hash '$attributes_hash'";
            if (function_exists('xtc_db_error')) {
                $error_msg .= " - DB-Fehler: " . xtc_db_error();
            }
            // Stille fehlschlag - versuche nicht zu recovern
            return false;
        }
        
        // WICHTIG: Bei ON DUPLICATE KEY UPDATE erhalten wir möglicherweise eine alte identifier_id
        // die nicht mehr existiert. Daher: Erst nach INSERT-ID suchen, dann verifizieren.
        $was_insert = (xtc_db_insert_id() > 0);
        
        if ($was_insert) {
            // Echter INSERT - verwende neue ID
            $identifier_id = xtc_db_insert_id();
        } else {
            // UPDATE - hole ID via SELECT und verifiziere Existenz
            $check_query = xtc_db_query("
                SELECT identifier_id
                FROM " . TABLE_PRODUCT_IDENTIFIERS . "
                WHERE products_id = '" . (int)$products_id . "'
                  AND attributes_hash = '" . xtc_db_input($attributes_hash) . "'
                LIMIT 1
            ");
            
            if (!$check_query) {
                return false;
            }
            
            $check_row = xtc_db_fetch_array($check_query);
            if (!$check_row) {
                return false;
            }
            
            $identifier_id = (int)$check_row['identifier_id'];
        }
        
        if ($identifier_id <= 0) {
            return false;
        }
        
        // Zusätzliche Sicherheitsprüfung: Verifiziere dass identifier_id wirklich existiert
        $verify_query = xtc_db_query("
            SELECT identifier_id 
            FROM " . TABLE_PRODUCT_IDENTIFIERS . " 
            WHERE identifier_id = '" . (int)$identifier_id . "'
            LIMIT 1
        ");
        
        if (!$verify_query || !xtc_db_fetch_array($verify_query)) {
            // Identifier existiert nicht (mehr) - abbrechen
            return false;
        }
        
        // Attribute-Mapping speichern
        if (!empty($attributes) && $identifier_id > 0) {
            self::saveAttributeMapping($identifier_id, $attributes);
        }
        
        // EAN-Auto-Generierung (NACH dem INSERT, wenn identifier_id vorhanden)
        if ($auto_generate_ean && $identifier_id > 0) {
            $generated_ean = null;
            
            if ($ean_mode == 'auto_pseudo') {
                // Pseudo-EAN generieren (Prefix 2 = Instore)
                $generated_ean = self::generatePseudoEAN($identifier_id);
                
            } elseif ($ean_mode == 'auto_gs1') {
                // Echte GS1-EAN generieren
                $generated_ean = self::generateGS1EAN();
            }
            
            // Generierte EAN speichern (wenn erfolgreich)
            if ($generated_ean) {
                xtc_db_query("
                    UPDATE " . TABLE_PRODUCT_IDENTIFIERS . "
                    SET products_ean = '" . xtc_db_input($generated_ean) . "',
                        updated_at = NOW()
                    WHERE identifier_id = '" . (int)$identifier_id . "'
                ");
                
                // Historie: EAN-Generierung protokollieren
                if (defined('MODULE_BX_MPI_ENABLE_HISTORY') && MODULE_BX_MPI_ENABLE_HISTORY == 'true') {
                    $reason = $ean_mode == 'auto_pseudo' ? 'ean_auto_generated_pseudo' : 'ean_auto_generated_gs1';
                    self::logChange($identifier_id, 'ean', null, $generated_ean, $reason);
                }
            }
        }
        
        // Historie (optional)
        if (defined('MODULE_BX_MPI_ENABLE_HISTORY') && MODULE_BX_MPI_ENABLE_HISTORY == 'true') {
            self::logChange($identifier_id, 'products_sku', null, $sku, 'auto_created');
        }
        
        return $sku;
    }
    
    /**
     * Hole EAN für Produkt-Variante
     * 
     * @param int   $products_id    Produkt-ID
     * @param array $attributes     Attribute als [options_id => values_id]
     * @return string|false         EAN oder false
     */
    public static function getEAN($products_id, $attributes = []) {
        // NICHT zu früh abbrechen - Modul könnte nicht geladen sein
        // if (!self::isAvailable()) {
        //     return false;
        // }
        
        $attributes_hash = self::generateAttributesHash($attributes);
        
        $query = xtc_db_query("
            SELECT products_ean 
            FROM " . TABLE_PRODUCT_IDENTIFIERS . "
            WHERE products_id = '" . (int)$products_id . "'
              AND attributes_hash = '" . xtc_db_input($attributes_hash) . "'
        ");
        
        if ($row = xtc_db_fetch_array($query)) {
            return $row['products_ean'];
        }
        
        return false;
    }
    
    /**
     * Setze EAN für Variante
     * 
     * @param int    $products_id   Produkt-ID
     * @param array  $attributes    Attribute als [options_id => values_id]
     * @param string $ean           EAN-Wert
     * @return bool                 Erfolg
     */
    public static function setEAN($products_id, $attributes, $ean) {
        if (!self::isAvailable()) {
            return false;
        }
        
        $attributes_hash = self::generateAttributesHash($attributes);
        
        // Hole alte EAN für Historie
        $old_ean = self::getEAN($products_id, $attributes);
        
        $query = "
            UPDATE " . TABLE_PRODUCT_IDENTIFIERS . "
            SET products_ean = '" . xtc_db_input($ean) . "',
                updated_at = NOW()
            WHERE products_id = '" . (int)$products_id . "'
              AND attributes_hash = '" . xtc_db_input($attributes_hash) . "'";
        
        xtc_db_query($query);
        $success = xtc_db_affected_rows() > 0;
        
        // Historie
        if ($success && defined('MODULE_BX_MPI_ENABLE_HISTORY') && MODULE_BX_MPI_ENABLE_HISTORY == 'true') {
            $identifier_id = self::getIdentifierId($products_id, $attributes);
            if ($identifier_id) {
                self::logChange($identifier_id, 'ean', $old_ean, $ean, 'manual_update');
            }
        }
        
        return $success;
    }
    
    /**
     * Hole komplette Identifier-Daten
     * 
     * @param int   $products_id    Produkt-ID
     * @param array $attributes     Attribute als [options_id => values_id]
     * @return array|false          Vollständige Daten oder false
     */
    public static function getIdentifier($products_id, $attributes = []) {
        // NICHT zu früh abbrechen - Modul könnte nicht geladen sein
        // if (!self::isAvailable()) {
        //     return false;
        // }
        
        $attributes_hash = self::generateAttributesHash($attributes);
        
        $query = xtc_db_query("
            SELECT *
            FROM " . TABLE_PRODUCT_IDENTIFIERS . "
            WHERE products_id = '" . (int)$products_id . "'
              AND attributes_hash = '" . xtc_db_input($attributes_hash) . "'
        ");
        
        if ($row = xtc_db_fetch_array($query)) {
            return $row;
        }
        
        return false;
    }
    
    /**
     * Setze Warenwirtschafts-Nummer
     * 
     * @param int    $products_id       Produkt-ID
     * @param array  $attributes        Attribute
     * @param string $wws_artikel_nr    WWS-Artikelnummer
     * @param string $wws_system        WWS-System (z.B. 'JTL', 'SAP')
     * @return bool                     Erfolg
     */
    public static function setWWSNummer($products_id, $attributes, $wws_artikel_nr, $wws_system = null) {
        if (!self::isAvailable()) {
            return false;
        }
        
        $attributes_hash = self::generateAttributesHash($attributes);
        
        $query = "
            UPDATE " . TABLE_PRODUCT_IDENTIFIERS . "
            SET wws_artikel_nr = '" . xtc_db_input($wws_artikel_nr) . "',
                " . ($wws_system ? "wws_system = '" . xtc_db_input($wws_system) . "'," : "") . "
                updated_at = NOW()
            WHERE products_id = '" . (int)$products_id . "'
              AND attributes_hash = '" . xtc_db_input($attributes_hash) . "'";
        
        xtc_db_query($query);
        return xtc_db_affected_rows() > 0;
    }
    
    /**
     * Setze Lagerplatz
     * 
     * @param int    $products_id           Produkt-ID
     * @param array  $attributes            Attribute
     * @param string $warehouse_location    Lagerplatz (z.B. "A-12-03")
     * @return bool                         Erfolg
     */
    public static function setWarehouseLocation($products_id, $attributes, $warehouse_location) {
        if (!self::isAvailable()) {
            return false;
        }
        
        $attributes_hash = self::generateAttributesHash($attributes);
        
        $query = "
            UPDATE " . TABLE_PRODUCT_IDENTIFIERS . "
            SET warehouse_location = '" . xtc_db_input($warehouse_location) . "',
                updated_at = NOW()
            WHERE products_id = '" . (int)$products_id . "'
              AND attributes_hash = '" . xtc_db_input($attributes_hash) . "'";
        
        xtc_db_query($query);
        return xtc_db_affected_rows() > 0;
    }
    
    /**
     * Generiere Hash für Attribut-Kombination
     * 
     * @param array|string $attributes Attribute als [options_id => values_id] oder String "0003-0045x0006-0195"
     * @return string           MD5-Hash oder leerer String für Produkte ohne Optionen
     */
    private static function generateAttributesHash($attributes) {
        if (empty($attributes)) {
            return '';  // Leerer String statt NULL für Produkte ohne Optionen
        }
        
        // Wenn String übergeben wurde, erst parsen
        if (is_string($attributes)) {
            $attributes = self::parseStringId($attributes);
        }
        
        if (!is_array($attributes) || empty($attributes)) {
            return '';  // Leerer String statt NULL für Produkte ohne Optionen
        }
        
        ksort($attributes);
        return md5(serialize($attributes));
    }
    
    /**
     * Parse String-ID zu Attribut-Array
     * Format: "0003-0045x0006-0195" → [3 => 45, 6 => 195]
     * 
     * @param string $string_id String-ID
     * @return array Attribute als [options_id => values_id]
     */
    private static function parseStringId($string_id) {
        $attributes = [];
        
        if (empty($string_id)) {
            return $attributes;
        }
        
        // Split by 'x': ["0003-0045", "0006-0195"]
        $pairs = explode('x', $string_id);
        
        foreach ($pairs as $pair) {
            // Split by '-': ["0003", "0045"]
            $parts = explode('-', $pair);
            if (count($parts) == 2) {
                $option_id = (int)$parts[0];
                $value_id = (int)$parts[1];
                
                // Ignoriere 0000-0000 (Platzhalter)
                if ($option_id > 0 && $value_id > 0) {
                    $attributes[$option_id] = $value_id;
                }
            }
        }
        
        ksort($attributes);
        return $attributes;
    }
    
    /**
     * Hole Produkt-Daten
     * 
     * @param int $products_id Produkt-ID
     * @return array|false     Produkt-Daten oder false
     */
    private static function getProduct($products_id) {
        if (!defined('TABLE_PRODUCTS')) {
            // Tabellenkonstante nicht definiert
            return false;
        }
        
        $query = xtc_db_query("
            SELECT products_model, products_ean 
            FROM " . TABLE_PRODUCTS . " 
            WHERE products_id = '" . (int)$products_id . "'
        ");
        
        if (!$query) {
            // Query fehlgeschlagen
            return false;
        }
        
        if ($row = xtc_db_fetch_array($query)) {
            return $row;
        }
        
        return false;
    }
    
    /**
     * Hole Attribut-Model für SKU-Generierung
     * 
     * @param int   $products_id    Produkt-ID
     * @param int   $option_id      Options-ID
     * @param int   $value_id       Values-ID
     * @param int   $orders_id      Bestell-ID (optional, für historische Daten)
     * @return string|false         Model oder false
     */
    private static function getAttributeModel($products_id, $option_id, $value_id, $orders_id = null) {
        // PRIORITÄT 1: Historische Daten aus orders_products_attributes (wenn orders_id übergeben)
        if ($orders_id !== null) {
            $order_query = xtc_db_query("
                SELECT opa.attributes_model 
                FROM " . TABLE_ORDERS_PRODUCTS_ATTRIBUTES . " opa
                JOIN " . TABLE_ORDERS_PRODUCTS . " op ON opa.orders_products_id = op.orders_products_id
                WHERE op.orders_id = '" . (int)$orders_id . "'
                  AND op.products_id = '" . (int)$products_id . "'
                  AND opa.orders_products_options_id = '" . (int)$option_id . "'
                  AND opa.orders_products_options_values_id = '" . (int)$value_id . "'
                LIMIT 1
            ");
            
            if ($order_row = xtc_db_fetch_array($order_query)) {
                $model = trim($order_row['attributes_model']);
                if (!empty($model)) {
                    return $model;
                }
            }
        }
        
        // PRIORITÄT 2: Aktuelle Produktdaten aus products_attributes
        $query = xtc_db_query("
            SELECT attributes_model FROM " . TABLE_PRODUCTS_ATTRIBUTES . " 
            WHERE products_id = '" . (int)$products_id . "' 
            AND options_id = '" . (int)$option_id . "' 
            AND options_values_id = '" . (int)$value_id . "'
            LIMIT 1
        ");
        
        if ($row = xtc_db_fetch_array($query)) {
            $model = trim($row['attributes_model']);
            if (!empty($model)) {
                return $model;
            }
        }
        
        // PRIORITÄT 3: Fallback mit formatierten IDs (z.B. "003-005")
        return str_pad((int)$option_id, 3, '0', STR_PAD_LEFT) . '-' . str_pad((int)$value_id, 3, '0', STR_PAD_LEFT);
    }
    
    /**
     * Speichere Attribut-Mapping
     * 
     * @param int   $identifier_id  Identifier-ID
     * @param array $attributes     Attribute
     */
    private static function saveAttributeMapping($identifier_id, $attributes) {
        // Alte Mappings löschen
        xtc_db_query("
            DELETE FROM " . TABLE_PRODUCT_IDENTIFIER_ATTRIBUTES . "
            WHERE identifier_id = '" . (int)$identifier_id . "'
        ");
        
        // Neue Mappings einfügen
        $sort = 0;
        foreach ($attributes as $option_id => $value_id) {
            xtc_db_query("
                INSERT INTO " . TABLE_PRODUCT_IDENTIFIER_ATTRIBUTES . "
                (identifier_id, products_options_id, products_options_values_id, sort_order)
                VALUES (
                    '" . (int)$identifier_id . "',
                    '" . (int)$option_id . "',
                    '" . (int)$value_id . "',
                    '" . (int)$sort . "'
                )
            ");
            $sort++;
        }
    }
    
    /**
     * Hole Identifier-ID
     * 
     * @param int   $products_id    Produkt-ID
     * @param array $attributes     Attribute
     * @return int|false            Identifier-ID oder false
     */
    private static function getIdentifierId($products_id, $attributes) {
        $attributes_hash = self::generateAttributesHash($attributes);
        
        $query = xtc_db_query("
            SELECT identifier_id 
            FROM " . TABLE_PRODUCT_IDENTIFIERS . "
            WHERE products_id = '" . (int)$products_id . "'
              AND attributes_hash = '" . xtc_db_input($attributes_hash) . "'
        ");
        
        if ($row = xtc_db_fetch_array($query)) {
            return (int)$row['identifier_id'];
        }
        
        return false;
    }
    
    /**
     * Protokolliere Änderung in Historie
     * 
     * @param int    $identifier_id     Identifier-ID
     * @param string $field_name        Feld-Name
     * @param mixed  $old_value         Alter Wert
     * @param mixed  $new_value         Neuer Wert
     * @param string $change_reason     Grund (z.B. 'auto_created', 'manual_update')
     */
    private static function logChange($identifier_id, $field_name, $old_value, $new_value, $change_reason = null) {
        if (!defined('MODULE_BX_MPI_ENABLE_HISTORY') || MODULE_BX_MPI_ENABLE_HISTORY != 'true') {
            return;
        }
        
        // Admin-ID holen (falls vorhanden)
        $changed_by      = isset($_SESSION['customer_id']) ? (int)$_SESSION['customer_id'] : null;
        $changed_by_type = isset($_SESSION['customer_id']) ? 'admin' : 'system';
        
        xtc_db_query("
            INSERT INTO " . TABLE_PRODUCT_IDENTIFIER_HISTORY . "
            (identifier_id, field_name, old_value, new_value, changed_by, changed_by_type, change_reason, changed_at)
            VALUES (
                '" . (int)$identifier_id . "',
                '" . xtc_db_input($field_name) . "',
                " . ($old_value ? "'" . xtc_db_input($old_value) . "'" : "NULL") . ",
                " . ($new_value ? "'" . xtc_db_input($new_value) . "'" : "NULL") . ",
                " . ($changed_by ? "'" . (int)$changed_by . "'" : "NULL") . ",
                '" . xtc_db_input($changed_by_type) . "',
                " . ($change_reason ? "'" . xtc_db_input($change_reason) . "'" : "NULL") . ",
                NOW()
            )
        ");
    }
    
    /**
     * Hole Änderungs-Historie für Identifier
     * 
     * @param int $identifier_id    Identifier-ID
     * @param int $limit            Anzahl Einträge (Standard: 50)
     * @return array                Historie-Einträge
     */
    public static function getHistory($identifier_id, $limit = 50) {
        if (!self::isAvailable()) {
            return [];
        }
        
        $history = [];
        $query = xtc_db_query("
            SELECT *
            FROM " . TABLE_PRODUCT_IDENTIFIER_HISTORY . "
            WHERE identifier_id = '" . (int)$identifier_id . "'
            ORDER BY changed_at DESC
            LIMIT " . (int)$limit . "
        ");
        
        while ($row = xtc_db_fetch_array($query)) {
            $history[] = $row;
        }
        
        return $history;
    }
    
    /**
     * Lösche Identifier (mit Attributen und Historie)
     * 
     * @param int   $products_id    Produkt-ID
     * @param array $attributes     Attribute
     * @return bool                 Erfolg
     */
    public static function deleteIdentifier($products_id, $attributes = []) {
        if (!self::isAvailable()) {
            return false;
        }
        
        $attributes_hash = self::generateAttributesHash($attributes);
        
        $query = "
            DELETE FROM " . TABLE_PRODUCT_IDENTIFIERS . "
            WHERE products_id = '" . (int)$products_id . "'
              AND attributes_hash = '" . xtc_db_input($attributes_hash) . "'";
        
        xtc_db_query($query);
        return xtc_db_affected_rows() > 0;
    }
    
    /**
     * Generiere Pseudo-EAN (Instore-Code mit Prefix 2)
     * 
     * Format: 2 + 11 Stellen ID + Prüfziffer = EAN-13
     * Prefix "2" = Instore-Code (nicht für externen Handel)
     * 
     * @param int $identifier_id Identifier-ID
     * @return string EAN-13 Code
     */
    public static function generatePseudoEAN($identifier_id) {
        if (!self::isAvailable()) {
            return false;
        }
        
        // Prefix 2 = Instore-Code (2000000000000-2999999999999)
        $prefix = '2';
        $id = str_pad($identifier_id, 11, '0', STR_PAD_LEFT);
        $ean12 = $prefix . $id;
        
        // EAN-13 Prüfziffer berechnen
        $sum = 0;
        for ($i = 0; $i < 12; $i++) {
            $sum += ($i % 2 == 0 ? 1 : 3) * (int)$ean12[$i];
        }
        $check = (10 - ($sum % 10)) % 10;
        
        return $ean12 . $check;
    }
    
    /**
     * Generiere GS1-EAN mit registriertem Präfix
     * 
     * Format: GS1-Präfix (7-10 Stellen) + Zähler (2-5 Stellen) + Prüfziffer = EAN-13
     * Beispiel: 4004332 (GS1) + 85001 (Zähler) + 7 (Prüfziffer) = 4004332850017
     * 
     * @param string $gs1_prefix GS1-Präfix (7-10 Stellen)
     * @return string|false EAN-13 Code oder false
     */
    public static function generateGS1EAN($gs1_prefix = null) {
        if (!self::isAvailable()) {
            return false;
        }
        
        // GS1-Präfix aus Config holen
        if ($gs1_prefix === null) {
            $gs1_prefix = defined('MODULE_BX_MPI_GS1_PREFIX') ? MODULE_BX_MPI_GS1_PREFIX : '';
        }
        
        // Validierung
        if (empty($gs1_prefix) || !ctype_digit($gs1_prefix)) {
            return false;
        }
        
        $prefix_length = strlen($gs1_prefix);
        if ($prefix_length < 7 || $prefix_length > 10) {
            return false;
        }
        
        // Nächste freie Nummer ermitteln
        $counter = self::getNextGS1Counter($gs1_prefix);
        if ($counter === false) {
            return false;
        }
        
        // EAN-12 zusammensetzen (muss 12 Stellen ergeben)
        $counter_length = 12 - $prefix_length;
        $ean12 = $gs1_prefix . str_pad($counter, $counter_length, '0', STR_PAD_LEFT);
        
        // Prüfziffer berechnen
        $sum = 0;
        for ($i = 0; $i < 12; $i++) {
            $sum += ($i % 2 == 0 ? 1 : 3) * (int)$ean12[$i];
        }
        $check = (10 - ($sum % 10)) % 10;
        
        return $ean12 . $check;
    }
    
    /**
     * Hole nächste freie GS1-Zählernummer
     * 
     * @param string $gs1_prefix GS1-Präfix
     * @return int|false Nächste Nummer oder false
     */
    private static function getNextGS1Counter($gs1_prefix) {
        $prefix_length = strlen($gs1_prefix);
        $counter_length = 12 - $prefix_length;
        
        // Finde höchste verwendete Nummer
        $query = xtc_db_query("
            SELECT MAX(CAST(SUBSTRING(ean, " . ($prefix_length + 1) . ", " . $counter_length . ") AS UNSIGNED)) AS max_counter
            FROM " . TABLE_PRODUCT_IDENTIFIERS . "
            WHERE ean LIKE '" . xtc_db_input($gs1_prefix) . "%'
              AND LENGTH(ean) = 13
        ");
        
        if ($row = xtc_db_fetch_array($query)) {
            $max_counter = isset($row['max_counter']) && $row['max_counter'] !== null ? (int)$row['max_counter'] : 0;
            return $max_counter + 1;
        }
        
        return 1; // Start bei 1
    }
}
