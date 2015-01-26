/**
 * System class.
 */
function System() {
    var _system = this;

    // attributes
    this.csrf = null;
    this.ajaxTimeout = 60000;
    this.messageTimeout = 5000;
    this.l10nMessages = {};
    this.constants = {};

    /**
     * Add alert in alerts queue
     * @param eventType
     * @param message
     */
    this.addAlert = function (eventType, message) {
        var $container = $('.message-container');
        var options = {
            timeout: this.messageTimeout
        };

        $('html, body').animate({ scrollTop : 0 }, 'fast');

        switch (eventType) {
            case "success":
                $container.addSuccessAlert(message, options);
                break;
            case "error":
                $container.addDangerAlert(message, options);
                break;
            case "warning":
                $container.addWarningAlert(message, options);
                break;
            case "info":
                $container.addInfoAlert(message, options);
                break;
            default:
                break;
        }
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

                    if (data.status == 'error') {
                        _system.addAlert('error', data.errorText);

                        if (operation == "delete") {
                            $("tr[data-id=" + id + "]").removeClass("delete-row");
                        }

                        return;
                    }

                    if (operation == 'delete') {
                        $('tr[data-id=' + id + ']').fadeOut('slow', undefined, function () {
                            $('tr[data-id=' + id + ']').remove();
                            _system.addAlert('success', _system.translate('Object deleted.'));

                            if ($('table.table > tbody > tr').length == 1)
                                location.reload();
                        });
                    } else if (operation == 'up' || operation == 'down') {
                        location.reload();
                    }
                },

                error : function(jqXHR, textStatus, e) {
                    $('.loader-image').hide();
                    _system.addAlert('error', _system.translate('Request failed, please try again.'));

                    if (operation == "delete") {
                        $("tr[data-id=" + id + "]").removeClass("delete-row");
                    }
                },

                beforeSend : function (jqXHR, settings) {
                    $('.loader-image').show();
                }
            });
        };

        /**
         * Move object up.
         */
        this.up = function (id) {
            _system_control._control(id, 'up');
        };

        /**
         * Move object down.
         */
        this.down = function (id) {
            _system_control._control(id, 'down');
        };

        /**
         * Delete object.
         */
        this.del = function (id, message) {
            $('tr[data-id=' + id + ']').addClass('delete-row');

            if (
                confirm(_system.translate('Are you sure that you want to delete this object?')) &&
                (message == undefined || (message != undefined && confirm(message + '\n\n' + _system.translate('PROCEED AT YOUR OWN RISK!'))))
            ) {
                _system_control._control(id, 'delete');
            } else {
                $('tr[data-id=' + id + ']').removeClass('delete-row');
            }
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
                        _system.addAlert('error', data.errorText);
                        callback();

                        return;
                    }

                    callback(data.data);
                },

                error : function(jqXHR, textStatus, e) {
                    $('.loader-image').hide();
                    _system_control._setLoaded();
                    _system.addAlert('error', _system.translate('Request failed, please try again.'));
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
            var i, data, statuses, sortBy, sortDirection;

            statuses = $('input[name="ProjectFilterForm[status]"]:checked').map(function () {
                return this.value;
            });

            data = [];

            for (i = 0; i < statuses.length; i++) {
                data.push(statuses[i]);
            }

            data = data.join(",");
            sortBy = parseInt($('select[name="ProjectFilterForm[sortBy]"]').val());
            sortDirection = parseInt($('select[name="ProjectFilterForm[sortDirection]"]').val());

            $.cookie("project_filter_status", data, {path : "/"});
            $.cookie("project_filter_sort_by", sortBy, {path : "/"});
            $.cookie("project_filter_sort_direction", sortDirection, {path : "/"});

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

            if (e.id == 'ProjectSelectForm_clientId') {
                val = $('#ProjectSelectForm_clientId').val();

                $('#project-list').hide();
                $('.form-actions > button[type="submit"]').prop('disabled', true);

                if (val != 0) {
                    _system.control.loadObjects(val, 'project-list', function (data) {
                        $('#ProjectSelectForm_clientId').prop('disabled', false);
                        $('#ProjectSelectForm_projectId > option:not(:first)').remove();

                        if (data && data.objects.length) {
                            for (var i = 0; i < data.objects.length; i++) {
                                $('<option>')
                                    .val(data.objects[i].id)
                                    .html(data.objects[i].name)
                                    .appendTo('#ProjectSelectForm_projectId');
                            }

                            $('#project-list').show();
                        } else {
                            $('#client-list').addClass('error');
                            $('#client-list > div > .help-block').show();
                        }
                    });
                }
            } else if (e.id == 'ProjectSelectForm_projectId') {
                val = $('#ProjectSelectForm_projectId').val();
                $('.form-actions > button[type="submit"]').prop('disabled', val == 0);
            }
        };
    };

    /**
     * Report object.
     */
    this.report = new function () {
        var _report = this;

        this._riskMatrixTargets = [];
        this._riskMatrixCategories = [];
        this._effortList = [];

        /**
         * Switch save button state for the project report form
         */
        this._projectFormSwitchButton = function () {
            var guided = parseInt($('#ProjectReportForm_projectId option:selected').attr("data-guided"));

            if ((!guided && $('.report-target-list input:checked').length == 0) ||
                $('#ProjectReportForm_templateId').val() == 0 ||
                ($("#ProjectReportForm_templateId option:selected").data("type") == 0 && $('#ProjectReportForm_options_matrix').is(':checked') && $('#RiskMatrixForm_templateId').val() == 0)
            ) {
                $('.form-actions > button[type="submit"]').prop('disabled', true);
            } else {
                $('.form-actions > button[type="submit"]').prop('disabled', false);
            }
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

            if (e.id == 'ProjectReportForm_clientId') {
                val = $('#ProjectReportForm_clientId').val();

                _report._riskMatrixTargets = [];

                $('.report-target-header').remove();
                $('.report-target-content').remove();
                $('#check-list').hide();
                $('#project-list').hide();
                $('#target-list').hide();
                $('.form-actions > button[type="submit"]').prop('disabled', true);

                if (val != 0) {
                    _system.control.loadObjects(val, 'project-list', function (data) {
                        $('#ProjectReportForm_clientId').prop('disabled', false);
                        $('#ProjectReportForm_projectId > option:not(:first)').remove();

                        if (data && data.objects.length) {
                            for (var i = 0; i < data.objects.length; i++) {
                                $('<option>')
                                    .val(data.objects[i].id)
                                    .attr("data-guided", data.objects[i].guided ? 1 : 0)
                                    .html(data.objects[i].name)
                                    .appendTo('#ProjectReportForm_projectId');
                            }

                            $('#project-list').show();
                        } else {
                            $('#client-list').addClass('error');
                            $('#client-list > div > .help-block').show();
                        }
                    });
                }
            } else if (e.id == 'ProjectReportForm_projectId') {
                val = $('#ProjectReportForm_projectId').val();

                _report._riskMatrixTargets = [];

                $('.report-target-header').remove();
                $('.report-target-content').remove();
                $('#check-list').hide();
                $('#target-list').hide();
                $('.form-actions > button[type="submit"]').prop('disabled', true);

                if (val != 0) {
                    var guided = parseInt($('#ProjectReportForm_projectId option:selected').attr("data-guided"));

                    if (guided) {
                        if ($('#ProjectReportForm_options_matrix').is(':checked') && $('#RiskMatrixForm_templateId').val() > 0) {
                            _report._refreshChecks(true, true);
                        }

                        _report._projectFormSwitchButton();
                    } else {
                        _system.control.loadObjects(val, 'target-list', function (data) {
                            $('#target-list > .controls > .report-target-list > li').remove();

                            if (data && data.objects.length) {
                                for (var i = 0; i < data.objects.length; i++) {
                                    var li = $('<li>'),
                                        label = $('<label>'),
                                        input = $('<input>');

                                    input
                                        .attr('type', 'checkbox')
                                        .prop('checked', true)
                                        .attr('name', 'ProjectReportForm[targetIds][]')
                                        .attr('id', 'ProjectReportForm_targetIds_' + data.objects[i].id)
                                        .val(data.objects[i].id)
                                        .click(function () {
                                            system.report.projectFormChange(this);
                                        })
                                        .appendTo(label);

                                    label
                                        .append(' ' + data.objects[i].host)
                                        .appendTo(li);

                                    $('#target-list > .controls > .report-target-list').append(li);
                                }

                                $('#target-list').show();

                                if ($('#ProjectReportForm_options_matrix').is(':checked') && $('#RiskMatrixForm_templateId').val() > 0) {
                                    _report._refreshChecks(true, false);
                                }

                                _report._projectFormSwitchButton();
                            } else {
                                $('#project-list').addClass('error');
                                $('#project-list > div > .help-block').show();
                            }
                        });
                    }
                }
            } else if (e.id.match(/^ProjectReportForm_targetIds_/i)) {
                if ($('#ProjectReportForm_options_matrix').is(':checked') && $('#RiskMatrixForm_templateId').val() > 0) {
                    _report._refreshChecks(true, false);
                }

                _report._projectFormSwitchButton();
            } else if (e.id == 'ProjectReportForm_templateId') {
                var type = $("#ProjectReportForm_templateId option:selected").data("type");

                if (type != undefined) {
                    if (type == 0) {
                        $(".rtf-report").slideDown("slow");
                    } else if (type == 1) {
                        $(".rtf-report").slideUp("slow");
                    }
                }

                _report._projectFormSwitchButton();
            } else if (e.id == 'ProjectReportForm_options_matrix') {
                _report._riskMatrixTargets = [];
                _report._riskMatrixCategories = [];

                if ($('#ProjectReportForm_options_matrix').is(':checked')) {
                    $('#risk-template-list').show();
                    $('.form-actions > button[type="submit"]').prop('disabled', true);
                } else {
                    $('#risk-template-list').hide();
                    $('#check-list').hide();
                    $('.report-target-header').remove();
                    $('.report-target-content').remove();
                    $('#RiskMatrixForm_templateId').val(0);
                    $('#risk-template-list').removeClass('error');
                    $('#risk-template-list > div > .help-block').hide();

                    _report._projectFormSwitchButton();
                }
            } else if (e.id == 'RiskMatrixForm_templateId') {
                var val = $('#RiskMatrixForm_templateId').val();

                $('#check-list').hide();

                _report._riskMatrixTargets = [];
                _report._riskMatrixCategories = [];

                $('#risk-template-list').removeClass('error');
                $('#risk-template-list > div > .help-block').hide();

                // remove checklist content
                $('.report-target-header').remove();
                $('.report-target-content').remove();

                if (val != 0) {
                    _system.control.loadObjects(val, 'category-list', function (data) {
                        if (data && data.objects.length) {
                            _report._riskMatrixCategories = data.objects;

                            if ($('#ProjectReportForm_options_matrix').is(':checked') && $('#RiskMatrixForm_templateId').val() > 0) {
                                var guided = parseInt($('#ProjectReportForm_projectId option:selected').attr("data-guided"));
                                _report._refreshChecks(true, guided);
                            }

                            _report._projectFormSwitchButton();
                        } else {
                            $('#risk-template-list').addClass('error');
                            $('#risk-template-list > div > .help-block').show();
                            $('.form-actions > button[type="submit"]').prop('disabled', true);
                        }
                    });
                } else {
                    $('.form-actions > button[type="submit"]').prop('disabled', true);
                }
            }
        };

        /**
         * Comparison form has been changed.
         */
        this.comparisonFormChange = function (e) {
            var val;

            $('#client-list').removeClass('error');
            $('#client-list > div > .help-block').hide();

            if (e.id == 'ProjectComparisonForm_clientId') {
                val = $('#ProjectComparisonForm_clientId').val();

                $('#project-list-1').hide();
                $('#project-list-2').hide();
                $('.form-actions > button[type="submit"]').prop('disabled', true);

                if (val != 0) {
                    _system.control.loadObjects(val, 'project-list', function (data) {
                        $('#ProjectComparisonForm_projectId1 > option:not(:first)').remove();
                        $('#ProjectComparisonForm_projectId2 > option:not(:first)').remove();

                        if (data && data.objects.length) {
                            for (var i = 0; i < data.objects.length; i++) {
                                $('<option>')
                                    .val(data.objects[i].id)
                                    .attr("data-guided", data.objects[i].guided ? 1 : 0)
                                    .html(data.objects[i].name)
                                    .appendTo('#ProjectComparisonForm_projectId1');

                                $('<option>')
                                    .val(data.objects[i].id)
                                    .attr("data-guided", data.objects[i].guided ? 1 : 0)
                                    .html(data.objects[i].name)
                                    .appendTo('#ProjectComparisonForm_projectId2');
                            }

                            $('#project-list-1').show();
                            $('#project-list-2').show();
                        } else {
                            $('#client-list').addClass('error');
                            $('#client-list > div > .help-block').show();
                        }
                    });
                }
            } else if (e.id == 'ProjectComparisonForm_projectId1' || e.id == 'ProjectComparisonForm_projectId2') {
                var project1 = $('#ProjectComparisonForm_projectId1'),
                    project2 = $('#ProjectComparisonForm_projectId2');

                $('.form-actions > button[type="submit"]').prop('disabled', true);

                if (project1.val() != 0 &&
                    project2.val() != 0 &&
                    project1.val() != project2.val() &&
                    project1.find("option:selected").attr("data-guided") == project2.find("option:selected").attr("data-guided")
                ) {
                    $('.form-actions > button[type="submit"]').prop('disabled', false);
                }
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

            if (e.id == 'FulfillmentDegreeForm_clientId') {
                val = $('#FulfillmentDegreeForm_clientId').val();

                $('#project-list').hide();
                $('#target-list').hide();
                $('.form-actions > button[type="submit"]').prop('disabled', true);

                if (val != 0) {
                    _system.control.loadObjects(val, 'project-list', function (data) {
                        $('#FulfillmentDegreeForm_projectId > option:not(:first)').remove();

                        if (data && data.objects.length) {
                            for (var i = 0; i < data.objects.length; i++) {
                                $('<option>')
                                    .val(data.objects[i].id)
                                    .attr("data-guided", data.objects[i].guided ? 1 : 0)
                                    .html(data.objects[i].name)
                                    .appendTo('#FulfillmentDegreeForm_projectId');
                            }

                            $('#project-list').show();
                        } else {
                            $('#client-list').addClass('error');
                            $('#client-list > div > .help-block').show();
                        }
                    });
                }
            } else if (e.id == 'FulfillmentDegreeForm_projectId') {
                val = $('#FulfillmentDegreeForm_projectId').val();

                $('#target-list').hide();
                $('.form-actions > button[type="submit"]').prop('disabled', true);

                if (val != 0) {
                    var guided = parseInt($('#FulfillmentDegreeForm_projectId option:selected').attr("data-guided"));

                    if (guided) {
                        $('.form-actions > button[type="submit"]').prop('disabled', false);
                    } else {
                        _system.control.loadObjects(val, 'target-list', function (data) {
                            $('#target-list > .controls > .report-target-list > li').remove();

                            if (data && data.objects.length) {
                                for (var i = 0; i < data.objects.length; i++) {
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
                            } else {
                                $('#project-list').addClass('error');
                                $('#project-list > div > .help-block').show();
                            }
                        });
                    }
                }
            } else {
                if ($('.report-target-list input:checked').length == 0) {
                    $('.form-actions > button[type="submit"]').prop('disabled', true);
                } else {
                    $('.form-actions > button[type="submit"]').prop('disabled', false);
                }
            }
        };

        /**
         * Refresh check list.
         */
        this._refreshChecks = function (projectReport, guidedTest) {
            var targets, delTargets, addTargets, i, k, id, target;

            $('.form-actions > button[type="submit"]').prop('disabled', true);

            targets = $('.report-target-list input:checked').map(function () {
                return parseInt($(this).val());
            }).get();

            addTargets = [];
            delTargets = [];

            for (i = 0; i < targets.length; i++) {
                if ($.inArray(targets[i], _report._riskMatrixTargets) == -1) {
                    addTargets.push(targets[i]);
                }
            }

            for (i = 0; i < _report._riskMatrixTargets.length; i++) {
                if ($.inArray(_report._riskMatrixTargets[i], targets) == -1) {
                    delTargets.push(_report._riskMatrixTargets[i]);
                }
            }

            for (i = 0; i < delTargets.length; i++) {
                for (k = 0; k < _report._riskMatrixTargets.length; k++) {
                    if (_report._riskMatrixTargets[k] == delTargets[i]) {
                        _report._riskMatrixTargets.splice(k, 1);
                        break;
                    }
                }

                id = delTargets[i];

                $('.report-target-content[data-id=' + id + ']').slideUp('slow', undefined, function () {
                    $('.report-target-header[data-id=' + id + ']').slideUp('fast', undefined, function () {
                        $('.report-target-header[data-id=' + id + ']').remove();
                        $('.report-target-content[data-id=' + id + ']').remove();

                        if (_report._riskMatrixTargets.length == 0) {
                            $('#check-list').slideUp('fast');
                        }
                    });
                });
            }

            if (guidedTest || addTargets.length > 0) {
                var param, cmd;

                if (guidedTest) {
                    if (projectReport) {
                        param = $('#ProjectReportForm_projectId').val();
                    } else {
                        param = $('#RiskMatrixForm_projectId').val();
                    }

                    cmd = 'gt-target-check-list';
                } else {
                    param = addTargets.join(',');
                    cmd = 'target-check-list';
                }

                _system.control.loadObjects(param, cmd, function (data) {
                    var targetHeader, targetDiv, category, categoryDiv, check, checkDiv, i, k, j, rating, risk, field,
                        checked, damage, likelihood;

                    if (data && data.objects.length) {
                        for (i = 0; i < data.objects.length; i++) {
                            target = data.objects[i];

                            targetHeader = $('<div>')
                                .attr('data-id', target.id)
                                .addClass('report-target-header')
                                .addClass('hide')
                                .html('<a href="#toggle" onclick="system.report.riskMatrixTargetToggle(' + target.id + ');">' + target.host + '</a>');

                            targetDiv = $('<div>')
                                .attr('data-id', target.id)
                                .addClass('report-target-content')
                                .addClass('hide');

                            if (target.checks.length) {
                                for (k = 0; k < target.checks.length; k++) {
                                    check = target.checks[k];

                                    if (check.rating == _system.constants.TargetCheck.RATING_HIGH_RISK) {
                                        rating = '<span class="label label-high-risk">' + check.ratingName + '</span>';
                                    } else if (check.rating == _system.constants.TargetCheck.RATING_MED_RISK) {
                                        rating = '<span class="label label-med-risk">' + check.ratingName + '</span>';
                                    }

                                    $('<div>')
                                        .attr('data-id', check.id)
                                        .addClass('report-check-header')
                                        .html(
                                            '<table class="report-check"><tbody><tr><td class="name">' +
                                            '<a href="#toggle" onclick="system.report.riskMatrixCheckToggle(' + check.id +
                                            ');">' + check.name + '</a></td>' +
                                            '<td class="status">' + rating +  '</td></tr></tbody></table>'
                                        )
                                        .appendTo(targetDiv);

                                    checkDiv = $('<div>')
                                        .attr('data-id', check.id)
                                        .addClass('report-check-content');

                                    for (j = 0; j < _report._riskMatrixCategories.length; j++) {
                                        damage = 1;
                                        likelihood = 1;

                                        risk = _report._riskMatrixCategories[j];

                                        if (check.id in risk.checks) {
                                            damage = risk.checks[check.id].damage;
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
                                                '<input type="radio" name="RiskMatrixForm[matrix][' + target.id + '][' + check.id + '][' + risk.id + '][damage]" value="1"' + ( damage == 1 ? ' checked' : '' ) + '>&nbsp;&nbsp;' +
                                                '<input type="radio" name="RiskMatrixForm[matrix][' + target.id + '][' + check.id + '][' + risk.id + '][damage]" value="2"' + ( damage == 2 ? ' checked' : '' ) + '>&nbsp;&nbsp;' +
                                                '<input type="radio" name="RiskMatrixForm[matrix][' + target.id + '][' + check.id + '][' + risk.id + '][damage]" value="3"' + ( damage == 3 ? ' checked' : '' ) + '>&nbsp;&nbsp;' +
                                                '<input type="radio" name="RiskMatrixForm[matrix][' + target.id + '][' + check.id + '][' + risk.id + '][damage]" value="4"' + ( damage == 4 ? ' checked' : '' ) + '>&nbsp;&nbsp;' +
                                                '</div>'
                                            )
                                            .appendTo(checkDiv);

                                        $('<div>')
                                            .addClass('control-group')
                                            .html(
                                                '<label class="control-label">' + _system.translate('Likelihood') + '</label>' +
                                                '<div class="controls">' +
                                                '<input type="radio" name="RiskMatrixForm[matrix][' + target.id + '][' + check.id + '][' + risk.id + '][likelihood]" value="1"' + ( likelihood == 1 ? ' checked' : '' ) + '>&nbsp;&nbsp;' +
                                                '<input type="radio" name="RiskMatrixForm[matrix][' + target.id + '][' + check.id + '][' + risk.id + '][likelihood]" value="2"' + ( likelihood == 2 ? ' checked' : '' ) + '>&nbsp;&nbsp;' +
                                                '<input type="radio" name="RiskMatrixForm[matrix][' + target.id + '][' + check.id + '][' + risk.id + '][likelihood]" value="3"' + ( likelihood == 3 ? ' checked' : '' ) + '>&nbsp;&nbsp;' +
                                                '<input type="radio" name="RiskMatrixForm[matrix][' + target.id + '][' + check.id + '][' + risk.id + '][likelihood]" value="4"' + ( likelihood == 4 ? ' checked' : '' ) + '>&nbsp;&nbsp;' +
                                                '</div>'
                                            )
                                            .appendTo(checkDiv);
                                    }

                                    checkDiv.appendTo(targetDiv);
                                }

                                $('#check-list .span8')
                                    .append(targetHeader)
                                    .append(targetDiv);

                                _report._riskMatrixTargets.push(target.id);

                                $('.report-target-header[data-id=' + target.id + ']').slideDown('fast', undefined, function () {
                                    $('.report-target-content[data-id=' + $(this).data('id') + ']').slideDown('slow', undefined, function () {
                                        if (projectReport)
                                            _report._projectFormSwitchButton();
                                        else
                                            $('.form-actions > button[type="submit"]').prop('disabled', false);
                                    });
                                });

                                if (!$('#check-list').is(':visible')) {
                                    $('#check-list').slideDown('fast');
                                }
                            } else if (projectReport) {
                                _report._projectFormSwitchButton();
                            }
                        }
                    } else if (projectReport) {
                        _report._projectFormSwitchButton();
                    }
                });
            }
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

            if (e.id == 'RiskMatrixForm_templateId') {
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

                if (val != 0) {
                    _system.control.loadObjects(val, 'category-list', function (data) {
                        if (data && data.objects.length) {
                            _report._riskMatrixCategories = data.objects;
                            $('#client-list').show();
                        } else {
                            $('#template-list').addClass('error');
                            $('#template-list > div > .help-block').show();
                        }
                    });
                }
            } else if (e.id == 'RiskMatrixForm_clientId') {
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

                if (val != 0) {
                    _system.control.loadObjects(val, 'project-list', function (data) {
                        if (data && data.objects.length) {
                            for (var i = 0; i < data.objects.length; i++) {
                                $('<option>')
                                    .val(data.objects[i].id)
                                    .attr("data-guided", data.objects[i].guided ? 1 : 0)
                                    .html(data.objects[i].name)
                                    .appendTo('#RiskMatrixForm_projectId');
                            }

                            $('#project-list').show();
                        } else {
                            $('#client-list').addClass('error');
                            $('#client-list > div > .help-block').show();
                        }
                    });
                }
            } else if (e.id == 'RiskMatrixForm_projectId') {
                val = $('#RiskMatrixForm_projectId').val();

                $('#target-list').hide();
                $('#check-list').hide();

                // remove targets
                $('#target-list > .controls > .report-target-list > li').remove();
                _report._riskMatrixTargets = [];

                // remove checklist content
                $('.report-target-header').remove();
                $('.report-target-content').remove();

                if (val != 0) {
                    var guided = parseInt($('#RiskMatrixForm_projectId option:selected').attr("data-guided"));

                    if (guided) {
                        _report._refreshChecks(false, true);
                    } else {
                        _system.control.loadObjects(val, 'target-list', function (data) {
                            if (data && data.objects.length) {
                                for (var i = 0; i < data.objects.length; i++) {
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
                                            system.report.riskMatrixFormChange(this);
                                        })
                                        .appendTo(label);

                                    label
                                        .append(' ' + data.objects[i].host)
                                        .appendTo(li);

                                    $('#target-list > .controls > .report-target-list').append(li);
                                }

                                $('#target-list').show();
                                _report._refreshChecks(false, false);
                            } else {
                                $('#project-list').addClass('error');
                                $('#project-list > div > .help-block').show();
                            }
                        });
                    }
                }
            } else if (e.id.match(/^RiskMatrixForm_targetIds_/i)) {
                _report._refreshChecks(false, false);
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
         * Vulnerability export form has been changed.
         */
        this.vulnExportFormChange = function (e) {
            var val;

            $('#client-list').removeClass('error');
            $('#project-list').removeClass('error');
            $('#project-list > div > .help-block').hide();
            $('#client-list > div > .help-block').hide();
            $('.form-actions > button[type="submit"]').prop('disabled', true);

            if (e.id == 'VulnExportReportForm_clientId') {
                val = $('#VulnExportReportForm_clientId').val();

                $('#project-list').hide();
                $('#target-list').hide();
                $('#report-details').hide();

                // clear columns
                $('input[name="VulnExportReportForm[header]"]').prop('checked', true);
                $('input[name="VulnExportReportForm[ratings][]"]').prop('checked', true);
                $('input[name="VulnExportReportForm[columns][]"]').prop('checked', true);

                if (val != 0) {
                    _system.control.loadObjects(val, 'project-list', function (data) {
                        $('#VulnExportReportForm_clientId').prop('disabled', false);
                        $('#VulnExportReportForm_projectId > option:not(:first)').remove();

                        if (data && data.objects.length) {
                            for (var i = 0; i < data.objects.length; i++) {
                                $('<option>')
                                    .val(data.objects[i].id)
                                    .attr("data-guided", data.objects[i].guided ? 1 : 0)
                                    .html(data.objects[i].name)
                                    .appendTo('#VulnExportReportForm_projectId');
                            }

                            $('#project-list').show();
                        } else {
                            $('#client-list').addClass('error');
                            $('#client-list > div > .help-block').show();
                        }
                    });
                }
            } else if (e.id == 'VulnExportReportForm_projectId') {
                val = $('#VulnExportReportForm_projectId').val();

                $('#target-list').hide();
                $('#report-details').hide();

                // clear columns
                $('input[name="VulnExportReportForm[header]"]').prop('checked', true);
                $('input[name="VulnExportReportForm[ratings][]"]').prop('checked', true);
                $('input[name="VulnExportReportForm[columns][]"]').prop('checked', true);

                if (val != 0) {
                    var guided = parseInt($('#VulnExportReportForm_projectId option:selected').attr("data-guided"));

                    if (guided) {
                        $('#report-details').show();
                        $('.form-actions > button[type="submit"]').prop('disabled', false);
                    } else {
                        _system.control.loadObjects(val, 'target-list', function (data) {
                            $('#target-list > .controls > .report-target-list > li').remove();

                            if (data && data.objects.length) {
                                for (var i = 0; i < data.objects.length; i++) {
                                    var li    = $('<li>'),
                                        label = $('<label>'),
                                        input = $('<input>');

                                    input
                                        .attr('type', 'checkbox')
                                        .prop('checked', true)
                                        .attr('id', 'VulnExportReportForm_targetIds_' + data.objects[i].id)
                                        .attr('name', 'VulnExportReportForm[targetIds][]')
                                        .val(data.objects[i].id)
                                        .click(function () {
                                            system.report.vulnExportFormChange(this);
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
                            } else {
                                $('#project-list').addClass('error');
                                $('#project-list > div > .help-block').show();
                            }
                        });
                    }
                }
            } else if (e.id.match(/^VulnExportReportForm_targetIds_/i)) {
                $('#report-details').hide();

                if ($('.report-target-list input:checked').length > 0) {
                    $('#report-details').show();

                    if ($('input[name="VulnExportReportForm[ratings][]"]:checked').length > 0 &&
                        $('input[name="VulnExportReportForm[columns][]"]:checked').length > 0) {
                        $('.form-actions > button[type="submit"]').prop('disabled', false);
                        $('#report-details').show();
                    }
                } else {
                    // clear columns
                    $('input[name="VulnExportReportForm[header]"]').prop('checked', true);
                    $('input[name="VulnExportReportForm[ratings][]"]').prop('checked', true);
                    $('input[name="VulnExportReportForm[columns][]"]').prop('checked', true);
                }
            } else if ($('input[name="VulnExportReportForm[ratings][]"]:checked').length > 0 &&
                $('input[name="VulnExportReportForm[columns][]"]:checked').length > 0) {
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

/**
 * Check if text contains HTML
 * @returns {boolean}
 */
String.prototype.isHTML = function () {
    var htmlTest = [
        "<b>",
        "<em>",
        "<u>",
        "<ul>",
        "<ol>",
        "<br />"
    ];

    for (var i = 0; i < htmlTest.length; i++) {
        if (this.indexOf(htmlTest[i]) > -1) {
            return true;
        }
    }

    return false;
};
