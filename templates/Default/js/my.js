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
            $( '[data-content="css_loading"]' ).hide();

        }

    },
    ready : function () {
        CMS.cssLoading.init();
        CMS.login();

    }
}

CMS.a.on( 'click', '[data-btn="form"]', function () {
	$( $( this ).closest( 'form' ) ).hide();
	$( '[data-form="' + $( this ).data( 'name-form' ) + '"]' ).show();


}).on( 'click', '[data-btn="formGo"]', function () {
	if ( $( this ).data( 'action' ) == 'login' ) {
		if ( $( '[data-form="' + $( this ).data( 'action' ) + '"] [name="username"]' ).val() == '' ) {
			return false;

		}

		if ( $( '[data-form="' + $( this ).data( 'action' ) + '"] [name="password"]' ).val() == '' ) {
			return false;

		}

	} else if ( $( this ).data( 'action' ) == 'forget' ) {
		if ( $( '[data-form="' + $( this ).data( 'action' ) + '"] [name="email"]' ).val() == '' ) {
			return false;

		}

	}

}).ready( function () {
    CMS.ready();

});
