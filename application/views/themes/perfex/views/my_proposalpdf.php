<?php

use app\services\utilities\Date;

defined('BASEPATH') or exit('No direct script access allowed');
$dimensions = $pdf->getPageDimensions();

$pdf_logo_url = pdf_logo_url();
$pdf->writeHTMLCell(($dimensions['wk'] - ($dimensions['rm'] + $dimensions['lm'])), '', '', '', $pdf_logo_url, 0, 1, false, true, 'L', true);

$pdf->ln(4);
// Get Y position for the separation
$y = $pdf->getY();

$proposal_info = '<div style="color:#424242;">';
    $proposal_info .= format_organization_info();
$proposal_info .= '</div>';

$pdf->writeHTMLCell(($swap == '0' ? (($dimensions['wk'] / 2) - $dimensions['rm']) : ''), '', '', ($swap == '0' ? $y : ''), $proposal_info, 0, 0, false, true, ($swap == '1' ? 'R' : 'J'), true);

$rowcount = max([$pdf->getNumLines($proposal_info, 80)]);

// Proposal to
$client_details = '<b>' . _l('proposal_to') . '</b>';
$client_details .= '<div style="color:#424242;">';
    $client_details .= format_proposal_info($proposal, 'pdf');
$client_details .= '</div>';

$pdf->writeHTMLCell(($dimensions['wk'] / 2) - $dimensions['lm'], $rowcount * 7, '', ($swap == '1' ? $y : ''), $client_details, 0, 1, false, true, ($swap == '1' ? 'J' : 'R'), true);

$pdf->ln(6);

$proposal_date = _l('proposal_date') . ': ' . _d($proposal->date);
$open_till     = '';

if (!empty($proposal->open_till)) {
    $open_till = _l('proposal_open_till') . ': ' . _d($proposal->open_till) . '<br />';
}


$project = '';
if ($proposal->project_id != '' && get_option('show_project_on_proposal') == 1) {
    $project .= _l('project') . ': ' . get_project_name_by_id($proposal->project_id) . '<br />';
}

$qty_heading = _l('estimate_table_quantity_heading', '', false);

if ($proposal->show_quantity_as == 2) {
    $qty_heading = _l($this->type . '_table_hours_heading', '', false);
} elseif ($proposal->show_quantity_as == 3) {
    $qty_heading = _l('estimate_table_quantity_heading', '', false) . '/' . _l('estimate_table_hours_heading', '', false);
}


// print_r($proposal);
// print_r('<br />');
// print_r($proposal->incoterms['cfr_port']);
// print_r('<br />');
// print_r($proposal->items_incoterms);
// print_r('<br />');
// die;
$interComeHd = $proposal->incoterms;
$itemableIntermData = $proposal->items_incoterms;
$rateThStyle = "text-align: center;";
$rateTDStyle = "text-align: center;";
$totalTDStyle = "text-align: center;";
$tableHtml = '<table style="font-size: 15px; width: 100%;">';
$tableHtml .= '<tr height="40" style="background-color: #323a45; color: white; line-height: 40px;">';
$tableHtml .= '<th height="30">   Product   </th><th style="' . $rateThStyle . '">Rate</th>';
// print_r($proposal->$incoterms);
//     die;
foreach ($interComeHd['fob_port'] as $fob_port) {
    
    $filterId = $fob_port;
    $port = array_filter($interComeHd['ports'], function ($var) use ($filterId) {
        return ($var['id'] == $filterId);
    });

    $port = array_values($port);

    $port_name = ($port) ? $port[0]['name'] : 'N/A';
    foreach ($interComeHd['container_type'] as $container_type) {
        $prevContainer = strtolower($container_type) == 'air' ? 'FOB (' . $port_name . ') Destination' : 'FOB (' . $port_name . ') '.$container_type;
        $html .= '<th align="right" style="' . $rateThStyle . '">' . $prevContainer . '</th>';
    }
}

foreach ($interComeHd['cfr_port'] as $cfr_port) {
    // print_r($cfr_port);
    // die;
    $filterId = $cfr_port;
    $port = array_filter($interComeHd['ports'], function ($var) use ($filterId) {
        return ($var['id'] == $filterId);
    });
    
    $port = array_values($port);
    
    $port_name = ($port) ? $port[0]['name'] : 'N/A';
    foreach ($interComeHd['container_type'] as $container_type) {
        $prevContainer = strtolower($container_type) == 'air' ? 'CFR (' . $port_name . ') Destination' : 'CFR (' . $port_name . ') '.$container_type;
        $tableHtml .= '<th align="right" style="' . $rateThStyle . '">' . $prevContainer . '</th>';
    }
}

$tableHtml .= '</tr>';
foreach ($proposal->items as $prodItem) {
    $tableHtml .= '<tr height="40" style="color: black; line-height: 40px;"><td height="30"><a href="' . $prodItem['prod_link'] . '">' . $prodItem['description'] . '</a></td>
    <td  style="'.$rateTDStyle.'"><span>' . $prodItem['rate'] . ' USD/mt</span></td>';
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
                    $tableHtml .= '<td style="'.$rateTDStyle.'">' . app_format_money($incoterm['rate_fob_fcl_20'], 'USD', false) . ' ' . 'USD' . '/' . $prodItem['unit'] . '</td>';
                }

                if ($container_type == '40 FCL') {
                    $tableHtml .= '<td style="'.$rateTDStyle.'">' . app_format_money($incoterm['rate_fob_fcl_40'], 'USD', false) . ' ' . 'USD' . '/' . $prodItem['unit'] . '</td>';
                }

                if (strtolower($container_type) == 'air') {
                    $tableHtml .= '<td style="'.$rateTDStyle.'">' . app_format_money($incoterm['rate_fob_air'], 'USD', false) . ' ' . 'USD' . '/' . $prodItem['unit'] . '</td>';
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
                    $tableHtml .= '<td style="'.$rateTDStyle.'">' . app_format_money($incoterm['rate_cfr_fcl_20'], 'USD', false) . ' ' . 'USD' . '/' . $prodItem['unit'] . '</td>';
                }

                if ($container_type == '40 FCL') {
                    $tableHtml .= '<td style="'.$rateTDStyle.'">' . app_format_money($incoterm['rate_cfr_fcl_40'], 'USD', false) . ' ' . 'USD' . '/' . $prodItem['unit'] . '</td>';
                }

                if (strtolower($container_type) == 'air') {
                    $tableHtml .= '<td style="'.$rateTDStyle.'">' . app_format_money($incoterm['rate_cfr_air'], 'USD', false) . ' ' . 'USD' . '/' . $prodItem['unit'] . '</td>';
                }
            }
        }
    }

    $tableHtml .= '</tr>';
}
$tableHtml .= '</table>';
$tableHtml .= '<p><i>To view packing options and the data sheet, click on the product name</i></p>';
$tableHtml .= '<p></p><p></p>';
$shipmentDate = date('F Y', strtotime($proposal->shipment_period));
$tableHtml .= '<p><b>Shipment period: </b> '.$shipmentDate.'</p>';
$tableHtml .= '<p><b>Terms: </b> 5% Perpayment, 95% cash against copy of documents</p>';
$tableHtml .= '<p><b>MOQ: </b> '.$proposal->moq.'</p>';
$tableHtml .= '<p></p><p></p>';
if(!empty($proposal->items)){
    $tableHtml .= '<p><b>Capacity per container</b></p>';
    foreach($proposal->incoterms['container_type'] as $containerItem){
       $tableHtml .=  " <p style='padding: 0px !important; margin: 0px; line-height: 1px;'><strong>".$containerItem.":</strong></p>";
       foreach($proposal->items as $itemData){
           if($containerItem == '20 FCL'){
               $tableHtml .=  "<p style='margin: 0px; padding: 0px !important; line-height: 1px;'><strong>".$itemData['description'].":</strong> ".$itemData['fcl_20_container']." ".$itemData['unit']."</p>";
           }else if($containerItem == '40 FCL'){
               $tableHtml .=  "<p style='margin: 0px; padding: 0px; line-height: 1px;'><strong>".$itemData['description'].":</strong> ".$itemData['fcl_40_container']." ".$itemData['unit']."</p>";
           }
        }
       
    }
}   
   
// $items = get_items_table_data($proposal, 'proposal', 'pdf')
//         ->set_headings('estimate');
// $items_html = $tableHtml.'<br/>';
//$items_html .= $items->table();

// if (empty($proposal->incoterms)) {
//     $items_html .= '<br /><br />';
//     $items_html .= '';
//     $items_html .= '<table cellpadding="6" style="font-size:' . ($font_size + 4) . 'px">';

//     $items_html .= '
//     <tr>
//         <td align="right""><strong>' . _l('estimate_subtotal') . '</strong></td>
//         <td align="right">' . app_format_money($proposal->subtotal, $proposal->currency_name) . '</td>
//     </tr>';

//     if (is_sale_discount_applied($proposal)) {
//         $items_html .= '
//         <tr>
//             <td align="right"><strong>' . _l('estimate_discount');
//         if (is_sale_discount($proposal, 'percent')) {
//             $items_html .= ' (' . app_format_number($proposal->discount_percent, true) . '%)';
//         }
//         $items_html .= '</strong>';
//         $items_html .= '</td>';
//         $items_html .= '<td align="right" width="15%">-' . app_format_money($proposal->discount_total, $proposal->currency_name) . '</td>
//         </tr>';
//     }

//     foreach ($items->taxes() as $tax) {
//         $items_html .= '<tr>
//         <td align="right"><strong>' . $tax['taxname'] . ' (' . app_format_number($tax['taxrate']) . '%)' . '</strong></td>
//         <td align="right">' . app_format_money($tax['total_tax'], $proposal->currency_name) . '</td>
//     </tr>';
//     }

//     if ((int)$proposal->adjustment != 0) {
//         $items_html .= '<tr>
//         <td align="right"><strong>' . _l('estimate_adjustment') . '</strong></td>
//         <td align="right">' . app_format_money($proposal->adjustment, $proposal->currency_name) . '</td>
//     </tr>';
//     }
//     $items_html .= '
//     <tr style="background-color:#f0f0f0;">
//         <td align="right"><strong>' . _l('estimate_total') . '</strong></td>
//         <td align="right">' . app_format_money($proposal->total, $proposal->currency_name) . '</td>
//     </tr>';
//     $items_html .= '</table>';
// }

// if (get_option('total_to_words_enabled') == 1) {
//     $items_html .= '<br /><br /><br />';
//     $items_html .= '<strong style="text-align:center;">' . _l('num_word') . ': ' . $CI->numberword->convert($proposal->total, $proposal->currency_name) . '</strong>';
// }

// //$proposal->content = str_replace('{proposal_items}', $items_html, $proposal->content);
$proposal->content = str_replace('{proposal_items}', $tableHtml, $proposal->content);

// // BOF VK, Notes, MOQ & Quantity Offered
// // VK Mod: Add -->
// $proposalNMQ = '';
// if (!empty($proposal->notes) || !empty($proposal->moq) || !empty($proposal->quantity_offered)) {
//     $proposalNMQ .= '<div style="width:675px !important;">';
//     if (!empty($proposal->notes)) {
//         $proposalNMQ .= "<p><strong>Notes:</strong> $proposal->notes</p>";
//     }
    
//     if (!empty($proposal->moq)) {
//         $proposalNMQ .= "<p><strong>MOQ:</strong> $proposal->moq</p>";
//     }

//     if (!empty($proposal->quantity_offered)) {
//         $proposalNMQ .= "<p><strong>Quantity Offered:</strong> $proposal->quantity_offered</p>";
//     }
//     $proposalNMQ .= '</div>';
// }
// if(!empty($proposal->items)){
//  $proposalNMQ .= '<div style="width:675px !important;">';
//  $proposalNMQ .= '<p style="margin: 0px; padding: 0px; line-height: 1px;"><strong>Capacity per container</strong></p>';
 
//  foreach($proposal->incoterms['container_type'] as $containerItem){
//     $proposalNMQ .=  " <span style='padding: 0px !important; margin: 0px; line-height: 1px;'><strong>".$containerItem.":</strong></span><br/>";
//     foreach($proposal->items as $itemData){
//         if($containerItem == '20 FCL'){
//             $proposalNMQ .=  "<span style='margin: 0px; padding: 0px !important; line-height: 1px;'><strong>".$itemData['description'].":</strong> ".$itemData['fcl_20_container']." ".$itemData['unit']."</span><br/> ";
//         }else if($containerItem == '40 FCL'){
//             $proposalNMQ .=  "<span style='margin: 0px; padding: 0px; line-height: 1px;'><strong>".$itemData['description'].":</strong> ".$itemData['fcl_40_container']." ".$itemData['unit']."</span><br/> ";
//         }
//      }
    
//  }
 
 
// }
// EOF VK, Notes, MOQ & Quantity Offered

// Get the proposals css
// Theese lines should aways at the end of the document left side. Dont indent these lines
$html = <<<EOF

<p style="font-size:20px;"> <b>QUOTATION:</b><br/> # $number

</p>
$proposal_date
<br />
$open_till
$project
<div style="width:675px !important;">
$proposal->content
</div>
<!-- // BOF VK, Notes, MOQ & Quantity Offered
// VK Mod: Add -->
$proposalNMQ
<!-- // EOF VK, Notes, MOQ & Quantity Offered -->
EOF;

$pdf->writeHTML($html, true, false, true, false, '');
