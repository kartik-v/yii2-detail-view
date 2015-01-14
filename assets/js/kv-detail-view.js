/*!
 * @package   yii2-detail-view
 * @author    Kartik Visweswaran <kartikv2@gmail.com>
 * @copyright Copyright &copy; Kartik Visweswaran, Krajee.com, 2014
 * @version   1.6.0
 *
 * Client extension for the yii2-detail-view extension 
 * 
 * Author: Kartik Visweswaran
 * Copyright: 2014, Kartik Visweswaran, Krajee.com
 * For more JQuery plugins visit http://plugins.krajee.com
 * For more Yii related demos visit http://demos.krajee.com
 */

(function ($) {

    var KvDetailView = function (element, options) {
        this.$element = $(element);
        this.mode = options.mode;
        this.fadeDelay = options.fadeDelay;
        this.initElements();
        this.init();
    };

    KvDetailView.prototype = {
        constructor: KvDetailView,
        init: function () {
            var self = this;
            self.initElements();
            self.$btnUpdate.on('click', function (e) {
                self.setMode('edit');
            });
            self.$btnView.on('click', function (e) {
                self.setMode('view');
            });
        },
        setMode: function (mode) {
            var self = this, t = self.fadeDelay;
            if (mode === 'edit') {
                self.$attribs.fadeOut(t, function () {
                    self.$formAttribs.fadeIn(t);
                });
                self.$buttons1.fadeOut(t, function () {
                    self.$buttons2.fadeIn(t);
                });
            }
            else {
                self.$formAttribs.fadeOut(t, function () {
                    self.$attribs.fadeIn(t);
                });
                self.$buttons2.fadeOut(t, function () {
                    self.$buttons1.fadeIn(t);
                });
            }
        },
        initElements: function () {
            var self = this;
            self.$btnUpdate = self.$element.find('.kv-btn-update');
            self.$btnDelete = self.$element.find('.kv-btn-delete');
            self.$btnView = self.$element.find('.kv-btn-view');
            self.$attribs = self.$element.find('.kv-attribute');
            self.$formAttribs = self.$element.find('.kv-form-attribute');
            self.$buttons1 = self.$element.find('.kv-buttons-1');
            self.$buttons2 = self.$element.find('.kv-buttons-2');
        }
    };

    //Detail View plugin definition
    $.fn.kvDetailView = function (option) {
        var args = Array.apply(null, arguments);
        args.shift();
        return this.each(function () {
            var $this = $(this),
                data = $this.data('kvDetailView'),
                options = typeof option === 'object' && option;

            if (!data) {
                $this.data('kvDetailView',
                    (data = new KvDetailView(this, $.extend({}, $.fn.kvDetailView.defaults, options, $(this).data()))));
            }

            if (typeof option === 'string') {
                data[option].apply(data, args);
            }
        });
    };

    $.fn.kvDetailView.defaults = {
        mode: 'view',
        fadeDelay: 800
    };
}(jQuery));