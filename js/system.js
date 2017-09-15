/**
 * System class.
 */
function System() {
    var _system = this;

    /**
     * Init
     */
    this.init = function () {
        this.csrf = null;
        this.ajaxTimeout = 60000;
        this.messageTimeout = 5000;
        this.l10nMessages = {};
        this.constants = {};

        $("select.show-hide-toggle").change(function () {
            var option = $(this).find("option:selected");
            $(option.data("hide")).hide();
            $(option.data("show")).show();
        });
    };

    /**
     * Refresh page without request
     * @param url
     */
    this.refreshPage = function (url) {
        this.redirect(url ? url : window.location.href.split("#")[0]);
    };

    /**
     * Redirect to url
     * @param url
     */
    this.redirect = function (url) {
        window.location = url;
    };

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
     * Detect IE11
     * @returns {boolean}
     */
    this.isIE11 = function () {
        return !(window.ActiveXObject) && "ActiveXObject" in window;
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

                    switch (operation) {
                        case 'delete':
                            $('tr[data-id=' + id + ']').fadeOut('slow', undefined, function () {
                                $('tr[data-id=' + id + ']').remove();
                                _system.addAlert('success', _system.translate('Object deleted.'));

                                if ($('table.table > tbody > tr').length == 1)
                                    location.reload();
                            });

                            break;

                        case 'up':
                        case 'down':
                            location.reload();

                            break;

                        case 'restore':
                            setTimeout(function () {
                                admin.backup.check($('.backups-list').data('check-restore-url'), "restore");
                            }, admin.backup.checkTimeout);

                            break;

                        default:
                            break;
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
            $(".form-actions > button[type=submit]").prop("disabled",
                $(".report-target-list input:checked").length == 0 ||
                $("#ProjectReportForm_templateId").val() == 0
            );
        };

        /**
         * Project template form has been changed.
         */
        this.projectTemplateFormChange = function (e) {
            var val;

            if (e.id == "ProjectReportTemplateForm_templateId") {
                var type = $("#ProjectReportTemplateForm_templateId option:selected").data("type"),
                    customReport = $(".custom-report");

                if (type != undefined && type == 0) {
                    customReport.slideDown("slow");
                } else {
                    customReport.slideUp("slow");
                }
            }
        };

        /**
         * Project form has been changed.
         */
        this.projectFormChange = function (e) {
            var val;

            if (e.id.match(/^ProjectReportForm_targetIds_/i)) {
                if ($("#ProjectReportForm_riskTemplateId").val() > 0) {
                    _report._refreshRiskMatrix(true);
                }

                _report._projectFormSwitchButton();
            } else if (e.id == "ProjectReportForm_templateId") {
                var type = $("#ProjectReportForm_templateId option:selected").data("type");

                if (type != undefined) {
                    if (type == 0) {
                        $(".rtf-report").slideDown("slow");
                    } else if (type == 1) {
                        $(".rtf-report").slideUp("slow");
                    }
                }

                _report._projectFormSwitchButton();
            } else if (e.id == "ProjectReportForm_riskTemplateId") {
                val = $("#ProjectReportForm_riskTemplateId").val();

                $("#check-list").hide();

                _report._riskMatrixTargets = [];
                _report._riskMatrixCategories = [];

                $("#risk-template-list").removeClass("error");
                $("#risk-template-list > div > .help-block").hide();

                // remove checklist content
                $(".report-target-header").remove();
                $(".report-target-content").remove();

                if (val != 0) {
                    _system.control.loadObjects(val, "category-list", function (data) {
                        if (data && data.objects.length) {
                            _report._riskMatrixCategories = data.objects;

                            if ($("#ProjectReportForm_riskTemplateId").val() > 0) {
                                _report._refreshRiskMatrix(true);
                            }

                            _report._projectFormSwitchButton();
                        } else {
                            $("#risk-template-list").addClass("error");
                            $("#risk-template-list > div > .help-block").show();
                        }
                    });
                }
            }
        };

        /**
         * Comparison form has been changed.
         */
        this.comparisonFormChange = function (e) {
            var val;

            $("#client-list").removeClass("error");
            $("#client-list > div > .help-block").hide();

            if (e.id == "ProjectComparisonForm_clientId") {
                val = $("#ProjectComparisonForm_clientId").val();

                $("#project-list").hide();
                $(".form-actions > button[type=submit]").prop("disabled", true);

                if (val != 0) {
                    _system.control.loadObjects(val, "project-list", function (data) {
                        $("#ProjectComparisonForm_projectId > option:not(:first)").remove();

                        if (data && data.objects.length) {
                            for (var i = 0; i < data.objects.length; i++) {
                                $("<option>")
                                    .val(data.objects[i].id)
                                    .html(data.objects[i].name)
                                    .appendTo("#ProjectComparisonForm_projectId");
                            }

                            $("#project-list").show();
                        } else {
                            $("#client-list").addClass("error");
                            $("#client-list > div > .help-block").show();
                        }
                    });
                }
            } else if (e.id == "ProjectComparisonForm_projectId") {
                var project = $("#ProjectComparisonForm_projectId");

                $(".form-actions > button[type=submit]").prop("disabled", true);

                if (project.val() != 0) {
                    $(".form-actions > button[type=submit]").prop("disabled", false);
                }
            }
        };

        /**
         * Refresh risk matrix check list.
         * @param bool projectReport
         */
        this._refreshRiskMatrix = function (projectReport) {
            var targets, delTargets, addTargets, i, k, id, target, matrixVar;

            matrixVar = projectReport ? "ProjectReportForm[riskMatrix]" : "RiskMatrixForm[matrix]";

            $(".form-actions > button[type=submit]").prop("disabled", true);

            targets = $(".report-target-list input:checked").map(function () {
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

                $(".report-target-content[data-id=" + id + "]").slideUp("slow", undefined, function () {
                    $(".report-target-header[data-id=" + id + "]").slideUp("fast", undefined, function () {
                        $(".report-target-header[data-id=" + id + "]").remove();
                        $(".report-target-content[data-id=" + id + "]").remove();

                        if (_report._riskMatrixTargets.length == 0) {
                            $("#check-list").slideUp("fast");
                        }
                    });
                });
            }

            if (addTargets.length > 0) {
                var param, cmd;

                param = addTargets.join(",");
                cmd = "target-check-list";

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
                                                '<input type="radio" name="' + matrixVar + '[' + target.id + '][' + check.id + '][' + risk.id + '][damage]" value="1"' + ( damage == 1 ? ' checked' : '' ) + '>&nbsp;&nbsp;' +
                                                '<input type="radio" name="' + matrixVar + '[' + target.id + '][' + check.id + '][' + risk.id + '][damage]" value="2"' + ( damage == 2 ? ' checked' : '' ) + '>&nbsp;&nbsp;' +
                                                '<input type="radio" name="' + matrixVar + '[' + target.id + '][' + check.id + '][' + risk.id + '][damage]" value="3"' + ( damage == 3 ? ' checked' : '' ) + '>&nbsp;&nbsp;' +
                                                '<input type="radio" name="' + matrixVar + '[' + target.id + '][' + check.id + '][' + risk.id + '][damage]" value="4"' + ( damage == 4 ? ' checked' : '' ) + '>&nbsp;&nbsp;' +
                                                '</div>'
                                            )
                                            .appendTo(checkDiv);

                                        $('<div>')
                                            .addClass('control-group')
                                            .html(
                                                '<label class="control-label">' + _system.translate('Likelihood') + '</label>' +
                                                '<div class="controls">' +
                                                '<input type="radio" name="' + matrixVar + '[' + target.id + '][' + check.id + '][' + risk.id + '][likelihood]" value="1"' + ( likelihood == 1 ? ' checked' : '' ) + '>&nbsp;&nbsp;' +
                                                '<input type="radio" name="' + matrixVar + '[' + target.id + '][' + check.id + '][' + risk.id + '][likelihood]" value="2"' + ( likelihood == 2 ? ' checked' : '' ) + '>&nbsp;&nbsp;' +
                                                '<input type="radio" name="' + matrixVar + '[' + target.id + '][' + check.id + '][' + risk.id + '][likelihood]" value="3"' + ( likelihood == 3 ? ' checked' : '' ) + '>&nbsp;&nbsp;' +
                                                '<input type="radio" name="' + matrixVar + '[' + target.id + '][' + check.id + '][' + risk.id + '][likelihood]" value="4"' + ( likelihood == 4 ? ' checked' : '' ) + '>&nbsp;&nbsp;' +
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

            $(".form-actions > button[type=submit]").prop("disabled", true);
            $("#template-list").removeClass("error");

            if (e.id == "RiskMatrixForm_templateId") {
                val = $("#RiskMatrixForm_templateId").val();

                $("#check-list").hide();
                _report._riskMatrixTargets = [];

                // remove checklist content
                $(".report-target-header").remove();
                $(".report-target-content").remove();

                if (val != 0) {
                    _system.control.loadObjects(val, "category-list", function (data) {
                        if (data && data.objects.length) {
                            _report._riskMatrixCategories = data.objects;
                            _report._refreshRiskMatrix(false);
                        } else {
                            $("#template-list").addClass("error");
                            $("#template-list > div > .help-block").show();
                        }
                    });
                }
            } else if (e.id.match(/^RiskMatrixForm_targetIds_/i)) {
                _report._refreshRiskMatrix(false);
            }
        };

        /**
         * Toggle risk matrix check.
         */
        this.riskMatrixCheckToggle = function (id) {
            if ($("div.report-check-content[data-id=" + id + "]").is(":visible")) {
                $("div.report-check-content[data-id=" + id + "]").slideUp("slow");
            } else {
                $("div.report-check-content[data-id=" + id + "]").slideDown("slow");
            }
        };

        /**
         * Toggle risk matrix target.
         */
        this.riskMatrixTargetToggle = function (id) {
            if ($("div.report-target-content[data-id=" + id + "]").is(":visible")) {
                $("div.report-target-content[data-id=" + id + "]").slideUp("slow");
            } else {
                $("div.report-target-content[data-id=" + id + "]").slideDown("slow");
            }
        };

        /**
         * Vulnerability export form has been changed.
         */
        this.vulnExportFormChange = function (e) {
            var val;

            $(".form-actions > button[type=submit]").prop("disabled", true);

            if (e.id.match(/^VulnExportReportForm_targetIds_/i)) {
                $("#report-details").hide();

                if ($(".report-target-list input:checked").length > 0) {
                    $("#report-details").show();

                    if ($("input[name=\"VulnExportReportForm[ratings][]\"]:checked").length > 0 &&
                        $("input[name=\"VulnExportReportForm[columns][]\"]:checked").length > 0) {
                        $(".form-actions > button[type=submit]").prop("disabled", false);
                        $("#report-details").show();
                    }
                } else {
                    // clear columns
                    $("input[name=\"VulnExportReportForm[header]\"]").prop("checked", true);
                    $("input[name=\"VulnExportReportForm[ratings][]\"]").prop("checked", true);
                    $("input[name=\"VulnExportReportForm[columns][]\"]").prop("checked", true);
                }
            } else if ($("input[name=\"VulnExportReportForm[ratings][]\"]:checked").length > 0 &&
                $("input[name=\"VulnExportReportForm[columns][]\"]:checked").length > 0) {
                    $(".form-actions > button[type=submit]").prop("disabled", false);
            }
        };

        /**
         * Show form.
         */
        this.effortForm = function () {
            $("#effort-modal").modal();
        };

        /**
         * Calculate effort.
         */
        this._calculateEffort = function () {
            var effort, checks, category, check, targets, references;

            category   = $('#EffortEstimateForm_categoryId').val();
            targets    = $('#EffortEstimateForm_targets').val();
            references = $('input[name="EffortEstimateForm[referenceIds][]"]:checked').map(
                function () {
                    return parseInt($(this).val());
                }
            ).get();

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

    /**
     * Paginator
     */
    this.paginator = new function () {
        var _paginator = this;

        /**
         * Change list item count event
         * @param c
         * @param url
         */
        this.itemCountChange = function (c, url) {
            var count = parseInt(c);

            if (count) {
                $.cookie("per_page_item_limit", count, {path: "/"});
                _system.refreshPage(url);
            }
        };
    };
}

var system = new System();

$(function () {
    system.init();
});

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
        "<br />",
        "<br>"
    ];

    for (var i = 0; i < htmlTest.length; i++) {
        if (this.indexOf(htmlTest[i]) > -1) {
            return true;
        }
    }

    return false;
};
