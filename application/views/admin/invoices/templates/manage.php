<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12 tw-mb-3">
                <div class="tw-mb-2 sm:tw-mb-4">
                    <div class="_buttons">
                        <button onclick="openNewTemplateModal()" class="btn btn-primary pull-left display-block new-proposal-btn">
                        <i class="fa-regular fa-plus tw-mr-1"></i>
                            New Template
                        </button>
                    </div> 
                </div>
            </div>
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                    <table class="table table-templates"></table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="templateData_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button group="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">
                    <span class="edit-title">Edit Template</span>
                    <span class="add-title">New Template</span>
                </h4>
            </div>
            <?php echo form_open('admin/invoices/addTemplate'); ?>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <?php echo render_select('group', $groups, ['value', 'name'],'Group')?>
                    </div>
                    <div class="col-md-12">
                        <?php echo render_input('name', 'Name'); ?>
                        <?php echo form_hidden('id'); ?>
                    </div>
                    <div class="col-md-12">
                        <?php echo render_textarea('content', 'Content')?>
                    </div>
                    <div class="col-md-12">
                        <?php echo render_yes_no_option('isDefault', 'Is Default')?>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button group="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
                <button group="submit" class="btn btn-primary"><?php echo _l('submit'); ?></button>
                <?php echo form_close(); ?>
            </div>
        </div>
    </div>
</div>
<?php $this->load->view('admin/includes/modals/sales_attach_file'); ?>
<script>
var hidden_columns = [5, 7, 8, 9];
var tableDatas = <?php echo json_encode($templates)?>;
console.log(tableDatas);
function openNewTemplateModal(){
    $('.edit-title').addClass('hide')
    $('.add-title').removeClass('hide');
    $('input[name="id"]').val('');
    $('select[name="group"]').val('').trigger('change');
    $('input[name="name"]').val('');
    $('textarea[name="content"]').val('');
    console.log($('input[name="settings[isDefault]"]')[0]);
    $('input[name="settings[isDefault]"]').prop("checked", false);
    $('#templateData_modal').modal('show');
}
function openTemplateModal(data){
    console.log(data);
    $('.edit-title').removeClass('hide')
    $('.add-title').addClass('hide');
    $.get('getTemplate?id='+data).then((res) => {
        console.log('dsdddd', res);
        var response = JSON.parse(res)[0];
        $('input[name="id"]').val(response.id);
        $('select[name="group"]').val(response.type).trigger('change');
        $('input[name="name"]').val(response.name);
        $('textarea[name="content"]').val(response.content);
        if(response.isDefault == 1){
            $($('input[name="settings[isDefault]"]')[0]).prop("checked", true);
        }else{
            $($('input[name="settings[isDefault]"]')[1]).prop("checked", true);
        }
        $('#templateData_modal').modal('show');
    })
}

</script>
<?php init_tail(); ?>
<script>
const columns = [
                { title: "Id", data: 'id' },
                { title: "Name", data: 'name' },
                { title: "Type", data: 'type' },
                { title: "Action", data: 'id',
                    render: function (data, type) {
                        return `<button class="btn btn-sm btn-primary" onclick="openTemplateModal(${data})">Edit</button>`
                    }
                },
            ];
$('.table-templates').dataTable({
    "responsive" : true,
    "loading": false,
    "processing" : false,
    "stateSave" : false,
    "columnDefs": [
        {
            target: 1,
            visible: false,
            searchable: false
        },
    ],
    "data": tableDatas,
    "columns": columns,
});
$('.dataTables_wrapper').removeClass('table-loading');
// $(function() {
//     init_invoice();
// });
</script>
</body>

</html>