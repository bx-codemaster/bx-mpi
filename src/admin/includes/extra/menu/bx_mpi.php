<?php
/** 
 * ██████╗  ███████╗ ███╗   ██╗  █████╗  ██╗  ██╗
 * ██╔══██╗ ██╔════╝ ████╗  ██║ ██╔══██╗ ╚██╗██╔╝
 * ██████╔╝ █████╗   ██╔██╗ ██║ ███████║  ╚███╔╝
 * ██╔══██╗ ██╔══╝   ██║╚██╗██║ ██╔══██║  ██╔██╗
 * ██████╔╝ ███████╗ ██║ ╚████║ ██║  ██║ ██╔╝ ██╗
 * ╚═════╝  ╚══════╝ ╚═╝  ╚═══╝ ╚═╝  ╚═╝ ╚═╝  ╚═╝
 * BX Modified Product Identifier - Admin Menu Integration
 * 
 * Registriert den Menüeintrag für BX Modified Product Identifier im Admin-Bereich.
 * Fügt das Modul in die Tools-Sektion des modified eCommerce Admin-Menüs ein.
 * 
 * Menu Configuration:
 * - Box: BOX_HEADING_TOOLS (Werkzeuge)
 * - Access Name: bx_modified_product_identifier
 * - Filename: bx_modified_product_identifier.php
 * - SSL: Required
 * - Status: Controlled by MODULE_BX_MODIFIED_PRODUCT_IDENTIFIER_STATUS
 * 
 * @package    BX Modified Product Identifier
 * @subpackage Configuration
 * @category   Admin
 * @author     Axel Benkert
 * @version    1.2
 * @since      1.0.0
 * @date       2025-11-09
 * @copyright  2020-2025 Axel Benkert
 * @license    GNU General Public License
 */

defined('_VALID_XTC') or die('Direct Access to this location is not allowed.');

if (defined("MODULE_BX_MPI_STATUS") && 'true' === MODULE_BX_MPI_STATUS) {
  switch ($_SESSION['language_code']) {
    case 'de':
      if (!defined('MENU_NAME_BX_MPI')) define('MENU_NAME_BX_MPI', 'BX Modified Product Identifier');
      break;
    default:
      if (!defined('MENU_NAME_BX_MPI')) define('MENU_NAME_BX_MPI', 'BX Modified Product Identifier');
      break;
  }

  // BOX_HEADING_TOOLS = Werkzeuge-Menü im Admin
  $add_contents[BOX_HEADING_TOOLS][] = array( 
    'admin_access_name' => 'bx_mpi',
    'filename' => 'bx_mpi.php',
    'boxname' => MENU_NAME_BX_MPI,
    'parameters' => '',
    'ssl' => 'SSL'
  );
}
