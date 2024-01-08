<?php

defined('BASEPATH') or exit('No direct script access allowed');

$table_data = [
    _l('the_number_sign'),
    _l('freight_port'),
    _l('freight_fcl_20'),
    _l('freight_fcl_40'),
    _l('Destination'),
    _l('freight_validity'),
    _l('freight_dateadded'),
];


render_datatable($table_data, 'freights', ['number-index-' . isset($bulk_actions) ? 2 : 1]);