/*!
 * @package   yii2-detail-view
 * @author    Kartik Visweswaran <kartikv2@gmail.com>
 * @copyright Copyright &copy; Kartik Visweswaran, Krajee.com, 2014 - 2015
 * @version   1.7.0
 *
 * Client extension for the yii2-detail-view extension 
 * 
 * Author: Kartik Visweswaran
 * Copyright: 2014 - 2015, Kartik Visweswaran, Krajee.com
 * For more JQuery plugins visit http://plugins.krajee.com
 * For more Yii related demos visit http://demos.krajee.com
 */

(function ($) {
    "use strict";

    var KvDetailView = function (element, options) {
        var self = this;
        self.$element = $(element);
        self.mode = options.mode;
        self.fadeDelay = options.fadeDelay;
        self.init();
    };

    KvDetailView.prototype = {
        constructor: KvDetailView,
        init: function () {
            var self = this;
            self.initElements();
            self.$btnUpdate.on('click', function () {
                self.setMode('edit');
            });
            self.$btnView.on('click', function () {
                self.setMode('view');
            });
        },
        setMode: function (mode) {
            var self = this, t = self.fadeDelay;
            if (mode === 'edit') {
                self.$attribs.fadeOut(t, function () {
                    self.$formAttribs.fadeIn(t);
                    self.$element.removeClass('kv-view-mode kv-edit-mode').addClass('kv-edit-mode');
                });
                self.$buttons1.fadeOut(t, function () {
                    self.$buttons2.fadeIn(t);
                });
            }
            else {
                self.$formAttribs.fadeOut(t, function () {
                    self.$attribs.fadeIn(t);
                    self.$element.removeClass('kv-view-mode kv-edit-mode').addClass('kv-view-mode');
                });
                self.$buttons2.fadeOut(t, function () {
                    self.$buttons1.fadeIn(t);
                });
            }
        },
        initElements: function () {
            var self = this, $el = self.$element;
            self.$btnUpdate = $el.find('.kv-btn-update');
            self.$btnDelete = $el.find('.kv-btn-delete');
            self.$btnView = $el.find('.kv-btn-view');
            self.$attribs = $el.find('.kv-attribute');
            self.$formAttribs = $el.find('.kv-form-attribute');
            self.$buttons1 = $el.find('.kv-buttons-1');
            self.$buttons2 = $el.find('.kv-buttons-2');
        }
    };

    $.fn.kvDetailView = function (option) {
        var args = Array.apply(null, arguments);
        args.shift();
        return this.each(function () {
            var $this = $(this), data = $this.data('kvDetailView'),
                options = typeof option === 'object' && option;

            if (!data) {
                data = new KvDetailView(this, $.extend({}, $.fn.kvDetailView.defaults, options, $(this).data()));
                $this.data('kvDetailView', data);
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

    $.fn.kvDetailView.Constructor = KvDetailView;
}(window.jQuery));