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
				type: a.type,
				data: b,
				error: function() {
					console.log( 'ERROR' );
					CMS.cssLoading.hide();

				},
				success: function( c ) {
					CMS.cssLoading.hide();

					if ( c.success ) {
						var d = '';
						if ( a.action == 'data-ready' ) {
							if ( b.action == 'countries' ) {
								d = '<option></option>';
								$.each( c.content.countries, function ( k, v ) {
									d += '<option value="' + v.id_country + '">' + v.title_country + '</option>';

								});
								var i = '[data-ready="countries"]';
								$( i ).html( d );

							}

						} else if ( a.action == 'data-change' ) {
							if ( b.action == 'regions' ) {
								d = '<option></option><option value="0">выберите регион</option>';
								$.each( c.content.regions, function ( k, v ) {
									d += '<option value="' + v.id_region + '">' + v.title_region + '</option>';

								});

								var i = '[data-change="regions"]';
								$( i ).html( d );

								if ( $( i ).html() != '' ) {
									CMS.select2.disabled( i, false );
									$( i ).val( 0 ).trigger( 'change' );

								}

							}

							if ( b.action == 'regions' || b.action == 'cities' ) {
								if ( b.action == 'regions' ) {
									CMS.cities.mainCities = c.content.cities;

								}

								CMS.cities.addCities( c.content.cities );

							}

						} else if ( a.action == 'vk_search' ) {
							$( '[data-content="vk_search_result"]' ).html( '<div class="portlet box blue">' +
																				'<div class="portlet-title">' +
																					'<div class="caption">' +
																						'<i class="fa fa-gift"></i>Результат поиска' +
																					'</div>' +
																					'<div class="tools">' +
																						'<a href="javascript:;" class="collapse" data-original-title="" title=""> </a>' +
																					'</div>' +
																				'</div>' +
																				'<div class="portlet-body form">' +
																					'<div class="form-body clearfix">' + c.content + '</div>' +
																				'</div>' +
																			'</div>'  );

						}
					} else {
						$( '[data-html="msg"]' ).after( c.msg );

					}

				}

			});

		}

	},
	cities : {
		mainCities : '',
		addCities : function ( a ) {
			var b = '',
				c = '<option></option><option value="0">выберите населённый пункт</option>',
				d = '[data-change="cities"]';

			$.each( a, function ( i, f ) {
				b = ( f.area_city != '' && f.area_city != null ) ? ' (' + f.area_city + ')' : '';

				c += '<option value="' + f.id_city + '">' + f.title_city + b + '</option>';

			});

			$( d ).html( c );

			if ( $( d ).html() != '' ) {
				CMS.select2.disabled( d, false );
				$( d ).val( 0 ).trigger( 'change' );

			}

		}

	},
	hash_array: function( a ) {
		var b		= {},
			name	= '',
			x		= false,
			y		= '';

		$( a ).each( function( ) {
			if ( this.value !== '' ) {
				y = this.name.replace( /[\[\]]/g, '' );

				if ( y != this.name ) {
					name = this.name.replace( /[\]]/g, '' ),
						name = name.split( '[' );
					if ( name[name.length-1] != '' ) {
						x = true;

					} else {
						name = this.name.replace( /[\[\]]/g, '' );

					}

				} else {
					name = y;

				}

				if ( x == false ) {
					if ( name in b ) {
						if ( $.isArray( b[name] ) ) {
							b[name].push( this.value );

						} else {
							b[name] = [ b[name], this.value ];

						}

					} else {
						b[name] = this.value;

					}

				} else {
					if ( name.length == 2 ) {
						if ( ! ( name[0] in b ) ) {
							b[name[0]] = {};

						}
						b[name[0]][name[1]] = this.value;

					} else if ( name.length == 3 ) {
						if ( name[0] in b ) {
							if ( ! ( name[1] in b[name[0]] ) ) {
								b[name[0]][name[1]] = {};

							}
							b[name[0]][name[1]][name[2]] = this.value;

						} else {
							b[name[0]] = {};
							b[name[0]][name[1]] = {};
							b[name[0]][name[1]][name[2]] = this.value;

						}

					}



				}

			}

		});

		return b;

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
			CMS.ajax.request( {
				action : 'data-ready',
				type : 'get'
			}, {
				method : 'ajax',
				action : $( this ).data( 'ready' )
			} );

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
				allowClear: true,
				minimumResultsForSearch: 20
			});
			$( '[data-select="select1"]' ).select2 ({
				minimumResultsForSearch: 20
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
		if ( CMS.obj( a.val() ) == 'string' && a.val() != '0' && a.val() != '' && a.val() != 'undefined' ) {
			CMS.ajax.request( {
				action : 'data-change',
				type : 'get'
			}, {
				method			: 'ajax',
				id_country		: a.val(),
				action			: 'regions'
			} );

		} else {
			CMS.select2.disabled( '[data-change="regions"]', true );
			$( '[data-change="regions"]' ).val( 0 ).trigger( 'change' );

			CMS.select2.disabled( '[data-change="cities"]', true );
			$( '[data-change="cities"]' ).val( 0 ).trigger( 'change' );

		}

	} else if ( b == 'regions' ) {
		var c = $( '[data-change="countries"]' ).val();

		if ( ( CMS.obj( a.val() ) == 'string' && a.val() != '0' && a.val() != '' ) && ( CMS.obj( c ) == 'string' && c != '0' && c != '' ) ) {
			CMS.ajax.request( {
				action : 'data-change',
				type : 'get'
			}, {
				method			: 'ajax',
				id_country		: c,
				id_region		: a.val(),
				action			: 'cities'
			} );

		} else {
			CMS.cities.addCities( CMS.cities.mainCities );
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

}).on( 'click', '[data-btn="vk_search"]', function () {
	var a = '[data-form="vk_search"]',
		b = $( a + ' input, ' + a + ' select' ).serializeArray(),
		c = CMS.hash_array( b );

	CMS.ajax.request( {
		action : 'vk_search',
		type : 'get'
	}, {
		method			: 'ajax',
		data			: c,
		action			: 'vk_search'
	} );

	console.log( c );
}).ready( function () {
    CMS.ready();

});
