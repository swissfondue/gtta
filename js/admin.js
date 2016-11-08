/**
 * Admin namespace.
 */
function Admin() {
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

        /**
         * Load checks
         */
        this.loadChecks = function (obj, target) {
            var control = obj.val();

            target.find("option:gt(0)").remove();

            system.control.loadObjects(control, 'control-check-list', function (data) {
                if (data && data.objects.length) {
                    for (var i = 0; i < data.objects.length; i++) {
                        var item = data.objects[i];
                        target.append($("<option>").attr("value", item.id).html(item.name));
                    }
                }
            });
        };

        /**
         * Save check
         */
        this.save = function (formId) {
            var form = $("#" + formId);
            var radioFields = form.find(".check-field-radio");

            $.each(radioFields, function (key, value) {
                var fieldName = $(value).data("field-name");
                var fieldValues = [];

                $(value).find("li.radio-field-item input[type=text]").each(function (inputKey, inputValue) {
                    if ($(inputValue).val()) {
                        fieldValues.push($(inputValue).val());
                    }
                });

                $("#" + formId).append(
                    $("<input>")
                        .attr("type", "hidden")
                        .attr("name", fieldName)
                        .val(JSON.stringify(fieldValues))
                )
            });

            form.submit();
        };

        /**
         * Append radio field item
         * @param button
         */
        this.appendRadioFieldItem = function (button) {
            $(button).before(
                $("<li>")
                    .addClass("radio-field-item")
                    .append(
                        $("<input>")
                            .attr("type", "text")
                            .attr("class", "input-xlarge")
                            .attr("placeholder", "Option Text"),
                        "&nbsp;",
                        $("<a>")
                            .addClass("link")
                            .append(
                                $("<i>")
                                    .addClass("icon")
                                    .addClass("icon-remove")
                            )
                            .click(function () {
                                _check.removeRadioFieldItem(this);

                                return false;
                            })
                    )
            );
        };

        /**
         * Remove radio field item
         * @param button
         */
        this.removeRadioFieldItem = function (button) {
            if ($(button).parent().siblings().length > 1) {
                $(button).parent().remove();
            }
        };

        /**
         * Toggle field hide
         */
        this.toggleHiddenField = function (checkbox, name) {
            var field = $(".field-control-" + name),
                otherCheckboxes = $("input[type=checkbox][name=\"" + checkbox.name + "\"]");

            if (checkbox.checked) {
                otherCheckboxes.prop("checked", "checked");
                field.slideUp();
            } else {
                otherCheckboxes.prop("checked", null);
                field.slideDown();
            }
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
        this.toggleClientFields = function () {
            if ($('#UserEditForm_role').val() == 'client') {
                $('#client-input').show();
                $('#show-details').show();
                $('#show-reports').show();
                $('#send-notifications').hide();
            } else {
                $('#client-input').hide();
                $('#show-details').hide();
                $('#show-reports').hide();
                $('#send-notifications').show();
            }
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
                        system.addAlert('error', data.errorText);
                        callback();

                        return;
                    }

                    callback(data.data);
                },

                error : function(jqXHR, textStatus, e) {
                    $('.loader-image').hide();
                    _user._setLoaded();
                    system.addAlert('error', system.translate('Request failed, please try again.'));
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
     * Risk category object.
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
            else if (option.data('role') == 'client')
            {
                $('#ProjectUserAddForm_admin').prop('checked', false);
                $('#ProjectUserAddForm_admin').prop('disabled', true);
            }
            else
            {
                $('#ProjectUserAddForm_admin').prop('checked', false);
                $('#ProjectUserAddForm_admin').prop('disabled', false);
            }
        };
    };

    /**
     * Issue object
     */
    this.issue = new function () {
        var _issue = this;

        this.runningChecks = [];
        this.updateIteration = 0;

        /**
         * Timeout before keypress & search
         * @type {number}
         */
        this.searchTimeout = 500;
        this.searchTimeoutHandler = null;

        /**
         * Init check selection dialog
         */
        this.initCheckSelectDialog = function () {
            $("#issue-check-select-dialog").on("shown", function () {
                $(".issue-search-query").focus();
            });
        };

        /**
         * Init target selection dialog
         */
        this.initTargetSelectDialog = function () {
            $("#target-select-dialog").on("shown", function () {
                $(".target-search-query").focus();
            });
        };

        /**
         * Show add target popup
         */
        this.showTargetSelectDialog = function () {
            var modal = $("#target-select-dialog");
            var list = modal.find("table.target-list");
            var field = modal.find(".target-search-query");

            list.empty();
            field.val("");

            modal.find(".no-search-result").hide();
            modal.modal();
        };

        /**
         * Load check list
         * @param query
         * @param onChooseEvent
         */
        this.searchChecks = function (query, onChooseEvent) {
            var list = $("#issue-check-select-dialog ul.check-list");

            if (query.length < 1) {
                list.empty();
                return;
            }

            if (_issue.searchTimeoutHandler) {
                clearTimeout(_issue.searchTimeoutHandler);
            }

            _issue.searchTimeoutHandler = setTimeout(function () {
                var data = {
                    "YII_CSRF_TOKEN": system.csrf
                };

                if (query) {
                    data["SearchForm[query]"] = query;
                }

                $.ajax({
                    dataType: "json",
                    url: $("[data-search-check-url]").data("search-check-url"),
                    timeout: system.ajaxTimeout,
                    type: "POST",

                    data: data,

                    success : function (data, textStatus) {
                        $(".loader-image").hide();

                        if (data.status == "error") {
                            system.addAlert("error", data.errorText);
                            return;
                        }

                        var checks = data.data.checks;

                        list.empty();
                        list.siblings(".no-search-result").hide();

                        if (checks.length) {
                            var link;

                            $.each(checks, function (key, value) {
                                link = $("<a>")
                                    .attr("href", "#")
                                    .text(value.name)
                                    .click(function () {
                                        onChooseEvent(value.id);
                                    });

                                list.append(
                                    $("<li>").append(link)
                                )
                            });
                        } else {
                            list.siblings(".no-search-result").show();
                        }
                    },

                    error : function(jqXHR, textStatus, e) {
                        $(".loader-image").hide();
                        system.addAlert("error", system.translate("Request failed, please try again."));
                    },

                    beforeSend : function (jqXHR, settings) {
                        $(".loader-image").show();
                    }
                });
            }, _issue.searchTimeout);
        };

        /**
         * Search targets
         * @param query
         */
        this.searchTargets = function (query) {
            var list = $("#target-select-dialog ul.target-list");

            if (query.length < 1) {
                list.empty();

                return;
            }

            if (_issue.searchTimeoutHandler) {
                clearTimeout(_issue.searchTimeoutHandler);
            }

            _issue.searchTimeoutHandler = setTimeout(function () {
                var data = {
                    "YII_CSRF_TOKEN": system.csrf
                };

                if (query) {
                    data["SearchForm[query]"] = query;
                }

                $.ajax({
                    dataType : "json",
                    url      : $("[data-search-target-url]").data("search-target-url"),
                    timeout  : system.ajaxTimeout,
                    type     : "POST",

                    data : data,

                    success : function (data, textStatus) {
                        $(".loader-image").hide();

                        if (data.status == "error") {
                            system.addAlert("error", data.errorText);
                            return;
                        }

                        var targets = data.data.targets;

                        list.empty();
                        list.siblings(".no-search-result").hide();

                        if (targets.length) {
                            var link;

                            $.each(targets, function (key, value) {
                                link = $("<a>")
                                    .attr("href", "#")
                                    .text(value.name)
                                    .click(function () {
                                        _issue.evidence.add(value.id);
                                    });

                                list.append(
                                    $("<li>").append(link)
                                );
                            });
                        } else {
                            list.siblings(".no-search-result").show();
                        }
                    },

                    error : function(jqXHR, textStatus, e) {
                        $(".loader-image").hide();
                        system.addAlert("error", system.translate("Request failed, please try again."));
                    },

                    beforeSend : function (jqXHR, settings) {
                        $(".loader-image").show();
                    }
                });
            }, _issue.searchTimeout);
        };

        /**
         * Update status of running checks
         */
        this.update = function () {
            var i, k, checkIds;

            checkIds = [];

            if (_issue.runningChecks.length > 0) {
                _issue.updateIteration++;
            } else {
                _issue.updateIteration = 0;
            }

            for (i = 0; i < _issue.runningChecks.length; i++) {
                var minutes, seconds, time;

                var check = _issue.runningChecks[i];
                var row = $("[data-target-check-id=" + check.id + "]");

                if (check.time > -1) {
                    check.time++;

                    minutes = 0;
                    seconds = check.time;
                } else {
                    minutes = 0;
                    seconds = 0;
                }

                if (seconds > 59) {
                    minutes = Math.floor(seconds / 60);
                    seconds = seconds - (minutes * 60);
                }

                row.find(".run-info .check-time").html(minutes.zeroPad(2) + ":" + seconds.zeroPad(2));
                checkIds.push(check.id);
            }

            if (_issue.updateIteration > 5) {
                _issue.updateIteration = 0;

                $.ajax({
                    dataType: "json",
                    url: $("[data-update-running-checks-url]").data("update-running-checks-url"),
                    timeout: system.ajaxTimeout,
                    type: "POST",
                    data: {
                        "TargetCheckUpdateForm[checks]": checkIds.join(","),
                        "YII_CSRF_TOKEN": system.csrf
                    },

                    success : function (data, textStatus) {
                        $(".loader-image").hide();

                        if (data.status == "error") {
                            system.addAlert("error", data.errorText);
                            return;
                        }

                        data = data.data;

                        if (data.checks) {
                            for (i = 0; i < data.checks.length; i++) {
                                var check;

                                check = data.checks[i];
                                var row = $("[data-target-check-id=" + check.id + "]");

                                if (check.poc) {
                                    var poc = row.find(".evidence-field.poc .field-value");

                                    poc.addClass("issue-pre");
                                    poc.text(check.poc);
                                }

                                if (check.finished) {
                                    var innerCheck = _issue.runningChecks.filter(function (obj) {
                                        return obj.id == check.id
                                    });

                                    if (innerCheck.length) {
                                        _issue.runningChecks.splice(_issue.runningChecks.indexOf(innerCheck), 1);
                                    }

                                    row.find(".start-button").removeClass("hide");
                                    row.find(".stop-button").addClass("hide");
                                    row.find(".start-button", ".stop-button").prop("disabled", true);

                                    row.find(".run-info .check-time").empty();
                                }
                            }
                        }
                    },

                    error : function(jqXHR, textStatus, e) {
                        $(".loader-image").hide();
                        system.addAlert("error", system.translate("Request failed, please try again."));
                    },

                    beforeSend : function (jqXHR, settings) {
                        $(".loader-image").show();
                    }
                });
            }
        };

        /**
         * Get running check list
         * @param url
         */
        this.getRunningChecks = function () {
            $.ajax({
                dataType: "json",
                url: $("[data-get-running-checks-url]").data("get-running-checks-url"),
                timeout: system.ajaxTimeout,
                type: "POST",
                data: {
                    "YII_CSRF_TOKEN": system.csrf
                },

                success : function (data, textStatus) {
                    $(".loader-image").hide();

                    if (data.status == "error") {
                        system.addAlert("error", data.errorText);
                        return;
                    }

                    if (data.data.checks) {
                        $.each(data.data.checks, function (key, value) {
                            var exists = _issue.runningChecks.filter(function (obj) {
                                return obj.id == value.id
                            });

                            if (!exists.length) {
                                var row = $("[data-target-check-id=" + value["id"] + "]");

                                row.find(".start-button").addClass("hide");
                                row.find(".stop-button").removeClass("hide");

                                _issue.runningChecks.push({
                                    "id" : value.id,
                                    "time" : value.time
                                });
                            } else {
                                exists[0].time = value.time;
                            }
                        });
                    }
                },

                error : function(jqXHR, textStatus, e) {
                    $(".loader-image").hide();
                    system.addAlert("error", system.translate("Request failed, please try again."));
                },

                beforeSend : function (jqXHR, settings) {
                    $(".loader-image").show();
                }
            });
        };

        /**
         * Delete issue
         * @param url
         * @param id
         * @param callback
         */
        this.delete = function (url, id, callback) {
            if (confirm(system.translate("Are you sure that you want to delete this issue?"))) {
                _issue.control(url, id, "delete", callback);
            }
        };

        /**
         * Add issue for current project and provided check
         * @param checkId
         */
        this.add = function (checkId) {
            var data = {
                "YII_CSRF_TOKEN": system.csrf,
                "EntryControlForm[id]": checkId,
                "EntryControlForm[operation]": "add"
            };

            $.ajax({
                dataType: "json",
                url: $("[data-add-issue-url]").data("add-issue-url"),
                timeout: system.ajaxTimeout,
                type: "POST",
                data: data,

                success: function (data, textStatus) {
                    $(".loader-image").hide();

                    if (data.status == "error") {
                        system.addAlert("error", data.errorText);
                        return;
                    }

                    window.location.href = data.data.url;
                },

                error: function(jqXHR, textStatus, e) {
                    $(".loader-image").hide();
                    system.addAlert("error", system.translate("Request failed, please try again."));
                },

                beforeSend: function (jqXHR, settings) {
                    $(".loader-image").show();
                }
            });
        };

        /**
         * Control issue
         * @param url
         * @param id
         * @param operation
         * @param callback
         */
        this.control = function (url, id, operation, callback) {
            $.ajax({
                dataType: "json",
                url: url,
                timeout: system.ajaxTimeout,
                type: "POST",

                data: {
                    "EntryControlForm[operation]": operation,
                    "EntryControlForm[id]": id,
                    "YII_CSRF_TOKEN": system.csrf
                },

                success : function (data, textStatus) {
                    $(".loader-image").hide();

                    if (data.status == "error") {
                        system.addAlert("error", data.errorText);
                        return;
                    }

                    data = data.data;

                    if (operation == "delete") {
                        if (callback) {
                            callback();
                        }
                    }
                },

                error : function(jqXHR, textStatus, e) {
                    $(".loader-image").hide();
                    system.addAlert("error", system.translate("Request failed, please try again."));
                },

                beforeSend : function (jqXHR, settings) {
                    $(".loader-image").show();
                }
            });
        };

        /**
         * Evidence object
         */
        this.evidence = new function () {
            var _evidence = this;

            /**
             * Start evidence
             * @param url
             * @param id
             */
            this.start = function (url, id) {
                $.ajax({
                    dataType: "json",
                    url: url,
                    timeout: system.ajaxTimeout,
                    type: "POST",

                    data: {
                        "EntryControlForm[operation]": "start",
                        "EntryControlForm[id]": id,
                        "YII_CSRF_TOKEN": system.csrf
                    },

                    success: function (data, textStatus) {
                        if (data.status == "error") {
                            system.addAlert("error", data.errorText);
                            return;
                        }

                        data = data.data;

                        var row = $("[data-target-check-id=" + id + "]");

                        row.find(".run-info .check-time").html("00:00");
                        row.find(".start-button").addClass("hide");
                        row.find(".stop-button").removeClass("hide");
                        row.find(".start-button", ".stop-button").prop("disabled", true);


                        $(".loader-image").hide();

                        _issue.runningChecks.push({
                            id: id,
                            time: -1,
                            result: ""
                        });
                    },

                    error: function(jqXHR, textStatus, e) {
                        system.addAlert("error", system.translate("Request failed, please try again."));
                    }
                });
            };

            /**
             * Stop evidence
             * @param url
             * @param id
             */
            this.stop = function (url, id) {
                $.ajax({
                    dataType: "json",
                    url: url,
                    timeout: system.ajaxTimeout,
                    type: "POST",

                    data: {
                        "EntryControlForm[operation]": "stop",
                        "EntryControlForm[id]": id,
                        "YII_CSRF_TOKEN": system.csrf
                    },

                    success: function (data, textStatus) {
                        if (data.status == "error") {
                            system.addAlert("error", data.errorText);
                            return;
                        }

                        data = data.data;

                        var row = $("[data-target-check-id=" + id + "]");

                        row.find(".start-button").addClass("hide");
                        row.find(".stop-button").removeClass("hide").prop("disabled", true);

                        $(".loader-image").hide();
                    },

                    error: function(jqXHR, textStatus, e) {
                        _evidence.setLoaded(id, custom);
                        system.addAlert("error", system.translate("Request failed, please try again."));
                    }
                });
            };

            /**
             * Delete evidence
             * @param url
             * @param id
             * @param callback
             */
            this.delete = function (url, id, callback) {
                if (confirm(system.translate("Are you sure that you want to delete this evidence?"))) {
                    _issue.control(url, id, "delete", callback);
                }
            };

            /**
             * Add evidence for current issue and provided target
             * @param targetId
             */
            this.add = function (targetId) {
                var data = {
                    "YII_CSRF_TOKEN": system.csrf,
                    "EntryControlForm[id]": targetId,
                    "EntryControlForm[operation]": "add"
                };

                $.ajax({
                    dataType: "json",
                    url: $("[data-add-evidence-url]").data("add-evidence-url"),
                    timeout: system.ajaxTimeout,
                    type: "POST",
                    data: data,

                    success: function (data, textStatus) {
                        $(".loader-image").hide();

                        if (data.status == "error") {
                            system.addAlert("error", data.errorText);
                            return;
                        }

                        location.reload();
                    },

                    error: function(jqXHR, textStatus, e) {
                        $(".loader-image").hide();
                        system.addAlert("error", system.translate("Request failed, please try again."));
                    },

                    beforeSend: function (jqXHR, settings) {
                        $(".loader-image").show();
                    }
                });
            };
        };
    };

    /**
     * Report template object.
     */
    this.reportTemplate = new function () {
        var _reportTemplate = this;

        /**
         * Initialize header image upload form.
         */
        this.initHeaderImageUploadForm = function () {
            $('input[name^="ReportTemplateHeaderImageUploadForm"]').each(function () {
                var url  = $(this).data('upload-url'),
                    data = {};

                data['YII_CSRF_TOKEN'] = system.csrf;

                var imageWrapper = $('.image-wrapper[data-image-type=header]');

                $(this).fileupload({
                    dataType             : 'json',
                    url                  : url,
                    forceIframeTransport : true,
                    timeout              : 120000,
                    formData             : data,
                    dropZone:$('input[name^="ReportTemplateHeaderImageUploadForm"]'),

                    done : function (e, data) {
                        $('.loader-image').hide();
                        imageWrapper.find('.upload-message').hide();
                        imageWrapper.find('.file-input').show();

                        var json = data.result;

                        if (json.status == 'error')
                        {
                            system.addAlert('error', json.errorText);
                            return;
                        }

                        data = json.data;

                        // refresh the image
                        var d = new Date();

                        if ($('.header-image > img').length)
                            $('.header-image > img').attr('src', data.url + '?' + d.getTime());
                        else
                            $('.header-image').html('<img src="' + data.url + '?' + d.getTime() + '" width="400">');

                        $('.delete-header-link').show();
                    },

                    fail : function (e, data) {
                        $('.loader-image').hide();
                        imageWrapper.find('.upload-message').hide();
                        imageWrapper.find('.file-input').show();
                        system.addAlert('error', system.translate('Request failed, please try again.'));
                    },

                    start : function (e) {
                        $('.loader-image').show();
                        imageWrapper.find('.file-input').hide();
                        imageWrapper.find('.upload-message').show();
                    }
                });
            });
        };

        /**
         * Control header image function.
         */
        this._controlHeaderImage = function(id, operation) {
            var url = $('.header-image').data('control-url');

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
                        system.addAlert('error', data.errorText);
                        return;
                    }

                    if (operation == 'delete')
                    {
                        $('.header-image').html(system.translate('No header image.'));
                        $('.delete-header-link').hide();
                    }
                },

                error : function(jqXHR, textStatus, e) {
                    $('.loader-image').hide();
                    system.addAlert('error', system.translate('Request failed, please try again.'));
                },

                beforeSend : function (jqXHR, settings) {
                    $('.loader-image').show();
                }
            });
        };

        /**
         * Delete header image.
         */
        this.delHeaderImage = function (id) {
            if (confirm(system.translate('Are you sure that you want to delete this object?'))) {
                _reportTemplate._controlHeaderImage(id, 'delete');
            }
        };

        /**
         * Initialize header image upload form.
         */
        this.initRatingImageUploadForm = function () {
            $('.image-wrapper[data-image-type=rating]').each(function () {
                var input = $(this).find('input[name^="ReportTemplateRatingImageUploadForm"]');
                var url = input.data('upload-url');
                var data = {};
                var imageWrapper = $(this);
                var itemId = imageWrapper.data('item-id');

                data['YII_CSRF_TOKEN'] = system.csrf;

                $(this).fileupload({
                    dataType             : 'json',
                    url                  : url,
                    forceIframeTransport : true,
                    timeout              : 120000,
                    formData             : data,
                    dropZone:$('input[name^="ReportTemplateRatingImageUploadForm"]'),

                    done : function (e, data) {
                        imageWrapper.find('.upload-message').hide();
                        imageWrapper.find('.file-input').show();

                        var json = data.result;

                        if (json.status == 'error')
                        {
                            system.addAlert('error', json.errorText);
                            return;
                        }

                        data = json.data;

                        // refresh the image
                        var d = new Date();

                        var rImage = imageWrapper.find('.rating-image');

                        if (rImage.find('img').length)
                            rImage.find('img').attr('src', data.url + '?' + d.getTime());
                        else
                            rImage.html('<img src="' + data.url + '?' + d.getTime() + '" width="32">');

                        imageWrapper.find('.delete-rating-image-link').show();
                    },

                    fail : function (e, data) {
                        $('.loader-image').hide();
                        imageWrapper.find('.upload-message').hide();
                        imageWrapper.find('.file-input').show();
                        system.addAlert('error', system.translate('Request failed, please try again.'));
                    },

                    start : function (e) {
                        $('.loader-image').show();
                        imageWrapper.find('.file-input').hide();
                        imageWrapper.find('.upload-message').show();
                    }
                });
            });
        };

        /**
         * Control header image function.
         */
        this._controlRatingImage = function(id, operation) {
            var imageWrapper = $('.image-wrapper[data-image-type=rating][data-item-id=' + id + ']');
            var picBlock = imageWrapper.find('.rating-image');
            var url = picBlock.data('control-url');

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
                        system.addAlert('error', data.errorText);
                        return;
                    }

                    if (operation == 'delete')
                    {
                        picBlock.html(system.translate('No rating image.'));
                        imageWrapper.find('.delete-rating-image-link').hide();
                    }
                },

                error : function(jqXHR, textStatus, e) {
                    $('.loader-image').hide();
                    system.addAlert('error', system.translate('Request failed, please try again.'));
                },

                beforeSend : function (jqXHR, settings) {
                    $('.loader-image').show();
                }
            });
        };

        /**
         * Delete header image.
         */
        this.delRatingImage = function (id) {
            if (confirm(system.translate('Are you sure that you want to delete this object?'))) {
                _reportTemplate._controlRatingImage(id, 'delete');
            }
        };

        /**
         * Report type change handler
         */
        this.onTypeChange = function () {
            var type = $("#ReportTemplateEditForm_type").val();

            if (type == 0) {
                $(".docx-report").slideUp("slow", function () {
                    $(".rtf-report").slideDown("fast");
                });
            } else {
                $(".rtf-report").slideUp("slow", function () {
                    $(".docx-report").slideDown("fast");
                });
            }
        };

        /**
         * Initialize file template upload form.
         */
        this.initTemplateUploadForm = function () {
            $('input[name^="ReportTemplateFileUploadForm"]').each(function () {
                var url = $(this).data("upload-url"),
                    data = {};

                data["YII_CSRF_TOKEN"] = system.csrf;

                $(this).fileupload({
                    dataType: "json",
                    url: url,
                    forceIframeTransport: true,
                    timeout: 120000,
                    formData: data,
                    dropZone: $('input[name^="ReportTemplateFileUploadForm"]'),

                    done: function (e, data) {
                        $(".loader-image").hide();
                        $(".upload-message").hide();
                        $(".file-input").show();

                        var json = data.result;

                        if (json.status == "error") {
                            system.addAlert("error", json.errorText);
                            return;
                        }

                        data = json.data;
                        $(".template-file-link").html("<a href=\"" + data.url + "\">" + system.translate("Download") + "</a>");
                        $(".delete-file-link").show();
                    },

                    fail: function (e, data) {
                        $(".loader-image").hide();
                        $(".upload-message").hide();
                        $(".file-input").show();
                        system.addAlert("error", system.translate("Request failed, please try again."));
                    },

                    start: function (e) {
                        $(".loader-image").show();
                        $(".file-input").hide();
                        $(".upload-message").show();
                    }
                });
            });
        };

        /**
         * Control template file function.
         */
        this._controlTemplate = function(id, operation) {
            var url = $(".template-file").data("control-url");

            $.ajax({
                dataType: "json",
                url: url,
                timeout: system.ajaxTimeout,
                type: "POST",

                data: {
                    "EntryControlForm[operation]": operation,
                    "EntryControlForm[id]": id,
                    "YII_CSRF_TOKEN": system.csrf
                },

                success: function (data, textStatus) {
                    $(".loader-image").hide();

                    if (data.status == "error") {
                        system.addAlert("error", data.errorText);
                        return;
                    }

                    if (operation == "delete") {
                        $(".template-file-link").html(system.translate("No template file."));
                        $(".delete-file-link").hide();
                    }
                },

                error: function(jqXHR, textStatus, e) {
                    $(".loader-image").hide();
                    system.addAlert("error", system.translate("Request failed, please try again."));
                },

                beforeSend: function (jqXHR, settings) {
                    $(".loader-image").show();
                }
            });
        };

        /**
         * Delete template file.
         */
        this.delTemplate = function (id) {
            if (confirm(system.translate("Are you sure that you want to delete this object?"))) {
                _reportTemplate._controlTemplate(id, "delete");
            }
        };

        /**
         * Report Template Sections
         */
        this.sections = new function () {
            var _sections = this,
                sectionList,
                sectionData,
                fieldTypes,
                formId,
                modified = false;

            /**
             * Init sections editor
             * @param sections
             * @param fTypes
             */
            this.init = function (sections, fTypes) {
                sectionData = sections;
                fieldTypes = fTypes;
                formId = $("[data-form-id]").data("form-id");

                sectionList = Sortable.create($(".sortable-section-list")[0], {
                    group: {
                        name: "report-sections",
                        put: true,
                        pull: true
                    },

                    onAdd: function (evt) {
                        var item, type, title;

                        item = $(evt.item);

                        item.click(function () {
                            _sections.select(this);
                        });

                        item
                            .append(
                                $("<a>")
                                    .attr("href", "#remove")
                                    .click(function (e) {
                                        _sections.del(this);
                                        e.stopPropagation();
                                    })
                                    .append(
                                        $("<i>")
                                            .addClass("icon icon-remove")
                                    )
                            );

                        type = item.data("section-type");
                        title = fieldTypes[type.toString()];

                        _sections.saveSection(item, null, type, title, "");
                    },

                    onUpdate: function (evt) {
                        _sections.saveOrder();
                    }
                });

                var availableSectionList = Sortable.create($(".available-section-list")[0], {
                    sort: false,
                    group: {
                        name: "report-sections",
                        pull: "clone",
                        put: false
                    }
                });

                $(".sortable-section-list a.remove").click(function (e) {
                    _sections.del(this);
                    e.stopPropagation();
                });

                window.onbeforeunload = function(e) {
                    if (modified) {
                        return "Section is unsaved. Do you really want to continue and lose all changes?"
                    }

                    return null;
                };
            };

            /**
             * Show "Add Section" form
             */
            this.showAddForm = function () {
                var addSection = $(".add-section");

                if (addSection.is(":visible")) {
                    this.closeAddForm();
                } else {
                    $(".edit-section").slideUp("fast");
                    addSection.slideDown("fast");
                }
            };

            /**
             * Close "Add Section" form
             */
            this.closeAddForm = function () {
                $(".edit-section").slideDown("fast");
                $(".add-section").slideUp("fast");
            };

            /**
             * Remove element from the list
             * @param item
             */
            this.del = function (item) {
                var el, id;

                el = $(sectionList.closest(item));

                if (confirm(system.translate("Are you sure that you want to delete this object?"))) {
                    id = el.data("section-id");

                    if (id) {
                        $.ajax({
                            dataType: "json",
                            url: $("[data-control-section-url]").data("control-section-url"),
                            timeout: system.ajaxTimeout,
                            type: "POST",

                            data: {
                                "EntryControlForm[operation]": "delete",
                                "EntryControlForm[id]": id,
                                "YII_CSRF_TOKEN": system.csrf
                            },

                            success: function (data, textStatus) {
                                $(".loader-image").hide();

                                if (data.status == "error") {
                                    system.addAlert("error", data.errorText);
                                    return;
                                }

                                el.slideUp("fast", function () {
                                    el.remove();
                                });
                            },

                            error: function (jqXHR, textStatus, e) {
                                $(".loader-image").hide();
                                system.addAlert("error", system.translate("Request failed, please try again."));
                            },

                            beforeSend: function (jqXHR, settings) {
                                $(".loader-image").show();
                            }
                        });
                    } else {
                        el.slideUp("fast", function () {
                            el.remove();
                        });
                    }
                }
            };

            /**
             * Select list item
             * @param item
             */
            this.select = function (item) {
                if (modified && !confirm("Section has unsaved changes. Do you really want to continue and lose all changes?")) {
                    return;
                }

                modified = false;

                var id, template, editSection, title, content, type;

                item = $(item);
                editSection = $(".edit-section");
                id = item.data("section-id");

                if (id) {
                    title = sectionData[id].title;
                    content = sectionData[id].content;
                    type = sectionData[id].type;
                } else {
                    type = item.data("section-type");
                    title = fieldTypes[type.toString()];
                }

                template = $(".section-form-template").clone();
                this.closeAddForm();

                $(".sortable-section-list li").removeClass("selected");
                item.addClass("selected");

                template
                    .removeClass("section-form-template")
                    .removeClass("hide")
                    .data("section-id", id);

                template.find("[name=\"" + formId + "[title]\"]")
                    .val(title)
                    .change(function () {
                        modified = true;
                    });

                template.find("[name=\"" + formId + "[content]\"]")
                    .val(content);

                template.find("[data-field-type]")
                    .text(fieldTypes[type.toString()]);

                template.find("button")
                    .click(function () {
                        _sections.saveSectionForm(item);
                        return false;
                    });

                template.find(".wysiwyg").ckeditor(function () {
                    this.on("change", function() {
                         if (this.checkDirty()) {
                             modified = true;
                         }
                    })
                });

                editSection
                    .empty()
                    .append(template);
            };

            /**
             * Get sections order
             */
            this.getOrder = function () {
                var order = [];

                $(".sortable-section-list li").each(function (number, e) {
                    order[number] = $(e).data("section-id");
                });

                return order;
            };

            /**
             * Save sections order
             */
            this.saveOrder = function () {
                var data = {"YII_CSRF_TOKEN": system.csrf};
                data[formId + "[order]"] = JSON.stringify(_sections.getOrder());

                $.ajax({
                    dataType: "json",
                    url: $("[data-save-section-order-url]").data("save-section-order-url"),
                    timeout: system.ajaxTimeout,
                    type: "POST",
                    data: data,

                    success: function (data, textStatus) {
                        $(".loader-image").hide();

                        if (data.status == "error") {
                            system.addAlert("error", data.errorText);
                        }
                    },

                    error: function(jqXHR, textStatus, e) {
                        $(".loader-image").hide();
                        system.addAlert("error", system.translate("Request failed, please try again."));
                    },

                    beforeSend: function (jqXHR, settings) {
                        $(".loader-image").show();
                    }
                });
            };

            /**
             * Save section
             * @param listItem
             * @param id
             * @param type
             * @param title
             * @param content
             */
            this.saveSection = function (listItem, id, type, title, content) {
                var editSection, button, data;

                editSection = $(".edit-section");
                button = editSection.find("button");
                data = {"YII_CSRF_TOKEN": system.csrf};
                
                data[formId + "[order]"] = JSON.stringify(_sections.getOrder());
                data[formId + "[content]"] = content;
                data[formId + "[title]"] = title;
                data[formId + "[type]"] = type;
                data[formId + "[id]"] = id;

                $.ajax({
                    dataType: "json",
                    url: $("[data-save-section-url]").data("save-section-url"),
                    timeout: system.ajaxTimeout,
                    type: "POST",

                    data: data,

                    success: function (data, textStatus) {
                        $(".loader-image").hide();
                        button.prop("disabled", false);

                        if (data.status == "error") {
                            system.addAlert("error", data.errorText);
                            return;
                        }

                        data = data.data;

                        if (!id) {
                            id = data.id;
                        }

                        sectionData[id] = {
                            type: type,
                            title: title,
                            content: content
                        };

                        listItem.attr("data-section-id", id);
                        listItem.find("span.title").text(title);

                        system.addAlert("success", system.translate("Section saved."));
                        modified = false;
                    },

                    error: function(jqXHR, textStatus, e) {
                        $(".loader-image").hide();
                        system.addAlert("error", system.translate("Request failed, please try again."));
                        button.prop("disabled", false);
                    },

                    beforeSend: function (jqXHR, settings) {
                        $(".loader-image").show();
                        button.prop("disabled", true);
                    }
                });
            };

            /**
             * Save section form
             * @param listItem
             */
            this.saveSectionForm = function (listItem) {
                var editSection, title, content, type, id;

                id = listItem.data("section-id") || null;
                type = listItem.data("section-type");

                editSection = $(".edit-section");
                title = editSection.find("input").val();
                content = editSection.find("textarea").val();

                _sections.saveSection(listItem, id, type, title, content);
            };
        };
    };

    /**
     * Client object.
     */
    this.client = new function () {
        var _client = this;

        /**
         * Initialize logo upload form.
         * @param newClient
         */
        this.initLogoUploadForm = function (newClient) {
            $('input[name^="ClientLogoUploadForm"]').each(function () {
                var url = $(this).data('upload-url'),
                    data = {};

                data['YII_CSRF_TOKEN'] = system.csrf;

                $(this).fileupload({
                    dataType: 'json',
                    url: url,
                    forceIframeTransport: true,
                    timeout: 120000,
                    formData: data,
                    dropZone:$('input[name^="ClientLogoUploadForm"]'),

                    done : function (e, data) {
                        $('.loader-image').hide();
                        $('.upload-message').hide();
                        $('.file-input').show();

                        var json = data.result;

                        if (json.status == 'error') {
                            system.addAlert('error', json.errorText);
                            return;
                        }

                        data = json.data;

                        // refresh the image
                        var d = new Date();

                        if ($('.logo-image > img').length) {
                            $('.logo-image > img').attr('src', data.url + '?' + d.getTime());
                        } else {
                            $('.logo-image').html('<img src="' + data.url + '?' + d.getTime() + '">');
                        }

                        if (newClient) {
                            $("#ClientEditForm_logoPath").val(data.path);
                        }

                        $('.delete-logo-link').show();
                    },

                    fail : function (e, data) {
                        $('.loader-image').hide();
                        $('.upload-message').hide();
                        $('.file-input').show();
                        system.addAlert('error', system.translate('Request failed, please try again.'));
                    },

                    start : function (e) {
                        $('.loader-image').show();
                        $('.file-input').hide();
                        $('.upload-message').show();
                    }
                });
            });
        };

        /**
         * Control logo.
         */
        this._controlLogo = function(id, operation) {
            var url = $('.logo-image').data('control-url');

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
                        system.addAlert('error', data.errorText);
                        return;
                    }

                    if (operation == 'delete')
                    {
                        $('.logo-image').html(system.translate('No logo.'));
                        $('.delete-logo-link').hide();
                    }
                },

                error : function(jqXHR, textStatus, e) {
                    $('.loader-image').hide();
                    system.addAlert('error', system.translate('Request failed, please try again.'));
                },

                beforeSend : function (jqXHR, settings) {
                    $('.loader-image').show();
                }
            });
        };

        /**
         * Delete logo.
         */
        this.delLogo = function (id) {
            if (confirm(system.translate('Are you sure that you want to delete this object?'))) {
                if (id) {
                    _client._controlLogo(id, 'delete');
                } else {
                    $('.logo-image').html(system.translate('No logo.'));
                    $('.delete-logo-link').hide();
                    $("#ClientEditForm_logoPath").val("");
                }
            }
        };
    };

    /**
     * Process object.
     */
    this.process = new function () {
        var _process = this;

        /**
         * Control process function.
         */
        this._control = function (id, operation) {
            var row, url;

            row = $('div.process-monitor[data-id="' + id + '"]');
            url = row.data('control-url');

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
                        system.addAlert('error', data.errorText);
                        return;
                    }

                    data = data.data;

                    if (operation == 'stop') {
                        row.fadeOut('slow', undefined, function () {
                            row.remove();
                            system.addAlert('success', system.translate('Task stopped.'));

                            if ($('div.process-monitor').length <= 1) {
                                // Timeout for stoping task
                                setTimeout(function () {
                                    location.reload();
                                }, 2000);
                            }
                        });
                    }
                },

                error : function(jqXHR, textStatus, e) {
                    $('.loader-image').hide();
                    system.addAlert('error', system.translate('Request failed, please try again.'));
                },

                beforeSend : function (jqXHR, settings) {
                    $('.loader-image').show();
                }
            });
        };

        /**
         * Stop process.
         */
        this.stop = function (targetId, checkId) {
            var id = targetId.toString() + '-' + checkId.toString();
            var row = $('div.process-monitor[data-id="' + id + '"]');
            row.addClass('delete-row');

            if (confirm(system.translate('Are you sure that you want to stop this task?'))) {
                _process._control(id, 'stop');
            } else {
                row.removeClass('delete-row');
            }
        };
    };

    /**
     * Background job object
     */
    this.job = new function () {
        /**
         * Get job's log
         * @param job
         * @private
         */
        this.getLog = function (job) {
            var url, log;

            if (job != '0') {
                url = $("#BgLogForm_job").data('url');

                $.ajax({
                    dataType : 'json',
                    url      : url,
                    timeout  : system.ajaxTimeout,
                    type     : 'POST',

                    data : {
                        'BgLogForm[job]'     : job,
                        'YII_CSRF_TOKEN'    : system.csrf
                    },

                    success : function (data, textStatus) {
                        $('.loader-image').hide();
                        $("#BgLogForm select, #BgLogForm textarea").removeAttr("disabled");

                        if (data.status == 'error')
                        {
                            system.addAlert('error', data.errorText);
                            return;
                        }

                        data = data.data;

                        log = data.log;

                        if (!log) {
                            log = system.translate("Log is empty.");
                        } else {
                            $("#clear").removeAttr("disabled");
                        }

                        $("#BgLogForm_log").html(log);
                    },

                    error : function(jqXHR, textStatus, e) {
                        $('.loader-image').hide();
                        $("#BgLogForm select, #BgLogForm textarea").removeAttr("disabled");
                        system.addAlert('error', system.translate('Request failed, please try again.'));
                    },

                    beforeSend : function (jqXHR, settings) {
                        $("#BgLogForm select, #BgLogForm textarea, #clear").prop('disabled', true);
                        $('.loader-image').show();
                    }
                });
            } else {
                $("#BgLogForm_log").html("");
                $("#clear").prop("disabled", true);
            }
        };

        /**
         * Clear job's log
         * @param url
         */
        this.clearLog = function (url) {
            var job = $("#BgLogForm_job").val();

            if (job != '0') {
                $.ajax({
                    dataType : 'json',
                    url      : url,
                    timeout  : system.ajaxTimeout,
                    type     : 'POST',

                    data : {
                        'EntryControlForm[id]'          : job,
                        'EntryControlForm[operation]'   : 'clear',
                        'YII_CSRF_TOKEN'                : system.csrf
                    },

                    success : function (data, textStatus) {
                        $('.loader-image').hide();
                        $("#clear").removeClass("active");
                        $("#BgLogForm select, #BgLogForm textarea").removeAttr("disabled");

                        if (data.status == 'error')
                        {
                            system.addAlert('error', data.errorText);
                            return;
                        }

                        data = data.data;

                        $("#BgLogForm_log").html(system.translate("Log is empty."));

                        system.addAlert("success", system.translate("Log cleared."));
                    },

                    error : function(jqXHR, textStatus, e) {
                        $('.loader-image').hide();
                        $("#BgLogForm select, #BgLogForm textarea, #clear").removeAttr("disabled");
                        $("#clear").removeClass("active");
                        system.addAlert('error', system.translate('Request failed, please try again.'));
                    },

                    beforeSend : function (jqXHR, settings) {
                        $("#BgLogForm select, #BgLogForm textarea, #clear").prop('disabled', true);
                        $('.loader-image').show();
                    }
                });
            }
        };
    };

    /**
     * Update object.
     */
    this.update = new function () {
        var _update = this;

        /**
         * Refresh status
         */
        this.update = function (url) {
            $.ajax({
                dataType: "json",
                url: url,
                timeout: system.ajaxTimeout,
                type: "POST",

                data: {
                    YII_CSRF_TOKEN: system.csrf
                },

                success: function (data, textStatus) {
                    $('.loader-image').hide();

                    if (data.status == "error") {
                        system.addAlert("error", data.errorText);
                        return;
                    }

                    data = data.data;

                    if (data.updating) {
                        setTimeout(function () {
                            _update.update(url);
                        }, 5000);
                    } else {
                        location.reload();
                    }
                },

                error: function(jqXHR, textStatus, e) {
                    $(".loader-image").hide();

                    if (textStatus == "timeout") {
                        setTimeout(function () {
                            _update.update(url);
                        }, 5000);
                    } else {
                        system.addAlert("error", system.translate("Request failed, please try again."));
                    }
                },

                beforeSend: function (jqXHR, settings) {
                    $(".loader-image").show();
                }
            });
        };
    };

    /**
     * Package object.
     */
    this.pkg = new function () {
        var _package = this;

        /**
         * Initialize the upload form.
         */
        this.initUploadForm = function () {
            $("input[name^=PackageUploadForm]").each(function () {
                var url = $(this).data("upload-url"), data;

                data = {
                    "YII_CSRF_TOKEN": system.csrf
                };

                $(this).fileupload({
                    dataType: "json",
                    url: url,
                    forceIframeTransport: true,
                    timeout: 120000,
                    formData: data,
                    dropZone:$("input[name^=PackageUploadForm]"),

                    done : function (e, data) {
                        $(".loader-image").hide();
                        $(".upload-message").hide();
                        $(".file-input").show();

                        var json = data.result;

                        if (json.status == "error") {
                            system.addAlert("error", json.errorText);
                            return;
                        }

                        data = json.data;

                        // show package data
                        $("#PackageAddForm_id").val(data.pkg.id);

                        $("#package_type")
                            .html(data.pkg.type)
                            .parent()
                            .show();

                        $("#package_name")
                            .html(data.pkg.name)
                            .parent()
                            .show();

                        $("#package_version")
                            .html(data.pkg.version)
                            .parent()
                            .show();

                        $("#submit_button").prop("disabled", false);
                    },

                    fail: function (e, data) {
                        $("#PackageAddForm_id").val(0);
                        $(".loader-image").hide();
                        $(".upload-message").hide();
                        $(".file-input").show();

                        system.addAlert("error", system.translate("Request failed, please try again."));
                    },

                    start: function (e) {
                        $("#PackageAddForm_id").val(0);

                        $(".loader-image").show();
                        $(".file-input").hide();
                        $(".upload-message").show();

                        $("#package_type").parent().hide();
                        $("#package_name").parent().hide();
                        $("#package_version").parent().hide();

                        $("#submit_button").prop("disabled", true);
                    }
                });
            });
        };

        /**
         * Regenerate status
         */
        this.checkRegenerate = function (url) {
            $.ajax({
                dataType: "json",
                url: url,
                timeout: system.ajaxTimeout,
                type: "POST",

                data: {
                    YII_CSRF_TOKEN: system.csrf
                },

                success: function (data, textStatus) {
                    $('.loader-image').hide();

                    if (data.status == "error") {
                        system.addAlert("error", data.errorText);
                        return;
                    }

                    data = data.data;

                    if (data.regenerating) {
                        setTimeout(function () {
                            _package.checkRegenerate(url);
                        }, 5000);
                    } else {
                        system.addAlert("success", "Regeneration completed.");

                        setTimeout(function() {
                            window.location.href = $('.form-description').data("redirect-url");
                        }, 5000);
                    }
                },

                error: function(jqXHR, textStatus, e) {
                    $(".loader-image").hide();

                    if (textStatus == "timeout") {
                        setTimeout(function () {
                            _package.checkRegenerate(url);
                        }, 5000);
                    } else {
                        system.addAlert("error", system.translate("Request failed, please try again."));
                    }
                },

                beforeSend: function (jqXHR, settings) {
                    $(".loader-image").show();
                }
            });
        };

        /**
         * Regenerate status
         */
        this.checkSync = function (url) {
            $.ajax({
                dataType: "json",
                url: url,
                timeout: system.ajaxTimeout,
                type: "POST",

                data: {
                    YII_CSRF_TOKEN: system.csrf
                },

                success: function (data, textStatus) {
                    $('.loader-image').hide();

                    if (data.status == "error") {
                        system.addAlert("error", data.errorText);
                        return;
                    }

                    data = data.data;

                    if (data.sync) {
                        setTimeout(function () {
                            _package.checkSync(url);
                        }, 5000);
                    } else {
                        if (data.error) {
                            system.addAlert("error", "Synchronization failed.");
                        } else {
                            system.addAlert("success", "Synchronization succeeded.");
                        }

                        setTimeout(function() {
                            system.refreshPage();
                        }, 5000);
                    }
                },

                error: function(jqXHR, textStatus, e) {
                    $(".loader-image").hide();

                    if (textStatus == "timeout") {
                        setTimeout(function () {
                            _package.checkRegenerate(url);
                        }, 5000);
                    } else {
                        system.addAlert("error", system.translate("Request failed, please try again."));
                    }
                },

                beforeSend: function (jqXHR, settings) {
                    $(".loader-image").show();
                }
            });
        };

        /**
         * File select changed
         * @param val
         */
        this.fileSelectChanged = function (val) {
            $('#PackageEditFilesForm_content').val('');

            if (val == '0') {
                $('#PackageEditFilesForm_path')
                    .val('')
                    .closest('.control-group')
                    .show();
                $('#PackageEditFilesForm_file').siblings('.del-button').hide();
            } else {
                $('#PackageEditFilesForm_path')
                    .val(val)
                    .closest('.control-group')
                    .hide();
                $('#PackageEditFilesForm_file').siblings('.del-button').show();
                this.loadFileContent();
            }
        };

        /**
         * Load package file content
         * @param package
         */
        this.loadFileContent = function () {
            var url = $('#PackageEditFilesForm_file').data('url');
            var path = $('#PackageEditFilesForm_file').val();

            $.ajax({
                dataType: "json",
                url: url,
                timeout: system.ajaxTimeout,
                type: "POST",

                data: {
                    "PackageLoadFileForm[path]": path,
                    "YII_CSRF_TOKEN": system.csrf
                },
                'success' : function (response) {
                    var content = response.data.file_content;

                    $('[id^=PackageEditFilesForm]').removeAttr('disabled');
                    $('#PackageEditFilesForm_content').val(content);

                    $(".loader-image").hide();
                },

                'error' : function (data) {
                    $('[id^=PackageEditFilesForm]').removeAttr('disabled');
                    $(".loader-image").hide();
                    system.addAlert('error', system.translate('Request failed, please try again.'));
                },

                'beforeSend' : function () {
                    $('[id^=PackageEditFilesForm]').attr('disabled', 'disabled');
                    $(".loader-image").show();
                }
            });
        };

        /**
         * Package file edit
         * @param operation
         */
        this.fileEdit = function (operation) {
            switch (operation) {
                case 'save':
                    $('#PackageEditFilesForm_operation').val('save');
                    break;
                case 'delete':
                    if (!confirm(system.translate("Are you sure that you want to delete this object?"))) {
                        return;
                    }

                    $('#PackageEditFilesForm_operation').val('delete');
                    break;
                default:
                    system.addAlert('error', 'Unknown operation type.');
            }

            $('#PackageEdit').submit();
        };

        /**
         * Get messages of scheduled packages
         * @param url
         */
        this.messages = function (url) {
            $.ajax({
                dataType: "json",
                url: url,
                timeout: system.ajaxTimeout,
                type: "POST",
                data: {
                    "YII_CSRF_TOKEN": system.csrf
                },
                'success' : function (response) {
                    var messages = response.data.messages;
                    var type, text;

                    $.each(messages, function (key, message) {
                        type = message['status'] == system.constants.Package.STATUS_INSTALLED ? "success" : "error";
                        text = message['value'];

                        system.addAlert(type, text);
                    });

                    $(".loader-image").hide();
                },

                'error' : function (data) {
                    $(".loader-image").hide();
                    system.addAlert('error', system.translate('Request failed, please try again.'));
                },

                'beforeSend' : function () {
                    $(".loader-image").show();
                }
            });
        };
    };

    /**
     * Settings object.
     */
    this.settings = new function () {
        var _settings = this;

        /**
         * Initialize logo upload form.
         */
        this.initLogoUploadForm = function () {
            $('input[name^="SystemLogoUploadForm"]').each(function () {
                var url = $(this).data("upload-url"),
                    data = {};

                data["YII_CSRF_TOKEN"] = system.csrf;

                $(this).fileupload({
                    dataType: "json",
                    url: url,
                    forceIframeTransport: true,
                    timeout: 120000,
                    formData: data,
                    dropZone: $('input[name^="SystemLogoUploadForm"]'),

                    done : function (e, data) {
                        $(".loader-image").hide();
                        $(".upload-message").hide();
                        $(".file-input").show();

                        var json = data.result;

                        if (json.status == "error") {
                            system.addAlert("error", json.errorText);
                            return;
                        }

                        data = json.data;

                        // refresh the image
                        var d = new Date();
                        $(".logo-image > img").attr("src", data.url + "?" + d.getTime());
                        $(".delete-logo-link").show();
                    },

                    fail : function (e, data) {
                        $(".loader-image").hide();
                        $(".upload-message").hide();
                        $(".file-input").show();
                        system.addAlert("error", system.translate("Request failed, please try again."));
                    },

                    start : function (e) {
                        $(".loader-image").show();
                        $(".file-input").hide();
                        $(".upload-message").show();
                    }
                });
            });
        };

        /**
         * Control logo.
         */
        this._controlLogo = function(operation) {
            var url = $(".logo-image").data("control-url");

            $.ajax({
                dataType: "json",
                url: url,
                timeout: system.ajaxTimeout,
                type: "POST",

                data: {
                    "EntryControlForm[operation]": operation,
                    "EntryControlForm[id]": 1,
                    "YII_CSRF_TOKEN": system.csrf
                },

                success : function (data, textStatus) {
                    $(".loader-image").hide();

                    if (data.status == "error") {
                        system.addAlert("error", data.errorText);
                        return;
                    }

                    data = data.data;

                    if (operation == "delete") {
                        var d = new Date();
                        $(".logo-image > img").attr("src", data.url + "?" + d.getTime());
                        $(".delete-logo-link").hide();
                    }
                },

                error : function(jqXHR, textStatus, e) {
                    $(".loader-image").hide();
                    system.addAlert("error", system.translate("Request failed, please try again."));
                },

                beforeSend : function (jqXHR, settings) {
                    $(".loader-image").show();
                }
            });
        };

        /**
         * Delete logo.
         */
        this.delLogo = function () {
            if (confirm(system.translate("Are you sure that you want to delete this object?"))) {
                _settings._controlLogo("delete");
            }
        };

        /**
         * Generate new integration key.
         */
        this.generateIntegrationKey = function() {
            var url = $("#integration-key").data("integration-key-url");

            $.ajax({
                dataType: "json",
                url: url,
                timeout: system.ajaxTimeout,
                type: "POST",

                data: {
                    "EntryControlForm[operation]": "generate",
                    "EntryControlForm[id]": 1,
                    "YII_CSRF_TOKEN": system.csrf
                },

                success : function (data, textStatus) {
                    $(".loader-image").hide();

                    if (data.status == "error") {
                        system.addAlert("error", data.errorText);
                        return;
                    }

                    data = data.data;
                    $("#integration-key").html(data.integrationKey);
                },

                error : function(jqXHR, textStatus, e) {
                    $(".loader-image").hide();
                    system.addAlert("error", system.translate("Request failed, please try again."));
                },

                beforeSend : function (jqXHR, settings) {
                    $(".loader-image").show();
                }
            });
        };
    };

    /**
     * Project planner
     */
    this.planner = new function () {
        var _planner = this;

        /**
         * Show add plan form.
         */
        this.planForm = function () {
            $('#plan-modal').modal();
        };

        /**
         * Form change handler
         * @param elem
         */
        this.onFormChange = function (elem) {
            var id, targetId, projectId, categoryId, moduleId;

            elem = $(elem);
            id = $(elem).attr("id");

            $("#add-button").prop("disabled", true);

            targetId = $("#ProjectPlannerEditForm_targetId");
            projectId = $("#ProjectPlannerEditForm_projectId");
            categoryId = $("#ProjectPlannerEditForm_categoryId");
            moduleId = $("#ProjectPlannerEditForm_moduleId");

            if (id == "ProjectPlannerEditForm_clientId") {
                projectId.find("option:not(:first)").remove();
                targetId.find("option:not(:first)").remove();
                categoryId.find("option:not(:first)").remove();
                moduleId.find("option:not(:first)").remove();

                targetId.parent().parent().hide();
                categoryId.parent().parent().hide();
                moduleId.parent().parent().hide();

                if (!$(elem).val()) {
                    projectId.parent().parent().hide();
                } else {
                    projectId.parent().parent().show();
                    system.control.loadObjects($(elem).val(), "project-list", function (data) {
                        if (data && data.objects) {
                            var options = [];

                            for (var i = 0; i < data.objects.length; i++) {
                                var option = data.objects[i];

                                options.push($("<option></option>")
                                    .val(option.id)
                                    .html(option.name)
                                );
                            }

                            projectId.append(options);
                        }
                    });
                }
            } else if (id == "ProjectPlannerEditForm_projectId") {
                targetId.find("option:not(:first)").remove();
                categoryId.find("option:not(:first)").remove();
                moduleId.find("option:not(:first)").remove();

                categoryId.parent().parent().hide();

                if (!$(elem).val()) {
                    targetId.parent().parent().hide();
                    moduleId.parent().parent().hide();
                } else {
                    system.control.loadObjects($(elem).val(), "target-list", function (data) {
                        if (data && data.objects) {
                            targetId.parent().parent().show();
                            moduleId.parent().parent().hide();

                            var options = [];

                            for (var i = 0; i < data.objects.length; i++) {
                                var option = data.objects[i];

                                options.push($("<option></option>")
                                    .val(option.id)
                                    .html(option.host)
                                );
                            }

                            targetId.append(options);
                        }
                    });
                }
            } else if (id == "ProjectPlannerEditForm_targetId") {
                categoryId.find("option:not(:first)").remove();
                moduleId.find("option:not(:first)").remove();

                if (!$(elem).val()) {
                    categoryId.parent().parent().hide();
                } else {
                    system.control.loadObjects($(elem).val(), "target-category-list", function (data) {
                        if (data && data.objects) {
                            categoryId.parent().parent().show();

                            var options = [];

                            for (var i = 0; i < data.objects.length; i++) {
                                var option = data.objects[i];

                                options.push($("<option></option>")
                                    .val(option.id)
                                    .html(option.name)
                                );
                            }

                            categoryId.append(options);
                        }
                    });
                }
            } else if ((id == "ProjectPlannerEditForm_categoryId" || id == "ProjectPlannerEditForm_moduleId") && $(elem).val()) {
                $("#add-button").prop("disabled", false);
            }
        };

        /**
         * Submit add form
         */
        this.addFormSubmit = function () {
            $("#object-selection-form").submit();
        };

        /**
         * Control.
         */
        this._control = function(operation, id, url, callback) {
            $.ajax({
                dataType: "json",
                url: url,
                timeout: system.ajaxTimeout,
                type: "POST",

                data: {
                    "EntryControlForm[operation]": operation,
                    "EntryControlForm[id]": id,
                    "YII_CSRF_TOKEN": system.csrf
                },

                success : function (data, textStatus) {
                    $(".loader-image").hide();

                    if (data.status == "error") {
                        system.addAlert("error", data.errorText);
                        return;
                    }

                    data = data.data;

                    if (operation == "delete") {
                        if (callback) {
                            callback();
                        }
                    }
                },

                error : function(jqXHR, textStatus, e) {
                    $(".loader-image").hide();
                    system.addAlert("error", system.translate("Request failed, please try again."));
                },

                beforeSend : function (jqXHR, settings) {
                    $(".loader-image").show();
                }
            });
        };

        /**
         * Delete plan.
         */
        this.del = function (id, url, callback) {
            if (confirm(system.translate("Are you sure that you want to delete this object?"))) {
                _planner._control("delete", id, url, callback);
            }
        };
    };

    /**
     * Time tracker object
     */
    this.timeTracker = new function () {
        var _timeTracker = this;

        /**
         * Expand project.
         */
        this.expandProject = function (id) {
            $("div.project-body[data-id=" + id + "]").slideDown("slow");
        };

        /**
         * Collapse project.
         */
        this.collapseProject = function (id) {
            $("div.project-body[data-id=" + id + "]").slideUp("slow");
        };

        /**
         * Toggle project.
         */
        this.toggleProject = function (id) {
            if ($("div.project-body[data-id=" + id + "]").is(":visible")) {
                _timeTracker.collapseProject(id);
            } else {
                _timeTracker.expandProject(id);
            }
        };
    };

    /**
     * Backups
     */
    this.backup = new function () {
        var _backup = this;

        this.checkTimeout = 2000;

        /**
         * Backup request
         */
        this.create = function (url, callback) {
            $.ajax({
                dataType : 'json',
                url      : url,
                timeout  : system.ajaxTimeout,
                type     : 'POST',

                data : {
                    'YII_CSRF_TOKEN' : system.csrf
                },

                success : function (data, textStatus) {
                    $('.loader-image').hide();

                    if (data.status == 'error')
                    {
                        system.addAlert('error', data.errorText);

                        if (callback) {
                            callback();
                        }

                        return;
                    }

                    if (callback) {
                        callback(data.data);
                    }
                },

                error : function(jqXHR, textStatus, e) {
                    $('.loader-image').hide();
                    system.addAlert('error', system.translate('Request failed, please try again.'));

                    if (callback) {
                        callback();
                    }
                },

                beforeSend : function (jqXHR, settings) {
                    $('.loader-image').show();
                    $('#backup').prop("disabled", true);
                }
            });
        };

        /**
         * Check system is backing up || restoring
         */
        this.check = function (url, type) {
            $.ajax({
                dataType : 'json',
                url      : url,
                timeout  : system.ajaxTimeout,
                type     : 'POST',

                data : {
                    'YII_CSRF_TOKEN' : system.csrf
                },

                success : function (data, textStatus) {
                    var status, message;

                    $('.loader-image').hide();

                    if (data.status == 'error')
                    {
                        system.addAlert('error', data.errorText);
                        return;
                    }

                    if (type == "backup") {
                        status = data.data.backingup;

                        if (!status) {
                            system.addAlert("success", "Backup created.");
                            $('#backup').prop("disabled", false);

                            setTimeout(function () {
                                location.reload();
                            }, 2000);

                            return;
                        }
                    } else if (type == "restore") {
                        status = data.data.restoring;
                        message = data.data.message;

                        if (!status) {
                            if (message) {
                                system.addAlert("error", message);
                            } else {
                                system.addAlert("success", "System restored.");
                            }

                            $('#backup, #restore').prop('disabled', false);

                            return;
                        }
                    }

                    setTimeout(function () {
                        _backup.check(url, type);
                    }, _backup.checkTimeout);
                },

                error : function(jqXHR, textStatus, e) {
                    $('.loader-image').hide();
                    system.addAlert('error', system.translate('Request failed, please try again.'));
                },

                beforeSend : function (jqXHR, settings) {
                    $('.loader-image').show();
                }
            });
        };

        /**
         * Restore from selected backup in list
         * @param id
         */
        this.restore = function (id) {
            if (confirm(system.translate('Are you sure that you want to restore system from this backup?'))) {
                $('#backup').prop("disabled", true);
                system.control._control(id, 'restore');
            }
        };
    };

    /**
     * Checklist Template object
     */
    this.checklisttemplate = new function () {
        var _checklisttemplate = this;
        /**
         * Load category checks list
         * @param url
         * @param id
         */
        this.loadChecks = function (id) {
            $('.check-list').empty();
            $('.check-list-wrapper').addClass('hide');
            _checklisttemplate.toggleChecklistButton();

            if (id != '0') {
                system.control.loadObjects(id, "category-check-list", function (data) {
                    if (data && data.objects.length) {
                        $.each(data.objects, function (key, value) {
                            $('.check-list').
                                append(
                                    $('<label>')
                                        .addClass('checkbox')
                                        .text(value.name)
                                        .append(
                                            $('<input>')
                                                .attr('type', 'checkbox')
                                                .attr('id', 'ChecklistTemplateCheckCategoryEditForm_checkIds_' + value.id)
                                                .attr('name', 'ChecklistTemplateCheckCategoryEditForm[checkIds][]')
                                                .change(_checklisttemplate.toggleChecklistButton)
                                                .val(value.id)
                                        )
                                );
                        });

                        $('.check-list-wrapper').removeClass('hide');
                    } else {
                        system.addAlert("error", "Category has no checks.");
                    }
                });
            }
        };

        /**
         * Disable save button if no checkbox marked
         */
        this.toggleChecklistButton = function () {
            if ($('.check-list input[type=checkbox]:checked').length) {
                $('.btn-submit').prop('disabled', false);
            } else {
                $('.btn-submit').prop('disabled', true);
            }
        };
    };

    /**
     * Nessus report mapping
     * @type {nessusMapping}
     */
    this.nessusMapping = new function () {
        var _nessusMapping = this;

        /**
         * Item id on binding check through search popup
         * @type {null}
         */
        this.currentItemId = null;

        /**
         * jQuery selectors
         * @type {{table: string}}
         */
        this.selectors = {
            table: ".nessus-mapping",
            items: ".nessus-mapping .nessus-mapping-vuln",
            filters: {
                hosts: ".filter.nessus-hosts .nessus-host input:checked",
                ratings: ".filter.nessus-ratings .nessus-rating input:checked",
                container: ".nessus-mapping-filters"
            }
        };

        /**
         * Show check search popup
         * @param itemId
         */
        this.showCheckSearchPopup = function (itemId) {
            _nessusMapping.currentItemId = itemId;

            admin.showCheckSearchPopup();
        };

        /**
         * Forbid nessus mapping toggle
         * @param vulnId
         * @param disabled
         */
        this.disableItem = function (vulnId, disabled) {
            var _disabled = !!disabled;

            $("[data-item-id=" + vulnId + "]")
                .find("input, select")
                .prop("disabled", _disabled);
        };

        /**
         * Save nessus mapping item
         * @param vulnId
         */
        this.saveItem = function (vulnId) {
            var item = $("[data-item-id=" + vulnId + "]");

            var active = +item.find(".active > input").is(":checked");
            var checkId = item.data("check-id");
            var resultId = parseInt(item.find(".mapped-result > select").val()) || null;
            var solutionId = parseInt(item.find(".mapped-solution > select").val()) || null;
            var insertTitle = +item.find(".insert-nessus-title > input").is(":checked");
            var rating = parseInt(item.find(".rating > select").val()) || null;

            var data = {
                "YII_CSRF_TOKEN": system.csrf,
                "NessusMappingVulnUpdateForm[vulnId]": vulnId,
                "NessusMappingVulnUpdateForm[active]": active
            };

            if (checkId) {
                data["NessusMappingVulnUpdateForm[checkId]"] = checkId;
            }

            if (insertTitle) {
                data["NessusMappingVulnUpdateForm[insertTitle]"] = insertTitle;
            }

            if (resultId) {
                data["NessusMappingVulnUpdateForm[resultId]"] = resultId;
            }

            if (solutionId) {
                data["NessusMappingVulnUpdateForm[solutionId]"] = solutionId;
            }

            if (rating) {
                data["NessusMappingVulnUpdateForm[rating]"] = rating;
            }

            $.ajax({
                dataType: "json",
                url: $(item).data("update-url"),
                timeout: system.ajaxTimeout,
                type: "POST",
                data: data,

                success: function (data, textStatus) {
                    $(".loader-image").hide();

                    if (data.status == "error") {
                        system.addAlert("error", data.errorText);
                        return;
                    }

                    item.replaceWith(data.data.item_rendered);
                    admin.hideCheckSearchPopup();
                },

                error: function(jqXHR, textStatus, e) {
                    $(".loader-image").hide();
                    system.addAlert("error", system.translate("Request failed, please try again."));
                },

                beforeSend: function (jqXHR, settings) {
                    _nessusMapping.disableItem(vulnId, true);
                    $(".loader-image").show();
                }
            });
        };

        /**
         * Update vuln item
         * @param checkId
         */
        this.updateItem = function (checkId) {
            var item = $("[data-item-id=" + _nessusMapping.currentItemId + "]");
            item.data("check-id", checkId);

            _nessusMapping.saveItem(_nessusMapping.currentItemId);
        };

        /**
         * Get filter data
         * @param mappingId
         * @returns {{YII_CSRF_TOKEN: string, NessusMappingVulnFilterForm[mappingId]: *, NessusMappingVulnFilterForm[hosts], NessusMappingVulnFilterForm[ratings]}}
         */
        this.getFilterData = function (mappingId) {
            var hosts = $(_nessusMapping.selectors.filters.hosts);
            var ratings = $(_nessusMapping.selectors.filters.ratings);

            var hostIds = [];
            var ratingIds = [];

            $.each(hosts, function (key, value) {
                hostIds.push($(value).val());
            });

            $.each(ratings, function (key, value) {
                ratingIds.push($(value).val());
            });

            return {
                "YII_CSRF_TOKEN": system.csrf,
                "NessusMappingVulnFilterForm[mappingId]": mappingId,
                "NessusMappingVulnFilterForm[hosts]": JSON.stringify(hostIds),
                "NessusMappingVulnFilterForm[ratings]": JSON.stringify(ratingIds)
            };
        };

        /**
         * Filter mapping items
         * @param mappingId
         */
        this.filterItems = function (mappingId) {
            var data = _nessusMapping.getFilterData(mappingId);

            $.ajax({
                dataType: "json",
                url: $(_nessusMapping.selectors.filters.container).data("filter-url"),
                timeout: system.ajaxTimeout,
                type: "POST",
                data: data,

                success: function (data, textStatus) {
                    $(".loader-image").hide();

                    if (data.status == "error") {
                        system.addAlert("error", data.errorText);
                        return;
                    }

                    $(_nessusMapping.selectors.table).replaceWith(data.data.table_rendered);
                },

                error: function(jqXHR, textStatus, e) {
                    $(".loader-image").hide();
                    system.addAlert("error", system.translate("Request failed, please try again."));
                },

                beforeSend: function (jqXHR, settings) {
                    $(".loader-image").show();
                }
            });
        };

        /**
         * Select all checkbox changed
         * @param enable
         */
        this.selectAll = function (enable) {
            var items = $(_nessusMapping.selectors.items);
            var ids = [];

            $.each(items, function (key, value) {
                ids.push($(value).data("item-id"));
            });

            var data = {
                "YII_CSRF_TOKEN": system.csrf,
                "NessusMappingVulnActivateForm[mappingIds]": JSON.stringify(ids),
                "NessusMappingVulnActivateForm[activate]": +enable
            };

            $.ajax({
                dataType: "json",
                url: $(_nessusMapping.selectors.table).data("activate-url"),
                timeout: system.ajaxTimeout,
                type: "POST",
                data: data,

                success: function (data, textStatus) {
                    $(".loader-image").hide();

                    if (data.status == "error") {
                        system.addAlert("error", data.errorText);
                        return;
                    }

                    $(_nessusMapping.selectors.items).find("td.active input[type=checkbox]").prop("checked", enable);
                },

                error: function(jqXHR, textStatus, e) {
                    $(".loader-image").hide();
                    system.addAlert("error", system.translate("Request failed, please try again."));
                },

                beforeSend: function (jqXHR, settings) {
                    $(".loader-image").show();
                }
            });
        };
    };

    /**
     * Show check search popup
     */
    this.showCheckSearchPopup = function () {
        var modal = $("#issue-check-select-dialog");
        var list = modal.find("ul.check-list");
        var field = modal.find(".issue-search-query");

        field.val("");
        list.empty();

        modal.find(".no-search-result").hide();
        modal.modal();
    };

    /**
     * Hide check search popup
     */
    this.hideCheckSearchPopup = function () {
        var modal = $("#issue-check-select-dialog");

        if (modal.is(":visible")) {
            modal.modal("toggle");
        }
    };
}

var admin = new Admin();
