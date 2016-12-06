/**
 * Created by Сергей
 */
if ( typeof( jQuery ) === 'undefined' ) {
    var jQuery;

    if ( typeof( require ) === 'function' ) {
        jQuery = $ = require( 'jquery' );

    } else {
        jQuery = $;

    }

}

var CMS = {
    a : $( document ),
	ajax: {
		request: function( a, b ) {
			CMS.cssLoading.open();

			$.ajax({
				url: CMSVAR.home + 'index.php',
				dataType: 'json',
				type: 'post',
				data: b,
				error: function() {
					console.log( 'ERROR' );
					CMS.cssLoading.hide();

				},
				success: function( c ) {
					CMS.cssLoading.hide();

					if ( c.success ) {
						var d = '<option></option>';
						if ( a == 'data-ready' ) {
							if ( b.action == 'countries' ) {
								$.each( c.content, function ( k, v ) {
									d += '<option value="' + v.id_country + '">' + v.title_country + '</option>';

								});
								$( '[' + a + '="' + b.action + '"]' ).html( d );

							}

						} else if ( a == 'data-change' ) {
							if ( b.action == 'regions' ) {
								d += '<option value="0">Выберите регион</option>';
								$.each( c.content, function ( k, v ) {
									d += '<option value="' + v.id_region + '">' + v.title_region + '</option>';

								});

								var i = '[' + a + '="' + b.action + '"]';
								$( i ).html( d );

								if ( $( i ).html() != '' ) {
									CMS.select2.disabled( '[' + a + '="' + b.action + '"]', false );
									$( '[' + a + '="' + b.action + '"]' ).val( 0 ).trigger( 'change' );

								}

							} else if ( b.action == 'cities' ) {
								var f = '';
								d += '<option value="0">Выберите населённый пункт</option>';
								$.each( c.content, function ( k, v ) {
									f = ( v.area_city != '' && v.area_city != null ) ? ' (' + v.area_city + ')' : '';
									d += '<option value="' + v.id_city + '">' + v.title_city + f + '</option>';

								});

								var i = '[' + a + '="' + b.action + '"]';
								$( i ).html( d );

								if ( $( i ).html() != '' ) {
									CMS.select2.disabled( '[' + a + '="' + b.action + '"]', false );
									$( '[' + a + '="' + b.action + '"]' ).val( 0 ).trigger( 'change' );

								}

							}

						}
					} else {
						$( '[data-html="msg"]' ).after( c.msg );

					}

				}

			});

		}

	},
	obj: function( a ) {
		var b = typeof a;

		if ( 'object' == b ) {
			if ( a ) {
				if ( a instanceof Array ) {
					return 'array';

				}

				if ( a instanceof Object ) {
					return b;

				}
				var c = Object.prototype.toString.call( a );

				if ( '[object Window]' == c ) {
					return 'object';

				}
				if ( '[object Array]' == c || 'number' == typeof a.length && 'undefined' != typeof a.splice && 'undefined' != typeof a.propertyIsEnumerable && !a.propertyIsEnumerable( 'splice' ) ) {
					return 'array';

				}

				if ( '[object Function]' == c || 'undefined' != typeof a.call && 'undefined' != typeof a.propertyIsEnumerable && !a.propertyIsEnumerable( 'call' ) ) {
					return 'function';

				}

			} else {
				return 'null';

			}

		} else {
			if ( 'function' == b && 'undefined' == typeof a.call ) {
				return 'object';

			}

		}
		return b;

	},
	readyAjax : function () {
		$( '[data-ready]' ).each( function( k, v ) {
			CMS.ajax.request( 'data-ready', {
				method : 'ajax',
				action : $( this ).data( 'ready' )

			});

		});

	},
	tokenField : function() {
		$( '[data-input="tokenfield"]' ).tokenfield({
			delimiter: [ ',', ' ' ],
			showAutocompleteOnFocus: true

		});

	},
	select2 : {
    	init : function() {
			$( '[data-select="select2"]' ).select2 ({
				allowClear: true
			});

		},
		disabled : function ( a, b ) {
			$( a ).prop( 'disabled', b );

		}

	},
    cssLoading : {
        tpl: '<div class="css_loadading_form" data-alt="ЗАГРУЗКА" data-content="css_loading" style="display: none;">' +
                '<div class="cssload-thecube">' +
                    '<div class="cssload-cube cssload-c1"></div>' +
                    '<div class="cssload-cube cssload-c2"></div>' +
                    '<div class="cssload-cube cssload-c4"></div>' +
                    '<div class="cssload-cube cssload-c3"></div>' +
                '</div>' +
            '</div>',
        init : function () {
            $( 'body' ).append( CMS.cssLoading.tpl );

        },
        open : function () {
            $( '[data-content="css_loading"]' ).show();

        },
		hide : function () {
        	setTimeout(function (  ) {
				$( '[data-content="css_loading"]' ).hide();

			}, 500 );

        }

    },
    ready : function () {
    	CMS.readyAjax();
        CMS.cssLoading.init();
        CMS.tokenField();
        CMS.select2.init();

    }

};

CMS.a.on( 'click', '[data-btn="form"]', function () {
	CMS.cssLoading.open();

	$( $( this ).closest( 'form' ) ).hide();
	$( '[data-form="' + $( this ).data( 'name-form' ) + '"]' ).show();

	CMS.cssLoading.hide();

}).on( 'change', '[data-change]', function () {
	var a = $( this ),
		b = a.data( 'change' );

	if ( b == 'countries' ) {
		if ( CMS.obj( a.val() ) == 'string' && a.val() != '0' && a.val() != '' ) {
			CMS.ajax.request( 'data-change', {
				method			: 'ajax',
				id_country		: a.val(),
				action			: 'regions'

			});

		} else {
			CMS.select2.disabled( '[data-change="regions"]', true );
			$( '[data-change="regions"]' ).val( 0 ).trigger( 'change' );

			CMS.select2.disabled( '[data-change="cities"]', true );
			$( '[data-change="cities"]' ).val( 0 ).trigger( 'change' );

		}

	} else if ( b == 'regions' ) {
		var c = $( '[data-change="countries"]' ).val();

		if ( ( CMS.obj( a.val() ) == 'string' && a.val() != '0' && a.val() != '' ) && ( CMS.obj( c ) == 'string' && c != '0' && c != '' ) ) {
			CMS.ajax.request( 'data-change', {
				method			: 'ajax',
				id_country		: c,
				id_region		: a.val(),
				action			: 'cities'

			});

		} else {
			CMS.select2.disabled( '[data-change="cities"]', true );
			$( '[data-change="cities"]' ).val( 0 ).trigger( 'change' );

		}

	}

}).on( 'click', '[data-btn="formGo"]', function () {
	var a = true,
		b = $( this ).data( 'action' ),
		c,
		d;
	if ( b == 'login' ) {
		c = [ 'login', 'password' ];

	} else if ( b == 'registration' ) {
		c = [ 'login', 'email', 'password' ];

	} else if ( b == 'forget' ) {
		c = [ 'email' ];

	}

	$( c ).each( function ( k, v ) {
		d = $( '[data-form="' + b + '"] [name="' + v + '"]' );
		if ( d.val() == '' ) {
			$( d.closest( '.form-group' ) ).addClass( 'has-error' );
			a = false;
		} else {
			$( d.closest( '.form-group' ) ).removeClass( 'has-error' );
		}

	});

	if ( a == false ) {
		return a;

	}

}).ready( function () {
    CMS.ready();

});
