/**
 * Created by Сергей on 06.11.2016.
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
	btnLoading : function (  ) {
		$( '.demo-loading-btn' ).click( function() {
			var t = $( this );

			t.button( 'loading' );
			setTimeout( function() {
				t.button( 'reset' )
			}, 3e3 )

		});

		Ladda.bind(".mt-ladda-btn", {
			timeout: 2e3

		});

		Ladda.bind( '.mt-ladda-btn.mt-progress-demo', {
			callback: function( t ) {
				var n = 0,
                    a = setInterval( function() {
                            n = Math.min( n + .1 * Math.random(), 1 );
                            t.setProgress(n);
                            1 === n && ( t.stop(), clearInterval( a ) );

                        }, 200 );
			}

		});

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
        CMS.btnLoading();

    }
}
var UIButtons = function() {
	var t = function() {

	};
	return {
		init: function() {
			t()
		}
	}
}();
jQuery(document).ready(function() {
	UIButtons.init()
});
CMS.a.ready(function (  ) {
    CMS.ready();
});
