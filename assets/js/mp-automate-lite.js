(function($) {
	/*$(document).ready( function() {
		var row1 = $('#mpa-lite-rules-table tr:first');
    	row1.find('span.remove-automation-rule').hide();
	});*/
	$(document).on('click','#mpa-lite-add-rule', function(e){
		e.preventDefault();
		if($('#single-rule').hasClass("mp-rule-added")){
			if(!$('.upgrade').hasClass("automation-upgrade-error")){
			$('.upgrade').addClass('automation-upgrade-error');
			$('.upgrade').fadeIn();
			}

		} else {
		$('#single-rule').fadeIn();
		$('#single-rule').addClass("mp-rule-added");
		}
	});
	$(document).on('click','span.add-automation-rule', function(e){
		e.preventDefault();
		if(!$('.upgrade').hasClass("automation-upgrade-error")) {
		$('.upgrade').addClass('automation-upgrade-error');
		$('.upgrade').fadeIn();
	}
	});

	$(document).on('click','span.remove-automation-rule', function(e){
		e.preventDefault();
		$(this).closest('tr').hide();
		$('#single-rule').removeClass("mp-rule-added");
	});


	$(document).on('click', '#mpa-lite-save-automation-rules', function(e) {
		e.preventDefault();
		$('.automation-rule-success').fadeOut();
		$('.automation-rule-error').fadeOut();
		$('#mpa-lite-save-automation-rules').attr('disabled', true);
		var self_form = $( '#mpa-lite-rules-form' );
		$.ajax({
			url:mpa_lite.ajaxurl,
			type: "POST",
			data: {action: 'save_mpa_lite_rules', form_data: self_form.serialize() },
			success: function(returned) {
				$('#mpa-lite-save-automation-rules').attr('disabled', false);
				$('.automation-rule-success').fadeIn();
			},
			error: function(xhr){
				$('.automation-rule-error').fadeIn();
			}
		});	
	});

	$(document).on('click', '#mpa-lite-save-log-settings', function(e) {
		e.preventDefault();
		$('.mpa-log-success').fadeOut();
		$('.mpa-log-error').fadeOut();
		$('#mpa-lite-save-log-settings').attr('disabled', true);
		var self_form = $( '#mailpoet-log-form' );
		$.ajax({
			url:mpa_lite.ajaxurl,
			type: "POST",
			data: {action: 'save_mpa_lite_log_settings', form_data: self_form.serialize() },
			success: function(returned) {
				$('#mpa-lite-save-log-settings').attr('disabled', false);
				$('.mpa-log-success').fadeIn();
			},
			error: function(xhr){
				$('.mpa-log-error').fadeIn();
			}
		});	
	});

	$(document).on('click', '#mpa_lite_log_reset', function(e) {
		e.preventDefault();
		$('#mpa-reset-success').fadeOut();
		$('#mpa-reset-success').attr('disabled',true);
		$.ajax({
			url: mpa_lite.ajaxurl,
			type: "POST",
			data: {action: 'reset_mpa_lite_log'},
			success: function(returned) {
				$('#mpa-reset-success').fadeIn();
				$('#mpa-reset-success').attr('disabled',false);
				location.reload();
			},
			error: function(xhr) {

			}
		});
	});
})(jQuery);