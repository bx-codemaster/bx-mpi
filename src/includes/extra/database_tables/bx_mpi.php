<?php
/* --------------------------------------------------------------
 * BX MPI - EAN Pool Database Tables Definition
 * 
 * Defines additional database table constants for the
 * BX Modified Product Identifier EAN Pool module.
 * 
 * WICHTIG: TABLE_PRODUCT_IDENTIFIERS zeigt jetzt auf die gemeinsame
 * Tabelle bx_product_variants (geteilt mit BX Stockmanager Pro).
 * 
 * @version 1.5.0
 * @date 2026-01-03
 * --------------------------------------------------------------
 */
define('TABLE_PRODUCT_IDENTIFIERS', 'bx_product_variants');
define('TABLE_PRODUCT_IDENTIFIER_ATTRIBUTES', 'product_identifier_attributes');
define('TABLE_PRODUCT_IDENTIFIER_HISTORY', 'product_identifier_history');
// EAN Blocks Table (GS1 purchased blocks)
define('TABLE_PRODUCT_IDENTIFIER_EAN_BLOCKS', 'bx_ean_blocks');
// EAN Pool Table (individual EANs from blocks)
define('TABLE_PRODUCT_IDENTIFIER_EAN_POOL', 'bx_ean_pool');