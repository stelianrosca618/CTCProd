<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div class="modal fade" id="sales_item_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">
                    <span class="edit-title"><?php echo _l('invoice_item_edit_heading'); ?></span>
                    <span class="add-title"><?php echo _l('invoice_item_add_heading'); ?></span>
                </h4>
            </div>
            <?php echo form_open('admin/invoice_items/manage', ['id' => 'invoice_item_form']); ?>
            <?php echo form_hidden('itemid'); ?>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="alert alert-warning affect-warning hide">
                            <?php echo _l('changing_items_affect_warning'); ?>
                        </div>
                        <?php echo render_input('prod_link', 'Link')?>
                        <?php echo render_input('description', 'invoice_item_add_edit_description'); ?>
                        <?php echo render_textarea('long_description', 'invoice_item_long_description'); ?>
                        <div class="form-group">
                        <label for="rate" class="control-label">
                            <?php echo _l('invoice_item_add_edit_rate_currency', $base_currency->name . ' <small>(' . _l('base_currency_string') . ')</small>'); ?></label>
                            <input type="number" id="rate" name="rate" class="form-control" value="">
                        </div>
                        <?php
                            foreach ($currencies as $currency) {
                                if ($currency['isdefault'] == 0 && total_rows(db_prefix() . 'clients', ['default_currency' => $currency['id']]) > 0) { ?>
                                <div class="form-group">
                                    <label for="rate_currency_<?php echo $currency['id']; ?>" class="control-label">
                                        <?php echo _l('invoice_item_add_edit_rate_currency', $currency['name']); ?></label>
                                        <input type="number" id="rate_currency_<?php echo $currency['id']; ?>" name="rate_currency_<?php echo $currency['id']; ?>" class="form-control" value="">
                                    </div>
                             <?php   }
                            }
                        ?>
                        <div class="row">
                            <div class="col-md-6">
                             <div class="form-group">
                                <label class="control-label" for="tax"><?php echo _l('tax_1'); ?></label>
                                <select class="selectpicker display-block" data-width="100%" name="tax" data-none-selected-text="<?php echo _l('no_tax'); ?>">
                                    <option value=""></option>
                                    <?php foreach ($taxes as $tax) { ?>
                                    <option value="<?php echo $tax['id']; ?>" data-subtext="<?php echo $tax['name']; ?>"><?php echo $tax['taxrate']; ?>%</option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                         <div class="form-group">
                            <label class="control-label" for="tax2"><?php echo _l('tax_2'); ?></label>
                            <select class="selectpicker display-block" disabled data-width="100%" name="tax2" data-none-selected-text="<?php echo _l('no_tax'); ?>">
                                <option value=""></option>
                                <?php foreach ($taxes as $tax) { ?>
                                <option value="<?php echo $tax['id']; ?>" data-subtext="<?php echo $tax['name']; ?>"><?php echo $tax['taxrate']; ?>%</option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="clearfix mbot15"></div>

                <!-- // BOF VK, Add three fields
                // VK Mod: Add -->
                <?php echo render_input('fcl_20_container', '20\' FCL'); ?>
                <?php echo render_input('fcl_40_container', '40\' FCL'); ?>
                <?php echo render_input('air_container', 'Destination'); ?>
                <!-- // EOF VK, Add three fields -->

                <?php echo render_input('unit', 'unit'); ?>

                <div id="custom_fields_items">
                    <?php echo render_custom_fields('items'); ?>
                </div>
                <?php echo render_select('group_id', $items_groups, ['id', 'name'], 'item_group'); ?>
                <?php echo render_select('subgroup_id', $item_subGroups, ['id', 'name'], 'Sub Group')?>
                <?php echo render_input('production_ratio', 'Production ratio')?>
                <?php hooks()->do_action('before_invoice_item_modal_form_close'); ?>
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
        <button type="submit" class="btn btn-primary"><?php echo _l('submit'); ?></button>
        <?php echo form_close(); ?>
    </div>
</div>
</div>
</div>
<script>
    // Maybe in modal? Eq convert to invoice or convert proposal to estimate/invoice
    if(typeof(jQuery) != 'undefined'){
        init_item_js();
        console.log($('.main input[name="quantity"]'));
        $('.main input[name="quantity"]').on( "change", function() {
                  console.log('changedVal', $(this).attr('data-limitQty'));
                  let limitQty =  $(this).attr('data-limitQty');
                  if(limitQty != 0){
                      alert("Stock is below "+limitQty);
                  }else{
                    //alert('here is not limit');
                  }
                } );
    } else {
     window.addEventListener('load', function () {
       var initItemsJsInterval = setInterval(function(){
            if(typeof(jQuery) != 'undefined') {
                init_item_js();
                clearInterval(initItemsJsInterval);
                $('.main input[name="quantity"]').on( "change", function() {
                  console.log('changedVal', $(this).attr('data-limitQty'));
                  let limitQty =  $(this).attr('data-limitQty');
                  if(limitQty != 0){
                    alert("Stock is below "+limitQty);
                  }

                  // if(limitQty){
                  //   if(parseInt(limitQty) < parseInt($(this).val())){
                  //     alert("Stock is below "+limitQty);
                  //   }
                  // }else{
                  //   //alert('here is not limit');
                  // }
                });
                // $('body').find('[data-quantity]').on( "change", function() {
                //   console.log('changedVal', $(this).attr('data-limitQty'));
                //   let limitQty =  $(this).attr('data-limitQty');
                //   if(limitQty){
                //     if(parseInt(limitQty) < parseInt($(this).val())){
                //       alert("Stock is below "+limitQty);
                //     }
                //   }else{
                //     //alert('here is not limit');
                //   }
                // } );
            }
         }, 1000);
     });
  }
// Items add/edit
function manage_invoice_items(form) {
    var data = $(form).serialize();

    var url = form.action;
    $.post(url, data).done(function (response) {
        response = JSON.parse(response);
        if (response.success == true) {
            var item_select = $('#item_select');
            if ($("body").find('.accounting-template').length > 0) {
                if (!item_select.hasClass('ajax-search')) {
                    var group = item_select.find('[data-group-id="' + response.item.group_id + '"]');
                    if (group.length == 0) {
                        var _option = '<optgroup label="' + (response.item.group_name == null ? '' : response.item.group_name) + '" data-group-id="' + response.item.group_id + '">' + _option + '</optgroup>';
                        if (item_select.find('[data-group-id="0"]').length == 0) {
                            item_select.find('option:first-child').after(_option);
                        } else {
                            item_select.find('[data-group-id="0"]').after(_option);
                        }
                    } else {
                        group.prepend('<option data-subtext="' + response.item.long_description + '" value="' + response.item.itemid + '">(' + accounting.formatNumber(response.item.rate) + ') ' + response.item.description + '</option>');
                    }
                }
                if (!item_select.hasClass('ajax-search')) {
                    item_select.selectpicker('refresh');
                } else {

                    item_select.contents().filter(function () {
                        return !$(this).is('.newitem') && !$(this).is('.newitem-divider');
                    }).remove();

                    var clonedItemsAjaxSearchSelect = item_select.clone();
                    item_select.selectpicker('destroy').remove();
                    $("body").find('.items-select-wrapper').append(clonedItemsAjaxSearchSelect);
                    init_ajax_search('items', '#item_select.ajax-search', undefined, admin_url + 'items/search');
                }

                add_item_to_preview(response.item.itemid);
            } else {
                // Is general items view
                $('.table-invoice-items').DataTable().ajax.reload(null, false);
            }
            alert_float('success', response.message);
        }
        $('#sales_item_modal').modal('hide');
    }).fail(function (data) {
        alert_float('danger', data.responseText);
    });
    return false;
}
// BOF VK, Incoterms column
// VK Mod: Add
var freights_list = [];

<?php if (isset($freights) && count($freights) > 0) { ?>
freights_list = <?php echo json_encode($freights); ?>;
<?php } ?>


function get_item_incoterms_preview_values() {
    var response = {};
    response.reference_item_id = $('.main input[name="reference_item_id"]').val();
    response.description = $('.main textarea[name="description"]').val();
    response.long_description = $('.main textarea[name="long_description"]').val();
    response.qty = $('.main input[name="quantity"]').val();
    response.limitQty = $('.main input[name="quantity"]').attr('data-limitQty');
    
    response.taxname = $(".main select.tax").selectpicker("val");
    response.rate = $('.main input[name="rate"]').val();
    response.unit = $('.main input[name="unit"]').val();

    response.freights = [];

    // Incoterms column data
    if (freights_list.length > 0) {
        $.each(freights_list, function (fcolumn, fvalue) {
            // console.log(fvalue);
            let rowVal = {};
            rowVal.id = fvalue.id;
            rowVal.fob_fcl_20 = $('.main input.fi'+ fvalue.id +'_rate_fob_fcl_20').val();
            rowVal.fob_fcl_40 = $('.main input.fi'+ fvalue.id +'_rate_fob_fcl_40').val();
            rowVal.fob_air = $('.main input.fi'+ fvalue.id +'_rate_fob_air').val();
            rowVal.cfr_fcl_20 = $('.main input.fi'+ fvalue.id +'_rate_cfr_fcl_20').val();
            rowVal.cfr_fcl_40 = $('.main input.fi'+ fvalue.id +'_rate_cfr_fcl_40').val();
            rowVal.cfr_air = $('.main input.fi'+ fvalue.id +'_rate_cfr_air').val();

            response.freights.push(rowVal);
        });
    }

  return response;
}

function add_item_to_incoterms_preview(id) {
  requestGetJSON("invoice_items/get_item_by_id/" + id).done(function (
    response
  ) {
    clear_item_incoterms_preview_values();
    console.log('myPreview', response, response.rate_prod);
    $('.main input[name="reference_item_id"]').val(response.itemid);
    $('.main textarea[name="description"]').val(response.description);
    $('.main textarea[name="long_description"]').val(
      response.long_description.replace(/(<|&lt;)br\s*\/*(>|&gt;)/g, " ")
    );

    _set_item_preview_custom_fields_array(response.custom_fields);
    var autoRate = $('input[name="proposal_to"]').attr('data-autorate');
    if(!autoRate){
      autoRate = 0;
    }else{
      response.rate = Math.round(parseFloat(response.rate) * (1 - parseFloat(autoRate)/100)).toFixed(2);
    }


    $('.main input[name="quantity"]').val(1);
    if(response.totalQuantity.totalQuantity < response.limitQty){
      $( '.main input[name="quantity"]' ).attr( "data-limitQty", response.limitQty );
      alert("Stock is below "+response.limitQty);
    }else{
      $( '.main input[name="quantity"]' ).attr( "data-limitQty", 0 );
    }
    
    
    var taxSelectedArray = [];
    if (response.taxname && response.taxrate) {
      taxSelectedArray.push(response.taxname + "|" + response.taxrate);
    }
    if (response.taxname_2 && response.taxrate_2) {
      taxSelectedArray.push(response.taxname_2 + "|" + response.taxrate_2);
    }

    $(".main select.tax").selectpicker("val", taxSelectedArray);
    $('.main input[name="unit"]').val(response.unit);
    $('.main input[name="production_ratio"]').val(response.rate_prod);
    var $currency = $("body").find(
      '.accounting-template select[name="currency"]'
    );
    var baseCurency = $currency.attr("data-base");
    var selectedCurrency = $currency.find("option:selected").val();
    var $rateInputPreview = $('.main input[name="rate"]');

    if (baseCurency == selectedCurrency) {
      $rateInputPreview.val(response.rate);
    } else {
      // BOF VK, Allow to change currency
      // VK Mod: Add
      if ($('input[name="exchange_currency"]').val() != '' && parseFloat($('input[name="exchange_currency"]').val()) > 0) {
        response.rate = Math.round(parseFloat(response.rate) * parseFloat($('input[name="exchange_currency"]').val())).toFixed(2);
        //var rateVal = response.rate * (1 - parseFloat(autoRate)/100)
        //console.log(rateVal, parseFloat(autoRate));
        $rateInputPreview.val(rateVal);
      } else {
        var itemCurrencyRate = response["rate_currency_" + selectedCurrency];
        if (!itemCurrencyRate || parseFloat(itemCurrencyRate) === 0) {
          $rateInputPreview.val(response.rate);
        } else {
          var rateVal = itemCurrencyRate * (1 - parseFloat(autoRate)/100)
          console.log(rateVal, parseFloat(autoRate));
          $rateInputPreview.val(rateVal);
        }
      }
    }

    // Incoterms column data
    if (freights_list.length > 0) {
        $.each(freights_list, function (fcolumn, fvalue) {
            console.log(fvalue, response);
            $('.main input.fi'+ fvalue.id +'_rate_fob_fcl_20').val(response.rate);
            $('.main input.fi'+ fvalue.id +'_rate_fob_fcl_40').val(response.rate);
            $('.main input.fi'+ fvalue.id +'_rate_fob_air').val(response.rate);

            let cfr_fcl_20 = parseFloat(response.rate);
            if (parseFloat(fvalue.fcl_20) > 0 && parseFloat(response.fcl_20_container) > 0) {
                cfr_fcl_20 = cfr_fcl_20 + (parseFloat(fvalue.fcl_20)/parseFloat(response.fcl_20_container));
            }

            $('.main input.fi'+ fvalue.id +'_rate_cfr_fcl_20').val(Math.round(cfr_fcl_20).toFixed(2));

            let cfr_fcl_40 = parseFloat(response.rate);
            if (parseFloat(fvalue.fcl_40) > 0 && parseFloat(response.fcl_40_container) > 0) {
                cfr_fcl_40 = cfr_fcl_40 + (parseFloat(fvalue.fcl_40)/parseFloat(response.fcl_40_container));
            }

            $('.main input.fi'+ fvalue.id +'_rate_cfr_fcl_40').val(Math.round(cfr_fcl_40).toFixed(2));

            let cfr_air = parseFloat(response.rate);
            if (parseFloat(fvalue.air) > 0 && parseFloat(response.air_container) > 0) {
                cfr_air = cfr_air + (parseFloat(fvalue.air)/parseFloat(response.air_container));
            }

            $('.main input.fi'+ fvalue.id +'_rate_cfr_air').val(Math.round(cfr_air).toFixed(2));
        });
    }

    $(document).trigger({
      type: "item-added-to-preview",
      item: response,
      item_type: "item",
    });
  });
}


// Clear the items added to preview
function clear_item_incoterms_preview_values(default_taxes) {
  // Get the last taxes applied to be available for the next item
  var last_taxes_applied = $("table.items tbody")
    .find("tr:last-child")
    .find("select")
    .selectpicker("val");
  var previewArea = $(".main");

  previewArea.find("textarea").val(""); // includes cf
  previewArea
    .find('td.custom_field input[type="checkbox"]')
    .prop("checked", false); // cf
  previewArea.find("td.custom_field input:not(:checkbox):not(:hidden)").val(""); // cf // not hidden for chkbox hidden helpers
  previewArea.find("td.custom_field select").selectpicker("val", ""); // cf
  previewArea.find('input[name="quantity"]').val(1);
  previewArea.find("select.tax").selectpicker("val", last_taxes_applied);
  previewArea.find('input[name="rate"]').val("");
  previewArea.find('input[name="unit"]').val("");
  previewArea.find('.incoterms-column input').val("");

  $('input[name="task_id"]').val("");
  $('input[name="expense_id"]').val("");
}

function add_item_to_incoterms_table(data, itemid, merge_invoice, bill_expense) {
  // If not custom data passed get from the preview
  data =
    typeof data == "undefined" || data == "undefined"
      ? get_item_incoterms_preview_values()
      : data;
  if (
    data.description === "" &&
    data.long_description === "" &&
    data.rate === ""
  ) {
    return;
  }

  var table_row = "";
  var item_key = lastAddedItemKey
    ? (lastAddedItemKey += 1)
    : $("body").find("tbody .item").length + 1;
  lastAddedItemKey = item_key;
  console.log('addedItem', data);
  table_row +=
    '<tr class="sortable item" data-merge-invoice="' +
    merge_invoice +
    '" data-bill-expense="' +
    bill_expense +
    '" fcl-counter="'+data.fcl_20_container+'">';

  table_row += '<td class="dragger">';

  // Check if quantity is number
  if (isNaN(data.qty)) {
    data.qty = 1;
  }

  // Check if rate is number
  if (data.rate === "" || isNaN(data.rate)) {
    data.rate = 0;
  }

  var amount = data.rate * data.qty;

  var tax_name = "newitems[" + item_key + "][taxname][]";
  $("body").append('<div class="dt-loader"></div>');
  var regex = /<br[^>]*>/gi;
  get_taxes_dropdown_template(tax_name, data.taxname).done(function (
    tax_dropdown
  ) {
    // order input
    table_row +=
      '<input type="hidden" class="order" name="newitems[' +
      item_key +
      '][order]">';

    table_row += "</td>";

    table_row +=
      '<td class="bold description"><input type="hidden" name="newitems[' +
      item_key +
      '][reference_item_id]" value="' + data.reference_item_id + '"><textarea name="newitems[' +
      item_key +
      '][description]" class="form-control" rows="5">' +
      data.description +
      "</textarea></td>";

    table_row +=
      '<td><textarea name="newitems[' +
      item_key +
      '][long_description]" class="form-control item_long_description" rows="5">' +
      data.long_description.replace(regex, "\n") +
      "</textarea></td>";

    var custom_fields = $("tr.main td.custom_field");
    var cf_has_required = false;

    if (custom_fields.length > 0) {
      $.each(custom_fields, function () {
        var cf = $(this).clone();
        var cf_html = "";
        var cf_field = $(this).find("[data-fieldid]");
        var cf_name =
          "newitems[" +
          item_key +
          "][custom_fields][items][" +
          cf_field.attr("data-fieldid") +
          "]";

        if (cf_field.is(":checkbox")) {
          var checked = $(this).find('input[type="checkbox"]:checked');
          var checkboxes = cf.find('input[type="checkbox"]');

          $.each(checkboxes, function (i, e) {
            var random_key = Math.random().toString(20).slice(2);
            $(this)
              .attr("id", random_key)
              .attr("name", cf_name)
              .next("label")
              .attr("for", random_key);
            if ($(this).attr("data-custom-field-required") == "1") {
              cf_has_required = true;
            }
          });

          $.each(checked, function (i, e) {
            cf.find('input[value="' + $(e).val() + '"]').attr("checked", true);
          });

          cf_html = cf.html();
        } else if (cf_field.is("input") || cf_field.is("textarea")) {
          if (cf_field.is("input")) {
            cf.find("[data-fieldid]").attr("value", cf_field.val());
          } else {
            cf.find("[data-fieldid]").html(cf_field.val());
          }
          cf.find("[data-fieldid]").attr("name", cf_name);
          if (
            cf.find("[data-fieldid]").attr("data-custom-field-required") == "1"
          ) {
            cf_has_required = true;
          }
          cf_html = cf.html();
        } else if (cf_field.is("select")) {
          if ($(this).attr("data-custom-field-required") == "1") {
            cf_has_required = true;
          }

          var selected = $(this)
            .find("select[data-fieldid]")
            .selectpicker("val");
          selected = typeof (selected != "array")
            ? new Array(selected)
            : selected;

          // Check if is multidimensional by multi-select customfield
          selected = selected[0].constructor === Array ? selected[0] : selected;

          var selectNow = cf.find("select");
          var $wrapper = $("<div/>");
          selectNow.attr("name", cf_name);

          var $select = selectNow.clone();
          $wrapper.append($select);
          $.each(selected, function (i, e) {
            $wrapper
              .find('select option[value="' + e + '"]')
              .attr("selected", true);
          });

          cf_html = $wrapper.html();
        }
        table_row += '<td class="custom_field">' + cf_html + "</td>";
      });
    }
    console.log('My adding item', data);
    table_row +=
      '<td><input type="number" min="0" onblur="calculate_total();" onchange="calculate_total();" data-quantity name="newitems[' +
      item_key +
      '][qty]" value="' +
      data.qty +
      '" class="form-control" data-limitQty="'+data.limitQty+'">';

    if (!data.unit || typeof data.unit == "undefined") {
      data.unit = "";
    }

    table_row +=
      '<input type="text" placeholder="' +
      app.lang.unit +
      '" name="newitems[' +
      item_key +
      '][unit]" class="form-control input-transparent text-right" value="' +
      data.unit +
      '">';

    table_row += "</td>";

    let default_rate_column = '';
    /*if ($('#fob_port').val().length > 0) {
        default_rate_column = ' hide';
    }*/

    table_row +=
      '<td class="default-rate-column rate' + default_rate_column + '"><input type="number" data-toggle="tooltip" title="' +
      app.lang.item_field_not_formatted +
      '" onblur="calculate_total();" onchange="calculate_total();" name="newitems[' +
      item_key +
      '][rate]" value="' +
      data.rate +
      '" class="form-control"></td>';

    // Incoterms column data
    if (freights_list.length > 0) {
        let container_fcl_20 = $('#container_type_fcl_20').is(':checked');
        let container_fcl_40 = $('#container_type_fcl_40').is(':checked');
        let container_air = $('#container_type_air').is(':checked');
        let fob_ports = $('#fob_port').val();
        let cfr_ports = $('#cfr_port').val();

        $.each(freights_list, function (fcolumn, fvalue) {
            let fob_col_fcl_20 = (container_fcl_20 && (fob_ports.indexOf(fvalue.id) != -1)) ? '' : ' hide';
            let fob_col_fcl_40 = (container_fcl_40 && (fob_ports.indexOf(fvalue.id) != -1)) ? '' : ' hide';
            let fob_col_air = (container_air && (fob_ports.indexOf(fvalue.id) != -1)) ? '' : ' hide';

            var freightData = data.freights.find(({ id }) => id === fvalue.id);
            table_row +=
              '<td class="incoterms-column incoterms-column-fob incoterms-column-fcl-20 fob-port-'+fvalue.id+'' + fob_col_fcl_20 + '"><input type="number"  name="newitems[' +
              item_key +
              '][incoterms][' + fvalue.id + '][rate_fob_fcl_20]" value="' +
              freightData.fob_fcl_20 +
              '" class="form-control fi' + fvalue.id + '_rate_fob_fcl_20"></td>';
            table_row +=
              '<td class="incoterms-column incoterms-column-fob incoterms-column-fcl-40 fob-port-'+fvalue.id+'' + fob_col_fcl_40 + '"><input type="number"  name="newitems[' +
              item_key +
              '][incoterms][' + fvalue.id + '][rate_fob_fcl_40]" value="' +
              freightData.fob_fcl_40 +
              '" class="form-control fi' + fvalue.id + '_rate_fob_fcl_40"></td>';
            table_row +=
              '<td class="incoterms-column incoterms-column-fob incoterms-column-air fob-port-'+fvalue.id+''+ fob_col_air +'"><input type="number"  name="newitems[' +
              item_key +
              '][incoterms][' + fvalue.id + '][rate_fob_air]" value="' +
              freightData.fob_air +
              '" class="form-control fi' + fvalue.id + '_rate_fob_air"></td>';
            //
            let cfr_col_fcl_20 = (container_fcl_20 && (cfr_ports.indexOf(fvalue.id) != -1)) ? '' : ' hide';
            let cfr_col_fcl_40 = (container_fcl_40 && (cfr_ports.indexOf(fvalue.id) != -1)) ? '' : ' hide';
            let cfr_col_air = (container_air && (cfr_ports.indexOf(fvalue.id) != -1)) ? '' : ' hide';
            table_row +=
              '<td class="incoterms-column incoterms-column-cfr incoterms-column-fcl-20 cfr-port-'+fvalue.id+'' + cfr_col_fcl_20 + '"><input type="number"  name="newitems[' +
              item_key +
              '][incoterms][' + fvalue.id + '][rate_cfr_fcl_20]" value="' +
              freightData.cfr_fcl_20 +
              '" class="form-control fi' + fvalue.id + '_rate_cfr_fcl_20"></td>';
            table_row +=
              '<td class="incoterms-column incoterms-column-cfr incoterms-column-fcl-40 cfr-port-'+fvalue.id+'' + cfr_col_fcl_40 + '"><input type="number"  name="newitems[' +
              item_key +
              '][incoterms][' + fvalue.id + '][rate_cfr_fcl_40]" value="' +
              freightData.cfr_fcl_40 +
              '" class="form-control fi' + fvalue.id + '_rate_cfr_fcl_40"></td>';
            table_row +=
              '<td class="incoterms-column incoterms-column-cfr incoterms-column-air cfr-port-'+fvalue.id+''+ cfr_col_air +'"><input type="number"  name="newitems[' +
              item_key +
              '][incoterms][' + fvalue.id + '][rate_cfr_air]" value="' +
              freightData.cfr_air +
              '" class="form-control fi' + fvalue.id + '_rate_cfr_air"></td>';
        });
    }

    table_row += '<td class="taxrate hide">' + tax_dropdown + "</td>";

    table_row +=
      '<td class="amount hide" align="right">' +
      format_money(amount, true) +
      "</td>";

    table_row +=
      '<td><a href="#" class="btn btn-danger pull-left" onclick="delete_item(this,' +
      itemid +
      '); return false;"><i class="fa fa-trash"></i></a></td>';

    table_row += "</tr>";

    $("table.items tbody").append(table_row);

    $(document).trigger({
      type: "item-added-to-table",
      data: data,
      row: table_row,
    });

    setTimeout(function () {
      calculate_total();
    }, 15);

    var billed_task = $('input[name="task_id"]').val();
    var billed_expense = $('input[name="expense_id"]').val();

    if (billed_task !== "" && typeof billed_task != "undefined") {
      billed_tasks = billed_task.split(",");
      $.each(billed_tasks, function (i, obj) {
        $("#billed-tasks").append(
          hidden_input("billed_tasks[" + item_key + "][]", obj)
        );
      });
    }

    if (billed_expense !== "" && typeof billed_expense != "undefined") {
      billed_expenses = billed_expense.split(",");
      $.each(billed_expenses, function (i, obj) {
        $("#billed-expenses").append(
          hidden_input("billed_expenses[" + item_key + "][]", obj)
        );
      });
    }

    if (
      $("#item_select").hasClass("ajax-search") &&
      $("#item_select").selectpicker("val") !== ""
    ) {
      $("#item_select").prepend("<option></option>");
    }

    init_selectpicker();
    init_datepicker();
    init_color_pickers();
    clear_item_incoterms_preview_values();
    reorder_items();

    $("body").find("#items-warning").remove();
    $("body").find(".dt-loader").remove();
    $("#item_select").selectpicker("val", "");

    if (cf_has_required && $(".invoice-form").length) {
      validate_invoice_form();
    } else if (cf_has_required && $(".estimate-form").length) {
      validate_estimate_form();
    } else if (cf_has_required && $(".proposal-form").length) {
      validate_proposal_form();
    } else if (cf_has_required && $(".credit-note-form").length) {
      validate_credit_note_form();
    }

    if (bill_expense == "undefined" || !bill_expense) {
      $('select[name="task_select"]')
        .find('[value="' + billed_task + '"]')
        .remove();
      $('select[name="task_select"]').selectpicker("refresh");
    }
    return true;
  });

  return false;
}

// EOF VK, Incoterms column
function init_item_js() {
     // Add item to preview from the dropdown for invoices estimates
    $("body").on('change', 'select[name="item_select"]', function () {
        var itemid = $(this).selectpicker('val');
        if (itemid != '') {
            // BOF VK, Incoterms column
            // VK Mod: Replace
            // Add item to preview
            // add_item_to_preview(itemid);
            add_item_to_incoterms_preview(itemid);
        }
    });
    console.log('viewItems', location);
    if(location.hash && pathname == "/admin/invoice_items"){
      var initId = location.hash.replace('#', '');
      $('.affect-warning').addClass('hide');
      
      var $itemModal = $('#sales_item_modal');
      $('input[name="itemid"]').val('');
      $itemModal.find('input').not('input[type="hidden"]').val('');
      $itemModal.find('textarea').val('');
      $itemModal.find('select').selectpicker('val', '').selectpicker('refresh');
      $('select[name="tax2"]').selectpicker('val', '').change();
      $('select[name="tax"]').selectpicker('val', '').change();
      $itemModal.find('.add-title').removeClass('hide');
      $itemModal.find('.edit-title').addClass('hide');
      requestGetJSON('invoice_items/get_item_by_id/' + initId).done(function (response) {
        console.log('loaded ItemData', response);
          $itemModal.find('input[name="prod_link"]').val(response.prod_link);
          $itemModal.find('input[name="description"]').val(response.description);
          $itemModal.find('textarea[name="long_description"]').val(response.long_description.replace(/(<|<)br\s*\/*(>|>)/g, " "));
          $itemModal.find('input[name="rate"]').val(response.rate);
          $itemModal.find('input[name="unit"]').val(response.unit);
          $('select[name="tax"]').selectpicker('val', response.taxid).change();
          $('select[name="tax2"]').selectpicker('val', response.taxid_2).change();
          $itemModal.find('#group_id').selectpicker('val', response.group_id);
          $itemModal.find('#subgroup_id').selectpicker('val', response.subgroup_id);
          $.each(response, function (column, value) {
              if (column.indexOf('rate_currency_') > -1) {
                  $itemModal.find('input[name="' + column + '"]').val(value);
              }
          });

          // BOF VK, Add three fields
          // VK Mod: Add
          console.log('items', response);
          $itemModal.find('input[name="production_ratio"]').val(response.production_ratio);
          $itemModal.find('input[name="fcl_20_container"]').val(response.fcl_20_container);
          $itemModal.find('input[name="fcl_40_container"]').val(response.fcl_40_container);
          $itemModal.find('input[name="air_container"]').val(response.air_container);
          // EOF VK, Add three fields

          $('#custom_fields_items').html(response.custom_fields_html);

          init_selectpicker();
          init_color_pickers();
          init_datepicker();

          $itemModal.find('.add-title').addClass('hide');
          $itemModal.find('.edit-title').removeClass('hide');
          validate_item_form();
      });
      $itemModal.modal('show');
     // $('#sales_item_modal').click();
    }
    // Items modal show action
    $("body").on('show.bs.modal', '#sales_item_modal', function (event) {

        $('.affect-warning').addClass('hide');

        var $itemModal = $('#sales_item_modal');
        $('input[name="itemid"]').val('');
        $itemModal.find('input').not('input[type="hidden"]').val('');
        $itemModal.find('textarea').val('');
        $itemModal.find('select').selectpicker('val', '').selectpicker('refresh');
        $('select[name="tax2"]').selectpicker('val', '').change();
        $('select[name="tax"]').selectpicker('val', '').change();
        $itemModal.find('.add-title').removeClass('hide');
        $itemModal.find('.edit-title').addClass('hide');

        var id = $(event.relatedTarget).data('id');
        // If id found get the text from the datatable
        if (typeof (id) !== 'undefined') {

            $('.affect-warning').removeClass('hide');
            $('input[name="itemid"]').val(id);

            requestGetJSON('invoice_items/get_item_by_id/' + id).done(function (response) {
                $itemModal.find('input[name="prod_link"]').val(response.prod_link);
                $itemModal.find('input[name="description"]').val(response.description);
                $itemModal.find('textarea[name="long_description"]').val(response.long_description.replace(/(<|<)br\s*\/*(>|>)/g, " "));
                $itemModal.find('input[name="rate"]').val(response.rate);
                $itemModal.find('input[name="unit"]').val(response.unit);
                $('select[name="tax"]').selectpicker('val', response.taxid).change();
                $('select[name="tax2"]').selectpicker('val', response.taxid_2).change();
                $itemModal.find('#group_id').selectpicker('val', response.group_id);
                $itemModal.find('#subgroup_id').selectpicker('val', response.subgroup_id);
                $.each(response, function (column, value) {
                    if (column.indexOf('rate_currency_') > -1) {
                        $itemModal.find('input[name="' + column + '"]').val(value);
                    }
                });

                // BOF VK, Add three fields
                // VK Mod: Add
                console.log('items', response);
                $itemModal.find('input[name="production_ratio"]').val(response.production_ratio);
                $itemModal.find('input[name="fcl_20_container"]').val(response.fcl_20_container);
                $itemModal.find('input[name="fcl_40_container"]').val(response.fcl_40_container);
                $itemModal.find('input[name="air_container"]').val(response.air_container);
                // EOF VK, Add three fields

                $('#custom_fields_items').html(response.custom_fields_html);

                init_selectpicker();
                init_color_pickers();
                init_datepicker();

                $itemModal.find('.add-title').addClass('hide');
                $itemModal.find('.edit-title').removeClass('hide');
                validate_item_form();
            });

        }
    });

    $("body").on("hidden.bs.modal", '#sales_item_modal', function (event) {
        $('#item_select').selectpicker('val', '');
    });

   validate_item_form();
}
function validate_item_form(){
    // Set validation for invoice item form
    appValidateForm($('#invoice_item_form'), {
        description: 'required',
        rate: {
            required: true,
        }
    }, manage_invoice_items);
}
</script>
