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
     * Report object.
     */
    this.report = new function () {
        var _report = this;

        this._riskMatrixTargets = [];
        this._effortList = [];

        /**
         * Disable inputs and select boxes.
         */
        this._setLoading = function () {
            $('#project-report-form input').prop('disabled', true);
            $('#project-report-form select').prop('disabled', true);
        };

        /**
         * Enable inputs and select boxes.
         */
        this._setLoaded = function () {
            $('#project-report-form input').prop('disabled', false);
            $('#project-report-form select').prop('disabled', false);
        };

        /**
         * Load a list of objects.
         */
        this._loadObjects = function (parentId, operation, callback) {
            var url = $('#project-report-form').data('object-list-url');

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
                    _report._setLoaded();

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
                    _report._setLoaded();
                    _system.showMessage('error', _system.translate('Request failed, please try again.'));
                    callback();
                },

                beforeSend : function (jqXHR, settings) {
                    $('.loader-image').show();
                    _report._setLoading();
                }
            });
        };

        /**
         * Project form has been changed.
         */
        this.projectFormChange = function (e) {
            var val;

            $('#client-list').removeClass('error');
            $('#project-list').removeClass('error');
            $('#project-list > div > .help-block').hide();
            $('#client-list > div > .help-block').hide();

            if (e.id == 'ProjectReportForm_clientId')
            {
                val = $('#ProjectReportForm_clientId').val();

                $('#project-list').hide();
                $('#target-list').hide();
                $('.form-actions > button[type="submit"]').prop('disabled', true);

                if (val != 0)
                {
                    _report._loadObjects(val, 'project-list', function (data) {
                        $('#ProjectReportForm_clientId').prop('disabled', false);
                        $('#ProjectReportForm_projectId > option:not(:first)').remove();

                        if (data && data.objects.length)
                        {
                            for (var i = 0; i < data.objects.length; i++)
                                $('<option>')
                                    .val(data.objects[i].id)
                                    .html(data.objects[i].name)
                                    .appendTo('#ProjectReportForm_projectId');

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
            else if (e.id == 'ProjectReportForm_projectId')
            {
                val = $('#ProjectReportForm_projectId').val();

                $('#target-list').hide();
                $('.form-actions > button[type="submit"]').prop('disabled', true);

                if (val != 0)
                {
                    _report._loadObjects(val, 'target-list', function (data) {
                        $('#target-list > .controls > .report-target-list > li').remove();

                        if (data && data.objects.length)
                        {
                            for (var i = 0; i < data.objects.length; i++)
                            {
                                var li    = $('<li>'),
                                    label = $('<label>'),
                                    input = $('<input>');

                                input
                                    .attr('type', 'checkbox')
                                    .prop('checked', true)
                                    .attr('name', 'ProjectReportForm[targetIds][]')
                                    .val(data.objects[i].id)
                                    .click(function () {
                                        user.report.projectFormChange(this);
                                    })
                                    .appendTo(label);

                                label
                                    .append(' ' + data.objects[i].host)
                                    .appendTo(li);

                                $('#target-list > .controls > .report-target-list').append(li);
                            }

                            $('#target-list').show();
                            $('.form-actions > button[type="submit"]').prop('disabled', false);
                        }
                        else
                        {
                            $('#project-list').addClass('error');
                            $('#project-list > div > .help-block').show();
                        }
                    });
                }
            }
            else
            {
                if ($('.report-target-list input:checked').length == 0)
                    $('.form-actions > button[type="submit"]').prop('disabled', true);
                else
                    $('.form-actions > button[type="submit"]').prop('disabled', false);
            }
        };

        /**
         * Comparison form has been changed.
         */
        this.comparisonFormChange = function (e) {
            var val, val1, val2;

            $('#client-list').removeClass('error');
            $('#client-list > div > .help-block').hide();

            if (e.id == 'ProjectComparisonForm_clientId')
            {
                val = $('#ProjectComparisonForm_clientId').val();

                $('#project-list-1').hide();
                $('#project-list-2').hide();
                $('.form-actions > button[type="submit"]').prop('disabled', true);

                if (val != 0)
                {
                    _report._loadObjects(val, 'project-list', function (data) {
                        $('#ProjectComparisonForm_projectId1 > option:not(:first)').remove();
                        $('#ProjectComparisonForm_projectId2 > option:not(:first)').remove();

                        if (data && data.objects.length)
                        {
                            for (var i = 0; i < data.objects.length; i++)
                            {
                                $('<option>')
                                    .val(data.objects[i].id)
                                    .html(data.objects[i].name)
                                    .appendTo('#ProjectComparisonForm_projectId1');

                                $('<option>')
                                    .val(data.objects[i].id)
                                    .html(data.objects[i].name)
                                    .appendTo('#ProjectComparisonForm_projectId2');
                            }

                            $('#project-list-1').show();
                            $('#project-list-2').show();
                        }
                        else
                        {
                            $('#client-list').addClass('error');
                            $('#client-list > div > .help-block').show();
                        }
                    });
                }
            }
            else if (e.id == 'ProjectComparisonForm_projectId1' || e.id == 'ProjectComparisonForm_projectId2')
            {
                val1 = $('#ProjectComparisonForm_projectId1').val();
                val2 = $('#ProjectComparisonForm_projectId2').val();

                $('.form-actions > button[type="submit"]').prop('disabled', true);

                if (val1 != 0 && val2 != 0 && val1 != val2)
                    $('.form-actions > button[type="submit"]').prop('disabled', false);
            }
        };

        /**
         * Degree of Fulfillment form has been changed.
         */
        this.fulfillmentFormChange = function (e) {
            var val;

            $('#client-list').removeClass('error');
            $('#project-list').removeClass('error');
            $('#project-list > div > .help-block').hide();
            $('#client-list > div > .help-block').hide();

            if (e.id == 'FulfillmentDegreeForm_clientId')
            {
                val = $('#FulfillmentDegreeForm_clientId').val();

                $('#project-list').hide();
                $('#target-list').hide();
                $('.form-actions > button[type="submit"]').prop('disabled', true);

                if (val != 0)
                {
                    _report._loadObjects(val, 'project-list', function (data) {
                        $('#FulfillmentDegreeForm_projectId > option:not(:first)').remove();

                        if (data && data.objects.length)
                        {
                            for (var i = 0; i < data.objects.length; i++)
                                $('<option>')
                                    .val(data.objects[i].id)
                                    .html(data.objects[i].name)
                                    .appendTo('#FulfillmentDegreeForm_projectId');

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
            else if (e.id == 'FulfillmentDegreeForm_projectId')
            {
                val = $('#FulfillmentDegreeForm_projectId').val();

                $('#target-list').hide();
                $('.form-actions > button[type="submit"]').prop('disabled', true);

                if (val != 0)
                {
                    _report._loadObjects(val, 'target-list', function (data) {
                        $('#target-list > .controls > .report-target-list > li').remove();

                        if (data && data.objects.length)
                        {
                            for (var i = 0; i < data.objects.length; i++)
                            {
                                var li    = $('<li>'),
                                    label = $('<label>'),
                                    input = $('<input>');

                                input
                                    .attr('type', 'checkbox')
                                    .prop('checked', true)
                                    .attr('name', 'FulfillmentDegreeForm[targetIds][]')
                                    .val(data.objects[i].id)
                                    .click(function () {
                                        user.report.fulfillmentFormChange(this);
                                    })
                                    .appendTo(label);

                                label
                                    .append(' ' + data.objects[i].host)
                                    .appendTo(li);

                                $('#target-list > .controls > .report-target-list').append(li);
                            }

                            $('#target-list').show();
                            $('.form-actions > button[type="submit"]').prop('disabled', false);
                        }
                        else
                        {
                            $('#project-list').addClass('error');
                            $('#project-list > div > .help-block').show();
                        }
                    });
                }
            }
            else
            {
                if ($('.report-target-list input:checked').length == 0)
                    $('.form-actions > button[type="submit"]').prop('disabled', true);
                else
                    $('.form-actions > button[type="submit"]').prop('disabled', false);
            }
        };

        /**
         * Refresh check list.
         */
        this._refreshChecks = function () {
            var targets, delTargets, addTargets, i, k, id;

            $('.form-actions > button[type="submit"]').prop('disabled', true);

            targets = $('.report-target-list input:checked').map(function () {
                return parseInt($(this).val());
            }).get();

            addTargets = [];
            delTargets = [];

            for (i = 0; i < targets.length; i++)
                if ($.inArray(targets[i], _report._riskMatrixTargets) == -1)
                    addTargets.push(targets[i]);

            for (i = 0; i < _report._riskMatrixTargets.length; i++)
                if ($.inArray(_report._riskMatrixTargets[i], targets) == -1)
                    delTargets.push(_report._riskMatrixTargets[i]);

            for (i = 0; i < delTargets.length; i++)
            {
                for (k = 0; k < _report._riskMatrixTargets.length; k++)
                    if (_report._riskMatrixTargets[k] == delTargets[i])
                    {
                        _report._riskMatrixTargets.splice(k, 1);
                        break;
                    }

                id = delTargets[i];

                $('.report-target-content[data-id=' + id + ']').slideUp('slow', undefined, function () {
                    $('.report-target-header[data-id=' + id + ']').slideUp('fast', undefined, function () {
                        $('.report-target-header[data-id=' + id + ']').remove();
                        $('.report-target-content[data-id=' + id + ']').remove();

                        if (_report._riskMatrixTargets.length == 0)
                            $('#check-list').slideUp('fast');
                    });
                });
            }

            for (i = 0; i < addTargets.length; i++)
            {
                _report._loadObjects(addTargets[i], 'check-list', function (data) {
                    var targetHeader, targetDiv, category, categoryDiv, check, checkDiv, i, k, j, rating, risk, field,
                        checked, damage, likelihood;

                    if (data && data.objects.length)
                    {
                        targetHeader = $('<div>')
                            .attr('data-id', data.target.id)
                            .addClass('report-target-header')
                            .addClass('hide')
                            .html('<a href="#toggle" onclick="user.report.riskMatrixTargetToggle(' + data.target.id + ');">' + data.target.host + '</a>');

                        targetDiv = $('<div>')
                            .attr('data-id', data.target.id)
                            .addClass('report-target-content')
                            .addClass('hide');

                        for (k = 0; k < data.objects.length; k++)
                        {
                            check = data.objects[k];

                            if (check.rating == _system.RATING_HIGH_RISK)
                                rating = '<span class="label label-high-risk">' + check.ratingName + '</span>';
                            else
                                rating = '<span class="label label-med-risk">' + check.ratingName + '</span>';

                            $('<div>')
                                .attr('data-id', check.id)
                                .addClass('report-check-header')
                                .html(
                                    '<table class="report-check"><tbody><tr><td class="name">' +
                                    '<a href="#toggle" onclick="user.report.riskMatrixCheckToggle(' + check.id +
                                    ');">' + check.name + '</a></td>' +
                                    '<td class="status">' + rating +  '</td></tr></tbody></table>'
                                )
                                .appendTo(targetDiv);

                            checkDiv = $('<div>')
                                .attr('data-id', check.id)
                                .addClass('report-check-content');

                            for (j = 0; j < _report._riskMatrixCategories.length; j++)
                            {
                                damage     = 1;
                                likelihood = 1;

                                risk = _report._riskMatrixCategories[j];

                                if (check.id in risk.checks)
                                {
                                    damage     = risk.checks[check.id].damage;
                                    likelihood = risk.checks[check.id].likelihood;
                                }

                                $('<div>')
                                    .addClass('report-risk-category-name')
                                    .html(risk.name + ' (R' + (j + 1).toString() + ')')
                                    .appendTo(checkDiv);

                                $('<div>')
                                    .addClass('control-group')
                                    .html(
                                        '<label class="control-label">' + _system.translate('Damage') + '</label>' +
                                        '<div class="controls">' +
                                        '<input type="radio" name="RiskMatrixForm[matrix][' + data.target.id + '][' + check.id + '][' + risk.id + '][damage]" value="1"' + ( damage == 1 ? ' checked' : '' ) + '>&nbsp;&nbsp;' +
                                        '<input type="radio" name="RiskMatrixForm[matrix][' + data.target.id + '][' + check.id + '][' + risk.id + '][damage]" value="2"' + ( damage == 2 ? ' checked' : '' ) + '>&nbsp;&nbsp;' +
                                        '<input type="radio" name="RiskMatrixForm[matrix][' + data.target.id + '][' + check.id + '][' + risk.id + '][damage]" value="3"' + ( damage == 3 ? ' checked' : '' ) + '>&nbsp;&nbsp;' +
                                        '<input type="radio" name="RiskMatrixForm[matrix][' + data.target.id + '][' + check.id + '][' + risk.id + '][damage]" value="4"' + ( damage == 4 ? ' checked' : '' ) + '>&nbsp;&nbsp;' +
                                        '</div>'
                                    )
                                    .appendTo(checkDiv);

                                $('<div>')
                                    .addClass('control-group')
                                    .html(
                                        '<label class="control-label">' + _system.translate('Likelihood') + '</label>' +
                                        '<div class="controls">' +
                                        '<input type="radio" name="RiskMatrixForm[matrix][' + data.target.id + '][' + check.id + '][' + risk.id + '][likelihood]" value="1"' + ( likelihood == 1 ? ' checked' : '' ) + '>&nbsp;&nbsp;' +
                                        '<input type="radio" name="RiskMatrixForm[matrix][' + data.target.id + '][' + check.id + '][' + risk.id + '][likelihood]" value="2"' + ( likelihood == 2 ? ' checked' : '' ) + '>&nbsp;&nbsp;' +
                                        '<input type="radio" name="RiskMatrixForm[matrix][' + data.target.id + '][' + check.id + '][' + risk.id + '][likelihood]" value="3"' + ( likelihood == 3 ? ' checked' : '' ) + '>&nbsp;&nbsp;' +
                                        '<input type="radio" name="RiskMatrixForm[matrix][' + data.target.id + '][' + check.id + '][' + risk.id + '][likelihood]" value="4"' + ( likelihood == 4 ? ' checked' : '' ) + '>&nbsp;&nbsp;' +
                                        '</div>'
                                    )
                                    .appendTo(checkDiv);
                            }

                            checkDiv.appendTo(targetDiv);
                        }

                        $('#check-list .span8')
                            .append(targetHeader)
                            .append(targetDiv);

                        _report._riskMatrixTargets.push(data.target.id);

                        $('.report-target-header[data-id=' + data.target.id + ']').slideDown('fast', undefined, function () {
                            $('.report-target-content[data-id=' + data.target.id + ']').slideDown('slow');
                        });

                        if (!$('#check-list').is(':visible'))
                            $('#check-list').slideDown('fast');
                    }

                    if (_report._riskMatrixTargets.length > 0)
                        $('.form-actions > button[type="submit"]').prop('disabled', false);
                });
            }

            if (addTargets.length == 0)
                $('.form-actions > button[type="submit"]').prop('disabled', false);
        };

        /**
         * Risk Matrix form has been changed.
         */
        this.riskMatrixFormChange = function (e) {
            var val;

            $('.form-actions > button[type="submit"]').prop('disabled', true);

            $('#client-list').removeClass('error');
            $('#project-list').removeClass('error');
            $('#template-list').removeClass('error');
            $('#client-list > div > .help-block').hide();
            $('#project-list > div > .help-block').hide();
            $('#template-list > div > .help-block').hide();

            if (e.id == 'RiskMatrixForm_templateId')
            {
                val = $('#RiskMatrixForm_templateId').val();

                $('#client-list').hide();
                $('#project-list').hide();
                $('#target-list').hide();
                $('#check-list').hide();

                // reset clients
                $('#RiskMatrixForm_clientId').val(0);

                // remove projects
                $('#RiskMatrixForm_projectId > option:not(:first)').remove();

                // remove targets
                $('#target-list > .controls > .report-target-list > li').remove();
                _report._riskMatrixTargets = [];

                // remove checklist content
                $('.report-target-header').remove();
                $('.report-target-content').remove();

                if (val != 0)
                {
                    _report._loadObjects(val, 'category-list', function (data) {
                        if (data && data.objects.length)
                        {
                            _report._riskMatrixCategories = data.objects;
                            $('#client-list').show();
                        }
                        else
                        {
                            $('#template-list').addClass('error');
                            $('#template-list > div > .help-block').show();
                        }
                    });
                }

            }
            else if (e.id == 'RiskMatrixForm_clientId')
            {
                val = $('#RiskMatrixForm_clientId').val();

                $('#project-list').hide();
                $('#target-list').hide();
                $('#check-list').hide();

                // remove projects
                $('#RiskMatrixForm_projectId > option:not(:first)').remove();

                // remove targets
                $('#target-list > .controls > .report-target-list > li').remove();
                _report._riskMatrixTargets = [];

                // remove checklist content
                $('.report-target-header').remove();
                $('.report-target-content').remove();

                if (val != 0)
                {
                    _report._loadObjects(val, 'project-list', function (data) {
                        if (data && data.objects.length)
                        {
                            for (var i = 0; i < data.objects.length; i++)
                                $('<option>')
                                    .val(data.objects[i].id)
                                    .html(data.objects[i].name)
                                    .appendTo('#RiskMatrixForm_projectId');

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
            else if (e.id == 'RiskMatrixForm_projectId')
            {
                val = $('#RiskMatrixForm_projectId').val();

                $('#target-list').hide();
                $('#check-list').hide();

                // remove targets
                $('#target-list > .controls > .report-target-list > li').remove();
                _report._riskMatrixTargets = [];

                // remove checklist content
                $('.report-target-header').remove();
                $('.report-target-content').remove();

                if (val != 0)
                {
                    _report._loadObjects(val, 'target-list', function (data) {
                        if (data && data.objects.length)
                        {
                            for (var i = 0; i < data.objects.length; i++)
                            {
                                var li    = $('<li>'),
                                    label = $('<label>'),
                                    input = $('<input>');

                                input
                                    .attr('type', 'checkbox')
                                    .prop('checked', true)
                                    .attr('id', 'RiskMatrixForm_targetIds_' + data.objects[i].id)
                                    .attr('name', 'RiskMatrixForm[targetIds][]')
                                    .val(data.objects[i].id)
                                    .click(function () {
                                        user.report.riskMatrixFormChange(this);
                                    })
                                    .appendTo(label);

                                label
                                    .append(' ' + data.objects[i].host)
                                    .appendTo(li);

                                $('#target-list > .controls > .report-target-list').append(li);
                            }

                            $('#target-list').show();
                            _report._refreshChecks();
                        }
                        else
                        {
                            $('#project-list').addClass('error');
                            $('#project-list > div > .help-block').show();
                        }
                    });
                }
            }
            else if (e.id.match(/^RiskMatrixForm_targetIds_/i))
            {
                _report._refreshChecks();
            }
        };

        /**
         * Toggle risk matrix check.
         */
        this.riskMatrixCheckToggle = function (id) {
            if ($('div.report-check-content[data-id=' + id + ']').is(':visible'))
                $('div.report-check-content[data-id=' + id + ']').slideUp('slow');
            else
                $('div.report-check-content[data-id=' + id + ']').slideDown('slow');
        };

        /**
         * Toggle risk matrix target.
         */
        this.riskMatrixTargetToggle = function (id) {
            if ($('div.report-target-content[data-id=' + id + ']').is(':visible'))
                $('div.report-target-content[data-id=' + id + ']').slideUp('slow');
            else
                $('div.report-target-content[data-id=' + id + ']').slideDown('slow');
        };

        /**
         * Vulnerabilities form has been changed.
         */
        this.vulnsFormChange = function (e) {
            var val;

            $('#client-list').removeClass('error');
            $('#project-list').removeClass('error');
            $('#project-list > div > .help-block').hide();
            $('#client-list > div > .help-block').hide();
            $('.form-actions > button[type="submit"]').prop('disabled', true);

            if (e.id == 'VulnsReportForm_clientId')
            {
                val = $('#VulnsReportForm_clientId').val();

                $('#project-list').hide();
                $('#target-list').hide();
                $('#report-details').hide();

                // clear columns
                $('input[name="VulnsReportForm[header]"]').prop('checked', true);
                $('input[name="VulnsReportForm[ratings][]"]').prop('checked', true);
                $('input[name="VulnsReportForm[columns][]"]').prop('checked', true);

                if (val != 0)
                {
                    _report._loadObjects(val, 'project-list', function (data) {
                        $('#VulnsReportForm_clientId').prop('disabled', false);
                        $('#VulnsReportForm_projectId > option:not(:first)').remove();

                        if (data && data.objects.length)
                        {
                            for (var i = 0; i < data.objects.length; i++)
                                $('<option>')
                                    .val(data.objects[i].id)
                                    .html(data.objects[i].name)
                                    .appendTo('#VulnsReportForm_projectId');

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
            else if (e.id == 'VulnsReportForm_projectId')
            {
                val = $('#VulnsReportForm_projectId').val();

                $('#target-list').hide();
                $('#report-details').hide();

                // clear columns
                $('input[name="VulnsReportForm[header]"]').prop('checked', true);
                $('input[name="VulnsReportForm[ratings][]"]').prop('checked', true);
                $('input[name="VulnsReportForm[columns][]"]').prop('checked', true);

                if (val != 0)
                {
                    _report._loadObjects(val, 'target-list', function (data) {
                        $('#target-list > .controls > .report-target-list > li').remove();

                        if (data && data.objects.length)
                        {
                            for (var i = 0; i < data.objects.length; i++)
                            {
                                var li    = $('<li>'),
                                    label = $('<label>'),
                                    input = $('<input>');

                                input
                                    .attr('type', 'checkbox')
                                    .prop('checked', true)
                                    .attr('id', 'VulnsReportForm_targetIds_' + data.objects[i].id)
                                    .attr('name', 'VulnsReportForm[targetIds][]')
                                    .val(data.objects[i].id)
                                    .click(function () {
                                        user.report.vulnsFormChange(this);
                                    })
                                    .appendTo(label);

                                label
                                    .append(' ' + data.objects[i].host)
                                    .appendTo(li);

                                $('#target-list > .controls > .report-target-list').append(li);
                            }

                            $('#target-list').show();
                            $('#report-details').show();
                            $('.form-actions > button[type="submit"]').prop('disabled', false);
                        }
                        else
                        {
                            $('#project-list').addClass('error');
                            $('#project-list > div > .help-block').show();
                        }
                    });
                }
            }
            else if (e.id.match(/^VulnsReportForm_targetIds_/i))
            {
                $('#report-details').hide();

                if ($('.report-target-list input:checked').length > 0)
                {
                    $('#report-details').show();

                    if ($('input[name="VulnsReportForm[ratings][]"]:checked').length > 0 &&
                        $('input[name="VulnsReportForm[columns][]"]:checked').length > 0)
                    {
                        $('.form-actions > button[type="submit"]').prop('disabled', false);
                        $('#report-details').show();
                    }
                }
                else
                {
                    // clear columns
                    $('input[name="VulnsReportForm[header]"]').prop('checked', true);
                    $('input[name="VulnsReportForm[ratings][]"]').prop('checked', true);
                    $('input[name="VulnsReportForm[columns][]"]').prop('checked', true);
                }
            }
            else
            {
                if ($('input[name="VulnsReportForm[ratings][]"]:checked').length > 0 &&
                    $('input[name="VulnsReportForm[columns][]"]:checked').length > 0)
                    $('.form-actions > button[type="submit"]').prop('disabled', false);
            }
        };
        
        /**
         * Show form.
         */
        this.effortForm = function () {
            $('#effort-modal').modal();
        };

        /**
         * Calculate effort.
         */
        this._calculateEffort = function () {
            var effort, checks, category, check, advanced, targets, references;

            category   = $('#EffortEstimateForm_categoryId').val();
            targets    = $('#EffortEstimateForm_targets').val();
            references = $('input[name="EffortEstimateForm[referenceIds][]"]:checked').map(
                function () {
                    return parseInt($(this).val());
                }
            ).get();

            advanced = $('#EffortEstimateForm_advanced').is(':checked');

            checks = 0;
            effort = 0;

            for (var i = 0; i < checkList.length; i++)
                if (checkList[i].id == category)
                {
                    category = checkList[i];

                    for (var c = 0; c < category.checks.length; c++)
                    {
                        check = category.checks[c];

                        if ($.inArray(check.reference, references) != -1)
                        {
                            if (!advanced && check.advanced)
                                continue;

                            effort += check.effort;
                            checks++;
                        }
                    }

                    break;
                }

            $('#checks').html(checks * targets);
            $('#estimated-effort').html(effort * targets);
            $('#EffortEstimateForm_effort').val(effort * targets);

            if (checks == 0)
                $('#add-button').prop('disabled', true);
        };

        /**
         * Form has been changed.
         */
        this.effortFormChange = function (e) {
            var category, targets, valid;

            valid = false;

            category = $('#EffortEstimateForm_categoryId').val();
            targets  = parseInt($('#EffortEstimateForm_targets').val());

            if (category > 0 && targets > 0)
                valid = true;

            if (valid)
            {
                $('#add-button').prop('disabled', false);
                _report._calculateEffort();
            }
            else
            {
                $('#add-button').prop('disabled', true);
                $('#checks').html('0');
                $('#estimated-effort').html('0');
                $('#EffortEstimateForm_effort').val(0);
            }
        };

        /**
         * Form submit.
         */
        this.effortFormSubmit = function () {
            $('#EffortEstimateForm').submit();
        };

        /**
         * Draw effort table.
         */
        this._drawEffortTable = function () {
            var item, tr, totalEffort, totalTargets;

            totalEffort  = 0;
            totalTargets = 0;

            $('table.effort-list > tbody').find('tr:gt(0)').remove();

            for (var i = 0; i < _report._effortList.length; i++)
            {
                item = _report._effortList[i];

                tr = '<tr data-id="' + item.id + '"><td class="name">' + item.name + '</td>' +
                    '<td class="targets">' + item.targets + '</td>' + '<td class="effort">' + item.effort + '</td>' +
                    '<td class="actions"><a href="#del" title="' + _system.translate('Delete') +
                    '" onclick="_system.report.delEffort(' + item.id + ')"><i class="icon icon-remove"></i></a></td</tr>'

                $('table.effort-list > tbody').append(tr);

                totalTargets += item.targets;
                totalEffort  += item.effort;
            }

            if (_report._effortList.length > 0)
            {
                tr = '<tr><td class="name">' + _system.translate('Total') + '</td><td class="targets">'  +
                    totalTargets + '</td><td class="effort" colspan="2">' + totalEffort + ' ' +
                    _system.translate('minutes') + '</td></tr>';

                $('table.effort-list > tbody').append(tr);

                if (!$('.effort-list-container').is(':visible'))
                    $('.effort-list-container').slideDown('slow');

                $('#print-button').show();
                $('#placeholder-text').hide();
            }
            else
            {
                $('.effort-list-container').slideUp('slow');
                $('#print-button').hide();
                $('#placeholder-text').show();
            }
        };

        /**
         * Add effort to the table.
         */
        this.addEffort = function () {
            $('#effort-modal').modal('hide');

            _report._effortList.push({
                id      : $('#EffortEstimateForm_categoryId').val(),
                name    : $('#EffortEstimateForm_categoryId option:selected').text(),
                targets : parseInt($('#EffortEstimateForm_targets').val()),
                effort  : parseInt($('#EffortEstimateForm_effort').val())
            });

            _report._drawEffortTable();

            $('#EffortEstimateForm_categoryId option:selected').prop('disabled', true);

            // refresh the form
            $('#EffortEstimateForm_categoryId').val(0);
            $('#EffortEstimateForm_advanced').prop('checked', true);
            $('input[name="EffortEstimateForm[referenceIds][]"]').prop('checked', true);
            $('#EffortEstimateForm_targets').val(1);

            $('#checks').html('0');
            $('#estimated-effort').html('0');
            $('#EffortEstimateForm_effort').val(0);

            $('#add-button').prop('disabled', true);
        };

        /**
         * Delete effort from the table.
         */
        this.delEffort = function (id) {
            for (var i = 0; i < _report._effortList.length; i++)
                if (_report._effortList[i].id == id)
                {
                    _report._effortList.splice(i, 1);
                    break;
                }

            $('#EffortEstimateForm_categoryId option[value=' + id + ']').prop('disabled', false);

            $('tr[data-id=' + id + ']').addClass('delete-row');
            $('tr[data-id=' + id + ']').fadeOut('slow', undefined, function () {
                _report._drawEffortTable();
            });
        };

        /**
         * Print effort.
         */
        this.printEffort = function () {
            var content, win;

            content = $('.effort-list-container > .row > .span8').html();
            content = content.replace(/<i class="icon icon-remove"><\/i>/g, '');

            win = window.open('', 'printWindow', 'location=0,status=0,width=620,height=500');

            win.document.writeln(
                '<!DOCTYPE html>' +
                '<html><head>' +
                '<title>' + _system.translate('Estimated Effort') + '</title>' +
                '<meta charset="utf-8">' +
                '<link rel="stylesheet" type="text/css" href="/css/bootstrap/bootstrap.css">' +
                '<link rel="stylesheet" type="text/css" href="/css/style.css">' +
                '</head>'
            );

            win.document.writeln('<body>' + content + '</body>');
            win.document.writeln('</html>');
            win.print();
            win.close();
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
