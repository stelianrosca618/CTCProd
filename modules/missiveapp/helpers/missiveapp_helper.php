<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * @since  1.0.0
 * Init admin head
 * @param  boolean $aside should include aside
 */
function init_missiveapp_head($aside = true)
{
    $CI = &get_instance();
    $CI->load->view('missiveapp_head');
}

function init_missiveapp_footer($aside = true)
{
    $CI = &get_instance();
    $CI->load->view('missiveapp_footer');
}

function sales_tables_quotations($quotations)
{   $stateCheckBox = '<input type="checkbox" class="seles-invoiced-checked" checked readonly/>';
    $tableHtml ='';
    
    //$tableHtml = '<div class="table-responsive table-responsive-sales"><table class="table table-striped table-proposal"><thead><tr><th>Proposal #</th><th>Date</th><th>Open Till</th><th>Status</th></tr></thead><tbody>';

    if ($quotations) {
        foreach ($quotations as $quotation) {
            $newDate = date('Y-m-d');
            $openTillDate = date($quotation['open_till']);
            if($quotation['invoice_id']){
                $stateCheckBox = '<input type="checkbox" class="seles-invoiced-checked" checked readonly/>';
            }else if($newDate > $openTillDate){
                $stateCheckBox = '<input type="checkbox" class="seles-passed-checked" checked readonly/>';
            }else{
                $stateCheckBox = '<input type="checkbox" class="seles-default-checked" checked readonly/>';
            }
           $tableHtml .= '<div class="seles-card-row">' . $stateCheckBox . '<span>' . _d($quotation['date']) .' - '._d($quotation['open_till']).' - '. format_proposal_number($quotation['id']) .' - (<a class="proposal-copy" id="'.$quotation['id'].'">Copy</a>-<a class="proposal-view" data-hash="'.$quotation['hash'].'" id="'.$quotation['id'].'">View</a>-<a class="proposal-edit" id="'.$quotation['id'].'">Edit</a>)</span></div>';
            //$tableHtml .= '<tr><td>' . format_proposal_number($quotation['id']) . '</td><td>' . _d($quotation['date']) . '</td><td>' . _d($quotation['open_till']) . '</td><td>' . format_proposal_status($quotation['status']) . '</td></tr>';
        }
    } else {
        $tableHtml .= '<div class="seles-card-row text-center"> Empty </div>';
    }

   // $tableHtml .= '</tbody></table></div>';

    return $tableHtml;
}

function getProductsMonths($data) {
    $groups = array();
    foreach ($data as $record) {
        if(isset($groups[$record['description']])) {
            $groups[$record['description']]['qty'] += $record['Quantity'];
        } else $groups[$record['description']] = [
            'discription' => $record['description'],
            'qty' => $record['Quantity']
        ];
    }
    $productStr = '';
    $indexer = 0;
    usort($groups, function($a, $b) {return ($a['qty'] < $b['qty']);});
    foreach ($groups as $group){
        if($indexer > 0){
            $productStr .= '+ ';
        }
        $productStr .= $group['discription'];
        $productStr .= "(".$group['qty'].") ";
        $indexer++;
    }
    return $productStr;
}

function sales_tables_invoices($invoices)
{
//    $tableHtml = '<div class="table-responsive table-responsive-sales"><table class="table table-striped table-proposal"><thead><tr><th>Invoice #</th><th>Amount</th><th>Due Date</th><th>Status</th></tr></thead><tbody>';
    $tableHtml = '';
    if ($invoices) {
        foreach ($invoices as $invoice) {
            //$tableHtml .= '<tr><td>' . format_invoice_number($invoice['id']) . '</td><td>' . app_format_money($invoice['total'], $invoice['currency_name']) . '</td><td>' . _d($invoice['duedate']) . '</td><td>' . format_invoice_status($invoice['status']) . '</td></tr>';
            $tableHtml .= '<div class="seles-card-row"><span>'._d($invoice['duedate']).' - '._d($invoice['date']).' - '. format_invoice_number($invoice['id']) .'</span> - (<a class="contract-view" data-hash="'.$invoice['hash'].'" id="'.$invoice['id'].'">View</a>-<a class="contract-edit" id="'.$invoice['id'].'">Edit</a>)</div>';
        }
    } else {
        $tableHtml .= '<div class="seles-card-row">empty</div>';
    }

    //$tableHtml .= '</tbody></table></div>';

    return $tableHtml;
}

function sales_tables_consumption($consumption)
{
    //$tableHtml = '<div class="table-responsive table-responsive-sales"><table class="table table-striped table-proposal"><thead><tr><th>Year #</th><th>Qty</th></tr></thead><tbody>';
    $tableHtml = '';
    if($consumption){
        if($consumption['lastQuantity']){
            $tableHtml .= '<div class="contact-data-row"><div class="data-Name">LAST 12M</div><div class="data-value">'.$consumption['lastQuantity'].' mt</div></div>';
        }
        if($consumption['qtyPerYear']){
            $tableHtml .= '<div class="contact-data-row"><div class="data-Name">PER YEAR</div><div class="data-value">'.$consumption['qtyPerYear'].' mt</div></div>';
        }
        if ($consumption['consureData']) {
            foreach ($consumption['consureData'] as $consumption_row) {
                $tableHtml .= '<div class="contact-data-row"><div class="data-Name">'. $consumption_row['date_year'] .'</div><div class="data-value">' . (int)$consumption_row['total_qty'] . ' mt</div></div>';
            }
        }
        if($consumption['lastProducts']){
            $tableHtml .= '<div class="contact-data-row align-start" ><div class="data-Name">Products 12m</div><div class="data-value">'.$consumption['lastProducts'].'</div></div>';
        }
    }
    // if ($consumption['consureData']) {
    //     foreach ($consumption['consureData'] as $consumption_row) {
    //         $tableHtml .= '<tr><td>' . $consumption_row['date_year'] . '</td><td>' . (int)$consumption_row['total_qty'] . ' mt</td></tr>';
    //     }
    // } else {
    //     $tableHtml .= '<tr><td class="text-center" colspan="2">Empty</td></tr>';
    // }

  //  $tableHtml .= '</tbody></table></div>';

    return $tableHtml;
}

function sales_payemnets($payments)
{
    $tableHtml = '<div class="contact-data-row"><div class="data-Name"  style="width: 50%">TO BE PAID</div><div class="data-value"  style="width: 50%">' . app_format_money($payments['pending'], $payments['currency']) . '</div></div><div class="contact-data-row"><div class="data-Name" style="width: 50%">OVERDUE AMOUNT</div><div class="data-value" style="color: red; width: 50%;">' . app_format_money($payments['overdue'], $payments['currency']) . '</div></div>';
    //$tableHtml = '<div class="list-items light-box"><div class="list-item padding-small"><div class="columns-middle"><span>Pending within terms: ' . app_format_money($payments['pending'], $payments['currency']) . '</span></div></div><div class="list-item padding-small"><div class="columns-middle"><span>Overdue amount: ' . app_format_money($payments['overdue'], $payments['currency']) . '</span></div></div><div class="list-item padding-small"><div class="columns-middle"><span>Paid: ' . app_format_money($payments['paid'], $payments['currency']) . '</span></div></div><div class="list-item padding-small"><div class="columns-middle"><span>% Paid: ' . $payments['paid_percentage'] . '%</span></div></div></div>';

    return $tableHtml;
}