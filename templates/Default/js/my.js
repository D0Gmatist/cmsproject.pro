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

    pole : {
        p1 : '1',
        p2 : '2',
        p3 : '3'
    },

    tpl :   '<form class="form-horizontal" data-form="onclickForm">' +
    '<div class="form-group form-group-sm">' +
    '<label class="col-md-5 control-label" for="title">Название поля 1:</label>' +
    '<div class="col-md-7">' +
    '<input type="text" name="p1" class="form-control" value="" placeholder="поле 1 ..." data-id="p1" data-pole="onclickForm">' +
    '</div>' +
    '</div>' +
    '<div class="form-group form-group-sm">' +
    '<label class="col-md-5 control-label" for="title">Название поля 2:</label>' +
    '<div class="col-md-7">' +
    '<input type="text" name="p2" class="form-control" value="" placeholder="поле 2 ..." data-id="p2" data-pole="onclickForm">' +
    '</div>' +
    '</div>' +
    '<div class="form-group form-group-sm">' +
    '<label class="col-md-5 control-label" for="title">Название поля 3:</label>' +
    '<div class="col-md-7">' +
    '<input type="text" name="p3" class="form-control" value="" placeholder="поле 3 ..." data-id="p3" data-pole="onclickForm">' +
    '</div>' +
    '</div>' +
    '</form>',

    hash_array : function( a ) {
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

    demo : function (  ) {
        $( '[data-btn="onclickForm"]' ).click( function() {
            DEMO.bootbox( false, false );

        });
    },

    bootbox : function ( a, b ) {
        bootbox.confirm({
            title: 'Фарма заявки в 1 клик',
            message: DEMO.tpl,
            buttons: {
                confirm: {
                    label: 'Отправить',
                    className: 'btn-success'
                },
                cancel: {
                    label: 'Закрыть',
                    className: 'btn-danger'
                }
            },
            callback: function ( result ) {
                if ( result ) {
                    var form = DEMO.hash_array ( $( '[data-form="onclickForm"] [data-pole="onclickForm"]' ).serializeArray() );

                    console.log( form.p1 );
                    DEMO.pole.p1 = form.p1;
                    DEMO.pole.p2 = form.p2;
                    DEMO.pole.p3 = form.p3;

                    var c = [],
                        d = false;
                    if ( DEMO.pole.p1 == '' || DEMO.pole.p1 == undefined ) {
                        c.push('p1');
                        d = true;

                    }
                    if ( DEMO.pole.p2 == '' || DEMO.pole.p2 == undefined ) {
                        c.push('p2');
                        d = true;

                    }
                    if ( DEMO.pole.p3 == '' || DEMO.pole.p3 == undefined ) {
                        c.push('p3');
                        d = true;

                    }

                    if ( d == true ) {
                        DEMO.bootbox( true, c );

                    }

                }

            }

        });

        if ( a == true ) {
            $( '[data-id="p1"]' ).val( DEMO.pole.p1 );
            $( '[data-id="p2"]' ).val( DEMO.pole.p2 );
            $( '[data-id="p3"]' ).val( DEMO.pole.p3 );

        }

        if ( b != false ) {
            $( b ).each( function ( k, v ) {
                console.log( v );
                var e = $( '[data-id="' + v + '"]' ).closest( '.form-group' );
                e.addClass( 'has-error' );

            } );

        }

    }

}
/** DEMO */

CMS.a.ready(function (  ) {
    CMS.ready();
    DEMO.demo();
});
