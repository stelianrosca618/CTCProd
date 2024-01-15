<?php

defined('BASEPATH') or exit('No direct script access allowed');

$dimensions = $pdf->getPageDimensions();

$info_right_column = '';
$info_left_column  = '';

// $info_right_column .= '<span style="font-weight:bold;font-size:27px;">' . _l('invoice_pdf_heading') . '</span><br />';
// $info_right_column .= '<b style="color:#4e4e4e;"># ' . $invoice_number . '</b>';

// if (get_option('show_status_on_pdf_ei') == 1) {
//     $info_right_column .= '<br /><span style="color:rgb(' . invoice_status_color_pdf($status) . ');text-transform:uppercase;">' . format_invoice_status($status, '', false) . '</span>';
// }

if (
    $status != Invoices_model::STATUS_PAID && $status != Invoices_model::STATUS_CANCELLED && get_option('show_pay_link_to_invoice_pdf') == 1
    && found_invoice_mode($payment_modes, $invoice->id, false)
) {
    $info_right_column .= ' - <a style="color:#84c529;text-decoration:none;text-transform:uppercase;" href="' . site_url('invoice/' . $invoice->id . '/' . $invoice->hash) . '"><1b>' . _l('view_invoice_pdf_link_pay') . '</1b></a>';
}

// Add logo
$info_left_column .= pdf_logo_url();

// Write top left logo and right column info/text
pdf_multi_row($info_left_column, $info_right_column, $pdf, ($dimensions['wk'] / 2) - $dimensions['lm']);

$pdf->ln(10);

$organization_info = '<div style="color:#424242;">';

$organization_info .= format_organization_info();

$organization_info .= '</div>';

// Bill to
$invoice_info = '<b>' . _l('To') . '</b>';
$invoice_info .= '<div style="color:#424242;">';
$invoice_info .= '<b>'.$invoice->proposal_to.'</b>';
$invoice_info .= '<p></p>';
$invoice_info .= '<p>'.$invoice->iso2.'</p>';
$invoice_info .= '<span>'.$invoice->email.'</span>';
//$invoice_info .= format_customer_info($invoice, 'invoice', 'billing');
$invoice_info .= '</div>';

// ship to to
if ($invoice->include_shipping == 1 && $invoice->show_shipping_on_invoice == 1) {
    $invoice_info .= '<br /><b>' . _l('ship_to') . ':</b>';
    $invoice_info .= '<div style="color:#424242;">';
    $invoice_info .= format_customer_info($invoice, 'invoice', 'shipping');
    $invoice_info .= '</div>';
}

// $invoice_info .= '<br />' . _l('invoice_data_date') . ' ' . _d($invoice->date) . '<br />';

$invoice_info = hooks()->apply_filters('invoice_pdf_header_after_date', $invoice_info, $invoice);
// print_r($invoice);
// die;
// if (!empty($invoice->duedate)) {
//     $invoice_info .= _l('invoice_data_duedate') . ' ' . _d($invoice->duedate) . '<br />';
//     $invoice_info = hooks()->apply_filters('invoice_pdf_header_after_due_date', $invoice_info, $invoice);
// }

// if ($invoice->sale_agent && get_option('show_sale_agent_on_invoices') == 1) {
//     $invoice_info .= _l('sale_agent_string') . ': ' . get_staff_full_name($invoice->sale_agent) . '<br />';
//     $invoice_info = hooks()->apply_filters('invoice_pdf_header_after_sale_agent', $invoice_info, $invoice);
// }

// if ($invoice->project_id && get_option('show_project_on_invoice') == 1) {
//     $invoice_info .= _l('project') . ': ' . get_project_name_by_id($invoice->project_id) . '<br />';
//     $invoice_info = hooks()->apply_filters('invoice_pdf_header_after_project_name', $invoice_info, $invoice);
// }

$invoice_info = hooks()->apply_filters('invoice_pdf_header_before_custom_fields', $invoice_info, $invoice);

foreach ($pdf_custom_fields as $field) {
    $value = get_custom_field_value($invoice->id, $field['id'], 'invoice');
    if ($value == '') {
        continue;
    }
    $invoice_info .= $field['name'] . ': ' . $value . '<br />';
}

$invoice_info      = hooks()->apply_filters('invoice_pdf_header_after_custom_fields', $invoice_info, $invoice);
$organization_info = hooks()->apply_filters('invoicepdf_organization_info', $organization_info, $invoice);
$invoice_info      = hooks()->apply_filters('invoice_pdf_info', $invoice_info, $invoice);

$left_info  = $swap == '1' ? $invoice_info : $organization_info;
$right_info = $swap == '1' ? $organization_info : $invoice_info;

pdf_multi_row($left_info, $right_info, $pdf, ($dimensions['wk'] / 2) - $dimensions['lm']);

$pdf->Ln(hooks()->apply_filters('pdf_info_and_table_separator', 6));
$contractHtml = '<span style="font-size: 20px; font-weight: bold;">CONTRACT:</span><br />';
$contractHtml .= '<span style="font-size: 20px;" ># ' . format_invoice_number($invoice) . '</span><br />';
pdf_multi_row($contractHtml, '', $pdf, ($dimensions['wk'] / 2) - $dimensions['lm']);
$pdf->Ln(hooks()->apply_filters('pdf_info_and_table_separator', 3));
$invoiceDateHtml = '<span style="font-size: 16px;">Date: ' . $invoice->date . '</span>';
pdf_multi_row($invoiceDateHtml, '', $pdf, ($dimensions['wk'] / 2) - $dimensions['lm']);
$pdf->Ln(hooks()->apply_filters('pdf_info_and_table_separator', 3));
$proposalNumHtml = '<span style="font-size: 16px;">Origin: ' . format_proposal_number($invoice->number) . '</span>';
pdf_multi_row($proposalNumHtml, '', $pdf, ($dimensions['wk'] / 2) - $dimensions['lm']);
// The Table
$pdf->Ln(hooks()->apply_filters('pdf_info_and_table_separator', 6));

//The product table

//$interComeHd = get_proposal_incoterms_data($invoice->proposal_id);
$interComeHd = get_invoice_incoterms_data($invoice->id);
$itemableIntermData = get_itemable_incoterms_data($invoice->proposal_id);

// print_r($itemableIntermData);
// die;

$rateThStyle = "text-align: center; border-left: 6px solid white;";
$rateTDStyle = "text-align: center; border-left: 3px solid #7d7e7f; border-right: 3px solid #7d7e7f;";
$totalTDStyle = "text-align: center; border: 3px solid #7d7e7f; line-height: 40px;";
$tableHtml = '<table style="font-size: ' . ($font_size + 4) . 'px; width: 100%;">';
$tableHtml .= '<tr height="40" style="background-color: #323a45; color: white; line-height: 40px;">';
$tableHtml .= '<th height="30">   Product   </th>';
if(!$interComeHd){
    $tableHtml .= '<th style="' . $rateThStyle . '">Rate</th>';
}else if($interComeHd['isRate']){
    $tableHtml .= '<th style="' . $rateThStyle . '">Rate</th>';
}

$totalColSpan = 1;
$interComTotal = array();
foreach ($interComeHd['fob_port'] as $fob_port) {
    $filterId = $fob_port;
    $port = array_filter($interComeHd['ports'], function ($var) use ($filterId) {
        return ($var['id'] == $filterId);
    });

    $port = array_values($port);

    $port_name = ($port) ? $port[0]['name'] : 'N/A';
    
    foreach ($interComeHd['container_type'] as $container_type) {
  
        $prevContainer = strtolower($container_type) == 'air' ? 'Destination' : $container_type;
        $tableHtml .= '<th align="right" style="' . $rateThStyle . '">FOB (' . $port_name . ') ' . $prevContainer . '</th>';
        $totalColSpan++;
        if ($container_type == '20 FCL') {
            $interComTotal['total_fob_fcl_20_'.$fob_port] = [
                'rate' => 0,
                'qty' => 0,
            ];
        }
        if ($container_type == '40 FCL') {
            $interComTotal['total_fob_fcl_40_'.$fob_port] = [
                'rate' => 0,
                'qty' => 0,
            ];
        }
        if (strtolower($container_type) == 'air') {
            $interComTotal['total_fob_air_'.$fob_port] = [
                'rate' => 0,
                'qty' => 0,
            ];
        }
        
    }
}
foreach ($interComeHd['cfr_port'] as $cfr_port) {

    $filterId = $cfr_port;
    $port = array_filter($interComeHd['ports'], function ($var) use ($filterId) {
        return ($var['id'] == $filterId);
    });
    
    $port = array_values($port);
    
    $port_name = ($port) ? $port[0]['name'] : 'N/A';
    foreach ($interComeHd['container_type'] as $container_type) {
        $prevContainer = strtolower($container_type) == 'air'? 'Destination' : $container_type;
        $tableHtml .= '<th align="right" style="' . $rateThStyle . '">CFR (' . $port_name . ') ' . $prevContainer . '</th>';
        $totalColSpan++;
        if ($container_type == '20 FCL') {
            $interComTotal['total_cfr_fcl_20_'.$cfr_port] = [
                'rate' => 0,
                'qty' => 0,
            ];
        }
        if ($container_type == '40 FCL') {
            $interComTotal['total_cfr_fcl_40_'.$cfr_port] = [
                'rate' => 0,
                'qty' => 0,
            ];
        }
        if (strtolower($container_type) == 'air') {
            $interComTotal['total_cfr_air_'.$cfr_port] = [
                'rate' => 0,
                'qty' => 0,
            ];
        }
    }
}
// print_r($interComTotal);
// die;
$totalRateVal = 0;
$totalRateQty = 0.00;
$tableHtml .= '</tr>';
// print_r($interComeHd);
// die;
foreach ($invoice->items as $prodItem) {
    $tableHtml .= '<tr height="40" style="color: black; line-height: 40px;"><td height="30"><a href="' . $prodItem['prod_link'] . '">' . $prodItem['description'] . '</a></td>';
    if(!$interComeHd){
        $tableHtml .= '<td  style="'.$rateTDStyle.'"><span>' . $prodItem['qty'] . 'mt - ' . $prodItem['rate'] . ' USD/mt</span></td>';
    }else if($interComeHd['isRate']){
        $tableHtml .= '<td  style="'.$rateTDStyle.'"><span>' . $prodItem['qty'] . 'mt - ' . $prodItem['rate'] . ' USD/mt</span></td>';
    }
    

    $totalRateVal = $totalRateVal + (float)$prodItem['rate'];
    $totalRateQty = (float)$totalRateQty + (float)$prodItem['qty'];
    if ($interComeHd) {
        if (!empty($itemableIntermData)) {
            $filterId = $prodItem['id'];
            $incoterms = array_filter($itemableIntermData, function ($var) use ($filterId) {
                return ($var['item_id'] == $filterId);
            });
            $incoterms = array_values($incoterms);
        } else {
            $incoterms = array();
        }
        
        
        foreach ($interComeHd['fob_port'] as $fob_port) {
            $filterPortId = $fob_port;
            $freightIncoterm = array_filter($incoterms, function ($var) use ($filterPortId) {
                return ($var['freight_id'] == $filterPortId);
            });

            $freightIncoterm = array_values($freightIncoterm);

            $incoterm = ($freightIncoterm) ? $freightIncoterm[0] : array('rate_fob_fcl_20' => $prodItem['rate'], 'rate_fob_fcl_40' => $prodItem['rate'], 'rate_fob_air' => $prodItem['rate']);

            foreach ($interComeHd['container_type'] as $container_type) {
                if ($container_type == '20 FCL') {
                    $tableHtml .= '<td style="'.$rateTDStyle.'">' . $prodItem['qty'] . ' mt - ' . app_format_money($incoterm['rate_fob_fcl_20'], 'USD', false) . ' ' . 'USD' . '/' . $prodItem['unit'] . '</td>';
                    $interComTotal['total_fob_fcl_20_'.$fob_port] = [
                        'rate' => $interComTotal['total_fob_fcl_20_'.$fob_port]['rate'] + (float)$incoterm['rate_fob_fcl_20'],
                        'qty' => $interComTotal['total_fob_fcl_20_'.$fob_port]['qty'] + (float)$incoterm['qty_fob_fcl_20'],
                    ];
                }

                if ($container_type == '40 FCL') {
                    $tableHtml .= '<td style="'.$rateTDStyle.'">' . $prodItem['qty'] . ' mt - ' . app_format_money($incoterm['rate_fob_fcl_40'], 'USD', false) . ' ' . 'USD' . '/' . $prodItem['unit'] . '</td>';
                    $interComTotal['total_fob_fcl_40_'.$fob_port] = [
                        'rate' => $interComTotal['total_fob_fcl_40_'.$fob_port]['rate'] + (float)$incoterm['rate_fob_fcl_40'],
                        'qty' => $interComTotal['total_fob_fcl_40_'.$fob_port]['qty'] + (float)$incoterm['qty_fob_fcl_40'],
                    ];
                }

                if (strtolower($container_type) == 'air') {
                    $tableHtml .= '<td style="'.$rateTDStyle.'">' . $prodItem['qty'] . ' mt - ' . app_format_money($incoterm['rate_fob_air'], 'USD', false) . ' ' . 'USD' . '/' . $prodItem['unit'] . '</td>';
                    $interComTotal['total_fob_air_'.$fob_port] = [
                        'rate' => $interComTotal['total_fob_air_'.$fob_port]['rate'] + (float)$incoterm['rate_fob_air'],
                        'qty' => $interComTotal['total_fob_air_'.$fob_port]['qty'] + (float)$incoterm['qty_fob_air'],
                    ];
                }
            }
        }
        foreach ($interComeHd['cfr_port'] as $cfr_port) {

            $filterPortId = $cfr_port;
            $freightIncoterm = array_filter($incoterms, function ($var) use ($filterPortId) {
                return ($var['freight_id'] == $filterPortId);
            });

            $freightIncoterm = array_values($freightIncoterm);
            $incoterm = ($freightIncoterm) ? $freightIncoterm[0] : array('rate_cfr_fcl_20' => $prodItem['rate'], 'rate_cfr_fcl_40' => $prodItem['rate'], 'rate_cfr_air' => $prodItem['rate']);

            foreach ($interComeHd['container_type'] as $container_type) {
                if ($container_type == '20 FCL') {
                    $tableHtml .= '<td style="'.$rateTDStyle.'">' . $incoterm['qty_cfr_fcl_20'] . ' mt - ' . app_format_money($incoterm['rate_cfr_fcl_20'], 'USD', false) . ' ' . 'USD' . '/' . $prodItem['unit'] . '</td>';
                    $interComTotal['total_cfr_fcl_20_'.$cfr_port] = [
                        'rate' => $interComTotal['total_cfr_fcl_20_'.$cfr_port]['rate'] + (float)$incoterm['rate_cfr_fcl_20'],
                        'qty' => $interComTotal['total_cfr_fcl_20_'.$cfr_port]['qty'] + (float)$incoterm['qty_cfr_fcl_20'],
                    ];
                }

                if ($container_type == '40 FCL') {
                    $tableHtml .= '<td style="'.$rateTDStyle.'">' . $incoterm['qty_cfr_fcl_40'] . ' mt - ' . app_format_money($incoterm['rate_cfr_fcl_40'], 'USD', false) . ' ' . 'USD' . '/' . $prodItem['unit'] . '</td>';
                    $interComTotal['total_cfr_fcl_40_'.$cfr_port] = [
                        'rate' => $interComTotal['total_cfr_fcl_40_'.$cfr_port]['rate'] + (float)$incoterm['rate_cfr_fcl_40'],
                        'qty' => $interComTotal['total_cfr_fcl_40_'.$cfr_port]['qty'] + (float)$incoterm['qty_cfr_fcl_40'],
                    ];
                }

                if (strtolower($container_type) == 'air') {
                    $tableHtml .= '<td style="'.$rateTDStyle.'">' . $incoterm['qty_cfr_air'] . ' mt - ' . app_format_money($incoterm['rate_cfr_air'], 'USD', false) . ' ' . 'USD' . '/' . $prodItem['unit'] . '</td>';
                    $interComTotal['total_cfr_air_'.$cfr_port] = [
                        'rate' => $interComTotal['total_cfr_air_'.$cfr_port]['rate'] + (float)$incoterm['rate_cfr_air'],
                        'qty' => $interComTotal['total_cfr_air_'.$cfr_port]['qty'] + (float)$incoterm['qty_cfr_air'],
                    ];
                }
            }
        }
    }

    $tableHtml .= '</tr>';
}
/* subtotal */
$ivTotalRate = 0;
$ivTotalQty = 0;
$tableHtml .= '<tr><td></td>';
$tableHtml .= '<td style="'.$rateTDStyle.'"></td>';
foreach($interComTotal as $comTotal){
    $tableHtml .= '<td style="'.$rateTDStyle.'"></td>';
}
$tableHtml .= '</tr>';
$tableHtml .= '<tr><td></td>';
$tableHtml .= '<td style="'.$totalTDStyle.'"><span>' . $totalRateQty . '.00 mt - ' . $totalRateVal . ' USD/mt</span></td>';
// $ivTotalRate += (float)$totalRateVal;
// $ivTotalQty += (float)$totalRateQty;
foreach($interComTotal as $comTotal){
    $tableHtml .= '<td style="'.$totalTDStyle.'"><span>' . $comTotal['qty'] . '.00 mt - ' . $comTotal['rate'] . ' USD/mt</span></td>';
    $ivTotalRate += (float)$comTotal['rate'];
    $ivTotalQty += (float)$comTotal['qty'];
}
$tableHtml .= '</tr>';
$tableHtml .= '<tr><td></td><td colspan="'.$totalColSpan.'" style="text-align: center; border: 3px solid black; line-height: 50px;"><b>Total: <span>'.$ivTotalQty.' mt - '.$ivTotalRate.' USD</span></b></td></tr>';
$tableHtml .= '</table>';
$pdf->writeHTML($tableHtml, true, false, false, false, '');

// The items table
// $items = get_items_table_data($invoice, 'invoice', 'pdf');

// $tblhtml = $items->table();

// $pdf->writeHTML($tblhtml, true, false, false, false, '');

$pdf->Ln(8);


// $tbltotal = '';
// $tbltotal .= '<table cellpadding="6" style="font-size:' . ($font_size + 4) . 'px">';
// $tbltotal .= '
// <tr>
//     <td align="right" width="85%"><strong>' . _l('invoice_subtotal') . '</strong></td>
//     <td align="right" width="15%">' . app_format_money($invoice->subtotal, $invoice->currency_name) . '</td>
// </tr>';

// if (is_sale_discount_applied($invoice)) {
//     $tbltotal .= '
//     <tr>
//         <td align="right" width="85%"><strong>' . _l('invoice_discount');
//     if (is_sale_discount($invoice, 'percent')) {
//         $tbltotal .= ' (' . app_format_number($invoice->discount_percent, true) . '%)';
//     }
//     $tbltotal .= '</strong>';
//     $tbltotal .= '</td>';
//     $tbltotal .= '<td align="right" width="15%">-' . app_format_money($invoice->discount_total, $invoice->currency_name) . '</td>
//     </tr>';
// }

// foreach ($items->taxes() as $tax) {
//     $tbltotal .= '<tr>
//         <td align="right" width="85%"><strong>' . $tax['taxname'] . ' (' . app_format_number($tax['taxrate']) . '%)' . '</strong></td>
//         <td align="right" width="15%">' . app_format_money($tax['total_tax'], $invoice->currency_name) . '</td>
//     </tr>';
// }

// if ((int) $invoice->adjustment != 0) {
//     $tbltotal .= '<tr>
//         <td align="right" width="85%"><strong>' . _l('invoice_adjustment') . '</strong></td>
//         <td align="right" width="15%">' . app_format_money($invoice->adjustment, $invoice->currency_name) . '</td>
//     </tr>';
// }

// $tbltotal .= '
// <tr style="background-color:#f0f0f0;">
//     <td align="right" width="85%"><strong>' . _l('invoice_total') . '</strong></td>
//     <td align="right" width="15%">' . app_format_money($invoice->total, $invoice->currency_name) . '</td>
// </tr>';

// if (count($invoice->payments) > 0 && get_option('show_total_paid_on_invoice') == 1) {
//     $tbltotal .= '
//     <tr>
//         <td align="right" width="85%"><strong>' . _l('invoice_total_paid') . '</strong></td>
//         <td align="right" width="15%">-' . app_format_money(sum_from_table(db_prefix() . 'invoicepaymentrecords', [
//         'field' => 'amount',
//         'where' => [
//             'invoiceid' => $invoice->id,
//         ],
//     ]), $invoice->currency_name) . '</td>
//     </tr>';
// }

// if (get_option('show_credits_applied_on_invoice') == 1 && $credits_applied = total_credits_applied_to_invoice($invoice->id)) {
//     $tbltotal .= '
//     <tr>
//         <td align="right" width="85%"><strong>' . _l('applied_credits') . '</strong></td>
//         <td align="right" width="15%">-' . app_format_money($credits_applied, $invoice->currency_name) . '</td>
//     </tr>';
// }

// if (get_option('show_amount_due_on_invoice') == 1 && $invoice->status != Invoices_model::STATUS_CANCELLED) {
//     $tbltotal .= '<tr style="background-color:#f0f0f0;">
//        <td align="right" width="85%"><strong>' . _l('invoice_amount_due') . '</strong></td>
//        <td align="right" width="15%">' . app_format_money($invoice->total_left_to_pay, $invoice->currency_name) . '</td>
//    </tr>';
// }

// $tbltotal .= '</table>';
// $pdf->writeHTML($tbltotal, true, false, false, false, '');

if (get_option('total_to_words_enabled') == 1) {
    // Set the font bold
    $pdf->SetFont($font_name, 'B', $font_size);
    $pdf->writeHTMLCell('', '', '', '', _l('num_word') . ': ' . $CI->numberword->convert($invoice->total, $invoice->currency_name), 0, 1, false, true, 'C', true);
    // Set the font again to normal like the rest of the pdf
    $pdf->SetFont($font_name, '', $font_size);
    $pdf->Ln(4);
}

// if (count($invoice->payments) > 0 && get_option('show_transactions_on_invoice_pdf') == 1) {
//     $pdf->Ln(4);
//     $border = 'border-bottom-color:#000000;border-bottom-width:1px;border-bottom-style:solid; 1px solid black;';
//     $pdf->SetFont($font_name, 'B', $font_size);
//     $pdf->Cell(0, 0, _l('invoice_received_payments') . ':', 0, 1, 'L', 0, '', 0);
//     $pdf->SetFont($font_name, '', $font_size);
//     $pdf->Ln(4);
//     $tblhtml = '<table width="100%" bgcolor="#fff" cellspacing="0" cellpadding="5" border="0">
//         <tr height="20"  style="color:#000;border:1px solid #000;">
//             <th width="25%;" style="' . $border . '">' . _l('invoice_payments_table_number_heading') . '</th>
//             <th width="25%;" style="' . $border . '">' . _l('invoice_payments_table_mode_heading') . '</th>
//             <th width="25%;" style="' . $border . '">' . _l('invoice_payments_table_date_heading') . '</th>
//             <th width="25%;" style="' . $border . '">' . _l('invoice_payments_table_amount_heading') . '</th>
//         </tr>';
//     $tblhtml .= '<tbody>';
//     foreach ($invoice->payments as $payment) {
//         $payment_name = $payment['name'];
//         if (!empty($payment['paymentmethod'])) {
//             $payment_name .= ' - ' . $payment['paymentmethod'];
//         }
//         $tblhtml .= '
//             <tr>
//             <td>' . $payment['paymentid'] . '</td>
//             <td>' . $payment_name . '</td>
//             <td>' . _d($payment['date']) . '</td>
//             <td>' . app_format_money($payment['amount'], $invoice->currency_name) . '</td>
//             </tr>
//         ';
//     }
//     $tblhtml .= '</tbody>';
//     $tblhtml .= '</table>';
//     $pdf->writeHTML($tblhtml, true, false, false, false, '');
// }

// if (found_invoice_mode($payment_modes, $invoice->id, true, true)) {
//     $pdf->Ln(4);
//     $pdf->SetFont($font_name, 'B', $font_size);
//     $pdf->Cell(0, 0, _l('invoice_html_offline_payment') . ':', 0, 1, 'L', 0, '', 0);
//     $pdf->SetFont($font_name, '', $font_size);

//     foreach ($payment_modes as $mode) {
//         if (is_numeric($mode['id'])) {
//             if (!is_payment_mode_allowed_for_invoice($mode['id'], $invoice->id)) {
//                 continue;
//             }
//         }
//         if (isset($mode['show_on_pdf']) && $mode['show_on_pdf'] == 1) {
//             $pdf->Ln(1);
//             $pdf->Cell(0, 0, $mode['name'], 0, 1, 'L', 0, '', 0);
//             $pdf->Ln(2);
//             $pdf->writeHTMLCell('', '', '', '', $mode['description'], 0, 1, false, true, 'L', true);
//         }
//     }
// }

// if (!empty($invoice->clientnote)) {
//     $pdf->Ln(4);
//     $pdf->SetFont($font_name, 'B', $font_size);
//     $pdf->Cell(0, 0, _l('invoice_note'), 0, 1, 'L', 0, '', 0);
//     $pdf->SetFont($font_name, '', $font_size);
//     $pdf->Ln(2);
//     $pdf->writeHTMLCell('', '', '', '', $invoice->clientnote, 0, 1, false, true, 'L', true);
// }

$packProd = '<i>To view packing options and the data sheet, click on the product name.</i>';
$pdf->writeHTML($packProd, true, false, false, false, '');

$pdf->Ln(hooks()->apply_filters('pdf_info_and_table_separator', 6));
$shipmentHtml = '<span><b>Shipment period:</b> January 2024</span>';
$pdf->writeHTML($shipmentHtml, true, false, false, false, '');

$pdf->Ln(hooks()->apply_filters('pdf_info_and_table_separator', 6));
$termsHtml = '<span><b>Terms:</b> 5% Prepayment, 95% cash against copy of documents</span>';
$pdf->writeHTML($termsHtml, true, false, false, false, '');

$pdf->Ln(hooks()->apply_filters('pdf_info_and_table_separator', 6));
$bankHtml = '<span><b>Bank:</b> Text of bank</span>';
$pdf->writeHTML($bankHtml, true, false, false, false, '');

if (!empty($invoice->terms)) {
    // $termsHtml = '<span><b>Terms:</b> '.$invoice->terms.'</span>';
    // $pdf->writeHTML($termsHtml, true, false, false, false, '');
    // $pdf->Ln(4);
    // $pdf->SetFont($font_name, 'B', $font_size);
    // $pdf->Cell(0, 0, _l('terms_and_conditions') . ':', 0, 1, 'L', 0, '', 0);
    // $pdf->SetFont($font_name, '', $font_size);
    // $pdf->Ln(2);
    // $pdf->writeHTMLCell('', '', '', '', $invoice->terms, 0, 1, false, true, 'L', true);
}
