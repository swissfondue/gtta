/**
 * System class.
 */
function System()
{
    var _system = this;

    // attributes
    this.csrf            = null;
    this.ajaxTimeout     = 5000;
    this.messageTimeout  = 5000;
    this.l10nMessages    = {};
    this._messageTimeout = null;

    // constants
    this.RATING_HIGH_RISK = 'high_risk';
    this.RATING_MED_RISK  = 'med_risk';

    /**
     * Shows a message.
     */
    this.showMessage = function (eventType, message) {
        $('.message-container').html('');

        $('<div>', {
            'class' : 'alert alert-' + eventType + ' hide',
            html    : '<a class="close" data-dismiss="alert">×</a>' + message
        }).appendTo('.message-container');

        $('html, body').animate({ scrollTop : 0 }, 'fast', function () {
            $('.message-container > div').fadeIn('slow');

            if (_system._messageTimeout)
                clearTimeout(_system._messageTimeout);

            _system._messageTimeout = setTimeout(function () {
                $('.message-container > div').fadeOut('slow');
                _system._messageTimeout = null;
            }, _system.messageTimeout);
        });
    };

    /**
     * Set language.
     */
    this.setLanguage = function (language) {
        $.cookie('language', language, { path : '/' });
        location.reload();
    };

    /**
     * Get translated string.
     */
    this.translate = function (sourceString) {
        if (sourceString in _system.l10nMessages)
            return _system.l10nMessages[sourceString];

        return sourceString;
    };

    /**
     * Toggle collapsible content block.
     */
    this.toggleBlock = function (blockId) {
        $(blockId + '-icon > i').removeClass();

        if ($(blockId).is(':visible'))
            $(blockId + '-icon > i').addClass('icon-chevron-down');
        else
            $(blockId + '-icon > i').addClass('icon-chevron-up');

        $(blockId).slideToggle('slow');
    };

    /**
     * Object control functions.
     */
    this.control = new function () {
        var _system_control = this;

        /**
         * Control function.
         */
        this._control = function(id, operation) {
            var url = $('tr[data-id=' + id + ']').data('control-url');

            $.ajax({
                dataType : 'json',
                url      : url,
                timeout  : _system.ajaxTimeout,
                type     : 'POST',

                data : {
                    'EntryControlForm[operation]' : operation,
                    'EntryControlForm[id]'        : id,
                    'YII_CSRF_TOKEN'              : _system.csrf
                },

                success : function (data, textStatus) {
                    $('.loader-image').hide();

                    if (data.status == 'error')
                    {
                        _system.showMessage('error', data.errorText);
                        return;
                    }

                    if (operation == 'delete')
                    {
                        $('tr[data-id=' + id + ']').fadeOut('slow', undefined, function () {
                            $('tr[data-id=' + id + ']').remove();
                            _system.showMessage('success', _system.translate('Object deleted.'));

                            if ($('table.table > tbody > tr').length == 1)
                                location.reload();
                        });
                    }
                },

                error : function(jqXHR, textStatus, e) {
                    $('.loader-image').hide();
                    _system.showMessage('error', _system.translate('Request failed, please try again.'));
                },

                beforeSend : function (jqXHR, settings) {
                    $('.loader-image').show();
                }
            });
        };

        /**
         * Delete object.
         */
        this.del = function (id, message) {
            $('tr[data-id=' + id + ']').addClass('delete-row');

            if (
                confirm(_system.translate('Are you sure that you want to delete this object?')) &&
                (message == undefined || (message != undefined && confirm(message + '\n\n' + _system.translate('PROCEED AT YOUR OWN RISK!'))))
            )
                _system_control._control(id, 'delete');
            else
                $('tr[data-id=' + id + ']').removeClass('delete-row');
        };

        /**
         * Disable inputs and select boxes.
         */
        this._setLoading = function () {
            $('#object-selection-form input').prop('disabled', true);
            $('#object-selection-form select').prop('disabled', true);
        };

        /**
         * Enable inputs and select boxes.
         */
        this._setLoaded = function () {
            $('#object-selection-form input').prop('disabled', false);
            $('#object-selection-form select').prop('disabled', false);
        };

        /**
         * Load a list of objects.
         */
        this.loadObjects = function (parentId, operation, callback) {
            var url = $('#object-selection-form').data('object-list-url');

            $.ajax({
                dataType : 'json',
                url      : url,
                timeout  : _system.ajaxTimeout,
                type     : 'POST',

                data : {
                    'EntryControlForm[id]'        : parentId,
                    'EntryControlForm[operation]' : operation,
                    'YII_CSRF_TOKEN'              : _system.csrf
                },

                success : function (data, textStatus) {
                    $('.loader-image').hide();
                    _system_control._setLoaded();

                    if (data.status == 'error')
                    {
                        _system.showMessage('error', data.errorText);
                        callback();

                        return;
                    }

                    callback(data.data);
                },

                error : function(jqXHR, textStatus, e) {
                    $('.loader-image').hide();
                    _system_control._setLoaded();
                    _system.showMessage('error', _system.translate('Request failed, please try again.'));
                    callback();
                },

                beforeSend : function (jqXHR, settings) {
                    $('.loader-image').show();
                    _system_control._setLoading();
                }
            });
        };
    };

    /**
     * Search form functions.
     */
    this.search = new function () {
        var _search = this;

        /**
         * Validate search form.
         */
        this.validate = function () {
            var query = $('.search-query').val();

            if (query == '' || query.length < 3 || query == _system.translate('Search...'))
                return false;

            return true;
        };

        /**
         * On focus handler.
         */
        this.focus = function () {
            if ($('.search-query').val() == _system.translate('Search...'))
                $('.search-query').val('');
        };

        /**
         * On blur handler.
         */
        this.blur = function () {
            if ($('.search-query').val() == '')
                $('.search-query').val(_system.translate('Search...'));
        };
    };

    /**
     * Project object.
     */
    this.project = new function () {
        var _project = this;

        /**
         * Filter has been changed.
         */
        this.filterChange = function () {
            var i, realStatus, status, sortBy, sortDirection;

            status = $('input[name="ProjectFilterForm[status]"]:checked').map(function () {
                return parseInt(this.value);
            });

            realStatus = 0;

            for (i = 0; i < status.length; i++)
                realStatus += status[i];

            sortBy        = parseInt($('select[name="ProjectFilterForm[sortBy]"]').val());
            sortDirection = parseInt($('select[name="ProjectFilterForm[sortDirection]"]').val());

            $.cookie('project_filter_status', realStatus, { path : '/' });
            $.cookie('project_filter_sort_by', sortBy, { path : '/' });
            $.cookie('project_filter_sort_direction', sortDirection, { path : '/' });

            location.reload();
        };
    };

    /**
     * Vulnerability list object.
     */
    this.vuln = new function () {
        var _vuln = this;

        /**
         * Project select form has been changed.
         */
        this.projectSelectFormChange = function (e) {
            var val;

            $('#client-list').removeClass('error');
            $('#project-list').removeClass('error');
            $('#project-list > div > .help-block').hide();
            $('#client-list > div > .help-block').hide();

            if (e.id == 'ProjectSelectForm_clientId')
            {
                val = $('#ProjectSelectForm_clientId').val();

                $('#project-list').hide();
                $('.form-actions > button[type="submit"]').prop('disabled', true);

                if (val != 0)
                {
                    _system.control.loadObjects(val, 'project-list', function (data) {
                        $('#ProjectSelectForm_clientId').prop('disabled', false);
                        $('#ProjectSelectForm_projectId > option:not(:first)').remove();

                        if (data && data.objects.length)
                        {
                            for (var i = 0; i < data.objects.length; i++)
                                $('<option>')
                                    .val(data.objects[i].id)
                                    .html(data.objects[i].name)
                                    .appendTo('#ProjectSelectForm_projectId');

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
            else if (e.id == 'ProjectSelectForm_projectId')
            {
                val = $('#ProjectSelectForm_projectId').val();

                $('.form-actions > button[type="submit"]').prop('disabled', true);

                if (val != 0)
                {
                    _system.control.loadObjects(val, 'target-list', function (data) {
                        if (data && data.objects.length)
                            $('.form-actions > button[type="submit"]').prop('disabled', false);
                        else
                        {
                            $('#project-list').addClass('error');
                            $('#project-list > div > .help-block').show();
                        }
                    });
                }
            }
        };
    };
}

var system = new System();

/**
 * Number zero padding.
 */
Number.prototype.zeroPad = function (size)
{
    var s = this + '';

    while (s.length < size)
        s = '0' + s;

    return s;
};
