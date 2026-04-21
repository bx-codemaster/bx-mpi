<?php
/** --------------------------------------------------------------
 * $Id: admin/bx_mpi.php 16358 2025-11-16 12:00:00Z benax $
 * modified eCommerce Shopsoftware
 * http://www.modified-shop.org
 * 
 * Copyright (c) 2009 - 2013 [www.modified-shop.org]
 * --------------------------------------------------------------
 * based on:
 * (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
 * (c) 2002-2003 osCommercecoding standards www.oscommerce.com
 * (c) 2003	nextcommerce www.nextcommerce.org
 * (c) 2003 XT-Commerce
 * 
 * Released under the GNU General Public License
 * --------------------------------------------------------------
 */

require ('includes/application_top.php');

// Action-Handling MUSS VOR dem HTML-Output stehen!
$action        = isset($_GET['action']) ? $_GET['action'] : '';
$cPath         = isset($_GET['cPath'])  ? $_GET['cPath'] : '';
$identifier_id = isset($_GET['id'])     ? (int)$_GET['id'] : 0;
$page          = isset($_GET['page'])   ? (int)$_GET['page'] : 1;

$debug         = false;

// Speichern
if ($action == 'save' && isset($_POST['identifier_id'])) {
    $id                 = (int)$_POST['identifier_id'];
    $ean                = isset($_POST['ean']) ? trim($_POST['ean']) : '';
    $wws_artikel_nr     = isset($_POST['wws_artikel_nr']) ? trim($_POST['wws_artikel_nr']) : '';
    $wws_system         = isset($_POST['wws_system']) ? trim($_POST['wws_system']) : '';
    $warehouse_location = isset($_POST['warehouse_location']) ? trim($_POST['warehouse_location']) : '';
    
    // Hole alte Werte für Historie
    $old_query = xtc_db_query("SELECT * FROM " . TABLE_PRODUCT_IDENTIFIERS . " WHERE identifier_id = '" . (int)$id . "'");
    $old_data  = xtc_db_fetch_array($old_query);
    
    // Update
    xtc_db_query("UPDATE " . TABLE_PRODUCT_IDENTIFIERS . "
                        SET products_ean = " . (!empty($ean) ? "'" . xtc_db_input($ean) . "'" : "NULL") . ", 
                            wws_artikel_nr = " . (!empty($wws_artikel_nr) ? "'" . xtc_db_input($wws_artikel_nr) . "'" : "NULL") . ", 
                            wws_system = " . (!empty($wws_system) ? "'" . xtc_db_input($wws_system) . "'" : "NULL") . ", 
                            warehouse_location = " . (!empty($warehouse_location) ? "'" . xtc_db_input($warehouse_location) . "'" : "NULL") . "
                        WHERE identifier_id = '" . (int)$id . "'");
    
    // Historie protokollieren
    if (defined('MODULE_BX_MPI_ENABLE_HISTORY') && MODULE_BX_MPI_ENABLE_HISTORY == 'true' && $old_data) {
        $changed_by = isset($_SESSION['customer_id']) ? (int)$_SESSION['customer_id'] : null;
        
        if ($old_data['products_ean'] != $ean) {
            xtc_db_query("INSERT INTO " . TABLE_PRODUCT_IDENTIFIER_HISTORY . "
                (identifier_id, field_name, old_value, new_value, changed_by, changed_by_type, change_reason, changed_at)
                VALUES (
                    '" . (int)$id . "', 'products_ean',
                    " . ($old_data['products_ean'] ? "'" . xtc_db_input($old_data['products_ean']) . "'" : "NULL") . ",
                    " . ($ean ? "'" . xtc_db_input($ean) . "'" : "NULL") . ",
                    " . ($changed_by ? "'$changed_by'" : "NULL") . ",
                    'admin', 'manual_edit', NOW())");
        }
        if ($old_data['wws_artikel_nr'] != $wws_artikel_nr) {
            xtc_db_query("INSERT INTO " . TABLE_PRODUCT_IDENTIFIER_HISTORY . "
                (identifier_id, field_name, old_value, new_value, changed_by, changed_by_type, change_reason, changed_at)
                VALUES (
                    '" . (int)$id . "', 'wws_artikel_nr',
                    " . ($old_data['wws_artikel_nr'] ? "'" . xtc_db_input($old_data['wws_artikel_nr']) . "'" : "NULL") . ",
                    " . ($wws_artikel_nr ? "'" . xtc_db_input($wws_artikel_nr) . "'" : "NULL") . ",
                    " . ($changed_by ? "'$changed_by'" : "NULL") . ",
                    'admin', 'manual_edit', NOW()
                )
            ");
        }
        if ($old_data['warehouse_location'] != $warehouse_location) {
            xtc_db_query("
                INSERT INTO " . TABLE_PRODUCT_IDENTIFIER_HISTORY . "
                (identifier_id, field_name, old_value, new_value, changed_by, changed_by_type, change_reason, changed_at)
                VALUES (
                    '" . (int)$id . "', 'warehouse_location',
                    " . ($old_data['warehouse_location'] ? "'" . xtc_db_input($old_data['warehouse_location']) . "'" : "NULL") . ",
                    " . ($warehouse_location ? "'" . xtc_db_input($warehouse_location) . "'" : "NULL") . ",
                    " . ($changed_by ? "'$changed_by'" : "NULL") . ",
                    'admin', 'manual_edit', NOW()
                )
            ");
        }
    }
    
    $messageStack->add_session(BX_MPI_MSG_IDENTIFIER_UPDATED, 'success');
    
    $redirect_params = xtc_get_all_get_params_include(array('cPath', 'page'));
    xtc_redirect(xtc_href_link(FILENAME_BX_MPI, $redirect_params));
    exit();
}

// Löschen
if ($action == 'delete' && $identifier_id > 0) {
    xtc_db_query("DELETE FROM " . TABLE_PRODUCT_IDENTIFIERS . " WHERE identifier_id = '" . (int)$identifier_id . "'");
    xtc_db_query("DELETE FROM " . TABLE_PRODUCT_IDENTIFIER_ATTRIBUTES . " WHERE identifier_id = '" . (int)$identifier_id . "'");
    xtc_db_query("DELETE FROM " . TABLE_PRODUCT_IDENTIFIER_HISTORY . " WHERE identifier_id = '" . (int)$identifier_id . "'");
    
    $messageStack->add_session(BX_MPI_MSG_IDENTIFIER_DELETED, 'success');
    xtc_redirect(xtc_href_link(FILENAME_BX_MPI));
    exit();
}

// EAN-Pool: CSV-Import verarbeiten
if (isset($_POST['import_block']) && isset($_FILES['csv_file'])) {
  try {
    $csv_file     = $_FILES['csv_file'];
    $block_number = trim($_POST['block_number']);
    $block_size   = (int)$_POST['block_size'];
    $purchased_at = $_POST['purchased_at'];
    $notes        = trim($_POST['notes']);
    
    // Validierung
    if (empty($block_number)) {
      throw new Exception(BX_MPI_ERR_BLOCK_NUMBER_MISSING);
    }
    if (!in_array($block_size, [10, 100, 1000])) {
      throw new Exception(BX_MPI_ERR_INVALID_BLOCK_SIZE);
    }
    if ($csv_file['error'] !== UPLOAD_ERR_OK) {
      throw new Exception(BX_MPI_ERR_FILE_UPLOAD);
    }
    
    // CSV verarbeiten
    $handle = fopen($csv_file['tmp_name'], 'r');
    if (!$handle) {
      throw new Exception(BX_MPI_CSV_FILE_ERROR);
    }
    
    // Header-Zeile lesen
    $header = fgetcsv($handle, 0, ',');
    $gtin_column = array_search('Gtin', $header);
    
    if ($gtin_column === false) {
      // Alternativ: Lowercase suchen
      $gtin_column = array_search('gtin', array_map('strtolower', $header));
      if ($gtin_column === false) {
        fclose($handle);
        throw new Exception(BX_MPI_ERR_COLUMN_NOT_FOUND);
      }
    }
    
    // EANs sammeln
    $eans = array();
    while (($row = fgetcsv($handle, 0, ',')) !== false) {
      if (isset($row[$gtin_column]) && !empty($row[$gtin_column])) {
        $ean = trim($row[$gtin_column]);
        
        // EAN-Format validieren (13 oder 14 Stellen)
        if (preg_match('/^[0-9]{13,14}$/', $ean)) {
            $eans[] = $ean;
        }
      }
    }
    fclose($handle);
    
    $ean_count = count($eans);
    
    if ($ean_count == 0) {
      throw new Exception(BX_MPI_ERR_NO_VALID_EANS);
    }

    // Bereits vergebene EANs aus Identifiers ermitteln
    $assigned_eans = array();
    $ean_chunks    = array_chunk($eans, 200);
    foreach ($ean_chunks as $ean_chunk) {
      $escaped_eans = array();
      foreach ($ean_chunk as $ean) {
        $escaped_eans[] = "'" . xtc_db_input($ean) . "'";
      }

      $assigned_query = xtc_db_query("
        SELECT identifier_id, products_ean
        FROM " . TABLE_PRODUCT_IDENTIFIERS . "
        WHERE products_ean IN (" . implode(',', $escaped_eans) . ")
      ");

      while ($assigned_row = xtc_db_fetch_array($assigned_query)) {
        if (!empty($assigned_row['products_ean'])) {
          $assigned_eans[$assigned_row['products_ean']] = (int)$assigned_row['identifier_id'];
        }
      }
    }

    $already_used_count = count($assigned_eans);
    $block_status = ($already_used_count >= $ean_count) ? 'depleted' : 'active';
    
    // Transaktion starten
    xtc_db_query("START TRANSACTION");
    
    // Block erstellen
    xtc_db_query("
        INSERT INTO " . TABLE_PRODUCT_IDENTIFIER_EAN_BLOCKS . " 
        (block_number, block_size, purchased_at, total_eans, used_eans, status, notes)
        VALUES 
        ('" . xtc_db_input($block_number) . "',
          '" . $block_size . "',
          '" . xtc_db_input($purchased_at) . "',
          '" . $ean_count . "',
              '" . $already_used_count . "',
              '" . $block_status . "',
          '" . xtc_db_input($notes) . "')
    ");
    
    $block_id = xtc_db_insert_id();
    
    // EANs in Pool einfügen (Batch-Insert)
    $batch_size = 100;
    $batches = array_chunk($eans, $batch_size);
    
    foreach ($batches as $batch) {
      $values = array();
      foreach ($batch as $ean) {
        if (isset($assigned_eans[$ean])) {
          $values[] = "('" . $block_id . "', '" . xtc_db_input($ean) . "', 'assigned', '" . (int)$assigned_eans[$ean] . "', NOW())";
        } else {
          $values[] = "('" . $block_id . "', '" . xtc_db_input($ean) . "', 'available', NULL, NULL)";
        }
      }
      
      xtc_db_query("
          INSERT INTO " . TABLE_PRODUCT_IDENTIFIER_EAN_POOL . " 
          (block_id, ean, status, identifier_id, assigned_at)
          VALUES " . implode(',', $values)
      );
    }
    
    xtc_db_query("COMMIT");
    
    $messageStack->add_session(sprintf(BX_MPI_MSG_BLOCK_IMPORTED, htmlspecialchars($block_number), $ean_count), 'success');
    xtc_redirect(xtc_href_link(FILENAME_BX_MPI));
    exit();
      
  } catch (Exception $e) {
    xtc_db_query("ROLLBACK");
    $messageStack->add_session(sprintf(BX_MPI_ERR_IMPORT_FAILED, htmlspecialchars($e->getMessage())), 'error');
    xtc_redirect(xtc_href_link(FILENAME_BX_MPI));
    exit();
  }
}

// EAN-Pool: Block löschen
if (isset($_GET['delete_block'])) {
  $block_id = (int)$_GET['delete_block'];
  
  // Prüfen ob Block EANs in Benutzung hat
  $check_query = xtc_db_query("
      SELECT COUNT(*) as used_count
      FROM " . TABLE_PRODUCT_IDENTIFIER_EAN_POOL . "
      WHERE block_id = '" . $block_id . "'
      AND status = 'assigned'
  ");
  $check = xtc_db_fetch_array($check_query);
  
  if ($check['used_count'] > 0) {
      $messageStack->add_session(sprintf(BX_MPI_ERR_BLOCK_HAS_ASSIGNED_EANS, $check['used_count']), 'error');
  } else {
      xtc_db_query("DELETE FROM " . TABLE_PRODUCT_IDENTIFIER_EAN_BLOCKS . " WHERE block_id = '" . $block_id . "'");
      $messageStack->add_session(BX_MPI_MSG_BLOCK_DELETED, 'success');
  }
  
  xtc_redirect(xtc_href_link(FILENAME_BX_MPI));
  exit();
}

// AJAX-Handler für EAN-Pool-Zuweisung
if (isset($_GET['ajax']) || (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest')) {
    header('Content-Type: application/json');
    
    $ajax_action = isset($_GET['ajax_action']) ? $_GET['ajax_action'] : (isset($_POST['ajax_action']) ? $_POST['ajax_action'] : '');
    
    // AJAX: Einzelne EAN zuweisen
    if ($ajax_action == 'assign_ean' && isset($_POST['identifier_id'])) {
        $identifier_id = (int)$_POST['identifier_id'];
        
        try {
            // Prüfen ob Identifier existiert und noch keine EAN hat
            $check_query = xtc_db_query("
                SELECT identifier_id, products_ean, products_id 
                FROM " . TABLE_PRODUCT_IDENTIFIERS . " 
                WHERE identifier_id = '" . $identifier_id . "'
            ");
            
            if (xtc_db_num_rows($check_query) == 0) {
                throw new Exception(BX_MPI_ERR_IDENTIFIER_NOT_FOUND);
            }
            
            $identifier = xtc_db_fetch_array($check_query);
            
            if (!empty($identifier['products_ean'])) {
                throw new Exception(sprintf(BX_MPI_ERR_IDENTIFIER_HAS_EAN, $identifier['products_ean']));
            }
            
            // Nächste verfügbare EAN aus Pool holen (mit FOR UPDATE Lock)
            xtc_db_query("START TRANSACTION");
            
            $pool_query = xtc_db_query("
                SELECT pool_id, ean, block_id
                FROM " . TABLE_PRODUCT_IDENTIFIER_EAN_POOL . "
                WHERE status = 'available'
                ORDER BY pool_id ASC
                LIMIT 1
                FOR UPDATE
            ");
            
            if (xtc_db_num_rows($pool_query) == 0) {
                xtc_db_query("ROLLBACK");
                throw new Exception(BX_MPI_ERR_NO_POOL_EANS);
            }
            
            $pool_ean = xtc_db_fetch_array($pool_query);
            
            // EAN zuweisen
            xtc_db_query("
                UPDATE " . TABLE_PRODUCT_IDENTIFIER_EAN_POOL . "
                SET status = 'assigned', assigned_at = NOW() WHERE pool_id = '" . $pool_ean['pool_id'] . "'
            ");
            
            // Identifier aktualisieren
            xtc_db_query("
                UPDATE " . TABLE_PRODUCT_IDENTIFIERS . "
                SET products_ean = '" . xtc_db_input($pool_ean['ean']) . "', updated_at = NOW()
                WHERE identifier_id = '" . $identifier['identifier_id'] . "'
            ");
            
            // Historie protokollieren (falls aktiviert)
            if (defined('MODULE_BX_MPI_ENABLE_HISTORY') && MODULE_BX_MPI_ENABLE_HISTORY == 'true') {
                $changed_by = isset($_SESSION['customer_id']) ? (int)$_SESSION['customer_id'] : null;
                xtc_db_query("
                    INSERT INTO " . TABLE_PRODUCT_IDENTIFIER_HISTORY . "
                    (identifier_id, field_name, old_value, new_value, changed_by, changed_by_type, change_reason, changed_at)
                    VALUES (
                        '" . $identifier['identifier_id'] . "', 'ean',
                        NULL,
                        '" . xtc_db_input($pool_ean['ean']) . "',
                        " . ($changed_by ? "'" . $changed_by . "'" : "NULL") . ",
                        'admin', 'pool_assignment', NOW()
                    )
                ");
            }
            
            xtc_db_query("COMMIT");
            
            echo json_encode(array(
                'success'    => true,
                'ean'        => $pool_ean['ean'],
                'block_id'   => $pool_ean['block_id'],
                'identifier_id' => $identifier['identifier_id'],
                'message'    => BX_MPI_MSG_EAN_ASSIGNED
            ));
            
        } catch (Exception $e) {
            xtc_db_query("ROLLBACK");
            echo json_encode(array(
                'success' => false,
                'message' => $e->getMessage()
            ));
        }
        exit();
    }
    
    // AJAX: EAN zurück in Pool legen
    if ($ajax_action == 'release_ean' && isset($_POST['identifier_id'])) {
        $identifier_id = (int)$_POST['identifier_id'];
        
        try {
            // Identifier und zugehörige Pool-EAN laden
            $check_query = xtc_db_query("
                SELECT pi.identifier_id, pi.products_ean, p.pool_id, p.block_id
                FROM " . TABLE_PRODUCT_IDENTIFIERS . " pi
                LEFT JOIN " . TABLE_PRODUCT_IDENTIFIER_EAN_POOL . " p ON pi.products_ean = p.ean AND p.identifier_id = pi.identifier_id
                WHERE pi.identifier_id = '" . $identifier_id . "'
            ");
            
            if (xtc_db_num_rows($check_query) == 0) {
                throw new Exception(BX_MPI_ERR_IDENTIFIER_NOT_FOUND);
            }
            
            $identifier = xtc_db_fetch_array($check_query);
            
            if (empty($identifier['products_ean'])) {
                throw new Exception(BX_MPI_ERR_IDENTIFIER_NO_EAN);
            }
            
            xtc_db_query("START TRANSACTION");
            
            // EAN aus Identifier entfernen
            xtc_db_query("
                UPDATE " . TABLE_PRODUCT_IDENTIFIERS . "
                SET products_ean = NULL,
                    updated_at = NOW()
                WHERE identifier_id = '" . $identifier_id . "'
            ");
            
            // Falls EAN aus Pool: Zurück in Pool legen
            if (!empty($identifier['pool_id'])) {
                xtc_db_query("
                    UPDATE " . TABLE_PRODUCT_IDENTIFIER_EAN_POOL . "
                    SET status = 'available',
                        identifier_id = NULL,
                        assigned_at = NULL
                    WHERE pool_id = '" . $identifier['pool_id'] . "'
                ");
            }
            
            // Historie protokollieren
            if (defined('MODULE_BX_MPI_ENABLE_HISTORY') && MODULE_BX_MPI_ENABLE_HISTORY == 'true') {
                $changed_by = isset($_SESSION['customer_id']) ? (int)$_SESSION['customer_id'] : null;
                xtc_db_query("
                    INSERT INTO " . TABLE_PRODUCT_IDENTIFIER_HISTORY . "
                    (identifier_id, field_name, old_value, new_value, changed_by, changed_by_type, change_reason, changed_at)
                    VALUES (
                        '" . $identifier['identifier_id'] . "', 'ean',
                        '" . xtc_db_input($identifier['products_ean']) . "',
                        NULL,
                        " . ($changed_by ? "'" . $changed_by . "'" : "NULL") . ",
                        'admin', 'pool_release', NOW()
                    )
                ");
            }
            
            xtc_db_query("COMMIT");
            
            echo json_encode(array(
                'success' => true,
                'ean' => $identifier['products_ean'],
                'identifier_id' => $identifier['identifier_id'],
                'message' => BX_MPI_MSG_EAN_RELEASED
            ));
            
        } catch (Exception $e) {
            xtc_db_query("ROLLBACK");
            echo json_encode(array(
                'success' => false,
                'message' => $e->getMessage()
            ));
        }
        exit();
    }
    
    // AJAX: Bulk-Zuweisung für alle Identifier eines Produkts
    if ($ajax_action == 'assign_bulk' && isset($_POST['product_id'])) {
        $product_id = (int)$_POST['product_id'];
        
        try {
            // Alle Identifiers ohne EAN für dieses Produkt laden
            $identifiers_query = xtc_db_query("
                SELECT identifier_id, products_sku
                FROM " . TABLE_PRODUCT_IDENTIFIERS . "
                WHERE products_id = '" . $product_id . "'
                AND (products_ean IS NULL OR products_ean = '')
                ORDER BY identifier_id ASC
            ");
            
            $identifiers = array();
            while ($row = xtc_db_fetch_array($identifiers_query)) {
                $identifiers[] = $row;
            }
            
            if (empty($identifiers)) {
                throw new Exception(BX_MPI_ERR_NO_IDENTIFIERS_WITHOUT_EAN);
            }
            
            xtc_db_query("START TRANSACTION");
            
            $assigned_count = 0;
            $assigned_eans  = array();
            
            foreach ($identifiers as $identifier) {
                // Nächste verfügbare EAN aus Pool holen
                $pool_query = xtc_db_query("
                    SELECT pool_id, ean, block_id
                    FROM " . TABLE_PRODUCT_IDENTIFIER_EAN_POOL . "
                    WHERE status = 'available'
                    ORDER BY pool_id ASC
                    LIMIT 1
                    FOR UPDATE
                ");
                
                if (xtc_db_num_rows($pool_query) == 0) {
                    // Pool leer - restliche überspringen
                    break;
                }
                
                $pool_ean = xtc_db_fetch_array($pool_query);
                
                // EAN zuweisen
                xtc_db_query("
                    UPDATE " . TABLE_PRODUCT_IDENTIFIER_EAN_POOL . "
                    SET status = 'assigned',
                        identifier_id = '" . $identifier['identifier_id'] . "',
                        assigned_at = NOW()
                    WHERE pool_id = '" . $pool_ean['pool_id'] . "'
                ");
                
                // Identifier aktualisieren
                xtc_db_query("
                    UPDATE " . TABLE_PRODUCT_IDENTIFIERS . "
                    SET products_ean = '" . xtc_db_input($pool_ean['ean']) . "',
                        updated_at = NOW()
                    WHERE identifier_id = '" . $identifier['identifier_id'] . "'
                ");
                
                // Historie
                if (defined('MODULE_BX_MPI_ENABLE_HISTORY') && MODULE_BX_MPI_ENABLE_HISTORY == 'true') {
                    $changed_by = isset($_SESSION['customer_id']) ? (int)$_SESSION['customer_id'] : null;
                    xtc_db_query("
                        INSERT INTO " . TABLE_PRODUCT_IDENTIFIER_HISTORY . "
                        (identifier_id, field_name, old_value, new_value, changed_by, changed_by_type, change_reason, changed_at)
                        VALUES (
                            '" . $identifier['identifier_id'] . "', 'ean',
                            NULL,
                            '" . xtc_db_input($pool_ean['ean']) . "',
                            " . ($changed_by ? "'" . $changed_by . "'" : "NULL") . ",
                            'admin', 'pool_bulk_assignment', NOW()
                        )
                    ");
                }
                
                $assigned_count++;
                $assigned_eans[] = $pool_ean['ean'];
            }
            
            xtc_db_query("COMMIT");
            
            echo json_encode(array(
                'success' => true,
                'assigned_count' => $assigned_count,
                'eans' => $assigned_eans,
                'message' => sprintf(BX_MPI_MSG_EANS_ASSIGNED, $assigned_count)
            ));
            
        } catch (Exception $e) {
            xtc_db_query("ROLLBACK");
            echo json_encode(array(
                'success' => false,
                'message' => $e->getMessage()
            ));
        }
        exit();
    }
    
    // AJAX: Pool-Status abfragen
    if ($ajax_action == 'pool_status') {
        $pool_query = xtc_db_query("
            SELECT COALESCE(SUM(available_eans), 0) as available
            FROM " . TABLE_PRODUCT_IDENTIFIER_EAN_BLOCKS . "
            WHERE status = 'active'
        ");
        $pool_data = xtc_db_fetch_array($pool_query);
        
        echo json_encode(array(
            'success' => true,
            'available' => (int)$pool_data['available']
        ));
        exit();
    }
    
    // AJAX: EAN für Einfachprodukt zuweisen (ohne Attribute)
    if ($ajax_action == 'assign_simple_ean' && isset($_POST['product_id'])) {
        $product_id = (int)$_POST['product_id'];
        $ean_source = isset($_POST['ean_source']) ? $_POST['ean_source'] : 'pool';
        $manual_ean = isset($_POST['manual_ean']) ? trim($_POST['manual_ean']) : '';
        
        try {
            if (!class_exists('ProductIdentifier')) {
                require_once(DIR_WS_INCLUDES . 'classes/ProductIdentifier.php');
            }
            
            // Basis-Identifier holen oder erstellen (leere attribute_ids = Einfachprodukt)
            $identifier = ProductIdentifier::getIdentifier($product_id, array());

            if (!$identifier) {
                // Identifier erstellen
                $sku = ProductIdentifier::createSKU($product_id, array());
                if (!$sku) {
                    // Debug: Warum schlägt createSKU fehl?
                    $debug_product = xtc_db_query("SELECT * FROM " . TABLE_PRODUCTS . " WHERE products_id = '" . (int)$product_id . "'");
                    if (xtc_db_num_rows($debug_product) == 0) {
                        throw new Exception("Produkt mit ID $product_id existiert nicht in der Datenbank");
                    }
                    
                    // Prüfe ob Tabelle existiert
                    $debug_table = xtc_db_query("SHOW TABLES LIKE '" . TABLE_PRODUCT_IDENTIFIERS . "'");
                    if (xtc_db_num_rows($debug_table) == 0) {
                        throw new Exception("Tabelle " . TABLE_PRODUCT_IDENTIFIERS . " existiert nicht");
                    }
                    
                    throw new Exception(BX_MPI_ERR_SKU_CREATION_FAILED . " (createSKU returns false)");
                }
                $identifier = ProductIdentifier::getIdentifier($product_id, array());
            }
            
            if (!$identifier) {
                throw new Exception(BX_MPI_ERR_IDENTIFIER_CREATION_FAILED);
            }
            
            $identifier_id = (int)$identifier['identifier_id'];
            $old_ean = $identifier['products_ean'];
            
            xtc_db_query("START TRANSACTION");
            
            // Falls alte EAN aus Pool: zurück in Pool legen
            if (!empty($old_ean)) {
                $old_pool_check = xtc_db_query("
                    SELECT pool_id FROM " . TABLE_PRODUCT_IDENTIFIER_EAN_POOL . "
                    WHERE ean = '" . xtc_db_input($old_ean) . "'
                    AND identifier_id = '" . $identifier_id . "'
                ");
                
                if (xtc_db_num_rows($old_pool_check) > 0) {
                    $old_pool = xtc_db_fetch_array($old_pool_check);
                    xtc_db_query("
                        UPDATE " . TABLE_PRODUCT_IDENTIFIER_EAN_POOL . "
                        SET status = 'available',
                            identifier = NULL,
                            assigned_at = NULL
                        WHERE pool_id = '" . $old_pool['pool_id'] . "'
                    ");
                }
            }
            
            $assigned_ean = null;
            
            // EAN zuweisen je nach Quelle
            if ($ean_source == 'pool') {
                $pool_query = xtc_db_query("
                    SELECT pool_id, ean, block_id
                    FROM " . TABLE_PRODUCT_IDENTIFIER_EAN_POOL . "
                    WHERE status = 'available'
                    ORDER BY pool_id ASC
                    LIMIT 1
                    FOR UPDATE
                ");
                
                if (xtc_db_num_rows($pool_query) == 0) {
                    throw new Exception(BX_MPI_ERR_NO_POOL_EANS);
                }
                
                $pool_ean = xtc_db_fetch_array($pool_query);
                
                xtc_db_query("
                    UPDATE " . TABLE_PRODUCT_IDENTIFIER_EAN_POOL . "
                    SET status = 'assigned',
                        identifier = '" . $identifier_id . "',
                        assigned_at = NOW()
                    WHERE pool_id = '" . $pool_ean['pool_id'] . "'
                ");
                
                xtc_db_query("
                    UPDATE " . TABLE_PRODUCT_IDENTIFIERS . "
                    SET products_ean = '" . xtc_db_input($pool_ean['ean']) . "',
                        updated_at = NOW()
                    WHERE identifier_id = '" . $identifier_id . "'
                ");
                
                $assigned_ean = $pool_ean['ean'];
                
            } elseif ($ean_source == 'pseudo') {
                $pseudo_ean = ProductIdentifier::generatePseudoEAN($identifier_id);
                
                if (!$pseudo_ean) {
                    throw new Exception(BX_MPI_ERR_PSEUDO_EAN_FAILED);
                }
                
                xtc_db_query("
                    UPDATE " . TABLE_PRODUCT_IDENTIFIERS . "
                    SET products_ean = '" . xtc_db_input($pseudo_ean) . "',
                        updated_at = NOW()
                    WHERE identifier_id = '" . $identifier_id . "'
                ");
                
                $assigned_ean = $pseudo_ean;
                
            } elseif ($ean_source == 'manual' && !empty($manual_ean)) {
                if (!preg_match('/^[0-9]{13,14}$/', $manual_ean)) {
                    throw new Exception(BX_MPI_ERR_INVALID_EAN_FORMAT);
                }
                
                // Prüfen ob EAN bereits vergeben
                $ean_check = xtc_db_query("
                    SELECT identifier_id FROM " . TABLE_PRODUCT_IDENTIFIERS . "
                    WHERE products_ean = '" . xtc_db_input($manual_ean) . "'
                    AND identifier_id != '" . $identifier_id . "'
                ");
                
                if (xtc_db_num_rows($ean_check) > 0) {
                    throw new Exception(BX_MPI_ERR_EAN_ALREADY_ASSIGNED);
                }
                
                xtc_db_query("
                    UPDATE " . TABLE_PRODUCT_IDENTIFIERS . "
                    SET products_ean = '" . xtc_db_input($manual_ean) . "',
                        updated_at = NOW()
                    WHERE identifier_id = '" . $identifier_id . "'
                ");
                
                $assigned_ean = $manual_ean;
            }
            
            // Historie protokollieren
            if (defined('MODULE_BX_MPI_ENABLE_HISTORY') && MODULE_BX_MPI_ENABLE_HISTORY == 'true' && $assigned_ean) {
                $changed_by = isset($_SESSION['customer_id']) ? (int)$_SESSION['customer_id'] : null;
                xtc_db_query("
                    INSERT INTO " . TABLE_PRODUCT_IDENTIFIER_HISTORY . "
                    (identifier_id, field_name, old_value, new_value, changed_by, changed_by_type, change_reason, changed_at)
                    VALUES (
                        '" . $identifier_id . "', 'ean',
                        " . ($old_ean ? "'" . xtc_db_input($old_ean) . "'" : "NULL") . ",
                        '" . xtc_db_input($assigned_ean) . "',
                        " . ($changed_by ? "'" . $changed_by . "'" : "NULL") . ",
                        'admin', 'manual_edit', NOW()
                    )
                ");
            }
            
            // WICHTIG: Auch products.products_ean aktualisieren (für Einfachprodukte)
            xtc_db_query("
                UPDATE " . TABLE_PRODUCTS . "
                SET products_ean = '" . xtc_db_input($assigned_ean) . "'
                WHERE products_id = '" . $product_id . "'
            ");
            
            xtc_db_query("COMMIT");
            
            echo json_encode(array(
                'success' => true,
                'ean' => $assigned_ean,
                'identifier_id' => $identifier_id,
                'message' => BX_MPI_MSG_EAN_ASSIGNED
            ));
            
        } catch (Exception $e) {
            xtc_db_query("ROLLBACK");
            echo json_encode(array(
                'success' => false,
                'message' => $e->getMessage()
            ));
        }
        exit();
    }
    
    // AJAX: EAN für Einfachprodukt entfernen
    if ($ajax_action == 'release_simple_ean' && isset($_POST['product_id'])) {
        $product_id = (int)$_POST['product_id'];
        
        try {
            if (!class_exists('ProductIdentifier')) {
                require_once(DIR_WS_INCLUDES . 'classes/ProductIdentifier.php');
            }
            
            $identifier = ProductIdentifier::getIdentifier($product_id, array());
            
            if (!$identifier) {
                throw new Exception(BX_MPI_ERR_IDENTIFIER_NOT_FOUND);
            }
            
            if (empty($identifier['products_ean'])) {
                throw new Exception(BX_MPI_ERR_IDENTIFIER_NO_EAN);
            }
            
            $identifier_id = (int)$identifier['identifier_id'];
            $old_ean = $identifier['products_ean'];
            
            xtc_db_query("START TRANSACTION");
            
            // EAN aus Identifier entfernen
            xtc_db_query("
                UPDATE " . TABLE_PRODUCT_IDENTIFIERS . "
                SET products_ean = NULL,
                    updated_at = NOW()
                WHERE identifier_id = '" . $identifier_id . "'
            ");
            
            // Falls EAN aus Pool: zurück in Pool legen
            $pool_check = xtc_db_query("
                SELECT pool_id FROM " . TABLE_PRODUCT_IDENTIFIER_EAN_POOL . "
                WHERE ean = '" . xtc_db_input($old_ean) . "'
                AND identifier_id = '" . $identifier_id . "'
            ");
            
            if (xtc_db_num_rows($pool_check) > 0) {
                $pool_row = xtc_db_fetch_array($pool_check);
                xtc_db_query("
                    UPDATE " . TABLE_PRODUCT_IDENTIFIER_EAN_POOL . "
                    SET status = 'available',
                        identifier_id = NULL,
                        assigned_at = NULL
                    WHERE pool_id = '" . $pool_row['pool_id'] . "'
                ");
            }
            
            // Historie protokollieren
            if (defined('MODULE_BX_MPI_ENABLE_HISTORY') && MODULE_BX_MPI_ENABLE_HISTORY == 'true') {
                $changed_by = isset($_SESSION['customer_id']) ? (int)$_SESSION['customer_id'] : null;
                xtc_db_query("
                    INSERT INTO " . TABLE_PRODUCT_IDENTIFIER_HISTORY . "
                    (identifier_id, field_name, old_value, new_value, changed_by, changed_by_type, change_reason, changed_at)
                    VALUES (
                        '" . $identifier_id . "', 'ean',
                        '" . xtc_db_input($old_ean) . "',
                        NULL,
                        " . ($changed_by ? "'" . $changed_by . "'" : "NULL") . ",
                        'admin', 'manual_edit', NOW()
                    )
                ");
            }
            
            // WICHTIG: Auch products.products_ean löschen (für Einfachprodukte)
            xtc_db_query("
                UPDATE " . TABLE_PRODUCTS . "
                SET products_ean = NULL
                WHERE products_id = '" . $product_id . "'
            ");
            
            xtc_db_query("COMMIT");
            
            echo json_encode(array(
                'success' => true,
                'ean' => null,
                'identifier_id' => $identifier_id,
                'message' => BX_MPI_MSG_EAN_REMOVED
            ));
            
        } catch (Exception $e) {
            xtc_db_query("ROLLBACK");
            echo json_encode(array(
                'success' => false,
                'message' => $e->getMessage()
            ));
        }
        exit();
    }
    
    // AJAX: Varianten speichern mit EAN-Zuweisung
    if ($ajax_action == 'save_variants' && isset($_POST['product_id']) && isset($_POST['variants'])) {
      $product_id    = (int)$_POST['product_id'];
      $variants_json = $_POST['variants'];
      
      try {
        // ProductIdentifier-Klasse laden
        if (!class_exists('ProductIdentifier')) {
            require_once(DIR_WS_INCLUDES . 'classes/ProductIdentifier.php');
        }
        
        $variants = json_decode($variants_json, true);
        if (!is_array($variants)) {
          throw new Exception(BX_MPI_ERR_INVALID_VARIANT_DATA);
        }
        
        xtc_db_query("START TRANSACTION");
        
        $saved_count = 0;
        $results = array();
          
        foreach ($variants as $variant) {
          // Attribute-IDs sind vom JSON-Decode als Strings - zu Integers konvertieren
          $attribute_ids = array();
          if (!empty($variant['attribute_ids']) && is_array($variant['attribute_ids'])) {
            foreach ($variant['attribute_ids'] as $opt_id => $val_id) {
                $attribute_ids[(int)$opt_id] = (int)$val_id;
            }
          }
            
          $ean_source = $variant['ean_source'];
          $manual_ean = isset($variant['manual_ean']) ? trim($variant['manual_ean']) : '';
          
          // SKU erstellen (auto-save in DB)
          $sku = ProductIdentifier::createSKU($product_id, $attribute_ids);
          
          if (!$sku) {
            throw new Exception(BX_MPI_ERR_SKU_CREATION_VARIANT_FAILED);
          }
          
          // Identifier-ID holen (direkt per SQL, da getIdentifierId private ist)
          // WICHTIG: ksort() muss vorher aufgerufen werden, um den gleichen Hash wie in createSKU() zu bekommen!
          $attr_for_hash = $attribute_ids;
          ksort($attr_for_hash);
          $attributes_hash = md5(serialize($attr_for_hash));
          
          $id_query = xtc_db_query("
              SELECT identifier_id 
              FROM " . TABLE_PRODUCT_IDENTIFIERS . "
              WHERE products_id = '" . $product_id . "'
              AND attributes_hash = '" . xtc_db_input($attributes_hash) . "'
          ");
          
          if (xtc_db_num_rows($id_query) == 0) {
            throw new Exception(BX_MPI_ERR_IDENTIFIER_ID_NOT_FOUND);
          }
            
          $id_row = xtc_db_fetch_array($id_query);
          $identifier_id = (int)$id_row['identifier_id'];
          
          // Alte EAN laden (falls vorhanden)
          $old_ean_query = xtc_db_query("
              SELECT products_ean FROM " . TABLE_PRODUCT_IDENTIFIERS . "
              WHERE identifier_id = '" . $identifier_id . "'
          ");
          $old_ean_row = xtc_db_fetch_array($old_ean_query);
          $old_ean = $old_ean_row ? $old_ean_row['products_ean'] : null;
            
          // Falls alte EAN aus Pool stammt: Zurück in Pool legen
          if (!empty($old_ean)) {
            $old_pool_check = xtc_db_query("
                SELECT pool_id FROM " . TABLE_PRODUCT_IDENTIFIER_EAN_POOL . "
                WHERE ean = '" . xtc_db_input($old_ean) . "'
                AND identifier_id = '" . $identifier_id . "'
            ");
            
            if (xtc_db_num_rows($old_pool_check) > 0) {
              // Pool-EAN zurückgeben
              xtc_db_query("
                  UPDATE " . TABLE_PRODUCT_IDENTIFIER_EAN_POOL . "
                  SET status = 'available',
                      identifier_id = NULL,
                      assigned_at = NULL
                  WHERE ean = '" . xtc_db_input($old_ean) . "'
                  AND identifier_id = '" . $identifier_id . "'
              ");
            }
          }
            
          // EAN zuweisen je nach Quelle
          $assigned_ean = null;
            
            if ($ean_source == 'pool') {
              // EAN aus Pool holen
              $pool_query = xtc_db_query("
                  SELECT pool_id, ean, block_id
                  FROM " . TABLE_PRODUCT_IDENTIFIER_EAN_POOL . "
                  WHERE status = 'available'
                  ORDER BY pool_id ASC
                  LIMIT 1
                  FOR UPDATE
              ");
                
              if (xtc_db_num_rows($pool_query) > 0) {
                  $pool_ean = xtc_db_fetch_array($pool_query);
                  
                  // Pool-EAN zuweisen
                  xtc_db_query("
                      UPDATE " . TABLE_PRODUCT_IDENTIFIER_EAN_POOL . "
                      SET status = 'assigned',
                          identifier_id = '" . $identifier_id . "',
                          assigned_at = NOW()
                      WHERE pool_id = '" . $pool_ean['pool_id'] . "'
                  ");
                  
                  // Identifier aktualisieren
                  xtc_db_query("
                      UPDATE " . TABLE_PRODUCT_IDENTIFIERS . "
                      SET products_ean = '" . xtc_db_input($pool_ean['ean']) . "',
                          updated_at = NOW()
                      WHERE identifier_id = '" . $identifier_id . "'
                  ");
                  
                  $assigned_ean = $pool_ean['ean'];
              }
                
            } elseif ($ean_source == 'pseudo') {
              // Pseudo-EAN generieren
              $pseudo_ean = ProductIdentifier::generatePseudoEAN($identifier_id);
              
              if ($pseudo_ean) {
                xtc_db_query("
                    UPDATE " . TABLE_PRODUCT_IDENTIFIERS . "
                    SET products_ean = '" . xtc_db_input($pseudo_ean) . "',
                        updated_at = NOW()
                    WHERE identifier_id = '" . $identifier_id . "'
                ");
                
                $assigned_ean = $pseudo_ean;
              }
                
            } elseif ($ean_source == 'manual' && !empty($manual_ean)) {
              // Manuelle EAN validieren
              if (!preg_match('/^[0-9]{13,14}$/', $manual_ean)) {
                throw new Exception(sprintf(BX_MPI_ERR_INVALID_EAN_FORMAT_VALUE, $manual_ean));
              }
              
              // Duplikat-Check
              $dup_check = xtc_db_query("
                  SELECT identifier_id FROM " . TABLE_PRODUCT_IDENTIFIERS . "
                  WHERE ean = '" . xtc_db_input($manual_ean) . "'
                  AND identifier_id != '" . $identifier_id . "'
              ");
              
              if (xtc_db_num_rows($dup_check) > 0) {
                throw new Exception(sprintf(BX_MPI_ERR_EAN_ALREADY_ASSIGNED_VALUE, $manual_ean));
              }
              
              xtc_db_query("
                  UPDATE " . TABLE_PRODUCT_IDENTIFIERS . "
                  SET products_ean = '" . xtc_db_input($manual_ean) . "',
                      updated_at = NOW()
                  WHERE identifier_id = '" . $identifier_id . "'
              ");
              
              $assigned_ean = $manual_ean;

            } elseif ($ean_source == 'none') {
              // Keine EAN zuweisen
              xtc_db_query("
                  UPDATE " . TABLE_PRODUCT_IDENTIFIERS . "
                  SET products_ean = NULL,
                      updated_at = NOW()
                  WHERE identifier_id = '" . $identifier_id . "'
              ");
              $assigned_ean = null;
            }
            
            // Historie protokollieren (falls aktiviert)
            if (defined('MODULE_BX_MPI_ENABLE_HISTORY') && MODULE_BX_MPI_ENABLE_HISTORY == 'true') {
                // Nur loggen wenn sich der Wert tatsächlich geändert hat (auch NULL-Änderungen)
                if ($old_ean !== $assigned_ean) {
                    $changed_by = isset($_SESSION['customer_id']) ? (int)$_SESSION['customer_id'] : null;
                    $reason = 'variant_generator_' . $ean_source;
                    
                    xtc_db_query("
                        INSERT INTO " . TABLE_PRODUCT_IDENTIFIER_HISTORY . "
                        (identifier_id, field_name, old_value, new_value, changed_by, changed_by_type, change_reason, changed_at)
                        VALUES (
                            '" . $identifier_id . "', 'ean',
                            " . ($old_ean ? "'" . xtc_db_input($old_ean) . "'" : "NULL") . ",
                            " . ($assigned_ean ? "'" . xtc_db_input($assigned_ean) . "'" : "NULL") . ",
                            " . ($changed_by ? "'" . $changed_by . "'" : "NULL") . ",
                            'admin', '" . xtc_db_input($reason) . "', NOW()
                        )
                    ");
                }
            }
            
            $saved_count++;
            $results[] = array(
                'row' => isset($variant['row']) ? (int)$variant['row'] : 0,
                'sku' => $sku,
                'ean' => $assigned_ean,
                'identifier_id' => $identifier_id,
                'ean_type' => $ean_source
            );
        }
          
          xtc_db_query("COMMIT");
          
          echo json_encode(array(
              'success' => true,
              'saved_count' => $saved_count,
              'results' => $results,
              'message' => sprintf(BX_MPI_MSG_VARIANTS_SAVED, $saved_count)
          ));
          
      } catch (Exception $e) {
          xtc_db_query("ROLLBACK");
          echo json_encode(array(
              'success' => false,
              'message' => $e->getMessage()
          ));
      }
      exit();
    }
    
    // Unbekannte AJAX-Action
    echo json_encode(array(
        'success' => false,
        'message' => BX_MPI_ERR_UNKNOWN_AJAX_ACTION
    ));
    exit();
}

// Identifier für Bearbeitung laden
$edit_identifier = null;
if ($action == 'edit' && $identifier_id > 0) {
  $query = xtc_db_query("
      SELECT pi.*, 
              p.products_model, 
              pd.products_name 
        FROM " . TABLE_PRODUCT_IDENTIFIERS . " pi 
        LEFT JOIN " . TABLE_PRODUCTS . " p ON pi.products_id = p.products_id 
        LEFT JOIN " . TABLE_PRODUCTS_DESCRIPTION . " pd ON p.products_id = pd.products_id AND pd.language_id = '" . (int)$_SESSION['languages_id'] . "'
        WHERE pi.identifier_id = '$identifier_id'");
  
  $edit_identifier = xtc_db_fetch_array($query);
  
  // Attribute laden
  if ($edit_identifier) {
    $attr_query = xtc_db_query("
        SELECT pia.*, 
                po.products_options_name, 
                pov.products_options_values_name
          FROM " . TABLE_PRODUCT_IDENTIFIER_ATTRIBUTES . " pia 
          LEFT JOIN " . TABLE_PRODUCTS_OPTIONS . " po 
              ON pia.products_options_id = po.products_options_id AND po.language_id = '" . (int)$_SESSION['languages_id'] . "'
          LEFT JOIN " . TABLE_PRODUCTS_OPTIONS_VALUES . " pov 
              ON pia.products_options_values_id = pov.products_options_values_id AND pov.language_id = '" . (int)$_SESSION['languages_id'] . "'
          WHERE pia.identifier_id = '$identifier_id'
          ORDER BY pia.sort_order");
    
    $edit_identifier['attributes'] = array();
    
    while ($attr = xtc_db_fetch_array($attr_query)) {
      $edit_identifier['attributes'][] = $attr;
    }
  }
}
              
// Speichern-Aktion
if (isset($_POST['save_settings'])) {
    // Liste der zu speichernden Einstellungen
    $config_keys = array(
        'MODULE_BX_MPI_AUTO_CREATE',
        'MODULE_BX_MPI_SKU_SEPARATOR',
        'MODULE_BX_MPI_SKU_MODE',
        'MODULE_BX_MPI_SKU_PREFIX',
        'MODULE_BX_MPI_ENABLE_HISTORY',
        'MODULE_BX_MPI_EAN_MODE',
        'MODULE_BX_MPI_GS1_PREFIX'
    );
    
    foreach ($config_keys as $key) {
        if (isset($_POST[$key])) {
            $value = xtc_db_input($_POST[$key]);
            xtc_db_query("UPDATE " . TABLE_CONFIGURATION . " 
                          SET configuration_value = '" . $value . "',
                              last_modified = NOW()
                          WHERE configuration_key = '" . $key . "'");
        }
    }
    
    $messageStack->add_session(BX_MPI_SETTINGS_SAVED, 'success');
    xtc_redirect(xtc_href_link(FILENAME_BX_MPI));
    exit();
}

require_once (DIR_WS_INCLUDES.'head.php');

// Display per page for MPI overview
$cfg_max_display_mpi_key = 'MAX_DISPLAY_MPI_RESULTS';
$page_max_display_mpi = xtc_cfg_save_max_display_results($cfg_max_display_mpi_key);

$cfg_max_display_history_key = 'MAX_DISPLAY_MPI_RESULTS';
$page_max_display_history = xtc_cfg_save_max_display_results($cfg_max_display_history_key);

// Dashboard-Statistiken holen
$stats = array();

// Anzahl Identifiers
$query = xtc_db_query("SELECT COUNT(*) as total FROM " . TABLE_PRODUCT_IDENTIFIERS);
$row = xtc_db_fetch_array($query);
$stats['total_identifiers'] = (int)$row['total'];

// Anzahl mit EAN
$query = xtc_db_query("SELECT COUNT(*) as total FROM " . TABLE_PRODUCT_IDENTIFIERS . " WHERE products_ean IS NOT NULL AND products_ean != ''");
$row = xtc_db_fetch_array($query);
$stats['with_ean'] = (int)$row['total'];

// Anzahl ohne EAN
$stats['without_ean'] = $stats['total_identifiers'] - $stats['with_ean'];

// Pseudo-EAN Anzahl (Prefix 2)
$query = xtc_db_query("SELECT COUNT(*) as total FROM " . TABLE_PRODUCT_IDENTIFIERS . " WHERE products_ean LIKE '2%'");
$row = xtc_db_fetch_array($query);
$stats['pseudo_ean'] = (int)$row['total'];

// GS1-EAN Anzahl (nicht Prefix 2)
$query = xtc_db_query("SELECT COUNT(*) as total FROM " . TABLE_PRODUCT_IDENTIFIERS . " WHERE products_ean IS NOT NULL AND products_ean NOT LIKE '2%' AND products_ean != ''");
$row = xtc_db_fetch_array($query);
$stats['gs1_ean'] = (int)$row['total'];

// Letzte 5 Aktivitäten aus Historie
$latest_activities = array();
if (defined('MODULE_BX_MPI_ENABLE_HISTORY') && MODULE_BX_MPI_ENABLE_HISTORY == 'true') {
    $query = xtc_db_query("
        SELECT h.*, p.products_sku, p.products_id
        FROM " . TABLE_PRODUCT_IDENTIFIER_HISTORY . " h
        LEFT JOIN " . TABLE_PRODUCT_IDENTIFIERS . " p ON h.identifier_id = p.identifier_id
        ORDER BY h.changed_at DESC
        LIMIT 5
    ");
    while ($row = xtc_db_fetch_array($query)) {
        $latest_activities[] = $row;
    }
}

// Konfiguration
$config = array(
    'status'          => defined('MODULE_BX_MPI_STATUS') ? MODULE_BX_MPI_STATUS : 'false',
    'auto_create'     => defined('MODULE_BX_MPI_AUTO_CREATE') ? MODULE_BX_MPI_AUTO_CREATE : 'false',
    'ean_mode'        => defined('MODULE_BX_MPI_EAN_MODE') ? MODULE_BX_MPI_EAN_MODE : 'manual',
    'history_enabled' => defined('MODULE_BX_MPI_ENABLE_HISTORY') ? MODULE_BX_MPI_ENABLE_HISTORY : 'false',
);

$messageStack->output();
?>
</head>
<!-- header //-->
<?php require(DIR_WS_INCLUDES.'header.php'); ?>

<!-- header_eof //-->
<!-- body //-->
<table class="tableBody">
  <tr>
    <?php //left_navigation
    if (USE_ADMIN_TOP_MENU == 'false') {
      echo '<td class="columnLeft2">'.PHP_EOL;
      echo '<!-- left_navigation //-->'.PHP_EOL;
      require_once(DIR_WS_INCLUDES.'column_left.php');
      echo '<!-- left_navigation eof //-->'.PHP_EOL;
      echo '</td>'.PHP_EOL;
    }
    ?>
    <!-- body_text //-->
    <td class="boxCenter">
      <div class="pageHeadingImage" style="min-width: 45px;"><?php echo xtc_image(DIR_WS_ICONS.'heading/bx_mpi.png', 'BX Modified Product Identifier', '', '', 'style="max-height: 32px;"'); ?></div>
      <div class="pageHeading flt-l">
        <?php echo BX_MPI_PAGE_TITLE; ?>
        <div class="main pdg2">
          <?php echo BX_MPI_PAGE_DESCRIPTION; ?>
        </div>
      </div>
      <div class="clear"></div>

      <table class="tableCenter" style="margin-top: 5px;">
        <tr>
          <td class="boxCenterLeft">

            <div class="main" style="display: flex; flex-direction: row; justify-content: left; align-items: center; background: #AF417E; color: #ffffff; border-radius: 4px; margin: 5px 0px; padding: 4px 0 2px 0;">
              <div class="main" style="margin: 5px 10px;"><strong>BX MPI</strong></div>
              <div class="main" style="margin: 5px 10px;">&nbsp;</div>
            </div>

            <div class="tabs">
              <ul class="tab-nav">
                <li><a href="#tab-dashboard"><?php echo BX_MPI_TAB_DASHBOARD; ?></a></li>
                <li><a href="#tab-admin"><?php echo BX_MPI_TAB_ADMIN; ?></a></li>
                <li><a href="#tab-gtin"><?php echo BX_MPI_TAB_POOL; ?></a></li>
                <li><a href="#tab-history"><?php echo BX_MPI_TAB_HISTORY; ?></a></li>
                <li><a href="#tab-settings"><?php echo BX_MPI_TAB_SETTINGS; ?></a></li>
              </ul>

              <div class="tab-content">

                <div id="tab-dashboard">
                  
                  <!-- Statistik-Karten //-->
                  <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px; margin-bottom: 20px;">
                    
                    <!-- Karte: Gesamt Identifiers //-->
                    <div style="background: #fff; border: 1px solid #ddd; border-radius: 4px; padding: 15px; text-align: center;">
                      <div style="font-size: 32px; font-weight: bold; color: #AF417E;"><?php echo $stats['total_identifiers']; ?></div>
                      <div style="color: #666; margin-top: 5px;"><?php echo BX_MPI_DASHBOARD_TOTAL_IDENTIFIERS; ?></div>
                    </div>
                    
                    <!-- Karte: Mit EAN //-->
                    <div style="background: #fff; border: 1px solid #ddd; border-radius: 4px; padding: 15px; text-align: center;">
                      <div style="font-size: 32px; font-weight: bold; color: #28a745;"><?php echo $stats['with_ean']; ?></div>
                      <div style="color: #666; margin-top: 5px;"><?php echo BX_MPI_DASHBOARD_WITH_EAN; ?></div>
                    </div>
                    
                    <!-- Karte: Ohne EAN //-->
                    <div style="background: #fff; border: 1px solid #ddd; border-radius: 4px; padding: 15px; text-align: center;">
                      <div style="font-size: 32px; font-weight: bold; color: #dc3545;"><?php echo $stats['without_ean']; ?></div>
                      <div style="color: #666; margin-top: 5px;"><?php echo BX_MPI_DASHBOARD_WITHOUT_EAN; ?></div>
                    </div>
                    
                    <!-- Karte: Pseudo-EAN //-->
                    <div style="background: #fff; border: 1px solid #ddd; border-radius: 4px; padding: 15px; text-align: center;">
                      <div style="font-size: 32px; font-weight: bold; color: #ffc107;"><?php echo $stats['pseudo_ean']; ?></div>
                      <div style="color: #666; margin-top: 5px;"><?php echo BX_MPI_DASHBOARD_PSEUDO_EAN; ?></div>
                    </div>
                    
                    <!-- Karte: GS1-EAN //-->
                    <div style="background: #fff; border: 1px solid #ddd; border-radius: 4px; padding: 15px; text-align: center;">
                      <div style="font-size: 32px; font-weight: bold; color: #17a2b8;"><?php echo $stats['gs1_ean']; ?></div>
                      <div style="color: #666; margin-top: 5px;"><?php echo BX_MPI_DASHBOARD_GS1_EAN; ?></div>
                    </div>
                    
                  </div>
                  <!-- end Statistik-Karten //-->
                  
                  <!-- Letzte Aktivitäten //-->
                  <?php if (!empty($latest_activities)): ?>
                  <div style="background: #fff; border: 1px solid #ddd; border-radius: 4px; padding: 15px;">
                    <h3 style="margin: 0 0 15px 0; padding-bottom: 10px; border-bottom: 2px solid #AF417E;"><?php echo BX_MPI_LATEST_ACTIVITIES_TITLE; ?></h3>
                    <table class="tableBoxCenter collapse" style="width: 100%;">
                      <tr class="dataTableHeadingRow">
                        <td class="dataTableHeadingContent"><?php echo BX_MPI_ACTIVITIES_TABLE_TIME; ?></td>
                        <td class="dataTableHeadingContent"><?php echo BX_MPI_ACTIVITIES_TABLE_SKU; ?></td>
                        <td class="dataTableHeadingContent"><?php echo BX_MPI_ACTIVITIES_TABLE_PRODUCT_ID; ?></td>
                        <td class="dataTableHeadingContent"><?php echo BX_MPI_ACTIVITIES_TABLE_FIELD; ?></td>
                        <td class="dataTableHeadingContent"><?php echo BX_MPI_ACTIVITIES_TABLE_OLD_VALUE; ?></td>
                        <td class="dataTableHeadingContent"><?php echo BX_MPI_ACTIVITIES_TABLE_NEW_VALUE; ?></td>
                        <td class="dataTableHeadingContent"><?php echo BX_MPI_ACTIVITIES_TABLE_REASON; ?></td>
                      </tr>
                      <?php 
                      $row_class = 'dataTableRow';
                      foreach ($latest_activities as $activity): 
                        $row_class = $row_class == 'dataTableRow' ? 'dataTableRowAlt' : 'dataTableRow';
                      ?>
                      <tr class="<?php echo $row_class; ?>">
                        <td class="dataTableContent"><?php echo date('d.m.Y H:i', strtotime($activity['changed_at'])); ?></td>
                        <td class="dataTableContent"><?php echo htmlspecialchars($activity['products_sku'] ?: '-'); ?></td>
                        <td class="dataTableContent"><?php echo (int)$activity['products_id']; ?></td>
                        <td class="dataTableContent"><?php echo htmlspecialchars($activity['field_name'] ?: '-'); ?></td>
                        <td class="dataTableContent"><?php echo htmlspecialchars($activity['old_value'] ?: '-'); ?></td>
                        <td class="dataTableContent"><?php echo htmlspecialchars($activity['new_value'] ?: '-'); ?></td>
                        <td class="dataTableContent">
                          <span style="color: #666;"><?php echo htmlspecialchars($activity['change_reason'] ?: '-'); ?></span>
                        </td>
                      </tr>
                      <?php endforeach; ?>
                    </table>
                  </div>
                  <?php else: ?>
                  <div style="background: #f8f9fa; border: 1px solid #ddd; border-radius: 4px; padding: 15px; text-align: center; color: #666;">
                    <?php echo BX_MPI_HISTORY_DISABLED_OR_EMPTY; ?>
                  </div>
                  <?php endif; ?>
                  <!-- end Letzte Aktivitäten //-->
                  
                </div>
                <!-- end tab-dashboard //-->

                <div id="tab-admin">
                  
                  <?php
                  // ============================================================
                  // Kategorienavigation-Logik (analog zu categories.php)
                  // ============================================================
                  
                  // cPath parsen
                  $cPath = isset($_GET['cPath']) ? $_GET['cPath'] : '';
                  $page  = isset($_GET['page'])  ? $_GET['page']  : '';
                  $cPath_array = array();
                  
                  if (!empty($cPath)) {
                      $cPath_array = explode('_', $cPath);
                      // Letzte ID ist die aktuelle Kategorie
                      $current_category_id = (int)end($cPath_array);
                  } else {
                      $current_category_id = 0;
                  }
                  
                  // Suchparameter
                  $search_product_id = isset($_GET['search_product_id']) ? (int)$_GET['search_product_id'] : 0;
                  $search_sku        = isset($_GET['search_sku']) ? trim($_GET['search_sku']) : '';
                  $search_ean        = isset($_GET['search_ean']) ? trim($_GET['search_ean']) : '';
                  
                  // Längenprüfung
                  if (strlen($search_sku) > 50) $search_sku = substr($search_sku, 0, 50);
                  if (strlen($search_ean) > 20) $search_ean = substr($search_ean, 0, 20);
                  
                  // EAN-Format validieren (nur Zahlen)
                  if (!empty($search_ean) && !preg_match('/^[0-9]+$/', $search_ean)) {
                      $search_ean = '';
                  }
                  
                  // Breadcrumb erstellen
                  require_once (DIR_FS_CATALOG.'includes/classes/breadcrumb.php');
                  $breadcrumb = new breadcrumb();
                  $breadcrumb->add('MPI', xtc_href_link(FILENAME_BX_MPI));
                  
                  // Breadcrumb-Pfad aufbauen
                  if (!empty($cPath_array)) {
                      $cPath_link = array();
                      foreach ($cPath_array as $cat_id) {
                          if ($cat_id > 0) {
                              $cPath_link[] = $cat_id;
                              $cat_query = xtc_db_query("SELECT categories_name 
                                                          FROM " . TABLE_CATEGORIES_DESCRIPTION . " 
                                                         WHERE categories_id = '" . (int)$cat_id . "' 
                                                           AND language_id = '" . (int)$_SESSION['languages_id'] . "'");
                              if (xtc_db_num_rows($cat_query) > 0) {
                                  $cat = xtc_db_fetch_array($cat_query);
                                  $breadcrumb->add($cat['categories_name'], xtc_href_link(FILENAME_BX_MPI, 'cPath=' . implode('_', $cPath_link)));
                              }
                          }
                      }
                  }
                  
                  $breadcrumb_html = '<span class="breadcrumb">' . $breadcrumb->trail(' &raquo; ') . '</span>';
                  
                  // Kategoriename holen
                  $category_name = '';
                  if ($current_category_id > 0) {
                      $cat_query = xtc_db_query("SELECT categories_name 
                                                  FROM " . TABLE_CATEGORIES_DESCRIPTION . " 
                                                 WHERE categories_id = '" . (int)$current_category_id . "' 
                                                   AND language_id = '" . (int)$_SESSION['languages_id'] . "'");
                      if (xtc_db_num_rows($cat_query) > 0) {
                          $cat = xtc_db_fetch_array($cat_query);
                          $category_name = $cat['categories_name'];
                      }
                  }
                  ?>
                  
                  <!-- Breadcrumb und Titel //-->
                  <div style="background: #f8f9fa; border: 1px solid #ddd; border-radius: 4px; padding: 15px; margin-bottom: 15px;">
                    <h2 style="margin: 0 0 10px 0; color: #AF417E;">
                      <?php echo !empty($category_name) ? htmlspecialchars($category_name) : BX_MPI_ADMIN_RESULTS_TITLE; ?>
                    </h2>
                    <div class="smallText"><?php echo $breadcrumb_html; ?></div>
                  </div>
                  
                  <?php if ($action == 'edit' && $edit_identifier): ?>
                  <!-- Edit Form //-->
                  <div style="background: #fff; border: 2px solid #AF417E; border-radius: 4px; padding: 20px; margin-bottom: 20px;">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; padding-bottom: 15px; border-bottom: 2px solid #AF417E;">
                      <h2 style="margin: 0; color: #AF417E;"><?php echo BX_MPI_EDIT_IDENTIFIER; ?> #<?php echo $edit_identifier['identifier_id']; ?></h2>
                      <a href="<?php echo xtc_href_link(FILENAME_BX_MPI); ?>" class="button" style="padding: 6px 15px;">✕ <?php echo BX_MPI_CLOSE_BUTTON; ?></a>
                    </div>
                    
                    <?php
                    $include_get_params = array('page', 'cPath');
                    $form_params = xtc_get_all_get_params_include($include_get_params);

                     echo xtc_draw_form('edit_identifier', FILENAME_BX_MPI, $form_params . 'action=save', 'post');
                        echo xtc_draw_hidden_field('identifier_id', $edit_identifier['identifier_id']);
                    ?>
                      
                      <!-- Produkt-Info (Read-only) //-->
                      <div style="background: #f8f9fa; border: 1px solid #ddd; border-radius: 4px; padding: 15px; margin-bottom: 20px;">
                        <table style="width: 100%;">
                          <tr>
                            <td style="padding: 5px 0; width: 180px;"><strong>Produkt-ID:</strong></td>
                            <td style="padding: 5px 0;"><?php echo (int)$edit_identifier['products_id']; ?></td>
                          </tr>
                          <tr>
                            <td style="padding: 5px 0;"><strong>Produktname:</strong></td>
                            <td style="padding: 5px 0;"><?php echo htmlspecialchars($edit_identifier['products_name']); ?></td>
                          </tr>
                          <tr>
                            <td style="padding: 5px 0;"><strong>SKU (Read-only):</strong></td>
                            <td style="padding: 5px 0;"><code style="background: #e9ecef; padding: 4px 8px; border-radius: 3px;"><?php echo htmlspecialchars($edit_identifier['products_sku']); ?></code></td>
                          </tr>
                          <?php if (!empty($edit_identifier['attributes'])): ?>
                          <tr>
                            <td style="padding: 5px 0;"><strong>Attribute:</strong></td>
                            <td style="padding: 5px 0;">
                              <?php 
                              $attr_labels = array();
                              foreach ($edit_identifier['attributes'] as $attr) {
                                  $attr_labels[] = htmlspecialchars($attr['products_options_name']) . ': ' . htmlspecialchars($attr['products_options_values_name']);
                              }
                              echo implode(' | ', $attr_labels);
                              ?>
                            </td>
                          </tr>
                          <?php endif; ?>
                        </table>
                      </div>
                      <!-- end Produkt-Info //-->
                      
                      <!-- Editierbare Felder //-->
                      <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
                        
                        <div>
                          <label style="display: block; margin-bottom: 8px; font-weight: bold;"><?php echo BX_MPI_ADMIN_EAN_LABEL; ?></label>
                          <?php echo xtc_draw_input_field('ean', $edit_identifier['products_ean'], 'style="width: 100%; padding: 8px;" maxlength="20" placeholder="' . BX_MPI_EXAMPLE_PREFIX . '2000000000123"'); ?>
                          <small style="color: #666; margin-top: 5px; display: block;"><?php echo BX_MPI_ADMIN_EAN_HELP; ?></small>
                        </div>
                        
                        <div>
                          <label style="display: block; margin-bottom: 8px; font-weight: bold;"><?php echo BX_MPI_ADMIN_WWS_ARTICLE_NR_LABEL; ?></label>
                          <?php echo xtc_draw_input_field('wws_artikel_nr', $edit_identifier['wws_artikel_nr'], 'style="width: 100%; padding: 8px;" maxlength="50" placeholder="' . BX_MPI_EXAMPLE_PREFIX . 'JTL-12345"'); ?>
                          <small style="color: #666; margin-top: 5px; display: block;"><?php echo BX_MPI_ADMIN_WWS_ARTICLE_NR_HELP; ?></small>
                        </div>
                        
                        <div>
                          <label style="display: block; margin-bottom: 8px; font-weight: bold;"><?php echo BX_MPI_ADMIN_WWS_SYSTEM_LABEL; ?></label>
                          <?php echo xtc_draw_input_field('wws_system', $edit_identifier['wws_system'], 'style="width: 100%; padding: 8px;" maxlength="50" placeholder="' . BX_MPI_EXAMPLE_PREFIX . 'JTL, SAP, SAGE"'); ?>
                          <small style="color: #666; margin-top: 5px; display: block;"><?php echo BX_MPI_ADMIN_WWS_SYSTEM_HELP; ?></small>
                        </div>
                        
                        <div>
                          <label style="display: block; margin-bottom: 8px; font-weight: bold;"><?php echo BX_MPI_ADMIN_WAREHOUSE_LABEL; ?></label>
                          <?php echo xtc_draw_input_field('warehouse_location', $edit_identifier['warehouse_location'], 'style="width: 100%; padding: 8px;" maxlength="50" placeholder="' . BX_MPI_EXAMPLE_PREFIX . 'A-12-03"'); ?>
                          <small style="color: #666; margin-top: 5px; display: block;"><?php echo BX_MPI_ADMIN_WAREHOUSE_HELP; ?></small>
                        </div>
                        
                      </div>
                      <!-- end Editierbare Felder //-->
                      
                      <!-- Aktionen //-->
                      <div style="display: flex; justify-content: space-between; align-items: center; padding-top: 15px; border-top: 1px solid #ddd;">
                        <a href="<?php echo xtc_href_link(FILENAME_BX_MPI, 'action=delete&id=' . $edit_identifier['identifier_id']); ?>" 
                           onclick="return confirm('<?php echo BX_MPI_DELETE_CONFIRM; ?>');" 
                           class="button" 
                           style="padding: 10px 30px; background: #dc3545; color: #fff;">
                          <?php echo BX_MPI_ADMIN_DELETE_BUTTON; ?>
                        </a>
                        <button type="submit" class="button" style="padding: 10px 30px; background: #28a745; color: #fff; font-weight: bold;"><?php echo BX_MPI_ADMIN_SAVE_BUTTON; ?></button>
                      </div>
                      <!-- end Aktionen //-->
                      
                    </form>
                  </div>
                  <!-- end Bearbeitungs-Formular //-->
                  <?php endif; ?>
                  
                  <!-- Suchfilter //-->
                  <?php echo xtc_draw_form('search_identifiers', FILENAME_BX_MPI, '', 'get'); ?>
                    <?php 
                    // cPath beibehalten im Suchformular
                    if (!empty($cPath)) {
                        echo xtc_draw_hidden_field('cPath', $cPath);
                    }
                    ?>
                    <div style="background: #f8f9fa; border: 1px solid #ddd; border-radius: 4px; padding: 15px; margin-bottom: 20px;">
                      <div style="display: grid; grid-template-columns: 1fr 1fr 1fr auto; gap: 10px; align-items: end;">
                        <div>
                          <label style="display: block; margin-bottom: 5px; font-weight: bold;"><?php echo BX_MPI_ADMIN_PRODUCT_ID_SEARCH; ?></label>
                          <?php echo xtc_draw_input_field('search_product_id', $search_product_id > 0 ? $search_product_id : '', 'style="width: 100%; padding: 6px;" placeholder="' . BX_MPI_EXAMPLE_PREFIX . '123"'); ?>
                        </div>
                        <div>
                          <label style="display: block; margin-bottom: 5px; font-weight: bold;"><?php echo BX_MPI_ADMIN_SKU_SEARCH; ?></label>
                          <?php echo xtc_draw_input_field('search_sku', htmlspecialchars($search_sku, ENT_QUOTES), 'style="width: 100%; padding: 6px;" placeholder="' . BX_MPI_EXAMPLE_PREFIX . 'SKU0139_0003-0290"'); ?>
                        </div>
                        <div>
                          <label style="display: block; margin-bottom: 5px; font-weight: bold;"><?php echo BX_MPI_ADMIN_EAN_SEARCH; ?></label>
                          <?php echo xtc_draw_input_field('search_ean', htmlspecialchars($search_ean, ENT_QUOTES), 'style="width: 100%; padding: 6px;" placeholder="' . BX_MPI_EXAMPLE_PREFIX . '2000000000123"'); ?>
                        </div>
                        <div>
                          <button type="submit" class="button" style="padding: 6px 20px;"><?php echo BX_MPI_ADMIN_SEARCH_BUTTON; ?></button>
                          <?php if ($search_product_id > 0 || !empty($search_sku) || !empty($search_ean) || !empty($cPath)): ?>
                          <a href="<?php echo xtc_href_link(FILENAME_BX_MPI); ?>" class="button" style="padding: 6px 20px; margin-left: 5px;"><?php echo BX_MPI_ADMIN_RESET_FILTER; ?></a>
                          <?php endif; ?>
                        </div>
                      </div>
                    </div>
                  </form>
                  <!-- end Suchfilter //-->
                  
                  <?php
                  // ============================================================
                  // Produktabfrage (analog zu categories_view.php)
                  // ============================================================
                  
                  // splitPageResults initialisieren
                  require_once(DIR_WS_CLASSES . 'split_page_results.php');
                  $page_max_display_mpi = isset($page_max_display_mpi) ? $page_max_display_mpi : 50;
                  $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
                  
                  // WHERE-Bedingungen und JOINs aufbauen
                  $where_conditions = array();
                  $add_join = '';
                  
                  // Kategoriefilter - analog zu categories_view.php
                  if ($current_category_id > 0) {
                      // Produkte in der aktuellen Kategorie (über products_to_categories)
                      $add_join = " JOIN " . TABLE_PRODUCTS_TO_CATEGORIES . " p2c ON p.products_id = p2c.products_id AND p2c.categories_id = '" . (int)$current_category_id . "' ";
                  }
                  
                  // Suchfilter
                  if ($search_product_id > 0) {
                      $where_conditions[] = "p.products_id = '" . (int)$search_product_id . "'";
                  }
                  
                  if (!empty($search_sku)) {
                      $where_conditions[] = "pi.products_sku LIKE '%" . xtc_db_input($search_sku) . "%'";
                  }
                  
                  if (!empty($search_ean)) {
                      $where_conditions[] = "pi.products_ean LIKE '%" . xtc_db_input($search_ean) . "%'";
                  }
                  
                  // Quick-Filter aus Sidebar
                  if (isset($_GET['filter_no_ean'])) {
                      $where_conditions[] = "(pi.products_ean IS NULL OR pi.products_ean = '')";
                  }
                  
                  if (isset($_GET['filter_pseudo_ean'])) {
                      $where_conditions[] = "pi.products_ean LIKE '2%'";
                  }
                  
                  if (isset($_GET['filter_gs1_ean'])) {
                      $where_conditions[] = "pi.products_ean NOT LIKE '2%' AND pi.products_ean IS NOT NULL AND pi.products_ean != ''";
                  }
                  
                  if (isset($_GET['filter_no_warehouse'])) {
                      $where_conditions[] = "(pi.warehouse_location IS NULL OR pi.warehouse_location = '')";
                  }
                  
                  $where_sql = !empty($where_conditions) ? 'WHERE ' . implode(' AND ', $where_conditions) : '';
                  
                  // Query aufbauen - nur wenn wir in einer Kategorie sind ODER eine Suche aktiv ist
                  $identifiers = array();
                  $identifiers_query_numrows = 0;
                  
                  // DEBUG (kann später entfernt werden)
                  $debug_info = array(
                      'current_category_id' => $current_category_id,
                      'search_active' => ($search_product_id > 0 || !empty($search_sku) || !empty($search_ean)),
                      'add_join' => $add_join,
                      'where_conditions' => $where_conditions
                  );
                  
                  if ($current_category_id > 0 || $search_product_id > 0 || !empty($search_sku) || !empty($search_ean)) {
                    // Basis-Query für splitPageResults
                    // LEFT JOIN mit product_identifiers: Zeige ALLE Produkte, auch ohne MPI-Einträge
                    $identifiers_query_raw = "
                        SELECT pi.*, p.products_id, p.products_model, pd.products_name, p.products_image
                        FROM " . TABLE_PRODUCTS . " p
                        " . $add_join . "
                        LEFT JOIN " . TABLE_PRODUCT_IDENTIFIERS . " pi ON p.products_id = pi.products_id
                        LEFT JOIN " . TABLE_PRODUCTS_DESCRIPTION . " pd ON p.products_id = pd.products_id AND pd.language_id = '" . (int)$_SESSION['languages_id'] . "'
                        $where_sql
                        ORDER BY p.products_id DESC
                    ";
                    
                    // DEBUG - Query speichern
                    $debug_info['query'] = $identifiers_query_raw;
      
                    // COUNT-Query - Zähle Produkte, nicht Identifier
                    $count_result = xtc_db_query("
                        SELECT COUNT(DISTINCT p.products_id) as total 
                        FROM " . TABLE_PRODUCTS . " p
                        " . $add_join . "
                        LEFT JOIN " . TABLE_PRODUCT_IDENTIFIERS . " pi ON p.products_id = pi.products_id
                        LEFT JOIN " . TABLE_PRODUCTS_DESCRIPTION . " pd ON p.products_id = pd.products_id AND pd.language_id = '" . (int)$_SESSION['languages_id'] . "'
                        $where_sql
                    ");
                    $count_row = xtc_db_fetch_array($count_result);
                    $identifiers_query_numrows = (int)$count_row['total'];
                    
                    // splitPageResults initialisieren (modifiziert $identifiers_query_raw per Referenz)
                    $identifiers_split = new splitPageResults($page, $page_max_display_mpi, $identifiers_query_raw, $identifiers_query_numrows);
                    
                    // Query mit LIMIT ausführen
                    $query = xtc_db_query($identifiers_query_raw);
                    while ($row = xtc_db_fetch_array($query)) {
                        // Produkt muss existieren
                        if (!isset($row['products_id']) || $row['products_id'] <= 0) {
                            continue;
                        }
                        
                        // Attribute laden (nur wenn Identifier vorhanden)
                        $row['attributes'] = array();
                        if (isset($row['identifier_id']) && $row['identifier_id'] > 0) {
                            $attr_query = xtc_db_query("
                                SELECT pia.*, po.products_options_name, pov.products_options_values_name
                                FROM " . TABLE_PRODUCT_IDENTIFIER_ATTRIBUTES . " pia
                                LEFT JOIN " . TABLE_PRODUCTS_OPTIONS . " po ON pia.products_options_id = po.products_options_id 
                                    AND po.language_id = '" . (int)$_SESSION['languages_id'] . "'
                                LEFT JOIN " . TABLE_PRODUCTS_OPTIONS_VALUES . " pov ON pia.products_options_values_id = pov.products_options_values_id 
                                    AND pov.language_id = '" . (int)$_SESSION['languages_id'] . "'
                                WHERE pia.identifier_id = '" . (int)$row['identifier_id'] . "'
                                ORDER BY pia.sort_order
                            ");
                            
                            while ($attr = xtc_db_fetch_array($attr_query)) {
                                $row['attributes'][] = $attr;
                            }
                        }
                        
                        $identifiers[] = $row;
                    }
                  }
  ?>
                  
                  <!-- DEBUG Info (nur wenn keine Produkte gefunden) //-->
                  <?php if ($current_category_id > 0 && empty($identifiers) && isset($debug_info)): ?>
                  <div style="background: #fff3cd; border: 1px solid #ffc107; border-radius: 4px; padding: 15px; margin-bottom: 15px;">
                    <strong>🔍 DEBUG Info:</strong><br>
                    <small>
                      Kategorie-ID: <?php echo $current_category_id; ?><br>
                      Gefundene Produkte: <?php echo $identifiers_query_numrows; ?><br>
                      JOIN: <?php echo !empty($add_join) ? 'Ja (products_to_categories)' : 'Nein'; ?><br>
                      WHERE-Bedingungen: <?php echo count($where_conditions); ?><br>
                      <?php if (isset($debug_info['query'])): ?>
                      <details style="margin-top: 10px;">
                        <summary style="cursor: pointer; color: #856404;">SQL-Query anzeigen</summary>
                        <pre style="background: #fff; padding: 10px; margin-top: 5px; overflow-x: auto; font-size: 11px;"><?php echo htmlspecialchars($debug_info['query']); ?></pre>
                      </details>
                      <?php endif; ?>
                    </small>
                  </div>
                  <?php endif; ?>
                  
                  <!-- File Manager View: Kategorien + Produkte //-->
                  <div style="background: #fff; border: 1px solid #ddd; border-radius: 4px; padding: 15px;">
                    
                    <table class="tableBoxCenter collapse" style="width: 100%;">
                      <tr class="dataTableHeadingRow">
                        <td class="dataTableHeadingContent" style="width: 5%;">ID</td>
                        <td class="dataTableHeadingContent" style="width: 7%;">Prod-ID</td>
                        <td class="dataTableHeadingContent" style="width: 25%;"><?php echo BX_MPI_ADMIN_TABLE_PRODUCT_NAME; ?></td>
                        <td class="dataTableHeadingContent" style="width: 15%;"><?php echo BX_MPI_ADMIN_TABLE_SKU; ?></td>
                        <td class="dataTableHeadingContent" style="width: 15%;"><?php echo BX_MPI_ADMIN_TABLE_EAN; ?></td>
                        <td class="dataTableHeadingContent" style="width: 12%;"><?php echo BX_MPI_ADMIN_TABLE_WWS_NR; ?></td>
                        <td class="dataTableHeadingContent" style="width: 12%;"><?php echo BX_MPI_ADMIN_TABLE_WAREHOUSE; ?></td>
                        <td class="dataTableHeadingContent" style="text-align: right; width: 9%;"><?php echo BX_MPI_ADMIN_TABLE_ACTIONS; ?></td>
                      </tr>
                      
                      <?php
                      // ============================================================
                      // ".."-Rücklink anzeigen (wenn nicht auf Top-Level)
                      // ============================================================
                      if (!empty($cPath_array) && count($cPath_array) > 0) {
                          // Berechne Parent-cPath
                          $cPath_back = '';
                          for($i = 0, $n = count($cPath_array) - 1; $i < $n; $i++) {
                              if ($cPath_back == '') {
                                  $cPath_back .= $cPath_array[$i];
                              } else {
                                  $cPath_back .= '_' . $cPath_array[$i];
                              }
                          }
                          $back_url = xtc_href_link(FILENAME_BX_MPI, ($cPath_back != '' ? 'cPath=' . $cPath_back : ''));
                          ?>
                          <tr class="dataTableRow" onmouseover="this.className='dataTableRowOver';this.style.cursor='pointer'" onmouseout="this.className='dataTableRow'" onclick="window.location.href='<?php echo $back_url; ?>'">
                            <td class="dataTableContent" colspan="2" style="text-align: center;">--</td>
                            <td class="dataTableContent" style="padding-left: 8px;">
                              <a href="<?php echo $back_url; ?>">
                                <?php echo xtc_image(DIR_WS_ICONS . 'folder_parent.gif', '..')  . ' <strong>..</strong>'; ?>
                              </a>
                            </td>
                            <td class="dataTableContent" colspan="5" style="text-align: center;">--</td>
                          </tr>
                          <?php
                      }
                      
                      // ============================================================
                      // Unterkategorien der aktuellen Kategorie abfragen
                      // ============================================================
                      $categories_query = xtc_db_query(
                          "SELECT c.categories_id, cd.categories_name, c.sort_order
                           FROM " . TABLE_CATEGORIES . " c
                           LEFT JOIN " . TABLE_CATEGORIES_DESCRIPTION . " cd 
                             ON c.categories_id = cd.categories_id 
                             AND cd.language_id = '" . (int)$_SESSION['languages_id'] . "'
                           WHERE c.parent_id = '" . (int)$current_category_id . "'
                           ORDER BY c.sort_order, cd.categories_name"
                      );
                      
                      $categories_count = xtc_db_num_rows($categories_query);
                      
                      // Kategorien anzeigen
                      while ($category = xtc_db_fetch_array($categories_query)) {
                          $category_url = xtc_href_link(
                              FILENAME_BX_MPI, 
                              'cPath=' . ($cPath != '' ? $cPath . '_' : '') . $category['categories_id']
                          );
                          ?> 
                          <tr class="dataTableRow" onmouseover="this.className='dataTableRowOver';this.style.cursor='pointer'" onmouseout="this.className='dataTableRow'" onclick="window.location.href='<?php echo $category_url; ?>'">
                            <td class="dataTableContent" colspan="2" style="text-align: center;">--</td>
                            <td class="dataTableContent" style="padding-left: 8px;">
                              <a href="<?php echo $category_url; ?>" style="color: #AF417E; font-weight: bold;">
                                <?php echo xtc_image(DIR_WS_ICONS . 'folder.gif', 'Folder'); ?>
                                <?php echo htmlspecialchars($category['categories_name']); ?>
                              </a>
                            </td>
                            <td class="dataTableContent" colspan="4" style="text-align: center;">--</td>
                            <td class="dataTableContent" style="text-align: center;">
                              <?php echo xtc_image(DIR_WS_IMAGES . 'icon_arrow_right.gif', '→'); ?>
                            </td>
                          </tr>
                          <?php
                      }
                      
                      // ============================================================
                      // Produkte (Identifiers) anzeigen
                      // ============================================================
                      $row_class = 'dataTableRow';
                      if (!empty($identifiers)) {
                          foreach ($identifiers as $identifier) { 
                            $row_class = $row_class == 'dataTableRow' ? 'dataTableRowAlt' : 'dataTableRow';
                        
                            // EAN-Typ erkennen
                            $ean_badge = '';
                            if (!empty($identifier['products_ean'])) {
                                if (substr($identifier['products_ean'], 0, 1) == '2') {
                                    $ean_badge = '<span style="background: #ffc107; color: #000; padding: 2px 6px; border-radius: 3px; font-size: 10px; margin-left: 5px;">PSEUDO</span>';
                                } else {
                                    $ean_badge = '<span style="background: #17a2b8; color: #fff; padding: 2px 6px; border-radius: 3px; font-size: 10px; margin-left: 5px;">GS1</span>';
                                }
                            }
                      ?>
                      <tr class="<?php echo $row_class; ?>">
                        <td class="dataTableContent"><?php echo isset($identifier['identifier_id']) && $identifier['identifier_id'] > 0 ? (int)$identifier['identifier_id'] : '<span style="color: #999;">-</span>'; ?></td>
                        <td class="dataTableContent"><?php echo (int)$identifier['products_id']; ?></td>
                        <td class="dataTableContent">
                          <?php 
                          // Produktname
                          echo htmlspecialchars(substr($identifier['products_name'], 0, 40)); 
                          if (strlen($identifier['products_name']) > 40) echo '...';
                          
                          // Attribute anhängen (falls vorhanden)
                          if (!empty($identifier['attributes'])) {
                              echo '<br><span style="color: #666; font-size: 11px;">';
                              $attr_pairs = array();
                              foreach ($identifier['attributes'] as $attr) {
                                  $attr_pairs[] = htmlspecialchars($attr['products_options_name']) . ': ' . htmlspecialchars($attr['products_options_values_name']);
                              }
                              echo implode(', ', $attr_pairs);
                              echo '</span>';
                          }
                          
                          // Hinweis wenn kein MPI-Eintrag
                          if (!isset($identifier['identifier_id']) || $identifier['identifier_id'] <= 0) {
                              echo '<br><span style="color: #dc3545; font-size: 10px;">⚠ Kein MPI-Eintrag vorhanden</span>';
                          }
                          ?>
                        </td>
                        <td class="dataTableContent"><strong><?php echo isset($identifier['products_sku']) ? htmlspecialchars($identifier['products_sku']) : '-'; ?></strong></td>
                        <td class="dataTableContent">
                          <?php echo isset($identifier['products_ean']) ? htmlspecialchars($identifier['products_ean'] ?: '-') : '-'; ?>
                          <?php echo $ean_badge; ?>
                        </td>
                        <td class="dataTableContent"><?php echo isset($identifier['wws_artikel_nr']) ? htmlspecialchars($identifier['wws_artikel_nr'] ?: '-') : '-'; ?></td>
                        <td class="dataTableContent"><?php echo isset($identifier['warehouse_location']) ? htmlspecialchars($identifier['warehouse_location'] ?: '-') : '-'; ?></td>
                        <td class="dataTableContent" style="text-align: right;">
                          <?php if (isset($identifier['identifier_id']) && $identifier['identifier_id'] > 0): ?>
                            <a href="<?php echo xtc_href_link(FILENAME_BX_MPI, 'action=edit&id=' . $identifier['identifier_id'] . ($cPath ? '&cPath=' . $cPath : '')); ?>" class="button" style="padding: 3px 10px; font-size: 11px;"><?php echo BX_MPI_ADMIN_EDIT_BUTTON; ?></a>
                          <?php else: ?>
                            <span style="color: #999; font-size: 10px;">Nicht erfasst</span>
                          <?php endif; ?>
                        </td>
                      </tr>
                      <?php 
                          } // foreach identifiers
                      } elseif ($current_category_id > 0 && $categories_count == 0) {
                          // Keine Produkte UND keine Kategorien - Kategorie ist leer
                          ?>
                          <tr>
                            <td colspan="8" style="text-align: center; padding: 40px; color: #666;">
                              <strong><?php echo BX_MPI_ADMIN_NO_ENTRIES; ?></strong>
                              <?php if ($search_product_id > 0 || !empty($search_sku) || !empty($search_ean)): ?>
                              <br><a href="<?php echo xtc_href_link(FILENAME_BX_MPI, 'cPath=' . $cPath); ?>" style="color: #AF417E; margin-top: 10px; display: inline-block;"><?php echo BX_MPI_ADMIN_RESET_FILTER; ?></a>
                              <?php endif; ?>
                            </td>
                          </tr>
                          <?php
                      }
                      ?>
                    </table>
                    
                    <!-- Pagination //-->
                    <?php if (!empty($identifiers) && isset($identifiers_split)): ?>
                    <table width="100%" border="0" cellspacing="0" cellpadding="2" style="margin-top: 10px;">
                      <tr>
                        <td class="smallText pdg2 flt-l">
                          <span style="line-height: 28px;">
                            <?php echo $identifiers_split->display_count($identifiers_query_numrows, $page_max_display_mpi, $page, BX_MPI_DISPLAY_COUNT); ?>
                          </span>
                        </td>
                        <td class="smallText pdg2 flt-r">
                          <?php 
                          // URL-Parameter für Pagination beibehalten
                          $url_params = array();
                          if (!empty($cPath)) {
                              $url_params[] = 'cPath=' . urlencode($cPath);
                          }
                          if ($search_product_id > 0) {
                              $url_params[] = 'search_product_id=' . $search_product_id;
                          }
                          if (!empty($search_sku)) {
                              $url_params[] = 'search_sku=' . urlencode($search_sku);
                          }
                          if (!empty($search_ean)) {
                              $url_params[] = 'search_ean=' . urlencode($search_ean);
                          }
                          $url_params_str = !empty($url_params) ? implode('&', $url_params) : '';
                          
                          echo $identifiers_split->display_links($identifiers_query_numrows, $page_max_display_mpi, MAX_DISPLAY_PAGE_LINKS, $page, $url_params_str, 'page');
                          ?>
                        </td>
                      </tr>
                      <tr>
                        <td class="smallText pdg2" colspan="2">
                          <?php echo draw_input_per_page(FILENAME_BX_MPI, $cfg_max_display_mpi_key, $page_max_display_mpi); ?>
                        </td>
                      </tr>
                    </table>
                    <?php endif; ?>
                    <!-- end Pagination //-->
                    
                  </div>
                  <!-- end File Manager View //-->
                  
                </div>
                <!-- end tab-admin //-->

                <div id="tab-gtin">
                  
                  <?php
                  // EAN-Pool: Block-Verwaltung und Anzeige
                  
                  // Block-Detail-Ansicht
                  if (isset($_GET['view_block'])) {
                      $view_block_id = (int)$_GET['view_block'];
                      
                      // Block-Details laden
                      $block_query = xtc_db_query("
                          SELECT * FROM " . TABLE_PRODUCT_IDENTIFIER_EAN_BLOCKS . "
                          WHERE block_id = '" . $view_block_id . "'
                      ");
                      $block = xtc_db_fetch_array($block_query);
                      
                      if ($block):
                          // EANs des Blocks laden mit Pagination
                          $eans_per_page = 50;
                          $ean_page      = isset($_GET['ean_page']) ? (int)$_GET['ean_page'] : 1;
                          $ean_offset    = ($ean_page - 1) * $eans_per_page;
                          
                          // Filter
                          $filter_status = isset($_GET['filter_status']) ? $_GET['filter_status'] : '';
                          $search_ean = isset($_GET['search_ean']) ? trim($_GET['search_ean']) : '';
                          
                          $where_conditions = array("block_id = '" . $view_block_id . "'");
                          if (!empty($filter_status) && in_array($filter_status, array('available', 'assigned', 'reserved'))) {
                              $where_conditions[] = "status = '" . xtc_db_input($filter_status) . "'";
                          }
                          if (!empty($search_ean)) {
                              $where_conditions[] = "ean LIKE '%" . xtc_db_input($search_ean) . "%'";
                          }
                          $where_sql = implode(' AND ', $where_conditions);
                          
                          // Gesamtzahl EANs
                          $count_query = xtc_db_query("
                              SELECT COUNT(*) as total FROM " . TABLE_PRODUCT_IDENTIFIER_EAN_POOL . "
                              WHERE " . $where_sql
                          );
                          $count_row = xtc_db_fetch_array($count_query);
                          $total_eans = $count_row['total'];
                          $total_pages = ceil($total_eans / $eans_per_page);
                          
                          // EANs laden
                          $eans_query = xtc_db_query("
                              SELECT p.*, i.products_sku, i.products_id
                              FROM " . TABLE_PRODUCT_IDENTIFIER_EAN_POOL . " p
                              LEFT JOIN " . TABLE_PRODUCT_IDENTIFIERS . " i ON p.identifier_id = i.identifier_id
                              WHERE " . $where_sql . "
                              ORDER BY p.pool_id ASC
                              LIMIT " . $eans_per_page . " OFFSET " . $ean_offset
                          );
                          
                          $usage_percent = $block['total_eans'] > 0 ? round(($block['used_eans'] / $block['total_eans']) * 100, 1) : 0;
                  ?>
                  
                  <!-- Block-Detail-Header //-->
                  <div style="background: #fff; border: 2px solid #AF417E; border-radius: 4px; padding: 20px; margin-bottom: 20px;">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                      <h2 style="margin: 0; color: #AF417E;"><?php echo BX_MPI_POOL_DETAILS_HEADER; ?> <?php echo htmlspecialchars($block['block_number']); ?></h2>
                      <a href="<?php echo xtc_href_link(FILENAME_BX_MPI); ?>" class="button" style="padding: 8px 20px;"><?php echo BX_MPI_POOL_BACK_OVERVIEW; ?></a>
                    </div>
                    
                    <!-- Block-Statistiken //-->
                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 15px; margin-bottom: 20px;">
                      <div style="background: #f8f9fa; border: 1px solid #ddd; border-radius: 4px; padding: 15px;">
                        <div style="font-size: 12px; color: #666; margin-bottom: 5px;"><?php echo BX_MPI_POOL_BLOCK_SIZE; ?></div>
                        <div style="font-size: 24px; font-weight: bold; color: #AF417E;"><?php echo $block['block_size']; ?> EANs</div>
                      </div>
                      <div style="background: #f8f9fa; border: 1px solid #ddd; border-radius: 4px; padding: 15px;">
                        <div style="font-size: 12px; color: #666; margin-bottom: 5px;"><?php echo BX_MPI_POOL_PURCHASED_AT; ?></div>
                        <div style="font-size: 16px; font-weight: bold;"><?php echo date('d.m.Y', strtotime($block['purchased_at'])); ?></div>
                      </div>
                      <div style="background: #f8f9fa; border: 1px solid #ddd; border-radius: 4px; padding: 15px;">
                        <div style="font-size: 12px; color: #666; margin-bottom: 5px;"><?php echo BX_MPI_POOL_IMPORTED_AT; ?></div>
                        <div style="font-size: 16px; font-weight: bold;"><?php echo date('d.m.Y H:i', strtotime($block['imported_at'])); ?></div>
                      </div>
                      <div style="background: #f8f9fa; border: 1px solid #ddd; border-radius: 4px; padding: 15px;">
                        <div style="font-size: 12px; color: #666; margin-bottom: 5px;"><?php echo BX_MPI_POOL_AVAILABLE; ?></div>
                        <div style="font-size: 24px; font-weight: bold; color: #28a745;"><?php echo $block['available_eans']; ?></div>
                      </div>
                      <div style="background: #f8f9fa; border: 1px solid #ddd; border-radius: 4px; padding: 15px;">
                        <div style="font-size: 12px; color: #666; margin-bottom: 5px;"><?php echo BX_MPI_POOL_USED; ?></div>
                        <div style="font-size: 24px; font-weight: bold; color: #dc3545;"><?php echo $block['used_eans']; ?></div>
                      </div>
                      <div style="background: #f8f9fa; border: 1px solid #ddd; border-radius: 4px; padding: 15px;">
                        <div style="font-size: 12px; color: #666; margin-bottom: 5px;"><?php echo BX_MPI_POOL_USAGE; ?></div>
                        <div style="font-size: 24px; font-weight: bold; color: <?php echo $usage_percent > 80 ? '#dc3545' : '#17a2b8'; ?>;"><?php echo $usage_percent; ?>%</div>
                      </div>
                    </div>
                    
                    <?php if (!empty($block['notes'])): ?>
                    <div style="background: #e7f3ff; border: 1px solid #b3d9ff; border-radius: 4px; padding: 15px;">
                      <strong style="color: #0066cc;"><?php echo BX_MPI_POOL_NOTES_TITLE; ?></strong><br>
                      <div style="margin-top: 8px; color: #333;"><?php echo nl2br(htmlspecialchars($block['notes'])); ?></div>
                    </div>
                    <?php endif; ?>
                  </div>
                  
                  <!-- Filter und Suche //-->
                  <div style="background: #f8f9fa; border: 1px solid #ddd; border-radius: 4px; padding: 15px; margin-bottom: 20px;">
                    <form method="get" action="<?php echo xtc_href_link(FILENAME_BX_MPI); ?>">
                      <input type="hidden" name="view_block" value="<?php echo $view_block_id; ?>">
                      <div style="display: grid; grid-template-columns: 1fr 1fr auto; gap: 10px; align-items: end;">
                        <div>
                          <label style="display: block; margin-bottom: 5px; font-weight: bold;"><?php echo BX_MPI_POOL_FILTER_STATUS; ?></label>
                          <select name="filter_status" style="width: 100%; padding: 6px;">
                            <option value=""><?php echo BX_MPI_POOL_ALL_STATUS; ?></option>
                            <option value="available" <?php echo $filter_status == 'available' ? 'selected' : ''; ?>><?php echo BX_MPI_AVAILABLE_LABEL; ?></option>
                            <option value="assigned" <?php echo $filter_status == 'assigned' ? 'selected' : ''; ?>><?php echo BX_MPI_POOL_STATUS_ASSIGNED; ?></option>
                            <option value="reserved" <?php echo $filter_status == 'reserved' ? 'selected' : ''; ?>><?php echo BX_MPI_POOL_STATUS_RESERVED; ?></option>
                          </select>
                        </div>
                        <div>
                          <label style="display: block; margin-bottom: 5px; font-weight: bold;"><?php echo BX_MPI_POOL_EAN_SEARCH; ?></label>
                          <input type="text" name="search_ean" value="<?php echo htmlspecialchars($search_ean); ?>" placeholder="<?php echo BX_MPI_POOL_EAN_SEARCH_PLACEHOLDER; ?>" style="width: 100%; padding: 6px;">
                        </div>
                        <div>
                          <button type="submit" class="button" style="padding: 6px 20px;"><?php echo BX_MPI_POOL_FILTER_BUTTON; ?></button>
                          <?php if (!empty($filter_status) || !empty($search_ean)): ?>
                          <a href="<?php echo xtc_href_link(FILENAME_BX_MPI, 'view_block=' . $view_block_id); ?>" class="button" style="padding: 6px 15px; margin-left: 5px;"><?php echo BX_MPI_RESET_BUTTON; ?></a>
                          <?php endif; ?>
                        </div>
                      </div>
                    </form>
                  </div>
                  
                  <!-- EAN-Liste //-->
                  <div style="background: #fff; border: 1px solid #ddd; border-radius: 4px; padding: 15px;">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
                      <h3 style="margin: 0;"><?php echo BX_MPI_POOL_EAN_LIST_TITLE; ?></h3>
                      <div style="color: #666; font-size: 13px;">
                        <?php echo $total_eans; ?> EAN<?php echo $total_eans != 1 ? 's' : ''; ?> <?php echo BX_MPI_POOL_EAN_FOUND; ?>
                        <?php if ($total_pages > 1): ?>
                        | <?php echo sprintf(BX_MPI_POOL_PAGE_OF, $ean_page, $total_pages); ?>
                        <?php endif; ?>
                      </div>
                    </div>
                    
                    <?php if ($total_eans > 0): ?>
                    <table class="tableBoxCenter collapse" style="width: 100%;">
                      <tr class="dataTableHeadingRow">
                        <td class="dataTableHeadingContent" style="width: 60px;"><?php echo BX_MPI_POOL_TABLE_ID; ?></td>
                        <td class="dataTableHeadingContent"><?php echo BX_MPI_POOL_TABLE_EAN; ?></td>
                        <td class="dataTableHeadingContent" style="width: 120px;"><?php echo BX_MPI_POOL_TABLE_STATUS; ?></td>
                        <td class="dataTableHeadingContent"><?php echo BX_MPI_POOL_TABLE_ASSIGNED_TO; ?></td>
                        <td class="dataTableHeadingContent" style="width: 150px;"><?php echo BX_MPI_POOL_TABLE_ASSIGNED_AT; ?></td>
                      </tr>
                      <?php 
                      $row_class = 'dataTableRow';
                      while ($ean_row = xtc_db_fetch_array($eans_query)):
                        $row_class = $row_class == 'dataTableRow' ? 'dataTableRowAlt' : 'dataTableRow';
                        
                        $status_colors = array(
                            'available' => '#28a745',
                            'assigned' => '#dc3545',
                            'reserved' => '#ffc107'
                        );
                        $status_labels = array(
                            'available' => '✓ ' . BX_MPI_AVAILABLE_LABEL,
                            'assigned' => '⊗ ' . BX_MPI_POOL_STATUS_LABEL_ASSIGNED,
                            'reserved' => '⊙ ' . BX_MPI_POOL_STATUS_LABEL_RESERVED
                        );
                        $status_color = $status_colors[$ean_row['status']] ?? '#6c757d';
                        $status_label = $status_labels[$ean_row['status']] ?? $ean_row['status'];
                      ?>
                      <tr class="<?php echo $row_class; ?>">
                        <td class="dataTableContent" style="text-align: center;"><?php echo $ean_row['pool_id']; ?></td>
                        <td class="dataTableContent"><code style="font-size: 14px; font-weight: bold;"><?php echo htmlspecialchars($ean_row['ean']); ?></code></td>
                        <td class="dataTableContent">
                          <span style="background: <?php echo $status_color; ?>; color: <?php echo $ean_row['status'] == 'reserved' ? '#000' : '#fff'; ?>; padding: 3px 8px; border-radius: 3px; font-size: 11px; display: inline-block;">
                            <?php echo $status_label; ?>
                          </span>
                        </td>
                        <td class="dataTableContent">
                          <?php if ($ean_row['identifier_id']): ?>
                            <a href="<?php echo xtc_href_link(FILENAME_BX_MPI, 'action=edit&id=' . $ean_row['identifier_id']); ?>" style="color: #AF417E;">
                              <strong>ID <?php echo $ean_row['identifier_id']; ?>:</strong> <?php echo htmlspecialchars($ean_row['products_sku']); ?>
                            </a>
                            <br><small style="color: #666;">Produkt-ID: <?php echo $ean_row['products_id']; ?></small>
                          <?php else: ?>
                            <span style="color: #999;">-</span>
                          <?php endif; ?>
                        </td>
                        <td class="dataTableContent">
                          <?php echo $ean_row['assigned_at'] ? date('d.m.Y H:i', strtotime($ean_row['assigned_at'])) : '-'; ?>
                        </td>
                      </tr>
                      <?php endwhile; ?>
                    </table>
                    
                    <!-- Pagination //-->
                    <?php if ($total_pages > 1): ?>
                    <div style="margin-top: 15px; text-align: center;">
                      <?php
                      $base_url = 'view_block=' . $view_block_id;
                      if (!empty($filter_status)) $base_url .= '&filter_status=' . urlencode($filter_status);
                      if (!empty($search_ean)) $base_url .= '&search_ean=' . urlencode($search_ean);
                      
                      if ($ean_page > 1): ?>
                        <a href="<?php echo xtc_href_link(FILENAME_BX_MPI, $base_url . '&ean_page=' . ($ean_page - 1)); ?>" class="button"><?php echo BX_MPI_POOL_PREV_PAGE; ?></a>
                      <?php endif; ?>
                      
                      <span style="margin: 0 15px; color: #666;"><?php echo sprintf(BX_MPI_POOL_PAGE_OF, $ean_page, $total_pages); ?></span>
                      
                      <?php if ($ean_page < $total_pages): ?>
                        <a href="<?php echo xtc_href_link(FILENAME_BX_MPI, $base_url . '&ean_page=' . ($ean_page + 1)); ?>" class="button"><?php echo BX_MPI_NEXT_PAGE; ?></a>
                      <?php endif; ?>
                    </div>
                    <?php endif; ?>
                    
                    <?php else: ?>
                    <div style="text-align: center; padding: 40px; color: #666;">
                      <?php echo BX_MPI_POOL_NO_EANS_FOUND; ?>
                      <?php if (!empty($filter_status) || !empty($search_ean)): ?>
                      <br><a href="<?php echo xtc_href_link(FILENAME_BX_MPI, 'view_block=' . $view_block_id); ?>" style="color: #AF417E; margin-top: 10px; display: inline-block;"><?php echo BX_MPI_RESET_FILTER; ?></a>
                      <?php endif; ?>
                    </div>
                    <?php endif; ?>
                  </div>
                  
                  <?php 
                      else:
                          echo '<div style="background: #f8d7da; border: 1px solid #f5c6cb; padding: 20px; text-align: center; border-radius: 4px;">';
                          echo '<strong>Fehler:</strong> Block nicht gefunden.';
                          echo '<br><a href="' . xtc_href_link(FILENAME_BX_MPI) . '" class="button" style="margin-top: 15px;">' . BX_MPI_BACK_OVERVIEW . '</a>';
                          echo '</div>';
                      endif;
                  } else {
                      // Übersichtsliste der Blöcke
                      
                      // Gesamtstatistik laden
                      $pool_stats_query = xtc_db_query("
                          SELECT 
                              COUNT(DISTINCT b.block_id) as total_blocks,
                              COALESCE(SUM(b.total_eans), 0) as total_eans,
                              COALESCE(SUM(b.used_eans), 0) as used_eans,
                              COALESCE(SUM(b.available_eans), 0) as available_eans
                          FROM " . TABLE_PRODUCT_IDENTIFIER_EAN_BLOCKS . " b
                          WHERE (b.status IN ('active', 'depleted') OR b.status IS NULL)
                      ");
                      $pool_stats = xtc_db_fetch_array($pool_stats_query);
                      
                      // DEBUG: Zeige was geladen wurde
                      if (!$pool_stats && $debug === true) {
                          echo '<div style="background: #f8d7da; border: 1px solid #f5c6cb; padding: 15px; margin: 15px; border-radius: 4px;">';
                          echo '<strong>DEBUG:</strong> Keine Statistik-Daten gefunden!<br>';
                          echo 'Query: ' . htmlspecialchars("SELECT COUNT(DISTINCT b.block_id) as total_blocks FROM " . TABLE_PRODUCT_IDENTIFIER_EAN_BLOCKS . " b") . '<br>';
                          echo 'TABLE_PRODUCT_IDENTIFIER_EAN_BLOCKS = ' . TABLE_PRODUCT_IDENTIFIER_EAN_BLOCKS;
                          echo '</div>';
                          $pool_stats = array('total_blocks' => 0, 'total_eans' => 0, 'used_eans' => 0, 'available_eans' => 0);
                      }
                      
                      // Blöcke laden
                      $blocks_query = xtc_db_query("
                          SELECT 
                              block_id,
                              block_number,
                              block_size,
                              purchased_at,
                              imported_at,
                              total_eans,
                              used_eans,
                              available_eans,
                              status,
                              notes
                          FROM " . TABLE_PRODUCT_IDENTIFIER_EAN_BLOCKS . "
                          ORDER BY imported_at DESC
                      ");
                      
                      // DEBUG: Zeige Anzahl gefundener Blöcke
                      $blocks_count = xtc_db_num_rows($blocks_query);
                      if ($blocks_count == 0  && $debug === true) {
                          echo '<div style="background: #fff3cd; border: 1px solid #ffc107; padding: 15px; margin: 15px; border-radius: 4px;">';
                          echo '<strong>DEBUG:</strong> ' . BX_MPI_DEBUG_NO_BLOCKS . '<br>';
                          echo 'Query ergab: ' . $blocks_count . ' Zeilen<br>';
                          echo 'TABLE_PRODUCT_IDENTIFIER_EAN_BLOCKS = ' . TABLE_PRODUCT_IDENTIFIER_EAN_BLOCKS;
                          echo '</div>';
                      }
                      
                      $blocks = array();
                      while ($row = xtc_db_fetch_array($blocks_query)) {
                          $blocks[] = $row;
                      }
                  ?>
                  
                  <!-- Gesamtübersicht //-->
                  <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 15px; margin-bottom: 20px;">
                    <div style="background: #fff; border: 1px solid #ddd; border-radius: 4px; padding: 15px; text-align: center;">
                      <div style="font-size: 32px; font-weight: bold; color: #AF417E;"><?php echo $pool_stats['total_eans']; ?></div>
                      <div style="color: #666; margin-top: 5px;"><?php echo BX_MPI_POOL_TOTAL_EANS; ?></div>
                    </div>
                    <div style="background: #fff; border: 1px solid #ddd; border-radius: 4px; padding: 15px; text-align: center;">
                      <div style="font-size: 32px; font-weight: bold; color: #28a745;"><?php echo $pool_stats['available_eans']; ?></div>
                      <div style="color: #666; margin-top: 5px;"><?php echo BX_MPI_POOL_AVAILABLE; ?></div>
                    </div>
                    <div style="background: #fff; border: 1px solid #ddd; border-radius: 4px; padding: 15px; text-align: center;">
                      <div style="font-size: 32px; font-weight: bold; color: #dc3545;"><?php echo $pool_stats['used_eans']; ?></div>
                      <div style="color: #666; margin-top: 5px;"><?php echo BX_MPI_POOL_USED; ?></div>
                    </div>
                    <div style="background: #fff; border: 1px solid #ddd; border-radius: 4px; padding: 15px; text-align: center;">
                      <div style="font-size: 32px; font-weight: bold; color: #17a2b8;"><?php echo $pool_stats['total_blocks']; ?></div>
                      <div style="color: #666; margin-top: 5px;"><?php echo BX_MPI_POOL_TOTAL_BLOCKS; ?></div>
                    </div>
                  </div>
                  
                  <!-- Warnung: Niedriger Bestand //-->
                  <?php
                  $low_stock_threshold = 100;
                  $low_stock_query = xtc_db_query("
                      SELECT block_number, available_eans
                      FROM " . TABLE_PRODUCT_IDENTIFIER_EAN_BLOCKS . "
                      WHERE available_eans < $low_stock_threshold
                      AND status = 'active'
                  ");
                  
                  if (xtc_db_num_rows($low_stock_query) > 0):
                  ?>
                  <div style="background: #fff3cd; border: 1px solid #ffc107; border-radius: 4px; padding: 15px; margin-bottom: 20px;">
                    <strong style="color: #856404;"><?php echo BX_MPI_POOL_LOW_STOCK_WARNING; ?></strong><br>
                    <div style="margin-top: 10px; color: #856404;">
                      <?php while ($low_block = xtc_db_fetch_array($low_stock_query)): ?>
                        • Block <strong><?php echo htmlspecialchars($low_block['block_number']); ?></strong>: 
                        Nur noch <strong><?php echo $low_block['available_eans']; ?></strong> EANs verfügbar<br>
                      <?php endwhile; ?>
                    </div>
                  </div>
                  <?php endif; ?>
                  
                  <!-- Import-Button //-->
                  <div style="margin-bottom: 20px; text-align: right;">
                    <button onclick="document.getElementById('import-modal').style.display='block'" class="button" style="padding: 10px 20px; background: #28a745; color: #fff; font-weight: bold;">
                      <?php echo BX_MPI_POOL_IMPORT_BUTTON; ?>
                    </button>
                  </div>
                  
                  <!-- Block-Liste //-->
                  <?php if (!empty($blocks)): ?>
                  <div style="background: #fff; border: 1px solid #ddd; border-radius: 4px; padding: 15px;">
                                            <h3 style="margin: 0 0 15px 0;"><?php echo BX_MPI_POOL_BLOCKS_TITLE; ?></h3>
                    
                    <table class="tableBoxCenter collapse" style="width: 100%;">
                      <tr class="dataTableHeadingRow">
                        <td class="dataTableHeadingContent"><?php echo BX_MPI_POOL_BLOCK_NR; ?></td>
                        <td class="dataTableHeadingContent"><?php echo BX_MPI_POOL_BLOCK_SIZE_LABEL; ?></td>
                        <td class="dataTableHeadingContent"><?php echo BX_MPI_POOL_BLOCK_PURCHASED; ?></td>
                        <td class="dataTableHeadingContent"><?php echo BX_MPI_POOL_BLOCK_IMPORTED; ?></td>
                        <td class="dataTableHeadingContent"><?php echo BX_MPI_POOL_BLOCK_TOTAL; ?></td>
                        <td class="dataTableHeadingContent"><?php echo BX_MPI_POOL_BLOCK_FREE; ?></td>
                        <td class="dataTableHeadingContent"><?php echo BX_MPI_POOL_BLOCK_USED; ?></td>
                        <td class="dataTableHeadingContent"><?php echo BX_MPI_POOL_BLOCK_USAGE; ?></td>
                        <td class="dataTableHeadingContent"><?php echo BX_MPI_POOL_BLOCK_STATUS; ?></td>
                        <td class="dataTableHeadingContent" style="text-align: right;"><?php echo BX_MPI_POOL_BLOCK_ACTIONS; ?></td>
                      </tr>
                      <?php 
                      $row_class = 'dataTableRow';
                      foreach ($blocks as $block):
                        $row_class = $row_class == 'dataTableRow' ? 'dataTableRowAlt' : 'dataTableRow';
                        $usage_percent = $block['total_eans'] > 0 ? round(($block['used_eans'] / $block['total_eans']) * 100, 1) : 0;
                        
                        // Status-Badge
                        $status_colors = [
                            'active' => '#28a745',
                            'depleted' => '#dc3545',
                            'archived' => '#6c757d'
                        ];
                        $status_labels = [
                            'active' => '🟢 ' . BX_MPI_POOL_BLOCK_ACTIVE,
                            'depleted' => '🔴 ' . BX_MPI_POOL_BLOCK_DEPLETED_LABEL,
                            'archived' => '⚫ ' . BX_MPI_POOL_BLOCK_ARCHIVED
                        ];
                        
                        $status_color = $status_colors[$block['status']] ?? '#6c757d';
                        $status_label = $status_labels[$block['status']] ?? $block['status'];
                      ?>
                      <tr class="<?php echo $row_class; ?>">
                        <td class="dataTableContent">
                          <strong><?php echo htmlspecialchars($block['block_number']); ?></strong>
                          <?php if (!empty($block['notes'])): ?>
                          <br><small style="color: #666;"><?php echo htmlspecialchars(substr($block['notes'], 0, 30)); ?><?php echo strlen($block['notes']) > 30 ? '...' : ''; ?></small>
                          <?php endif; ?>
                        </td>
                        <td class="dataTableContent" style="text-align: center;"><?php echo $block['block_size']; ?></td>
                        <td class="dataTableContent"><?php echo date('d.m.Y', strtotime($block['purchased_at'])); ?></td>
                        <td class="dataTableContent"><?php echo date('d.m.Y H:i', strtotime($block['imported_at'])); ?></td>
                        <td class="dataTableContent" style="text-align: center;"><?php echo $block['total_eans']; ?></td>
                        <td class="dataTableContent" style="text-align: center; color: #28a745; font-weight: bold;"><?php echo $block['available_eans']; ?></td>
                        <td class="dataTableContent" style="text-align: center; color: #dc3545;"><?php echo $block['used_eans']; ?></td>
                        <td class="dataTableContent">
                          <div style="display: flex; align-items: center; gap: 5px;">
                            <div style="flex: 1; height: 20px; background: #e9ecef; border-radius: 3px; overflow: hidden;">
                              <div style="width: <?php echo $usage_percent; ?>%; height: 100%; background: <?php echo $usage_percent > 80 ? '#dc3545' : ($usage_percent > 50 ? '#ffc107' : '#28a745'); ?>;"></div>
                            </div>
                            <span style="font-size: 11px; color: #666;"><?php echo $usage_percent; ?>%</span>
                          </div>
                        </td>
                        <td class="dataTableContent">
                          <span style="background: <?php echo $status_color; ?>; color: #fff; padding: 3px 8px; border-radius: 3px; font-size: 11px;">
                            <?php echo $status_label; ?>
                          </span>
                        </td>
                        <td class="dataTableContent" style="text-align: right;">
                          <a href="<?php echo xtc_href_link(FILENAME_BX_MPI, 'view_block=' . $block['block_id']); ?>" class="button" style="padding: 3px 10px; font-size: 11px;">Details</a>
                          <?php if ($block['used_eans'] == 0): ?>
                          <a href="<?php echo xtc_href_link(FILENAME_BX_MPI, 'delete_block=' . $block['block_id']); ?>" 
                             onclick="return confirm('<?php echo BX_MPI_DELETE_BLOCK_CONFIRM; ?>');" 
                             class="button" 
                             style="padding: 3px 10px; font-size: 11px; background: #dc3545; color: #fff;">
                            Löschen
                          </a>
                          <?php endif; ?>
                        </td>
                      </tr>
                      <?php endforeach; ?>
                    </table>
                  </div>
                  <?php else: ?>
                  <div style="background: #f8f9fa; border: 1px solid #ddd; border-radius: 4px; padding: 40px; text-align: center; color: #666;">
                    <div style="font-size: 48px; margin-bottom: 15px;">📦</div>
                    <div style="font-size: 18px; font-weight: bold; margin-bottom: 10px;"><?php echo BX_MPI_POOL_NO_BLOCKS; ?></div>
                    <div style="font-size: 14px; margin-bottom: 20px;">
                      <?php echo BX_MPI_POOL_NO_BLOCKS_TEXT; ?>
                    </div>
                    <button onclick="document.getElementById('import-modal').style.display='block'" class="button" style="padding: 12px 30px; background: #28a745; color: #fff; font-weight: bold; font-size: 16px;">
                      <?php echo BX_MPI_POOL_IMPORT_FIRST_BLOCK; ?>
                    </button>
                  </div>
                  <?php endif; ?>
                  <?php } // Ende view_block else ?>
                  
                  <!-- Import-Modal //-->
                  <div id="import-modal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 9999; justify-content: center; align-items: center;">
                    <div style="background: #fff; border-radius: 8px; padding: 30px; max-width: 600px; width: 90%; max-height: 90vh; overflow-y: auto;">
                      <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; padding-bottom: 15px; border-bottom: 2px solid #AF417E;">
                        <h2 style="margin: 0; color: #AF417E;"><?php echo BX_MPI_POOL_IMPORT_MODAL_TITLE; ?></h2>
                        <button onclick="document.getElementById('import-modal').style.display='none'" style="background: #dc3545; color: #fff; border: none; padding: 5px 15px; border-radius: 4px; cursor: pointer; font-size: 18px; line-height: 1;">✕</button>
                      </div>
                      
                      <?php echo xtc_draw_form('import_block', FILENAME_BX_MPI, '', 'post', 'enctype="multipart/form-data"'); ?>
                        
                        <div style="margin-bottom: 20px;">
                          <label style="display: block; margin-bottom: 8px; font-weight: bold;"><?php echo BX_MPI_POOL_CSV_FILE_LABEL; ?></label>
                          <input type="file" name="csv_file" accept=".csv" required style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px;">
                          <small style="color: #666; margin-top: 5px; display: block;"><?php echo BX_MPI_POOL_CSV_FILE_HELP; ?></small>
                        </div>
                        
                        <div style="margin-bottom: 20px;">
                          <label style="display: block; margin-bottom: 8px; font-weight: bold;"><?php echo BX_MPI_POOL_BLOCK_NUMBER_LABEL; ?></label>
                          <input type="text" name="block_number" required placeholder="<?php echo BX_MPI_POOL_BLOCK_NUMBER_PLACEHOLDER; ?>" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px;">
                          <small style="color: #666; margin-top: 5px; display: block;"><?php echo BX_MPI_POOL_BLOCK_NUMBER_HELP; ?></small>
                        </div>
                        
                        <div style="margin-bottom: 20px;">
                          <label style="display: block; margin-bottom: 8px; font-weight: bold;"><?php echo BX_MPI_POOL_BLOCK_SIZE_LABEL_SELECT; ?></label>
                          <select name="block_size" required style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px;">
                            <option value="10">10 EANs</option>
                            <option value="100">100 EANs</option>
                            <option value="1000" selected>1.000 EANs</option>
                          </select>
                        </div>
                        
                        <div style="margin-bottom: 20px;">
                          <label style="display: block; margin-bottom: 8px; font-weight: bold;"><?php echo BX_MPI_POOL_PURCHASE_DATE_LABEL; ?></label>
                          <input type="date" name="purchased_at" required style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px;" value="<?php echo date('Y-m-d'); ?>">
                        </div>
                        
                        <div style="margin-bottom: 20px;">
                          <label style="display: block; margin-bottom: 8px; font-weight: bold;"><?php echo BX_MPI_POOL_NOTES_LABEL; ?></label>
                          <textarea name="notes" rows="3" placeholder="<?php echo BX_MPI_CSV_PLACEHOLDER; ?>" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px;"></textarea>
                        </div>
                        
                        <div style="background: #e7f3ff; border: 1px solid #b3d9ff; border-radius: 4px; padding: 15px; margin-bottom: 20px;">
                          <strong style="color: #0066cc;"><?php echo BX_MPI_POOL_IMPORT_INFO_TITLE; ?></strong><br>
                          <span style="color: #555; font-size: 13px;">
                            <?php echo BX_MPI_POOL_IMPORT_INFO_TEXT; ?>
                          </span>
                        </div>
                        
                        <div style="text-align: right;">
                          <button type="button" onclick="document.getElementById('import-modal').style.display='none'" class="button" style="padding: 10px 20px; margin-right: 10px;"><?php echo BX_MPI_POOL_IMPORT_CANCEL_BUTTON; ?></button>
                          <button type="submit" name="import_block" class="button" style="padding: 10px 30px; background: #28a745; color: #fff; font-weight: bold;"><?php echo BX_MPI_POOL_IMPORT_SUBMIT_BUTTON; ?></button>
                        </div>
                        
                      </form>
                    </div>
                  </div>
                  <!-- end Import-Modal //-->
                  
                </div>
                <!-- end tab-gtin //-->

                <div id="tab-history">
                  
                  <?php if (defined('MODULE_BX_MPI_ENABLE_HISTORY') && MODULE_BX_MPI_ENABLE_HISTORY == 'true'): ?>
                  
                  <!-- Filter-Bereich //-->
                  <div style="background: #f8f9fa; border: 1px solid #ddd; border-radius: 4px; padding: 15px; margin-bottom: 15px;">
                    <form method="get" action="<?php echo basename($_SERVER['PHP_SELF']); ?>">
                      <input type="hidden" name="tab" value="history">
                      <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 10px; align-items: end;">
                        
                        <div>
                          <label style="display: block; margin-bottom: 5px; font-weight: bold; font-size: 12px;"><?php echo BX_MPI_HISTORY_FILTER_PRODUCT_ID_LABEL; ?></label>
                          <input type="text" name="filter_product_id" value="<?php echo isset($_GET['filter_product_id']) ? (int)$_GET['filter_product_id'] : ''; ?>" placeholder="<?php echo BX_MPI_HISTORY_FILTER_PRODUCT_ID_PLACEHOLDER; ?>" style="width: 100%; padding: 6px;">
                        </div>
                        
                        <div>
                          <label style="display: block; margin-bottom: 5px; font-weight: bold; font-size: 12px;"><?php echo BX_MPI_HISTORY_FILTER_FIELD_LABEL; ?></label>
                          <select name="filter_field" style="width: 100%; padding: 6px;">
                            <option value=""><?php echo BX_MPI_HISTORY_FILTER_FIELD_ALL; ?></option>
                            <option value="products_sku" <?php echo (isset($_GET['filter_field']) && $_GET['filter_field'] == 'products_sku') ? 'selected' : ''; ?>><?php echo BX_MPI_HISTORY_FILTER_FIELD_SKU; ?></option>
                            <option value="ean" <?php echo (isset($_GET['filter_field']) && $_GET['filter_field'] == 'ean') ? 'selected' : ''; ?>><?php echo BX_MPI_HISTORY_FILTER_FIELD_EAN; ?></option>
                            <option value="wws_artikel_nr" <?php echo (isset($_GET['filter_field']) && $_GET['filter_field'] == 'wws_artikel_nr') ? 'selected' : ''; ?>><?php echo BX_MPI_HISTORY_FILTER_FIELD_WWS; ?></option>
                            <option value="warehouse_location" <?php echo (isset($_GET['filter_field']) && $_GET['filter_field'] == 'warehouse_location') ? 'selected' : ''; ?>><?php echo BX_MPI_HISTORY_FILTER_FIELD_WAREHOUSE; ?></option>
                          </select>
                        </div>
                        
                        <div>
                          <label style="display: block; margin-bottom: 5px; font-weight: bold; font-size: 12px;"><?php echo BX_MPI_HISTORY_FILTER_TIMEFRAME_LABEL; ?></label>
                          <select name="filter_timeframe" style="width: 100%; padding: 6px;">
                            <option value=""><?php echo BX_MPI_HISTORY_FILTER_TIMEFRAME_ALL; ?></option>
                            <option value="today" <?php echo (isset($_GET['filter_timeframe']) && $_GET['filter_timeframe'] == 'today') ? 'selected' : ''; ?>><?php echo BX_MPI_HISTORY_FILTER_TIMEFRAME_TODAY; ?></option>
                            <option value="week" <?php echo (isset($_GET['filter_timeframe']) && $_GET['filter_timeframe'] == 'week') ? 'selected' : ''; ?>><?php echo BX_MPI_HISTORY_FILTER_TIMEFRAME_WEEK; ?></option>
                            <option value="month" <?php echo (isset($_GET['filter_timeframe']) && $_GET['filter_timeframe'] == 'month') ? 'selected' : ''; ?>><?php echo BX_MPI_HISTORY_FILTER_TIMEFRAME_MONTH; ?></option>
                          </select>
                        </div>
                        
                        <div style="display: flex; gap: 5px;">
                          <button type="submit" class="button" style="flex: 1;"><?php echo BX_MPI_HISTORY_FILTER_BUTTON; ?></button>
                          <a href="<?php echo basename($_SERVER['PHP_SELF']); ?>" class="button" style="flex: 1; text-align: center; text-decoration: none;">↺ <?php echo BX_MPI_RESET_BUTTON; ?></a>
                        </div>
                        
                      </div>
                    </form>
                  </div>
                  
                  <!-- Historie-Tabelle //-->
                  <?php
                  // Filter aufbauen
                  $where_conditions = array();
                  
                  if (isset($_GET['filter_product_id']) && !empty($_GET['filter_product_id'])) {
                      $where_conditions[] = "p.products_id = '" . (int)$_GET['filter_product_id'] . "'";
                  }
                  
                  if (isset($_GET['filter_field']) && !empty($_GET['filter_field'])) {
                      $where_conditions[] = "h.field_name = '" . xtc_db_input($_GET['filter_field']) . "'";
                  }
                  
                  if (isset($_GET['filter_timeframe']) && !empty($_GET['filter_timeframe'])) {
                      switch ($_GET['filter_timeframe']) {
                          case 'today':
                              $where_conditions[] = "DATE(h.changed_at) = CURDATE()";
                              break;
                          case 'week':
                              $where_conditions[] = "h.changed_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)";
                              break;
                          case 'month':
                              $where_conditions[] = "h.changed_at >= DATE_SUB(NOW(), INTERVAL 1 MONTH)";
                              break;
                      }
                  }
                  
                  $where_sql = !empty($where_conditions) ? 'WHERE ' . implode(' AND ', $where_conditions) : '';
                  
                  // Basis-Query für splitPageResults (ohne LIMIT)
                  $history_query_raw = "
                      SELECT h.*, p.products_sku, p.products_id
                      FROM " . TABLE_PRODUCT_IDENTIFIER_HISTORY . " h
                      LEFT JOIN " . TABLE_PRODUCT_IDENTIFIERS . " p ON h.identifier_id = p.identifier_id
                      $where_sql
                      ORDER BY h.changed_at DESC
                  ";
                  
                  // splitPageResults initialisieren
                  $history_page     = isset($_GET['history_page']) ? (int)$_GET['history_page'] : 1;
                  $history_max_rows = MAX_DISPLAY_MPI_RESULTS;
                  $history_count    = 0;
                  
                  $history_split = new splitPageResults($history_page, $history_max_rows, $history_query_raw, $history_count, '*', '', 'history_page');
                  
                  // Query mit LIMIT ausführen
                  $history_query = xtc_db_query($history_query_raw);

                  if ($history_count > 0): ?>
                  
                  <table class="tableBoxCenter collapse" style="width: 100%;">
                    <tr class="dataTableHeadingRow">
                      <td class="dataTableHeadingContent" style="width: 140px;"><?php echo BX_MPI_HISTORY_TABLE_TIMESTAMP; ?></td>
                      <td class="dataTableHeadingContent" style="width: 200px;"><?php echo BX_MPI_HISTORY_TABLE_SKU; ?></td>
                      <td class="dataTableHeadingContent" style="width: 80px;"><?php echo BX_MPI_HISTORY_TABLE_PRODUCT_ID; ?></td>
                      <td class="dataTableHeadingContent" style="width: 120px;"><?php echo BX_MPI_HISTORY_TABLE_FIELD; ?></td>
                      <td class="dataTableHeadingContent" style="width: 150px;"><?php echo BX_MPI_HISTORY_TABLE_OLD_VALUE; ?></td>
                      <td class="dataTableHeadingContent" style="width: 150px;"><?php echo BX_MPI_HISTORY_TABLE_NEW_VALUE; ?></td>
                      <td class="dataTableHeadingContent"><?php echo BX_MPI_HISTORY_TABLE_REASON; ?></td>
                    </tr>
                    
                    <?php while ($row = xtc_db_fetch_array($history_query)): ?>
                    <tr class="dataTableRow">
                      <td class="dataTableContent"><?php echo date('d.m.Y H:i:s', strtotime($row['changed_at'])); ?></td>
                      <td class="dataTableContent">
                        <?php if ($row['products_sku']): ?>
                          <span style="font-family: monospace; font-size: 11px;"><?php echo htmlspecialchars($row['products_sku']); ?></span>
                        <?php else: ?>
                          <span style="color: #999;">-</span>
                        <?php endif; ?>
                      </td>
                      <td class="dataTableContent" style="text-align: center;"><?php echo (int)$row['products_id']; ?></td>
                      <td class="dataTableContent">
                        <strong><?php echo htmlspecialchars($row['field_name']); ?></strong>
                      </td>
                      <td class="dataTableContent" style="font-family: monospace; font-size: 11px; color: #dc3545;">
                        <?php echo htmlspecialchars($row['old_value'] ?: '-'); ?>
                      </td>
                      <td class="dataTableContent" style="font-family: monospace; font-size: 11px; color: #28a745;">
                        <?php echo htmlspecialchars($row['new_value'] ?: '-'); ?>
                      </td>
                      <td class="dataTableContent">
                        <small style="color: #666;"><?php echo htmlspecialchars($row['change_reason'] ?: '-'); ?></small>
                      </td>
                    </tr>
                    <?php endwhile; ?>
                    
                  </table>
                  
                  <!-- Pagination mit splitPageResults //-->
                  <table width="100%" border="0" cellspacing="0" cellpadding="2" style="margin-top: 10px;">
                    <tr>
                      <td class="smallText pdg2 flt-l">
                        <span style="line-height: 28px;">
                          <?php echo $history_split->display_count($history_count, $history_max_rows, $history_page, BX_MPI_DISPLAY_COUNT); ?>
                        </span>
                      </td>
                      <td class="smallText pdg2 flt-r">
                        <?php 
                        // URL-Parameter für Pagination beibehalten (sicher)
                        $url_params = array();
                        if ($search_product_id > 0) {
                            $url_params[] = 'search_product_id=' . $search_product_id;
                        }
                        if (!empty($search_sku)) {
                            $url_params[] = 'search_sku=' . urlencode($search_sku);
                        }
                        if (!empty($search_ean)) {
                            $url_params[] = 'search_ean=' . urlencode($search_ean);
                        }
                        $url_params_str = !empty($url_params) ? implode('&', $url_params) : '';
                        
                        echo $history_split->display_links($history_count, $history_max_rows, MAX_DISPLAY_PAGE_LINKS, $history_page, $url_params_str, 'history_page');
                        ?>
                      </td>
                    </tr>
                    <tr>
                      <td class="smallText pdg2" colspan="2">
                        <?php echo draw_input_per_page(FILENAME_BX_MPI, $cfg_max_display_history_key, $page_max_display_history); ?>
                      </td>
                    </tr>
                  </table>
                  <!-- end Pagination //-->                  
                  <?php else: ?>
                  
                  <div style="background: #f8f9fa; border: 1px solid #ddd; border-radius: 4px; padding: 40px; text-align: center; color: #666;">
                    <div style="font-size: 48px; margin-bottom: 10px;">📋</div>
                    <div style="font-size: 16px; margin-bottom: 10px;"><?php echo BX_MPI_HISTORY_NO_ENTRIES; ?></div>
                    <div style="font-size: 13px;">
                      <?php if (!empty($where_conditions)): ?>
                        <?php echo BX_MPI_HISTORY_NO_ENTRIES_FILTERED; ?>
                      <?php else: ?>
                        <?php echo BX_MPI_HISTORY_NO_ENTRIES_DEFAULT; ?>
                      <?php endif; ?>
                    </div>
                  </div>
                  
                  <?php endif; ?>
                  
                  <?php else: ?>
                  
                  <!-- Historie deaktiviert //-->
                  <div style="background: #fff3cd; border: 1px solid #ffc107; border-radius: 4px; padding: 20px; text-align: center;">
                    <div style="font-size: 32px; margin-bottom: 10px;">⚠️</div>
                    <div style="font-size: 16px; font-weight: bold; margin-bottom: 10px;"><?php echo BX_MPI_HISTORY_DISABLED_TITLE; ?></div>
                    <div style="color: #666; margin-bottom: 15px;">
                      <?php echo BX_MPI_HISTORY_DISABLED_DESC; ?>
                    </div>
                    <a href="#tab-settings" class="button" onclick="document.querySelector('.tabs .tab-nav a[href=\'#tab-settings\']').click(); return false;"><?php echo BX_MPI_HISTORY_SETTINGS_LINK; ?></a>
                  </div>
                  
                  <?php endif; ?>
                  
                </div>
                <!-- end tab-history //-->

                <div id="tab-settings">
                  
                  <!-- Info-Box: Statistiken //-->
                  <div style="background: #e7f3ff; border: 1px solid #b3d9ff; border-radius: 4px; padding: 20px; margin-bottom: 20px;">
                    <h4 style="margin: 0 0 15px 0; color: #0066cc;"><?php echo BX_MPI_SETTINGS_STATISTICS_TITLE; ?></h4>
                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px;">
                      <div>
                        <div style="font-size: 24px; font-weight: bold; color: #AF417E; text-align: center;"><?php echo $stats['total_identifiers']; ?></div>
                        <div style="color: #666; font-size: 13px; text-align: center;"><?php echo BX_MPI_DASHBOARD_TOTAL_IDENTIFIERS; ?></div>
                      </div>
                      <div>
                        <div style="font-size: 24px; font-weight: bold; color: #28a745; text-align: center;"><?php echo $stats['with_ean']; ?></div>
                        <div style="color: #666; font-size: 13px; text-align: center;"><?php echo BX_MPI_DASHBOARD_WITH_EAN; ?></div>
                      </div>
                      <div>
                        <div style="font-size: 24px; font-weight: bold; color: #ffc107; text-shadow: 1px 1px 0 #fff; text-align: center;"><?php echo $stats['pseudo_ean']; ?></div>
                        <div style="color: #666; font-size: 13px; text-align: center;"><?php echo BX_MPI_DASHBOARD_PSEUDO_EAN; ?></div>
                      </div>
                      <div>
                        <div style="font-size: 24px; font-weight: bold; color: #17a2b8; text-align: center;"><?php echo $stats['gs1_ean']; ?></div>
                        <div style="color: #666; font-size: 13px; text-align: center;"><?php echo BX_MPI_DASHBOARD_GS1_EAN; ?></div>
                      </div>
                    </div>
                  </div>
                  
                  <div style="background: #f8f9fa; border: 1px solid #ddd; border-radius: 4px; padding: 20px; margin-bottom: 20px;">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
                      <h3 style="margin: 0;"><?php echo BX_MPI_SETTINGS_MODULE_SETTINGS; ?> <span style="font-weight: normal; font-size: small;"">(<?php echo BX_MPI_SETTINGS_CONFIG_ID; ?>: <?php echo MODULE_BX_MPI_CONFIG_ID; ?>)</span></h3>
                      <a href="<?php echo xtc_href_link(FILENAME_CONFIGURATION, 'gID=' . (defined('MODULE_BX_MPI_CONFIG_ID') ? MODULE_BX_MPI_CONFIG_ID : '6')); ?>" class="button" target="_blank">
                        🔧 <?php echo BX_MPI_SETTINGS_ADVANCED_CONFIG; ?>
                      </a>
                    </div>
                    <p style="color: #666; font-size: 13px; margin: 0;">
                      <?php echo BX_MPI_SETTINGS_INTRO; ?>
                      <?php echo BX_MPI_SETTINGS_INTRO_ADVANCED; ?>
                    </p>
                  </div>
                  
                  <?php echo xtc_draw_form('mpi_settings', FILENAME_BX_MPI, '', 'post'); ?>
                  
                  <!-- Info: Festes SKU-Format //-->
                  <div style="background: #e7f3ff; border-left: 4px solid #0066cc; padding: 15px; margin-bottom: 20px; border-radius: 4px;">
                    <h4 style="margin: 0 0 10px 0; color: #0066cc;">ℹ️ <?php echo BX_MPI_SETTINGS_SKU_FORMAT_TITLE; ?></h4>
                    <div style="font-size: 13px; color: #555;">
                      <strong><?php echo BX_MPI_SETTINGS_SKU_FORMAT_FIXED; ?>:</strong> <code style="background: #fff; padding: 2px 6px; border-radius: 3px; font-size: 14px;">PREFIX + PID(4) + "_" + OID(4) + "-" + VID(4) + "x" + ...</code><br>
                      <strong><?php echo BX_MPI_SETTINGS_SKU_FORMAT_EXAMPLE; ?>:</strong> <code style="background: #fff; padding: 2px 6px; border-radius: 3px; font-size: 14px;">SKU0042_0003-0045x0006-0195</code><br>
                      <small style="color: #666;">• <?php echo BX_MPI_SETTINGS_SKU_FORMAT_SEPARATOR_INFO; ?></small><br>
                      <small style="color: #666;">• <?php echo BX_MPI_SETTINGS_SKU_FORMAT_PADDING_INFO; ?></small><br>
                      <small style="color: #666;">• <?php echo BX_MPI_SETTINGS_SKU_FORMAT_OPTIMIZED_INFO; ?></small>
                    </div>
                  </div>
                  
                  <!-- Auto-Create //-->
                  <div style="background: #fff; border: 1px solid #ddd; border-radius: 4px; padding: 20px; margin-bottom: 15px;">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
                      <strong style="font-size: 16px;"><?php echo BX_MPI_SETTINGS_AUTO_CREATE_TITLE; ?></strong>
                      <select name="MODULE_BX_MPI_AUTO_CREATE" style="padding: 8px; border: 1px solid #ddd; border-radius: 4px; font-size: 14px;">
                        <option value="true" <?php echo (defined('MODULE_BX_MPI_AUTO_CREATE') && MODULE_BX_MPI_AUTO_CREATE == 'true') ? 'selected' : ''; ?>>✓ <?php echo BX_MPI_SETTINGS_ACTIVATED; ?></option>
                        <option value="false" <?php echo (defined('MODULE_BX_MPI_AUTO_CREATE') && MODULE_BX_MPI_AUTO_CREATE == 'false') ? 'selected' : ''; ?>>✗ <?php echo BX_MPI_SETTINGS_DEACTIVATED; ?></option>
                      </select>
                    </div>
                    <p style="color: #666; font-size: 13px; margin: 0;">
                      <?php echo BX_MPI_SETTINGS_AUTO_CREATE_DESC; ?>
                      <br><small><strong><?php echo BX_MPI_SETTINGS_RECOMMENDED; ?>:</strong> <?php echo BX_MPI_SETTINGS_ACTIVATED; ?> (true)</small>
                    </p>
                  </div>
                  
                  <!-- SKU Prefix //-->
                  <div style="background: #fff; border: 1px solid #ddd; border-radius: 4px; padding: 20px; margin-bottom: 15px;">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
                      <strong style="font-size: 16px;"><?php echo BX_MPI_SETTINGS_SKU_PREFIX_TITLE; ?></strong>
                      <?php 
                      $prefix_value = defined('MODULE_BX_MPI_SKU_PREFIX') ? MODULE_BX_MPI_SKU_PREFIX : 'SKU';
                      echo xtc_draw_input_field('MODULE_BX_MPI_SKU_PREFIX', $prefix_value, 'style="width: 150px; padding: 8px; border: 1px solid #ddd; border-radius: 4px; font-size: 14px;" maxlength="10" placeholder="' . BX_MPI_EXAMPLE_PREFIX . BX_MPI_SETTINGS_SKU_PREFIX_PLACEHOLDER . '"');
                      ?>
                    </div>
                    <p style="color: #666; font-size: 13px; margin: 0;">
                      <?php echo BX_MPI_SETTINGS_SKU_PREFIX_DESC; ?>
                      <br><small><strong><?php echo BX_MPI_SETTINGS_SKU_PREFIX_EXAMPLE_WITH; ?>:</strong> <code>SKU0042_0003-0045</code> | <strong><?php echo BX_MPI_SETTINGS_SKU_PREFIX_EXAMPLE_WITHOUT; ?>:</strong> <code>0042_0003-0045</code></small>
                      <br><small style="color: #999;"><?php echo BX_MPI_SETTINGS_SKU_PREFIX_NOTE; ?></small>
                    </p>
                  </div>
                  
                  <!-- Enable History //-->
                  <div style="background: #fff; border: 1px solid #ddd; border-radius: 4px; padding: 20px; margin-bottom: 15px;">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
                      <strong style="font-size: 16px;"><?php echo BX_MPI_SETTINGS_HISTORY_TITLE; ?></strong>
                      <select name="MODULE_BX_MPI_ENABLE_HISTORY" style="padding: 8px; border: 1px solid #ddd; border-radius: 4px; font-size: 14px;">
                        <option value="true" <?php echo (defined('MODULE_BX_MPI_ENABLE_HISTORY') && MODULE_BX_MPI_ENABLE_HISTORY == 'true') ? 'selected' : ''; ?>>✓ <?php echo BX_MPI_SETTINGS_ACTIVATED; ?></option>
                        <option value="false" <?php echo (defined('MODULE_BX_MPI_ENABLE_HISTORY') && MODULE_BX_MPI_ENABLE_HISTORY == 'false') ? 'selected' : ''; ?>>✗ <?php echo BX_MPI_SETTINGS_DEACTIVATED; ?></option>
                      </select>
                    </div>
                    <p style="color: #666; font-size: 13px; margin: 0;">
                      <?php echo BX_MPI_SETTINGS_HISTORY_DESC; ?>
                      <br><small><strong><?php echo BX_MPI_SETTINGS_RECOMMENDED; ?>:</strong> <?php echo BX_MPI_SETTINGS_HISTORY_RECOMMENDED; ?></small>
                    </p>
                  </div>
                  
                  <!-- EAN Mode //-->
                  <div style="background: #fff; border: 1px solid #ddd; border-radius: 4px; padding: 20px; margin-bottom: 15px;">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
                      <strong style="font-size: 16px;"><?php echo BX_MPI_SETTINGS_EAN_MODE_TITLE; ?></strong>
                      <select name="MODULE_BX_MPI_EAN_MODE" style="padding: 8px; border: 1px solid #ddd; border-radius: 4px; font-size: 14px; min-width: 200px;">
                        <?php
                        $ean_mode_value = defined('MODULE_BX_MPI_EAN_MODE') ? MODULE_BX_MPI_EAN_MODE : 'manual';
                        ?>
                        <option value="manual" <?php echo $ean_mode_value == 'manual' ? 'selected' : ''; ?>>📝 <?php echo BX_MPI_SETTINGS_EAN_MODE_MANUAL; ?></option>
                        <option value="auto_pseudo" <?php echo $ean_mode_value == 'auto_pseudo' ? 'selected' : ''; ?>>🏪 <?php echo BX_MPI_SETTINGS_EAN_MODE_AUTO_PSEUDO; ?></option>
                        <option value="auto_gs1" <?php echo $ean_mode_value == 'auto_gs1' ? 'selected' : ''; ?>>🌐 <?php echo BX_MPI_SETTINGS_EAN_MODE_AUTO_GS1; ?></option>
                      </select>
                    </div>
                    <div style="background: #f8f9fa; padding: 12px; border-radius: 4px; font-size: 13px; color: #555;">
                      <strong><?php echo BX_MPI_SETTINGS_EAN_MODE_DETAIL_TITLE; ?>:</strong><br>
                      • <strong><?php echo BX_MPI_SETTINGS_EAN_MODE_MANUAL; ?>:</strong> <?php echo BX_MPI_SETTINGS_EAN_MODE_MANUAL_DESC; ?><br>
                      • <strong>Pseudo-EAN:</strong> <?php echo BX_MPI_SETTINGS_EAN_MODE_PSEUDO_DESC; ?><br>
                      • <strong>GS1-EAN:</strong> <?php echo BX_MPI_SETTINGS_EAN_MODE_GS1_DESC; ?>
                    </div>
                  </div>
                  
                  <!-- GS1 Prefix //-->
                  <div style="background: #fff; border: 1px solid #ddd; border-radius: 4px; padding: 20px; margin-bottom: 15px;">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
                      <strong style="font-size: 16px;"><?php echo BX_MPI_SETTINGS_GS1_PREFIX_TITLE; ?> <small style="color: #999; font-weight: normal;">(<?php echo BX_MPI_SETTINGS_GS1_PREFIX_ONLY_FOR; ?>)</small></strong>
                      <?php 
                      $gs1_value = defined('MODULE_BX_MPI_GS1_PREFIX') ? MODULE_BX_MPI_GS1_PREFIX : '';
                      echo xtc_draw_input_field('MODULE_BX_MPI_GS1_PREFIX', $gs1_value, 'style="width: 200px; padding: 8px; border: 1px solid #ddd; border-radius: 4px; font-size: 14px;" maxlength="10" placeholder="' . BX_MPI_EXAMPLE_PREFIX . '4004332"');
                      ?>
                    </div>
                    <p style="color: #666; font-size: 13px; margin: 0;">
                      <?php echo BX_MPI_SETTINGS_GS1_PREFIX_DESC; ?>
                      <br><small><strong><?php echo BX_MPI_SETTINGS_GS1_PREFIX_IMPORTANT; ?>:</strong> <?php echo BX_MPI_SETTINGS_GS1_PREFIX_MEMBERSHIP; ?></small>
                    </p>
                    <div style="background: #fff3cd; padding: 10px; border-radius: 4px; margin-top: 10px; font-size: 12px; border-left: 4px solid #ffc107;">
                      <strong>ℹ️ Info:</strong> <?php echo BX_MPI_SETTINGS_GS1_PREFIX_INFO; ?>: 
                      <a href="https://www.gs1-germany.de" target="_blank" style="color: #0066cc;">www.gs1-germany.de</a>
                    </div>
                  </div>
                  
                  <!-- Speichern-Button //-->
                  <div style="text-align: right; padding: 20px 0;">
                    <button type="submit" name="save_settings" class="button" style="padding: 12px 40px; background: #28a745; color: #fff; font-size: 16px; font-weight: bold; border: none; border-radius: 4px; cursor: pointer;">
                      ✓ <?php echo BX_MPI_SETTINGS_SAVE_BUTTON; ?>
                    </button>
                  </div>
                  
                  </form>
                  
                </div>
                <!-- end tab-settings //-->

              </div>
              <!-- end tab-content //-->

            </div>
            <!-- end tabs //-->
          
          </td> <!-- boxCenterLeft eof //-->
          <td class="boxRight">

            <div id="tab-dashboard-right">
            <?php
              // Box 1: Aktuelle Konfiguration
              $heading  = array();
              $contents = array();
              
              $heading[]  = array('text' => '<strong>' . BX_MPI_SIDEBAR_CONFIG_TITLE . '</strong>');
              
              $config_html = '
                <div style="padding: 10px;">
                  <div style="margin-bottom: 12px; padding-bottom: 12px; border-bottom: 1px solid #eee;">
                    <div style="font-size: 11px; color: #666; margin-bottom: 4px;">' . BX_MPI_SIDEBAR_MODULE_STATUS . '</div>
                    <span class="info-mpi-button" style="background: ' . ($config['status'] == 'true' ? '#28a745' : '#dc3545') . '; color: #fff; font-size: 11px; padding: 3px 8px;">
                      ' . ($config['status'] == 'true' ? BX_MPI_SIDEBAR_ACTIVE : BX_MPI_SIDEBAR_INACTIVE) . '
                    </span>
                  </div>
                  
                  <div style="margin-bottom: 12px; padding-bottom: 12px; border-bottom: 1px solid #eee;">
                    <div style="font-size: 11px; color: #666; margin-bottom: 4px;">' . BX_MPI_SIDEBAR_AUTO_CREATE . '</div>
                    <span class="info-mpi-button" style="background: ' . ($config['auto_create'] == 'true' ? '#28a745' : '#6c757d') . '; color: #fff; font-size: 11px; padding: 3px 8px;">
                      ' . ($config['auto_create'] == 'true' ? BX_MPI_SIDEBAR_ENABLED : BX_MPI_SIDEBAR_DISABLED) . '
                    </span>
                  </div>
                  
                  <div style="margin-bottom: 12px; padding-bottom: 12px; border-bottom: 1px solid #eee;">
                    <div style="font-size: 11px; color: #666; margin-bottom: 4px;">' . BX_MPI_SIDEBAR_EAN_MODE . '</div>';
              
              $ean_mode_labels = array(
                'manual'      => BX_MPI_SETTINGS_EAN_MODE_MANUAL,
                'auto_pseudo' => BX_MPI_SETTINGS_EAN_MODE_AUTO_PSEUDO,
                'auto_gs1'    => BX_MPI_SETTINGS_EAN_MODE_AUTO_GS1
              );
              $ean_mode_colors = array(
                'manual'      => '#6c757d',
                'auto_pseudo' => '#ffc107',
                'auto_gs1'    => '#17a2b8'
              );
              $ean_mode_icons = array(
                'manual'      => '✋',
                'auto_pseudo' => '🏷️',
                'auto_gs1'    => '✅'
              );
              
              $config_html .= '
                    <span class="info-mpi-button" style="background: ' . $ean_mode_colors[$config['ean_mode']] . '; color: ' . ($config['ean_mode'] == 'auto_pseudo' ? '#000' : '#fff') . '; font-size: 11px; padding: 3px 8px;">
                      ' . $ean_mode_icons[$config['ean_mode']] . ' ' . $ean_mode_labels[$config['ean_mode']] . '
                    </span>
                  </div>
                  
                  <div style="margin-bottom: 8px;">
                    <div style="font-size: 11px; color: #666; margin-bottom: 4px;">'.BX_MPI_SETTINGS_HISTORY_TITLE.'</div>
                    <span class="info-mpi-button" style="background: ' . ($config['history_enabled'] == 'true' ? '#28a745' : '#6c757d') . '; color: #fff; font-size: 11px; padding: 3px 8px;">
                      ' . ($config['history_enabled'] == 'true' ? BX_MPI_SIDEBAR_ACTIVE : BX_MPI_SIDEBAR_INACTIVE) . '
                    </span>
                  </div>
                  
                  <div style="margin-top: 15px; padding-top: 15px; border-top: 2px solid #eee; text-align: center;">
                    <a href="' . xtc_href_link(FILENAME_CONFIGURATION, 'gID=' . (defined('MODULE_BX_MPI_CONFIG_ID') ? MODULE_BX_MPI_CONFIG_ID : '6')) . '" class="button" target="_blank" style="font-size: 11px; padding: 4px 10px; text-decoration: none; display: inline-block;">
                      🔧 '.BX_MPI_SETTINGS_ADVANCED_CONFIG.'
                    </a>
                  </div>
                </div>
              ';
              
              $contents[] = array('text' => $config_html);
              
              if ( (xtc_not_null($heading)) && (xtc_not_null($contents)) ) {
                $box = new box;
                echo $box->infoBox($heading, $contents);
              }

            ?>
            </div> <!-- tab-dashboard-right eof //-->

            <div id="tab-admin-right">
            <?php
              // Statistiken für aktuellen Filter berechnen
              $filter_stats = array(
                'total' => 0,
                'with_ean' => 0,
                'without_ean' => 0,
                'pseudo_ean' => 0,
                'gs1_ean' => 0,
                'without_warehouse' => 0
              );
              
              // Nur berechnen wenn wir Identifier haben
              if (!empty($identifiers)) {
                $filter_stats['total'] = count($identifiers);
                foreach ($identifiers as $id) {
                  if (!empty($id['products_ean'])) {
                    $filter_stats['with_ean']++;
                    if (substr($id['products_ean'], 0, 1) == '2') {
                      $filter_stats['pseudo_ean']++;
                    } else {
                      $filter_stats['gs1_ean']++;
                    }
                  } else {
                    $filter_stats['without_ean']++;
                  }
                  if (empty($id['warehouse_location'])) {
                    $filter_stats['without_warehouse']++;
                  }
                }
              }
              
              // Box 1: Schnell-Statistik
              $heading  = array();
              $contents = array();
              
              $heading[]  = array('text' => '<strong>📊 '.BX_MPI_CURRENT_VIEW.'</strong>');
              
              $stats_html = '<div style="padding: 10px;">';
              
              if ($filter_stats['total'] > 0) {
                $with_ean_percent = round(($filter_stats['with_ean'] / $filter_stats['total']) * 100);
                $without_ean_percent = round(($filter_stats['without_ean'] / $filter_stats['total']) * 100);
                
                $stats_html .= '
                  <div style="margin-bottom: 12px; padding-bottom: 12px; border-bottom: 1px solid #eee;">
                    <div style="font-size: 11px; color: #666; margin-bottom: 4px;">'.BX_MPI_SIDEBAR_STATS_DISPLAYED.'</div>
                    <div style="font-size: 20px; font-weight: bold; color: #AF417E;">' . $filter_stats['total'] . '</div>
                  </div>
                  
                  <div style="margin-bottom: 12px; padding-bottom: 12px; border-bottom: 1px solid #eee;">
                    <div style="font-size: 11px; color: #666; margin-bottom: 4px;">'.BX_MPI_SIDEBAR_STATS_WITH_EAN.'</div>
                    <div style="font-size: 18px; font-weight: bold; color: #28a745;">' . $filter_stats['with_ean'] . ' <small style="font-size: 11px; color: #666;">(' . $with_ean_percent . '%)</small></div>
                  </div>
                  
                  <div style="margin-bottom: 12px; padding-bottom: 12px; border-bottom: 1px solid #eee;">
                    <div style="font-size: 11px; color: #666; margin-bottom: 4px;">'.BX_MPI_SIDEBAR_STATS_WITHOUT_EAN.'</div>
                    <div style="font-size: 18px; font-weight: bold; color: #dc3545;">' . $filter_stats['without_ean'] . ' <small style="font-size: 11px; color: #666;">(' . $without_ean_percent . '%)</small></div>
                  </div>
                  
                  <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px; margin-bottom: 12px; padding-bottom: 12px; border-bottom: 1px solid #eee;">
                    <div>
                      <div style="font-size: 11px; color: #666; margin-bottom: 4px;">'.BX_MPI_SIDEBAR_STATS_PSEUDO.'</div>
                      <div style="font-size: 16px; font-weight: bold; color: #ffc107; text-shadow: 1px 1px 0 #fff;">' . $filter_stats['pseudo_ean'] . '</div>
                    </div>
                    <div>
                      <div style="font-size: 11px; color: #666; margin-bottom: 4px;">'.BX_MPI_SIDEBAR_STATS_GS1.'</div>
                      <div style="font-size: 16px; font-weight: bold; color: #17a2b8;">' . $filter_stats['gs1_ean'] . '</div>
                    </div>
                  </div>
                  
                  <div>
                    <div style="font-size: 11px; color: #666; margin-bottom: 4px;">'.BX_MPI_SIDEBAR_STATS_WITHOUT_WAREHOUSE.'</div>
                    <div style="font-size: 16px; font-weight: bold; color: #6c757d;">' . $filter_stats['without_warehouse'] . '</div>
                  </div>
                ';
              } else {
                $stats_html .= '
                  <div style="text-align: center; color: #999; padding: 20px 0;">
                    <div style="font-size: 32px; margin-bottom: 10px;">📊</div>
                    <div style="font-size: 12px;">'.BX_MPI_SIDEBAR_STATS_NO_DATA.'</div>
                  </div>
                ';
              }
              
              $stats_html .= '</div>';
              
              $contents[] = array('text' => $stats_html);
              
              if ( (xtc_not_null($heading)) && (xtc_not_null($contents)) ) {
                $box = new box;
                echo $box->infoBox($heading, $contents);
              }
              
              // Box 2: Quick Actions
              $include_get_params = array('page', 'cPath');
              $link_params = xtc_get_all_get_params_include($include_get_params);

              $heading  = array();
              $contents = array();
              
              $heading[]  = array('text' => '<strong>⚡ Quick Filter</strong>');
              
              $actions_html = '
                <div style="padding: 10px; display: flex; flex-wrap: wrap; gap: 5px;">
                  <a href="' . xtc_href_link(FILENAME_BX_MPI, $link_params .'filter_no_ean=1') . '" class="button" style="text-align: center; text-decoration: none; font-size: 11px; padding: 6px; flex: 0 0 calc(33.333% - 4px);">
                    ' . BX_MPI_SIDEBAR_FILTER_NO_EAN . '
                  </a>
                  <a href="' . xtc_href_link(FILENAME_BX_MPI, $link_params .'filter_pseudo_ean=1') . '" class="button" style="text-align: center; text-decoration: none; font-size: 11px; padding: 6px; flex: 0 0 calc(33.333% - 4px);">
                    ' . BX_MPI_SIDEBAR_FILTER_PSEUDO_EAN . '
                  </a>
                  <a href="' . xtc_href_link(FILENAME_BX_MPI, $link_params .'filter_gs1_ean=1') . '" class="button" style="text-align: center; text-decoration: none; font-size: 11px; padding: 6px; flex: 0 0 calc(33.333% - 4px);">
                    ' . BX_MPI_SIDEBAR_FILTER_GS1_EAN . '
                  </a>
                  <a href="' . xtc_href_link(FILENAME_BX_MPI, 'filter_no_warehouse=1') . '" class="button" style="text-align: center; text-decoration: none; font-size: 11px; padding: 6px; flex: 0 0 calc(33.333% - 4px);">
                    ' . BX_MPI_SIDEBAR_FILTER_NO_WAREHOUSE . '
                  </a>
                  <a href="' . xtc_href_link(FILENAME_BX_MPI, $link_params) . '" class="button" style="text-align: center; text-decoration: none; font-size: 11px; padding: 6px; background: #6c757d; color: #fff; flex: 0 0 calc(33.333% - 4px);">
                    ' . BX_MPI_SIDEBAR_FILTER_RESET . '
                  </a>
                </div>
              ';
              
              $contents[] = array('text' => $actions_html);
              
              if ( (xtc_not_null($heading)) && (xtc_not_null($contents)) ) {
                $box = new box;
                echo $box->infoBox($heading, $contents);
              }
              
              // Box 3: Tipp (Kontext-sensitiv)
              $heading  = array();
              $contents = array();
              
              $heading[]  = array('text' => '<strong>' . BX_MPI_SIDEBAR_TIP_TITLE . '</strong>');
              
              $tip_html = '<div style="padding: 10px; font-size: 11px; line-height: 1.6;">';
              
              // Kontext-sensitive Tipps
              if (isset($_GET['filter_no_ean'])) {
                $tip_html .= '
                  <strong style="color: #dc3545;">' . BX_MPI_NO_EAN_TITLE . '</strong><br>
                  ' . BX_MPI_NO_EAN_DESC . '
                ';
              } elseif (isset($_GET['filter_pseudo_ean'])) {
                $tip_html .= '
                  <strong style="color: #ffc107; text-shadow: 1px 1px 0 #fff;">' . BX_MPI_PSEUDO_EAN_TITLE . '</strong><br>
                  ' . BX_MPI_PSEUDO_EAN_DESC . '
                ';
              } elseif (isset($_GET['filter_gs1_ean'])) {
                $tip_html .= '
                  <strong style="color: #17a2b8;">' . BX_MPI_GS1_EAN_TITLE . '</strong><br>
                  ' . BX_MPI_GS1_EAN_DESC . '
                ';
              } elseif (isset($_GET['filter_no_warehouse'])) {
                $tip_html .= '
                  <strong style="color: #6c757d;">' . BX_MPI_NO_WAREHOUSE_TITLE . '</strong><br>
                  ' . BX_MPI_NO_WAREHOUSE_DESC . '
                ';
              } elseif (!empty($search_sku) || !empty($search_ean) || $search_product_id > 0) {
                $tip_html .= '
                  <strong style="color: #AF417E;">' . BX_MPI_ACTIVE_FILTER_TITLE . '</strong><br>
                  ' . BX_MPI_FILTER_ACTIVE_INFO . '
                ';
              } else {
                $tip_html .= '
                  <strong style="color: #AF417E;">' . BX_MPI_SKU_FORMAT_TITLE . '</strong><br>
                  ' . BX_MPI_SKU_FORMAT_DESC . '
                ';
              }
              
              $tip_html .= '</div>';
              
              $contents[] = array('text' => $tip_html);
              
              if ( (xtc_not_null($heading)) && (xtc_not_null($contents)) ) {
                $box = new box;
                echo $box->infoBox($heading, $contents);
              }
            ?>
            </div> <!-- tab-admin-right eof //-->

            <div id="tab-gtin-right">
            <?php
              // Sidebar für EAN-Pool Tab
              
              // Box 1: Pool-Übersicht
              $heading  = array();
              $contents = array();
              
              $heading[]  = array('text' => '<strong>' . BX_MPI_POOL_STATUS_TITLE . '</strong>');
              
              $pool_sidebar_query = xtc_db_query("
                  SELECT 
                      COALESCE(SUM(total_eans), 0) as total,
                      COALESCE(SUM(used_eans), 0) as used,
                      COALESCE(SUM(available_eans), 0) as available
                  FROM " . TABLE_PRODUCT_IDENTIFIER_EAN_BLOCKS . "
                  WHERE status IN ('active', 'depleted')
              ");
              $pool_sidebar = xtc_db_fetch_array($pool_sidebar_query);
              
              $pool_html = '<div style="padding: 10px;">';
              
              if ($pool_sidebar['total'] > 0) {
                  $available_percent = round(($pool_sidebar['available'] / $pool_sidebar['total']) * 100, 1);
                  
                  $pool_html .= '
                  <div style="margin-bottom: 15px; text-align: center;">
                    <div style="font-size: 36px; font-weight: bold; color: #28a745; margin-bottom: 5px;">' . $pool_sidebar['available'] . '</div>
                    <div style="font-size: 12px; color: #666;">' . BX_MPI_AVAILABLE_EANS . '</div>
                  </div>
                  
                  <div style="margin-bottom: 15px;">
                    <div style="display: flex; justify-content: space-between; font-size: 11px; color: #666; margin-bottom: 3px;">
                      <span>' . BX_MPI_AVAILABLE_LABEL . '</span>
                      <span>' . $available_percent . '%</span>
                    </div>
                    <div style="height: 20px; background: #e9ecef; border-radius: 4px; overflow: hidden;">
                      <div style="width: ' . $available_percent . '%; height: 100%; background: ' . ($available_percent < 20 ? '#dc3545' : ($available_percent < 50 ? '#ffc107' : '#28a745')) . ';"></div>
                    </div>
                  </div>
                  
                  <table style="width: 100%; font-size: 11px; margin-top: 10px;">
                    <tr>
                      <td style="color: #666; padding: 3px 0;">' . BX_MPI_TOTAL_LABEL . '</td>
                      <td style="text-align: right; font-weight: bold; padding: 3px 0;">' . $pool_sidebar['total'] . '</td>
                    </tr>
                    <tr>
                      <td style="color: #666; padding: 3px 0;">' . BX_MPI_AVAILABLE_LABEL . ':</td>
                      <td style="text-align: right; font-weight: bold; color: #28a745; padding: 3px 0;">' . $pool_sidebar['available'] . '</td>
                    </tr>
                    <tr>
                      <td style="color: #666; padding: 3px 0;">' . BX_MPI_USED_LABEL . '</td>
                      <td style="text-align: right; font-weight: bold; color: #dc3545; padding: 3px 0;">' . $pool_sidebar['used'] . '</td>
                    </tr>
                  </table>
                  ';
                  
                  // Warnung bei niedrigem Bestand
                  if ($pool_sidebar['available'] < 100) {
                      $pool_html .= '
                      <div style="margin-top: 15px; padding: 10px; background: #fff3cd; border-left: 3px solid #ffc107; font-size: 11px; color: #856404;">
                        <strong>' . BX_MPI_LOW_STOCK_WARNING . '</strong><br>
                        ' . BX_MPI_LOW_STOCK_TEXT . '
                      </div>
                      ';
                  }
                  
              } else {
                  $pool_html .= '
                  <div style="text-align: center; padding: 20px 10px; color: #666;">
                    <div style="font-size: 32px; margin-bottom: 10px;">📦</div>
                    <div style="font-size: 12px;">' . BX_MPI_NO_BLOCKS_IMPORTED . '</div>
                  </div>
                  ';
              }
              
              $pool_html .= '</div>';
              
              $contents[] = array('text' => $pool_html);
              
              if ( (xtc_not_null($heading)) && (xtc_not_null($contents)) ) {
                $box = new box;
                echo $box->infoBox($heading, $contents);
              }
              
              // Box 2: Schnell-Info
              $heading  = array();
              $contents = array();
              
              $heading[]  = array('text' => '<strong>' . BX_MPI_GS1_INFO_TITLE . '</strong>');
              
              $info_html = '
              <div style="padding: 10px; font-size: 11px; color: #555;">
                <div style="margin-bottom: 10px;">
                  <strong>' . BX_MPI_BUY_EAN_BLOCKS . '</strong><br>
                  <a href="https://www.gs1-germany.de" target="_blank" style="color: #AF417E;">www.gs1-germany.de</a>
                </div>
                
                <div style="margin-bottom: 10px; padding: 8px; background: #f8f9fa; border-radius: 3px;">
                  <strong>' . BX_MPI_GS1_PRICES . '</strong><br>
                  ' . BX_MPI_GS1_PRICE_10 . '<br>
                  ' . BX_MPI_GS1_PRICE_100 . '<br>
                  ' . BX_MPI_GS1_PRICE_1000 . '
                </div>
                
                <div style="font-size: 10px; color: #999;">
                ' . BX_MPI_GS1_PRICE_NOTE . '
                </div>
              </div>';
              
              $contents[] = array('text' => $info_html);
              
              if ( (xtc_not_null($heading)) && (xtc_not_null($contents)) ) {
                $box = new box;
                echo $box->infoBox($heading, $contents);
              }
              
              // Box 3: Letzte Aktivitäten im Pool
              if ($pool_sidebar['total'] > 0) {
                  $heading  = array();
                  $contents = array();
                  
                  $heading[]  = array('text' => '<strong>🔄 Letzte Zuweisungen</strong>');
                  
                  $recent_assigns_query = xtc_db_query("
                      SELECT 
                          ep.ean,
                          ep.assigned_at,
                          pi.products_sku,
                          eb.block_number
                      FROM " . TABLE_PRODUCT_IDENTIFIER_EAN_POOL . " ep
                      LEFT JOIN " . TABLE_PRODUCT_IDENTIFIERS . " pi ON ep.identifier_id = pi.identifier_id
                      LEFT JOIN " . TABLE_PRODUCT_IDENTIFIER_EAN_BLOCKS . " eb ON ep.block_id = eb.block_id
                      WHERE ep.status = 'assigned'
                      AND ep.assigned_at IS NOT NULL
                      ORDER BY ep.assigned_at DESC
                      LIMIT 5
                  ");
                  
                  $recent_html = '<div style="padding: 10px; font-size: 11px;">';
                  
                  if (xtc_db_num_rows($recent_assigns_query) > 0) {
                      while ($recent = xtc_db_fetch_array($recent_assigns_query)) {
                          $recent_html .= '
                          <div style="margin-bottom: 10px; padding-bottom: 10px; border-bottom: 1px solid #eee;">
                            <div style="font-family: monospace; font-weight: bold; color: #AF417E;">' . htmlspecialchars($recent['ean']) . '</div>
                            <div style="font-size: 10px; color: #666; margin-top: 3px;">
                              → SKU: ' . htmlspecialchars($recent['products_sku'] ?: '-') . '<br>
                              Block: ' . htmlspecialchars($recent['block_number']) . '<br>
                              ' . date('d.m.Y H:i', strtotime($recent['assigned_at'])) . '
                            </div>
                          </div>
                          ';
                      }
                  } else {
                      $recent_html .= '<div style="text-align: center; padding: 20px 10px; color: #999;">Noch keine Zuweisungen</div>';
                  }
                  
                  $recent_html .= '</div>';
                  
                  $contents[] = array('text' => $recent_html);
                  
                  if ( (xtc_not_null($heading)) && (xtc_not_null($contents)) ) {
                    $box = new box;
                    echo $box->infoBox($heading, $contents);
                  }
              }
            ?>
            </div> <!-- tab-gtin-right eof //-->

            <div id="tab-history-right">
            <?php
            if (defined('MODULE_BX_MPI_ENABLE_HISTORY') && MODULE_BX_MPI_ENABLE_HISTORY == 'true') {
              // Statistiken für Sidebar
              $stats_today_query = xtc_db_query("
                  SELECT COUNT(*) as total
                  FROM " . TABLE_PRODUCT_IDENTIFIER_HISTORY . "
                  WHERE DATE(changed_at) = CURDATE()
              ");
              $stats_today = xtc_db_fetch_array($stats_today_query);
              
              $stats_week_query = xtc_db_query("
                  SELECT COUNT(*) as total
                  FROM " . TABLE_PRODUCT_IDENTIFIER_HISTORY . "
                  WHERE changed_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)
              ");
              $stats_week = xtc_db_fetch_array($stats_week_query);
              
              // Meist geänderte Felder
              $top_fields_query = xtc_db_query("
                  SELECT field_name, COUNT(*) as count
                  FROM " . TABLE_PRODUCT_IDENTIFIER_HISTORY . "
                  GROUP BY field_name
                  ORDER BY count DESC
                  LIMIT 5
              ");
              
              // Box 1: Statistiken
              $heading  = array();
              $contents = array();
              
              $heading[]  = array('text' => '<strong>📊 ' . BX_MPI_HISTORY_SIDEBAR_STATS_TITLE . '</strong>');
              $contents[] = array('text' => '
                <div style="padding: 10px;">
                  <div style="margin-bottom: 15px;">
                    <div style="font-size: 24px; font-weight: bold; color: #AF417E;">' . (int)$stats_today['total'] . '</div>
                    <div style="color: #666; font-size: 12px;">' . BX_MPI_HISTORY_SIDEBAR_CHANGES_TODAY . '</div>
                  </div>
                  <div style="margin-bottom: 15px;">
                    <div style="font-size: 24px; font-weight: bold; color: #28a745;">' . (int)$stats_week['total'] . '</div>
                    <div style="color: #666; font-size: 12px;">' . BX_MPI_HISTORY_SIDEBAR_LAST_7_DAYS . '</div>
                  </div>
                  <div>
                    <div style="font-size: 24px; font-weight: bold; color: #007bff;">' . count($latest_activities) . '</div>
                    <div style="color: #666; font-size: 12px;">' . BX_MPI_HISTORY_SIDEBAR_LAST_ACTIVITIES . '</div>
                  </div>
                </div>
              ');
              
              if ( (xtc_not_null($heading)) && (xtc_not_null($contents)) ) {
                $box = new box;
                echo $box->infoBox($heading, $contents);
              }
              
              // Box 2: Meist geänderte Felder
              $heading  = array();
              $contents = array();
              
              $heading[]  = array('text' => '<strong>🔥 ' . BX_MPI_HISTORY_SIDEBAR_TOP_FIELDS . '</strong>');
              
              $field_list = '<div style="padding: 10px;">';
              while ($field = xtc_db_fetch_array($top_fields_query)) {
                  $field_list .= '
                    <div style="display: flex; justify-content: space-between; padding: 5px 0; border-bottom: 1px solid #eee;">
                      <span style="font-weight: bold; font-size: 12px;">' . htmlspecialchars($field['field_name']) . '</span>
                      <span style="color: #AF417E; font-weight: bold;">' . (int)$field['count'] . '</span>
                    </div>
                  ';
              }
              $field_list .= '</div>';
              
              $contents[] = array('text' => $field_list);
              
              if ( (xtc_not_null($heading)) && (xtc_not_null($contents)) ) {
                $box = new box;
                echo $box->infoBox($heading, $contents);
              }
              
              // Box 3: Quick Actions
              $heading  = array();
              $contents = array();
              
              $heading[]  = array('text' => '<strong>⚡ ' . BX_MPI_HISTORY_SIDEBAR_QUICK_ACTIONS . '</strong>');
              $contents[] = array('text' => '
                <div style="padding: 10px;">
                  <a href="' . basename($_SERVER['PHP_SELF']) . '?filter_timeframe=today" class="button" style="margin-bottom: 8px; text-align: center; display: block; text-decoration: none;">📅 ' . BX_MPI_HISTORY_SIDEBAR_TODAY . '</a>
                  <a href="' . basename($_SERVER['PHP_SELF']) . '?filter_timeframe=week" class="button" style="margin-bottom: 8px; text-align: center; display: block; text-decoration: none;">📊 ' . BX_MPI_HISTORY_SIDEBAR_THIS_WEEK . '</a>
                  <a href="' . basename($_SERVER['PHP_SELF']) . '?filter_field=ean" class="button" style="margin-bottom: 8px; text-align: center; display: block; text-decoration: none;">🏷️ ' . BX_MPI_HISTORY_SIDEBAR_ONLY_EAN_CHANGES . '</a>
                  <a href="' . basename($_SERVER['PHP_SELF']) . '" class="button" style="text-align: center; display: block; text-decoration: none;">↺ ' . BX_MPI_HISTORY_SIDEBAR_SHOW_ALL . '</a>
                </div>
              ');
              
              if ( (xtc_not_null($heading)) && (xtc_not_null($contents)) ) {
                $box = new box;
                echo $box->infoBox($heading, $contents);
              }
              
            } else {
              $heading  = array();
              $contents = array();

              $heading[]  = array('text' => '<strong>' . BX_MPI_HISTORY_SIDEBAR_DISABLED_TITLE . '</strong>');
              $contents[] = array('text' => BX_MPI_HISTORY_SIDEBAR_DISABLED_TEXT);
              $contents[] = array('text' => BX_MPI_HISTORY_DISABLED_DESC);
              
              if ( (xtc_not_null($heading)) && (xtc_not_null($contents)) ) {
                $box = new box;
                echo $box->infoBox($heading, $contents);
              }
            }
            ?>
            </div> <!-- tab-history-right eof //-->

            <div id="tab-settings-right">
            <?php
              // Konfigurations-Issues sammeln
              $config_issues = array();
              
              // 1. GS1-Präfix Check
              if ($config['ean_mode'] == 'auto_gs1') {
                  $gs1_prefix = defined('MODULE_BX_MPI_GS1_PREFIX') ? MODULE_BX_MPI_GS1_PREFIX : '';
                  if (empty($gs1_prefix)) {
                      $config_issues[] = array(
                          'level' => 'error',
                          'icon' => '❌',
                          'title' => BX_MPI_SETUP_CHECK_GS1_MISSING,
                          'desc' => BX_MPI_SETUP_CHECK_GS1_MISSING_DESC
                      );
                  }
              }
              
              // 2. Historie Check
              if ($config['history_enabled'] == 'false') {
                  $config_issues[] = array(
                      'level' => 'warning',
                      'icon' => '⚠️',
                      'title' => BX_MPI_CONFIG_HISTORY_DISABLED_TITLE,
                      'desc' => BX_MPI_CONFIG_HISTORY_DISABLED_DESC
                  );
              }
              
              // 3. Auto-Create Check
              if ($config['auto_create'] == 'false') {
                  $config_issues[] = array(
                      'level' => 'info',
                      'icon' => 'ℹ️',
                      'title' => BX_MPI_CONFIG_AUTO_CREATE_DISABLED_TITLE,
                      'desc' => BX_MPI_CONFIG_AUTO_CREATE_DISABLED_DESC
                  );
              }
              
              // 4. SKU-Modus Check
              if (defined('MODULE_BX_MPI_SKU_MODE') && MODULE_BX_MPI_SKU_MODE == 'model') {
                  $config_issues[] = array(
                      'level' => 'warning',
                      'icon' => '⚠️',
                      'title' => BX_MPI_SETUP_CHECK_LEGACY_SKU,
                      'desc' => BX_MPI_SETUP_CHECK_LEGACY_SKU_DESC
                  );
              }
              
              // 5. Separator Check
              if (defined('MODULE_BX_MPI_SKU_SEPARATOR')) {
                  $separator = MODULE_BX_MPI_SKU_SEPARATOR;
                  if (strlen($separator) > 1) {
                      $config_issues[] = array(
                          'level' => 'info',
                          'icon' => 'ℹ️',
                          'title' => BX_MPI_SETUP_CHECK_LONG_SEPARATOR,
                          'desc' => 'Separator "' . htmlspecialchars($separator) . '" ' . BX_MPI_SETUP_CHECK_LONG_SEPARATOR_DESC
                      );
                  }
              }
              
              // Box 1: Setup-Check
              $heading  = array();
              $contents = array();
              
              $heading[]  = array('text' => '<strong>🎯 ' . BX_MPI_SETUP_CHECK_TITLE . '</strong>');
              
              $check_html = '<div style="padding: 10px;">';
              
              if (empty($config_issues)) {
                  $check_html .= '
                    <div style="text-align: center; padding: 20px 0; color: #28a745;">
                      <div style="font-size: 48px; margin-bottom: 10px;">✅</div>
                      <div style="font-weight: bold; margin-bottom: 5px;">' . BX_MPI_SETUP_CHECK_ALL_OPTIMAL . '</div>
                      <div style="font-size: 11px; color: #666;">' . BX_MPI_SETUP_CHECK_CONFIG_OK . '</div>
                    </div>
                  ';
              } else {
                  foreach ($config_issues as $issue) {
                      $level_colors = array(
                          'error' => '#dc3545',
                          'warning' => '#ffc107',
                          'info' => '#17a2b8'
                      );
                      $bg_colors = array(
                          'error' => '#f8d7da',
                          'warning' => '#fff3cd',
                          'info' => '#d1ecf1'
                      );
                      
                      $color = $level_colors[$issue['level']];
                      $bg = $bg_colors[$issue['level']];
                      
                      $check_html .= '
                        <div style="background: ' . $bg . '; border-left: 3px solid ' . $color . '; padding: 8px; margin-bottom: 10px; border-radius: 3px;">
                          <div style="font-size: 11px; font-weight: bold; color: ' . $color . '; margin-bottom: 3px;">
                            ' . $issue['icon'] . ' ' . htmlspecialchars($issue['title']) . '
                          </div>
                          <div style="font-size: 10px; color: #666; line-height: 1.4;">
                            ' . htmlspecialchars($issue['desc']) . '
                          </div>
                        </div>
                      ';
                  }
              }
              
              $check_html .= '</div>';
              
              $contents[] = array('text' => $check_html);
              
              if ( (xtc_not_null($heading)) && (xtc_not_null($contents)) ) {
                $box = new box;
                echo $box->infoBox($heading, $contents);
              }
              
              // Box 2: Konfigurations-Tipp (kontext-sensitiv)
              $heading  = array();
              $contents = array();
              
              $heading[]  = array('text' => '<strong>💡 ' . BX_MPI_CONFIG_TIP_TITLE . '</strong>');
              
              $tip_html = '<div style="padding: 10px; font-size: 11px; line-height: 1.6;">';
              
              // Primärer Tipp basierend auf kritischstem Issue
              if (!empty($config_issues)) {
                  $critical_issue = $config_issues[0]; // Erstes Issue (meist kritischstes)
                  
                  $tip_html .= '<div style="background: #f8f9fa; border-left: 3px solid #AF417E; padding: 10px; margin-bottom: 12px; border-radius: 3px;">';
                  $tip_html .= '<strong style="color: #AF417E;">' . htmlspecialchars($critical_issue['title']) . '</strong><br>';
                  $tip_html .= '<div style="margin-top: 5px; color: #666;">' . htmlspecialchars($critical_issue['desc']) . '</div>';
                  $tip_html .= '</div>';
                  
                  // Spezifische Handlungsempfehlung
                  if ($critical_issue['level'] == 'error' && strpos($critical_issue['title'], 'GS1') !== false) {
                      $tip_html .= '
                        <div style="font-size: 10px; color: #666; margin-top: 10px;">
                          <strong>👉 ' . BX_MPI_CONFIG_TIP_RECOMMENDED_ACTION . '</strong><br>
                          • ' . BX_MPI_CONFIG_TIP_GS1_ACTION1 . '<br>
                          • ' . BX_MPI_CONFIG_TIP_GS1_ACTION2 . '
                        </div>
                      ';
                  } elseif ($critical_issue['level'] == 'warning' && strpos($critical_issue['title'], 'Historie') !== false) {
                      $tip_html .= '
                        <div style="font-size: 10px; color: #666; margin-top: 10px;">
                          <strong>👉 ' . BX_MPI_CONFIG_TIP_RECOMMENDED_ACTION . '</strong><br>
                          • ' . BX_MPI_CONFIG_TIP_HISTORY_ACTION1 . '<br>
                          • ' . BX_MPI_CONFIG_TIP_HISTORY_ACTION2 . '
                        </div>
                      ';
                  }
              } else {
                  // Allgemeine Tipps wenn alles OK
                  switch ($config['ean_mode']) {
                      case 'manual':
                          $tip_html .= '
                            <strong style="color: #AF417E;">' . BX_MPI_CONFIG_TIP_MANUAL_MODE . '</strong><br>
                            <div style="margin-top: 5px; color: #666;">
                              ' . BX_MPI_CONFIG_TIP_MANUAL_DESC . '
                            </div>
                          ';
                          break;
                      case 'auto_pseudo':
                          $tip_html .= '
                            <strong style="color: #AF417E;">' . BX_MPI_CONFIG_TIP_PSEUDO_MODE . '</strong><br>
                            <div style="margin-top: 5px; color: #666;">
                              ✅ ' . BX_MPI_CONFIG_TIP_PSEUDO_DESC_GOOD . '<br>
                              ⚠️ <strong>' . BX_MPI_CONFIG_TIP_PSEUDO_DESC_WARNING . '</strong>
                            </div>
                          ';
                          break;
                      case 'auto_gs1':
                          $tip_html .= '
                            <strong style="color: #AF417E;">' . BX_MPI_CONFIG_TIP_GS1_MODE . '</strong><br>
                            <div style="margin-top: 5px; color: #666;">
                              ✅ ' . BX_MPI_CONFIG_TIP_GS1_DESC1 . '<br>
                              ✅ ' . BX_MPI_CONFIG_TIP_GS1_DESC2 . '<br>
                              💡 ' . BX_MPI_CONFIG_TIP_GS1_DESC3 . '
                            </div>
                          ';
                          break;
                  }
              }
              
              // Zusätzlicher Tipp bei großen Datenmengen
              if ($stats['total_identifiers'] > 1000) {
                  $tip_html .= '
                    <div style="background: #e7f3ff; border-left: 3px solid #17a2b8; padding: 8px; margin-top: 12px; border-radius: 3px; font-size: 10px;">
                      <strong style="color: #17a2b8;">💡 ' . BX_MPI_CONFIG_TIP_PERFORMANCE . '</strong><br>
                      <span style="color: #666;">
                        ' . BX_MPI_SETTINGS_AT . ' ' . $stats['total_identifiers'] . ' ' . BX_MPI_CONFIG_TIP_PERFORMANCE_DESC . '
                      </span>
                    </div>
                  ';
              }
              
              $tip_html .= '</div>';
              
              $contents[] = array('text' => $tip_html);
              
              if ( (xtc_not_null($heading)) && (xtc_not_null($contents)) ) {
                $box = new box;
                echo $box->infoBox($heading, $contents);
              }
              
              // Box 3: Hilfe & Tools
              $heading  = array();
              $contents = array();
              
              $heading[]  = array('text' => '<strong>📚 ' . BX_MPI_HELP_TOOLS_TITLE . '</strong>');
              
              $tools_html = '
                <div style="padding: 10px;">
                  <div style="font-size: 10px; color: #666; margin-bottom: 8px;"><strong>' . BX_MPI_HELP_CONFIG_TEMPLATES . '</strong></div>
                  <div style="font-size: 10px; line-height: 1.8; color: #666; margin-bottom: 12px;">
                    • <strong>' . BX_MPI_HELP_TEMPLATE_STANDARD . '</strong> ' . BX_MPI_HELP_TEMPLATE_STANDARD_DESC . '<br>
                    • <strong>' . BX_MPI_HELP_TEMPLATE_WAREHOUSE . '</strong> ' . BX_MPI_HELP_TEMPLATE_WAREHOUSE_DESC . '<br>
                    • <strong>' . BX_MPI_HELP_TEMPLATE_MARKETPLACE . '</strong> ' . BX_MPI_HELP_TEMPLATE_MARKETPLACE_DESC . '<br>
                    • <strong>' . BX_MPI_HELP_TEMPLATE_MINIMAL . '</strong> ' . BX_MPI_HELP_TEMPLATE_MINIMAL_DESC . '
                  </div>
                  
                  <div style="border-top: 1px solid #eee; margin-top: 12px; padding-top: 12px;">
                    <div style="font-size: 10px; color: #666; margin-bottom: 8px;"><strong>❓ ' . BX_MPI_HELP_FAQ . '</strong></div>
                    
                    <div style="font-size: 10px; line-height: 1.6; color: #666; margin-bottom: 10px;">
                      <strong style="color: #AF417E;">' . BX_MPI_HELP_FAQ_Q1 . '</strong><br>
                      <span style="color: #555;">
                        <strong>' . BX_MPI_HELP_FAQ_A1_PSEUDO . '</strong> ' . BX_MPI_HELP_FAQ_A1_PSEUDO_DESC . '<br>
                        <strong>' . BX_MPI_HELP_FAQ_A1_GS1 . '</strong> ' . BX_MPI_HELP_FAQ_A1_GS1_DESC . '
                      </span>
                    </div>
                    
                    <div style="font-size: 10px; line-height: 1.6; color: #666; margin-bottom: 10px;">
                      <strong style="color: #AF417E;">' . BX_MPI_HELP_FAQ_Q2 . '</strong><br>
                      <span style="color: #555;">
                        ' . BX_MPI_HELP_FAQ_A2 . '
                      </span>
                    </div>
                    
                    <div style="font-size: 10px; line-height: 1.6; color: #666; margin-bottom: 10px;">
                      <strong style="color: #AF417E;">' . BX_MPI_HELP_FAQ_Q3 . '</strong><br>
                      <span style="color: #555;">
                        <strong>' . BX_MPI_HELP_FAQ_A3_WARNING . '</strong> ' . BX_MPI_HELP_FAQ_A3 . '
                      </span>
                    </div>
                    
                    <div style="font-size: 10px; line-height: 1.6; color: #666;">
                      <strong style="color: #AF417E;">' . BX_MPI_HELP_FAQ_Q4 . '</strong><br>
                      <span style="color: #555;">
                        ' . BX_MPI_HELP_FAQ_A4 . '
                      </span>
                    </div>
                  </div>
                  
                  <div style="background: #f8f9fa; border-radius: 3px; padding: 8px; margin-top: 12px; font-size: 10px; color: #666; text-align: center;">
                    <strong>💾 ' . BX_MPI_HELP_BACKUP_TIP . '</strong> ' . BX_MPI_HELP_BACKUP_TIP_DESC . '
                  </div>
                </div>
              ';
              
              $contents[] = array('text' => $tools_html);
              
              if ( (xtc_not_null($heading)) && (xtc_not_null($contents)) ) {
                $box = new box;
                echo $box->infoBox($heading, $contents);
              }
            ?>
            </div> <!-- tab-settings-right eof //-->

          </td>
        </tr>
      </table>
    </td>
  </tr>
</table>
<!-- body_eof //-->
<!-- footer //-->
<?php require(DIR_WS_INCLUDES.'footer.php'); ?>
<!-- footer_eof //-->

</body>
</html>
<?php require(DIR_WS_INCLUDES.'application_bottom.php'); ?>