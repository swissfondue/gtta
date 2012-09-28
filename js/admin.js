/**
 * Admin namespace.
 */
function Admin()
{
    /**
     * Check object.
     */
    this.check = new function () {
        var _check = this;

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
        var _user = this;

        /**
         * Display/hide client field.
         */
        this.toggleClientField = function () {
            if ($('#UserEditForm_role').val() == 'client')
                $('#client-input').show();
            else
                $('#client-input').hide();
        };

        /**
         * Disable select boxes.
         */
        this._setLoading = function () {
            $('#UserProjectAddForm select').prop('disabled', true);
        };

        /**
         * Enable select boxes.
         */
        this._setLoaded = function () {
            $('#UserProjectAddForm select').prop('disabled', false);
        };

        /**
         * Load a list of objects.
         */
        this._loadObjects = function (parentId, operation, callback) {
            var url = $('#UserProjectAddForm').data('object-list-url');

            $.ajax({
                dataType : 'json',
                url      : url,
                timeout  : system.ajaxTimeout,
                type     : 'POST',

                data : {
                    'EntryControlForm[id]'        : parentId,
                    'EntryControlForm[operation]' : operation,
                    'YII_CSRF_TOKEN'              : system.csrf
                },

                success : function (data, textStatus) {
                    $('.loader-image').hide();
                    _user._setLoaded();

                    if (data.status == 'error')
                    {
                        system.showMessage('error', data.errorText);
                        callback();

                        return;
                    }

                    callback(data.data);
                },

                error : function(jqXHR, textStatus, e) {
                    $('.loader-image').hide();
                    _user._setLoaded();
                    system.showMessage('error', system.translate('Request failed, please try again.'));
                    callback();
                },

                beforeSend : function (jqXHR, settings) {
                    $('.loader-image').show();
                    _user._setLoading();
                }
            });
        };

        /**
         * Project form has been changed.
         */
        this.projectFormChange = function (e) {
            var val;

            $('#client-list').removeClass('error');
            $('#client-list > div > .help-block').hide();

            if (e.id == 'UserProjectAddForm_clientId')
            {
                val = $('#UserProjectAddForm_clientId').val();

                $('#project-list').hide();
                $('#target-list').hide();
                $('.form-actions > button[type="submit"]').prop('disabled', true);

                if (val != 0)
                {
                    _user._loadObjects(val, 'project-list', function (data) {
                        $('#UserProjectAddForm_clientId').prop('disabled', false);
                        $('#UserProjectAddForm_projectId > option:not(:first)').remove();

                        if (data && data.objects.length)
                        {
                            for (var i = 0; i < data.objects.length; i++)
                                $('<option>')
                                    .val(data.objects[i].id)
                                    .html(data.objects[i].name)
                                    .appendTo('#UserProjectAddForm_projectId');

                            $('#project-list').show();
                        }
                        else
                        {
                            $('#client-list').addClass('error');
                            $('#client-list > div > .help-block').show();
                        }
                    });
                }
            }
            else
            {
                val = $('#UserProjectAddForm_projectId').val();

                if (val == 0)
                    $('.form-actions > button[type="submit"]').prop('disabled', true);
                else
                    $('.form-actions > button[type="submit"]').prop('disabled', false);
            }
        };
    };

    /**
     * Risk category object object.
     */
    this.riskCategory = new function () {
        var _riskCategory = this;

        /**
         * Toggle risk category check.
         */
        this.checkToggle = function (id) {
            if ($('div.risk-category-check-content[data-id=' + id + ']').is(':visible'))
                $('div.risk-category-check-content[data-id=' + id + ']').slideUp('slow');
            else
                $('div.risk-category-check-content[data-id=' + id + ']').slideDown('slow');
        };

        /**
         * Toggle risk category control.
         */
        this.controlToggle = function (id) {
            if ($('div.risk-category-control-content[data-id=' + id + ']').is(':visible'))
                $('div.risk-category-control-content[data-id=' + id + ']').slideUp('slow');
            else
                $('div.risk-category-control-content[data-id=' + id + ']').slideDown('slow');
        };

        /**
         * Expand all checks.
         */
        this.expandAll = function () {
            $('div.risk-category-control-content').slideDown('fast', undefined, function () {
                $('div.risk-category-check-content').slideDown('slow');
            });
        };

        /**
         * Collapse all checks.
         */
        this.collapseAll = function () {
            $('div.risk-category-check-content').slideUp('fast', undefined, function () {
                $('div.risk-category-control-content').slideUp('slow');
            });
        };
    };

    /**
     * Project object.
     */
    this.project = new function () {
        var _project = this;

        /**
         * User add form has been changed.
         */
        this.userAddFormChange = function () {
            var option = $('#ProjectUserAddForm_userId > option:selected');

            if (option.data('role') == 'admin')
            {
                $('#ProjectUserAddForm_admin').prop('checked', true);
                $('#ProjectUserAddForm_admin').prop('disabled', true);
            }
            else
            {
                $('#ProjectUserAddForm_admin').prop('checked', false);
                $('#ProjectUserAddForm_admin').prop('disabled', false);
            }
        };
    };
}

var admin = new Admin();
