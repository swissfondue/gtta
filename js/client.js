/**
 * Client namespace.
 */
function Client() {
    /**
     * Check object
     */
    this.check = new function () {
        var _check = this;

        /**
         * Expand.
         * @param id
         * @param callback
         */
        this.expand = function (id, callback) {
            var header, form, url;

            header = $("div.check-header[data-type=check][data-id=" + id + "]");
            form = $("div.check-form[data-type=check][data-id=" + id + "]");

            if (header.data("limited")) {
                form.slideDown("slow", function () {
                    if (callback) {
                        callback();
                    }
                });

                return;
            }

            url = header.data("check-url");

            $.ajax({
                dataType: "json",
                url: url,
                timeout: system.ajaxTimeout,
                type: "POST",

                data : {
                    "YII_CSRF_TOKEN": system.csrf
                },

                success : function (data, textStatus) {
                    $(".loader-image").hide();
                    $("a[data-type=check-link][data-id=" + id + "]").removeClass("disabled");

                    if (data.status == "error") {
                        system.showMessage("error", data.errorText);
                        return;
                    }

                    form.html(data.data.html);
                    form.slideDown("slow", function () {
                        if (callback) {
                            callback();
                        }
                    });
                },

                error : function(jqXHR, textStatus, e) {
                    $(".loader-image").hide();
                    $("a[data-type=check-link][data-id=" + id + "]").removeClass("disabled");
                    system.showMessage("error", system.translate("Request failed, please try again."));
                },

                beforeSend : function (jqXHR, settings) {
                    $(".loader-image").show();
                    $("a[data-type=check-link][data-id=" + id + "]").addClass("disabled");
                }
            });
        };

        /**
         * Collapse.
         * @param id
         * @param callback
         */
        this.collapse = function (id, callback) {
            var selector = $("div.check-form[data-type=check][data-id=" + id + "]");

            selector.slideUp("slow", undefined, function () {
                if (!selector.data("limited")) {
                    $("div.check-form[data-type=check][data-id=" + id + "]").html("");
                }

                if (callback) {
                    callback();
                }
            });
        };

        /**
         * Toggle check
         * @param id
         */
        this.toggle = function (id) {
            if ($("a[data-type=check-link][data-id=" + id + "]").hasClass("disabled")) {
                return;
            }

            var visible = $("div.check-form[data-type=check][data-id=" + id + "]").is(":visible");

            if (visible) {
                _check.collapse(id, null);
            } else {
                _check.expand(id, null);
            }
        };

        /**
         * Expand solution.
         */
        this.expandSolution = function (id) {
            $('span.solution-control[data-id=' + id + ']').html('<a href="#solution" onclick="client.check.collapseSolution(' + id + ');"><i class="icon-chevron-up"></i></a>');
            $('div.solution-content[data-id=' + id + ']').slideDown('slow');
        };

        /**
         * Collapse solution.
         */
        this.collapseSolution = function (id) {
            $('span.solution-control[data-id=' + id + ']').html('<a href="#solution" onclick="client.check.expandSolution(' + id + ');"><i class="icon-chevron-down"></i></a>');
            $('div.solution-content[data-id=' + id + ']').slideUp('slow');
        };

        /**
         * Expand control.
         */
        this.expandControl = function (id, callback) {
            var url = $("div[data-id=" + id + "][data-type=control]").data("checklist-url");

            $.ajax({
                dataType: "json",
                url: url,
                timeout: system.ajaxTimeout,
                type: "POST",

                data : {
                    "YII_CSRF_TOKEN": system.csrf
                },

                success : function (data, textStatus) {
                    $(".loader-image").hide();
                    $("a[data-type=control-link][data-id=" + id + "]").removeClass("disabled");

                    if (data.status == "error") {
                        system.showMessage("error", data.errorText);
                        return;
                    }

                    var body = $("div.control-body[data-id=" + id + "]");

                    body.append(data.data.html);
                    body.slideDown("slow", function () {
                        if (callback) {
                            callback();
                        }
                    });
                },

                error : function(jqXHR, textStatus, e) {
                    $(".loader-image").hide();
                    $("a[data-type=control-link][data-id=" + id + "]").removeClass("disabled");
                    system.showMessage("error", system.translate("Request failed, please try again."));
                },

                beforeSend : function (jqXHR, settings) {
                    $(".loader-image").show();
                    $("a[data-type=control-link][data-id=" + id + "]").addClass("disabled");
                }
            });
        };

        /**
         * Collapse control.
         */
        this.collapseControl = function (id, callback) {
            $("div.control-body[data-id=" + id + "]").slideUp("slow", function () {
                $("div.control-body[data-id=" + id + "] div[data-type=check]").remove();

                if (callback) {
                    callback();
                }
            });
        };

        /**
         * Toggle control.
         */
        this.toggleControl = function (id) {
            if ($("a[data-type=control-link][data-id=" + id + "]").hasClass("disabled")) {
                return;
            }

            if ($("div.control-body[data-id=" + id + "]").is(":visible")) {
                _check.collapseControl(id, null);
            } else {
                _check.expandControl(id, null);
            }
        };

        /**
         * Expand custom check.
         * @param id
         * @param callback
         */
        this.expandCustomCheck = function (id, callback) {
            var selector = $("div.check-form[data-type=custom-check][data-id=" + id + "]");

            selector.slideDown("slow", undefined, function () {
                if (callback) {
                    callback();
                }
            });
        };

        /**
         * Collapse custom check.
         * @param id
         * @param callback
         */
        this.collapseCustomCheck = function (id, callback) {
            var selector = $("div.check-form[data-type=custom-check][data-id=" + id + "]");

            selector.slideUp("slow", undefined, function () {
                if (callback) {
                    callback();
                }
            });
        };

        /**
         * Toggle custom template.
         * @param id
         */
        this.toggleCustomCheck = function (id) {
            var visible = $("div.check-form[data-type=custom-check][data-id=" + id + "]").is(":visible");

            if (visible) {
                _check.collapseCustomCheck(id, null);
            } else {
                _check.expandCustomCheck(id, null);
            }
        };
    };
}

var client = new Client();
