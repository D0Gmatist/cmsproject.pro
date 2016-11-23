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

    }
}

/** DEMO */
var DEMO = {

}
/** DEMO */

CMS.a.ready(function (  ) {
    CMS.ready();
    DEMO.ready();
});
