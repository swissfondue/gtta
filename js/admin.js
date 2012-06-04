/**
 * Admin namespace.
 */
function Admin()
{
    /**
     * Check object.
     */
    this.check = new function () {
        /**
         * Update tied field value.
         */
        this.updateTiedField = function (from, to) {
            $('#' + to).val($('#' + from).val());
        };

        /**
         * Display/hide script field.
         */
        this.toggleScriptField = function () {
            if ($('#CheckEditForm_automated').is(':checked'))
                $('#script-input').show();
            else
                $('#script-input').hide();
        };
    };

    /**
     * User object.
     */
    this.user = new function () {
        /**
         * Display/hide client field.
         */
        this.toggleClientField = function () {
            if ($('#UserEditForm_role').val() == 'client')
                $('#client-input').show();
            else
                $('#client-input').hide();
        };
    };
}

var admin = new Admin();
