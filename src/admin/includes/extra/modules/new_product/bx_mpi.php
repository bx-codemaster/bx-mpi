<?php
/* --------------------------------------------------------------
 * BX MPI - Variant Generator for SKU/EAN
 * 
 * Auto-Include for Product Editing
 * Automatically generates SKUs for all attribute combinations
 * Enables EAN assignment from pool, pseudo-EAN or manual input
 * 
 * @version 2.0.0
 * @date 2025-11-23
 * --------------------------------------------------------------
 */

  defined('_VALID_XTC') or die('Direct Access to this location is not allowed.');
  
  // Check if module is active
  if (!defined('MODULE_BX_MPI_STATUS') || MODULE_BX_MPI_STATUS != 'true') {
    return;
  }
  
  // Load ProductIdentifier class
  if (!class_exists('ProductIdentifier')) {
    require_once(DIR_WS_CLASSES . 'ProductIdentifier.php');
  }
  
  // Load CartesianBuilder class
  if (!class_exists('bxCartesianBuilder')) {
    require_once(DIR_FS_CATALOG . DIR_WS_CLASSES . 'bx_cartesian_builder.php');
  }
  
  // JavaScript-Meldungen als PHP-Variablen definieren
  $js_messages = array(
    'pool_empty'              => SKU_EAN_VARIANT_JS_POOL_EMPTY,
    'all_variants_set'        => SKU_EAN_VARIANT_JS_ALL_VARIANTS_SET,
    'please_enter_ean'        => SKU_EAN_VARIANT_JS_PLEASE_ENTER_EAN,
    'invalid_ean'             => SKU_EAN_VARIANT_JS_INVALID_EAN,
    'confirm_remove_ean'      => SKU_EAN_VARIANT_JS_CONFIRM_REMOVE_EAN,
    'confirm_assign_pool'     => SKU_EAN_VARIANT_JS_CONFIRM_ASSIGN_POOL,
    'confirm_generate_pseudo' => SKU_EAN_VARIANT_JS_CONFIRM_GENERATE_PSEUDO,
    'confirm_manual_ean'      => SKU_EAN_VARIANT_JS_CONFIRM_MANUAL_EAN,
    'processing'              => SKU_EAN_VARIANT_JS_PROCESSING,
    'select_variant'          => SKU_EAN_VARIANT_JS_SELECT_VARIANT,
    'confirm_save'            => SKU_EAN_VARIANT_JS_CONFIRM_SAVE,
    'saving'                  => SKU_EAN_VARIANT_JS_SAVING,
    'saving_success'          => SKU_EAN_VARIANT_JS_SAVING_SUCCESS,
    'error_prefix'            => SKU_EAN_VARIANT_JS_ERROR_PREFIX,
    'connection_error'        => SKU_EAN_VARIANT_JS_CONNECTION_ERROR
  );
  
  $current_product_id = isset($pInfo->products_id) ? (int)$pInfo->products_id : 0;
  
  // Load pool statistics
  $pool_query = xtc_db_query("
      SELECT COALESCE(SUM(available_eans), 0) as available_eans
      FROM " . TABLE_PRODUCT_IDENTIFIER_EAN_BLOCKS . "
      WHERE status = 'active'
  ");
  $pool_data = xtc_db_fetch_array($pool_query);
  $available_pool_eans = (int)$pool_data['available_eans'];
  
  // Check if product has attributes
  $has_attributes = false;
  $attribute_combinations = array();
  
  if ($current_product_id > 0) {
      $attr_check = xtc_db_query("
          SELECT COUNT(*) as count 
          FROM " . TABLE_PRODUCTS_ATTRIBUTES . " 
          WHERE products_id = '" . $current_product_id . "'
      ");
      $attr_row = xtc_db_fetch_array($attr_check);
      $has_attributes = $attr_row['count'] > 0;
      
      // Generate variants if attributes are present
      if ($has_attributes) {
          $builder = new bxCartesianBuilder($current_product_id);
          $cartesian_codes = $builder->getCartesian();
          
          // Convert cartesian codes to readable combinations
          foreach ($cartesian_codes as $code) {
              // Format: "0123_0001-0010x0002-0011" → Parse
              $parts = explode('_', $code);
              if (count($parts) < 2) continue;
              
              $attr_codes    = explode('x', $parts[1]);
              $attributes    = array();
              $attribute_ids = array();
              
              foreach ($attr_codes as $attr_code) {
                list($option_id, $value_id) = explode('-', $attr_code);
                $option_id = (int)$option_id;
                $value_id = (int)$value_id;
                
                // Namen laden
                $name_query = xtc_db_query("
                    SELECT 
                        po.products_options_name,
                        pov.products_options_values_name
                    FROM " . TABLE_PRODUCTS_ATTRIBUTES . " pa
                    LEFT JOIN " . TABLE_PRODUCTS_OPTIONS . " po ON pa.options_id = po.products_options_id 
                        AND po.language_id = '" . (int)$_SESSION['languages_id'] . "'
                    LEFT JOIN " . TABLE_PRODUCTS_OPTIONS_VALUES . " pov ON pa.options_values_id = pov.products_options_values_id 
                        AND pov.language_id = '" . (int)$_SESSION['languages_id'] . "'
                    WHERE pa.products_id = '" . $current_product_id . "'
                      AND pa.options_id = '" . $option_id . "'
                      AND pa.options_values_id = '" . $value_id . "'
                    LIMIT 1
                ");
                  
                if ($name_row = xtc_db_fetch_array($name_query)) {
                  $attributes[] = array(
                      'option_id' => $option_id,
                      'value_id' => $value_id,
                      'option_name' => $name_row['products_options_name'],
                      'value_name' => $name_row['products_options_values_name']
                  );
                  $attribute_ids[$option_id] = $value_id;
                }
              }
              
              if (!empty($attributes)) {
                $attribute_combinations[] = array(
                    'code'          => $code,
                    'attributes'    => $attributes,
                    'attribute_ids' => $attribute_ids,
                    'display_name'  => implode(' × ', array_column($attributes, 'value_name'))
                );
              }
          }
      }
  }
?>
<div id="bx-mpi-variant-generator" style="padding:5px;">
  <div id="variant-panel-header" class="main" data-action="toggle-variant-panel" style="cursor: pointer; user-select: none; background: linear-gradient(135deg, #e3f2fd 0%, #bbdefb 100%); transition: all 0.3s ease; border-left: 4px solid #2196f3; padding: 5px 12px;
  margin-bottom: 5px;">
    <b>🏷️ <?php echo SKU_EAN_VARIANT_GENERATOR; ?></b>
    <?php if ($has_attributes && count($attribute_combinations) > 0): ?>
    <span style="color: #1565c0; font-weight: normal;">(<?php echo count($attribute_combinations) . ' ' . SKU_EAN_VARIANT_COMBINATIONS; ?>)</span>
    <?php endif; ?>
    <span id="variant-toggle-icon" style="float: right; font-size: 18px; transition: transform 0.3s;">▼</span>
    <span class="tooltip">
      <img src="images/icons/tooltip_icon.png" style="border:0;">
      <em><?php echo SKU_EAN_VARIANT_GENERATOR_TOOLTIP; ?></em>
    </span>
  </div>

  <table class="tableInput">
    <tr>
      <td class="main">

  <!-- Pool Status //-->
  <div style="background: <?php echo $available_pool_eans > 100 ? '#d4edda' : ($available_pool_eans > 0 ? '#fff3cd' : '#f8d7da'); ?>; 
              border: 1px solid <?php echo $available_pool_eans > 100 ? '#c3e6cb' : ($available_pool_eans > 0 ? '#ffeaa7' : '#f5c6cb'); ?>; 
              padding: 12px; border-radius: 4px;">
    <div style="display: flex; justify-content: space-between; align-items: center;">
      <div>
        <strong style="color: <?php echo $available_pool_eans > 100 ? '#155724' : ($available_pool_eans > 0 ? '#856404' : '#721c24'); ?>;">
        <?php
        if ($available_pool_eans > 0):
          echo '✓ '.$available_pool_eans .' ' .SKU_EAN_VARIANT_EAN_POOL_AVAILABLE;
        else: 
          echo '⚠️ '.SKU_EAN_VARIANT_EAN_POOL_NOT_AVAILABLE;
        endif; ?>
        </strong>
      </div>
      <a href="<?php echo xtc_href_link(FILENAME_BX_MPI); ?>" target="_blank" class="button" style="padding: 6px 15px; text-decoration: none;">
        <?php echo SKU_EAN_VARIANT_MANAGE_POOL; ?> →
      </a>
    </div>
  </div>


  <div id="variant-panel-content" style="display: none; margin-top: 10px;">
    <?php if ($current_product_id == 0): ?>
    <!-- Note: Product must be saved first //-->
    <div style="background: #e7f3ff; border: 1px solid #b3d9ff; padding: 15px; border-radius: 4px; text-align: center;">
      <strong style="color: #0066cc;">ℹ️ <?php echo SKU_EAN_VARIANT_NOTE; ?>:</strong><br>
      <span style="color: #555;"><?php echo SKU_EAN_VARIANT_SAVE_PRODUCT_FIRST; ?></span>
    </div>
    
    <?php elseif (!$has_attributes): ?>
    <!-- Simple product without attributes //-->
    <?php
      // Load base identifier (without attributes)
      $base_identifier = ProductIdentifier::getIdentifier($current_product_id, array());
      $base_sku = $base_identifier ? $base_identifier['products_sku'] : null;
      $base_ean = $base_identifier ? $base_identifier['products_ean'] : null;
      $base_id  = $base_identifier ? $base_identifier['identifier_id'] : 0;
      
      // Detect EAN type
      $ean_source_default = 'pool';
      if (!empty($base_ean)) {
          $pool_check = xtc_db_query("
              SELECT pool_id FROM " . TABLE_PRODUCT_IDENTIFIER_EAN_POOL . "
              WHERE ean = '" . xtc_db_input($base_ean) . "'
              AND identifier_id = '" . $base_id . "'
          ");
          
          if (xtc_db_num_rows($pool_check) > 0) {
              $ean_source_default = 'pool';
          } elseif (substr($base_ean, 0, 1) == '2') {
              $ean_source_default = 'pseudo';
          } else {
              $ean_source_default = 'manual';
          }
      } elseif (empty($base_ean) && $base_identifier) {
          $ean_source_default = 'none';
      }
    ?>
    <div style="background: #f8f9fa; border: 1px solid #dee2e6; padding: 20px; border-radius: 6px;">
      <div style="margin-bottom: 15px;">
        <strong style="font-size: 14px; color: #495057;">📦 <?php echo SKU_EAN_VARIANT_SIMPLE_PRODUCT; ?></strong>
        <br><small style="color: #6c757d;"><?php echo SKU_EAN_VARIANT_NO_ATTRIBUTES; ?></small>
      </div>
      
      <table class="tableInput" style="width: 100%; margin-bottom: 15px;">
        <tr style="border-bottom: 1px solid #e9ecef;">
          <td style="padding: 12px 8px; width: 150px; font-weight: bold; color: #495057;"><?php echo SKU_EAN_VARIANT_BASIS_SKU; ?>:</td>
          <td style="padding: 12px 8px;">
            <?php if ($base_sku): ?>
              <code style="background: #e9ecef; padding: 6px 12px; border-radius: 4px; font-size: 13px; font-family: monospace; font-weight: bold;">
                <?php echo htmlspecialchars($base_sku); ?>
              </code>
              <span style="color: #28a745; font-size: 12px; margin-left: 10px;">✓ <?php echo SKU_EAN_VARIANT_SAVED; ?></span>
            <?php else: ?>
              <span style="color: #6c757d; font-style: italic;"><?php echo SKU_EAN_VARIANT_AUTO_GENERATED; ?></span>
            <?php endif; ?>
          </td>
        </tr>
        <tr>
          <td style="padding: 12px 8px; width: 150px; font-weight: bold; color: #495057; vertical-align: top;"><?php echo SKU_EAN_VARIANT_PSEUDO_CURRENT_EAN; ?>:</td>
          <td style="padding: 12px 8px;">
            <div id="bx-mpi-simple-ean-display" style="margin-bottom: 10px;">
              <?php if ($base_ean): ?>
                <code style="background: #d4edda; padding: 6px 12px; border-radius: 4px; font-size: 13px; font-weight: bold; color: #155724; font-family: monospace;">
                  <?php echo htmlspecialchars($base_ean); ?>
                </code>
                <?php
                  $ean_type_label = '';
                  if (substr($base_ean, 0, 1) == '2') {
                      $ean_type_label = '<span style="color: #856404; font-size: 11px; margin-left: 8px;">🤖 ' . SKU_EAN_VARIANT_PSEUDO_EAN . '</span>';
                  } else {
                      $pool_check2 = xtc_db_query("SELECT pool_id FROM " . TABLE_PRODUCT_IDENTIFIER_EAN_POOL . " WHERE ean = '" . xtc_db_input($base_ean) . "'");
                      if (xtc_db_num_rows($pool_check2) > 0) {
                          $ean_type_label = '<span style="color: #155724; font-size: 11px; margin-left: 8px;">🔢 ' . SKU_EAN_VARIANT_FROM_POOL . '</span>';
                      } else {
                          $ean_type_label = '<span style="color: #495057; font-size: 11px; margin-left: 8px;">✏️ ' . SKU_EAN_VARIANT_MANUAL . '</span>';
                      }
                  }
                  echo $ean_type_label;
                ?>
              <?php else: ?>
                <span style="color: #dc3545; font-weight: bold; font-size: 13px;">✗ <?php echo SKU_EAN_VARIANT_NO_EAN_ASSIGNED; ?></span>
              <?php endif; ?>
            </div>
            
    <!-- EAN Assignment //-->
    <div style="background: #fff; border: 1px solid #ced4da; padding: 15px; border-radius: 4px;">
              <div style="margin-bottom: 12px;">
                <label style="display: block; font-weight: bold; margin-bottom: 6px; color: #495057;"><?php echo SKU_EAN_VARIANT_EAN_SOURCE_LABEL; ?>:</label>
                <select id="bx-mpi-simple-ean-source" style="width: 100%; padding: 8px; border: 1px solid #ced4da; border-radius: 4px;">
                  <option value="pool" <?php echo $available_pool_eans > 0 ? '' : 'disabled'; ?> <?php echo $ean_source_default == 'pool' ? 'selected' : ''; ?>>
                    🔢 <?php echo SKU_EAN_VARIANT_SELECT_FROM_POOL; ?><?php echo $available_pool_eans > 0 ? ' (' . $available_pool_eans . ' ' . SKU_EAN_VARIANT_AVAILABLE . ')' : ' (' . SKU_EAN_VARIANT_EMPTY . ')'; ?>
                  </option>
                  <option value="pseudo" <?php echo $ean_source_default == 'pseudo' ? 'selected' : ''; ?>>🤖 <?php echo SKU_EAN_VARIANT_GENERATE_PSEUDO_EAN; ?></option>
                  <option value="manual" <?php echo $ean_source_default == 'manual' ? 'selected' : ''; ?>>✏️ <?php echo SKU_EAN_VARIANT_MANUAL_ENTRY; ?></option>
                  <option value="release" <?php echo !empty($base_ean) ? '' : 'disabled'; ?>>🔄 <?php echo SKU_EAN_VARIANT_REMOVE_EAN; ?><?php echo !empty($base_ean) ? '' : ' (' . SKU_EAN_VARIANT_NO_EAN_PRESENT . ')'; ?></option>
                </select>
              </div>
              
              <div id="bx-mpi-simple-manual-input" style="display: <?php echo $ean_source_default == 'manual' ? 'block' : 'none'; ?>; margin-bottom: 12px;">
                <label style="display: block; font-weight: bold; margin-bottom: 6px; color: #495057;"><?php echo SKU_EAN_VARIANT_EAN_INPUT_LABEL; ?>:</label>
                <input type="text" 
                       id="bx-mpi-simple-manual-ean" 
                       placeholder="<?php echo SKU_EAN_VARIANT_EAN_PLACEHOLDER; ?>"
                       maxlength="14"
                       value="<?php echo $ean_source_default == 'manual' && $base_ean ? htmlspecialchars($base_ean) : ''; ?>"
                       style="width: 100%; padding: 8px; border: 1px solid #ced4da; border-radius: 4px; font-family: monospace; font-size: 13px;">
                <small style="color: #6c757d; display: block; margin-top: 4px;"><?php echo SKU_EAN_VARIANT_EAN_FORMAT_INFO; ?></small>
              </div>
              
              <button type="button" id="bx-mpi-simple-assign" class="button" style="padding: 10px 20px; background: #28a745; color: #fff; font-weight: bold; width: 100%;">
                💾 <?php echo SKU_EAN_VARIANT_ASSIGN_EAN_BUTTON; ?>
              </button>
            </div>
          </td>
        </tr>
      </table>
      
      <!-- Status Message //-->
      <div id="bx-mpi-simple-message" style="display: none; margin-top: 15px; padding: 12px; border-radius: 4px;"></div>
    </div>
    
    <?php else: ?>
    <!-- Variants Table //-->
    <div style="margin-bottom: 15px;">
      <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
        <div>
          <strong><?php echo count($attribute_combinations); ?> <?php echo SKU_EAN_VARIANT_TABLE_VARIANTS; ?></strong>
          <span style="color: #666; font-size: 12px;"><?php echo SKU_EAN_VARIANT_VARIANTS_GENERATED_FROM; ?></span>
        </div>
        <div>
          <button type="button" id="bx-mpi-select-all" class="button" style="padding: 6px 12px; font-size: 12px;">
            ☑ <?php echo SKU_EAN_VARIANT_SELECT_ALL; ?>
          </button>
        </div>
      </div>
      
      <!-- EAN Source Bulk Buttons //-->
      <div style="background: #f8f9fa; border: 1px solid #ddd; padding: 10px; margin-bottom: 10px; border-radius: 4px;">
        <div style="display: flex; align-items: center; gap: 10px; flex-wrap: wrap;">
          <strong style="margin-right: 10px; color: #495057;">🎯 <?php echo SKU_EAN_VARIANT_EAN_SOURCE_FOR_ALL; ?>:</strong>
          <button type="button" class="button bx-mpi-bulk-source" data-source="pool" 
                  style="padding: 6px 12px; font-size: 12px; transition: all 0.3s; <?php echo $available_pool_eans > 0 ? '' : 'opacity: 0.5; cursor: not-allowed;'; ?>"
                  <?php echo $available_pool_eans > 0 ? '' : 'disabled'; ?>>
            🔢 <?php echo SKU_EAN_VARIANT_TABLE_VARIANTS; ?> → Pool<?php echo $available_pool_eans > 0 ? '' : ' (' . SKU_EAN_VARIANT_EMPTY . ')'; ?>
          </button>
          <button type="button" class="button bx-mpi-bulk-source" data-source="pseudo" 
                  style="padding: 6px 12px; font-size: 12px; transition: all 0.3s;">
            🤖 <?php echo SKU_EAN_VARIANT_TABLE_VARIANTS; ?> → Pseudo-EAN
          </button>
          <button type="button" class="button bx-mpi-bulk-source" data-source="manual" 
                  style="padding: 6px 12px; font-size: 12px; transition: all 0.3s;">
            ✏️ <?php echo SKU_EAN_VARIANT_TABLE_VARIANTS; ?> → Manuell
          </button>
          <button type="button" class="button bx-mpi-bulk-source" data-source="none" 
                  style="padding: 6px 12px; font-size: 12px; background: #6c757d; transition: all 0.3s;">
            ○ <?php echo SKU_EAN_VARIANT_TABLE_VARIANTS; ?> → <?php echo SKU_EAN_VARIANT_NO_EAN; ?>
          </button>
        </div>
        <small style="color: #6c757d; display: block; margin-top: 8px;">
          💡 <?php echo SKU_EAN_VARIANT_BULK_TIP; ?>
        </small>
      </div>
      
      <table class="tableInput" style="width: 100%;">
        <thead>
          <tr style="background: #f8f9fa;">
            <th style="padding: 8px; text-align: center; border-bottom: 2px solid #ddd; width: 40px;">
              <input type="checkbox" id="bx-mpi-check-all">
            </th>
            <th style="padding: 8px; text-align: center; border-bottom: 2px solid #ddd; width: 40px;">#</th>
            <th style="padding: 8px; text-align: left; border-bottom: 2px solid #ddd;"><?php echo SKU_EAN_VARIANT_TABLE_VARIANT; ?></th>
            <th style="padding: 8px; text-align: left; border-bottom: 2px solid #ddd; width: 200px;"><?php echo SKU_EAN_VARIANT_TABLE_GENERATED_SKU; ?></th>
            <th style="padding: 8px; text-align: left; border-bottom: 2px solid #ddd; width: 180px;"><?php echo SKU_EAN_VARIANT_TABLE_EAN; ?></th>
            <th style="padding: 8px; text-align: center; border-bottom: 2px solid #ddd; width: 220px;"><?php echo SKU_EAN_VARIANT_TABLE_EAN_SOURCE; ?></th>
          </tr>
        </thead>
        <tbody>
          <?php 
          $row_num = 0;
          foreach ($attribute_combinations as $combo): 
            $row_num++;
            
            // Check if already saved
            $existing     = ProductIdentifier::getIdentifier($current_product_id, $combo['attribute_ids']);
            $existing_ean = $existing ? $existing['products_ean'] : null;
            $existing_id  = $existing ? $existing['identifier_id'] : null;
            
            // Generate SKU (Preview) - only if not saved
            if ($existing) {
                $preview_sku = $existing['products_sku'];
            } else {
                // New: Generate SKU and store in DB
                $preview_sku = ProductIdentifier::createSKU($current_product_id, $combo['attribute_ids']);
                if (!$preview_sku) {
                    // Error during generation - show fallback
                    $preview_sku = false;
                }
            }
            
            // Detect EAN type for pre-selection
            $ean_source_default = 'pool';
            if (!empty($existing_ean)) {
                // Check if from pool
                $pool_check = xtc_db_query("
                    SELECT pool_id FROM " . TABLE_PRODUCT_IDENTIFIER_EAN_POOL . "
                    WHERE ean = '" . xtc_db_input($existing_ean) . "'
                    AND identifier_id = '" . $existing_id . "'
                ");
                
                if (xtc_db_num_rows($pool_check) > 0) {
                    $ean_source_default = 'pool';
                } elseif (substr($existing_ean, 0, 1) == '2') {
                    // Pseudo-EAN (starts with 2)
                    $ean_source_default = 'pseudo';
                } else {
                    // Manually entered
                    $ean_source_default = 'manual';
                }
            } elseif (empty($existing_ean) && $existing) {
                // Identifier exists but has no EAN
                $ean_source_default = 'none';
            }
          ?>
          <tr class="bx-mpi-variant-row" 
              data-variant-code="<?php echo htmlspecialchars($combo['code']); ?>"
              data-attribute-ids="<?php echo htmlspecialchars(json_encode($combo['attribute_ids'])); ?>"
              data-existing-id="<?php echo $existing_id ?: '0'; ?>"
              style="border-bottom: 1px solid #e9ecef;">
            
            <td style="padding: 8px; text-align: center;">
              <input type="checkbox" class="bx-mpi-variant-check" data-row="<?php echo $row_num; ?>">
            </td>
            
            <td style="padding: 8px; text-align: center; color: #999;"><?php echo $row_num; ?></td>
            
            <td style="padding: 8px;">
              <strong><?php echo htmlspecialchars($combo['display_name']); ?></strong>
              <br><small style="color: #999;">
                <?php foreach ($combo['attributes'] as $attr): ?>
                  <?php echo htmlspecialchars($attr['option_name'] . ': ' . $attr['value_name']); ?>
                  <?php if ($attr !== end($combo['attributes'])): ?>, <?php endif; ?>
                <?php endforeach; ?>
              </small>
            </td>
            
            <td style="padding: 8px;">
              <code style="background: #e9ecef; padding: 4px 8px; border-radius: 3px; font-size: 12px; font-family: monospace;">
                <?php echo htmlspecialchars($preview_sku ?: '(' . SKU_EAN_VARIANT_WILL_BE_GENERATED . ')'); ?>
              </code>
              <?php if ($existing): ?>
              <br><small style="color: #28a745;">✓ <?php echo SKU_EAN_VARIANT_SAVED_STATUS; ?></small>
              <?php else: ?>
              <br><small style="color: #6c757d;">○ <?php echo SKU_EAN_VARIANT_NEW_STATUS; ?></small>
              <?php endif; ?>
            </td>
            
            <td style="padding: 8px;">
              <span class="bx-mpi-ean-display-<?php echo $row_num; ?>">
                <?php if ($existing_ean): ?>
                  <code style="background: #d4edda; padding: 4px 8px; border-radius: 3px; font-size: 12px; font-weight: bold; color: #155724;">
                    <?php echo htmlspecialchars($existing_ean); ?>
                  </code>
                <?php else: ?>
                  <span style="color: #dc3545; font-weight: bold; font-size: 12px;">✗ <?php echo SKU_EAN_VARIANT_NO_EAN_ASSIGNED; ?></span>
                <?php endif; ?>
              </span>
            </td>
            
            <td style="padding: 8px; text-align: center;">
              <select class="bx-mpi-ean-source" data-row="<?php echo $row_num; ?>" style="width: 100%; padding: 4px;">
                <option value="pool" <?php echo $available_pool_eans > 0 ? '' : 'disabled'; ?> <?php echo $ean_source_default == 'pool' ? 'selected' : ''; ?>>
                  🔢 <?php echo SKU_EAN_VARIANT_SELECT_FROM_POOL; ?><?php echo $available_pool_eans > 0 ? ' (' . $available_pool_eans . ' verf.)' : ' (' . SKU_EAN_VARIANT_EMPTY . ')'; ?>
                </option>
                <option value="pseudo" <?php echo $ean_source_default == 'pseudo' ? 'selected' : ''; ?>>🤖 <?php echo SKU_EAN_VARIANT_GENERATE_PSEUDO_EAN; ?></option>
                <option value="manual" <?php echo $ean_source_default == 'manual' ? 'selected' : ''; ?>>✏️ <?php echo SKU_EAN_VARIANT_MANUAL_ENTRY; ?></option>
                <option value="none" <?php echo $ean_source_default == 'none' ? 'selected' : ''; ?>>○ <?php echo SKU_EAN_VARIANT_NO_EAN; ?></option>
              </select>
              
              <!-- Manual input field (hidden) //-->
              <input type="text" 
                     class="bx-mpi-ean-manual" 
                     data-row="<?php echo $row_num; ?>"
                     placeholder="<?php echo SKU_EAN_VARIANT_EAN_PLACEHOLDER; ?>"
                     maxlength="14"
                     value="<?php echo $ean_source_default == 'manual' ? htmlspecialchars($existing_ean) : ''; ?>"
                     style="display: <?php echo $ean_source_default == 'manual' ? 'block' : 'none'; ?>; width: 100%; margin-top: 5px; padding: 4px; font-family: monospace;">
            </td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
    
    <!-- Bulk-Aktionen //-->
    <div style="background: #f8f9fa; border: 1px solid #ddd; border-radius: 4px; padding: 15px;">
      <div style="display: flex; justify-content: space-between; align-items: center;">
        <div>
          <strong><?php echo SKU_EAN_VARIANT_BULK_ACTIONS; ?></strong>
          <br><small style="color: #666;"><?php echo SKU_EAN_VARIANT_SELECT_VARIANTS_DESC; ?></small>
        </div>
        <div>
          <button type="button" id="bx-mpi-save-selected" class="button" style="padding: 10px 25px; background: #28a745; color: #fff; font-weight: bold;">
            💾 <?php echo SKU_EAN_VARIANT_SAVE_SELECTED_BUTTON; ?>
          </button>
        </div>
      </div>
    </div>
    
    <!-- Status Message //-->
    <div id="bx-mpi-variant-message" style="display: none; margin-top: 15px; padding: 12px; border-radius: 4px;"></div>
    
    <?php endif; ?>
  </div>
      </td>
    </tr>
  </table>
</div>

<script>
$(function() {
  "use strict";
    
  // JavaScript-Meldungen aus PHP
  var jsMessages = <?php echo json_encode($js_messages); ?>;
  var panelOpen = false;
    
    // Panel Toggle with Event Delegation     
  $(document).on("click", "[data-action='toggle-variant-panel']", function() {
    panelOpen = !panelOpen;
    $("#variant-panel-content").slideToggle(300);
    $("#variant-toggle-icon").css("transform", panelOpen ? "rotate(180deg)" : "rotate(0deg)");
    
    // Background color change
    var $header = $(this);
    if (panelOpen) {
      // Opened: Green gradient
      $header.css({
        "background": "linear-gradient(135deg, #c8e6c9 0%, #a5d6a7 100%)", "border-left-color": "#4caf50"
      });
    } else {
      // Closed: Blue gradient
      $header.css({
        "background": "linear-gradient(135deg, #e3f2fd 0%, #bbdefb 100%)", "border-left-color": "#2196f3"
      });
    }
  });
  
  // Select/Deselect all
  $("#bx-mpi-check-all").on("change", function() {
    $(".bx-mpi-variant-check").prop("checked", $(this).prop("checked"));
  });
  
  $("#bx-mpi-select-all").on("click", function() {
    var allChecked = $(".bx-mpi-variant-check:checked").length === $(".bx-mpi-variant-check").length;
    $(".bx-mpi-variant-check").prop("checked", !allChecked);
    $("#bx-mpi-check-all").prop("checked", !allChecked);
  });
  
  // Bulk EAN source set
  $(".bx-mpi-bulk-source").on("click", function() {
    var $btn = $(this);
    var source = $btn.data("source");
    
    // Pool button disabled if empty
    if ($btn.prop("disabled")) {
      showVariantMessage("⚠️ " + jsMessages.pool_empty, "warning");
      return;
    }
    
    var sourceName = {
        'pool': 'Pool',
        'pseudo': 'Pseudo-EAN',
        'manual': '<?php echo SKU_EAN_VARIANT_MANUAL; ?>',
        'none': '<?php echo SKU_EAN_VARIANT_NONE; ?>'
    }[source] || source;
    
    // All dropdowns set to this source
    $(".bx-mpi-ean-source").val(source).trigger("change");
    
    // Visual feedback - highlight only clicked button
    $(".bx-mpi-bulk-source").css({
        "background": "",
        "color": "",
        "box-shadow": ""
    });
    
    $btn.css({
        "background": "#28a745",
        "color": "#fff",
        "box-shadow": "0 0 8px rgba(40, 167, 69, 0.6)"
    });
    
    setTimeout(function() {
        $btn.css({
            "background": "",
            "color": "",
            "box-shadow": ""
        });
    }, 1000);
    
    showVariantMessage("✓ " + jsMessages.all_variants_set.replace("%s", sourceName), "success");
  });
    
  // EAN source change (variants)
  $(".bx-mpi-ean-source").on("change", function() {
    var $select = $(this);
    var rowNum  = $select.data("row");
    var source  = $select.val();
    var $manualInput = $(".bx-mpi-ean-manual[data-row='" + rowNum + "']");
    
    if (source === "manual") {
      $manualInput.show();
    } else {
      $manualInput.hide();
    }
  });
    
  // EAN source change (simple product)
  $("#bx-mpi-simple-ean-source").on("change", function() {
      var source = $(this).val();
      var $manualInput = $("#bx-mpi-simple-manual-input");
      
      if (source === "manual") {
          $manualInput.show();
          $("#bx-mpi-simple-manual-ean").focus();
      } else {
          $manualInput.hide();
      }
  });
    
  // Assign EAN (simple product)
  $("#bx-mpi-simple-assign").on("click", function() {
      var $btn = $(this);
      var source = $("#bx-mpi-simple-ean-source").val();
      var manualEan = $("#bx-mpi-simple-manual-ean").val().trim();
      
      // Validation
      if (source === "manual" && !manualEan) {
          showSimpleMessage("⚠️ " + jsMessages.please_enter_ean, "warning");
          $("#bx-mpi-simple-manual-ean").focus();
          return;
      }
      
      if (source === "manual" && !/^[0-9]{13,14}$/.test(manualEan)) {
          showSimpleMessage("⚠️ " + jsMessages.invalid_ean, "warning");
          $("#bx-mpi-simple-manual-ean").focus();
          return;
      }
      
      if (source === "release") {
          if (!confirm(jsMessages.confirm_remove_ean)) {
              return;
          }
      } else {
          var confirmMsg = {
              'pool': jsMessages.confirm_assign_pool,
              'pseudo': jsMessages.confirm_generate_pseudo,
              'manual': jsMessages.confirm_manual_ean.replace("%s", manualEan)
          }[source];
          
          if (!confirm(confirmMsg)) {
              return;
          }
      }
      
      $btn.prop("disabled", true).html("⏳ " + jsMessages.processing);
      
      var ajaxAction = source === "release" ? "release_simple_ean" : "assign_simple_ean";
      
      $.ajax({
          url: "<?php echo xtc_href_link(FILENAME_BX_MPI, 'ajax=1'); ?>&ajax_action=" + ajaxAction,
          type: "POST",
          dataType: "json",
          data: { 
              product_id: <?php echo $current_product_id; ?>,
              ean_source: source,
              manual_ean: manualEan<?php if (defined('CSRF_TOKEN_SYSTEM') && CSRF_TOKEN_SYSTEM == 'true') { 
                  echo ', '.PHP_EOL
                  .'                '.$_SESSION["CSRFName"].": '".$_SESSION["CSRFToken"]."'".PHP_EOL; 
              } ?>
          }
      }).done(function(response) {
          if (response.success) {
              showSimpleMessage("✓ " + response.message, "success");
              
              // EAN-Anzeige aktualisieren
              if (response.ean) {
                  var eanTypeLabel = '';
                  if (source === 'pool') {
                      eanTypeLabel = '<span style="color: #155724; font-size: 11px; margin-left: 8px;">🔢 <?php echo SKU_EAN_VARIANT_FROM_POOL; ?></span>';
                  } else if (source === 'pseudo') {
                      eanTypeLabel = '<span style="color: #856404; font-size: 11px; margin-left: 8px;">🤖 <?php echo SKU_EAN_VARIANT_PSEUDO_EAN; ?></span>';
                  } else if (source === 'manual') {
                      eanTypeLabel = '<span style="color: #495057; font-size: 11px; margin-left: 8px;">✏️ <?php echo SKU_EAN_VARIANT_MANUAL; ?></span>';
                  }
                  
                  $("#bx-mpi-simple-ean-display").html(
                      '<code style="background: #d4edda; padding: 6px 12px; border-radius: 4px; font-size: 13px; font-weight: bold; color: #155724; font-family: monospace;">' +
                      response.ean +
                      '</code>' +
                      eanTypeLabel
                  );
                  
                  // "EAN entfernen" Option aktivieren
                  $("#bx-mpi-simple-ean-source option[value='release']").prop("disabled", false).text("🔄 <?php echo SKU_EAN_VARIANT_REMOVE_EAN; ?>");
                  
                  // WICHTIG: Auch products_ean Eingabefeld befüllen
                  $("input[name='products_ean']").val(response.ean);
              } else {
                  // EAN wurde entfernt
                  $("#bx-mpi-simple-ean-display").html('<span style="color: #dc3545; font-weight: bold; font-size: 13px;">✗ <?php echo SKU_EAN_VARIANT_NO_EAN_ASSIGNED; ?></span>');
                  
                  // "EAN entfernen" Option deaktivieren
                  $("#bx-mpi-simple-ean-source option[value='release']").prop("disabled", true).text("🔄 <?php echo SKU_EAN_VARIANT_REMOVE_EAN." (".SKU_EAN_VARIANT_NO_EAN_PRESENT.")"; ?>");
                  $("#bx-mpi-simple-ean-source").val("pool");
                  
                  // WICHTIG: Auch products_ean Eingabefeld leeren
                  $("input[name='products_ean']").val("");
              }
          } else {
              showSimpleMessage("✗ " + jsMessages.error_prefix + response.message, "error");
          }
          
          $btn.prop("disabled", false).html("💾 <?php echo SKU_EAN_VARIANT_ASSIGN_EAN_BUTTON; ?>");
      }).fail(function(xhr, status, error) {
          $btn.prop("disabled", false).html("💾 <?php echo SKU_EAN_VARIANT_ASSIGN_EAN_BUTTON; ?>");
          showSimpleMessage("✗ " + jsMessages.connection_error + error, "error");
      });
  });
    
  // Ausgewählte speichern
  $("#bx-mpi-save-selected").on("click", function() {
      var $btn = $(this);
      var selectedVariants = [];
      
      // Collect all selected variants
      $(".bx-mpi-variant-check:checked").each(function() {
        var $row = $(this).closest("tr");
        var rowNum = $(this).data("row");
        var variantCode = $row.data("variant-code");
        var attributeIdsStr = $row.data("attribute-ids");
        var attributeIds = typeof attributeIdsStr === 'string' ? JSON.parse(attributeIdsStr) : attributeIdsStr;
        var existingId = $row.data("existing-id");
        var eanSource = $(".bx-mpi-ean-source[data-row='" + rowNum + "']").val();
        var manualEan = $(".bx-mpi-ean-manual[data-row='" + rowNum + "']").val();
    
        selectedVariants.push({
            row: rowNum,
            code: variantCode,
            attribute_ids: attributeIds,
            existing_id: existingId,
            ean_source: eanSource,
            manual_ean: manualEan
        });
      });
      
      if (selectedVariants.length === 0) {
          showVariantMessage("⚠️ " + jsMessages.select_variant, "warning");
          return;
      }
      
      if (!confirm(jsMessages.confirm_save.replace("%d", selectedVariants.length))) {
          return;
      }
      
      $btn.prop("disabled", true).html("⏳ " + jsMessages.saving.replace("%d", selectedVariants.length));
      
      $.ajax({
          url: "<?php echo xtc_href_link(FILENAME_BX_MPI, 'ajax=1&ajax_action=save_variants'); ?>",
          type: "POST",
          dataType: "json",
          data: { 
              product_id: <?php echo $current_product_id; ?>,
              variants: JSON.stringify(selectedVariants)
              <?php if (defined('CSRF_TOKEN_SYSTEM') && CSRF_TOKEN_SYSTEM == 'true') { 
                  echo ', '.$_SESSION["CSRFName"].": '".$_SESSION["CSRFToken"]."'"; 
              } ?>
          }
      }).done(function(response) {
          if (response.success) {
              showVariantMessage("✓ " + response.saved_count + jsMessages.saving_success, "success");
              
              // DOM-Updates statt Reload
              if (response.results && response.results.length > 0) {
                  response.results.forEach(function(variant) {
                      updateVariantRow(variant);
                  });
              }
              
              // Checkboxen zurücksetzen
              $(".bx-mpi-variant-check").prop("checked", false);
              $("#bx-mpi-check-all").prop("checked", false);
              
              // Button wieder aktivieren
              $btn.prop("disabled", false).html("💾 <?php echo SKU_EAN_VARIANT_SAVE_SELECTED_BUTTON; ?>");
          } else {
              $btn.prop("disabled", false).html("💾 <?php echo SKU_EAN_VARIANT_SAVE_SELECTED_BUTTON; ?>");
              showVariantMessage("✗ " + jsMessages.error_prefix + response.message, "error");
          }
      }).fail(function(xhr, status, error) {
          $btn.prop("disabled", false).html("💾 <?php echo SKU_EAN_VARIANT_SAVE_SELECTED_BUTTON; ?>");
          showVariantMessage("✗ " + jsMessages.connection_error + error, "error");
      });
  });
    
  function updateVariantRow(variant) {
      if (!variant.row) return;
      
      var $row = $(".bx-mpi-variant-row").filter(function() {
          return $(this).find(".bx-mpi-variant-check").data("row") == variant.row;
      });
      
      if ($row.length === 0) return;
      
      // 1. identifier_id aktualisieren
      $row.attr("data-existing-id", variant.identifier_id);
      
      // 2. SKU aktualisieren
      var $skuCell = $row.find("td:eq(3)");
      $skuCell.html(
          '<code style="background: #e9ecef; padding: 4px 8px; border-radius: 3px; font-size: 12px; font-family: monospace;">' +
          variant.sku +
          '</code><br><small style="color: #28a745;">✓ <?php echo SKU_EAN_VARIANT_SAVED_STATUS; ?></small>'
      );
      
      // 3. EAN aktualisieren
      var eanHtml = '';
      if (variant.ean) {
          var eanTypeLabel = getEanTypeLabel(variant.ean_type);
          eanHtml = '<code style="background: #d4edda; padding: 4px 8px; border-radius: 3px; font-size: 12px; font-weight: bold; color: #155724; font-family: monospace;">' +
              variant.ean +
              '</code>' +
              eanTypeLabel;
      } else {
          eanHtml = '<span style="color: #dc3545; font-weight: bold; font-size: 12px;">✗ <?php echo SKU_EAN_VARIANT_NO_EAN_ASSIGNED; ?></span>';
      }
      
      $(".bx-mpi-ean-display-" + variant.row).html(eanHtml);
      
      // 4. Zeile kurz highlighten (visuelles Feedback)
      $row.css({
          "background": "#d4edda",
          "transition": "background 0.5s ease"
      });
      
      setTimeout(function() {
          $row.css("background", "");
      }, 1500);
  }
  
  function getEanTypeLabel(type) {
      var labels = {
          'pool': '<span style="color: #155724; font-size: 11px; margin-left: 8px;">🔢 <?php echo SKU_EAN_VARIANT_FROM_POOL; ?></span>',
          'pseudo': '<span style="color: #856404; font-size: 11px; margin-left: 8px;">🤖 <?php echo SKU_EAN_VARIANT_PSEUDO_EAN; ?></span>',
          'manual': '<span style="color: #495057; font-size: 11px; margin-left: 8px;">✏️ <?php echo SKU_EAN_VARIANT_MANUAL; ?></span>',
          'none': ''
      };
      return labels[type] || '';
  }
  
  function showVariantMessage(text, type) {
      var $msg = $("#bx-mpi-variant-message");
      var bgColor, borderColor, textColor;
      
      if (type === "success") {
          bgColor = "#d4edda";
          borderColor = "#c3e6cb";
          textColor = "#155724";
      } else if (type === "warning") {
          bgColor = "#fff3cd";
          borderColor = "#ffeaa7";
          textColor = "#856404";
      } else {
          bgColor = "#f8d7da";
          borderColor = "#f5c6cb";
          textColor = "#721c24";
      }
      
      $msg.css({
          "background": bgColor,
          "border": "1px solid " + borderColor,
          "color": textColor
      }).html("<strong>" + text + "</strong>").slideDown();
      
      setTimeout(function() {
          $msg.slideUp();
      }, 5000);
  }
    
  function showSimpleMessage(text, type) {
      var $msg = $("#bx-mpi-simple-message");
      var bgColor, borderColor, textColor;
      
      if (type === "success") {
          bgColor = "#d4edda";
          borderColor = "#c3e6cb";
          textColor = "#155724";
      } else if (type === "warning") {
          bgColor = "#fff3cd";
          borderColor = "#ffeaa7";
          textColor = "#856404";
      } else {
          bgColor = "#f8d7da";
          borderColor = "#f5c6cb";
          textColor = "#721c24";
      }
      
      $msg.css({
          "background": bgColor,
          "border": "1px solid " + borderColor,
          "color": textColor
      }).html("<strong>" + text + "</strong>").slideDown();
      
      setTimeout(function() {
          $msg.slideUp();
      }, 5000);
  }  
  
  // Alle bx-mpi-ean-manual Input-Felder überwachen
  // den darauf folgenden div-Tag aus countdown.js ein- / ausblenden
  // je nach Sichtbarkeit des bx-mpi-ean-manual Input-Felds
  $(".bx-mpi-ean-manual").each(function() {
    var inputField = this;
    var nextDiv = $(this).next('div')[0]; // Das direkt folgende div
    
    if (nextDiv) {
      // MutationObserver einrichten
      var observer = new MutationObserver(function(mutations) {
        mutations.forEach(function(mutation) {
          if (mutation.attributeName === "style") {
            // Input-Sichtbarkeit lesen
            var isInputVisible = $(inputField).is(':visible');
            
            // Div entsprechend setzen
            $(nextDiv).toggle(isInputVisible);
          }
        });
      });
      
      // Observer starten - überwacht style-Änderungen
      observer.observe(inputField, {
        attributes: true,
        attributeFilter: ['style']
      });
      
      // Initial-Status synchronisieren
      $(nextDiv).toggle($(inputField).is(':visible'));
    }
  });

  // Produkt-Speichern: products_ean automatisch leeren bei Varianten
  $('form[name="new_product"]').on('submit', function(e) {
    var hasAttributes = <?php echo $has_attributes ? 'true' : 'false'; ?>;
    
    // Bei Varianten MUSS products_ean leer sein - automatisch leeren
    if (hasAttributes) {
      $('input[name="products_ean"]').val('');
    }
    
    // Form normal weiter submitten (kein preventDefault!)
  });


});
</script> 
