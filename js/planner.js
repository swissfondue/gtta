(function($) {
    /**
     * Planner class
     * @param target
     * @param options
     * @constructor
     */
    function Planner(target, options) {
        var planner = this;

        this.SIZE = 90;
        this.MONTH_NAMES = [
            "Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"
        ];

        this.target = target;
        this.dataUrl = options.dataUrl;
        this.controlUrl = options.controlUrl;
        this.currentDate = null;
        this.startDate = null;
        this.endDate = null;
        this.users = [];

        /**
         * Load data.
         */
        this.loadData = function () {
            $.ajax({
                dataType: "json",
                url: this.dataUrl,
                timeout: system.ajaxTimeout,
                type: "POST",

                data: {
                    "ProjectPlannerLoadForm[startDate]": this.startDate.toJSON().substring(0, 10),
                    "ProjectPlannerLoadForm[endDate]": this.endDate.toJSON().substring(0, 10),
                    "YII_CSRF_TOKEN": system.csrf
                },

                success: function (data, textStatus) {
                    $(".loader-image").hide();

                    if (data.status == "error") {
                        system.showMessage("error", data.errorText);
                        return;
                    }

                    planner.users = data.data.users;
                    planner.redraw();
                },

                error: function(jqXHR, textStatus, e) {
                    $(".loader-image").hide();
                    system.showMessage("error", system.translate("Request failed, please try again."));
                },

                beforeSend: function (jqXHR, settings) {
                    $(".loader-image").show();
                }
            });
        };

        /**
         * Toggle user
         * @param id
         */
        this.toggleUser = function (id) {
            var leftPlans, rightPlans;

            leftPlans = planner.target.find(".users > .user#" + id + " > .plans");
            rightPlans = planner.target.find(".timeline-container > .timeline > .body > .user#" + id + " > .plans");

            if (!leftPlans.find(".plan").length) {
                return;
            }

            leftPlans.slideToggle("fast");
            rightPlans.slideToggle("fast");
        };

        /**
         * Draw a list of users
         * @private
         */
        this._drawUserList = function () {
            this.target.find(".users > .user").remove();

            for (var i = 0; i < this.users.length; i++) {
                var user, plans;

                user = this.users[i];
                plans = [];

                for (var k = 0; k < user.data.length; k++) {
                    var plan, planObj;

                    plan = user.data[k];
                    planObj = $("<div></div>")
                        .addClass("plan")
                        .append(
                            $("<div></div>")
                                .addClass("pull-left")
                                .css("margin-right", 10)
                                .append(
                                    $("<a></a>")
                                        .attr("href", "#del")
                                        .attr("title", system.translate("Delete"))
                                        .click(
                                            (function (plan, planner) {
                                                return function () {
                                                    admin.planner.del(plan.id, planner.controlUrl, function () {
                                                        planner.loadData();
                                                    });
                                                }
                                            })(plan, planner)
                                        )
                                        .append(
                                            $("<i></i>")
                                                .addClass("icon")
                                                .addClass("icon-remove")
                                        )
                                ),

                            $("<a></a>")
                                .attr("href", plan.link)
                                .html(plan.name)
                        );

                    plans.push(planObj);
                }

                this.target.find(".users").append(
                    $("<div></div>")
                        .addClass("user")
                        .attr("id", user.id)
                        .append(
                            $("<div></div>")
                                .addClass("name")
                                .append(
                                    $("<a></a>")
                                        .attr("href", "#")
                                        .html(user.name)
                                        .click(
                                            (function (userId) {
                                                return function () {
                                                    planner.toggleUser(userId);
                                                }
                                            })(user.id)
                                        )
                                ),

                            $("<div></div>")
                                .addClass("plans")
                                .addClass("hide")
                                .append(plans)
                        )
                );
            }
        };

        /**
         * Draw the calendar
         * @private
         */
        this._drawCalendar = function () {
            this.target.find(".timeline > .calendar > .month").remove();
            var months, curDate, curMonth, monthObject;

            months = [];
            curDate = new Date(this.startDate.valueOf());
            curMonth = null;
            monthObject = null;

            while (curDate <= this.endDate) {
                if (!monthObject || curDate.getMonth() != curMonth) {
                    if (monthObject) {
                        months.push(monthObject);
                    }

                    monthObject = $("<div></div>")
                        .addClass("month")
                        .append(
                            $("<div></div>")
                                .addClass("header")
                                .html(this.MONTH_NAMES[curDate.getMonth()] + " " + curDate.getFullYear()),

                            $("<div></div>")
                                .addClass("body")
                        );

                    curMonth = curDate.getMonth();
                }

                monthObject.find(".body").append(
                    $("<div></div>")
                        .addClass("day")
                        .html(curDate.getDate())
                );

                curDate.setDate(curDate.getDate() + 1);
            }

            // push the last month
            if (monthObject) {
                months.push(monthObject);
            }

            this.target.find(".timeline > .calendar").append(months);
        };

        /**
         * Parse ISO date
         * @param date
         * @returns {Date}
         * @private
         */
        this._parseIsoDate = function (date) {
            var parts = date.split("-");
            return new Date(parts[0], parts[1] - 1, parts[2]);
        };

        /**
         * Date difference in days
         * @param date1
         * @param date2
         * @returns {number}
         * @private
         */
        this._dateDiff = function (date1, date2) {
            return Math.abs(Math.floor((date2 - date1) / (24 * 3600 * 1000)));
        };

        /**
         * Draw user's data.
         * @private
         */
        this._drawData = function () {
            this.target.find(".timeline > .body > .user").remove();
            var users, i, k;

            users = [];

            for (i = 0; i < this.users.length; i++) {
                var user, userObj, ranges, rangeList, range, plans, rangeObj, x, duration;

                user = this.users[i];
                userObj = $("<div></div>")
                    .addClass("user")
                    .attr("id", user.id);

                ranges = [];
                rangeList = [];
                plans = [];

                for (k = 0; k < user.data.length; k++) {
                    var rangeStart, rangeEnd, planObj;

                    range = user.data[k];
                    rangeStart = this._parseIsoDate(range.startDate);
                    rangeEnd = this._parseIsoDate(range.endDate);

                    if (rangeStart < this.startDate) {
                        rangeStart = this.startDate;
                    }

                    if (rangeEnd > this.endDate) {
                        rangeEnd = this.endDate;
                    }

                    ranges.push([rangeStart, rangeEnd]);

                    rangeObj = $("<div></div>")
                        .addClass("range");

                    x = this._dateDiff(rangeStart, this.startDate) * 30;
                    rangeObj.css("left", x);
                    duration = (this._dateDiff(rangeStart, rangeEnd) + 1) * 30;
                    rangeObj.css("width", duration);

                    $("<div></div>")
                        .addClass("finished")
                        .css("width", range.finished)
                        .html(range.finished == "0%" ? "" : range.finished)
                        .appendTo(rangeObj);

                    planObj = $("<div></div>")
                        .addClass("plan")
                        .attr("id", range.id)
                        .append(rangeObj);

                    plans.push(planObj);
                }

                var top = 5;

                for (k = 0; k < ranges.length; k++) {
                    range = ranges[k];
                    rangeObj = $("<div></div>")
                        .addClass("range");

                    x = this._dateDiff(range[0], this.startDate) * 30;
                    rangeObj.css("left", x);
                    rangeObj.css("top", top);

                    duration = (this._dateDiff(range[0], range[1]) + 1) * 30;
                    rangeObj.css("width", duration);

                    rangeList.push(rangeObj);
                    top -= 20;
                }

                userObj.append(
                    $("<div></div>")
                        .addClass("ranges")
                        .append(rangeList),

                    $("<div></div>")
                        .addClass("plans")
                        .addClass("hide")
                        .append(plans)
                );

                users.push(userObj);
            }

            this.target.find(".timeline > .body").append(users);
        };

        /**
         * Redraw the widget.
         */
        this.redraw = function () {
            this._drawUserList();
            this._drawCalendar();
            this._drawData();
        };

        /**
         * Init the widget.
         */
        this.init = function () {
            this.currentDate = new Date();
            this.currentDate.setMinutes(0);
            this.currentDate.setHours(0);
            this.currentDate.setSeconds(0);
            this.currentDate.setMilliseconds(0);

            this.startDate = new Date(this.currentDate.valueOf());
            this.startDate.setDate(this.startDate.getDate() - 14);
            this.endDate = new Date(this.startDate.valueOf());
            this.endDate.setDate(this.endDate.getDate() + this.SIZE);

            this.target.html("");
            this.target.append(
                $("<div></div>")
                    .addClass("users")
                    .append(
                        $("<div></div>")
                            .addClass("header")
                            .html(system.translate("Users / Days"))
                    ),

                $("<div></div>")
                    .addClass("timeline-container")
                    .append(
                        $("<div></div>")
                            .addClass("timeline")
                            .append(
                                $("<div></div>")
                                    .addClass("calendar"),

                                $("<div></div>")
                                    .addClass("body")
                            )
                    )
            );

            this.loadData();
        };
    }

    /**
     * GTTA Planner
     * @returns {*}
     */
    $.fn.planner = function(options) {
        return this.each(function () {
            var planner = new Planner($(this), options);
            planner.init();
        })
    };
}(jQuery));