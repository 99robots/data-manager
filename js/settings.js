jQuery(document).ready(function($){

	// Datetimepicker

	$('#' + nnr_data_manager_data.prefix + 'start-datepicker').datetimepicker({
        format: 'YYYY-MM-DD H:mm:ss',
    });

	$('#' + nnr_data_manager_data.prefix + 'end-datepicker').datetimepicker({
        format: 'YYYY-MM-DD H:mm:ss',
        useCurrent: false,
    });

	$('#' + nnr_data_manager_data.prefix + 'end-datepicker').data("DateTimePicker");

	$('#' + nnr_data_manager_data.prefix + 'start-datepicker').on("dp.change",function (e) {
       $('#' + nnr_data_manager_data.prefix + 'end-datepicker').data("DateTimePicker").minDate(e.date);
    });

    $('#' + nnr_data_manager_data.prefix + 'end-datepicker').on("dp.change",function (e) {
       $('#' + nnr_data_manager_data.prefix + 'start-datepicker').data("DateTimePicker").maxDate(e.date);
    });

});