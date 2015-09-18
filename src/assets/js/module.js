/**
 * module.js
 *
 * PHP version 5.4+
 *
 * @author      David Ghyse <dghyse@ibitux.com>
 * @link        http://code.ibitux.net/projects/sweelix
 * @copyright   2010-2015 Ibitux
 * @license     http://www.ibitux.com/license license
 * @version     XXX
 * @category    Js
 * @package     application\modules\toolbar\assets\js
 */
(function($) {

    /**
     * This method refresh toolbar
     *
     * @param element
     *
     * @return void
     * @since  XXX
     */
    function refreshNav(element) {
        //Refresh Nav toolbar
        var url = $(element).data('refreshurl');
        $.ajax({
            url: url
        }).done(function (data, textStatus, jqXHR) {
            var newNav = data;
            $(element).html(newNav);
        });
    };

    /**
     * This method post form
     *
     * @param element
     * @param Idcontainer
     *
     * @return void
     * @since  XXX
     */
    function postform(element, Idcontainer, action) {
        var data = {};
        var source = $(element).data('source');
        var target = $(element).data('target');
        var urlType = $(element).data('elementtype');
        var targetUrlType = $(element).data('inputtype');
        var form = $(element).parent('div').parent('form');


        //Get form data
        if (typeof(form) !== 'undefined') {
            if (form.length > 1) {
                form = form.get(0);
            }

            data = $(form).serializeArray();
        }
        //Add action
        if (typeof(action) !== 'undefined') {
            data.push({'name':action , 'value': action});
        }
        var url = $(element).data('url');
        $.ajax({
            type: "post",
            url: url,
            data: data
        }).done(function (data, textStatus, jqXHR) {
            var contentType = jqXHR.getResponseHeader('content-type');
            var newForm = data;
            if (contentType === 'application/javascript') {
                newForm = "";
            }
            var container = $('#' + Idcontainer);
            $(container).html(newForm);
            var fieldset = $(container).parent('fieldset');
            if ($(fieldset).css('display') === 'none') {
                $(fieldset).show();
            } else if ((action !== 'loading') && (contentType === 'application/javascript')) {
                var siblings = $(container).parent().siblings();
                $(siblings).each(function (ind, ele) {
                    $(ele).hide(
                        "fast",
                        function(){
                            $(this).children().html(newForm);
                        }
                    )
                });
                $(container).parent().hide();
            }
            var inputSource = $("input[name='" + source + "']");
            var inputTarget = $("input[name='" + target + "']");
            var inputType = $("input[name='" + targetUrlType + "']");
            $(inputTarget).val($(inputSource).val());
            $(inputType).val(urlType);
            refreshNav($('#nav'));
        });
    };


    /**
     * This method get new form
     *
     * @param element
     *
     * @return void
     * @since  XXX
     */
    function getNewform(element, action) {
        var url = $(element).data('url');
        var targetUrl = $(element).data('targeturl');
        var refreshUrl = $(element).data('refreshurl');
        var target = $(element).data('target');
        var unTarget = $(element).data('untarget');
        var buttonAddItem = $('#createNewItem');
        var form = $(buttonAddItem).parents().find('form').get(0);
        var name = $(element).attr('name');
        var value = $(element).attr('value');

        //Update urls
        $(buttonAddItem).data('url', targetUrl);
        $(form).attr('action', targetUrl);
        $('#nav').data('refreshurl', refreshUrl);

        //Prepare data
        var data = [];
        if ((typeof(name) !== "undefined") && (typeof(value) !== "undefined")) {
            var form = $(this).parents().find('form');
            data = form.serializeArray();
            data.push({"name":name, "value":value});
        }
        //Call ajax
        $.ajax({
            type: "post",
            url: url,
            data:data
        }).done(function (data, textStatus, jqXHR) {
            var contentType = jqXHR.getResponseHeader('content-type');
            var newForm = data;
            if (contentType === 'application/javascript') {
                newForm = "";
            }
            var container = $(target);
            $(container).html(newForm);
            if ($(container).parent('fieldset').css('display') === 'none') {
                $(container).parent('fieldset').show();
            } else if (action !== 'add') {
                $(unTarget).parent().hide(
                    "fast",
                    function(){
                        $(this).children().html(newForm);
                    }
                );
            }
        });
    };

    /**
     * This method put val form dropdown to input field text
     *
     * @param element
     *
     * @return void
     * @since  XXX
     */
    function getDropDownChange(element) {
        $('body').on('change', element, function(event){
            event.preventDefault();
            var name = $(this).data('targetname')
            var target = $("input[name='"+name+"']");
            $(target).val($(this).val());
        });
    };

    /**
     * This method handle sortable toolbar
     *
     * @return void
     * @since  XXX
     */
    function activateSortableTree() {
        jQuery('body ul.sortableTree').nestedSortable({
            handle: 'a',
            items: 'li',
            listType: 'ul',
            // rootID:'#tree',
            toleranceElement: '> a',
            protectRoot: true,
            placeholder: 'sortable-placeholder',
            stop: function( event, ui ) {
                var data = {};
                var moveUrl = jQuery(ui.item).data('url-move');
                data['sourceIndex'] = jQuery(ui.item).data('indexid');
                var previousNode = jQuery(ui.item).prev().data('indexid');
                console.log(previousNode);
                var nextNode = jQuery(ui.item).next().data('indexid');
                console.log(nextNode);

                if(typeof(previousNode) != 'undefined') {
                    data['target'] = 'after';
                    data['targetIndex'] = jQuery(ui.item).prev().data('indexid');
                } else if(typeof(nextNode) != 'undefined') {
                    data['target'] = 'before';
                    data['targetIndex'] = jQuery(ui.item).next().data('indexid');
                } else {
                    data['target'] = 'in';
                    data['targetIndex'] = jQuery(ui.item).parent().parent().first('li').data('indexid');
                }

                var mode = jQuery(ui.item).data('mode');
                if(typeof(mode) == 'undefined') {
                    mode = null;
                }
                var target = jQuery(ui.item).data('target');
                if(typeof(target) == 'undefined') {
                    target = null;
                }
                sweelix.raise('ajaxRefreshHandler', {
                    'targetUrl' : moveUrl,
                    'data' : data,
                    'mode' : mode,
                    'targetSelector' : target
                });
            }
        }).removeClass('sortableTree');
    };

    /**
     * Document ready
     */
    $(document).ready(function () {
        /**
         * Init sortable tree
         */
        activateSortableTree();

        getDropDownChange('.listElements');
        $('body').on('click', '.btn-action', function (event) {
            event.preventDefault();
            postform(this, 'itemForm');

        });

        $('body').on('click', '.btn-load', function (event) {
            event.preventDefault();
            var action = $(this).data('action');
            postform(this, 'searchElement', action);
        });
        $('body').on('click', '.btn-save-url', function (event) {
            event.preventDefault();
            postform(this, 'searchElement');
        });

        $('body').on('click', '.btn-addItem', function (event) {
            event.preventDefault();
            getNewform(this);
        });
        $('body').on('click', '.btn-newItem', function (event) {
            event.preventDefault();
            var items = $('body nav.toolbar ul li a.btn-newItem');
            $(items).each(
                function(ind, ele){
                    $(ele).removeClass('active');
                }
            );
            $(this).addClass('active');
            getNewform(this, 'add');
        });

        $('body').on('click', '.btn-manage', function (event) {
            event.preventDefault();
            getNewform(this);
        });
    });

    $(document).ajaxComplete(function(){
        //refreshNav($('#nav'));
        /**
         * Init sortable tree
         */
        activateSortableTree();
    });
})(jQuery);