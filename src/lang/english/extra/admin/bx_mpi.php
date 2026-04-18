<?php
/* -----------------------------------------------------------------------------------------
   $Id: /lang/english/extra/admin/bx_mpi.php 1000 2026-01-02 13:00:00Z benax $
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

 // Global Constants
 define('BX_MPI_EXAMPLE_PREFIX', 'e.g. ');
 define('SKU_EAN_VARIANT_GENERATOR', 'SKU/EAN Variant Generator');
 define('SKU_EAN_VARIANT_COMBINATIONS', 'Combinations');
 define('SKU_EAN_VARIANT_GENERATOR_TOOLTIP', 'Automatically generates SKUs for all attribute combinations and enables EAN assignment');
 define('SKU_EAN_VARIANT_EAN_POOL_AVAILABLE', 'EANs available in pool');
 define('SKU_EAN_VARIANT_EAN_POOL_NOT_AVAILABLE', 'Pool empty - use Pseudo-EAN or manual entry');
 define('SKU_EAN_VARIANT_MANAGE_POOL', 'Manage pool');
 define('SKU_EAN_VARIANT_NOTE', 'Note');
 define('SKU_EAN_VARIANT_SAVE_PRODUCT_FIRST', 'Please save the product before you can assign SKU/EAN.');
 define('SKU_EAN_VARIANT_SIMPLE_PRODUCT', 'Simple product (no variants)');
 define('SKU_EAN_VARIANT_NO_ATTRIBUTES', 'This product has no attributes - a base SKU will be used');
 define('SKU_EAN_VARIANT_BASIS_SKU', 'Base SKU');
 define('SKU_EAN_VARIANT_SAVED', 'Saved');
 define('SKU_EAN_VARIANT_AUTO_GENERATED', 'Will be generated automatically on save');
 define('SKU_EAN_VARIANT_PSEUDO_CURRENT_EAN', 'Current EAN');
 define('SKU_EAN_VARIANT_PSEUDO_EAN', 'Pseudo-EAN (Instore)');
 define('SKU_EAN_VARIANT_FROM_POOL', 'From Pool');
 define('SKU_EAN_VARIANT_MANUAL', 'Manual');
 define('SKU_EAN_VARIANT_NONE', 'None');
 define('SKU_EAN_VARIANT_NO_EAN_ASSIGNED', 'No EAN assigned');
 define('SKU_EAN_VARIANT_VARIANTS_TEXT', 'Variants');
 define('SKU_EAN_VARIANT_VARIANTS_GENERATED_FROM', 'generated from product attributes');
 define('SKU_EAN_VARIANT_SELECT_ALL', 'Select all');
 define('SKU_EAN_VARIANT_EAN_SOURCE_FOR_ALL', 'EAN source for all');
 define('SKU_EAN_VARIANT_EAN_SOURCE_LABEL', 'Select EAN source');
 define('SKU_EAN_VARIANT_SELECT_FROM_POOL', 'Assign from pool');
 define('SKU_EAN_VARIANT_AVAILABLE', 'available');
 define('SKU_EAN_VARIANT_EMPTY', 'empty');
 define('SKU_EAN_VARIANT_GENERATE_PSEUDO_EAN', 'Generate Pseudo-EAN (Instore)');
 define('SKU_EAN_VARIANT_MANUAL_ENTRY', 'Manual entry');
 define('SKU_EAN_VARIANT_NO_EAN', 'No EAN');
 define('SKU_EAN_VARIANT_REMOVE_EAN', 'Remove EAN');
 define('SKU_EAN_VARIANT_NO_EAN_PRESENT', 'no EAN present');
 define('SKU_EAN_VARIANT_EAN_INPUT_LABEL', 'Enter EAN');
 define('SKU_EAN_VARIANT_EAN_PLACEHOLDER', 'Enter EAN (13 or 14 digits)');
 define('SKU_EAN_VARIANT_EAN_FORMAT_INFO', 'Format: 13-digit GTIN-13 or 14-digit GTIN-14');
 define('SKU_EAN_VARIANT_ASSIGN_EAN_BUTTON', 'Assign EAN');
 define('SKU_EAN_VARIANT_TABLE_VARIANTS', 'Variants');
 define('SKU_EAN_VARIANT_TABLE_VARIANT', 'Variant');
 define('SKU_EAN_VARIANT_TABLE_GENERATED_SKU', 'Generated SKU');
 define('SKU_EAN_VARIANT_TABLE_EAN', 'EAN');
 define('SKU_EAN_VARIANT_TABLE_EAN_SOURCE', 'EAN source');
 define('SKU_EAN_VARIANT_BULK_ACTIONS', 'Bulk actions');
 define('SKU_EAN_VARIANT_SELECT_VARIANTS_DESC', 'Select variants and save all at once');
 define('SKU_EAN_VARIANT_SAVE_SELECTED_BUTTON', 'Save selected & assign EANs');
 define('SKU_EAN_VARIANT_BULK_TIP', 'Tip: With these buttons you can set the EAN source for all variants at once');
 define('SKU_EAN_VARIANT_WILL_BE_GENERATED', 'will be generated on save');
 define('SKU_EAN_VARIANT_SAVED_STATUS', 'Saved');
 define('SKU_EAN_VARIANT_NEW_STATUS', 'New');
 define('SKU_EAN_VARIANT_SELECTION_CHECKBOX', 'Checkbox');
 define('SKU_EAN_VARIANT_JS_POOL_EMPTY', 'Pool is empty - please select a different source');
 define('SKU_EAN_VARIANT_JS_ALL_VARIANTS_SET', 'All variants set to "%s"');
 define('SKU_EAN_VARIANT_JS_PLEASE_ENTER_EAN', 'Please enter an EAN');
 define('SKU_EAN_VARIANT_JS_INVALID_EAN', 'Invalid EAN - use 13 or 14 digits');
 define('SKU_EAN_VARIANT_JS_CONFIRM_REMOVE_EAN', 'Really remove EAN?');
 define('SKU_EAN_VARIANT_JS_CONFIRM_ASSIGN_POOL', 'Assign EAN from pool?');
 define('SKU_EAN_VARIANT_JS_CONFIRM_GENERATE_PSEUDO', 'Generate Pseudo-EAN?');
 define('SKU_EAN_VARIANT_JS_CONFIRM_MANUAL_EAN', 'Assign manual EAN "%s"?');
 define('SKU_EAN_VARIANT_JS_PROCESSING', 'Processing...');
 define('SKU_EAN_VARIANT_JS_SELECT_VARIANT', 'Please select at least one variant');
 define('SKU_EAN_VARIANT_JS_CONFIRM_SAVE', 'Really save %d variants?
 SKUs will be generated and EANs assigned.');
 define('SKU_EAN_VARIANT_JS_SAVING', 'Saving... <span id=\'save-progress\'>0/%d</span>');
 define('SKU_EAN_VARIANT_JS_SAVING_SUCCESS', ' variants saved successfully');
 define('SKU_EAN_VARIANT_JS_ERROR_PREFIX', 'Error: ');
 define('SKU_EAN_VARIANT_JS_CONNECTION_ERROR', 'Connection error: ');

 // Settings Tab
 define('BX_MPI_SETTINGS_MODULE_SETTINGS', 'Module Settings');
 define('BX_MPI_SETTINGS_CONFIG_ID', 'Configuration ID');
 define('BX_MPI_SETTINGS_ADVANCED_CONFIG', 'Advanced Configuration');
 define('BX_MPI_SETTINGS_INTRO', 'Here you can adjust the most important settings of the BX MPI module.');
 define('BX_MPI_SETTINGS_INTRO_ADVANCED', 'For advanced options use the configuration page.');
define('BX_MPI_SETTINGS_AT', 'At');

// SKU Format Info
define('BX_MPI_SETTINGS_SKU_FORMAT_TITLE', 'SKU Format');
define('BX_MPI_SETTINGS_SKU_FORMAT_FIXED', 'Fixed Format');
define('BX_MPI_SETTINGS_SKU_FORMAT_EXAMPLE', 'Example');
define('BX_MPI_SETTINGS_SKU_FORMAT_SEPARATOR_INFO', 'Separators are fixed: "_" (after PID), "-" (between Option/Value), "x" (between attributes)');
define('BX_MPI_SETTINGS_SKU_FORMAT_PADDING_INFO', 'All IDs are 4-digit with leading zeros (Zero-Padded)');
define('BX_MPI_SETTINGS_SKU_FORMAT_OPTIMIZED_INFO', 'Format is optimized for warehouse management, scanners and BX Stockmanager integration');
 
 // Auto Create SKU
 define('BX_MPI_SETTINGS_AUTO_CREATE_TITLE', 'Automatic SKU Generation');
 define('BX_MPI_SETTINGS_AUTO_CREATE_DESC', 'Should a unique SKU be created automatically when a product with attributes is ordered?');
 define('BX_MPI_SETTINGS_RECOMMENDED', 'Recommended');
 define('BX_MPI_SETTINGS_ACTIVATED', 'Enabled');
 define('BX_MPI_SETTINGS_DEACTIVATED', 'Disabled');
 
 // SKU Prefix
 define('BX_MPI_SETTINGS_SKU_PREFIX_TITLE', 'SKU Prefix');
 define('BX_MPI_SETTINGS_SKU_PREFIX_DESC', 'Optional prefix for all generated SKUs. Can be left empty.');
 define('BX_MPI_SETTINGS_SKU_PREFIX_PLACEHOLDER', 'SKU or ART');
 define('BX_MPI_SETTINGS_SKU_PREFIX_EXAMPLE_WITH', 'Example with "SKU"');
 define('BX_MPI_SETTINGS_SKU_PREFIX_EXAMPLE_WITHOUT', 'Without prefix');
 define('BX_MPI_SETTINGS_SKU_PREFIX_NOTE', 'Note: Separators are fixed and cannot be changed (see info box above).');
 
 // History
 define('BX_MPI_SETTINGS_HISTORY_TITLE', 'Change History');
 define('BX_MPI_SETTINGS_HISTORY_DESC', 'Should all changes to product identifiers be logged?');
 define('BX_MPI_SETTINGS_HISTORY_RECOMMENDED', 'Enabled for audit trail');
 
 // EAN Mode
 define('BX_MPI_SETTINGS_EAN_MODE_TITLE', 'EAN Generation');
 define('BX_MPI_SETTINGS_EAN_MODE_MANUAL', 'Manual');
 define('BX_MPI_SETTINGS_EAN_MODE_AUTO_PSEUDO', 'Auto Pseudo-EAN (Prefix 2)');
 define('BX_MPI_SETTINGS_EAN_MODE_AUTO_GS1', 'Auto GS1-EAN (tradeable)');
 define('BX_MPI_SETTINGS_EAN_MODE_DETAIL_TITLE', 'Modes in detail');
 define('BX_MPI_SETTINGS_EAN_MODE_MANUAL_DESC', 'No automatic generation (full control)');
 define('BX_MPI_SETTINGS_EAN_MODE_PSEUDO_DESC', 'Prefix "2" + ID + Checksum (scanner-compatible, NOT tradeable)');
 define('BX_MPI_SETTINGS_EAN_MODE_GS1_DESC', 'Real EAN code (tradeable, requires GS1 membership)');
 
 // GS1 Prefix
 define('BX_MPI_SETTINGS_GS1_PREFIX_TITLE', 'GS1 Prefix');
 define('BX_MPI_SETTINGS_GS1_PREFIX_ONLY_FOR', 'only for GS1-EAN');
 define('BX_MPI_SETTINGS_GS1_PREFIX_DESC', 'Your registered GS1 prefix (7-10 digits). Only required for "Auto GS1-EAN" mode.');
 define('BX_MPI_SETTINGS_GS1_PREFIX_IMPORTANT', 'Important');
 define('BX_MPI_SETTINGS_GS1_PREFIX_MEMBERSHIP', 'Requires GS1 membership (approx. 100-300€/year)');
 define('BX_MPI_SETTINGS_GS1_PREFIX_INFO', 'More information about GS1 membership');
 
 // Save Button
 define('BX_MPI_SETTINGS_SAVE_BUTTON', 'Save settings');
 
 // General Messages
 define('BX_MPI_MSG_IDENTIFIER_UPDATED', 'Identifier successfully updated!');
 define('BX_MPI_MSG_IDENTIFIER_DELETED', 'Identifier successfully deleted!');
 define('BX_MPI_MSG_BLOCK_IMPORTED', 'Block "%s" with %d EANs successfully imported');
 define('BX_MPI_MSG_BLOCK_DELETED', 'Block successfully deleted');
 define('BX_MPI_MSG_EAN_ASSIGNED', 'EAN successfully assigned');
 define('BX_MPI_MSG_EAN_RELEASED', 'EAN returned to pool');
 define('BX_MPI_MSG_EAN_REMOVED', 'EAN successfully removed');
 define('BX_MPI_MSG_EANS_ASSIGNED', '%d EANs successfully assigned');
 define('BX_MPI_MSG_VARIANTS_SAVED', '%d variants successfully saved');
 
 // Error Messages
 define('BX_MPI_ERR_BLOCK_NUMBER_MISSING', 'Block number missing');
 define('BX_MPI_ERR_INVALID_BLOCK_SIZE', 'Invalid block size');
 define('BX_MPI_ERR_FILE_UPLOAD', 'Error during file upload');
 define('BX_MPI_ERR_COLUMN_NOT_FOUND', 'Column "Gtin" not found');
 define('BX_MPI_ERR_NO_VALID_EANS', 'No valid EANs found');
 define('BX_MPI_ERR_IMPORT_FAILED', 'Import failed: %s');
 define('BX_MPI_ERR_BLOCK_HAS_ASSIGNED_EANS', 'Block cannot be deleted: %d EANs are already assigned');
 define('BX_MPI_ERR_IDENTIFIER_NOT_FOUND', 'Identifier not found');
 define('BX_MPI_ERR_IDENTIFIER_HAS_EAN', 'Identifier already has an EAN: %s');
 define('BX_MPI_ERR_IDENTIFIER_NO_EAN', 'Identifier has no EAN');
 define('BX_MPI_ERR_NO_POOL_EANS', 'No EANs available in pool');
 define('BX_MPI_ERR_NO_IDENTIFIERS_WITHOUT_EAN', 'No identifiers without EAN found');
 define('BX_MPI_ERR_SKU_CREATION_FAILED', 'Error creating SKU');
 define('BX_MPI_ERR_IDENTIFIER_CREATION_FAILED', 'Identifier could not be created');
 define('BX_MPI_ERR_PSEUDO_EAN_FAILED', 'Error generating Pseudo-EAN');
 define('BX_MPI_ERR_INVALID_EAN_FORMAT', 'Invalid EAN - use 13 or 14 digits');
 define('BX_MPI_ERR_EAN_ALREADY_ASSIGNED', 'EAN already assigned to another identifier');
 define('BX_MPI_ERR_INVALID_VARIANT_DATA', 'Invalid variant data');
 define('BX_MPI_ERR_SKU_CREATION_VARIANT_FAILED', 'Error creating SKU for variant');
 define('BX_MPI_ERR_IDENTIFIER_ID_NOT_FOUND', 'Identifier ID could not be determined');
 define('BX_MPI_ERR_INVALID_EAN_FORMAT_VALUE', 'Invalid EAN format: %s');
 define('BX_MPI_ERR_EAN_ALREADY_ASSIGNED_VALUE', 'EAN already assigned: %s');
 define('BX_MPI_ERR_UNKNOWN_AJAX_ACTION', 'Unknown AJAX action'); 
 // Admin UI Texts
 define('BX_MPI_PAGE_TITLE', 'BX Modified Product Identifier');
 define('BX_MPI_PAGE_DESCRIPTION', 'Central management of unique product identifiers');
 define('BX_MPI_TAB_DASHBOARD', 'Dashboard');
 define('BX_MPI_TAB_ADMIN', 'SKU/EAN Management');
 define('BX_MPI_TAB_POOL', 'EAN Pool');
 define('BX_MPI_TAB_HISTORY', 'History');
 define('BX_MPI_TAB_SETTINGS', 'Settings');
 define('BX_MPI_EDIT_IDENTIFIER', 'Edit Identifier');
 define('BX_MPI_CLOSE_BUTTON', 'Close');
 define('BX_MPI_PRODUCT_ID_LABEL', 'Product ID');
 define('BX_MPI_PRODUCT_NAME_LABEL', 'Product Name');
 define('BX_MPI_SKU_READONLY_LABEL', 'SKU (Read-only)');
 define('BX_MPI_ATTRIBUTES_LABEL', 'Attributes');
 define('BX_MPI_DELETE_CONFIRM', 'Really delete identifier? This cannot be undone!');
 define('BX_MPI_DELETE_BLOCK_CONFIRM', 'Really delete block?');
 define('BX_MPI_DISPLAY_COUNT', 'Show <strong>%d</strong> to <strong>%d</strong> (of <strong>%d</strong> entries)');
 define('BX_MPI_RESET_BUTTON', 'Reset');
 define('BX_MPI_BACK_OVERVIEW', 'Back to Overview');
 define('BX_MPI_DEBUG_NO_BLOCKS', 'No blocks found in database!');
 define('BX_MPI_LOW_EANS_WARNING', 'Only <strong>%d</strong> EANs available');
 define('BX_MPI_BLOCK_DEPLETED', 'Depleted');
 define('BX_MPI_NEXT_PAGE', 'Next »');
 define('BX_MPI_RESET_FILTER', 'Reset Filter');
 define('BX_MPI_CONFIG_STATUS', 'Current Configuration');
 define('BX_MPI_CURRENT_VIEW', 'Current View');
 define('BX_MPI_QUICK_FILTER', 'Quick Filter');
 define('BX_MPI_QUICK_ACTIONS', 'Quick Actions');
 define('BX_MPI_POOL_STATUS', 'Pool Status');
 define('BX_MPI_CSV_FILE_ERROR', 'CSV file could not be opened');
 define('BX_MPI_SETTINGS_SAVED', '✓ Settings saved successfully!');
 define('BX_MPI_ACTIVE_FILTER_TITLE', 'Active Filter');
 define('BX_MPI_FILTER_ACTIVE_INFO', 'You are viewing filtered results only. Use "Reset" to see the complete list.');
 define('BX_MPI_NO_EAN_TITLE', 'Products without EAN');
 define('BX_MPI_NO_EAN_DESC', 'These products do not have an EAN yet. With Auto-Generation enabled, the EAN will be created on next access.');
 define('BX_MPI_PSEUDO_EAN_TITLE', 'Pseudo-EAN (Prefix 2)');
 define('BX_MPI_PSEUDO_EAN_DESC', 'For internal purposes and warehouse. Not suitable for marketplaces like Amazon/eBay!');
 define('BX_MPI_GS1_EAN_TITLE', 'GS1-EAN Codes');
 define('BX_MPI_GS1_EAN_DESC', 'These EANs are tradeable and can be used on marketplaces.');
 define('BX_MPI_NO_WAREHOUSE_TITLE', 'No Warehouse Location');
 define('BX_MPI_NO_WAREHOUSE_DESC', 'Enter warehouse locations to speed up picking.');
 define('BX_MPI_SKU_FORMAT_TITLE', 'SKU Format');
 define('BX_MPI_SKU_FORMAT_DESC', 'Numeric: <code style="background: #f0f0f0; padding: 2px 4px; border-radius: 2px;">SKU-[PID]-[Attr1]-[Attr2]</code><br><small style="color: #666;">Fixed length, scanner compatible</small>');
 define('BX_MPI_POOL_STATUS_TITLE', '📊 Pool Status');
 define('BX_MPI_AVAILABLE_EANS', 'available EANs');
 define('BX_MPI_AVAILABLE_LABEL', 'Available');
 define('BX_MPI_TOTAL_LABEL', 'Total:');
 define('BX_MPI_USED_LABEL', 'Used:');
 define('BX_MPI_LOW_STOCK_WARNING', '⚠️ Warning');
 define('BX_MPI_LOW_STOCK_TEXT', 'Less than 100 EANs available!');
 define('BX_MPI_NO_BLOCKS_IMPORTED', 'No blocks imported yet');
 define('BX_MPI_GS1_INFO_TITLE', '💡 GS1 Info');
 define('BX_MPI_BUY_EAN_BLOCKS', 'Buy EAN blocks:');
 define('BX_MPI_GS1_PRICES', 'Prices (approx.):');
 define('BX_MPI_GS1_PRICE_10', '• 10 EANs: ~€40');
 define('BX_MPI_GS1_PRICE_100', '• 100 EANs: ~€150');
 define('BX_MPI_GS1_PRICE_1000', '• 1,000 EANs: ~€300');
 define('BX_MPI_GS1_PRICE_NOTE', 'Prices as of 2024, plus annual fee');
 define('BX_MPI_CONFIG_HISTORY_DISABLED_TITLE', 'History disabled');
 define('BX_MPI_CONFIG_HISTORY_DISABLED_DESC', 'Changes are not being recorded');
 define('BX_MPI_CONFIG_AUTO_CREATE_DISABLED_TITLE', 'Auto-Create disabled');
 define('BX_MPI_CONFIG_AUTO_CREATE_DISABLED_DESC', 'SKUs must be created manually');
 define('BX_MPI_DASHBOARD_TOTAL_IDENTIFIERS', 'Total Identifiers');
 define('BX_MPI_DASHBOARD_WITH_EAN', 'With EAN');
 define('BX_MPI_DASHBOARD_WITHOUT_EAN', 'Without EAN');
 define('BX_MPI_DASHBOARD_PSEUDO_EAN', 'Pseudo-EAN (Prefix 2)');
 define('BX_MPI_DASHBOARD_GS1_EAN', 'GS1-EAN');
 define('BX_MPI_LATEST_ACTIVITIES_TITLE', 'Latest Activities');
 define('BX_MPI_ACTIVITIES_TABLE_TIME', 'Time');
 define('BX_MPI_ACTIVITIES_TABLE_SKU', 'SKU');
 define('BX_MPI_ACTIVITIES_TABLE_PRODUCT_ID', 'Product ID');
 define('BX_MPI_ACTIVITIES_TABLE_FIELD', 'Field');
 define('BX_MPI_ACTIVITIES_TABLE_OLD_VALUE', 'Old Value');
 define('BX_MPI_ACTIVITIES_TABLE_NEW_VALUE', 'New Value');
 define('BX_MPI_ACTIVITIES_TABLE_REASON', 'Reason');
 define('BX_MPI_HISTORY_DISABLED_OR_EMPTY', 'History is disabled or no activities available.');
 define('BX_MPI_ADMIN_SEARCH_FILTER', 'Search Filter');
 define('BX_MPI_ADMIN_PRODUCT_ID_SEARCH', 'Product ID:');
 define('BX_MPI_ADMIN_SKU_SEARCH', 'SKU:');
 define('BX_MPI_ADMIN_EAN_SEARCH', 'EAN:');
 define('BX_MPI_ADMIN_SEARCH_BUTTON', 'Search');
 define('BX_MPI_ADMIN_RESET_FILTER', 'Reset');
 define('BX_MPI_ADMIN_RESULTS_TITLE', 'SKU/EAN Management');
 define('BX_MPI_ADMIN_ENTRIES_FOUND', '%d entries found');
 define('BX_MPI_ADMIN_NO_ENTRIES', 'No entries found.');
 define('BX_MPI_ADMIN_TABLE_ID', 'ID');
 define('BX_MPI_ADMIN_TABLE_PRODUCT_ID', 'Product ID');
 define('BX_MPI_ADMIN_TABLE_PRODUCT_NAME', 'Product Name');
 define('BX_MPI_ADMIN_TABLE_SKU', 'SKU');
 define('BX_MPI_ADMIN_TABLE_EAN', 'EAN');
 define('BX_MPI_ADMIN_TABLE_WWS_NR', 'WWS No.');
 define('BX_MPI_ADMIN_TABLE_WAREHOUSE', 'Warehouse');
 define('BX_MPI_ADMIN_TABLE_ACTIONS', 'Actions');
 define('BX_MPI_ADMIN_EDIT_BUTTON', 'Edit');
 define('BX_MPI_ADMIN_DELETE_BUTTON', '🗑 Delete');
 define('BX_MPI_ADMIN_PRODUCT_INFO_HEADER', 'Product Info (Read-only)');
 define('BX_MPI_ADMIN_PRODUCT_ID_LABEL', 'Product ID:');
 define('BX_MPI_ADMIN_PRODUCT_NAME_LABEL', 'Product Name:');
 define('BX_MPI_ADMIN_SKU_READONLY_LABEL', 'SKU (Read-only):');
 define('BX_MPI_ADMIN_ATTRIBUTES_LABEL', 'Attributes:');
 define('BX_MPI_ADMIN_EAN_LABEL', 'EAN / GTIN:');
 define('BX_MPI_ADMIN_EAN_HELP', '13-digit EAN or 14-digit GTIN');
 define('BX_MPI_ADMIN_WWS_ARTICLE_NR_LABEL', 'WWS Article Number:');
 define('BX_MPI_ADMIN_WWS_ARTICLE_NR_HELP', 'Article number from ERP system');
 define('BX_MPI_ADMIN_WWS_SYSTEM_LABEL', 'WWS System:');
 define('BX_MPI_ADMIN_WWS_SYSTEM_HELP', 'Name of the ERP system');
 define('BX_MPI_ADMIN_WAREHOUSE_LABEL', 'Warehouse:');
 define('BX_MPI_ADMIN_WAREHOUSE_HELP', 'Physical warehouse location');
 define('BX_MPI_ADMIN_SAVE_BUTTON', '✓ Save');
 define('BX_MPI_POOL_DETAILS_HEADER', '📦 Block Details:');
 define('BX_MPI_POOL_BACK_OVERVIEW', '← Back to Overview');
 define('BX_MPI_POOL_BLOCK_SIZE', 'Block Size');
 define('BX_MPI_POOL_PURCHASED_AT', 'Purchased on');
 define('BX_MPI_POOL_IMPORTED_AT', 'Imported on');
 define('BX_MPI_POOL_AVAILABLE', 'Available');
 define('BX_MPI_POOL_USED', 'Used');
 define('BX_MPI_POOL_USAGE', 'Usage');
 define('BX_MPI_POOL_NOTES_TITLE', '📝 Notes:');
 define('BX_MPI_POOL_FILTER_STATUS', 'Filter by Status:');
 define('BX_MPI_POOL_ALL_STATUS', 'All Statuses');
 define('BX_MPI_POOL_STATUS_ASSIGNED', 'Assigned');
 define('BX_MPI_POOL_STATUS_RESERVED', 'Reserved');
 define('BX_MPI_POOL_EAN_SEARCH', 'Search EAN:');
 define('BX_MPI_POOL_EAN_SEARCH_PLACEHOLDER', 'EAN-13 or GTIN-14');
 define('BX_MPI_POOL_FILTER_BUTTON', 'Filter');
 define('BX_MPI_POOL_EAN_LIST_TITLE', 'EAN List');
 define('BX_MPI_POOL_EAN_FOUND', 'EAN%s found');
 define('BX_MPI_POOL_PAGE_OF', 'Page %d of %d');
 define('BX_MPI_POOL_TABLE_ID', 'ID');
 define('BX_MPI_POOL_TABLE_EAN', 'EAN / GTIN');
 define('BX_MPI_POOL_TABLE_STATUS', 'Status');
 define('BX_MPI_POOL_TABLE_ASSIGNED_TO', 'Assigned to');
 define('BX_MPI_POOL_TABLE_ASSIGNED_AT', 'Assigned on');
 define('BX_MPI_POOL_STATUS_LABEL_ASSIGNED', '⊗ Assigned');
 define('BX_MPI_POOL_STATUS_LABEL_RESERVED', '⊙ Reserved');
 define('BX_MPI_POOL_NO_EANS_FOUND', 'No EANs found.');
 define('BX_MPI_POOL_PREV_PAGE', '« Previous');
 define('BX_MPI_POOL_OVERVIEW_TITLE', 'Overview');
 define('BX_MPI_POOL_TOTAL_EANS', 'Total EANs');
 define('BX_MPI_POOL_TOTAL_BLOCKS', 'Blocks');
 define('BX_MPI_POOL_LOW_STOCK_WARNING', '⚠️ Warning: Low EAN Stock');
 define('BX_MPI_POOL_IMPORT_BUTTON', '➕ Import New Block');
 define('BX_MPI_POOL_BLOCKS_TITLE', '📦 Imported EAN Blocks');
 define('BX_MPI_POOL_BLOCK_NR', 'Block No.');
 define('BX_MPI_POOL_BLOCK_SIZE_LABEL', 'Size');
 define('BX_MPI_POOL_BLOCK_PURCHASED', 'Purchased');
 define('BX_MPI_POOL_BLOCK_IMPORTED', 'Imported');
 define('BX_MPI_POOL_BLOCK_TOTAL', 'Total');
 define('BX_MPI_POOL_BLOCK_FREE', 'Free');
 define('BX_MPI_POOL_BLOCK_USED', 'Used');
 define('BX_MPI_POOL_BLOCK_USAGE', 'Usage');
 define('BX_MPI_POOL_BLOCK_STATUS', 'Status');
 define('BX_MPI_POOL_BLOCK_ACTIONS', 'Actions');
 define('BX_MPI_POOL_BLOCK_ACTIVE', '🟢 Active');
 define('BX_MPI_POOL_BLOCK_DEPLETED_LABEL', '🔴 Depleted');
 define('BX_MPI_POOL_BLOCK_ARCHIVED', '⚫ Archived');
 define('BX_MPI_POOL_BLOCK_DETAILS_BUTTON', 'Details');
 define('BX_MPI_POOL_BLOCK_DELETE_BUTTON', 'Delete');
 define('BX_MPI_POOL_IMPORT_FIRST_BLOCK', '➕ Import First Block');
 define('BX_MPI_POOL_NO_BLOCKS', 'No EAN blocks imported yet');
 define('BX_MPI_POOL_NO_BLOCKS_TEXT', 'Import your EAN blocks purchased from GS1 Germany as CSV file.');
 define('BX_MPI_POOL_IMPORT_MODAL_TITLE', '📥 Import GS1 Block');
 define('BX_MPI_POOL_CSV_FILE_LABEL', 'CSV file from GS1 Germany:');
 define('BX_MPI_POOL_CSV_FILE_HELP', 'The "Gtin" column is detected automatically');
 define('BX_MPI_POOL_BLOCK_NUMBER_LABEL', 'Block Number:');
 define('BX_MPI_POOL_BLOCK_NUMBER_PLACEHOLDER', BX_MPI_EXAMPLE_PREFIX . 'Block-2024-001');
 define('BX_MPI_POOL_BLOCK_NUMBER_HELP', 'Unique identifier for this block');
 define('BX_MPI_POOL_BLOCK_SIZE_LABEL_SELECT', 'Block Size:');
 define('BX_MPI_POOL_PURCHASE_DATE_LABEL', 'Purchase Date:');
 define('BX_MPI_POOL_NOTES_LABEL', 'Notes (optional):');
 define('BX_MPI_CSV_PLACEHOLDER', 'Special features, discounts, owner, etc.');
 define('BX_MPI_POOL_IMPORT_INFO_TITLE', 'ℹ️ Note:');
 define('BX_MPI_POOL_IMPORT_INFO_TEXT', 'You can download the CSV file directly from GS1 Germany after purchase. 10-block: approx. €40 | 100-block: approx. €150 | 1000-block: approx. €300 (as of 2024)');
 define('BX_MPI_POOL_IMPORT_CANCEL_BUTTON', 'Cancel');
 define('BX_MPI_POOL_IMPORT_SUBMIT_BUTTON', '✓ Import');
 
 // History Tab
 define('BX_MPI_HISTORY_FILTER_PRODUCT_ID_LABEL', 'Product ID:');
 define('BX_MPI_HISTORY_FILTER_PRODUCT_ID_PLACEHOLDER', BX_MPI_EXAMPLE_PREFIX . '143');
 define('BX_MPI_HISTORY_FILTER_FIELD_LABEL', 'Field:');
 define('BX_MPI_HISTORY_FILTER_FIELD_ALL', 'All Fields');
 define('BX_MPI_HISTORY_FILTER_FIELD_SKU', 'SKU');
 define('BX_MPI_HISTORY_FILTER_FIELD_EAN', 'EAN');
 define('BX_MPI_HISTORY_FILTER_FIELD_WWS', 'WWS No.');
 define('BX_MPI_HISTORY_FILTER_FIELD_WAREHOUSE', 'Warehouse Location');
 define('BX_MPI_HISTORY_FILTER_TIMEFRAME_LABEL', 'Timeframe:');
 define('BX_MPI_HISTORY_FILTER_TIMEFRAME_ALL', 'Entire Period');
 define('BX_MPI_HISTORY_FILTER_TIMEFRAME_TODAY', 'Today');
 define('BX_MPI_HISTORY_FILTER_TIMEFRAME_WEEK', 'Last 7 Days');
 define('BX_MPI_HISTORY_FILTER_TIMEFRAME_MONTH', 'Last Month');
 define('BX_MPI_HISTORY_FILTER_BUTTON', '🔍 Filter');
 define('BX_MPI_HISTORY_RECORDS_FOUND', '📊 <strong>%d</strong> records found');
 define('BX_MPI_HISTORY_PAGE_INFO', 'Page <strong>%d</strong> of <strong>%d</strong>');
 define('BX_MPI_HISTORY_TABLE_TIMESTAMP', 'Timestamp');
 define('BX_MPI_HISTORY_TABLE_SKU', 'SKU');
 define('BX_MPI_HISTORY_TABLE_PRODUCT_ID', 'Product ID');
 define('BX_MPI_HISTORY_TABLE_FIELD', 'Field');
 define('BX_MPI_HISTORY_TABLE_OLD_VALUE', 'Old');
 define('BX_MPI_HISTORY_TABLE_NEW_VALUE', 'New');
 define('BX_MPI_HISTORY_TABLE_REASON', 'Reason');
 define('BX_MPI_HISTORY_PREV_PAGE', '« Previous');
 define('BX_MPI_HISTORY_NEXT_PAGE', 'Next »');
 define('BX_MPI_HISTORY_NO_ENTRIES', 'No entries found');
 define('BX_MPI_HISTORY_NO_ENTRIES_FILTERED', 'Try different filter criteria.');
 define('BX_MPI_HISTORY_NO_ENTRIES_DEFAULT', 'No changes have been recorded yet.');
 
 // Disabled History Notice
 define('BX_MPI_HISTORY_DISABLED_TITLE', 'History function is disabled');
 define('BX_MPI_HISTORY_DISABLED_DESC', 'To use the change history, enable it in the settings.');
 define('BX_MPI_HISTORY_SETTINGS_LINK', '⚙️ Go to Settings');
 
 // Settings Tab - Statistics
 define('BX_MPI_SETTINGS_STATISTICS_TITLE', '📊 Module Statistics');
 
 // Sidebar Box - Configuration
 define('BX_MPI_SIDEBAR_CONFIG_TITLE', '⚙️ Current Configuration');
 define('BX_MPI_SIDEBAR_MODULE_STATUS', 'Module Status');
 define('BX_MPI_SIDEBAR_AUTO_CREATE', 'Auto-Create');
 define('BX_MPI_SIDEBAR_EAN_MODE', 'EAN Mode');
 define('BX_MPI_SIDEBAR_ACTIVE', '✓ ACTIVE');
 define('BX_MPI_SIDEBAR_INACTIVE', '✘ INACTIVE');
 define('BX_MPI_SIDEBAR_ENABLED', '✓ ON');
 define('BX_MPI_SIDEBAR_DISABLED', '✘ OFF');
 
 // Sidebar Box - Statistics
 define('BX_MPI_SIDEBAR_STATS_DISPLAYED', 'Displayed');
 define('BX_MPI_SIDEBAR_STATS_WITH_EAN', 'With EAN');
 define('BX_MPI_SIDEBAR_STATS_WITHOUT_EAN', 'Without EAN');
 define('BX_MPI_SIDEBAR_STATS_PSEUDO', '🏷️ Pseudo');
 define('BX_MPI_SIDEBAR_STATS_GS1', '✅ GS1');
 define('BX_MPI_SIDEBAR_STATS_WITHOUT_WAREHOUSE', '📦 Without Warehouse');
 define('BX_MPI_SIDEBAR_STATS_NO_DATA', 'No Data');
// Sidebar Box - Filters
define('BX_MPI_SIDEBAR_FILTER_NO_EAN', '🔍 Without EAN');
define('BX_MPI_SIDEBAR_FILTER_PSEUDO_EAN', '🏷️ Pseudo-EAN Only');
define('BX_MPI_SIDEBAR_FILTER_GS1_EAN', '✅ GS1-EAN Only');
define('BX_MPI_SIDEBAR_FILTER_NO_WAREHOUSE', '📦 Without Warehouse');
define('BX_MPI_SIDEBAR_FILTER_RESET', '↺ Reset');
define('BX_MPI_SIDEBAR_TIP_TITLE', '💡 Tip');

// History Sidebar - Statistics & Actions
define('BX_MPI_HISTORY_SIDEBAR_STATS_TITLE', 'Statistics');
define('BX_MPI_HISTORY_SIDEBAR_CHANGES_TODAY', 'Changes today');
define('BX_MPI_HISTORY_SIDEBAR_LAST_7_DAYS', 'Last 7 days');
define('BX_MPI_HISTORY_SIDEBAR_LAST_ACTIVITIES', 'Recent activities');
define('BX_MPI_HISTORY_SIDEBAR_TOP_FIELDS', 'Top fields');
define('BX_MPI_HISTORY_SIDEBAR_QUICK_ACTIONS', 'Quick Actions');
define('BX_MPI_HISTORY_SIDEBAR_TODAY', 'Today');
define('BX_MPI_HISTORY_SIDEBAR_THIS_WEEK', 'This Week');
define('BX_MPI_HISTORY_SIDEBAR_ONLY_EAN_CHANGES', 'EAN Changes Only');
define('BX_MPI_HISTORY_SIDEBAR_SHOW_ALL', 'Show all');
define('BX_MPI_HISTORY_SIDEBAR_DISABLED_TITLE', 'History disabled');
define('BX_MPI_HISTORY_SIDEBAR_DISABLED_TEXT', 'The history feature is disabled.');

// Settings Sidebar - Setup Check
define('BX_MPI_SETUP_CHECK_TITLE', 'Setup Check');
define('BX_MPI_SETUP_CHECK_ALL_OPTIMAL', 'All optimal!');
define('BX_MPI_SETUP_CHECK_CONFIG_OK', 'Your configuration is fine');
define('BX_MPI_SETUP_CHECK_GS1_MISSING', 'GS1 prefix missing');
define('BX_MPI_SETUP_CHECK_GS1_MISSING_DESC', 'Auto GS1-EAN enabled, but no prefix configured');
define('BX_MPI_SETUP_CHECK_LEGACY_SKU', 'Legacy SKU mode');
define('BX_MPI_SETUP_CHECK_LEGACY_SKU_DESC', 'Model-based can lead to variable lengths');
define('BX_MPI_SETUP_CHECK_LONG_SEPARATOR', 'Long separator');
define('BX_MPI_SETUP_CHECK_LONG_SEPARATOR_DESC', 'extends SKUs');

// Settings Sidebar - Configuration Tips
define('BX_MPI_CONFIG_TIP_TITLE', 'Configuration Tip');
define('BX_MPI_CONFIG_TIP_RECOMMENDED_ACTION', 'Recommended action:');
define('BX_MPI_CONFIG_TIP_GS1_ACTION1', 'Enter your GS1 prefix, OR');
define('BX_MPI_CONFIG_TIP_GS1_ACTION2', 'Switch to "Pseudo-EAN" (Prefix 2)');
define('BX_MPI_CONFIG_TIP_HISTORY_ACTION1', 'Enable history for audit trail');
define('BX_MPI_CONFIG_TIP_HISTORY_ACTION2', 'Especially important for team usage');
define('BX_MPI_CONFIG_TIP_MANUAL_MODE', 'Manual EAN mode active');
define('BX_MPI_CONFIG_TIP_MANUAL_DESC', 'You have full control over EAN numbers. Note: Manual entry requires more effort for many products.');
define('BX_MPI_CONFIG_TIP_PSEUDO_MODE', 'Pseudo-EAN (Prefix 2)');
define('BX_MPI_CONFIG_TIP_PSEUDO_DESC_GOOD', 'Perfect for internal use and warehouse');
define('BX_MPI_CONFIG_TIP_PSEUDO_DESC_WARNING', 'NOT suitable for marketplaces like Amazon/eBay!');
define('BX_MPI_CONFIG_TIP_GS1_MODE', 'GS1-EAN active');
define('BX_MPI_CONFIG_TIP_GS1_DESC1', 'Tradeable on all marketplaces');
define('BX_MPI_CONFIG_TIP_GS1_DESC2', 'Internationally recognized standard');
define('BX_MPI_CONFIG_TIP_GS1_DESC3', 'Requires GS1 membership');
define('BX_MPI_CONFIG_TIP_PERFORMANCE', 'Performance Tip:');
define('BX_MPI_CONFIG_TIP_PERFORMANCE_DESC', 'identifiers we recommend the history function for better tracking and troubleshooting.');

// Settings Sidebar - Help & Tools
define('BX_MPI_HELP_TOOLS_TITLE', 'Help & Tools');
define('BX_MPI_HELP_CONFIG_TEMPLATES', 'Configuration Templates:');
define('BX_MPI_HELP_TEMPLATE_STANDARD', 'Standard:');
define('BX_MPI_HELP_TEMPLATE_STANDARD_DESC', 'Numeric SKU + Pseudo-EAN');
define('BX_MPI_HELP_TEMPLATE_WAREHOUSE', 'Warehouse:');
define('BX_MPI_HELP_TEMPLATE_WAREHOUSE_DESC', 'With warehouse integration');
define('BX_MPI_HELP_TEMPLATE_MARKETPLACE', 'Marketplace:');
define('BX_MPI_HELP_TEMPLATE_MARKETPLACE_DESC', 'GS1-EAN + ERP number');
define('BX_MPI_HELP_TEMPLATE_MINIMAL', 'Minimal:');
define('BX_MPI_HELP_TEMPLATE_MINIMAL_DESC', 'SKU only, no EAN');
define('BX_MPI_HELP_FAQ', 'Frequently Asked Questions (FAQ):');
define('BX_MPI_HELP_FAQ_Q1', 'Pseudo-EAN vs. GS1-EAN?');
define('BX_MPI_HELP_FAQ_A1_PSEUDO', 'Pseudo-EAN (Prefix 2):');
define('BX_MPI_HELP_FAQ_A1_PSEUDO_DESC', 'For internal use, warehouse & scanners. NOT for marketplaces (Amazon/eBay).');
define('BX_MPI_HELP_FAQ_A1_GS1', 'GS1-EAN:');
define('BX_MPI_HELP_FAQ_A1_GS1_DESC', 'Officially registered, tradeable, requires GS1 membership (approx. 100-300€/year).');
define('BX_MPI_HELP_FAQ_Q2', 'When to enable history?');
define('BX_MPI_HELP_FAQ_A2', 'Recommended for: Team usage, audit requirements, frequent changes. Logs all changes with timestamp & reason.');
define('BX_MPI_HELP_FAQ_Q3', 'Change SKU format?');
define('BX_MPI_HELP_FAQ_A3_WARNING', 'Warning:');
define('BX_MPI_HELP_FAQ_A3', 'Changes only affect NEWLY created SKUs! Existing SKUs remain unchanged. Backup recommended.');
define('BX_MPI_HELP_FAQ_Q4', 'Generate EAN retroactively?');
define('BX_MPI_HELP_FAQ_A4', 'Yes! Enable Auto-EAN (Pseudo or GS1). Missing EAN will be automatically created on next access.');
define('BX_MPI_HELP_BACKUP_TIP', 'Tip:');
define('BX_MPI_HELP_BACKUP_TIP_DESC', 'Create a backup before major changes!');