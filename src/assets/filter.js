var listFilter = {
    filterFormClass: '.list-filter-form',
    toolbarClass: '.list-filter-toolbar',
    resetClass: '.list-filter-form-reset',
    rangeClass: '.list-filter-form-range',
    id: null,
    pjax: null,
    timeout: 15000,
    init: function() {
        var self = this;

        jQuery(self.filterFormClass + '.no-js').removeClass('no-js');

        jQuery(self.rangeClass).each(function( index ) {
            self.slideInit(jQuery(this));
        });

        jQuery(document).on('change', self.filterFormClass, function (event) {
            if (! self.pjax) self.pjax = jQuery.pjax;

            self.pjax.submit(event, '#' + jQuery(this).data('listId') + '-pjax', {push: true, replace: false, timeout: self.timeout, scrollTo: false});
        });

        jQuery(document).on("click", self.toolbarClass + " a", function (event) {
            var name = jQuery(this).data('name');
            var value = jQuery(this).data('value');

            jQuery(self.filterFormClass + " input[name='" + name + "']").val(value);
        });

        jQuery(document).on("click", self.resetClass, function (event) {
            var form = jQuery(this).closest(self.filterFormClass);

            jQuery(form.find("input")).each(function( index ) {
                self.resetField(jQuery(this));
            });

            if (! self.pjax) self.pjax = jQuery.pjax;
            self.pjax({url: jQuery(this).attr('url'), container: '#' + jQuery(this).closest(self.filterFormClass).data('listId') + '-pjax', push: true, replace: false, timeout: self.timeout, scrollTo: jQuery('#' + self.pjaxId).position().top});

            return false;
        });

        jQuery(document).on("change", self.filterFormClass + " input", function (event) {
            self.changeField(jQuery(this));
        });

        jQuery(document).on("click", self.filterFormClass + " label a", function (event) {
            var checkbox = jQuery(this).closest('label').find('.checkbox');
            checkbox.prop('checked', !checkbox.prop("checked")).change();

            return false;
        });

        jQuery(document).on("hide.bs.collapse show.bs.collapse", self.filterFormClass + " .list-filter-form-more", function (event) {
            jQuery(this).next().find(".fa").toggleClass("fa-angle-double-down fa-angle-double-up");
        });

        jQuery(document).on("hide.bs.collapse show.bs.collapse", self.filterFormClass + " .list-filter-form-all-values", function (event) {
            jQuery(this).prev().find(".fa").toggleClass("fa-angle-down fa-angle-up");
        });
    },
    changeField: function (el) {
        var self = this;
        var cl = el.attr('id').split('_')[1];

        switch(el.attr('type')) {
            case 'checkbox':
                jQuery('.' + cl).each(function() {
                    if (jQuery(this).attr('id') != el.attr('id')) jQuery(this).prop('checked', el.prop("checked"));
                });

                break;
            default:
                jQuery('.' + cl).each(function() {
                    if (jQuery(this).attr('id') != el.attr('id')) {
                        jQuery(this).val( el.val() );

                        var range = jQuery(this).closest('.product-list-filter-range');
                        if (range) self.slideInit(range);
                    }
                });
        }
    },
    resetField: function(el) {
        var type = el.attr('type');

        switch (type) {
            case 'hidden':
                break;
            case 'checkbox':
                el.attr('checked', false);
                break;
            case 'text':
                el.val('');
                break;
            case 'number':
                if (el.attr('name').indexOf('[from]') > 0) {
                    el.val(el.attr('min'));
                } else if (el.attr('name').indexOf('[to]') > 0) {
                    el.val(el.attr('max'));
                } else {
                    el.val('');
                }

                var range = el.closest('.product-list-filter-range');
                if (range) this.slideInit(range);
                break;
        }

        this.changeField(el);
    },
    slideInit: function(el) {
        var self = this;
        var sliderClass = 'list-filter-form-slider';
        var resultClass = 'list-filter-form-slider-result';

        var unit = el.find(".list-filter-form-range-unit").text();
        var fromEl = el.find("input[type='number']").eq(0);
        var toEl = el.find("input[type='number']").eq(1);
        var min = parseFloat(fromEl.attr("min"));
        var max = parseFloat(toEl.attr("max"));
        var step = fromEl.attr('step') == 'any' ? parseFloat(0.1) : parseFloat(fromEl.attr('step'));

        if (el.find("." + sliderClass).hasClass('ui-slider')) {
            el.find("." + sliderClass).remove();
            el.find("." + resultClass).remove();
            el.find(".clearfix").remove();
        }

        if ((min || min === 0) && max) {
            el.find("div").hide();
            el.append("<div class=\"" + sliderClass + "\"></div>");
            el.append("<div class=\"" + resultClass + " input-group-addon\">" + self.prettyNum(fromEl.val()) + " - " + self.prettyNum(toEl.val()) + " " + unit + "</div><div class='clearfix'></div>");

            var options = {
                range: true,
                min: min,
                max: max,
                step: step,
                values: [ fromEl.val(), toEl.val() ],
                slide: function ( event, ui ) {
                    el.find("." + resultClass).text(self.prettyNum(ui.values[0]) + " - " + self.prettyNum(ui.values[1]) + " " + unit);
                },
                stop: function( event, ui ) {
                    if (fromEl.val() != ui.values[0]) {
                        fromEl.val(ui.values[0]);
                        fromEl.change();
                    }
                    if (toEl.val() != ui.values[1]) {
                        toEl.val(ui.values[1]);
                        toEl.change();
                    }

                    el.find("." + resultClass).text(self.prettyNum(ui.values[0]) + " - " + self.prettyNum(ui.values[1]) + " " + unit);
                }
            };

            el.find("." + sliderClass).slider(options);
        }

        // Work on reset?
        /*jQuery(fromEl).change(function() {
            if (! jQuery(this).val()) {
                var min = parseFloat(fromEl.attr("min"));
                jQuery(this).val(min);
                options.min = min;
                options.values[0] = min;
                el.find("." + sliderClass).slider("destroy");
                el.find("." + sliderClass).slider(options);
            }
        });
        jQuery(toEl).change(function() {
            if (! jQuery(this).val()) {
                var max = parseFloat(fromEl.attr("max"));
                jQuery(this).val(max);
                options.max = max;
                options.values[1] = max;
                el.find("." + sliderClass).slider("destroy");
                el.find("." + sliderClass).slider(options);
            }
        });*/
    },
    prettyNum: function numberWithCommas(x) {
        return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, " ");
    }
};
listFilter.init();
