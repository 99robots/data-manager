<?php $data_manager = new NNR_Data_Manager_v1( self::$data_manager_table_name ); ?>

<!-- Show based on status -->

<a class="<?php echo ( !isset( $_GET['status'] ) ? 'nnr-current-tab' : 'nnr-tab' ); ?>" href="<?php echo admin_url() . 'admin.php?page=' . self::$dashboard_page; ?>"> <?php _e('All', self::$text_domain); ?> <span class="<?php echo self::$prefix_dash . 'count'; ?>">(<?php echo count($data_manager->get_data()); ?>)</span></a> |
<a class="<?php echo ( isset( $_GET['status'] ) && $_GET['status'] == 'active' ? 'nnr-current-tab' : 'nnr-tab' ); ?>" href="<?php echo admin_url() . 'admin.php?page=' . self::$dashboard_page; ?>&status=active"> <?php _e('Active', self::$text_domain); ?> <span class="<?php echo self::$prefix_dash . 'count'; ?>">(<?php echo count($data_manager->get_active_data()); ?>)</span></a> |
<a class="<?php echo ( isset( $_GET['status'] ) && $_GET['status'] == 'inactive' ? 'nnr-current-tab' : 'nnr-tab' ); ?>" href="<?php echo admin_url() . 'admin.php?page=' . self::$dashboard_page; ?>&status=inactive"> <?php _e('Inactive', self::$text_domain); ?> <span class="<?php echo self::$prefix_dash . 'count'; ?>">(<?php echo count($data_manager->get_inactive_data()); ?>)</span></a>

<!-- Show the table -->

<form method="GET">
	<?php
	$data_manager_table = new NNR_Data_Manager_List_Table_v1( self::$data_manager_table_name, array(
		'prefix'			=> self::$prefix_dash,
		'text_domain'		=> self::$text_domain,
		'dashboard_page'	=> self::$dashboard_page,
		'add_edit_page'		=> self::$add_edit_page,
		'stats_page'		=> self::$stats_page,
		'stats_table_name'	=> self::$stats_table_name,
	) );
	$data_manager_table->prepare_items();
	$data_manager_table->display();
	?>
</form>

<!-- Modal Popup for the delete confirmation -->

<div class="modal fade" id="nnr-delete" tabindex="-3" role="dialog">
	<div class="modal-dialog" style="margin-top: 20vh;">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
				<h4 class="modal-title"><?php _e('Delete', self:: $text_domain); ?></h4>
			</div>
			<div class="modal-body"></div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal"><?php _e('Cancel', self:: $text_domain); ?></button>
				<button id="nnr-delete-success" type="button" class="btn btn-danger"><?php _e('Delete', self:: $text_domain); ?></button>
			</div>
		</div><!-- /.modal-content -->
	</div><!-- /.modal-dialog -->
</div><!-- /.modal -->