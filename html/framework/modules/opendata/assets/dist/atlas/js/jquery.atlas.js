(function ($) {
    $.fn.atlas = function (paths, options) {
        if (!paths) {
            console.error('paths argument is required');
            return this;
        }
        // Default values
        var defaults = {
            data: null,
            map_attributes: {
                highlight: '#1F3684',
                fill: '#e1e1e1',
                stroke: '#ffffff',
                'stroke-width': 1,
                'stroke-linejoin': 'round'
            },
            orig_width: 1200,
            orig_height: 640,
            regionPopupTemplateSelector: '#regionPopupTemplate'
        };

        options = $.extend(defaults, options);

        // Apply to each matching item
        return this.each(function () {
            var self = this;
            self.area_obj = [];
            self.data_colors = [];
            // Get handle on current obj
            var obj = $(this);
            self.mapWidth = obj.innerWidth();
            self.mapTopOffset = obj.offset().top;

            // register events

            this.registerAreaEvents = function (area) {
                var point;

                area.mouseover(function (event) {
                    var mouseY, regid = this.id.slice(3);

                    if (self.lock_over) return;
                    self.lock_over = 1;

                    mouseY = event.pageY - self.mapTopOffset; // event.Y || (event.clientY + document.body.scrollTop + document.documentElement.scrollTop);
                    point = this.getBBox(0);

                    self.template_regionPopup(regid, point, mouseY);
                    console.log('here');
                    self.area_obj[regid].animate({fill: options.map_attributes.highlight}, 0);
                    self.area_obj[regid].animate({stroke: '#1F3684'}, 0);
                    
                    self.lock_over = 0;
                });

                area.mouseout(function () {
                    var regid = this.id.slice(3);

                    if (self.lock_over) return;
                    self.lock_over = 1;

                    self.paintRegions(self.current_rate, regid);
                    // hide popup table
                    if (self.popup) self.popup.hide();

                    self.lock_over = 0;
                });
            };

            this.template_regionPopup = function(region, point, mouseY){
                var region_title = paths['reg' + region].name,
                    data = options.data[region_title];

                if(!data){
                    return;
                }

                var  template = $(options.regionPopupTemplateSelector);
                if (template.length === 0) {
                    return false;
                }

                // compile template
                if (!self.compiled) {
                    self.compiled = _.template(template.html());
                }
                // render template
                var html = self.compiled({
                    region: region_title,
                    data: data
                });

                if (!self.popup) {
                    self.popup = $('<div class="digit-box digit-box--opendata">' + html + '</div>').appendTo(obj.parent());
                } else {
                    self.popup.html(html);
                }
                self.popup.css({
                    'visibility': 'hidden',
                    'display': 'block'
                });
                var height = self.popup.height();
                self.popup.css({
                    'visibility': 'visible',
                    'display': 'none'
                });

                self.popup.css({
                    left: point.x - 80 + Math.round(point.width / 2),
                    top: mouseY -height + 120
                }).show();
            };

            this.factoryCompare = function (value, color) {
                var result,
                    regExp_exact = /^-?\d+(\.\d+)?$/,
                    regExp_less = /^:-?\d+(\.\d+)?$/,
                    regExp_more = /^-?\d+(\.\d+)?:$/,
                    regExp_range = /^(-?\d+(\.\d+)?):(-?\d+(\.\d+)?)$/;

                result = regExp_exact.exec(value);
                if (result !== null) {
                    return {
                        check: function (v) {
                            var value = parseInt(result[0], 10);
                            if (v === value) return color;
                            return false;
                        }
                    }
                }

                result = regExp_range.exec(value);
                if (result !== null) {
                    return {
                        check: function (v) {
                            var from = parseFloat(result[1]),
                                to = parseFloat(result[3]);

                            if (from < to && v >= from && v < to) return color;
                            return false;
                        }
                    }
                }

                result = regExp_less.exec(value);
                if (result !== null) {
                    return {
                        check: function (v) {
                            var to = parseFloat(result[0].slice(1));
                            if (v < to) return color;
                            return false;
                        }
                    }
                }

                result = regExp_more.exec(value);
                if (result !== null) {
                    return {
                        check: function (v) {
                            var from = parseFloat(result[0].substr(0, result[0].length - 1));
                            if (v >= from) return color;
                            return false;
                        }
                    }
                }

                return {
                    check: function () {
                        return false;
                    }
                }

            };

            this.initMap = function () {
                var region, area, reg_id,
                    resizeRate = (self.mapWidth / options.orig_width).toFixed(2);

                self.map = Raphael(obj.attr('id'), self.mapWidth, resizeRate * options.orig_height);
                for (region in paths) {
                    if (!paths.hasOwnProperty(region)) {
                        continue;
                    }

                    area = self.map.path(paths[region].path.replace(/\d+\.?\d*/g,
                        function (digit) {
                            return (digit * resizeRate).toFixed(1);
                        }
                    ));
                    reg_id = region.slice(3);

                    self.registerAreaEvents(area);

                    area.id = region;
                    area.attr(options.map_attributes);
                    self.area_obj[reg_id] = area;
                }
            };

            this.paintRegions = function (code, regid) {
                var i, area, color, f, len, value, id, regions = [];
                if (regid) {
                    regions[regid] = self.area_obj[regid];
                } else {
                    regions = self.area_obj;
                }
                for (i in regions) {
                    if (!regions.hasOwnProperty(i)) {
                        continue;
                    }
                    area = regions[i];
                    id = area.id.slice(3);
                    color = false;

                    if (!color) color = options.map_attributes.fill;
                    area.animate({fill: color}, 0);
                    area.animate({stroke: '#ffffff'}, 0);
                }
            };

            // init
            self.initMap();
        });
    }
})(jQuery);