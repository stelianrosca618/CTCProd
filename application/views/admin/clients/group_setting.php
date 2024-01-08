<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div class="modal fade" id="group_setting_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
          
            <div class="modal-header">
                <button group="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">
                    <span class="edit-title"><?php echo _l('customer_group_edit_heading'); ?></span>
                    <span class="add-title"><?php echo _l('customer_group_add_heading'); ?></span>
                </h4>
            </div>
            <?php echo form_open('admin/clients/updateGroups', ['id' => 'customer-groupRate-modal']); ?>
            <div class="modal-body">
              <div class="row">
                <?php 
                  foreach ($groups as $key => $group) {
                ?>
                    <div class="col-md-12 row">
                      <div class="col-12">
                        <?php echo render_input('group['.$key.'][id]', '', $group['id'], 'hidden', ['readonly' => true]); ?>
                      </div>
                      <div class="col-md-6">
                        <?php echo render_input('group['.$key.'][name]', 'customer_group_name', $group['name'], 'text', ['readonly' => true]); ?>
                      </div>
                      <div class="col-md-6">
                        <?php echo render_input('group['.$key.'][autoRate]', 'customer_group_rate', $group['autoRate'], 'number'); ?>
                      </div>
                    </div>
                <?php
                  }
                ?>
              </div>
            </div>
            <?php echo form_close(); ?>
            <div class="modal-footer">
                <button group="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
                <button group="button" class="btn btn-primary" onclick="editGroupRate()"><?php echo _l('submit'); ?></button>
            </div>
          
        </div>
    </div>
</div>

<script>
  function editGroupRate() {
    const groupRateForm = $('form[id="customer-groupRate-modal"]')[0];
    var data = $(groupRateForm).serialize()
    var url = groupRateForm.action;
    console.log('foormLog,', url, groupRateForm);
    $.post(url, data).done(function(response) {
            response = JSON.parse(response);
            if (response.success == true) {
                alert_float('success', response.message);
            }
            $('#group_setting_modal').modal('hide');
        });
  }
</script>