jQuery( function( $ ){

	$( "#nb-admin-settings-form" ).validate({

		rules: {
			'_nb_plugin_settings[status]': "required",
			'_nb_plugin_settings[theme]': "required",
		}

	});

} );