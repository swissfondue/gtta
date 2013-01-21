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

        this.runningChecks   = [];
        this.updateIteration = 0;

        /**
         * Expand all checks.
         */
        this.expandAll = function () {
            $('div.control-body').slideDown('fast', undefined, function () {
                $('div.check-form').slideDown('slow');
            });
        };

        /**
         * Collapse all checks.
         */
        this.collapseAll = function () {
            $('div.check-form').slideUp('fast', undefined, function () {
                $('div.control-body').slideUp('slow');
            });
        };

        /**
         * Start all automated checks.
         */
        this.startAll = function () {
            $('div.check-header[data-type="automated"]').each(function () {
                var id = $(this).data('id');

                if (!_check.isRunning(id))
                    _check.start(id);
            });
        };

        /**
         * Update status of running checks.
         */
        this.update = function (url) {
            var i;

            if (this.runningChecks.length > 0)
                _check.updateIteration++;

            for (i = 0; i < _check.runningChecks.length; i++)
            {
                var check, headingRow, minutes, seconds, time;

                check      = _check.runningChecks[i];
                headingRow = $('div.check-header[data-id=' + check.id + ']');

                if (check.time > -1)
                {
                    check.time++;

                    minutes = 0;
                    seconds = check.time;
                }
                else
                {
                    minutes = 0;
                    seconds = 0;
                }

                if (seconds > 59)
                {
                    minutes = Math.floor(seconds / 60);
                    seconds = seconds - (minutes * 60);
                }

                $('td.status', headingRow).html(minutes.zeroPad(2) + ':' + seconds.zeroPad(2));
            }

            if (_check.updateIteration < 5)
                setTimeout(function () {
                    _check.update(url);
                }, 1000);
            else
            {
                var checkIds = [];

                _check.updateIteration = 0;

                for (i = 0; i < _check.runningChecks.length; i++)
                    checkIds.push(_check.runningChecks[i].id);

                data = [];
                data.push({ name : 'TargetCheckUpdateForm[checks]', value : checkIds.join(',') })
                data.push({ name : 'YII_CSRF_TOKEN',                value : system.csrf });

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

                        data = data.data;

                        if (data.checks)
                        {
                            for (i = 0; i < data.checks.length; i++)
                            {
                                var check, checkIdx;

                                check = data.checks[i];

                                $('#TargetCheckEditForm_' + check.id + '_result').val(check.result);

                                for (var k = 0; k < _check.runningChecks.length; k++)
                                {
                                    var innerCheck = _check.runningChecks[k];

                                    if (innerCheck.id == check.id)
                                    {
                                        checkIdx        = k;
                                        innerCheck.time = check.time;

                                        break;
                                    }
                                }

                                if (check.finished)
                                {
                                    _check.runningChecks.splice(checkIdx, 1);

                                    var headerRow = $('div.check-header[data-id=' + check.id + ']');

                                    headerRow.removeClass('in-progress');
                                    $('td.status', headerRow).html('&nbsp;');

                                    _check.setLoaded(check.id);

                                    $('td.actions', headerRow).html('');
                                    $('td.actions', headerRow).append(
                                        '<a href="#start" title="' + system.translate('Start') + '" onclick="user.check.start(' + check.id +
                                        ');"><i class="icon icon-play"></i></a> &nbsp; ' +
                                        '<a href="#reset" title="' + system.translate('Reset') + '" onclick="user.check.reset(' + check.id +
                                        ');"><i class="icon icon-refresh"></i></a>'
                                    );
                                }
                            }
                        }

                        setTimeout(function () {
                            _check.update(url);
                        }, 1000);
                    },

                    error : function(jqXHR, textStatus, e) {
                        $('.loader-image').hide();
                        system.showMessage('error', system.translate('Request failed, please try again.'));
                    },

                    beforeSend : function (jqXHR, settings) {
                        $('.loader-image').show();
                    }
                });

            }
        };

        /**
         * Check if check is running.
         */
        this.isRunning = function (id) {
            for (var i = 0; i < _check.runningChecks.length; i++)
                if (_check.runningChecks[i].id == id)
                    return true;

            return false;
        };

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
            if ($('div.control-body[data-id=' + id + ']').is(':visible'))
                _check.collapseControl(id);
            else
                _check.expandControl(id);
        };

        /**
         * Expand solution.
         */
        this.expandSolution = function (id) {
            $('span.solution-control[data-id=' + id + ']').html('<a href="#solution" onclick="user.check.collapseSolution(' + id + ');"><i class="icon-chevron-up"></i></a>');
            $('div.solution-content[data-id=' + id + ']').slideDown('slow');
        };

        /**
         * Collapse solution.
         */
        this.collapseSolution = function (id) {
            $('span.solution-control[data-id=' + id + ']').html('<a href="#solution" onclick="user.check.expandSolution(' + id + ');"><i class="icon-chevron-down"></i></a>');
            $('div.solution-content[data-id=' + id + ']').slideUp('slow');
        };

        /**
         * Expand result.
         */
        this.expandResult = function (id) {
            $('span.result-control[data-id=' + id + ']').html('<a href="#result" onclick="user.check.collapseResult(' + id + ');"><i class="icon-chevron-up"></i></a>');
            $('div.result-content[data-id=' + id + ']').slideDown('slow');
        };

        /**
         * Collapse result.
         */
        this.collapseResult = function (id) {
            $('span.result-control[data-id=' + id + ']').html('<a href="#result" onclick="user.check.expandResult(' + id + ');"><i class="icon-chevron-down"></i></a>');
            $('div.result-content[data-id=' + id + ']').slideUp('slow');
        };

        /**
         * Insert predefined result.
         */
        this.insertResult = function (id, result) {
            if (_check.isRunning(id))
                return;

            var textarea = $('#TargetCheckEditForm_' + id + '_result');

            result = result.replace(/\n<br>/g, '\n');
            result = result.replace(/<br>/g, '\n');
            result = result.replace(/&amp;/g, '&').replace(/&lt;/g, '<').replace(/&gt;/g, '>');
            textarea.val(result + '\n' + textarea.val());
        };

        /**
         * Set loading.
         */
        this.setLoading = function (id) {
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
        this.setLoaded = function (id) {
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
        this.getData = function (id) {
            var i, row, textareas, texts, checkboxes, selects, override, protocol, port, result, solutions, rating, data;

            row = $('div.check-form[data-id="' + id + '"]');

            texts = $('input[type="text"][name^="TargetCheckEditForm_' + id + '[inputs]"]', row).map(
                function () {
                    return {
                        name  : $(this).attr('name'),
                        value : $(this).val()
                    }
                }
            ).get();

            textareas = $('textarea[name^="TargetCheckEditForm_' + id + '[inputs]"]', row).map(
                function () {
                    return {
                        name  : $(this).attr('name'),
                        value : $(this).val()
                    }
                }
            ).get();

            checkboxes = $('input[type="checkbox"][name^="TargetCheckEditForm_' + id + '[inputs]"]', row).map(
                function () {
                    return {
                        name  : $(this).attr('name'),
                        value : $(this).is(':checked') ? $(this).val() : '0'
                    }
                }
            ).get();

            selects = $('select[name^="TargetCheckEditForm_' + id + '[inputs]"]', row).map(
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

            for (i = 0; i < texts.length; i++)
                data.push(texts[i]);

            for (i = 0; i < textareas.length; i++)
                data.push(textareas[i]);

            for (i = 0; i < checkboxes.length; i++)
                data.push(checkboxes[i]);

            for (i = 0; i < selects.length; i++)
                data.push(selects[i]);

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

                    if (data.rating != undefined && data.rating != null)
                        $('td.status', headerRow).html(
                            '<span class="label ' +
                            (ratings[data.rating].classN ? ratings[data.rating].classN : '') + '">' +
                            ratings[data.rating].text + '</span>'
                        );
                    else
                        $('td.status', headerRow).html('');

                    $('i.icon-refresh', headerRow).parent().remove();
                    $('td.actions', headerRow).append(
                        '<a href="#reset" title="' + system.translate('Reset') + '" onclick="user.check.reset(' + id +
                        ');"><i class="icon icon-refresh"></i></a>'
                    );

                    if (goToNext)
                    {
                        _check.collapse(id, function () {
                            nextRow = $('div.check-form[data-id="' + id + '"] + div + div.check-form');

                            if (!nextRow.length)
                                nextRow = $('div.check-form[data-id="' + id + '"]').parent().next().next().find('div.check-form:first');

                            if (nextRow.length)
                            {
                                _check.expand(nextRow.data('id'), function () {
                                    location.href = '#check-' + nextRow.data('id');
                                });
                            }
                        });
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
        this.initTargetCheckAttachmentUploadForms = function () {
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
                        $('td.status',  headerRow).html('00:00');
                        $('td.actions', headerRow).html('');
                        $('td.actions', headerRow).append(
                            '<a href="#stop" title="' + system.translate('Stop') + '" onclick="user.check.stop(' + id +
                            ');"><i class="icon icon-stop"></i></a> &nbsp; ' +
                            '<span class="disabled"><i class="icon icon-refresh" title="' +
                            system.translate('Reset') + '"></i></span>'
                        );

                        $('#TargetCheckEditForm_' + id + '_result').val('');

                        _check.setLoading(id);
                        $('.loader-image').hide();

                        _check.runningChecks.push({
                            id     : id,
                            time   : -1,
                            result : ''
                        });

                        headerRow.addClass('in-progress');
                    }
                    else if (operation == 'reset')
                    {
                        $('td.actions', headerRow).html('');
                        $('td.status', headerRow).html('&nbsp;');

                        if (data.automated)
                            $('td.actions', headerRow).append(
                                '<a href="#start" title="' + system.translate('Start') + '" onclick="user.check.start(' + id +
                                ');"><i class="icon icon-play"></i></a> &nbsp; '
                            );

                        $('td.actions', headerRow).append(
                            '<span class="disabled"><i class="icon icon-refresh" title="' +
                            system.translate('Reset') + '"></i></span>'
                        );

                        $('input[type="text"]', row).val('');
                        $('input[type="radio"]', row).prop('checked', false);
                        $('input[type="checkbox"]', row).prop('checked', false);
                        $('textarea', row).val('');
                        $('table.attachment-list', row).remove();

                        // port & protocol values
                        if (data.protocol != null && data.protocol != undefined)
                            $('#TargetCheckEditForm_' + id + '_protocol').val(data.protocol);

                        if (data.port != null && data.port != undefined)
                            $('#TargetCheckEditForm_' + id + '_port').val(data.port);

                        // input values
                        for (var i = 0; i < data.inputs.length; i++)
                        {
                            var input = data.inputs[i];
                            $('#' + input.id).val(input.value);
                        }
                    }
                    else if (operation == 'stop')
                    {
                        $('td.actions', headerRow).html('');
                        $('td.actions', headerRow).append(
                            '<span class="disabled"><i class="icon icon-stop" title="' +
                            system.translate('Stop') + '"></i></span> &nbsp; ' +
                            '<span class="disabled"><i class="icon icon-refresh" title="' +
                            system.translate('Reset') + '"></i></span>'
                        );

                        _check.setLoading(id);
                        $('.loader-image').hide();
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
         * Stop check.
         */
        this.stop = function (id) {
            _check._control(id, 'stop');
        };

        /**
         * Reset check.
         */
        this.reset = function (id) {
            if (confirm(system.translate('Are you sure that you want to reset this check?')))
                _check._control(id, 'reset');
        };
    };
}

var user = new User();
