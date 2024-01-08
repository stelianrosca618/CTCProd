<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div class="modal fade proposal-convert-modal" id="convert_to_invoice" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-xxl" role="document">
        <?php echo form_open('admin/proposals/convert_to_invoice/' . $proposal->id, ['id' => 'proposal_convert_to_invoice_form', 'class' => '_transaction_form invoice-form']); ?>
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" onclick="close_modal_manually('#convert_to_invoice')" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title" id="myModalLabel">
                    <span class="edit-title"><?php echo _l('proposal_convert_to_invoice'); ?></span>
                </h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <?php $this->load->view('admin/invoices/invoice_template'); ?>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-default invoice-form-submit save-as-draft transaction-submit">
                    <?php echo _l('save_as_draft'); ?>
                </button>
                <button class="btn btn-primary invoice-form-submit transaction-submit">
                    <?php echo _l('submit'); ?>
                </button>
            </div>
        </div>
        <?php echo form_close(); ?>
    </div>
</div>
<?php $this->load->view('admin/invoice_items/item'); ?>
<script>
    init_ajax_search('customer','#clientid.ajax-search');
    init_ajax_search('items','#item_select.ajax-search',undefined,admin_url+'items/search');
    custom_fields_hyperlink();
    init_selectpicker();
    init_tags_inputs();
    init_datepicker();
    init_color_pickers();
    init_items_sortable();
    validate_invoice_form('#proposal_convert_to_invoice_form');
    <?php if ($proposal->assigned != 0) { ?>
     $('#convert_to_invoice #sale_agent').selectpicker('val',<?php echo $proposal->assigned; ?>);
    <?php } ?>
    $('select[name="discount_type"]').selectpicker('val','<?php echo $proposal->discount_type; ?>');
    $('input[name="discount_percent"]').val('<?php echo $proposal->discount_percent; ?>');
    $('input[name="discount_total"]').val('<?php echo $proposal->discount_total; ?>');
    <?php if (is_sale_discount($proposal, 'fixed')) { ?>
        $('.discount-total-type.discount-type-fixed').click();
    <?php } ?>
    $('input[name="adjustment"]').val('<?php echo $proposal->adjustment; ?>');
    $('input[name="show_quantity_as"][value="<?php echo $proposal->show_quantity_as; ?>"]').prop('checked',true).change();
    <?php if (!isset($project_id) || !$project_id) { ?>
        $('#convert_to_invoice #clientid').change();
    <?php } else { ?>
        $('#convert_to_invoice select#currency').val("<?php echo $proposal->currency ?>").trigger('change');
        init_ajax_project_search_by_customer_id('select#project_id');
    <?php } ?>
    // Trigger item select width fix
    $('#convert_to_invoice').on('shown.bs.modal', function(){
        $('#item_select').trigger('change')
    });
    $("body").on("change", '.itemRate-selector', function(e) {
        console.log('itemtemplate', e.target.options);
        //$('.incoterm-input').addClass('hide');
        $('.incoterm-input').each(function(id, elem){
            elem.classList.add('hide')
            elem.nextElementSibling.classList.add('hide');
        });
        $('.incoterm-qty-input').each(function(id, qEle){
            qEle.classList.add('hide')
            qEle.nextElementSibling.classList.add('hide');
        })
        $('td.rate-port').find('input#rate').each(function(id, rEle){
            rEle.classList.add('hide');
            rEle.nextElementSibling.classList.add('hide');
        })
        $('input[data-quantity]').each(function(id, qEle){
            qEle.classList.add('hide');
        })
        $('.rate-list').each(function(el){
            
            if($(this)[0].tagName == "A" && $(this).hasClass('selected')){
                var interComId = e.target.options[parseInt($(this).attr('aria-posinset')-1)].value;
                if(interComId == 'rate'){
                    console.log('rateposts', $('td.rate-port').find('input#rate'));
                    $('td.rate-port').find('input#rate').each(function(id, rEle){
                        rEle.classList.remove('hide');
                        rEle.nextElementSibling.classList.remove('hide');
                    })
                    $('input[data-quantity]').each(function(id, qEle){
                        qEle.classList.remove('hide');
                    })
                }
                $('.incoterm-input').each(function(id, elem){
                    if(elem.getAttribute('data-label') == interComId){
                        console.log(elem.getAttribute('data-label'))
                        elem.classList.remove('hide')
                        elem.nextElementSibling.classList.remove('hide');
                    }
                });
                $('.incoterm-qty-input').each(function(id, qEle){
                    if(qEle.getAttribute('data-label') == interComId){
                        console.log(qEle.getAttribute('data-label'))
                        qEle.classList.remove('hide')
                        qEle.nextElementSibling.classList.remove('hide');
                    }
                })
                console.log(e.target.options[parseInt($(this).attr('aria-posinset')-1)].value, $('.rate-input'));
            }
        })
        calculate_total();
    })
    $("body").on("click", '.rate-list', function (e) {
        e.preventDefault();
        var dataId = $(this).data('id');
        var dataName = $(this).data('name');
        //$('.rate-name').text(dataName);

        $('.rate-port').each(function(el) {
            var keyId = $( this ).data('key');
            console.log(items_rates[keyId]);
            console.log(dataId);
            console.log(items_rates[keyId][dataId]);
            if (typeof(items_rates[keyId]) != "undefined") {
                $( this ).find('input').val(items_rates[keyId][dataId]);
            }
        });

        setTimeout(function () {
          calculate_total();
        }, 15);
    });
</script>
