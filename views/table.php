<form method="GET">
	<?php
	$data_manager_table = new NNR_Data_Manager_List_Table_v1( self::$table_name );
	$data_manager_table->prepare_items();
	$data_manager_table->display();
	?>
</form>