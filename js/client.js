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
         */
        this.expand = function (id, callback) {
            $('div.check-form[data-id=' + id + ']').slideDown('slow', undefined, function () {
                if (callback)
                    callback();
            });
        };

        /**
         * Collapse.
         */
        this.collapse = function (id, callback) {
            $('div.check-form[data-id=' + id + ']').slideUp('slow', undefined, function () {
                if (callback)
                    callback();
            });
        };

        /**
         * Toggle.
         */
        this.toggle = function (id) {
            if ($('div.check-form[data-id=' + id + ']').is(':visible'))
                _check.collapse(id);
            else
                _check.expand(id);
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
    };
}

var client = new Client();
