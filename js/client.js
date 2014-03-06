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
         * @param custom
         */
        this.expand = function (id, callback, custom) {
            var selector = custom ?
                $("div.check-form[data-id=custom-" + id + "]") :
                $("div.check-form[data-id=" + id + "]");

            selector.slideDown("slow", undefined, function () {
                if (callback) {
                    callback();
                }
            });
        };

        /**
         * Collapse.
         * @param id
         * @param callback
         * @param custom
         */
        this.collapse = function (id, callback, custom) {
            var selector = custom ?
                $("div.check-form[data-id=custom-" + id + "]") :
                $("div.check-form[data-id=" + id + "]");

            selector.slideUp("slow", undefined, function () {
                if (callback) {
                    callback();
                }
            });
        };

        /**
         * Toggle check
         * @param id
         * @param custom
         */
        this.toggle = function (id, custom) {
            var visible = custom ?
                $("div.check-form[data-id=custom-" + id + "]").is(":visible") :
                $("div.check-form[data-id=" + id + "]").is(":visible");

            if (visible) {
                _check.collapse(id, null, custom);
            } else {
                _check.expand(id, null, custom);
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
        this.expandControl = function (id) {
            $('div.control-body[data-id=' + id + ']').slideDown('slow');
        };

        /**
         * Collapse control.
         */
        this.collapseControl = function (id) {
            $('div.control-body[data-id=' + id + ']').slideUp('slow');
        };

        /**
         * Toggle control.
         */
        this.toggleControl = function (id) {
            if ($('div.control-body[data-id=' + id + ']').is(':visible')) {
                _check.collapseControl(id);
            } else {
                _check.expandControl(id);
            }
        };
    };
}

var client = new Client();
