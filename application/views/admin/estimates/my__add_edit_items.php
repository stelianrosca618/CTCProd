<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div class="panel-body">
    <div class="row">
        <div class="col-md-4">
            <?php $this->load->view('admin/invoice_items/item_select'); ?>
        </div>
        <div class="col-md-8 text-right show_quantity_as_wrapper">
            <div class="mtop10">
                <span><?php echo _l('show_quantity_as'); ?></span>
                <div class="radio radio-primary radio-inline">
                    <input type="radio" value="1" id="1" name="show_quantity_as"
                        data-text="<?php echo _l('estimate_table_quantity_heading'); ?>"
                        <?php echo isset($estimate) && $estimate->show_quantity_as == 1 ? 'checked' : 'checked'; ?>>
                    <label for="1"><?php echo _l('quantity_as_qty'); ?></label>
                </div>
                <div class="radio radio-primary radio-inline">
                    <input type="radio" value="2" id="2" name="show_quantity_as"
                        data-text="<?php echo _l('estimate_table_hours_heading'); ?>"
                        <?php echo isset($estimate) && $estimate->show_quantity_as == 2 ? 'checked' : ''; ?>>
                    <label for="2"><?php echo _l('quantity_as_hours'); ?></label>
                </div>
                <div class="radio radio-primary radio-inline">
                    <input type="radio" id="3" value="3" name="show_quantity_as"
                        data-text="<?php echo _l('estimate_table_quantity_heading'); ?>/<?php echo _l('estimate_table_hours_heading'); ?>"
                        <?php echo isset($estimate) && $estimate->show_quantity_as == 3 ? 'checked' : ''; ?>>
                    <label for="3">
                        <?php echo _l('estimate_table_quantity_heading'); ?>/<?php echo _l('estimate_table_hours_heading'); ?>
                    </label>
                </div>
            </div>
        </div>
    </div>
    <div class="table-responsive">
        <!-- // BOF VK, Incoterms column
            // VK Mod: Add -->
        <!-- Freights => FOB & CFR -->
        <?php
        $th_col_fob = '';
        $th_col_cfr = '';
        $td_col_fob = '';
        $td_col_cfr = '';
        $opne_fob_ports = (isset($proposal) && !empty($proposal->incoterms)) ? $proposal->incoterms['fob_port'] : array();
        $opne_cfr_ports = (isset($proposal) && !empty($proposal->incoterms)) ? $proposal->incoterms['cfr_port'] : array();
        $opne_container_types = (isset($proposal) && !empty($proposal->incoterms)) ? $proposal->incoterms['container_type'] : array();
        foreach ($freights as $freight) {
            // TH => Heading
            $th_col_fob .= '<th class="incoterms-column incoterms-column-fob incoterms-column-fcl-20 fob-port-' . $freight['id'] . ''.((in_array($freight['id'], $opne_fob_ports) && in_array('20 FCL', $opne_container_types)) ? '' : ' hide').'" width="15%" align="right">FOB (' . $freight['port'] . ') 20\' FCL</th>';
            $th_col_fob .= '<th class="incoterms-column incoterms-column-fob incoterms-column-fcl-40 fob-port-' . $freight['id'] . ''.((in_array($freight['id'], $opne_fob_ports) && in_array('40 FCL', $opne_container_types)) ? '' : ' hide').'" width="15%" align="right">FOB (' . $freight['port'] . ') 40\' FCL</th>';
            $th_col_fob .= '<th class="incoterms-column incoterms-column-fob incoterms-column-air fob-port-' . $freight['id'] . ''.((in_array($freight['id'], $opne_fob_ports) && in_array('Air', $opne_container_types)) ? '' : ' hide').'" width="15%" align="right">FOB (' . $freight['port'] . ') Destination</th>';
            $th_col_cfr .= '<th class="incoterms-column incoterms-column-cfr incoterms-column-fcl-20 cfr-port-' . $freight['id'] . ''.((in_array($freight['id'], $opne_cfr_ports) && in_array('20 FCL', $opne_container_types)) ? '' : ' hide').'" width="15%" align="right">CFR (' . $freight['port'] . ') 20\' FCL</th>';
            $th_col_cfr .= '<th class="incoterms-column incoterms-column-cfr incoterms-column-fcl-40 cfr-port-' . $freight['id'] . ''.((in_array($freight['id'], $opne_cfr_ports) && in_array('40 FCL', $opne_container_types)) ? '' : ' hide').'" width="15%" align="right">CFR (' . $freight['port'] . ') 40\' FCL</th>';
            $th_col_cfr .= '<th class="incoterms-column incoterms-column-cfr incoterms-column-air cfr-port-' . $freight['id'] . ''.((in_array($freight['id'], $opne_cfr_ports) && in_array('Air', $opne_container_types)) ? '' : ' hide').'" width="15%" align="right">CFR (' . $freight['port'] . ') Destination</th>';
            // TD => Data
            $td_col_fob .= '<td class="incoterms-column incoterms-column-fob incoterms-column-fcl-20 fob-port-' . $freight['id'] . ''.((in_array($freight['id'], $opne_fob_ports) && in_array('20 FCL', $opne_container_types)) ? '' : ' hide').'"><input type="number" name="incoterms['. $freight['id'] .'][rate_fob_fcl_20]" class="form-control fi'. $freight['id'] .'_rate_fob_fcl_20" placeholder="' . _l('item_rate_placeholder') . '"></td>';
            $td_col_fob .= '<td class="incoterms-column incoterms-column-fob incoterms-column-fcl-40 fob-port-' . $freight['id'] . ''.((in_array($freight['id'], $opne_fob_ports) && in_array('40 FCL', $opne_container_types)) ? '' : ' hide').'"><input type="number" name="incoterms['. $freight['id'] .'][rate_fob_fcl_40]" class="form-control fi'. $freight['id'] .'_rate_fob_fcl_40" placeholder="' . _l('item_rate_placeholder') . '"></td>';
            $td_col_fob .= '<td class="incoterms-column incoterms-column-fob incoterms-column-air fob-port-' . $freight['id'] . ''.((in_array($freight['id'], $opne_fob_ports) && in_array('Air', $opne_container_types)) ? '' : ' hide').'"><input type="number" name="incoterms['. $freight['id'] .'][rate_fob_air]" class="form-control fi'. $freight['id'] .'_rate_fob_air" placeholder="' . _l('item_rate_placeholder') . '"></td>';
            $td_col_cfr .= '<td class="incoterms-column incoterms-column-cfr incoterms-column-fcl-20 cfr-port-' . $freight['id'] . ''.((in_array($freight['id'], $opne_cfr_ports) && in_array('20 FCL', $opne_container_types)) ? '' : ' hide').'"><input type="number" name="incoterms['. $freight['id'] .'][rate_cfr_fcl_20]" class="form-control fi'. $freight['id'] .'_rate_cfr_fcl_20" placeholder="' . _l('item_rate_placeholder') . '"></td>';
            $td_col_cfr .= '<td class="incoterms-column incoterms-column-cfr incoterms-column-fcl-40 cfr-port-' . $freight['id'] . ''.((in_array($freight['id'], $opne_cfr_ports) && in_array('40 FCL', $opne_container_types)) ? '' : ' hide').'"><input type="number" name="incoterms['. $freight['id'] .'][rate_cfr_fcl_40]" class="form-control fi'. $freight['id'] .'_rate_cfr_fcl_40" placeholder="' . _l('item_rate_placeholder') . '"></td>';
            $td_col_cfr .= '<td class="incoterms-column incoterms-column-cfr incoterms-column-air cfr-port-' . $freight['id'] . ''.((in_array($freight['id'], $opne_cfr_ports) && in_array('Air', $opne_container_types)) ? '' : ' hide').'"><input type="number" name="incoterms['. $freight['id'] .'][rate_cfr_air]" class="form-control fi'. $freight['id'] .'_rate_cfr_air" placeholder="' . _l('item_rate_placeholder') . '"></td>';
        }
        ?>
        <!-- // EOF VK, Incoterms column -->
        <table class="table estimate-items-table items table-main-estimate-edit has-calculations no-mtop">
            <thead>
                <tr>
                    <th></th>
                    <th width="20%" align="left"><i class="fa-solid fa-circle-exclamation tw-mr-1" aria-hidden="true"
                            data-toggle="tooltip"
                            data-title="<?php echo _l('item_description_new_lines_notice'); ?>"></i>
                        <?php echo _l('estimate_table_item_heading'); ?></th>
                    <th width="25%" align="left"><?php echo _l('estimate_table_item_description'); ?></th>
                    <?php
                  $custom_fields = get_custom_fields('items');
                  foreach ($custom_fields as $cf) {
                      echo '<th width="15%" align="left" class="custom_field">' . $cf['name'] . '</th>';
                  }

                  $qty_heading = _l('estimate_table_quantity_heading');
                  if (isset($estimate) && $estimate->show_quantity_as == 2) {
                      $qty_heading = _l('estimate_table_hours_heading');
                  } elseif (isset($estimate) && $estimate->show_quantity_as == 3) {
                      $qty_heading = _l('estimate_table_quantity_heading') . '/' . _l('estimate_table_hours_heading');
                  }
                  ?>
                    <th width="10%" class="qty" align="right"><?php echo $qty_heading; ?></th>
                    <!-- <th width="15%" class="default-rate-column<?php echo (count($opne_fob_ports) ? ' hide' : ''); ?>" align="right"><?php echo _l('estimate_table_rate_heading'); ?></th> -->
                    <th width="15%" class="default-rate-column" align="right"><?php echo _l('estimate_table_rate_heading'); ?></th>
                    <!-- // BOF VK, Incoterms column
                        // VK Mod: Add -->
                    <!-- Freights => FOB & CFR -->
                    <?php echo $th_col_fob . $th_col_cfr; ?>
                    <!-- // EOF VK, Incoterms column -->
                    <th width="20%" class="hide" align="right"><?php echo _l('estimate_table_tax_heading'); ?></th>
                    <th width="10%" class="hide" align="right"><?php echo _l('estimate_table_amount_heading'); ?></th>
                    <th align="center"><i class="fa fa-cog"></i></th>
                </tr>
            </thead>
            <tbody>
                <tr class="main">
                    <td></td>
                    <td>
                        <input type="hidden" name="reference_item_id" value="">
                        <textarea name="description" rows="4" class="form-control"
                            placeholder="<?php echo _l('item_description_placeholder'); ?>"></textarea>
                    </td>
                    <td>
                        <textarea name="long_description" rows="4" class="form-control"
                            placeholder="<?php echo _l('item_long_description_placeholder'); ?>"></textarea>
                    </td>
                    <?php echo render_custom_fields_items_table_add_edit_preview(); ?>
                    <td>
                        <input type="number" name="quantity" min="0" value="1" class="form-control"
                            placeholder="<?php echo _l('item_quantity_placeholder'); ?>">
                        <input type="text" placeholder="<?php echo _l('unit'); ?>" data-toggle="tooltip" 612
                            data-title="e.q kg, lots, packs" name="unit"
                            class="form-control input-transparent text-right">
                    </td>
                    <!-- <td class="default-rate-column<?php echo (count($opne_fob_ports) ? ' hide' : ''); ?>"> -->
                    <td class="default-rate-column">
                        <input type="number" name="rate" class="form-control"
                            placeholder="<?php echo _l('item_rate_placeholder'); ?>">
                    </td>
                    <!-- // BOF VK, Incoterms column
                        // VK Mod: Add -->
                    <!-- Freights => FOB & CFR -->
                    <?php echo $td_col_fob . $td_col_cfr; ?>
                    <!-- // EOF VK, Incoterms column -->
                    <td class="hide">
                        <?php
                     $default_tax = unserialize(get_option('default_tax'));
                     $select      = '<select class="selectpicker display-block tax main-tax" data-width="100%" name="taxname" multiple data-none-selected-text="' . _l('no_tax') . '">';
                     foreach ($taxes as $tax) {
                         $selected = '';
                         if (is_array($default_tax)) {
                             if (in_array($tax['name'] . '|' . $tax['taxrate'], $default_tax)) {
                                 $selected = ' selected ';
                             }
                         }
                         $select .= '<option value="' . $tax['name'] . '|' . $tax['taxrate'] . '"' . $selected . 'data-taxrate="' . $tax['taxrate'] . '" data-taxname="' . $tax['name'] . '" data-subtext="' . $tax['name'] . '">' . $tax['taxrate'] . '%</option>';
                     }
                     $select .= '</select>';
                     echo $select;
                     ?>
                    </td>
                    <td class="hide"></td>
                    <td>
                        <?php
                     $new_item = 'undefined';
                     if (isset($estimate)) {
                         $new_item = true;
                     }
                     ?>
                        <!-- // BOF VK, Incoterms column
                        // VK Mod: Replace -->
                        <!-- <button type="button"
                            onclick="add_item_to_table('undefined','undefined',<?php echo $new_item; ?>); return false;"
                            class="btn pull-right btn-primary"><i class="fa fa-check"></i></button> -->
                        <button type="button"
                            onclick="add_item_to_incoterms_table('undefined','undefined',<?php echo $new_item; ?>); return false;"
                            class="btn pull-right btn-primary"><i class="fa fa-check"></i></button>
                        <!-- // EOF VK, Incoterms column -->
                    </td>
                </tr>
                <?php if (isset($estimate) || isset($add_items)) {
                         $i               = 1;
                         $items_indicator = 'newitems';
                         if (isset($estimate)) {
                             $add_items       = $estimate->items;
                             $items_indicator = 'items';
                         }

                         foreach ($add_items as $item) {
                            // BOF VK, Handle proposal incoterms data
                            // VK Mod: Add
                            if (isset($proposal) && !empty($proposal->items_incoterms)) {
                                $filterId = $item['id'];
                                $incoterms = array_filter($proposal->items_incoterms, function ($var) use ($filterId) {
                                    return ($var['item_id'] == $filterId);
                                });

                                $incoterms = array_values($incoterms);
                            } else {
                                $incoterms = array();
                            }
                            $item_col_fob = '';
                            $item_col_cfr = '';

                            foreach ($freights as $freight) {
                               $filterPortId = $freight['id'];
                               $freightIncoterm = array_filter($incoterms, function ($var) use ($filterPortId) {
                                   return ($var['freight_id'] == $filterPortId);
                               });

                               $freightIncoterm = array_values($freightIncoterm);

                               $incoterm = ($freightIncoterm) ? $freightIncoterm[0] : array('rate_fob_fcl_20' => $item['rate'], 'rate_fob_fcl_40' => $item['rate'], 'rate_fob_air' => $item['rate'], 'rate_cfr_fcl_20' => $item['rate'], 'rate_cfr_fcl_40' => $item['rate'], 'rate_cfr_air' => $item['rate']);

                               $item_col_fob .= '<td class="incoterms-column incoterms-column-fob incoterms-column-fcl-20 fob-port-' . $freight['id'] . ''.((in_array($freight['id'], $opne_fob_ports) && in_array('20 FCL', $opne_container_types)) ? '' : ' hide').'"><input type="number" name="' . $items_indicator . '[' . $i . '][incoterms]['. $freight['id'] .'][rate_fob_fcl_20]" class="form-control fi'. $freight['id'] .'_rate_fob_fcl_20" placeholder="' . _l('item_rate_placeholder') . '" value="' .$incoterm['rate_fob_fcl_20']. '"></td>';
                               $item_col_fob .= '<td class="incoterms-column incoterms-column-fob incoterms-column-fcl-40 fob-port-' . $freight['id'] . ''.((in_array($freight['id'], $opne_fob_ports) && in_array('40 FCL', $opne_container_types)) ? '' : ' hide').'"><input type="number" name="' . $items_indicator . '[' . $i . '][incoterms]['. $freight['id'] .'][rate_fob_fcl_40]" class="form-control fi'. $freight['id'] .'_rate_fob_fcl_40" placeholder="' . _l('item_rate_placeholder') . '" value="' .$incoterm['rate_fob_fcl_40']. '"></td>';
                               $item_col_fob .= '<td class="incoterms-column incoterms-column-fob incoterms-column-air fob-port-' . $freight['id'] . ''.((in_array($freight['id'], $opne_fob_ports) && in_array('Air', $opne_container_types)) ? '' : ' hide').'"><input type="number" name="' . $items_indicator . '[' . $i . '][incoterms]['. $freight['id'] .'][rate_fob_air]" class="form-control fi'. $freight['id'] .'_rate_fob_air" placeholder="' . _l('item_rate_placeholder') . '" value="' .$incoterm['rate_fob_air']. '"></td>';
                               $item_col_cfr .= '<td class="incoterms-column incoterms-column-cfr incoterms-column-fcl-20 cfr-port-' . $freight['id'] . ''.((in_array($freight['id'], $opne_cfr_ports) && in_array('20 FCL', $opne_container_types)) ? '' : ' hide').'"><input type="number" name="' . $items_indicator . '[' . $i . '][incoterms]['. $freight['id'] .'][rate_cfr_fcl_20]" class="form-control fi'. $freight['id'] .'_rate_cfr_fcl_20" placeholder="' . _l('item_rate_placeholder') . '" value="' .$incoterm['rate_cfr_fcl_20']. '"></td>';
                               $item_col_cfr .= '<td class="incoterms-column incoterms-column-cfr incoterms-column-fcl-40 cfr-port-' . $freight['id'] . ''.((in_array($freight['id'], $opne_cfr_ports) && in_array('40 FCL', $opne_container_types)) ? '' : ' hide').'"><input type="number" name="' . $items_indicator . '[' . $i . '][incoterms]['. $freight['id'] .'][rate_cfr_fcl_40]" class="form-control fi'. $freight['id'] .'_rate_cfr_fcl_40" placeholder="' . _l('item_rate_placeholder') . '" value="' .$incoterm['rate_cfr_fcl_40']. '"></td>';
                               $item_col_cfr .= '<td class="incoterms-column incoterms-column-cfr incoterms-column-air cfr-port-' . $freight['id'] . ''.((in_array($freight['id'], $opne_cfr_ports) && in_array('Air', $opne_container_types)) ? '' : ' hide').'"><input type="number" name="' . $items_indicator . '[' . $i . '][incoterms]['. $freight['id'] .'][rate_cfr_air]" class="form-control fi'. $freight['id'] .'_rate_cfr_air" placeholder="' . _l('item_rate_placeholder') . '" value="' .$incoterm['rate_cfr_air']. '"></td>';
                            }

                             $manual    = false;
                             $table_row = '<tr class="sortable item">';
                             $table_row .= '<td class="dragger">';
                             if ($item['qty'] == '' || $item['qty'] == 0) {
                                 $item['qty'] = 1;
                             }
                             if (!isset($is_proposal)) {
                                 $estimate_item_taxes = get_estimate_item_taxes($item['id']);
                             } else {
                                 $estimate_item_taxes = get_proposal_item_taxes($item['id']);
                             }
                             if ($item['id'] == 0) {
                                 $estimate_item_taxes = $item['taxname'];
                                 $manual              = true;
                             }
                             $table_row .= form_hidden('' . $items_indicator . '[' . $i . '][itemid]', $item['id']);
                             $amount = $item['rate'] * $item['qty'];
                             $amount = app_format_number($amount);
                             // order input
                             $table_row .= '<input type="hidden" class="order" name="' . $items_indicator . '[' . $i . '][order]">';
                             $table_row .= '</td>';
                             $table_row .= '<td class="bold description"><input type="hidden" name="' . $items_indicator . '[' . $i . '][reference_item_id]" value=" ' . $item['reference_item_id'] . ' "><textarea name="' . $items_indicator . '[' . $i . '][description]" class="form-control" rows="5">' . clear_textarea_breaks($item['description']) . '</textarea></td>';
                             $table_row .= '<td><textarea name="' . $items_indicator . '[' . $i . '][long_description]" class="form-control" rows="5">' . clear_textarea_breaks($item['long_description']) . '</textarea></td>';
                             $table_row .= render_custom_fields_items_table_in($item, $items_indicator . '[' . $i . ']');
                             $table_row .= '<td><input type="number" min="0" onblur="calculate_total();" onchange="calculate_total();" data-quantity name="' . $items_indicator . '[' . $i . '][qty]" value="' . $item['qty'] . '" class="form-control">';
                             $unit_placeholder = '';
                             if (!$item['unit']) {
                                 $unit_placeholder = _l('unit');
                                 $item['unit']     = '';
                             }
                             $table_row .= '<input type="text" placeholder="' . $unit_placeholder . '" name="' . $items_indicator . '[' . $i . '][unit]" class="form-control input-transparent text-right" value="' . $item['unit'] . '">';
                             $table_row .= '</td>';
                             //$table_row .= '<td class="default-rate-column rate'.(count($opne_fob_ports) ? ' hide' : '').'"><input type="number" data-toggle="tooltip" title="' . _l('numbers_not_formatted_while_editing') . '" onblur="calculate_total();" onchange="calculate_total();" name="' . $items_indicator . '[' . $i . '][rate]" value="' . $item['rate'] . '" class="form-control"></td>';
                             $table_row .= '<td class="default-rate-column rate"><input type="number" data-toggle="tooltip" title="' . _l('numbers_not_formatted_while_editing') . '" onblur="calculate_total();" onchange="calculate_total();" name="' . $items_indicator . '[' . $i . '][rate]" value="' . $item['rate'] . '" class="form-control"></td>';
                             // 

                             $table_row .= $item_col_fob.$item_col_cfr;
                             // 

                             $table_row .= '<td class="taxrate hide">' . $this->misc_model->get_taxes_dropdown_template('' . $items_indicator . '[' . $i . '][taxname][]', $estimate_item_taxes, (isset($is_proposal) ? 'proposal' : 'estimate'), $item['id'], true, $manual) . '</td>';
                             $table_row .= '<td class="amount hide" align="right">' . $amount . '</td>';
                             $table_row .= '<td><a href="#" class="btn btn-danger pull-left" onclick="delete_item(this,' . $item['id'] . '); return false;"><i class="fa fa-times"></i></a></td>';
                             $table_row .= '</tr>';
                             echo $table_row;
                             $i++;
                         }
                     }
               ?>
            </tbody>
        </table>
    </div>
    <div class="col-md-8 col-md-offset-4<?php echo (isset($hide_sbutotal) ? ' hide' : ''); ?>">
        <table class="table text-right">
            <tbody>
                <tr id="subtotal">
                    <td><span class="bold tw-text-neutral-700"><?php echo _l('estimate_subtotal'); ?> :</span>
                    </td>
                    <td class="subtotal">
                    </td>
                </tr>
                <tr id="discount_area">
                    <td>
                        <div class="row">
                            <div class="col-md-7">
                                <span class="bold tw-text-neutral-700"><?php echo _l('estimate_discount'); ?></span>
                            </div>
                            <div class="col-md-5">
                                <div class="input-group" id="discount-total">

                                    <input type="number"
                                        value="<?php echo(isset($estimate) ? $estimate->discount_percent : 0); ?>"
                                        class="form-control pull-left input-discount-percent<?php if (isset($estimate) && !is_sale_discount($estimate, 'percent') && is_sale_discount_applied($estimate)) {
                   echo ' hide';
               } ?>" min="0" max="100" name="discount_percent">

                                    <input type="number" data-toggle="tooltip"
                                        data-title="<?php echo _l('numbers_not_formatted_while_editing'); ?>"
                                        value="<?php echo(isset($estimate) ? $estimate->discount_total : 0); ?>" class="form-control pull-left input-discount-fixed<?php if (!isset($estimate) || (isset($estimate) && !is_sale_discount($estimate, 'fixed'))) {
                   echo ' hide';
               } ?>" min="0" name="discount_total">

                                    <div class="input-group-addon">
                                        <div class="dropdown">
                                            <a class="dropdown-toggle" href="#" id="dropdown_menu_tax_total_type"
                                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                                                <span class="discount-total-type-selected">
                                                    <?php if (!isset($estimate) || isset($estimate) && (is_sale_discount($estimate, 'percent') || !is_sale_discount_applied($estimate))) {
                   echo '%';
               } else {
                   echo _l('discount_fixed_amount');
               }
                                    ?>
                                                </span>
                                                <span class="caret"></span>
                                            </a>
                                            <ul class="dropdown-menu" id="discount-total-type-dropdown"
                                                aria-labelledby="dropdown_menu_tax_total_type">
                                                <li>
                                                    <a href="#" class="discount-total-type discount-type-percent<?php if (!isset($estimate) || (isset($estimate) && is_sale_discount($estimate, 'percent')) || (isset($estimate) && !is_sale_discount_applied($estimate))) {
                                        echo ' selected';
                                    } ?>">%</a>
                                                </li>
                                                <li>
                                                    <a href="#" class="discount-total-type discount-type-fixed<?php if (isset($estimate) && is_sale_discount($estimate, 'fixed')) {
                                        echo ' selected';
                                    } ?>">
                                                        <?php echo _l('discount_fixed_amount'); ?>
                                                    </a>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </td>
                    <td class="discount-total"></td>
                </tr>
                <tr>
                    <td>
                        <div class="row">
                            <div class="col-md-7">
                                <span class="bold tw-text-neutral-700"><?php echo _l('estimate_adjustment'); ?></span>
                            </div>
                            <div class="col-md-5">
                                <input type="number" data-toggle="tooltip"
                                    data-title="<?php echo _l('numbers_not_formatted_while_editing'); ?>" value="<?php if (isset($estimate)) {
                                        echo $estimate->adjustment;
                                    } else {
                                        echo 0;
                                    } ?>" class="form-control pull-left" name="adjustment">
                            </div>
                        </div>
                    </td>
                    <td class="adjustment"></td>
                </tr>
                <tr>
                    <td><span class="bold tw-text-neutral-700"><?php echo _l('estimate_total'); ?> :</span>
                    </td>
                    <td class="total">
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
    <div id="removed-items"></div>
</div>