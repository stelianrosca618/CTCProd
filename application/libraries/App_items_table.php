<?php

defined('BASEPATH') or exit('No direct script access allowed');

include_once(APPPATH . 'libraries/App_items_table_template.php');

class App_items_table extends App_items_table_template
{
    public function __construct($transaction, $type, $for = 'html', $admin_preview = false)
    {
        // Required
        $this->type          = strtolower($type);
        $this->admin_preview = $admin_preview;
        $this->for           = $for;

        $this->set_transaction($transaction);
        $this->set_items($transaction->items);

        parent::__construct();
    }

    /**
     * Builds the actual table items rows preview
     * @return string
     */
    public function items()
    {
        $html = '';


        $descriptionItemWidth = $this->get_description_item_width();

        $regularItemWidth  = $this->get_regular_items_width(6);
        $customFieldsItems = $this->get_custom_fields_for_table();

        if ($this->for == 'html') {
            $descriptionItemWidth = $descriptionItemWidth - 5;
            $regularItemWidth     = $regularItemWidth - 5;
        }

        // BOF VK, Proposal invoice items
        // VK Mod: Add
        if ($this->type == 'proposal' && isset($this->transaction->show_invoice_qty) && $this->transaction->show_invoice_qty) {
            $proposalInvoice = $this->ci->invoices_model->get($this->transaction->invoice_id);
            $proposalInvoiceItems = $proposalInvoice->items;
        } else {
            $proposalInvoiceItems = [];
        }
        // EOF VK, Proposal invoice items
        
        $i = 1;
        foreach ($this->items as $item) {
            $itemHTML = '';

            // Open table row
            $itemHTML .= '<tr' . $this->tr_attributes($item) . '>';

            if($this->type == 'proposal'){
                // Table data number
                $itemHTML .= '<td' . $this->td_attributes() . ' align="center" width="5%">' . $i . '</td>';
            }
            
            $itemHTML .= '<td class="description" align="left;" width="' . $descriptionItemWidth . '%">';

            /**
             * Item description
             */
            if (!empty($item['description'])) {
                if($this->for == "pdf"){
                    
                    $itemHTML .= '<span style="font-size:' . $this->get_pdf_font_size() . 'px;"><a href="'.$item['prod_link'].'"><strong>'
                    . $this->period_merge_field($item['description'])
                    . '</strong></a></span>';
                }else{
                    $itemHTML .= '<span style="font-size:' . $this->get_pdf_font_size() . 'px;"><strong>'
                    . $this->period_merge_field($item['description'])
                    . '</strong></span>';
                }
                

                if (!empty($item['long_description'])) {
                    $itemHTML .= '<br />';
                }
            }

            /**
             * Item long description
             */
            if (!empty($item['long_description'])) {
                $itemHTML .= '<span style="color:#424242;">' . $this->period_merge_field($item['long_description']) . '</span>';
            }

            $itemHTML .= '</td>';

            /**
             * Item custom fields
             */
            foreach ($customFieldsItems as $custom_field) {
                $itemHTML .= '<td align="left" width="' . $regularItemWidth . '%">' . get_custom_field_value($item['id'], $custom_field['id'], 'items') . '</td>';
            }

            /**
             * Item quantity
             */

            // BOF VK, Proposal invoice items
            // VK Mod: Condition
            if ($proposalInvoiceItems) {
                $invoiceItems = array_filter($proposalInvoiceItems, function ($iItem) use ($item) {
                    return ($iItem['description'] == $item['description']);
                });

                $invoiceItem = ($invoiceItems) ? array_values($invoiceItems) : array();

                if ($invoiceItem) {
                    $itemHTML .= '<td align="right" width="' . $regularItemWidth . '%">' . floatVal($invoiceItem[0]['qty']);
                } else {
                    $itemHTML .= '<td align="right" width="' . $regularItemWidth . '%">' . floatVal($item['qty']);
                }
            } else {
                $itemHTML .= '<td align="right" width="' . $regularItemWidth . '%">' . floatVal($item['qty']);
            }

            /**
             * Maybe item has added unit?
             */
            if ($item['unit']) {
                $itemHTML .= ' ' . $item['unit'];
            }

            $itemHTML .= '</td>';

            /**
             * Item rate
             * @var string
             */
            $rate = hooks()->apply_filters(
                'item_preview_rate',
                app_format_money($item['rate'], $this->transaction->currency_name, $this->exclude_currency()),
                ['item' => $item, 'transaction' => $this->transaction]
            );

            // BOF VK, Handle proposal incoterms data
            // VK Mod: Add
            if (!empty($this->transaction->items_incoterms)) {
                $filterId = $item['id'];
                $incoterms = array_filter($this->transaction->items_incoterms, function ($var) use ($filterId) {
                    return ($var['item_id'] == $filterId);
                });

                $incoterms = array_values($incoterms);
            } else {
                $incoterms = array();
            }
            
            if (!empty($this->transaction->incoterms) && !empty($this->transaction->items_incoterms) && $incoterms && isset($this->transaction->incoterms['fob_port']) && $this->transaction->incoterms['fob_port'] && isset($this->transaction->incoterms['container_type']) && $this->transaction->incoterms['container_type'] && isset($this->transaction->incoterms['ports']) && $this->transaction->incoterms['ports']) {
                foreach ($this->transaction->incoterms['fob_port'] as $fob_port) {
                    $filterPortId = $fob_port;
                    $freightIncoterm = array_filter($incoterms, function ($var) use ($filterPortId) {
                        return ($var['freight_id'] == $filterPortId);
                    });

                    $freightIncoterm = array_values($freightIncoterm);

                    $incoterm = ($freightIncoterm) ? $freightIncoterm[0] : array('rate_fob_fcl_20' => $item['rate'], 'rate_fob_fcl_40' => $item['rate'], 'rate_fob_air' => $item['rate']);

                    foreach ($this->transaction->incoterms['container_type'] as $container_type) {
                        if ($container_type == '20 FCL') {
                            $itemHTML .= '<td align="right" width="' . $regularItemWidth . '%">' . app_format_money($incoterm['rate_fob_fcl_20'], $this->transaction->currency_name, $this->exclude_currency()) . ' '. $this->transaction->currency_name .'/' . $item['unit'] . '</td>';
                        }

                        if ($container_type == '40 FCL') {
                            $itemHTML .= '<td align="right" width="' . $regularItemWidth . '%">' . app_format_money($incoterm['rate_fob_fcl_40'], $this->transaction->currency_name, $this->exclude_currency()) . ' '. $this->transaction->currency_name .'/' . $item['unit'] . '</td>';
                        }

                        if (strtolower($container_type) == 'air') {
                            $itemHTML .= '<td align="right" width="' . $regularItemWidth . '%">' . app_format_money($incoterm['rate_fob_air'], $this->transaction->currency_name, $this->exclude_currency()) . ' '. $this->transaction->currency_name .'/' . $item['unit'] . '</td>';
                        }
                    }
                }
            } elseif (empty($this->transaction->incoterms) || $this->for != 'pdf') {
                $itemHTML .= '<td align="right" width="' . $regularItemWidth . '%">' . $rate . '</td>';
            }

            if (!empty($this->transaction->incoterms) && !empty($this->transaction->items_incoterms) && $incoterms && isset($this->transaction->incoterms['cfr_port']) && $this->transaction->incoterms['cfr_port'] && isset($this->transaction->incoterms['container_type']) && $this->transaction->incoterms['container_type'] && isset($this->transaction->incoterms['ports']) && $this->transaction->incoterms['ports']) {
                foreach ($this->transaction->incoterms['cfr_port'] as $cfr_port) {
                    $filterPortId = $cfr_port;
                    $freightIncoterm = array_filter($incoterms, function ($var) use ($filterPortId) {
                        return ($var['freight_id'] == $filterPortId);
                    });

                    $freightIncoterm = array_values($freightIncoterm);

                    $incoterm = ($freightIncoterm) ? $freightIncoterm[0] : array('rate_cfr_fcl_20' => $item['rate'], 'rate_cfr_fcl_40' => $item['rate'], 'rate_cfr_air' => $item['rate']);

                    foreach ($this->transaction->incoterms['container_type'] as $container_type) {
                        if ($container_type == '20 FCL') {
                            $itemHTML .= '<td align="right" width="' . $regularItemWidth . '%">' . app_format_money($incoterm['rate_cfr_fcl_20'], $this->transaction->currency_name, $this->exclude_currency()) . ' '. $this->transaction->currency_name .'/' . $item['unit'] . '</td>';
                        }

                        if ($container_type == '40 FCL') {
                            $itemHTML .= '<td align="right" width="' . $regularItemWidth . '%">' . app_format_money($incoterm['rate_cfr_fcl_40'], $this->transaction->currency_name, $this->exclude_currency()) . ' '. $this->transaction->currency_name .'/' . $item['unit'] . '</td>';
                        }

                        if (strtolower($container_type) == 'air') {
                            $itemHTML .= '<td align="right" width="' . $regularItemWidth . '%">' . app_format_money($incoterm['rate_cfr_air'], $this->transaction->currency_name, $this->exclude_currency()) . ' '. $this->transaction->currency_name .'/' . $item['unit'] . '</td>';
                        }
                    }
                }
            }
            // EOF VK, Handle proposal incoterms data

            if (empty($this->transaction->incoterms)) {
                /**
                 * Items table taxes HTML custom function because it's too general for all features/options
                 * @var string
                 */
                $itemHTML .= $this->taxes_html($item, $regularItemWidth);

                /**
                 * Possible action hook user to include tax in item total amount calculated with the quantiy
                 * eq Rate * QTY + TAXES APPLIED
                 */
                $item_amount_with_quantity = hooks()->apply_filters(
                    'item_preview_amount_with_currency',
                    app_format_money(($item['qty'] * $item['rate']), $this->transaction->currency_name, $this->exclude_currency()),
                    $item,
                    $this->transaction,
                    $this->exclude_currency()
                );

                $itemHTML .= '<td class="amount" align="right" width="' . $regularItemWidth . '%">' . $item_amount_with_quantity . '</td>';
            }

            // Close table row
            $itemHTML .= '</tr>';

            $html .= $itemHTML;

            $i++;
        }

        return $html;
    }

    /**
     * Html headings preview
     * @return string
     */
    public function html_headings()
    {
        $html = '<tr>';
        if($this->type == 'proposal'){
            $html .= '<th align="center">' . $this->number_heading() . '</th>';
        }
        
        $html .= '<th class="description" width="' . $this->get_description_item_width() . '%" align="left">' . $this->item_heading() . '</th>';

        $customFieldsItems = $this->get_custom_fields_for_table();
        foreach ($customFieldsItems as $cf) {
            $html .= '<th class="custom_field" align="left">' . $cf['name'] . '</th>';
        }

        // BOF VK, Proposal invoice items
        // VK Mod: Condition
        if ($this->type == 'proposal' && isset($this->transaction->show_invoice_qty) && $this->transaction->show_invoice_qty) {
            $html .= '<th align="right">' . $this->qty_heading() . ' Purchased</th>';
        } else {
            $html .= '<th align="right">' . $this->qty_heading() . '</th>';
        }
        // EOF VK, Proposal invoice items
        
        // BOF VK, Handle proposal incoterms data
        // VK Mod: Add
        if (!empty($this->transaction->incoterms) && isset($this->transaction->incoterms['fob_port']) && $this->transaction->incoterms['fob_port'] && isset($this->transaction->incoterms['container_type']) && $this->transaction->incoterms['container_type'] && isset($this->transaction->incoterms['ports']) && $this->transaction->incoterms['ports']) {
            foreach ($this->transaction->incoterms['fob_port'] as $fob_port) {
                $filterId = $fob_port;
                $port = array_filter($this->transaction->incoterms['ports'], function ($var) use ($filterId) {
                    return ($var['id'] == $filterId);
                });

                $port = array_values($port);

                $port_name = ($port) ? $port[0]['name'] : 'N/A';
                foreach ($this->transaction->incoterms['container_type'] as $container_type) {
                    $prevContainer = strtolower($container_type) == 'air'? 'Destination' : $container_type;
                    $html .= '<th align="right">FOB ('.$port_name.') '.$prevContainer.'</th>';
                }
            }
        } else {
            $html .= '<th align="right">' . $this->rate_heading() . '</th>';
        }

        if (!empty($this->transaction->incoterms) && isset($this->transaction->incoterms['cfr_port']) && $this->transaction->incoterms['cfr_port'] && isset($this->transaction->incoterms['container_type']) && $this->transaction->incoterms['container_type'] && isset($this->transaction->incoterms['ports']) && $this->transaction->incoterms['ports']) {
            foreach ($this->transaction->incoterms['cfr_port'] as $cfr_port) {
                $filterId = $cfr_port;
                $port = array_filter($this->transaction->incoterms['ports'], function ($var) use ($filterId) {
                    return ($var['id'] == $filterId);
                });

                $port = array_values($port);

                $port_name = ($port) ? $port[0]['name'] : 'N/A';
                foreach ($this->transaction->incoterms['container_type'] as $container_type) {
                    $prevContainer = strtolower($container_type) == 'air'? 'Destination' : $container_type;
                    $html .= '<th align="right">CFR ('.$port_name.') '.$prevContainer.'</th>';
                }
            }            
        }
        // EOF VK, Handle proposal incoterms data 
        
        if ($this->show_tax_per_item()) {
            $html .= '<th align="right">' . $this->tax_heading() . '</th>';
        }
        if (empty($this->transaction->incoterms)) {
            $html .= '<th align="right">' . $this->amount_heading() . '</th>';
        }
        $html .= '</tr>';

        return $html;
    }

    /**
     * PDF headings preview
     * @return string
     */
    public function pdf_headings()
    {
        $descriptionItemWidth = $this->get_description_item_width();
        $regularItemWidth     = $this->get_regular_items_width(6);
        $customFieldsItems    = $this->get_custom_fields_for_table();

        $tblhtml = '<tr height="30" bgcolor="' . get_option('pdf_table_heading_color') . '" style="color:' . get_option('pdf_table_heading_text_color') . ';">';

        //$tblhtml .= '<th width="5%;" align="center">' . $this->number_heading() . '</th>';
        $tblhtml .= '<th width="' . $descriptionItemWidth . '%" align="left">Product</th>';

        foreach ($customFieldsItems as $cf) {
            $tblhtml .= '<th width="' . $regularItemWidth . '%" align="left">' . $cf['name'] . '</th>';
        }

        $tblhtml .= '<th width="' . $regularItemWidth . '%" align="right">' . $this->qty_heading() . '</th>';

        // BOF VK, Handle proposal incoterms data
        // VK Mod: Add
        // print_r($this->transaction);
        //     die;
        if (!empty($this->transaction->incoterms) && isset($this->transaction->incoterms['fob_port']) && $this->transaction->incoterms['fob_port'] && isset($this->transaction->incoterms['container_type']) && $this->transaction->incoterms['container_type'] && isset($this->transaction->incoterms['ports']) && $this->transaction->incoterms['ports']) {
            
            foreach ($this->transaction->incoterms['fob_port'] as $fob_port) {
                $filterId = $fob_port;
                $port = array_filter($this->transaction->incoterms['ports'], function ($var) use ($filterId) {
                    return ($var['id'] == $filterId);
                });

                $port = array_values($port);

                $port_name = ($port) ? $port[0]['name'] : 'N/A';
                foreach ($this->transaction->incoterms['container_type'] as $container_type) {
                    $prevContainer = strtolower($container_type) == 'air'? 'Destination' : $container_type;
                    $tblhtml .= '<th width="' . $regularItemWidth . '%" align="left">FOB ('.$port_name.') '.$prevContainer.'</th>';
                }
            }
        } elseif (empty($this->transaction->incoterms) || $this->for != 'pdf') {
            $tblhtml .= '<th width="' . $regularItemWidth . '%" align="right">' . $this->rate_heading() . '</th>';
        }

        if (!empty($this->transaction->incoterms) && isset($this->transaction->incoterms['cfr_port']) && $this->transaction->incoterms['cfr_port'] && isset($this->transaction->incoterms['container_type']) && $this->transaction->incoterms['container_type'] && isset($this->transaction->incoterms['ports']) && $this->transaction->incoterms['ports']) {
            foreach ($this->transaction->incoterms['cfr_port'] as $cfr_port) {
                $filterId = $cfr_port;
                $port = array_filter($this->transaction->incoterms['ports'], function ($var) use ($filterId) {
                    return ($var['id'] == $filterId);
                });

                $port = array_values($port);

                $port_name = ($port) ? $port[0]['name'] : 'N/A';
                foreach ($this->transaction->incoterms['container_type'] as $container_type) {
                    $prevContainer = strtolower($container_type) == 'air'? 'Destination' : $container_type;
                    $tblhtml .= '<th width="' . $regularItemWidth . '%" align="left">CFR ('.$port_name.') '.$prevContainer.'</th>';
                }
            }            
        }
        // EOF VK, Handle proposal incoterms data        

        // BOF VK, Remove column with incoterms data
        // VK Mod: Add
        if (empty($this->transaction->incoterms)) {
            if ($this->show_tax_per_item()) {
                $tblhtml .= '<th width="' . $regularItemWidth . '%" align="right">' . $this->tax_heading() . '</th>';
            }

            $tblhtml .= '<th width="' . $regularItemWidth . '%" align="right">' . $this->amount_heading() . '</th>';
        }
        // EOF VK, Remove column

            $tblhtml .= '</tr>';

        return $tblhtml;
    }

    /**
     * Check for period merge field for recurring invoices
     *
     * @return string
     */
    protected function period_merge_field($text)
    {
        if ($this->type != 'invoice') {
            return $text;
        }

        // Is subscription invoice
        if (!property_exists($this->transaction, 'recurring_type')) {
            return $text;
        }

        $startDate       = $this->transaction->date;
        $originalInvoice = $this->transaction->is_recurring_from ?
            $this->ci->invoices_model->get($this->transaction->is_recurring_from) :
            $this->transaction;

        if (!preg_match('/^(\d{4})-(\d{1,2})-(\d{1,2})$/', $startDate)) {
            $startDate = to_sql_date($startDate);
        }

        if ($originalInvoice->custom_recurring == 0) {
            $originalInvoice->recurring_type = 'month';
        }

        $nextDate = date('Y-m-d', strtotime(
            '+' . $originalInvoice->recurring . ' ' . strtoupper($originalInvoice->recurring_type),
            strtotime($startDate)
        ));

        return str_ireplace('{period}', _d($startDate) . ' - ' . _d(date('Y-m-d', strtotime('-1 day', strtotime($nextDate)))), $text);
    }

    protected function get_description_item_width()
    {
        $item_width = hooks()->apply_filters('item_description_td_width', 38);

        // If show item taxes is disabled in PDF we should increase the item width table heading
        return $this->show_tax_per_item() == 0 ? $item_width + 15 : $item_width;
    }

    protected function get_regular_items_width($adjustment)
    {
        $descriptionItemWidth = $this->get_description_item_width();
        $customFieldsItems    = $this->get_custom_fields_for_table();

        // BOF VK, Handle proposal incoterms data
        // VK Mod: Add
        if (!empty($this->transaction->incoterms) && ((isset($this->transaction->incoterms['fob_port']) && $this->transaction->incoterms['fob_port']) || (isset($this->transaction->incoterms['cfr_port']) && $this->transaction->incoterms['cfr_port']))) {
            // Calculate headings width, in case there are custom fields for items
            $totalheadings = (count($this->transaction->incoterms['fob_port']) > 0) ? 1 : 1;
            $totalheadings += count($customFieldsItems);
            $totalheadings += (count($this->transaction->incoterms['fob_port']) + count($this->transaction->incoterms['cfr_port'])) * count($this->transaction->incoterms['container_type']);
        } else {
            // Calculate headings width, in case there are custom fields for items
            $totalheadings = $this->show_tax_per_item() == 1 ? 4 : 3;
            $totalheadings += count($customFieldsItems);
        }
        // EOF VK, Handle proposal incoterms data

        return (100 - ($descriptionItemWidth + $adjustment)) / $totalheadings;
    }
}