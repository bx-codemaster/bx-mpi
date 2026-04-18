<?php
/**
 * BX Modified Product Identifier (MPI)
 * English Language Constants for System Module
 * 
 * @package    BX Modified Product Identifier
 * @subpackage Language
 * @language   English
 * @author     Axel Benkert
 * @version    1.0.0
 * @date       2025-01-16
 * @copyright  2025 Axel Benkert
 * @license    GNU General Public License v2.0
 */

defined('_VALID_XTC') or die('Direct Access to this location is not allowed.');

// Module Title & Description
define('MODULE_BX_MPI_TEXT_TITLE', 'BX Product Identifier (MPI)');
define('MODULE_BX_MPI_TEXT_DESCRIPTION', '
  <h3 style="margin-top: 0;">🔑 BX Product Identifier (MPI)</h3>
  <div style="background: #f8f9fa; padding: 5px 20px; border-radius: 8px; margin: 10px 0;">
    <p>Centralised management of unique product identifiers</p>
    <p>This Module enables the unique identification of products with attributes for ERP integration.</p>
    
    <h4 style="color: #333; margin-top: 20px;">📦 Main Features:</h4>
    <ul>
      <li><strong>SKU Generation:</strong> Automatic creation of unique SKUs for product variants</li>
      <li><strong>EAN/GTIN Management:</strong> Management of EAN-13, UPC, ISBN per variant</li>
      <li><strong>ERP Integration:</strong> Mapping to external ERP product numbers</li>
      <li><strong>Storage Location Management:</strong> Optional: Shelf/bin assignment</li>
      <li><strong>API for Third-Party Modules:</strong> RMA, shipping, export modules can access it</li>
      <li><strong>Change History:</strong> Optional: Logging of all changes</li>
    </ul>
    
    <h4 style="color: #333; margin-top: 20px;">💡 Use Cases:</h4>
    <ul>
      <li><strong>Red T-Shirt M:</strong> TSHIRT-001-RED-M (from base + attributes)</li>
      <li><strong>RMA Module:</strong> Unique identification of returned variants</li>
      <li><strong>Shipping Label:</strong> Barcode printing with EAN</li>
      <li><strong>Warehouse Scanner:</strong> Quick product recognition via EAN</li>
      <li><strong>ERP Export:</strong> Transfer to JTL, SAP, Lexware</li>
    </ul>
    
    <h4 style="color: #333; margin-top: 20px;">🔧 Configuration:</h4>
    <ul>
      <li><strong>Auto-Create:</strong> Automatic SKU generation on order</li>
      <li><strong>SKU Separator:</strong> Separator for SKU components (default: "-")</li>
      <li><strong>History:</strong> Log changes (for audit)</li>
    </ul>
  </div>
');
// Configuration Options

// 1. Status
define('MODULE_BX_MPI_STATUS_TITLE', 'Enable Module');
define('MODULE_BX_MPI_STATUS_DESC', 'Do you want to enable the BX Product Identifier module?');

// 2. Config ID (hidden)
define('MODULE_BX_MPI_CONFIG_ID_TITLE', 'Configuration Group ID');
define('MODULE_BX_MPI_CONFIG_ID_DESC', 'Internal configuration group ID (do not change)');

// 3. Auto-Create
define('MODULE_BX_MPI_AUTO_CREATE_TITLE', 'Automatic SKU Generation');
define('MODULE_BX_MPI_AUTO_CREATE_DESC', '
  Should a unique SKU be automatically created when a product with attributes is ordered?<br>
  <small><strong>true:</strong> SKU is generated on first order (recommended)<br>
  <strong>false:</strong> SKUs must be maintained manually</small>
');

// 4. SKU Separator
define('MODULE_BX_MPI_SKU_SEPARATOR_TITLE', 'SKU Separator');
define('MODULE_BX_MPI_SKU_SEPARATOR_DESC', '
  Separator character for SKU components.<br>
  <small>Example with "-": <code>TSHIRT-001-RED-M</code><br>
  Example with "_": <code>TSHIRT_001_RED_M</code></small>
');

// 5. Enable History
define('MODULE_BX_MPI_ENABLE_HISTORY_TITLE', 'Enable Change History');
define('MODULE_BX_MPI_ENABLE_HISTORY_DESC', '
  Should all changes to product identifiers be logged?<br>
  <small><strong>true:</strong> All changes are saved in history table (recommended for audit)<br>
  <strong>false:</strong> No logging (saves storage space)</small>
');

// 6. EAN Mode
define('MODULE_BX_MPI_EAN_MODE_TITLE', 'EAN Generation');
define('MODULE_BX_MPI_EAN_MODE_DESC', '
  <strong>How should EAN codes be generated?</strong><br><br>
  
  <strong style="color: #333;">manual:</strong> No automatic generation (admin manages manually)<br>
  <small style="color: #666;">→ Best control, suitable for small shops or when supplier EANs are available</small><br><br>
  
  <strong style="color: #333;">auto_pseudo:</strong> Pseudo-EAN with prefix "2" (instore code)<br>
  <small style="color: #666;">→ Automatically generated, scanner-compatible, NOT for external trade (Amazon/eBay)<br>
  → Ideal for internal processes: warehouse, RMA, picking</small><br><br>
  
  <strong style="color: #333;">auto_gs1:</strong> Real EAN with GS1 prefix<br>
  <small style="color: #666;">→ Tradeable on Amazon/eBay/Kaufland, requires GS1 membership (approx. $100-300/year)<br>
  → GS1 prefix must be entered below</small><br><br>
  
  <div style="background: #fff3cd; padding: 10px; border-radius: 4px; margin-top: 10px;">
    <strong>💡 Note for Multi-Attribute Products:</strong><br>
    <small>For products with multiple attributes (e.g. T-Shirt Size M + Color Red) there is no unique EAN in modified.
    BX MPI automatically generates a unique EAN per variant - depending on the selected mode.</small>
  </div>
');

// 7. GS1 Prefix
define('MODULE_BX_MPI_GS1_PREFIX_TITLE', 'GS1 Prefix');
define('MODULE_BX_MPI_GS1_PREFIX_DESC', '
  <strong>GS1 Prefix for EAN generation</strong> (only required for <strong>auto_gs1</strong> mode)<br><br>
  
  Enter your registered GS1 prefix (7-10 digits).<br>
  <small><strong>Example:</strong> <code>4004332</code></small><br><br>
  
  <div style="background: #f8d7da; padding: 10px; border-radius: 4px; border-left: 4px solid #dc3545;">
    <strong>⚠️ Important:</strong> GS1 membership required!<br>
    <small>Without valid prefix, no tradeable EANs can be generated.<br>
    Cost: approx. $100-300/year depending on country and quota.</small>
  </div>
  
  <div style="margin-top: 10px;">
    <small>ℹ️ More information: 
    <a href="https://www.gs1.org" target="_blank" style="color: #0066cc;">www.gs1.org</a></small>
  </div>
');

// 8. Sort Order
define('MODULE_BX_MPI_SORT_ORDER_TITLE', 'Sort Order');
define('MODULE_BX_MPI_SORT_ORDER_DESC', 'Sort order in module overview');

// Admin Interface Texts (for future use)
define('TEXT_BX_MPI_HEADING', 'Product Identifier (SKU/EAN)');
define('TEXT_BX_MPI_OVERVIEW', 'Overview');
define('TEXT_BX_MPI_EDIT', 'Edit');
define('TEXT_BX_MPI_DELETE', 'Delete');
define('TEXT_BX_MPI_ADD', 'Add');

define('TEXT_BX_MPI_SKU_COMPLETE', 'Complete SKU');
define('TEXT_BX_MPI_EAN', 'EAN-13 / GTIN');
define('TEXT_BX_MPI_UPC', 'UPC');
define('TEXT_BX_MPI_ISBN', 'ISBN');
define('TEXT_BX_MPI_WWS_NR', 'ERP Article Number');
define('TEXT_BX_MPI_WWS_SYSTEM', 'ERP System');
define('TEXT_BX_MPI_WAREHOUSE_LOCATION', 'Storage Location');

define('TEXT_BX_MPI_PRODUCT', 'Product');
define('TEXT_BX_MPI_ATTRIBUTES', 'Attributes');
define('TEXT_BX_MPI_NO_ATTRIBUTES', 'Base Product (no attributes)');

define('TEXT_BX_MPI_HISTORY', 'Change History');
define('TEXT_BX_MPI_CHANGED_BY', 'Changed By');
define('TEXT_BX_MPI_CHANGED_AT', 'Changed At');
define('TEXT_BX_MPI_FIELD_NAME', 'Field');
define('TEXT_BX_MPI_OLD_VALUE', 'Old Value');
define('TEXT_BX_MPI_NEW_VALUE', 'New Value');

define('TEXT_BX_MPI_SAVE_SUCCESS', 'Product identifier successfully saved');
define('TEXT_BX_MPI_DELETE_SUCCESS', 'Product identifier successfully deleted');
define('TEXT_BX_MPI_ERROR', 'Error saving data');

define('TEXT_BX_MPI_CONFIRM_DELETE', 'Really delete? This action cannot be undone.');

// API Messages
define('TEXT_BX_MPI_API_NOT_AVAILABLE', 'BX Product Identifier module is not installed or disabled');
define('TEXT_BX_MPI_API_SKU_CREATED', 'SKU automatically created');
define('TEXT_BX_MPI_API_SKU_EXISTS', 'SKU already exists');
