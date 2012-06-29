/**
 * User namespace.
 */
function User()
{
    /**
     * Check object.
     */
    this.check = new function () {
        var _check = this;

        this.runningChecks = [];

        /**
         * Check if check is running.
         */
        this.isRunning = function (id)
        {
            for (var i = 0; i < _check.runningChecks.length; i++)
                if (_check.runningChecks[i].id == id)
                    return true;

            return false;
        };

        /**
         * Expand.
         */
        this.expand = function (id)
        {
            $('div.check-form[data-id=' + id + ']').slideDown('slow');
        };

        /**
         * Shrink.
         */
        this.shrink = function (id)
        {
            $('div.check-form[data-id=' + id + ']').slideUp('slow');
        };

        /**
         * Toggle.
         */
        this.toggle = function (id)
        {
            if ($('div.check-form[data-id=' + id + ']').is(':visible'))
                _check.shrink(id);
            else
                _check.expand(id);
        };

        /**
         * Insert predefined result.
         */
        this.insertResult = function (id, result)
        {
            if (_check.isRunning(id))
                return;

            var textarea = $('#TargetCheckEditForm_' + id + '_result');
            textarea.val(textarea.val() + result + '\n');
        };

        /**
         * Set loading.
         */
        this.setLoading = function (id)
        {
            row = $('div.check-form[data-id="' + id + '"]');

            $('.loader-image').show();
            $('input[type="text"]', row).prop('readonly', true);
            $('input[type="radio"]', row).prop('disabled', true);
            $('input[type="checkbox"]', row).prop('disabled', true);
            $('textarea', row).prop('readonly', true);
            $('button', row).prop('disabled', true);
        };

        /**
         * Set loaded.
         */
        this.setLoaded = function (id)
        {
            row = $('div.check-form[data-id="' + id + '"]');

            $('.loader-image').hide();
            $('input[type="text"]', row).prop('readonly', false);
            $('input[type="radio"]', row).prop('disabled', false);
            $('input[type="checkbox"]', row).prop('disabled', false);
            $('textarea', row).prop('readonly', false);
            $('button', row).prop('disabled', false);
        };

        /**
         * Get check data in array.
         */
        this.getData = function (id)
        {
            var row, inputs, override, protocol, port, result, solutions, rating, data;

            row = $('div.check-form[data-id="' + id + '"]');

            inputs = $('textarea[name^="TargetCheckEditForm_' + id + '[inputs]"]', row).map(
                function () {
                    return {
                        name  : $(this).attr('name'),
                        value : $(this).val()
                    }
                }
            ).get();

            override = $('input[name="TargetCheckEditForm_' + id + '[overrideTarget]"]', row).val();
            protocol = $('input[name="TargetCheckEditForm_' + id + '[protocol]"]', row).val();
            port     = $('input[name="TargetCheckEditForm_' + id + '[port]"]', row).val();
            result   = $('textarea[name="TargetCheckEditForm_' + id + '[result]"]', row).val();

            solutions = $('input[name^="TargetCheckEditForm_' + id + '[solutions]"]:checked', row).map(
                function () {
                    return {
                        name  : $(this).attr('name'),
                        value : $(this).val()
                    }
                }
            ).get();

            rating = $('input[name="TargetCheckEditForm_' + id + '[rating]"]:checked', row).val();

            if (override == undefined)
                override = '';

            if (protocol == undefined)
                protocol = '';

            if (port == undefined)
                port = '';

            if (result == undefined)
                result = '';

            if (rating == undefined)
                rating = '';

            data = [];

            data.push({ name : 'TargetCheckEditForm_' + id + '[overrideTarget]', value : override });
            data.push({ name : 'TargetCheckEditForm_' + id + '[protocol]',       value : protocol });
            data.push({ name : 'TargetCheckEditForm_' + id + '[port]',           value : port     });
            data.push({ name : 'TargetCheckEditForm_' + id + '[result]',         value : result   });
            data.push({ name : 'TargetCheckEditForm_' + id + '[rating]',         value : rating   });

            for (i = 0; i < inputs.length; i++)
                data.push(inputs[i]);

            for (i = 0; i < solutions.length; i++)
                data.push(solutions[i]);

            return data;
        };

        /**
         * Save the check.
         */
        this.save = function (id, goToNext) {
            var row, headerRow, data, url, nextRow, rating;

            headerRow = $('div.check-header[data-id="' + id + '"]');
            row       = $('div.check-form[data-id="' + id + '"]');
            url       = row.data('save-url');

            data = _check.getData(id);
            data.push({ name : 'YII_CSRF_TOKEN', value : system.csrf });

            $.ajax({
                dataType : 'json',
                url      : url,
                timeout  : system.ajaxTimeout,
                type     : 'POST',
                data     : data,

                success : function (data, textStatus) {
                    _check.setLoaded(id);

                    if (data.status == 'error')
                    {
                        system.showMessage('error', data.errorText);
                        return;
                    }

                    data = data.data;

                    if (data.rating != undefined && data.rating != 'undefined')
                        $('td.status', headerRow).html(
                            '<span class="label ' +
                            (ratings[data.rating].class ? ratings[data.rating].class : '') + '">' +
                            ratings[data.rating].text + '</span>'
                        );

                    $('i.icon-refresh', headerRow).parent().remove();
                    $('td.actions', headerRow).append(
                        '<a href="#reset" title="' + system.translate('Reset') + '" onclick="user.check.reset(' + id +
                        ');"><i class="icon icon-refresh"></i></a>'
                    );

                    if (goToNext)
                    {
                        _check.shrink(id);

                        nextRow = $('div.check-form[data-id="' + id + '"] + div + div');

                        if (nextRow)
                            _check.expand(nextRow.data('id'));
                    }
                },

                error : function(jqXHR, textStatus, e) {
                    _check.setLoaded(id);
                    system.showMessage('error', system.translate('Request failed, please try again.'));
                },

                beforeSend : function (jqXHR, settings) {
                    _check.setLoading(id);
                }
            });
        };

        /**
         * Set category as advanced.
         */
        this.setAdvanced = function (url, advanced) {
            data = {};

            data['YII_CSRF_TOKEN'] = system.csrf;
            data['TargetCheckCategoryEditForm[advanced]'] = advanced;

            $.ajax({
                dataType : 'json',
                url      : url,
                timeout  : system.ajaxTimeout,
                type     : 'POST',
                data     : data,

                success : function (data, textStatus) {
                    $('.loader-image').hide();

                    if (data.status == 'error')
                    {
                        system.showMessage('error', data.errorText);
                        return;
                    }

                    location.reload();
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
         * Initialize attachments form.
         */
        this.initTargetCheckAttachmentUploadForms = function ()
        {
            $('input[name^="TargetCheckAttachmentUploadForm"]').each(function () {
                var url  = $(this).data('upload-url'),
                    id   = $(this).data('id'),
                    data = {};

                data['YII_CSRF_TOKEN'] = system.csrf;

                $(this).fileupload({
                    dataType             : 'json',
                    url                  : url,
                    forceIframeTransport : true,
                    timeout              : 120000,
                    formData             : data,

                    done : function (e, data) {
                        $('.loader-image').hide();
                        $('#upload-message-' + id).hide();
                        $('#upload-link-' + id).show();

                        var json = data.result;

                        if (json.status == 'error')
                        {
                            system.showMessage('error', json.errorText);
                            return;
                        }

                        data = json.data;

                        var tr = '<tr data-path="' + data.path + '" data-control-url="' + data.controlUrl + '">' +
                                 '<td class="name"><a href="' + data.url + '">' + data.name + '</a></td>' +
                                 '<td class="actions"><a href="#del" title="' + system.translate('Delete') +
                                 '" onclick="user.check.delAttachment(\'' + data.path + '\');"><i class="icon icon-remove"></i></a></td></tr>';

                        if ($('div.check-form[data-id="' + id + '"] .attachment-list').length == 0)
                            $('div.check-form[data-id="' + id + '"] .upload-message').after('<table class="table attachment-list"><tbody></tbody></table>');

                        $('div.check-form[data-id="' + id + '"] .attachment-list > tbody').append(tr);
                    },

                    fail : function (e, data) {
                        $('.loader-image').hide();
                        $('#upload-message-' + id).hide();
                        $('#upload-link-' + id).show();
                        system.showMessage('error', system.translate('Request failed, please try again.'));
                    },

                    start : function (e) {
                        $('.loader-image').show();
                        $('#upload-link-' + id).hide();
                        $('#upload-message-' + id).show();
                    }
                });
            });
        };

        /**
         * Control attachment function.
         */
        this._controlAttachment = function(path, operation) {
            var url = $('tr[data-path=' + path + ']').data('control-url');

            $.ajax({
                dataType : 'json',
                url      : url,
                timeout  : system.ajaxTimeout,
                type     : 'POST',

                data : {
                    'TargetCheckAttachmentControlForm[operation]' : operation,
                    'TargetCheckAttachmentControlForm[path]'      : path,
                    'YII_CSRF_TOKEN'                              : system.csrf
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
                        $('tr[data-path=' + path + ']').fadeOut('slow', undefined, function () {
                            var table = $('tr[data-path=' + path + ']').parent().parent();

                            $('tr[data-path=' + path + ']').remove();

                            if ($('tbody > tr', table).length == 0)
                                table.remove();
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
         * Delete attachment.
         */
        this.delAttachment = function (path) {
            if (confirm(system.translate('Are you sure that you want to delete this object?')))
                _check._controlAttachment(path, 'delete');
        };

        /**
         * Control check function.
         */
        this._control = function(id, operation) {
            var row, headerRow, url;

            headerRow = $('div.check-header[data-id="' + id + '"]');
            row       = $('div.check-form[data-id="' + id + '"]');
            url       = headerRow.data('control-url');

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
                    _check.setLoaded(id);

                    if (data.status == 'error')
                    {
                        system.showMessage('error', data.errorText);
                        return;
                    }

                    data = data.data;

                    if (operation == 'start')
                    {
                        $('i.icon-play', headerRow).parent().remove();
                        $('i.icon-refresh', headerRow).parent().remove();

                        /*$('td.status', headerRow).html(
                            '<div class="progress"><div class="bar"></div></div>'
                        );*/

                        $('td.actions', headerRow).append(
                            '<span class="disabled"><i class="icon icon-play" title="' +
                            system.translate('Start') + '"></i></a> &nbsp; '
                        );

                        $('td.actions', headerRow).append(
                            '<span class="disabled"><i class="icon icon-refresh" title="' +
                            system.translate('Reset') + '"></i></a>'
                        );

                        _check.setLoading(id);
                        $('.loader-image').hide();

                        _check.runningChecks.push({
                            id     : id,
                            time   : 0,
                            result : ''
                        });

                        headerRow.addClass('in-progress');
                    }
                    else if (operation == 'reset')
                    {
                        $('i.icon-play', headerRow).parent().remove();
                        $('i.icon-refresh', headerRow).parent().remove();

                        $('td.status', headerRow).html('&nbsp;');

                        if (data.automated)
                            $('td.actions', headerRow).append(
                                '<a href="#start" title="' + system.translate('Start') + '" onclick="user.check.start(' + id +
                                ');"><i class="icon icon-play"></i></a> &nbsp; '
                            );

                        $('td.actions', headerRow).append(
                            '<span class="disabled"><i class="icon icon-refresh" title="' +
                            system.translate('Reset') + '"></i></a>'
                        );

                        $('input[type="text"]', row).val('');
                        $('input[type="radio"]', row).prop('checked', false);
                        $('input[type="checkbox"]', row).prop('checked', false);
                        $('textarea', row).val('');
                        $('table.attachment-list', row).remove();

                        // input values
                        for (var i = 0; i < data.inputs.length; i++)
                        {
                            var input = data.inputs[i];
                            $('#' + input.id).val(input.value);
                        }
                    }
                },

                error : function(jqXHR, textStatus, e) {
                    _check.setLoaded(id);
                    system.showMessage('error', system.translate('Request failed, please try again.'));
                },

                beforeSend : function (jqXHR, settings) {
                    _check.setLoading(id);
                }
            });
        };

        /**
         * Start check.
         */
        this.start = function (id) {
            var row, headerRow, data, url, nextRow, rating;

            headerRow = $('div.check-header[data-id="' + id + '"]');
            row       = $('div.check-form[data-id="' + id + '"]');
            url       = row.data('save-url');

            data = _check.getData(id);
            data.push({ name : 'YII_CSRF_TOKEN', value : system.csrf });

            _check.setLoading(id);

            $.ajax({
                dataType : 'json',
                url      : url,
                timeout  : system.ajaxTimeout,
                type     : 'POST',
                data     : data,

                success : function (data, textStatus) {
                    if (data.status == 'error')
                    {
                        _check.setLoaded(id);
                        system.showMessage('error', data.errorText);

                        return;
                    }

                    _check._control(id, 'start');
                },

                error : function(jqXHR, textStatus, e) {
                    _check.setLoaded(id);
                    system.showMessage('error', system.translate('Request failed, please try again.'));
                }
            });
        };

        /**
         * Reset check.
         */
        this.reset = function (id) {
            if (confirm(system.translate('Are you sure that you want to reset this check?')))
                _check._control(id, 'reset');
        };
    };

    /**
     * Project object.
     */
    this.project = new function () {
        var _project = this;

        /**
         * Report dialog.
         */
        this.reportDialog = new function () {
            var _reportDialog = this;

            /**
             * Show dialog.
             */
            this.show = function () {
                $('ul.report-target-list > li > label > input').prop('checked', true);
                $('#project-report > .modal-body > .alert').hide();
                $('#project-report').modal('show');
            };

            /**
             * Generate button handler.
             */
            this.generate = function () {
                var checked = $('ul.report-target-list > li > label > input:checked');

                if (checked && checked.length > 0)
                {
                    $('#project-report > .modal-body > .alert').hide();
                    $('#project-report').modal('hide');

                    $('#project-report > .modal-body > form').submit();
                }
                else
                    $('#project-report > .modal-body > .alert').show();
            };
        };

        /**
         * Comparison report dialog.
         */
        this.comparisonReportDialog = new function () {
            var _comparisonReportDialog = this;

            /**
             * Show dialog.
             */
            this.show = function () {
                $('#ProjectComparisonForm_projectId').val(0);
                $('#project-comparison-report > .modal-body > .alert').hide();
                $('#project-comparison-report').modal('show');
            };

            /**
             * Generate button handler.
             */
            this.generate = function () {
                var selected = $('#ProjectComparisonForm_projectId').val();

                if (selected != 0)
                {
                    $('#project-comparison-report > .modal-body > .alert').hide();
                    $('#project-comparison-report').modal('hide');

                    $('#project-comparison-report > .modal-body > form').submit();
                }
                else
                    $('#project-comparison-report > .modal-body > .alert').show();
            };
        };
    };
}

var user = new User();
