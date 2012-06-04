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

    /**
     * Object control functions.
     */
    this.control = new function () {
        var controlObj = this;

        /**
         * Control function.
         */
        this._control = function(id, operation) {
            var url = $('tr[data-id=' + id + ']').data('control-url');

            $.ajax({
                dataType : 'json',
                url      : url,
                timeout  : system.ajaxTimeout,
                type     : 'POST',

                data : {
                    'EntryControlForm[operation]' : operation,
                    'EntryControlForm[id]'        : id,
                    'YII_CSRF_TOKEN'              : system.csrf
                },

                success : function (data, textStatus) {
                    $('.loader-image').hide();

                    if (data.status == 'error')
                    {
                        system.showMessage('error', data.errorText);
                        return;
                    }

                    if (operation == 'delete')
                    {
                        $('tr[data-id=' + id + ']').fadeOut('slow', undefined, function () {
                            $('tr[data-id=' + id + ']').remove();
                            system.showMessage('success', system.translate('Object successfully deleted.'));

                            if ($('table.table > tbody > tr').length == 1)
                                location.reload();
                        });
                    }
                },

                error : function(jqXHR, textStatus, e) {
                    $('.loader-image').hide();
                    system.showMessage('error', system.translate('Request failed, please try again.'));
                },

                beforeSend : function (jqXHR, settings) {
                    $('.loader-image').show();
                }
            });
        };

        /**
         * Delete object.
         */
        this.del = function (id) {
            $('tr[data-id=' + id + ']').addClass('delete-row');

            if (confirm(system.translate('Are you sure that you want to delete this object?')))
                controlObj._control(id, 'delete');
            else
                $('tr[data-id=' + id + ']').removeClass('delete-row');
        };
    };
}

var admin = new Admin();
