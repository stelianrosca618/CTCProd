<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content accounting-template proposal">
        <div class="row">
            <?php
         if (isset($proposal)) {
             echo form_hidden('isedit', $proposal->id);
         }
         $rel_type  = '';
            $rel_id = '';
            if (isset($proposal) || ($this->input->get('rel_id') && $this->input->get('rel_type'))) {
                if ($this->input->get('rel_id')) {
                    $rel_id   = $this->input->get('rel_id');
                    $rel_type = $this->input->get('rel_type');
                } else {
                    $rel_id   = $proposal->rel_id;
                    $rel_type = $proposal->rel_type;
                }
            }
            ?>
            <?php
         echo form_open($this->uri->uri_string(), ['id' => 'proposal-form', 'class' => '_transaction_form proposal-form']);

         if ($this->input->get('estimate_request_id')) {
             echo form_hidden('estimate_request_id', $this->input->get('estimate_request_id'));
         }
         ?>

            <div class="col-md-12">
            <?php print_r($proposal->invoice_id) ?>
                <h4
                    class="tw-mt-0 tw-font-semibold tw-text-lg tw-text-neutral-700 tw-flex tw-items-center tw-space-x-2">
                    <span>
                        <?php echo isset($proposal) ? format_proposal_number($proposal->id) : _l('new_proposal'); ?>
                    </span>
                    
                    <?php echo isset($proposal) ? format_proposal_status($proposal->status) : ''; ?>
                </h4>
                <div class="panel_s">
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-md-6 border-right">
                                <?php $value = (isset($proposal) ? $proposal->subject : 'Proposal '.time()); ?>
                                <?php $attrs = (isset($proposal) ? [] : ['autofocus' => true]); ?>
                                <?php echo render_input('subject', 'proposal_subject', $value, 'text', $attrs); ?>
                                <div class="form-group select-placeholder">
                                    <label for="rel_type"
                                        class="control-label"><?php echo _l('proposal_related'); ?></label>
                                    <select name="rel_type" id="rel_type" class="selectpicker" data-width="100%"
                                        data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
                                        <option value=""></option>
                                        <option value="lead" <?php if ((isset($proposal) && $proposal->rel_type == 'lead') || $this->input->get('rel_type')) {
                                            if ($rel_type == 'lead') {
                                                echo 'selected';
                                            }
                                        } ?>><?php echo _l('proposal_for_lead'); ?></option>
                                        <option value="customer" <?php if ((isset($proposal) && $proposal->rel_type == 'customer') || $this->input->get('rel_type')) {
                                            if ($rel_type == 'customer') {
                                                echo 'selected';
                                            }
                                        } ?>><?php echo _l('proposal_for_customer'); ?></option>
                                    </select>
                                </div>
                                <div class="form-group select-placeholder<?php if ($rel_id == '') {
                                    echo ' hide';
                                } ?> " id="rel_id_wrapper">
                                    <label for="rel_id"><span class="rel_id_label"></span></label>
                                    <div id="rel_id_select">
                                        <select name="rel_id" id="rel_id" class="ajax-search" data-width="100%"
                                            data-live-search="true"
                                            data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
                                            <?php if ($rel_id != '' && $rel_type != '') {
                                                $rel_data = get_relation_data($rel_type, $rel_id);
                                                $rel_val  = get_relation_values($rel_data, $rel_type);
                                                echo '<option value="' . $rel_val['id'] . '" selected>' . $rel_val['name'] . '</option>';
                                            } ?>
                                        </select>
                                    </div>
                                </div>
                                <div
                                    class="form-group select-placeholder projects-wrapper <?php echo ((!isset($proposal)) || (isset($proposal) && $proposal->rel_type !== 'customer')) ? 'hide' : '' ?>">
                                    <label for="project_id"><?php echo _l('project'); ?></label>
                                    <div id="project_ajax_search_wrapper">
                                        <select name="project_id" id="project_id" class="projects ajax-search"
                                            data-live-search="true" data-width="100%"
                                            data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
                                            <?php
                                            if (isset($proposal) && $proposal->project_id) {
                                                echo '<option value="' . $proposal->project_id . '" selected>' . get_project_name_by_id($proposal->project_id) . '</option>';
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <?php $value = (isset($proposal) ? _d($proposal->date) : _d(date('Y-m-d'))) ?>
                                        <?php echo render_date_input('date', 'proposal_date', $value); ?>
                                    </div>
                                    <div class="col-md-6">
                                        <?php
                                        $value = '';
                                        if (isset($proposal)) {
                                            $value = _d($proposal->open_till);
                                        } else {
                                            if (get_option('proposal_due_after') != 0) {
                                                $value = _d(date('Y-m-d', strtotime('+' . get_option('proposal_due_after') . ' DAY', strtotime(date('Y-m-d')))));
                                            }
                                        }
                                        echo render_date_input('open_till', 'proposal_open_till', $value); ?>
                                    </div>
                                </div>
                                <?php
                                $selected      = '';
                                $currency_attr = ['data-show-subtext' => true];
                                foreach ($currencies as $currency) {
                                    if ($currency['isdefault'] == 1) {
                                        $currency_attr['data-base'] = $currency['id'];
                                    }
                                    if (isset($proposal)) {
                                            if ($currency['id'] == $proposal->currency) {
                                                $selected = $currency['id'];
                                            }
                                            // BOF VK, Allow to change currency
                                            // VK Mod: Comment 
                                            // if ($proposal->rel_type == 'customer') {
                                            //     $currency_attr['disabled'] = true;
                                            // }
                                            // EOF VK, Allow to change currency
                                    } else {
                                        if ($rel_type == 'customer') {
                                                $customer_currency = $this->clients_model->get_customer_default_currency($rel_id);
                                            if ($customer_currency != 0) {
                                                $selected = $customer_currency;
                                            } else {
                                                if ($currency['isdefault'] == 1) {
                                                    $selected = $currency['id'];
                                                }
                                            }
                                            // BOF VK, Allow to change currency
                                            // VK Mod: Comment 
                                            // $currency_attr['disabled'] = true;
                                            // EOF VK, Allow to change currency
                                        } else {
                                            if ($currency['isdefault'] == 1) {
                                                $selected = $currency['id'];
                                            }
                                        }
                                    }
                                }
                                $currency_attr = apply_filters_deprecated('proposal_currency_disabled', [$currency_attr], '2.3.0', 'proposal_currency_attributes');
                                $currency_attr = hooks()->apply_filters('proposal_currency_attributes', $currency_attr);
                                ?>
                                <div class="row">
                                    <div class="col-md-6">
                                        <?php
                                        echo render_select('currency', $currencies, ['id', 'name', 'symbol'], 'proposal_currency', $selected, $currency_attr);
                                        ?>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group select-placeholder">
                                            <label for="discount_type"
                                                class="control-label"><?php echo _l('discount_type'); ?></label>
                                            <select name="discount_type" class="selectpicker" data-width="100%"
                                                data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
                                                <option value="" selected><?php echo _l('no_discount'); ?></option>
                                                <option value="before_tax" <?php
                                                if (isset($estimate)) {
                                                    if ($estimate->discount_type == 'before_tax') {
                                                        echo 'selected';
                                                    }
                                                }?>><?php echo _l('discount_type_before_tax'); ?></option>
                                                <option value="after_tax" <?php if (isset($estimate)) {
                                                    if ($estimate->discount_type == 'after_tax') {
                                                        echo 'selected';
                                                    }
                                                } ?>><?php echo _l('discount_type_after_tax'); ?></option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <!-- // BOF VK, Allow to change currency
                                // VK Mod: Add -->
                                <?php
                                if (isset($proposal) && $proposal->incoterms && isset($proposal->incoterms['exchange_rate'])) {
                                    $exchange_rate = $proposal->incoterms['exchange_rate'];
                                } else {
                                    $exchange_rate = '';
                                }
                                ?>
                                <div id="currency-exchange-rate" class="row<?php echo (isset($proposal) && ($base_currency->id != $proposal->currency)) ? '' : ' hide'; ?>">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <div class="form-group">
                                                <label for="website">Currency Exchange Rate</label>
                                                <div class="input-group">
                                                    <span id="base_currency_symbol" class="input-group-addon"><?php echo $base_currency->symbol; ?></span>
                                                    <input type="text" name="base_currency" id="base_currency" value="1"
                                                        class="form-control" readonly>
                                                    <span class="input-group-addon">=</span>
                                                    <span id="exchange_currency_symbol" class="input-group-addon"><?php echo (isset($proposal) && isset($proposal->symbol)) ? $proposal->symbol : '@'; ?></span>
                                                    <input type="text" name="exchange_currency" id="exchange_currency" value="<?php echo $exchange_rate; ?>"
                                                        class="form-control">
                                                </div>
                                            </div>
                                        </div>                                        
                                    </div>
                                </div>
                                <!-- // EOF VK, Allow to change currency -->
                                <!-- // BOF VK, Choose container type 
                                // VK Mod: Add -->
                                <?php
                                $fob_port_options = '';
                                $cfr_port_options = '';
                                foreach ($freights as $freight) {
                                    $fob_port_options .= '<option value="' . $freight['id'] . '"'.((isset($proposal) && $proposal->incoterms && in_array($freight['id'], $proposal->incoterms['fob_port'])) ? ' selected' : '').'>' . $freight['port'] . '</option>';
                                    $cfr_port_options .= '<option value="' . $freight['id'] . '"'.((isset($proposal) && $proposal->incoterms && in_array($freight['id'], $proposal->incoterms['cfr_port'])) ? ' selected' : '').'>' . $freight['port'] . '</option>';
                                }
                                ?>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="fob_port"
                                                class="control-label">FOB Port</label>
                                            <select id="fob_port" name="fob_port[]" class="selectpicker" data-live-search="true" data-max-options="1" multiple>
                                                <?php echo $fob_port_options; ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="cfr_port"
                                                class="control-label">CFR Port</label>
                                            <select id="cfr_port" name="cfr_port[]" class="selectpicker" data-live-search="true" multiple>
                                                <?php echo $cfr_port_options; ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <?php $container_types = (isset($proposal) && !empty($proposal->incoterms)) ? $proposal->incoterms['container_type'] : array(); ?>
                                <div class="form-group">
                                    <label class="checkbox-inline">
                                      <input type="checkbox" id="container_type_fcl_20" name="container_type[]" value="20 FCL"<?php echo (in_array('20 FCL', $container_types) ? ' checked' : ''); ?>> 20' FCL
                                    </label>
                                    <label class="checkbox-inline">
                                      <input type="checkbox" id="container_type_fcl_40" name="container_type[]" value="40 FCL"<?php echo (in_array('40 FCL', $container_types) ? ' checked' : ''); ?>> 40' FCL
                                    </label>
                                    <label class="checkbox-inline">
                                      <input type="checkbox" id="container_type_air" name="container_type[]" value="Air"<?php echo (in_array('Airs', $container_types) ? ' checked' : ''); ?>> Destination
                                    </label>
                                </div>
                                <!-- // EOF VK, Choose container type -->
                                <?php $fc_rel_id = (isset($proposal) ? $proposal->id : false); ?>
                                <?php echo render_custom_fields('proposal', $fc_rel_id); ?>
                                <div class="form-group no-mbot">
                                    <label for="tags" class="control-label"><i class="fa fa-tag" aria-hidden="true"></i>
                                        <?php echo _l('tags'); ?></label>
                                    <input type="text" class="tagsinput" id="tags" name="tags"
                                        value="<?php echo(isset($proposal) ? prep_tags_input(get_tags_in($proposal->id, 'proposal')) : ''); ?>"
                                        data-role="tagsinput">
                                </div>
                                <div class="form-group mtop10 no-mbot">
                                    <p><?php echo _l('proposal_allow_comments'); ?></p>
                                    <div class="onoffswitch">
                                        <input type="checkbox" id="allow_comments" class="onoffswitch-checkbox" <?php if ((isset($proposal) && $proposal->allow_comments == 1) || !isset($proposal)) {
                                      echo 'checked';
                                  }; ?> value="on" name="allow_comments">
                                        <label class="onoffswitch-label" for="allow_comments" data-toggle="tooltip"
                                            title="<?php echo _l('proposal_allow_comments_help'); ?>"></label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group select-placeholder">
                                            <label for="status"
                                                class="control-label"><?php echo _l('proposal_status'); ?></label>
                                            <?php
                                    $disabled = '';
                                    if (isset($proposal)) {
                                        if ($proposal->estimate_id != null || $proposal->invoice_id != null) {
                                            $disabled = 'disabled';
                                        }
                                    }
                                    ?>
                                            <select name="status" class="selectpicker" data-width="100%"
                                                <?php echo $disabled; ?>
                                                data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
                                                <?php foreach ($statuses as $status) { ?>
                                                <option value="<?php echo $status; ?>" <?php if ((isset($proposal) && $proposal->status == $status) || (!isset($proposal) && $status == 0)) {
                                        echo 'selected';
                                    } ?>><?php echo format_proposal_status($status, '', false); ?></option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <?php
                                            $selected = !isset($proposal) && get_option('automatically_set_logged_in_staff_sales_agent') == '1' ? get_staff_user_id() : '';
                                            foreach ($staff as $member) {
                                                if (isset($proposal)) {
                                                    if ($proposal->assigned == $member['staffid']) {
                                                        $selected = $member['staffid'];
                                                    }
                                                }
                                            }
                                            echo render_select('assigned', $staff, ['staffid', ['firstname', 'lastname']], 'proposal_assigned', $selected);
                                        ?>
                                    </div>
                                </div>
                                <?php $value = (isset($proposal) ? $proposal->proposal_to : ''); ?>
                                <?php echo render_input('proposal_to', 'proposal_to', $value); ?>
                                <?php $value = (isset($proposal) ? $proposal->address : ''); ?>
                                <?php echo render_textarea('address', 'proposal_address', $value); ?>
                                <div class="row">
                                    <div class="col-md-6">
                                        <?php $value = (isset($proposal) ? $proposal->city : ''); ?>
                                        <?php echo render_input('city', 'billing_city', $value); ?>
                                    </div>
                                    <div class="col-md-6">
                                        <?php $value = (isset($proposal) ? $proposal->state : ''); ?>
                                        <?php echo render_input('state', 'billing_state', $value); ?>
                                    </div>
                                    <div class="col-md-6">
                                        <?php $countries = get_all_countries(); ?>
                                        <?php $selected  = (isset($proposal) ? $proposal->country : ''); ?>
                                        <?php echo render_select('country', $countries, ['country_id', ['short_name'], 'iso2'], 'billing_country', $selected); ?>
                                    </div>
                                    <div class="col-md-6">
                                        <?php $value = (isset($proposal) ? $proposal->zip : ''); ?>
                                        <?php echo render_input('zip', 'billing_zip', $value); ?>
                                    </div>
                                    <div class="col-md-6">
                                        <?php $value = (isset($proposal) ? $proposal->email : ''); ?>
                                        <?php echo render_input('email', 'proposal_email', $value); ?>
                                    </div>
                                    <div class="col-md-6">
                                        <?php $value = (isset($proposal) ? $proposal->phone : ''); ?>
                                        <?php echo render_input('phone', 'proposal_phone', $value); ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div
                            class="btn-bottom-toolbar bottom-transaction text-right sm:tw-flex sm:tw-items-center sm:tw-justify-between">
                            <p class="no-mbot pull-left mtop5 btn-toolbar-notice tw-hidden sm:tw-block">
                                <?php echo _l('include_proposal_items_merge_field_help', '<b>{proposal_items}</b>'); ?>
                            </p>
                            <div>
                                <button type="button"
                                    class="btn btn-default mleft10 proposal-form-submit save-and-send transaction-submit">
                                    <?php echo _l('save_and_send'); ?>
                                </button>
                                <button class="btn btn-primary mleft5 proposal-form-submit transaction-submit"
                                    type="button">
                                    <?php echo _l('submit'); ?>
                                </button>
                            </div>
                        </div>
                    </div>
                    <hr class="hr-panel-separator" />
                    <?php $this->load->view('admin/estimates/_add_edit_items'); ?>
                </div>
                <div class="col-md-6">
                    <!-- // BOF VK, Notes, MOQ & Quantity Offered
                    // VK Mod: Add -->
                    <div class="form-group">
                        <label for="notes" class="control-label">Notes</label>
                        <textarea id="notes" name="notes" rows="4" class="form-control"><?php echo (isset($proposal) ? $proposal->notes : ''); ?></textarea>
                    </div>
                    <div class="form-group">
                        <label for="moq" class="control-label">MOQ</label>
                        <input type="number" id="moq" name="moq" class="form-control" value="<?php echo (isset($proposal) ? $proposal->items[0]['fcl_20_container'] : ''); ?>">
                    </div>
                    <div class="form-group">
                        <label for="quantity-offered" class="control-label">Quantity Offered</label>
                        <input type="number" id="quantity-offered" name="quantity_offered" class="form-control" value="<?php echo (isset($proposal) ? $proposal->quantity_offered : ''); ?>">
                    </div>
                    <div class="form-group">
                        <?php 
                            if($proposal->open_till){
                                $periodDate = date('F Y', strtotime('+ 15DAY', strtotime($proposal->open_till)));
                            }else{
                                $opentill_Val = _d(date('Y-m-d', strtotime('+' . get_option('proposal_due_after') . ' DAY', strtotime(date('Y-m-d')))));
                                $periodDate = date('F Y', strtotime('+ 15DAY', strtotime($opentill_Val)));
                            }

                            //print_r($periodDate);
                            echo render_input('shipment_period', 'shipment-period', $periodDate); 
                            
                        ?>
                    </div>
                    <!-- // EOF VK, Notes, MOQ & Quantity Offered -->
                    <div class="form-group">
                        <label for="termTemplate">Term Template</label>
                        <textarea class="form-control" id="termTemplate" readonly><?php echo $termTemplate->content?></textarea>
                        <?php 
                            //echo render_select('termTemplate', $termTemplates, ['id', 'name'], 'Term Template', $termTemplate->id);
                        ?>
                    </div>
                </div>
            </div>
            <?php echo form_close(); ?>
            <?php $this->load->view('admin/invoice_items/item'); ?>
        </div>
        <div class="btn-bottom-pusher"></div>
    </div>
</div>
<?php init_tail(); ?>
<script>
    <?php 
    $monthsAyy = [];
    for ($m=1; $m<=12; $m++) {
        $month = date('F', mktime(0,0,0,$m, 1, date('Y')));
        array_push($monthsAyy, $month);
        
       
    }
    
    ?>
    var montNameArr = <?php echo json_encode($monthsAyy)?>;
var _rel_id = $('#rel_id'),
    _rel_type = $('#rel_type'),
    _rel_id_wrapper = $('#rel_id_wrapper'),
    _project_wrapper = $('.projects-wrapper'),
    data = {};

$(function() {
    $('.pull-right').on('click', function(){
        if($('input[name="moq"]').value != ''){
            const proposalForm = $('form[id="proposal-form"]')[0];
            var proposalFormData = $(proposalForm).serializeArray();
            var itemId = proposalFormData.find(formItem => formItem.name == "item_select");
            requestGetJSON("invoice_items/get_item_by_id/" + itemId.value).done(function (
                response
            ){
                $('input[name="moq"]').val(response.fcl_20_container);
            });     
        }
        
    })
    
    $('#open_till').on('change', function(e){
        console.log(montNameArr);
        var newDayTime = new Date();
        
        var pickedDayStr = e.target.value.toString();
        var newdate = pickedDayStr.split("-").reverse().join("-");
        var openTillDay = new Date(newdate);
        openTillDay.setDate(openTillDay.getDate() + 15);
        var shipPeriod = `${montNameArr[openTillDay.getMonth()]} ${openTillDay.getFullYear()}`;
        console.log(shipPeriod, openTillDay, newDayTime, newDayTime.getMonth());
        $('#shipment_period').val(shipPeriod);
        //alert_float('danger', 'Validity day already pass');
    })
    <?php if (isset($proposal) && $proposal->rel_type === 'customer') { ?>
    init_proposal_project_select('select#project_id')
    <?php } ?>
    $('body').on('change', '#rel_type', function() {
        if (_rel_type.val() != 'customer') {
            _project_wrapper.addClass('hide')
        }
    });
    
    
    // $('#shipment_period').on('change', function(e){
    //     console.log(e.target.value);
    //     $('#shipment_period').val(e.target.value);
    //     var newdate = e.target.value.split("-").reverse().join("-");
    //     var pickedDate = new Date(newdate);
    //     var shipPeriod = `${pickedDate.getFullYear()}-${montNameArr[pickedDate.getMonth()+1]}`;
        
    //     $('#shipment_period').val(shipPeriod);
    // })

    $('body').on('change', '#rel_id', function() {
        if (_rel_type.val() == 'customer') {
            console.log('working')
            var projectAjax = $('select#project_id');
            var clonedProjectsAjaxSearchSelect = projectAjax.html('').clone();
            projectAjax.selectpicker('destroy').remove();
            projectAjax = clonedProjectsAjaxSearchSelect;
            $('#project_ajax_search_wrapper').append(clonedProjectsAjaxSearchSelect);
            init_proposal_project_select(projectAjax);
            _project_wrapper.removeClass('hide')
        }
    });

    init_currency();
    // Maybe items ajax search
    init_ajax_search('items', '#item_select.ajax-search', undefined, admin_url + 'items/search');
    validate_proposal_form();
    // BOF VK, Allow to change currency
    // VK Mod: Add
    var rel_currency_preference = '';
    // EOF VK, Allow to change currency
    $('body').on('change', '#rel_id', function() {
        if ($(this).val() != '') {
            $.get(admin_url + 'proposals/get_relation_data_values/' + $(this).val() + '/' + _rel_type
                .val(),
                function(response) {
                    $('input[name="proposal_to"]').val(response.company);
                    $('input[name="proposal_to"]').attr('data-autoRate', response.autoRate);
                    if(response.autoRate){
                        var calculateRate = 1- (parseFloat(response.autoRate) / 100);
                        $('th.default-rate-column').html('Rate X '+calculateRate+'%');
                    }else{
                        $('th.default-rate-column').html('Rate');
                    }
                    
                    console.log('tableHT', $('th.default-rate-column').html(), $('th.default-rate-column'));
                    $('textarea[name="address"]').val(response.address);
                    $('input[name="email"]').val(response.email);
                    $('input[name="phone"]').val(response.phone);
                    $('input[name="city"]').val(response.city);
                    $('input[name="state"]').val(response.state);
                    $('input[name="zip"]').val(response.zip);
                    $('select[name="country"]').selectpicker('val', response.country);
                    var currency_selector = $('#currency');
                    if (_rel_type.val() == 'customer') {
                        if (typeof(currency_selector.attr('multi-currency')) == 'undefined') {
                            // BOF VK, Allow to change currency
                            // VK Mod: Comment 
                            // currency_selector.attr('disabled', true);
                        }

                    } else {
                        currency_selector.attr('disabled', false);
                    }

                    var proposal_to_wrapper = $('[app-field-wrapper="proposal_to"]');
                    if (response.is_using_company == false && !empty(response.company)) {
                        proposal_to_wrapper.find('#use_company_name').remove();
                        proposal_to_wrapper.find('#use_company_help').remove();
                        proposal_to_wrapper.append('<div id="use_company_help" class="hide">' +
                            response.company + '</div>');
                        proposal_to_wrapper.find('label')
                            .prepend(
                                "<a href=\"#\" id=\"use_company_name\" data-toggle=\"tooltip\" data-title=\"<?php echo _l('use_company_name_instead'); ?>\" onclick='document.getElementById(\"proposal_to\").value = document.getElementById(\"use_company_help\").innerHTML.trim(); this.remove();'><i class=\"fa fa-building-o\"></i></a> "
                            );
                    } else {
                        proposal_to_wrapper.find('label #use_company_name').remove();
                        proposal_to_wrapper.find('label #use_company_help').remove();
                    }
                    /* Check if customer default currency is passed */
                    if (response.currency) {
                        // BOF VK, Allow to change currency
                        // VK Mod: Add
                        rel_currency_preference = response.currency;
                        // EOF VK, Allow to change currency
                        currency_selector.selectpicker('val', response.currency);
                    } else {
                        // BOF VK, Allow to change currency
                        // VK Mod: Add
                        rel_currency_preference = currency_selector.data('base');
                        // EOF VK, Allow to change currency
                        /* Revert back to base currency */
                        currency_selector.selectpicker('val', currency_selector.data('base'));
                    }
                    currency_selector.selectpicker('refresh');
                    currency_selector.change();
                }, 'json');
        }
    });
    // BOF VK, Allow to change currency
    // VK Mod: Add
    var jsonCurrencies = <?php echo json_encode($currencies); ?>;
    $("body").on("change", 'select[name="currency"]', function () {
        init_currency();
        var currency_selector = $('#currency');
        if (_rel_type.val() == 'customer') {
            if (typeof(rel_currency_preference) != "undefined" && rel_currency_preference != $(this).val()) {
                let objB = jsonCurrencies.find(o => parseInt(o.id) === parseInt(rel_currency_preference));
                $('#base_currency_symbol').text(objB.name + ' ' + objB.symbol);
                let objE = jsonCurrencies.find(o => o.id === $(this).val());
                $('#exchange_currency_symbol').text(objE.name + ' ' + objE.symbol);
                $('#currency-exchange-rate').removeClass('hide');
                $('#exchange_currency').val('');
                $('#exchange_currency').prop('required', true);
            } else {
                $('#currency-exchange-rate').addClass('hide');
                $('#exchange_currency').prop('required', false);
                $('#exchange_currency').val('');
            }
        } else {
            $('#currency-exchange-rate').addClass('hide');
            $('#exchange_currency').prop('required', false);
            $('#exchange_currency').val('');
        }
    });
    // EOF VK, Allow to change currency
    // BOF VK, Incoterms column
    // VK Mod: Add
    function init_default_rate () {
        /*if ($('#fob_port').val().length > 0) {
            $('.default-rate-column').addClass('hide');   
        } else {
            $('.default-rate-column').removeClass('hide');     
        }*/
    }

    $("body").on("change", '#fob_port', function () {
        $('.incoterms-column-fob.incoterms-column-fcl-20').addClass('hide');
        $('.incoterms-column-fob.incoterms-column-fcl-40').addClass('hide');
        $('.incoterms-column-fob.incoterms-column-air').addClass('hide');

        if ($('#fob_port').val().length > 0) {
            if ( $('#container_type_fcl_20').is(':checked') ) {            
                $.each($('#fob_port').val(), function (pcolumn, pvalue) {
                    $('.incoterms-column-fcl-20.fob-port-'+pvalue).removeClass('hide');
                });            
            }
            if ( $('#container_type_fcl_40').is(':checked') ) {
                $.each($('#fob_port').val(), function (pcolumn, pvalue) {
                    $('.incoterms-column-fcl-40.fob-port-'+pvalue).removeClass('hide');
                });
            }
            if ( $('#container_type_air').is(':checked') ) {
                $.each($('#fob_port').val(), function (pcolumn, pvalue) {
                    $('.incoterms-column-air.fob-port-'+pvalue).removeClass('hide');
                });
            }
        } else {
            $('.incoterms-column-fob').addClass('hide');
        }
    });
    $("body").on("change", '#cfr_port', function () {
        $('.incoterms-column-cfr.incoterms-column-fcl-20').addClass('hide');
        $('.incoterms-column-cfr.incoterms-column-fcl-40').addClass('hide');
        $('.incoterms-column-cfr.incoterms-column-air').addClass('hide');

        if ($('#cfr_port').val().length > 0) {
            if ( $('#container_type_fcl_20').is(':checked') ) {
                $.each($('#cfr_port').val(), function (pcolumn, pvalue) {
                    $('.incoterms-column-fcl-20.cfr-port-'+pvalue).removeClass('hide');
                });
            }
            if ( $('#container_type_fcl_40').is(':checked') ) {
                $.each($('#cfr_port').val(), function (pcolumn, pvalue) {
                    $('.incoterms-column-fcl-40.cfr-port-'+pvalue).removeClass('hide');
                });
            }
            if ( $('#container_type_air').is(':checked') ) {
                $.each($('#cfr_port').val(), function (pcolumn, pvalue) {
                    $('.incoterms-column-air.cfr-port-'+pvalue).removeClass('hide');
                });
            }
        } else {
            $('.incoterms-column-cfr').addClass('hide');
        }
    });
    $("body").on("click", '#container_type_fcl_20', function () {
        $('.incoterms-column-fcl-20').addClass('hide');
        init_default_rate();
        if ( $(this).is(':checked') ) {
            $.each($('#fob_port').val(), function (pcolumn, pvalue) {
                $('.incoterms-column-fcl-20.fob-port-'+pvalue).removeClass('hide');
            });
            $.each($('#cfr_port').val(), function (pcolumn, pvalue) {
                $('.incoterms-column-fcl-20.cfr-port-'+pvalue).removeClass('hide');
            });
        }
    });
    $("body").on("click", '#container_type_fcl_40', function () {
        $('.incoterms-column-fcl-40').addClass('hide');
        init_default_rate();
        if ( $(this).is(':checked') ) {
            $.each($('#fob_port').val(), function (pcolumn, pvalue) {
                $('.incoterms-column-fcl-40.fob-port-'+pvalue).removeClass('hide');
            });
            $.each($('#cfr_port').val(), function (pcolumn, pvalue) {
                $('.incoterms-column-fcl-40.cfr-port-'+pvalue).removeClass('hide');
            });
        }
    });
    $("body").on("click", '#container_type_air', function () {
        $('.incoterms-column-air').addClass('hide');
        init_default_rate();
        if ( $(this).is(':checked') ) {
            $.each($('#fob_port').val(), function (pcolumn, pvalue) {
                $('.incoterms-column-air.fob-port-'+pvalue).removeClass('hide');
            });
            $.each($('#cfr_port').val(), function (pcolumn, pvalue) {
                $('.incoterms-column-air.cfr-port-'+pvalue).removeClass('hide');
            });
        }
    });
    // EOF VK, Incoterms column
    $('.rel_id_label').html(_rel_type.find('option:selected').text());
    _rel_type.on('change', function() {
        var clonedSelect = _rel_id.html('').clone();
        _rel_id.selectpicker('destroy').remove();
        _rel_id = clonedSelect;
        $('#rel_id_select').append(clonedSelect);
        proposal_rel_id_select();
        if ($(this).val() != '') {
            _rel_id_wrapper.removeClass('hide');
        } else {
            _rel_id_wrapper.addClass('hide');
        }
        $('.rel_id_label').html(_rel_type.find('option:selected').text());
    });
    proposal_rel_id_select();
    <?php if (!isset($proposal) && $rel_id != '') { ?>
    _rel_id.change();
    <?php } ?>
});

function init_proposal_project_select(selector) {
    init_ajax_search('project', selector, {
        customer_id: function() {
            return $('#rel_id').val();
        }
    })
}

function proposal_rel_id_select() {
    var serverData = {};
    serverData.rel_id = _rel_id.val();
    data.type = _rel_type.val();
    init_ajax_search(_rel_type.val(), _rel_id, serverData);
}

function validate_proposal_form() {
    appValidateForm($('#proposal-form'), {
        subject: 'required',
        proposal_to: 'required',
        rel_type: 'required',
        rel_id: 'required',
        date: 'required',
        email: {
            email: true,
            required: true
        },
        currency: 'required',
    });
}
</script>
</body>

</html>
