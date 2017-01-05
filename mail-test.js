jQuery(document).ready( function($) {

	var wp = window.wp;

	$( document.getElementById('send-test') ).on('click', function( event ) {
		event.preventDefault();

		var $result = $( document.getElementById('send-test-result') ),
			email = $( document.getElementById('send-test-email') ).val(),
			$loadingGif = $('<img>');

			$loadingGif = $loadingGif.attr( 'src', mailTest.loadingGif );

		$result.append( $loadingGif );

		wp.ajax.send( mailTest.action, {
			data: {
				email: email,
				nonce: mailTest.nonce
			},
			success: function( data ) {
				// console.log( data );
				$result.text( data.message );
			},
			error: function( data ) {
				// console.log( data );
				$result.text( data.message );
			}

		});
	});

});