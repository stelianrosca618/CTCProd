<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<style>
	.dt-body-indexer{
		display: none;
	}
</style>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s tw-mt-2 sm:tw-mt-4">
                    <div class="panel-body">
                        <div class="clearfix"></div>
                        <div class="row" style="align-items: baseline;">
                            <div class="col-md-6">
                                <h4 class="tw-mt-0 tw-font-semibold tw-text-lg tw-flex tw-items-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke-width="1.5" stroke="currentColor"
                                        class="tw-w-5 tw-h-5 tw-text-neutral-500 tw-mr-1.5">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                                    </svg>

                                    <span>
                                        <?php echo $group->name; ?>
                                    </span>
                                    <?php echo render_input('groupId', '', $group->id, 'hidden')?>
                                </h4>
                            </div>
                            <div class="col-md-6" style="display: flex; align-items: center;justify-content: flex-end;">
                                <div class="form-group tw-mr-3" style="display: flex; align-items:center; margin-bottom: 0;">
                                    <label for="qty_total"
                                        class="control-label tw-mr-1">Total Quantity </label>
                                    <input type="text" class="form-control" style="width: 90px; color: black;" id="qty_total" readonly/>
                                </div>
                                <div class="form-group" style="display: flex; align-items:center; margin-bottom: 0;">
                                    <label for="qty_limit"
                                        class="control-label tw-mr-1">Limit </label>
                                    <input type="text" class="form-control" style="width: 60px;" id="qty_limit" value="<?php echo $group->limitQty?>"/>
                                </div>
																<button class="btn btn-primary tw-ml-1" onclick="openNewStockModal()">Add Stock</button>
                            </div>
                        </div>
                        
                        <div class="col-md-12">
                        <table class="table table-StockData"></table>
                        </div>
                        
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="stock_update_modal" tabindex="-1" role="dialog" aria-labelledby="stockModalLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button group="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
										<h4 class="modal-title" id="stockModalLabel">
											<span class="edit-title">Edit Stock Data</span>
										</h4>
                </div>
								<?php echo form_open('admin/stocks/addStock', ['id' => 'stockUpdate-modal']); ?>
                <div class="modal-body">
									<div class="row">
										<?php echo render_input('itemGroup_id', '', $group->id, 'hidden')?>
										<?php echo render_input('item_id', '', '', 'hidden')?>
										<div class="col-md-6">
											<?php echo render_input('quantity', 'Quantity', '', 'text')?>
										</div>
										<div class="col-md-6">
											<?php echo render_date_input('date', 'Date',  _d(date('Y-m-d')))?>
										</div>
										<div class="col-md-12">
											<div class="form-group">
												<label for="status" class="form-label">Option</label>
												<select id="status" name="status" class="form-control" data-live-search="true" data-max-options="1">
													<option value="invoiced">Invoiced</option>
													<option value="purchase">Purchase</option>
													<option value="adjust">Adjust</option>
												</select>
											</div>
										</div>
										<div class="col-md-12">
											<?php echo render_textarea('Notes', 'Notes', '')?>
										</div>
									</div>
								</div>
								<?php echo form_close(); ?>
                <div class="modal-footer">
									<button class="btn btn-default" data-dismiss="modal">Close</button>
									<button class="btn btn-primary" onclick="updateStockData()" >Update</button>
								</div>
            </div>
        </div>
    </div>
</div>

<?php $this->load->view('admin/clients/group_setting'); ?>
<?php init_tail(); ?>

<script>
	
	//$(function() {
	var grouoID = $('input[name="groupId"]').val();
	
	var grouped = <?php print_r(json_encode($grouped)); ?>;
	console.log(grouped);
	let dataSet = [];


	const columns = [
							{ title: "Quantity", data: 'quantity' },
							{ title: "Date", data: 'date' },
							{ title: "Option", data: 'status' },
							{ title: "Note", data: 'Notes' },
							{ title: "Action", data: 'id',
									render: function (data, type) {
									
									return `<button class="btn btn-sm btn-primary" onclick="openStockModal(${data})">Edit</button>`
							}
							},
					]
	
	const stockTable = $('.table-StockData').dataTable( {
					'ajax': {
							"url":  admin_url + 'stocks/stockData/'+ grouoID,
							"type": "GET",
							"dataSrc": "",
					},
					"order": [[0, 'desc']],
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
					"columns": [
							{title: "ID", data: 'id', searchable: false, visible: false},
							{ title: "Quantity", data: 'quantity' },
							{ title: "Date", data: 'date' },
							{ title: "Option", data: 'status' },
							{ title: "Note", data: 'Notes' },
							{ title: "Action", data: 'id',
									render: function (data, type) {
									
									return `<button class="btn btn-sm btn-primary" onclick="openStockModal(${data})">Edit</button>`
							}
							},
					],
					"initComplete": function(settings, json) {
						console.log('DataTable', json);
						let autoSum = 0;
						json.map(jItem => {
							autoSum += parseFloat(jItem.quantity);
						})
						$('#qty_total').val(autoSum.toFixed(2));
					}
			} );
					
	$('.dataTables_wrapper').removeClass('table-loading');
	function updateStockData(){
		const stockForm = $('form[id="stockUpdate-modal"]')[0];
		var data = $(stockForm).serialize();
		var url = stockForm.action;
		$.post(url, data).done(function(response){
			response = JSON.parse(response);
			console.log(response, stockTable);
			$('#stock_update_modal').modal('hide');
			//stockTable.load();
			$('.table-StockData').DataTable().ajax.reload();
			//stockTable._fnAjaxUpdateDraw();

		})
	}
	$('#qty_limit').on('change', function(e) {
		console.log(e.target.value);
		var payload = {
			id: grouoID,
			limitval: e.target.value
		}
		$.post(admin_url + 'stocks/quantiylimit/', payload, function(response){
			console.log('updated response', response);
		})
	})
	function openNewStockModal(){
		$('input[name="item_id"]').val('');
		$('input[name="quantity"]').val('');
		$('input[name="date"]').val('');
		$('select[name="status"]').val('');
		$('textarea[name="Notes"]').val('');
		$('#stock_update_modal').modal('show')
	}
	function openStockModal(id){
		$.get(
			admin_url + 'stocks/item/' + id,
			function (response) {
				console.log(response);
				$('input[name="item_id"]').val(id);
				$('input[name="quantity"]').val(response.quantity);
				$('input[name="date"]').val(response.date);
				$('select[name="status"]').val(response.status);
				$('textarea[name="Notes"]').val(response.Notes);
				$('#stock_update_modal').modal('show')
			},
			"json"
		);
	}
	//});
</script>
</body>

</html>