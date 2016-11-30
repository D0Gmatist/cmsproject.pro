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

	login : function() {


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
        clos : function () {
			setTimeout( function () {
				$( '[data-content="css_loading"]' ).hide();

			}, 400 );

        }

    },
    ready : function () {
        CMS.cssLoading.init();
        CMS.login();

    }
};

CMS.a.on( 'click', '[data-btn="form"]', function () {
	CMS.cssLoading.open();

	$( $( this ).closest( 'form' ) ).hide();
	$( '[data-form="' + $( this ).data( 'name-form' ) + '"]' ).show();

	CMS.cssLoading.clos();

}).on( 'click', '[data-btn="formGo"]', function () {
	CMS.cssLoading.open();

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

	CMS.cssLoading.clos();
	if ( a == false ) {
		return a;

	}

}).ready( function () {
    CMS.ready();

});
