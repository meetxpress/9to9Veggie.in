( function( $ ) {

	var wpaasHelp = {

		show: function( e ) {

			e.preventDefault();

			if ( ! $( '.wpaas-help-support iframe' ).length ) {

				var iframe = document.createElement( 'iframe' );

				iframe.classList.add( 'hidden' );

				iframe.onload = function() {

					$( '.wpaas-help-support .preloader' ).remove();
					$( '.wpaas-help-support iframe' ).removeClass( 'hidden' );

				};

				iframe.src = wpaasHelpDocs.url;

				$( '.wpaas-help-support' ).append( iframe );

			}

			$( '.wpaas-help-support' ).addClass( 'visible' );

		},

		close: function( e ) {

			e.preventDefault();

			$( '.wpaas-help-support' ).removeClass( 'visible' );

			if ( window.history.replaceState ) {

				window.history.replaceState( null, document.title, location.href.split( '?' )[0] );

			}

		}

	};

	$( document ).on( 'click', '#wp-admin-bar-wpaas-help-and-support', wpaasHelp.show );

	$( document ).on( 'click', '.wpaas-help-support .close', wpaasHelp.close );

	if ( -1 !== window.location.search.toLowerCase().indexOf( 'wpaas-help=1' ) ) {

		wpaasHelp.show( new Event( 'click', {} ) );

	}

} )( jQuery );
