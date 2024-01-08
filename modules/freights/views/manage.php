<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row _buttons tw-mb-2 sm:tw-mb-4">
            <div class="col-md-8">
                <?php if (has_permission('freights', '', 'create')) { ?>
                <a href="#" onclick="new_freight(); return false;" class="btn btn-primary pull-left new">
                    <i class="fa-regular fa-plus tw-mr-1"></i>
                    <?php echo _l('new_freight'); ?>
                </a>
                <?php } ?>
            </div>
            <div class="col-md-4">
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                
                <div class="panel_s">
                    <div class="panel-body">
                        <div class="panel-table-full">
                            <?php $this->load->view('_table', ['bulk_actions' => true]); ?>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
<!--Add/edit task modal-->
<div id="_freight"></div>
<?php init_tail(); ?>
<script>
freightid = '<?php echo $freightid; ?>';
</script>
</body>

</html>