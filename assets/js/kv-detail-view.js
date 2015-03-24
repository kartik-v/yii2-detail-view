/*!
 * @package   yii2-detail-view
 * @author    Kartik Visweswaran <kartikv2@gmail.com>
 * @copyright Copyright &copy; Kartik Visweswaran, Krajee.com, 2014 - 2015
 * @version   1.7.1
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
        $.each(options, function (key, value) {
            self[key] = value;
        });
        self.init();
    };

    KvDetailView.prototype = {
        constructor: KvDetailView,
        init: function () {
            var self = this;
            self.initElements();
            self.listen();
        },
        alert: function(type, msg) {
            var self = this, css;
            css = self.alertMessageSettings[type];
            if (msg) {
                css = css || 'alert alert-' + type;
                return self.alertTemplate.replace('{content}', msg).replace('{class}', css);
            }
            return '';
        },
        initAlert: function () {
            var self = this, $alert = self.$element.find('.kv-alert-container');
            $alert.find('.alert .close').each(function() {
                var $el = $(this);
                $el.off('click').on('click', function() {
                    setTimeout(function() {
                        if (!$alert.find('.alert').length)  {
                            $alert.hide();
                        }
                    }, 300);
                });
            });            
        },
        listen: function () {
            var self = this, $alert = self.$element.find('.kv-alert-container'), 
                $detail = self.$element.find('.kv-detail-view');
            $detail.closest('form').on('afterValidate', function (event, messages) {
                if (messages !== undefined) {
                    $detail.removeClass('kv-detail-loading');
                }
            });
            self.$btnSave.on('click', function () {
                $alert.hide();
                $detail.removeClass('kv-detail-loading').addClass('kv-detail-loading');
            });
            self.$btnUpdate.on('click', function () {
                self.setMode('edit');
            });
            self.$btnView.on('click', function () {
                self.setMode('view');
            });
            self.$btnDelete.on('click', function (ev) {
            var $el = $(this), params = self.deleteParams, confirmMsg = self.deleteConfirm,
                settings = self.deleteAjaxSettings || {};
                ev.preventDefault();
                if  (confirmMsg && !confirm(confirmMsg)) {
                    return;
                }
                settings = $.extend({
                    type: 'post',
                    dataType: 'json',
                    data: params,
                    url: $el.attr('href'),
                    beforeSend: function() {
                        $alert.html('').hide();
                        $detail.removeClass('kv-detail-loading').addClass('kv-detail-loading');
                    },
                    success: function (data) {
                        if (data.success) {
                            $detail.hide();
                            self.$btnDelete.attr('disabled', 'disabled');
                            self.$btnUpdate.attr('disabled', 'disabled');
                            self.$btnView.attr('disabled', 'disabled');
                            self.$btnSave.attr('disabled', 'disabled');
                        };
                        $.each(data.messages, function(key, msg) {
                            $alert.append(self.alert(key, msg));
                        });
                        $alert.hide().fadeIn('slow', function() {
                            $detail.removeClass('kv-detail-loading'); 
                            self.initAlert();                           
                        });
                    },
                    error: function(xhr, txt, err) {
                        var msg = '';
                        if (self.showErrorStack) {
                            msg = $.trim($(xhr.responseText).text()).replace(/\n\s*\n/g, '\n').replace(/\</g, '&lt;');
                            msg = msg.length ? '<pre>' +  msg + '</pre>' : '';
                        }
                        msg = self.alert('kv-detail-error', err + msg);
                        $detail.removeClass('kv-detail-loading');
                        $alert.html(msg).hide().fadeIn('slow');
                        self.initAlert();
                    }
                }, settings);
                $.ajax(settings);
            });
            self.initAlert();
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
            self.$btnSave = $el.find('.kv-btn-save');
            self.$attribs = $el.find('.kv-attribute');
            self.$formAttribs = $el.find('.kv-form-attribute');
            self.$buttons1 = $el.find('.kv-buttons-1');
            self.$buttons2 = $el.find('.kv-buttons-2');
        }
    };

    //KvDetailView plugin definition
    $.fn.kvDetailView = function (option) {
        var args = Array.apply(null, arguments);
        args.shift();
        return this.each(function () {
            var $this = $(this),
                data = $this.data('kvDetailView'),
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
        fadeDelay: 800,
        alertTemplate: '',
        alertMessageSettings: {},
        deleteParams: {},
        deleteAjaxSettings: {},
        deleteConfirm: '',
        showErrorStack: false
    };

    $.fn.kvDetailView.Constructor = KvDetailView;
}(window.jQuery));