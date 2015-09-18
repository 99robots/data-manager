jQuery(document).ready(function($){

	// Tooltips

	$('[data-toggle="tooltip"]').tooltip();

	// Delete Data

	$('.nnr-delete').click(function(){
		$('#nnr-delete .modal-body').html('<p>Are you sure you want to delete <strong>' + $(this).data('name') + '</strong> and all of its data?</p>');
		$('#nnr-delete').attr('data-url', $(this).data('url'));
	});

	$('#nnr-delete-success').click(function(){
		window.location = $('#nnr-delete').data('url');
	});

});