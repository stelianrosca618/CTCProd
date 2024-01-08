<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s tw-mt-2 sm:tw-mt-4">
                    <div class="panel-body">
                        <div class="clearfix"></div>
                        <h4 class="tw-mt-0 tw-font-semibold tw-text-lg tw-flex tw-items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                stroke-width="1.5" stroke="currentColor"
                                class="tw-w-5 tw-h-5 tw-text-neutral-500 tw-mr-1.5">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                            </svg>

                            <span>
                                <?php echo _l('Stocks'); ?>
                            </span>
                        </h4>
                        <button class="btn" onclick="makeScotk()">makeStock</button>
                        <table class="table table-stocks">
                            <thead>
                                <tr>
                                    <th><?php echo _l('id'); ?></th>
                                    <th><?php echo _l('Item Group'); ?></th>
                                    <th><?php echo _l('Quantity'); ?></th>
                                    <th><?php echo _l('action'); ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                foreach($items_groups as $itemGroup) {
                                ?>
                                    <tr>
                                        <td><?php echo $itemGroup['id']; ?></td>
                                        <td><?php echo $itemGroup['name']; ?></td>
                                        <td><?php echo number_format((float)$itemGroup['totalQuantity'], 2, '.', ''); ?></td>
                                        <td>
                                            <?php echo "<a href='stocks/stock/".$itemGroup['id']."'><button class='btn btn-sm btn-primary'>View</button></a>"?> 
                                        </td>
                                    </tr>    
                                <?php
                                }
                                ?>
                                
                            </tbody>
                        </table>
                    </div>
                </div>
                
            </div>
        </div>
    </div>
    
</div>
<script>
    function makeScotk(){
        $.get(
			admin_url + 'stocks/makeStock/',
			function (response) {
				console.log(response);
				
			},
			"json"
		);
    }
</script>
<?php $this->load->view('admin/clients/group_setting'); ?>
<?php init_tail(); ?>
</body>

</html>