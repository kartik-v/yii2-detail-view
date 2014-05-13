/*!
 * @copyright Copyright &copy; Kartik Visweswaran, Krajee.com, 2014
 * @version 1.0.0
 *
 * Client actions for yii2-detail-view extension 
 * 
 * Author: Kartik Visweswaran
 * Copyright: 2014, Kartik Visweswaran, Krajee.com
 * For more JQuery plugins visit http://plugins.krajee.com
 * For more Yii related demos visit http://demos.krajee.com
 */

(function ($) {

    var KvDetailView = function (element, options) {
        this.$element = $(element);
        this.buttons1 = options.buttons1;
        this.buttons2 = options.buttons2;
        this.$buttons = self.$element.find('.kv-buttons');
        this.$btnUpdate = self.$element.find('.kv-btn-update');
        this.$btnDelete = self.$element.find('.kv-btn-delete');
        this.$btnView = self.$element.find('.kv-btn-view');
        this.$attribs = self.$element.find('.kv-attribute');
        this.$formAttribs = self.$element.find('.kv-form-attribute');
        self.init();
    };

    KvDetailView.prototype = {
        constructor: KvDetailView,
        init: function () {
            var self = this;
            self.$btnUpdate.on('click', function (e) {
                self.$attribs.addClass('kv-hide');
                self.$formAttribs.removeClass('kv-hide');
                self.$buttons.html(self.buttons2);
            });
            self.$btnView.on('click', function (e) {
                self.$attribs.removeClass('kv-hide');
                self.$formAttribs.addClass('kv-hide');
                self.$buttons.html(self.buttons1);
            });
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
                $this.data('kvDetailView', (data = new KvDetailView(this, $.extend({}, $.fn.kvDetailView.defaults, options, $(this).data()))));
            }

            if (typeof option === 'string') {
                data[option].apply(data, args);
            }
        });
    };
}(jQuery));

