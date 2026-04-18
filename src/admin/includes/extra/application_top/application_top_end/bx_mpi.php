<?php
/**
 * BX Modified Product Identifier (MPI)
 * Autoloader für ProductIdentifier API-Klasse
 * 
 * Lädt die ProductIdentifier-Klasse automatisch wenn Modul aktiviert ist.
 * Diese Datei wird bei jedem Admin-Seitenaufruf eingebunden.
 * 
 * @package    BX Modified Product Identifier
 * @subpackage Autoloader
 * @author     Axel Benkert
 * @version    1.0.0
 * @date       2025-01-16
 * @copyright  2025 Axel Benkert
 * @license    GNU General Public License v2.0
 */

defined('_VALID_XTC') or die('Direct Access to this location is not allowed.');

// Lade ProductIdentifier-Klasse nur wenn Modul aktiv
if (defined('MODULE_BX_MPI_STATUS') && MODULE_BX_MPI_STATUS == 'true') {
    if (file_exists(DIR_FS_ADMIN . DIR_WS_CLASSES . 'ProductIdentifier.php')) {
        require_once(DIR_FS_ADMIN . DIR_WS_CLASSES . 'ProductIdentifier.php');
    }
}
