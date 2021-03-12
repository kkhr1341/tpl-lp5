
/*--------------------------------------------------------------------------
	
	Script Name : Responsive Mailform
	Author : FIRSTSTEP - Motohiro Tani
	Author URL : https://www.1-firststep.com
	Create Date : 2014/03/25
	Version : 5.4
	Last Update : 2017/08/26
	
--------------------------------------------------------------------------*/


(function( $ ) {
	
	// global variable init
	var mailform_dt    = $( 'form#mail_form dl dt' );
	var start_time     = 0;
	var stop_time      = 0;
	var writing_time   = 0;
	var confirm_window = 1;
	
	
	
	
	// function resize
	function resize() {
		
		$( '.loading-layer' ).css({
			'width' : $( window ).width() + 'px',
			'height' : $( window ).height() + 'px'
		});
		
	}
	
	
	
	
	// function slice_method
	function slice_method( el ) {
		var dt      = el.parents( 'dd' ).prev( 'dt' );
		var dt_name = dt.html().replace( /<span>.*<\/span>/gi, '' );
		dt_name     = dt_name.replace( /^<span\s.*<\/span>/gi, '' );
		dt_name     = dt_name.replace( /<br>|<br \/>/gi, '' );
		return dt_name;
	}
	
	
	
	
	// function error_span
	function error_span( e, dt, comment, bool ) {
		if ( bool == true ) {
			var m = e.parents( 'dd' ).find( 'span.error_blank' ).text( dt + 'が' + comment + 'されていません' );
		} else {
			var m = e.parents( 'dd' ).find( 'span.error_blank' ).text( '' );
		}
	}
	
	
	
	
	// function compare_method
	function compare_method( s, e ) {
		if ( s > e ) {
			return e;
		} else {
			return s;
		}
	}
	
	
	
	
	// function hidden_append
	function hidden_append( name, value ) {
		$( '<input />' )
			.attr({
				type: 'hidden',
				id: name,
				name: name,
				value: value
			})
			.appendTo( $( 'p#form_submit' ) );
	}
	
	
	
	
	// function required_check
	function required_check() {
		
		var error        = 0;
		var scroll_point = $( 'body' ).height();
		
		
		for ( var i = 0; i < mailform_dt.length; i++ ) {
			// required input
			if ( mailform_dt.eq(i).next( 'dd' ).find( 'input' ).length && mailform_dt.eq(i).next( 'dd' ).attr( 'class' ) == 'required' ) {
				
				var elements = mailform_dt.eq(i).next( 'dd' ).find( 'input' );
				var dt_name  = slice_method( elements.eq(0) );
				
				if ( elements.eq(0).attr( 'type' ) == 'radio' || elements.eq(0).attr( 'type' ) == 'checkbox' ) {
					
					var list_error = 0;
					for ( var j = 0; j < elements.length; j++ ) {
						if ( elements.eq(j).is( ':checked' ) == '' ) {
							list_error++;
						}
					}
					
					if ( list_error == elements.length ) {
						error_span( elements.eq(0), dt_name, '選択', true );
						error++;
						scroll_point = compare_method( scroll_point, elements.eq(0).offset().top );
					} else {
						error_span( elements.eq(0), dt_name, '', false );
					}
					
				} else {
					
					var list_error = 0;
					for ( var j = 0; j < elements.length; j++ ) {
						if ( elements.eq(j).val() == '' ) {
							list_error++;
						}
					}
					
					if ( list_error != 0 ) {
						error_span( elements.eq(0), dt_name, '入力', true );
						error++;
						scroll_point = compare_method( scroll_point, elements.eq(0).offset().top );
					} else {
						error_span( elements.eq(0), dt_name, '', false );
					}
					
				}
			}
			
			
			// required select
			if ( mailform_dt.eq(i).next( 'dd' ).find( 'select' ).length && mailform_dt.eq(i).next( 'dd' ).attr( 'class' ) == 'required' ) {
				var elements = mailform_dt.eq(i).next( 'dd' ).find( 'select' );
				var dt_name  = slice_method( elements.eq(0) );
				
				var list_error = 0;
				for ( var j = 0; j < elements.length; j++ ) {
					if ( elements.eq(j).val() == '' ) {
						list_error++;
					}
				}
				
				if ( list_error != 0 ) {
					error_span( elements.eq(0), dt_name, '選択', true );
					error++;
					scroll_point = compare_method( scroll_point, elements.eq(0).offset().top );
				} else {
					error_span( elements.eq(0), dt_name, '', false );
				}
			}
			
			
			// required textarea
			if ( mailform_dt.eq(i).next( 'dd' ).find( 'textarea' ).length && mailform_dt.eq(i).next( 'dd' ).attr( 'class' ) == 'required' ) {
				var elements = mailform_dt.eq(i).next( 'dd' ).find( 'textarea' );
				var dt_name  = slice_method( elements.eq(0) );
				if ( elements.eq(0).val() == '' ) {
					error_span( elements.eq(0), dt_name, '入力', true );
					error++;
					scroll_point = compare_method( scroll_point, elements.eq(0).offset().top );
				} else {
					error_span( elements.eq(0), dt_name, '', false );
				}
			}
			
			
			// no-required email
			if ( mailform_dt.eq(i).next( 'dd' ).find( 'input' ).length && mailform_dt.eq(i).next( 'dd' ).find( 'input' ).eq(0).attr( 'type' ) == 'email' ) {
				var elements = mailform_dt.eq(i).next( 'dd' ).find( 'input' );
				var dt_name  = slice_method( elements.eq(0) );
				if( elements.eq(0).val() != '' && ! ( elements.eq(0).val().match(/^([a-zA-Z0-9])+([a-zA-Z0-9\._-])*@([a-zA-Z0-9_-])+([a-zA-Z0-9\._-]+)+$/) ) ) {
					elements.eq(0).parents( 'dd' ).find( 'span.error_format' ).text( '正しいメールアドレスの書式ではありません。' );
					error++;
					scroll_point = compare_method( scroll_point, elements.eq(0).offset().top );
				} else {
					elements.eq(0).parents( 'dd' ).find( 'span.error_format' ).text( '' );
				}
			}
		}
		
		
		if ( $( 'input#mail_address_confirm' ).length && $( 'input#mail_address' ).length ) {
			var element   = $( 'input#mail_address_confirm' );
			var element_2 = $( 'input#mail_address' );
			var dt_name   = slice_method( element );
			
			if ( element.val() != '' && element.val() !== element_2.val() ) {
				element.parents( 'dd' ).find( 'span.error_match' ).text( 'メールアドレスが一致しません。' );
				error++;
				scroll_point = compare_method( scroll_point, element.offset().top );
			} else {
				element.parents( 'dd' ).find( 'span.error_match' ).text( '' );
			}
		}
		
		
		
		
		if ( error == 0 ) {
			
			if ( confirm_window == 1 ) {
				if ( window.confirm( '送信してもよろしいですか？' ) ) {
					send_setup();
					order_set();
					send_method();
				}
			}
			
			if ( confirm_window == 2 ) {
				hidden_append( 'confirm_action', 'on' );
				send_setup();
				order_set();
				
				var form_data = new FormData( $( 'form#mail_form' ).get(0) );
				
				$.ajax({
					type: $( 'form#mail_form' ).attr( 'method' ),
					url: $( 'form#mail_form' ).attr( 'action' ),
					cache: false,
					dataType: 'html',
					data: form_data,
					contentType: false,
					processData: false,
					
					success: function( res ) {
						var response = res.split( 'CONFIRM_DELIMITER' );
						if ( response[0] == 'confirm_success' ) {
							$( 'input#confirm_action' ).remove();
							
							$( 'form#mail_form' ).css( 'display', 'none' );
							$( 'div#confirm_field dl' ).append( response[1] );
							$( 'div#confirm_field' ).fadeIn( 'slow' );
							$( 'html, body' ).animate({
								scrollTop: $( 'div#confirm_field' ).offset().top - 70
							}, 500 );
						} else {
							response[1] = response[1].replace( /<br>|<br \/>/gi, "\\n" );
							window.alert( response[1] );
						}
						
					},
					
					error : function( res ) {
						window.alert( '通信に失敗しました。\\nページの再読み込みをしてからもう一度お試しください。' );
					}
				});
			}
			
		} else {
			$( 'html, body' ).animate({
				scrollTop: scroll_point - 70
			}, 500 );
		}
		
	}
	
	
	
	
	// function send_setup
	function send_setup() {
		
		hidden_append( 'javascript_action', true );
		
		var now_url = encodeURI( document.URL );
		hidden_append( 'now_url', now_url );
		
		var before_url = encodeURI( document.referrer );
		hidden_append( 'before_url', before_url );
		
		hidden_append( 'writing_time', writing_time );
		
	}
	
	
	
	
	// function order_set
	function order_set() {
		
		var order_number = 0;
		for ( var i = 0; i < mailform_dt.length; i++ ) {
			
			if ( mailform_dt.eq(i).next( 'dd' ).find( 'input' ).length ) {
				var elements = mailform_dt.eq(i).next( 'dd' ).find( 'input' );
				var dt_name  = slice_method( elements.eq(0) );
				
				if ( elements.eq(0).attr( 'type' ) == 'radio' || elements.eq(0).attr( 'type' ) == 'checkbox' ) {
					
					var list_error = 0;
					for ( var j = 0; j < elements.length; j++ ) {
						if ( elements.eq(j).is( ':checked' ) == '' ) {
							list_error++;
						}
					}
					
					if ( list_error != elements.length ) {
						var attr_name = elements.eq(0).attr( 'name' ).replace( /\[|\]/g, '' );
						var attr_type = elements.eq(0).attr( 'type' );
						order_number++;
						hidden_append( 'order_' + order_number, attr_type + ',' + attr_name + ',false,' + dt_name );
					}
					
				} else {
					
					for ( var j = 0; j < elements.length; j++ ) {
						if ( elements.eq(j).val() != '' ) {
							var attr_name = elements.eq(j).attr( 'name' );
							var attr_type = elements.eq(j).attr( 'type' );
							if ( j == 0 ) {
								var connect = 'false';
							} else {
								var connect = 'true';
							}
							order_number++;
							hidden_append( 'order_' + order_number, attr_type + ',' + attr_name + ',' + connect + ',' + dt_name );
						}
					}
					
				}
			}
			
			
			if ( mailform_dt.eq(i).next( 'dd' ).find( 'select' ).length ) {
				var elements = mailform_dt.eq(i).next( 'dd' ).find( 'select' );
				var dt_name  = slice_method( elements.eq(0) );
				
				for ( var j = 0; j < elements.length; j++ ) {
					if ( elements.eq(j).val() != '' ) {
						var attr_name = elements.eq(j).attr( 'name' );
						var attr_type = 'select';
						if ( j == 0 ) {
							var connect = 'false';
						} else {
							var connect = 'true';
						}
						order_number++;
						hidden_append( 'order_' + order_number, attr_type + ',' + attr_name + ',' + connect + ',' + dt_name );
					}
				}
			}
			
			
			if ( mailform_dt.eq(i).next( 'dd' ).find( 'textarea' ).length ) {
				var elements = mailform_dt.eq(i).next( 'dd' ).find( 'textarea' );
				var dt_name  = slice_method( elements.eq(0) );
				if ( elements.eq(0).val() != '' ) {
					var attr_name = elements.eq(0).attr( 'name' );
					var attr_type = 'textarea';
					order_number++;
					hidden_append( 'order_' + order_number, attr_type + ',' + attr_name + ',false,' + dt_name );
				}
			}
			
		}
		
		
		hidden_append( 'order_count', order_number );
		
	}
	
	
	
	
	function cancel_click() {
		
		$( 'input#form_submit_button' ).nextAll( 'input' ).remove();
		
		$( 'div#confirm_field' ).css( 'display', 'none' );
		$( 'div#confirm_field dl' ).html( '' );
		$( 'form#mail_form' ).fadeIn( 'slow' );
		$( 'html, body' ).animate({
			scrollTop: $( 'form#mail_form' ).offset().top - 70
		}, 500);
		
	}
	
	
	
	
	// function send_method
	function send_method() {
		
		$( '<div>' )
			.addClass( 'loading-layer' )
			.appendTo( 'body' )
			.css({
				'width': $( window ).width() + 'px',
				'height': $( window ).height() + 'px',
				'background': 'rgba( 0, 0, 0, 0.7 )',
				'position': 'fixed',
				'left': '0',
				'top': '0',
				'z-index': '999',
			})
			.append( '<span class="loading"></span>' );
		
		setTimeout(function(){
			
			var form_data = new FormData( $( 'form#mail_form' ).get(0) );
			
			$.ajax({
				type: $( 'form#mail_form' ).attr( 'method' ),
				url: $( 'form#mail_form' ).attr( 'action' ),
				cache: false,
				dataType: 'html',
				data: form_data,
				contentType: false,
				processData: false,
				
				success: function( res ) {
					$( 'div.loading-layer, span.loading' ).remove();
					var response = res.split( ',' );
					if ( response[0] == 'send_success' ) {
						window.location.href = response[1];
					} else {
						response[1] = response[1].replace( /<br>|<br \/>/gi, "\n" );
						window.alert( response[1] );
					}
				},
				
				error : function( res ) {
					$( 'div.loading-layer, span.loading' ).remove();
					window.alert( '通信に失敗しました。\nページの再読み込みをしてからもう一度お試しください。' );
				}
			});
			
		}, 1000);
		
	}
	
	
	
	
	// page setting
	for ( var i = 0; i < mailform_dt.length; i++ ) {
		if ( mailform_dt.eq(i).next( 'dd' ).attr( 'class' ) == 'required' ) {
			$( '<span/>' )
				.text( '必須' )
				.addClass( 'required' )
				.prependTo( $( mailform_dt.eq(i) ) );
		} else {
			$( '<span/>' )
				.text( '任意' )
				.addClass( 'optional' )
				.prependTo( $( mailform_dt.eq(i) ) );
		}
		
		$( '<span/>' )
			.addClass( 'error_blank' )
			.appendTo( mailform_dt.eq(i).next( 'dd' ) );
		
		if ( mailform_dt.eq(i).next( 'dd' ).find( 'input' ).length && mailform_dt.eq(i).next( 'dd' ).find( 'input' ).eq(0).attr( 'type' ) == 'email' ) {
			$( '<span/>' )
				.addClass( 'error_format' )
				.appendTo( mailform_dt.eq(i).next( 'dd' ) );
		}
	}
	
	
	if ( $( 'input#mail_address_confirm' ).length ) {
		$( '<span/>' )
			.addClass( 'error_match' )
			.appendTo( $( 'input#mail_address_confirm' ).parents( 'dd' ) );
		
	}
	
	
	$( 'input' ).on( 'keydown', function( e ) {
		if ( ( e.which && e.which === 13 ) || ( e.keyCode && e.keyCode === 13 ) ) {
			return false;
		} else {
			return true;
		}
	});
	
	
	$( window ).on( 'resize', resize );
	
	
	if ( $( 'textarea' ).length ) {
		$( 'textarea' )
			.focus(function() {
				start_time = new Date();
			})
			.blur(function() {
				stop_time = new Date();
				writing_time += Math.round( ( stop_time - start_time ) / 1000 );
			});
	}
	
	
	$.ajax({
		type: $( 'form#mail_form' ).attr( 'method' ),
		url: $( 'form#mail_form' ).attr( 'action' ),
		cache: false,
		dataType: 'text',
		data: 'config_get=true',
		
		success: function( res ) {
			switch ( res ) {
				case '2':
					confirm_window = 2;
					break;
					
				case '1':
					confirm_window = 1;
					break;
					
				default:
					window.alert( '設定ファイルの読み込みに失敗しました。' );
					break;
			}
		},
		
		error : function( res ) {
			window.alert( '設定ファイルの読み込みに失敗しました。\\nページの再読み込みをしてからもう一度お試しください。' );
		}
	});
	
	
	$( 'input#form_submit_button' ).click( required_check );
	
	$( 'input#confirm_cancel_button' ).click( cancel_click );
	
	$( 'input#confirm_submit_button' ).click( send_method );
	
})( jQuery );

