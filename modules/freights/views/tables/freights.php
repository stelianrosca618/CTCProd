<?php

defined('BASEPATH') or exit('No direct script access allowed');

$hasPermissionEdit   = has_permission('freights', '', 'edit');
$hasPermissionDelete = has_permission('freights', '', 'delete');

$aColumns = [
    'id',
    'port',
    'fcl_20',
    'fcl_40',
    'air',
    'validity',
    'dateadded',
];

$sIndexColumn = 'id';
$sTable       = db_prefix() . 'freights';

$where = [];
$join  = [];

$result = data_tables_init(
    $aColumns,
    $sIndexColumn,
    $sTable,
    $join,
    $where
);

$output  = $result['output'];
$rResult = $result['rResult'];

foreach ($rResult as $aRow) {
    $row = [];

    $row[] = '<a href="#" onclick="edit_freight(' . $aRow['id'] . '); return false;">' . $aRow['id'] . '</a>';

    $outputName = '';

    $outputName .= '<a href="#" class="display-block main-freights-table-href-name" onclick="edit_freight(' . $aRow['id'] . '); return false;">' . $aRow['port'] . '</a>';

    $outputName .= '<div class="row-options">';

    $class = 'text-success bold';
    $style = '';

    if ($hasPermissionEdit) {
        $outputName .= '<a href="#" onclick="edit_freight(' . $aRow['id'] . '); return false">' . _l('edit') . '</a>';
    }

    if ($hasPermissionDelete) {
        $outputName .= '<span class="tw-text-neutral-300"> | </span><a href="' . admin_url('freights/delete_freight/' . $aRow['id']) . '" class="text-danger _delete freight-delete">' . _l('delete') . '</a>';
    }
    $outputName .= '</div>';

    $row[] = $outputName;
    
    $row[] = $aRow['fcl_20'];
    $row[] = $aRow['fcl_40'];
    $row[] = $aRow['air'];

    $row[] = _d($aRow['validity']);

    $row[] = _d($aRow['dateadded']);

    $row['DT_RowClass'] = 'has-row-options';

    $output['aaData'][] = $row;
}