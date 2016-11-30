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

};

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
}

CMS.a.on( 'click', '[data-btn="form"]', function () {
	CMS.cssLoading.open();

	$( $( this ).closest( 'form' ) ).hide();
	$( '[data-form="' + $( this ).data( 'name-form' ) + '"]' ).show();

	CMS.cssLoading.clos();

}).on( 'click', '[data-btn="formGo"]', function () {
	CMS.cssLoading.open();

	var a = true;
	if ( $( this ).data( 'action' ) == 'login' ) {
		if ( $( '[data-form="' + $( this ).data( 'action' ) + '"] [name="username"]' ).val() == '' ) {
			a = false;

		}

		if ( $( '[data-form="' + $( this ).data( 'action' ) + '"] [name="password"]' ).val() == '' ) {
			a = false;

		}

	} else if ( $( this ).data( 'action' ) == 'forget' ) {
		if ( $( '[data-form="' + $( this ).data( 'action' ) + '"] [name="email"]' ).val() == '' ) {
			a = false;

		}

	}

	CMS.cssLoading.clos();
	if ( a == false ) {
		return a;

	}

}).ready( function () {
    CMS.ready();

});
