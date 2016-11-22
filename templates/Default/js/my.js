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


var cms = {
    a : $( document ),
    ready : function (  ) {

    }
}

cms.a.ready(function (  ) {
    cms.ready();
});
