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

( function() {

	'use strict';

	var CMS = {
		a : $( document ),
		b: {
			optObj : [ '<option>', '</option>' ]
		},
		ajax: {
			request: function( a, b ) {
				CMS.cssLoading.open();

				$.ajax({
					url: CMSVAR.home + 'index.php',
					dataType: 'json',
					type: a.type,
					data: b,
					error: function() {
						CMS.confirm.alert({
							title : 'Внемание!',
							content : 'Ошибка на сервере!'
						});

						if ( a.action != 'vk_save_modal' ) {
							CMS.cssLoading.hide();

						}

					},
					success: function( c ) {
						CMS.cssLoading.hide();

						if ( c.success ) {
							var d = '',
								i = '';
							if ( a.action == 'data-ready' ) {
								if ( b.action == 'countries' ) {
									d = CMS.b.optObj[0] + CMS.b.optObj[1];
									$.each( c.content.countries, function ( k, v ) {
										d += '<option value="' + v.id_country + '">' + v.title_country + CMS.b.optObj[1];

									});
									i = '[data-ready="countries"]';
									$( i ).html( d );

								}

							} else if ( a.action == 'data-change' ) {
								if ( b.action == 'regions' ) {
									d = CMS.b.optObj[0] + CMS.b.optObj[1] + '<option value="0">выберите регион' + CMS.b.optObj[1];
									$.each( c.content.regions, function ( k, v ) {
										d += '<option value="' + v.id_region + '">' + v.title_region + CMS.b.optObj[1];

									});
									i = '[data-change="regions"]';
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

							} else if ( a.action == 'vk_user_search' || a.action == 'vk_group_search' ) {
								$( '[data-content="' + a.action + '_result"]' ).html( c.content );

							}
						} else {
							CMS.confirm.alert({
								title : 'Внемание!',
								content : c.msg
							});

							CMS.cssLoading.hide();

						}

						CMS.tooltip.init();

					}

				});

			}

		},
		cities : {
			mainCities : '',
			addCities : function ( a ) {
				var b = '',
					c = CMS.b.optObj[0] + CMS.b.optObj[1] + '<option value="0">выберите населённый пункт' + CMS.b.optObj[1],
					d = '[data-change="cities"]';

				$.each( a, function ( i, f ) {
					b = ( f.area_city != '' && f.area_city != null ) ? ' (' + f.area_city + ')' : '';

					c += '<option value="' + f.id_city + '">' + f.title_city + b + CMS.b.optObj[1];

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
						name = this.name.replace( /[\]]/g, '' );
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
				createTokensOnBlur: true

			});

		},
		tooltip : {
			init: function () {
				CMS.tooltip.destroy();
				$( '[data-toggle="tooltip"]' ).tooltip ();

			},
			destroy : function () {
				$( '[data-toggle="tooltip"]' ).tooltip( 'destroy' );

			}

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
		vk_search : function ( a ) {
			var b = '[data-form="' + a + '"]',
				c = $ ( b + ' input, ' + b + ' select' ).serializeArray(),
				d = CMS.hash_array ( c );

			CMS.ajax.request ( {
				action : a,
				type : 'get'
			}, {
				method : 'ajax',
				data : d,
				action : a
			} );

		},
		confirm : {
			form: function ( a ) {
				$.confirm({
					title: a.title,
					content: a.content,
					closeIcon: true,
					animation: 'top',
					closeAnimation: 'bottom',
					backgroundDismiss: true,
					columnClass: a.class,
					buttons: {
						okay : {
							text : a.button,
							btnClass : 'btn-success',
							keys : [ 'enter' ],
							action : function () {
								var i = $( '[data-danger="form"]' );
								i.html( '' );

								if ( a.form == 'vk_group_parser' ) {
									var b = '[data-form="' + a.form + '"]',
										c = $ ( b + ' [name="name"]' ),
										d = $ ( b + ' [name="id_list"]' );

									if ( c.val() == '' ) {
										i.append( '<div>Поле имя обязательно к заполнению</div>' ).slideDown( 200 );

									}

									if ( d.val() == '' ) {
										i.append( '<div>Вы не внесли ни одного ID</div>' ).slideDown( 200 );

									}

									if ( i.html() != '' ) {
										return false;

									}

									CMS.ajax.request ( {
										action : 'vk_group_parser',
										type : 'post'
									}, {
										method : 'ajax',
										data : CMS.hash_array( $ ( b + ' input' ).serializeArray() ),
										action : 'vk_group_parser',
										step : 'add'
									} );

								}

							}

						},
						cancel : {
							text : 'Закрыть',
							btnClass : 'btn-warning',
							keys : [ 'esc' ]

						}
					},
					onOpen : function ( ) {
						CMS.tokenField();

					}

				});

			},
			alert : function ( a ) {
				$.alert({
					title: a.title,
					content: a.content,
					closeIcon: true,
					animation: 'top',
					closeAnimation: 'bottom',
					backgroundDismiss: true,
					useBootstrap: true,
					buttons: {
						okay : {
							text : 'Закрыть',
							btnClass : 'btn-default',
							keys : [ 'enter', 'esc' ]

						}

					}

				});

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
			CMS.tooltip.init();

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

	}).on( 'click', '[data-btn="vk_user_search"],[data-btn="vk_group_search"]', function () {
		var a = $( this ).data( 'btn' ),
			b = true;

		$( '[data-form="' + a + '"] [data-required-field]' ).each( function (  ) {
			if ( $( this ).val() == '' ) {
				$( this ).closest( '.' + $( this ).data( 'required-field' ) ).addClass( 'has-error' );
				b = false;

			}

		});

		if ( b === false ) {
			CMS.confirm.alert( {
				'title' : 'Внемание!',
				'content' : 'Не все обязательные поля заполнены!'
			} );
			return b;

		}
		CMS.vk_search( a );
		$( '[data-content="' + a + '_result"]' ).html( '' );

	}).on( 'click', '[data-btn-delete1]', function () {
		var a = $( '[data-block-delete1="' + $( this ).data( 'btn-delete1' ) + '"]' ),
			b = a.outerHeight(),
			c = a.outerWidth();

		a.animate({
			opacity: 0,
			top: '-' +  b  + 'px',
			marginRight: '-' + c + 'px'
		}, 'slow', function (  ) {
			$( this ).fadeOut().remove();

		});

	}).on( 'click', '[data-btn-delete2]', function () {
		$( '[data-block-delete2="' + $( this ).data( 'btn-delete2' ) + '"]' ).fadeOut();

	}).on( 'click', '[data-btn-parser="group"]', function () {
		var a = $( '[data-vk-id-group]' ).map( function(){
			return $( this ).data( 'vk-id-group' );

		}).get();

		a = a.join( ', ' );

		CMS.confirm.form({
			button 		: 'Создать',
			class		: 'col-md-8 col-md-offset-2 col-sm-12 col-xs-12',
			content 	: '' +
			'<div data-form="vk_group_parser">' +
			'<div class="row">' +
			'<div class="col-md-12">' +
			'<div class="form-horizontal">' +
			'<div class="form-body">' +
			'<div class="form-group has-error">' +
			'<label class="col-md-3 control-label">Название задачи</label>' +
			'<div class="col-md-9">' +
			'<input type="text" name="name" data-required-field="form-group" class="form-control spinner" placeholder="обязательно">' +
			'</div>' +
			'</div>' +
			'<div class="form-group">' +
			'<label class="col-md-3 control-label">Список ID</label>' +
			'<div class="col-md-9">' +
			'<input type="text" name="id_list" data-input="tokenfield" class="form-control spinner" value="' + a + '">' +
			'</div>' +
			'</div>' +
			'</div>' +
			'</div>' +
			'</div>' +
			'</div>' +
			'</div>' +
			'</form>' +
			'<div class="row">' +
			'<div data-danger="form" class="col-md-9 col-md-offset-3 form-text-danger"></div>' +
			'</div>',
			form		: 'vk_group_parser',
			title 		: 'Создание задачи парсера'

		});

	}).on( 'keyup', '[data-required-field]', function () {
		if ( $( this ).val() == '' ) {
			$( this ).closest( '.' + $( this ).data( 'required-field' ) ).addClass( 'has-error' );

		} else {
			$( this ).closest( '.' + $( this ).data( 'required-field' ) ).removeClass( 'has-error' );

		}

	}).on( 'submit', '[data-form]', function () {
		return false;

	}).ready( function () {
		CMS.ready();

	});

})();