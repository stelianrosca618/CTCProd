<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12 tw-mb-2">
                <button class="btn btn-md btn-primary" onclick="openNewSalesModal()">Add Past Sale</button>
                <label for="xlxpicker" class="btn btn-md btn-primary">
                    Add Past Sale with XLX
                    <input type="file" id="xlxpicker" style="display: none;" accept=".csv, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel"/>
                </label>
                
                <!-- <button class="btn btn-md btn-primary" onclick="initSales()">init Past Sale</button> -->
            </div>
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                    <table class="table past-sales-table">
                    
                    </table>
                    </div>
                </div>
                
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="pastSales_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button group="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">
                    <span class="edit-title"><?php echo _l('Edit Past Sales'); ?></span>
                    <span class="add-title"><?php echo _l('New Past Sales'); ?></span>
                </h4>
            </div>
            <?php echo form_open('admin/pastsales/addSales'); ?>
            <div class="modal-body">
                <input type="hidden" name="id">
                <div class="row">
                    <div class="col-md-12">
                        <?php echo render_select('company', $companies, ['company', 'company'], 'Company')?>
                    </div>
                    <div class="col-md-12">
                        <?php echo render_date_input('date', 'Date') ?>
                    </div>
                    <div class="col-md-12">
                        <?php echo render_select('Product', $items, ['id', 'description'], 'Product') ?>
                    </div>
                    <div class="col-md-6">
                        <?php echo render_input('Quantity', 'Quantity', '', 'float') ?>
                    </div>
                    <div class="col-md-6">
                        <?php echo render_input('Price', 'Price', '', 'float') ?>
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

<?php init_tail(); ?>
<script>
    function initSales(){
        $.get(admin_url + 'pastsales/initPastSales/');
    }
let salesData = <?php echo json_encode($pastsales)?>;
console.log('pastSales', salesData);
let temp = Intl.NumberFormat("en-US",{
    style: "decimal",
    useGrouping: true,
});
const columns = [
                { title: "Company", data: 'company' },
                { title: "Product", data: 'ProdName' },
                { title: "Date", data: 'date' },
                { title: "Quantity", data: 'Quantity',
                    render: function (data, type){
                        return (Math.round(data * 100) / 100).toFixed(2);
                    }
                },
                { title: "Price", data: 'Price',
                    render: function (data, type){
                        return temp.format((Math.round(data * 100) / 100).toFixed(2));
                    }
                },
                { title: "Action", data: 'id',
                    render: function (data, type) {
                        return `<button class="btn btn-sm btn-primary" onclick="openEditSalesModal(${data})">Edit</button><button class="btn btn-sm btn-danger" onclick="removePastSale(${data})">Delete</button>`
                    }
                },
        ]
$('.past-sales-table').dataTable({
    "responsive" : true,
    "loading": false,
    "processing" : false,
    "stateSave" : false,
    "data": salesData,
    "columns":columns,
    "initComplete": function(settings, json) {
						console.log('DataTable', json);
						$('.dataTables_wrapper').removeClass('table-loading');
					}
})
$("#xlxpicker").change(function (e) {
    console.log('loaded event', e);
    //this.parseExcel = function(file) {
    var file = e.target.files[0];
    var reader = new FileReader();

    reader.onload = function(e) {
      var data = e.target.result;
      var workbook = XLSX.read(data, {
        type: 'binary'
      });

      workbook.SheetNames.forEach(function(sheetName) {
        // Here is your object
        var XL_row_object = XLSX.utils.sheet_to_row_object_array(workbook.Sheets[sheetName]);
        var json_object = JSON.parse(JSON.stringify(XL_row_object));
        console.log(json_object);
        $.post(admin_url + 'pastsales/uploadPastsales/', {pastSales: json_object}).then(res => {
            console.log(res);
            window.location.reload();
        });
      })

    };

    reader.onerror = function(ex) {
      console.log(ex);
    };

    reader.readAsBinaryString(file);
    //}
});
function removePastSale(id){
    $.get(admin_url + 'pastsales/removeSales/'+id).then(res => {
        window.location.reload();
    });
}
function openEditSalesModal(id){
    $.get(admin_url + 'pastsales/getSales/'+id).then(res => {
        var response = JSON.parse(res);
        console.log(response);
        if(response){
            $('input[name="id"]').val(response[0].id).trigger('change');
            $('select[name="company"]').val(response[0].company).trigger('change');
            $('select[name="Product"]').val(response[0].Product).trigger('change');
            $('input[name="date"]').val(response[0].date).trigger('change');
            $('input[name="Quantity"]').val(response[0].Quantity).trigger('change');
            $('input[name="Price"]').val(response[0].Price).trigger('change');
            $('#pastSales_modal').modal('show');
        }
    });
    
}
function openNewSalesModal(){
    $('input[name="id"]').val('').trigger('change');
    $('select[name="company"]').val('').trigger('change');
    $('select[name="Product"]').val('').trigger('change');
    $('input[name="date"]').val('').trigger('change');
    $('input[name="Quantity"]').val('').trigger('change');
    $('input[name="Price"]').val('').trigger('change');
    $('#pastSales_modal').modal('show');
}
</script>
</body>

</html>