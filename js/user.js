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
        this.updateIteration = 0;
        this.ckeditors = [];

        /**
         * Update status of running checks.
         */
        this.update = function (url) {
            var i, k;

            if (this.runningChecks.length > 0)
                _check.updateIteration++;

            for (i = 0; i < _check.runningChecks.length; i++) {
                var check, headingRow, minutes, seconds, time;

                check = _check.runningChecks[i];
                headingRow = $('div.check-header[data-type=check][data-id=' + check.id + ']');

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

                        if (data.status == 'error') {
                            system.addAlert('error', data.errorText);
                            return;
                        }

                        data = data.data;

                        if (data.checks) {
                            for (i = 0; i < data.checks.length; i++) {
                                var check, checkIdx;

                                check = data.checks[i];

                                $('#TargetCheckEditForm_' + check.id + '_result').val(check.result);
                                $('div.check-form[data-type=check][data-id="' + check.id + '"] div.table-result').html(check.tableResult);

                                if (check.startedText) {
                                    $('div.check-form[data-type=check][data-id="' + check.id + '"] .automated-info-block')
                                        .html(check.startedText)
                                        .show();
                                } else {
                                    $('div.check-form[data-type=check][data-id="' + check.id + '"] .automated-info-block').hide();
                                }

                                // attachments
                                if (check.attachments.length > 0) {
                                    var table, tbody;

                                    table = $('div.check-form[data-type=check][data-id="' + check.id + '"] .attachment-list');
                                    tbody = table.find("tbody");
                                    tbody.find("tr").remove();

                                    for (k = 0; k < check.attachments.length; k++) {
                                        var tr, attachment;

                                        attachment = check.attachments[k];
                                        tr = $("<tr>");

                                        tr.attr("data-path", attachment.path);
                                        tr.attr("data-control-url", check.attachmentControlUrl);

                                        tr.append(
                                            $("<td>")
                                                .addClass("name")
                                                .append(
                                                    $("<a>")
                                                        .attr("href", attachment.url)
                                                        .html(attachment.name)
                                                )
                                        );

                                        tr.append(
                                            $("<td>")
                                                .addClass("actions")
                                                .append(
                                                    $("<a>")
                                                        .attr("href", "#del")
                                                        .attr("title", system.translate("Delete"))
                                                        .html('<i class="icon icon-remove"></i>')
                                                        .click(function () {
                                                            user.check.delAttachment(attachment.path);
                                                        })
                                                )
                                        );

                                        tbody.append(tr);
                                    }

                                    table.show();
                                }

                                for (k = 0; k < _check.runningChecks.length; k++) {
                                    var innerCheck = _check.runningChecks[k];

                                    if (innerCheck.id == check.id) {
                                        checkIdx = k;
                                        innerCheck.time = check.time;

                                        break;
                                    }
                                }

                                if (check.finished) {
                                    _check.runningChecks.splice(checkIdx, 1);

                                    var headerRow = $('div.check-header[data-type=check][data-id=' + check.id + ']');

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
                        system.addAlert('error', system.translate('Request failed, please try again.'));
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
         * Expand custom template.
         * @param id
         * @param callback
         */
        this.expandCustomTemplate = function (id, callback) {
            var selector = $("div.check-form[data-type=custom-template][data-id=" + id + "]");

            selector.slideDown("slow", undefined, function () {
                if (callback) {
                    callback();
                }
            });
        };

        /**
         * Collapse custom template.
         * @param id
         * @param callback
         */
        this.collapseCustomTemplate = function (id, callback) {
            var selector = $("div.check-form[data-type=custom-template][data-id=" + id + "]");

            selector.slideUp("slow", undefined, function () {
                if (callback) {
                    callback();
                }
            });
        };

        /**
         * Toggle custom template.
         * @param id
         */
        this.toggleCustomTemplate = function (id) {
            var visible = $("div.check-form[data-type=custom-template][data-id=" + id + "]").is(":visible");

            if (visible) {
                _check.collapseCustomTemplate(id, null);
            } else {
                _check.expandCustomTemplate(id, null);
            }
        };

        /**
         * Expand custom check.
         * @param id
         * @param callback
         */
        this.expandCustomCheck = function (id, callback) {
            var selector = $("div.check-form[data-type=custom-check][data-id=" + id + "]");

            selector.slideDown("slow", undefined, function () {
                if (callback) {
                    callback();
                }
            });
        };

        /**
         * Collapse custom check.
         * @param id
         * @param callback
         */
        this.collapseCustomCheck = function (id, callback) {
            var selector = $("div.check-form[data-type=custom-check][data-id=" + id + "]");

            selector.slideUp("slow", undefined, function () {
                if (callback) {
                    callback();
                }
            });
        };

        /**
         * Toggle custom template.
         * @param id
         */
        this.toggleCustomCheck = function (id) {
            var visible = $("div.check-form[data-type=custom-check][data-id=" + id + "]").is(":visible");

            if (visible) {
                _check.collapseCustomCheck(id, null);
            } else {
                _check.expandCustomCheck(id, null);
            }
        };

        /**
         * Expand.
         * @param id
         * @param callback
         */
        this.expand = function (id, callback) {
            var header, form, url;

            header = $("div.check-header[data-type=check][data-id=" + id + "]");
            form = $("div.check-form[data-type=check][data-id=" + id + "]");

            if (header.data("limited")) {
                form.slideDown("slow", function () {
                    if (callback) {
                        callback();
                    }
                });

                return;
            }

            url = header.data("check-url");

            $.ajax({
                dataType: "json",
                url: url,
                timeout: system.ajaxTimeout,
                type: "POST",

                data : {
                    "YII_CSRF_TOKEN": system.csrf
                },

                success : function (data, textStatus) {
                    $(".loader-image").hide();
                    $("a[data-type=check-link][data-id=" + id + "]").removeClass("disabled");

                    if (data.status == "error") {
                        system.addAlert("error", data.errorText);
                        return;
                    }

                    form.html(data.data.html);
                    $(".wysiwyg", form).ckeditor();
                    _check.initTargetCheckAttachmentUploadForms(id);
                    _check.initAutosave(id);

                    form.slideDown("slow", function () {
                        if (callback) {
                            callback();
                        }
                    });
                },

                error : function(jqXHR, textStatus, e) {
                    $(".loader-image").hide();
                    $("a[data-type=check-link][data-id=" + id + "]").removeClass("disabled");
                    system.addAlert("error", system.translate("Request failed, please try again."));
                },

                beforeSend : function (jqXHR, settings) {
                    $(".loader-image").show();
                    $("a[data-type=check-link][data-id=" + id + "]").addClass("disabled");
                }
            });
        };

        /**
         * Collapse.
         * @param id
         * @param callback
         */
        this.collapse = function (id, callback) {
            var selector = $("div.check-form[data-type=check][data-id=" + id + "]");
            
            selector.slideUp("slow", undefined, function () {
                if (!selector.data("limited")) {
                    $("div.check-form[data-type=check][data-id=" + id + "]").html("");
                }

                if (callback) {
                    callback();
                }
            });
        };

        /**
         * Toggle check
         * @param id
         */
        this.toggle = function (id) {
            if ($("a[data-type=check-link][data-id=" + id + "]").hasClass("disabled")) {
                return;
            }

            var visible = $("div.check-form[data-type=check][data-id=" + id + "]").is(":visible");

            if (visible) {
                _check.collapse(id, null);
            } else {
                _check.expand(id, null);
            }
        };

        /**
         * Expand control.
         */
        this.expandControl = function (id, callback) {
            var url = $("div[data-id=" + id + "][data-type=control]").data("checklist-url");

            $.ajax({
                dataType: "json",
                url: url,
                timeout: system.ajaxTimeout,
                type: "POST",

                data : {
                    "YII_CSRF_TOKEN": system.csrf
                },

                success : function (data, textStatus) {
                    $(".loader-image").hide();
                    $("a[data-type=control-link][data-id=" + id + "]").removeClass("disabled");

                    if (data.status == "error") {
                        system.addAlert("error", data.errorText);
                        return;
                    }

                    var body = $("div.control-body[data-id=" + id + "]");

                    body.append(data.data.html);
                    body.slideDown("slow", function () {
                        if (callback) {
                            callback();
                        }
                    });
                },

                error : function(jqXHR, textStatus, e) {
                    $(".loader-image").hide();
                    $("a[data-type=control-link][data-id=" + id + "]").removeClass("disabled");
                    system.addAlert("error", system.translate("Request failed, please try again."));
                },

                beforeSend : function (jqXHR, settings) {
                    $(".loader-image").show();
                    $("a[data-type=control-link][data-id=" + id + "]").addClass("disabled");
                }
            });
        };

        /**
         * Collapse control.
         */
        this.collapseControl = function (id, callback) {
            $("div.control-body[data-id=" + id + "]").slideUp("slow", function () {
                $("div.control-body[data-id=" + id + "] div[data-type=check]").remove();

                if (callback) {
                    callback();
                }
            });
        };

        /**
         * Toggle control.
         */
        this.toggleControl = function (id) {
            if ($("a[data-type=control-link][data-id=" + id + "]").hasClass("disabled")) {
                return;
            }

            if ($("div.control-body[data-id=" + id + "]").is(":visible")) {
                _check.collapseControl(id, null);
            } else {
                _check.expandControl(id, null);
            }
        };

        /**
         * Expand solution.
         */
        this.expandSolution = function (id) {
            $('span.solution-control[data-id=' + id + ']').html('<a href="#solution" onclick="user.check.collapseSolution(\'' + id + '\');"><i class="icon-chevron-up"></i></a>');
            $('div.solution-content[data-id=' + id + ']').slideDown('slow');
        };

        /**
         * Collapse solution.
         */
        this.collapseSolution = function (id) {
            $('span.solution-control[data-id=' + id + ']').html('<a href="#solution" onclick="user.check.expandSolution(\'' + id + '\');"><i class="icon-chevron-down"></i></a>');
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

            if (result.match(/Problem:/ig)) {
                result = result + "\n\nTechnical Details:\n\n@cut\n";
            }

            textarea.val(result + '\n' + textarea.val());
            textarea.trigger('change');
        };

        /**
         * Set loading.
         */
        this.setLoading = function (id, custom) {
            var row = custom ?
                $('div.check-form[data-type=custom-check][data-id=' + id + ']') :
                $('div.check-form[data-type=check][data-id="' + id + '"]');

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
        this.setLoaded = function (id, custom) {
            var row = custom ?
                $('div.check-form[data-type=custom-check][data-id=' + id + ']') :
                $('div.check-form[data-type=check][data-id="' + id + '"]');

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
            var i, row, textareas, texts, checkboxes, radios, override, protocol, port, result, solutions, resultTitle, saveResult,
                attachments, rating, data, solution, solutionTitle, saveSolution, poc, links, scripts, timeouts;

            row = $('div.check-form[data-type=check][data-id="' + id + '"]');

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

            radios = $('input[type="radio"][name^="TargetCheckEditForm_' + id + '[inputs]"]:checked', row).map(
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
            resultTitle = $('input[name="TargetCheckEditForm_' + id + '[resultTitle]"]', row).val();
            result = _check.ckeditors["TargetCheckEditForm_" + id + "_result"] ?
                _check.ckeditors["TargetCheckEditForm_" + id + "_result"].getData() :
                $('textarea[name="TargetCheckEditForm_' + id + '[result]"]').val();

            scripts = $('input[name^="TargetCheckEditForm_' + id + '[scripts]"]', row).map(
                function () {
                    return {
                        name: $(this).attr('name'),
                        value: JSON.stringify({
                            id : $(this).data("id"),
                            start : $(this).is(":checked")
                        })
                    }
                }
            ).get();

            timeouts = $('input[name^="TargetCheckEditForm_' + id + '[timeouts]"]', row).map(
                function () {
                    return {
                        name : $(this).attr('name'),
                        value : JSON.stringify({
                            script_id : $(this).data("script-id"),
                            timeout : $(this).val()
                        })
                    }
                }
            );
                
            saveResult = $('input[name="TargetCheckEditForm_' + id + '[saveResult]"]', row).is(":checked");

            if ($('textarea[name="TargetCheckEditForm_' + id + '[poc]"]', row)) {
                poc = $('textarea[name="TargetCheckEditForm_' + id + '[poc]"]', row).val();
            }

            if ($('textarea[name="TargetCheckEditForm_' + id + '[links]"]', row)) {
                links = $('textarea[name="TargetCheckEditForm_' + id + '[links]"]', row).val();
            }

            solutions = $('input[name^="TargetCheckEditForm_' + id + '[solutions]"]:checked', row).map(
                function () {
                    return {
                        name: $(this).attr('name'),
                        value: $(this).val()
                    }
                }
            ).get();

            for (i = 0; i < solutions.length; i++) {
                if (solutions[i].value == system.constants.TargetCheckEditForm.CUSTOM_SOLUTION_IDENTIFIER) {
                    solution = $('textarea[name="TargetCheckEditForm_' + id + '[solution]"]', row).val();
                    solutionTitle = $('input[name="TargetCheckEditForm_' + id + '[solutionTitle]"]', row).val();
                    saveSolution = $('input[name="TargetCheckEditForm_' + id + '[saveSolution]"]', row).is(":checked");
                }
            }

            attachments = $('input[name^="TargetCheckEditForm_' + id + '[attachmentTitles]"]', row).map(
                function () {
                    return {
                        name : $(this).attr('name'),
                        value: JSON.stringify({ path : $(this).data('path'), 'title' : $(this).val().trim() })
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

            if (poc == undefined) {
                poc = "";
            }

            if (links == undefined) {
                links = "";
            }

            data = [];

            data.push({ name : 'TargetCheckEditForm_' + id + '[overrideTarget]', value : override });
            data.push({ name : 'TargetCheckEditForm_' + id + '[protocol]',       value : protocol });
            data.push({ name : 'TargetCheckEditForm_' + id + '[port]',           value : port     });
            data.push({ name : 'TargetCheckEditForm_' + id + '[result]',         value : result   });
            data.push({ name : 'TargetCheckEditForm_' + id + '[resultTitle]',    value : resultTitle   });
            data.push({ name : 'TargetCheckEditForm_' + id + '[rating]',         value : rating   });

            data.push({name: "TargetCheckEditForm_" + id + "[solution]", value: solution ? solution : ""});
            data.push({name: "TargetCheckEditForm_" + id + "[solutionTitle]", value: solutionTitle ? solutionTitle : ""});
            data.push({name: "TargetCheckEditForm_" + id + "[poc]", value: poc});
            data.push({name: "TargetCheckEditForm_" + id + "[links]", value: links});
            data.push({ name: "TargetCheckEditForm_" + id + "[tableResult]", value: _check.buildTableResult(row) });

            if (saveSolution) {
                data.push({name: "TargetCheckEditForm_" + id + "[saveSolution]", value: "1"});
            }

            if (saveResult) {
                data.push({name: "TargetCheckEditForm_" + id + "[saveResult]", value: "1"});
            }

            for (i = 0; i < texts.length; i++)
                data.push(texts[i]);

            for (i = 0; i < textareas.length; i++)
                data.push(textareas[i]);

            for (i = 0; i < checkboxes.length; i++)
                data.push(checkboxes[i]);

            for (i = 0; i < radios.length; i++)
                data.push(radios[i]);

            for (i = 0; i < solutions.length; i++) {
                data.push(solutions[i]);
            }

            for (i = 0; i < attachments.length; i++) {
                data.push(attachments[i]);
            }

            for (i = 0; i < scripts.length; i++) {
                data.push(scripts[i]);
            }

            for (i = 0; i < timeouts.length; i++) {
                data.push(timeouts[i]);
            }

            return data;
        };

        /**
         * Save the check.
         */
        this.save = function (id, goToNext) {
            var row, headerRow, data, url, nextRow, rating, check, targetCheck;

            headerRow = $('div.check-header[data-type=check][data-id="' + id + '"]');
            row = $('div.check-form[data-type=check][data-id="' + id + '"]');
            url = row.data('save-url');

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
                        system.addAlert('error', data.errorText);
                        return;
                    }

                    data = data.data;

                    targetCheck = data.targetCheck;
                    check = targetCheck.check;

                    if (data.rating != undefined && data.rating != null) {
                        $('td.status', headerRow).html(
                            '<span class="label ' +
                            (ratings[data.rating].classN ? ratings[data.rating].classN : '') + '">' +
                            ratings[data.rating].text + '</span>'
                        );
                    } else {
                        $('td.status', headerRow).html('');
                    }

                    $('i.icon-refresh', headerRow).parent().remove();
                    $('td.actions', headerRow).append(
                        '<a href="#reset" title="' + system.translate('Reset') + '" onclick="user.check.reset(' + id +
                        ');"><i class="icon icon-refresh"></i></a>'
                    );

                    if (data.newSolution) {
                        var solution = data.newSolution;

                        row.find('ul.solutions').append(
                            $("<li>")
                                .append(
                                    $("<div>")
                                        .addClass("solution-header")
                                        .append(
                                            $("<label>")
                                                .addClass(solution.multipleSolutions ? "checkbox" : "radio")
                                                .append(
                                                    $("<input>")
                                                        .attr("type", solution.multipleSolutions ? "checkbox" : "radio")
                                                        .attr("name", "TargetCheckEditForm_" + id + "[solutions][]")
                                                        .val(solution.id)
                                                        .prop("checked", true)
                                                )
                                                .append(solution.title)
                                                .append(
                                                    $("<span>")
                                                        .addClass("solution-control")
                                                        .attr("data-id", solution.id)
                                                        .append(
                                                            $("<a>")
                                                                .attr("href", "#solution")
                                                                .click(function () {
                                                                    _check.expandSolution(solution.id);
                                                                })
                                                                .append(
                                                                    $("<i>")
                                                                        .addClass("icon-chevron-down")
                                                                )
                                                        )
                                                )
                                        )
                                )
                                .append(
                                    $("<div></div>")
                                        .addClass("solution-content")
                                        .addClass("hide")
                                        .attr("data-id", solution.id)
                                        .html(solution.solution)
                                )
                        );

                        $('input[name="TargetCheckEditForm_' + id + '[solutions][]"].custom-solution').prop("checked", false);
                        $('textarea[name="TargetCheckEditForm_' + id + '[solution]"]').data("wysihtml5").editor.clear();
                        $('input[name="TargetCheckEditForm_' + id + '[solutionTitle]"]').val("");
                        $('input[name="TargetCheckEditForm_' + id + '[saveSolution]"]').prop("checked", false);
                        _check.collapseSolution(id + "-" + system.constants.TargetCheckEditForm.CUSTOM_SOLUTION_IDENTIFIER);
                    }

                    if (data.newResult) {
                        var result = data.newResult;
                        var resultsList = row.find('ul.results');

                        resultsList.append(
                            $("<li>")
                                .append(
                                    $("<div>")
                                        .addClass("result-header")
                                        .append(
                                            $('<a>')
                                                .attr('href', '#insert')
                                                .addClass('result-title')
                                                .click(function () {
                                                    user.check.insertResult(id, result.result)
                                                })
                                                .text(result.title),
                                            $('<span>')
                                                .addClass('result-control')
                                                .attr('data-id', result.id)
                                                .append(
                                                    $('<a>')
                                                        .attr('href', '#result')
                                                        .click(function () {
                                                            user.check.expandResult(result.id);
                                                        })
                                                        .append(
                                                            $('<i>')
                                                                .addClass('icon-chevron-down')
                                                        )
                                                )
                                        ),
                                    $('<div>')
                                        .addClass('result-content')
                                        .addClass('hide')
                                        .attr('data-id', result.id)
                                        .text(result.result)
                                )
                        );

                        $('input[name="TargetCheckEditForm_' + id + '[saveResult]"]').prop("checked", false);
                        $('input[name="TargetCheckEditForm_' + id + '[resultTitle]"]').val("").hide();

                        if (resultsList.parents('tr').hasClass('hide')) {
                            resultsList.parents('tr').removeClass('hide');
                        }
                    }

                    if (goToNext) {
                        _check.collapse(id, function () {
                            nextRow = $('div.check-form[data-type=check][data-id="' + id + '"] + div + div.check-form');

                            if (!nextRow.length)
                                nextRow = $('div.check-form[data-type=check][data-id="' + id + '"]').parent().next().next().find('div.check-form:first');

                            if (nextRow.length) {
                                _check.expand(nextRow.data('id'), function () {
                                    location.href = '#check-' + nextRow.data('id');
                                });
                            }
                        });
                    }
                },

                error : function(jqXHR, textStatus, e) {
                    _check.setLoaded(id);
                    system.addAlert('error', system.translate('Request failed, please try again.'));
                },

                beforeSend : function (jqXHR, settings) {
                    _check.setLoading(id);
                }
            });
        };

        /**
         * Get custom check data in array.
         * @param id
         */
        this.getCustomData = function (id) {
            var i, row, name, background, question, result, rating, data, solution, solutionTitle, createCheck, poc, links, attachments;

            row = $('div.check-form[data-type=custom-check][data-id=' + id + ']');

            name = $('input[name="TargetCustomCheckEditForm_' + id + '[name]"]', row).val();
            background = $('textarea[name="TargetCustomCheckEditForm_' + id + '[backgroundInfo]"]', row).val();
            question = $('textarea[name="TargetCustomCheckEditForm_' + id + '[question]"]', row).val();
            rating = $('input[name="TargetCustomCheckEditForm_' + id + '[rating]"]:checked', row).val();
            solutionTitle = $('input[name="TargetCustomCheckEditForm_' + id + '[solutionTitle]"]', row).val();
            solution = $('textarea[name="TargetCustomCheckEditForm_' + id + '[solution]"]', row).val();
            createCheck = $('input[name="TargetCustomCheckEditForm_' + id + '[createCheck]"]', row).is(":checked");

            if ($('textarea[name="TargetCustomCheckEditForm_' + id + '[poc]"]', row)) {
                poc = $('textarea[name="TargetCustomCheckEditForm_' + id + '[poc]"]', row).val();
            }

            if ($('textarea[name="TargetCustomCheckEditForm_' + id + '[links]"]', row)) {
                links = $('textarea[name="TargetCustomCheckEditForm_' + id + '[links]"]', row).val();
            }

            result = _check.ckeditors["TargetCustomCheckEditForm_" + id + "_result"] ?
                _check.ckeditors["TargetCustomCheckEditForm_" + id + "_result"].getData() :
                $('textarea[name="TargetCustomCheckEditForm_' + id + '[result]"]').val();

            attachments = $('input[name^="TargetCustomCheckEditForm_' + id + '[attachmentTitles]"]', row).map(
                function () {
                    return {
                        name : 'TargetCustomCheckEditForm[attachmentTitles][]',
                        value: JSON.stringify({ path : $(this).data('path'), 'title' : $(this).val().trim() })
                    }
                }
            ).get();

            if (name == undefined) {
                name = "";
            }

            if (background == undefined) {
                background = "";
            }

            if (question == undefined) {
                question = "";
            }

            if (result == undefined) {
                result = "";
            }

            if (rating == undefined) {
                rating = "";
            }

            if (poc == undefined) {
                poc = "";
            }

            if (links == undefined) {
                links = "";
            }

            data = [];

            if (createCheck) {
                data.push({name: "TargetCustomCheckEditForm[createCheck]", value: "1"});
            }

            data.push({name: "TargetCustomCheckEditForm[id]", value: id});
            data.push({name: "TargetCustomCheckEditForm[name]", value: name});
            data.push({name: "TargetCustomCheckEditForm[backgroundInfo]", value: background});
            data.push({name: "TargetCustomCheckEditForm[question]", value: question});
            data.push({name: "TargetCustomCheckEditForm[result]", value: result});
            data.push({name: "TargetCustomCheckEditForm[rating]", value: rating});
            data.push({name: "TargetCustomCheckEditForm[solution]", value: solution ? solution : ""});
            data.push({name: "TargetCustomCheckEditForm[solutionTitle]", value: solutionTitle ? solutionTitle : ""});
            data.push({name: "TargetCustomCheckEditForm[poc]", value: poc});
            data.push({name: "TargetCustomCheckEditForm[links]", value: links});

            for (i = 0; i < attachments.length; i++) {
                data.push(attachments[i]);
            }

            return data;
        };

        /**
         * Save custom check.
         * @param id
         */
        this.saveCustom = function (id) {
            var row, headerRow, data, url;

            headerRow = $('div.check-header[data-type=custom-check][data-id=' + id + ']');
            row = $('div.check-form[data-type=custom-check][data-id=' + id + ']');
            url = row.data("save-url");
            data = _check.getCustomData(id);
            data.push({name: "YII_CSRF_TOKEN", value: system.csrf, id: id});

            $.ajax({
                dataType: "json",
                url: url,
                timeout: system.ajaxTimeout,
                type: "POST",
                data: data,

                success: function (data, textStatus) {
                    _check.setLoaded(id, true);

                    if (data.status == "error") {
                        system.addAlert("error", data.errorText);
                        return;
                    }

                    data = data.data;

                    if (data.createCheck) {
                        location.reload();
                    }

                    if (data.rating != undefined && data.rating != null) {
                        $("td.status", headerRow).html(
                            '<span class="label ' +
                            (ratings[data.rating].classN ? ratings[data.rating].classN : '') + '">' +
                            ratings[data.rating].text + '</span>'
                        );
                    } else {
                        $("td.status", headerRow).html("");
                    }
                },

                error: function(jqXHR, textStatus, e) {
                    _check.setLoaded(id, true);
                    system.addAlert("error", system.translate("Request failed, please try again."));
                },

                beforeSend: function (jqXHR, settings) {
                    _check.setLoading(id, true);
                }
            });
        };

        /**
         * Get custom template data in array.
         * @param id
         */
        this.getCustomTemplateData = function (id) {
            var i, row, name, background, question, result, rating, data, solution, solutionTitle, createCheck, poc, links;

            row = $('div.check-form[data-type=custom-template][data-id=' + id + ']');
            name = $('input[name="TargetCustomCheckTemplateEditForm_' + id + '[name]"]', row).val();
            background = $('textarea[name="TargetCustomCheckTemplateEditForm_' + id + '[backgroundInfo]"]', row).val();
            question = $('textarea[name="TargetCustomCheckTemplateEditForm_' + id + '[question]"]', row).val();
            rating = $('input[name="TargetCustomCheckTemplateEditForm_' + id + '[rating]"]:checked', row).val();
            solutionTitle = $('input[name="TargetCustomCheckTemplateEditForm_' + id + '[solutionTitle]"]', row).val();
            solution = $('textarea[name="TargetCustomCheckTemplateEditForm_' + id + '[solution]"]', row).val();
            createCheck = $('input[name="TargetCustomCheckTemplateEditForm_' + id + '[createCheck]"]', row).is(":checked");

            if ($('textarea[name="TargetCustomCheckTemplateEditForm_' + id + '[poc]"]', row)) {
                poc = $('textarea[name="TargetCustomCheckTemplateEditForm_' + id + '[poc]"]', row).val();
            }

            if ($('textarea[name="TargetCustomCheckTemplateEditForm_' + id + '[links]"]', row)) {
                links = $('textarea[name="TargetCustomCheckTemplateEditForm_' + id + '[links]"]', row).val();
            }

            result = _check.ckeditors["TargetCustomCheckTemplateEditForm_" + id + "_result"] ?
                _check.ckeditors["TargetCustomCheckTemplateEditForm_" + id + "_result"].getData() :
                $('textarea[name="TargetCustomCheckTemplateEditForm_' + id + '[result]"]').val();

            if (name == undefined) {
                name = "";
            }

            if (background == undefined) {
                background = "";
            }

            if (question == undefined) {
                question = "";
            }

            if (result == undefined) {
                result = "";
            }

            if (rating == undefined) {
                rating = "";
            }

            if (poc == undefined) {
                poc = "";
            }

            if (links == undefined) {
                links = "";
            }

            data = [];

            if (createCheck) {
                data.push({name: "TargetCustomCheckEditForm[createCheck]", value: "1"});
            }

            data.push({name: "TargetCustomCheckEditForm[controlId]", value: id});
            data.push({name: "TargetCustomCheckEditForm[name]", value: name});
            data.push({name: "TargetCustomCheckEditForm[backgroundInfo]", value: background});
            data.push({name: "TargetCustomCheckEditForm[question]", value: question});
            data.push({name: "TargetCustomCheckEditForm[result]", value: result});
            data.push({name: "TargetCustomCheckEditForm[rating]", value: rating});
            data.push({name: "TargetCustomCheckEditForm[solution]", value: solution ? solution : ""});
            data.push({name: "TargetCustomCheckEditForm[solutionTitle]", value: solutionTitle ? solutionTitle : ""});
            data.push({name: "TargetCustomCheckEditForm[poc]", value: poc});
            data.push({name: "TargetCustomCheckEditForm[links]", value: links});

            return data;
        };

        /**
         * Save custom template.
         * @param id
         */
        this.saveCustomTemplate = function (id) {
            var row, data, url;

            row = $('div.check-form[data-type=custom-template][data-id=' + id + ']');
            url = row.data("save-url");
            data = _check.getCustomTemplateData(id);
            data.push({
                name: "YII_CSRF_TOKEN",
                value: system.csrf
            });

            $.ajax({
                dataType: "json",
                url: url,
                timeout: system.ajaxTimeout,
                type: "POST",
                data: data,

                success: function (data, textStatus) {
                    _check.setLoaded(id, true);

                    if (data.status == "error") {
                        system.addAlert("error", data.errorText);
                        return;
                    }

                    location.reload();
                },

                error: function(jqXHR, textStatus, e) {
                    _check.setLoaded(id, true);
                    system.addAlert("error", system.translate("Request failed, please try again."));
                },

                beforeSend: function (jqXHR, settings) {
                    _check.setLoading(id, true);
                }
            });
        };

        /**
         * Autosave the check.
         */
        this.autosave = function (id) {
            var row, headerRow, data, url, nextRow;

            row = $('div.check-form[data-type=check][data-id="' + id + '"]');
            url = row.data('autosave-url');

            data = {
                "YII_CSRF_TOKEN": system.csrf,
                "TargetCheckEditForm[result]": $('textarea[name="TargetCheckEditForm_' + id + '[result]"]', row).val()
            };

            $.ajax({
                dataType: "json",
                url: url,
                timeout: system.ajaxTimeout,
                type: "POST",
                data: data,

                success : function (data, textStatus) {
                    _check.setLoaded(id);

                    if (data.status == "error") {
                        system.addAlert("error", data.errorText);
                        return;
                    }
                },

                error : function(jqXHR, textStatus, e) {
                    _check.setLoaded(id);
                    system.addAlert("error", system.translate("Request failed, please try again."));
                },

                beforeSend : function (jqXHR, settings) {
                    _check.setLoading(id);
                }
            });
        };

        /**
         * Init autosave function for check id
         */
        this.initAutosave = function (id) {
            $("div.check-form[data-type=check]").each(function () {
                var id = $(this).data("id");

                $('textarea[name="TargetCheckEditForm_' + id + '[result]"]', $(this)).on("paste", function () {
                    setTimeout(function () {
                        _check.autosave(id);
                    }, 100);
                });
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
                        system.addAlert('error', data.errorText);
                        return;
                    }

                    location.reload();
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
         * Initialize attachments form.
         */
        this.initTargetCheckAttachmentUploadForms = function (id) {
            $('div.check-form[data-type=check][data-id=' + id + '] input[name^="TargetCheckAttachmentUploadForm"]').each(function () {
                var url = $(this).data("upload-url"),
                    id = $(this).data("id"),
                    data = {};

                data["YII_CSRF_TOKEN"] = system.csrf;

                $(this).fileupload({
                    dataType: "json",
                    url: url,
                    forceIframeTransport: true,
                    timeout: 120000,
                    formData: data,
                    dropZone:$('input[name^="TargetCheckAttachmentUploadForm"]'),

                    done: function (e, data) {
                        $(".loader-image").hide();
                        $("#upload-message-" + id).hide();
                        $("#upload-link-" + id).show();

                        var json = data.result;

                        if (json.status == "error") {
                            system.addAlert("error", json.errorText);
                            return;
                        }

                        data = json.data;

                        var tr = $('<tr>')
                            .attr('data-path', data.path)
                            .attr('data-control-url', data.controlUrl)
                            .append(
                                $('<td>')
                                    .addClass('info')
                                    .append(
                                        $('<span>')
                                            .attr('contenteditable', 'true')
                                            .addClass('single-line')
                                            .addClass('title')
                                            .blur(function () {
                                                $(this).siblings('input').val($(this).text());
                                            })
                                            .text(data.title),
                                        $('<input>')
                                            .attr('type', 'hidden')
                                            .attr('name', 'TargetCheckEditForm_' + data.targetCheck + '[attachmentTitles][]')
                                            .attr('data-path', data.path)
                                            .val(data.title)
                                    ),
                                $('<td>')
                                    .addClass('actions')
                                    .append(
                                        $('<a>')
                                            .attr('href', data.url)
                                            .attr('title', system.translate("Download"))
                                            .append(
                                                $('<i>')
                                                    .addClass('icon')
                                                    .addClass('icon-download')
                                            ),
                                        $('<a>')
                                            .attr('href', '#del')
                                            .attr('title', system.translate("Delete"))
                                            .attr('onclick', 'user.check.delAttachment(\'' + data.path + '\')')
                                            .append(
                                                $('<i>')
                                                    .addClass('icon')
                                                    .addClass('icon-remove')
                                            )
                                    )
                            );

                        if ($('div.check-form[data-type=check][data-id="' + id + '"] .attachment-list').length == 0) {
                            $('div.check-form[data-type=check][data-id="' + id + '"] .upload-message')
                                .after('<table class="table attachment-list"><tbody></tbody></table>');
                        }

                        $('div.check-form[data-type=check][data-id="' + id + '"] .attachment-list').show();
                        $('div.check-form[data-type=check][data-id="' + id + '"] .attachment-list > tbody').append(tr);
                    },

                    fail: function (e, data) {
                        $(".loader-image").hide();
                        $("#upload-message-" + id).hide();
                        $("#upload-link-" + id).show();
                        system.addAlert("error", system.translate("Request failed, please try again."));
                    },

                    start: function (e) {
                        $(".loader-image").show();
                        $("#upload-link-" + id).hide();
                        $("#upload-message-" + id).show();
                    }
                });
            });
        };

        /**
         * Control attachment function.
         */
        this._controlAttachment = function(path, operation) {
            var url = $("tr[data-path=" + path + "]").data("control-url");

            $.ajax({
                dataType: "json",
                url: url,
                timeout: system.ajaxTimeout,
                type: "POST",

                data : {
                    "TargetCheckAttachmentControlForm[operation]": operation,
                    "TargetCheckAttachmentControlForm[path]": path,
                    "YII_CSRF_TOKEN": system.csrf
                },

                success : function (data, textStatus) {
                    $(".loader-image").hide();

                    if (data.status == "error") {
                        system.addAlert("error", data.errorText);
                        return;
                    }

                    if (operation == "delete") {
                        $("tr[data-path=" + path + "]").fadeOut("slow", undefined, function () {
                            var table = $("tr[data-path=" + path + "]").parent().parent();

                            $("tr[data-path=" + path + "]").remove();

                            if ($("tbody > tr", table).length == 0)
                                table.hide();
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
         * Delete attachment.
         */
        this.delAttachment = function (path) {
            if (confirm(system.translate("Are you sure that you want to delete this object?"))) {
                _check._controlAttachment(path, "delete");
            }
        };

        /**
         * Initialize custom attachments form.
         */
        this.initTargetCustomCheckAttachmentUploadForms = function () {
            $('input[name^="TargetCustomCheckAttachmentUploadForm"]').each(function () {
                var url = $(this).data("upload-url"),
                    id = $(this).data("id"),
                    data = {};

                data["YII_CSRF_TOKEN"] = system.csrf;

                $(this).fileupload({
                    dataType: "json",
                    url: url,
                    forceIframeTransport: true,
                    timeout: 120000,
                    formData: data,
                    dropZone:$('input[name^="TargetCustomCheckAttachmentUploadForm"]'),

                    done: function (e, data) {
                        $(".loader-image").hide();
                        $("#upload-custom-message-" + id).hide();
                        $("#upload-custom-link-" + id).show();

                        var json = data.result;

                        if (json.status == "error") {
                            system.addAlert("error", json.errorText);
                            return;
                        }

                        data = json.data;

                        var tr = $('<tr>')
                            .attr('data-path', data.path)
                            .attr('data-control-url', data.controlUrl)
                            .append(
                                $('<td>')
                                    .addClass('info')
                                    .append(
                                        $('<span>')
                                            .attr('contenteditable', 'true')
                                            .addClass('single-line')
                                            .addClass('title')
                                            .blur(function () {
                                                $(this).siblings('input').val($(this).text());
                                            })
                                            .text(data.title),
                                        $('<input>')
                                            .attr('type', 'hidden')
                                            .attr('name', 'TargetCustomCheckEditForm_' + data.customCheck + '[attachmentTitles][]')
                                            .attr('data-path', data.path)
                                            .val(data.title)
                                    ),
                                $('<td>')
                                    .addClass('actions')
                                    .append(
                                        $('<a>')
                                            .attr('href', data.url)
                                            .attr('title', system.translate("Download"))
                                            .append(
                                                $('<i>')
                                                    .addClass('icon')
                                                    .addClass('icon-download')
                                            ),
                                        $('<a>')
                                            .attr('href', '#del')
                                            .attr('title', system.translate("Delete"))
                                            .attr('onclick', 'user.check.delCustomAttachment(\'' + data.path + '\')')
                                            .append(
                                                $('<i>')
                                                    .addClass('icon')
                                                    .addClass('icon-remove')
                                            )
                                    )
                            );

                        if ($('div.check-form[data-type=custom-check][data-id=' + id + '] .attachment-list').length == 0) {
                            $('div.check-form[data-type=custom-check][data-id=' + id + '] .upload-message')
                                .after('<table class="table attachment-list"><tbody></tbody></table>');
                        }

                        $('div.check-form[data-type=custom-check][data-id=' + id + '] .attachment-list').show();
                        $('div.check-form[data-type=custom-check][data-id=' + id + '] .attachment-list > tbody').append(tr);
                    },

                    fail: function (e, data) {
                        $(".loader-image").hide();
                        $("#upload-custom-message-" + id).hide();
                        $("#upload-custom-link-" + id).show();
                        system.addAlert("error", system.translate("Request failed, please try again."));
                    },

                    start: function (e) {
                        $(".loader-image").show();
                        $("#upload-custom-link-" + id).hide();
                        $("#upload-custom-message-" + id).show();
                    }
                });
            });
        };

        /**
         * Control custom attachment function.
         */
        this._controlCustomAttachment = function(path, operation) {
            var url = $("tr[data-path=" + path + "]").data("control-url");

            $.ajax({
                dataType: "json",
                url: url,
                timeout: system.ajaxTimeout,
                type: "POST",

                data : {
                    "TargetCustomCheckAttachmentControlForm[operation]": operation,
                    "TargetCustomCheckAttachmentControlForm[path]": path,
                    "YII_CSRF_TOKEN": system.csrf
                },

                success : function (data, textStatus) {
                    $(".loader-image").hide();

                    if (data.status == "error") {
                        system.addAlert("error", data.errorText);
                        return;
                    }

                    if (operation == "delete") {
                        $("tr[data-path=" + path + "]").fadeOut("slow", undefined, function () {
                            var table = $("tr[data-path=" + path + "]").parent().parent();

                            $("tr[data-path=" + path + "]").remove();

                            if ($("tbody > tr", table).length == 0) {
                                table.hide();
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
         * Delete custom attachment.
         */
        this.delCustomAttachment = function (path) {
            if (confirm(system.translate("Are you sure that you want to delete this object?"))) {
                _check._controlCustomAttachment(path, "delete");
            }
        };

        /**
         * Control check function.
         * @param id
         * @param operation
         * @param custom
         */
        this._control = function(id, operation, custom) {
            var row, headerRow, url;

            headerRow = custom ?
                $('div.check-header[data-type=custom-check][data-id=' + id + ']') :
                $('div.check-header[data-type=check][data-id="' + id + '"]');

            row = custom ?
                $('div.check-form[data-type=custom-check][data-id=' + id + ']') :
                $('div.check-form[data-type=check][data-id="' + id + '"]');

            url = headerRow.data("control-url");

            $.ajax({
                dataType: 'json',
                url: url,
                timeout: system.ajaxTimeout,
                type: 'POST',

                data: {
                    'EntryControlForm[operation]': operation,
                    'EntryControlForm[id]': id,
                    'YII_CSRF_TOKEN': system.csrf
                },

                success: function (data, textStatus) {
                    _check.setLoaded(id, custom);

                    if (data.status == "error") {
                        system.addAlert("error", data.errorText);
                        return;
                    }

                    data = data.data;

                    if (operation == "start") {
                        $("td.status", headerRow).html("00:00");
                        $("td.actions", headerRow).html("");
                        $("td.actions", headerRow).append(
                            '<a href="#stop" title="' + system.translate('Stop') + '" onclick="user.check.stop(' + id +
                            ');"><i class="icon icon-stop"></i></a> &nbsp; ' +
                            '<span class="disabled"><i class="icon icon-refresh" title="' +
                            system.translate("Reset") + '"></i></span>'
                        );

                        $("div.table-result", row).html("");
                        _check.setLoading(id);
                        $(".loader-image").hide();

                        _check.runningChecks.push({
                            id: id,
                            time: -1,
                            result: ""
                        });

                        headerRow.addClass("in-progress");
                    } else if (!custom && operation == "reset") {
                        $("td.actions", headerRow).html("");
                        $("td.status", headerRow).html("&nbsp;");

                        if (data.automated) {
                            $("td.actions", headerRow).append(
                                '<a href="#start" title="' + system.translate("Start") + '" onclick="user.check.start(' + id +
                                ');"><i class="icon icon-play"></i></a> &nbsp; '
                            );
                        }

                        $("td.actions", headerRow).append(
                            '<span class="disabled"><i class="icon icon-refresh" title="' +
                            system.translate('Reset') + '"></i></span>'
                        );

                        $('input[type="text"]', row).val('');
                        $('input[type="radio"]', row).prop('checked', false);
                        $('input[type="checkbox"]', row).prop('checked', false);
                        $('textarea', row).val('');
                        $('table.attachment-list', row).remove();
                        $('div.table-result', row).html('');

                        // port & protocol values
                        if (data.protocol != null && data.protocol != undefined)
                            $('#TargetCheckEditForm_' + id + '_protocol').val(data.protocol);

                        if (data.port != null && data.port != undefined)
                            $('#TargetCheckEditForm_' + id + '_port').val(data.port);

                        // input values
                        for (var i = 0; i < data.inputs.length; i++) {
                            var input, input_obj;

                            input = data.inputs[i];
                            input_obj = $("#" + input.id);

                            if (input_obj.is(":checkbox") || input_obj.is(":radio")) {
                                continue;
                            }

                            input_obj.val(input.value);
                        }

                        $.each(data.timeouts, function(key, timeout) {
                            $("#" + timeout.id).val(timeout.timeout);
                        });

                        $.each(data.scripts, function(key, script) {
                            $("#" + script.id).prop("checked", script.start);
                        });

                        $('input[name="TargetCheckEditForm_' + id + '[rating]"][value=0]').prop("checked", true);
                    } else if (operation == 'stop') {
                        $('td.actions', headerRow).html('');
                        $('td.actions', headerRow).append(
                            '<span class="disabled"><i class="icon icon-stop" title="' +
                            system.translate('Stop') + '"></i></span> &nbsp; ' +
                            '<span class="disabled"><i class="icon icon-refresh" title="' +
                            system.translate('Reset') + '"></i></span>'
                        );

                        _check.setLoading(id);
                        $('.loader-image').hide();
                    } else if (custom && operation == "reset") {
                        $("td.status", headerRow).html("&nbsp;");
                        $("td.actions", headerRow).html("");
                        $("td.actions", headerRow).append(
                            '<span class="disabled"><i class="icon icon-refresh" title="' +
                            system.translate('Reset') + '"></i></span>'
                        );

                        $('input[type="text"]', row).val("");
                        $('input[type="radio"]', row).prop("checked", false);
                        $('input[type="checkbox"]', row).prop("checked", false);
                        $("textarea", row).val("");

                        $('input[name="TargetCustomCheckEditForm_' + id + '[rating]"][value=0]').prop("checked", true);
                    } else if (custom && operation == "delete") {
                        $("div[data-id=custom-" + id + "]").fadeOut("slow", undefined, function () {
                            $("div[data-id=custom-" + id + ']').remove();
                        });
                    } else if (!custom && operation == "delete") {
                        $("div[data-id=" + id + "]").fadeOut("slow", undefined, function () {
                            $("div[data-id=" + id + ']').remove();
                        });
                    } else if (!custom && operation == "copy") {
                        location.reload();
                    }
                },

                error: function(jqXHR, textStatus, e) {
                    _check.setLoaded(id, custom);
                    system.addAlert('error', system.translate('Request failed, please try again.'));
                },

                beforeSend: function (jqXHR, settings) {
                    _check.setLoading(id, custom);
                }
            });
        };

        /**
         * Start check.
         */
        this.start = function (id) {
            var row, headerRow, data, url, scriptCount;

            headerRow = $('div.check-header[data-type=check][data-id="' + id + '"]');
            row = $('div.check-form[data-type=check][data-id="' + id + '"]');
            url = row.data('save-url');
            scriptCount = parseInt(headerRow.data('script-count'));

            if (scriptCount <= 0) {
                system.addAlert('error', system.translate('Check has no scripts attached.'));
                return;
            }

            data = _check.getData(id);
            data.push({name: 'YII_CSRF_TOKEN', value: system.csrf});

            _check.setLoading(id);

            $.ajax({
                dataType: 'json',
                url: url,
                timeout: system.ajaxTimeout,
                type: 'POST',
                data: data,

                success : function (data, textStatus) {
                    if (data.status == 'error') {
                        _check.setLoaded(id);
                        system.addAlert('error', data.errorText);

                        return;
                    }

                    _check._control(id, 'start');
                },

                error : function(jqXHR, textStatus, e) {
                    _check.setLoaded(id);
                    system.addAlert('error', system.translate('Request failed, please try again.'));
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
         * @param id
         */
        this.reset = function (id) {
            if (confirm(system.translate('Are you sure that you want to reset this check?'))) {
                _check._control(id, "reset");
            }
        };

        /**
         * Copy check.
         * @param id
         */
        this.copy = function (id) {
            if (confirm(system.translate("Are you sure that you want to copy this check?"))) {
                _check._control(id, "copy");
            }
        };

        /**
         * Delete check.
         * @param id
         */
        this.del = function (id) {
            if (confirm(system.translate("Are you sure that you want to delete this check?"))) {
                _check._control(id, "delete");
            }
        };

        /**
         * Reset custom check
         * @param id
         */
        this.resetCustom = function (id) {
            if (confirm(system.translate("Are you sure that you want to reset this custom check?"))) {
                _check._control(id, "reset", true);
            }
        };

        /**
         * Delete custom check
         * @param id
         */
        this.deleteCustom = function (id) {
            if (confirm(system.translate("Are you sure that you want to delete this custom check?"))) {
                _check._control(id, "delete", true);
            }
        };

        /**
         * Returns editor by id
         * @param id
         * @returns {boolean}
         */
        this.getEditor = function (id) {
            return _check.ckeditors[id];
        };

        /**
         * Destroy editor for element
         * @param id
         */
        this.destroyEditor = function (id) {
            _check.ckeditors[id].destroy();
            delete _check.ckeditors[id];
        };

        /**
         * Enable editor for element
         * @param id
         */
        this.enableEditor = function (id) {
            _check.ckeditors[id] = CKEDITOR.replace(id, {
                fullPage: false,
                allowedContent: false,
                height: "300px"
            });
        };

        /**
         * Toggle WYSIWYG editor
         * @param id
         */
        this.toggleEditor = function (id) {
            if (_check.getEditor(id)) {
                _check.destroyEditor(id);
            } else {
                _check.enableEditor(id);
            }
        };

        /**
         * Toggle field by id
         * @param id
         * @param callback
         */
        this.toggleField = function (id) {
            var target = $('#' + id);

            if (target.is(':visible')) {
                target.hide();
            } else {
                target.show();
            }
        };

        /**
         * Delete table result entry
         * @param tableId
         * @param entryId
         */
        this.delTableResultEntry = function (checkId, tableId, entryId) {
            if (confirm(system.translate("Are you sure that you want to delete this object?"))) {
                var $row = $('div.check-form[data-type=check][data-id="' + checkId + '"]');
                var $table = $row.find('table[data-table-id=' + tableId + ']');
                var $row = $table.find('[data-id=' + entryId + ']');

                $row.fadeOut("slow", function () {
                    $(this).remove();

                    if ($table.find("tr.data").length == 0) {
                        $table.remove();
                    }
                });
            }
        };

        /**
         * Add new enrty to table_result
         * @param tableId
         */
        this.newTableResultEntry = function (checkId, tableId) {
            var $row = $('div.check-form[data-type=check][data-id="' + checkId + '"]');
            var $table = $row.find('table[data-table-id=' + tableId + ']');
            var $inputs = $table.find('.new-entry input');

            if ($inputs.filter(function () {
                    return $.trim($(this).val()).length > 0
                }).length == 0) {
                alert(system.translate("At least one field must be filled!"));
                return;
            }

            var newEntryId = parseInt($table.find('tr.data').last().data('id')) + 1;
            var $newDataEntry = $('<tr>')
                .addClass('data')
                .attr('data-id', newEntryId);

            $.each($inputs, function (key, field) {
                $newDataEntry.append(
                    $('<td>')
                        .text($(field).val())
                );
                $(field).val('');
            });

            $newDataEntry.append(
                $('<td>')
                    .addClass('actions')
                    .append(
                        $('<a>')
                            .attr('href', '#del')
                            .attr('title', system.translate('Delete'))
                            .attr('onclick', "user.check.delTableResultEntry('" + checkId + "', '" + tableId + "', '" + newEntryId + "');")
                            .append(
                                $('<i>')
                                    .addClass('icon')
                                    .addClass('icon-remove')
                            )
                    )
            );

            $table.find('tr.data').last().after($newDataEntry);
        };

        /**
         * Build table_result string for check
         * @param checkForm
         */
        this.buildTableResult = function (checkForm) {
            var $tables, $titles, $title, $rows, $row, $cells, $cell, tableNode, columnsNode, columnNode, rowNode, cellNode, tableResultNode;

            tableResultNode = $('<table-result>');

            $tables = $(checkForm).find('table.table-result');

            if ($tables.length) {
                $.each($tables, function(key, table) {
                    var $table = $(table);

                    $titles = $table.find('.titles').find('th');

                    tableNode = $('<gtta-table>');
                    columnsNode = $('<columns>');

                    $.each($titles, function(key, title) {
                        $title = $(title);

                        columnNode = $('<column>');
                        columnNode.attr('name', $title.text().trim());
                        columnNode.attr('width', $title.data('width'));

                        columnsNode.append(columnNode);
                    });

                    tableNode.append(columnsNode);

                    $rows = $table.find('tr.data');

                    $.each($rows, function(key, row) {
                        $row = $(row);
                        rowNode = $('<row>');

                        $cells = $row.find('td').not('.actions');

                        $.each($cells, function(key, cell) {
                            $cell = $(cell);

                            cellNode = $('<cell>');
                            cellNode.text($cell.text().trim());

                            rowNode.append(cellNode);
                        });

                        tableNode.append(rowNode);
                    });

                    tableResultNode.append(tableNode);
                });
            }

            return tableResultNode.html();
        };
    };

    /**
     * Project object.
     */
    this.project = new function () {
        var _project = this;

        /**
         * Toggle project's "guided test" status.
         */
        this.toggleGuidedTest = function (url, id) {
            data = {};

            data['YII_CSRF_TOKEN'] = system.csrf;
            data['EntryControlForm[id]'] = id;
            data['EntryControlForm[operation]'] = 'gt';

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
                        system.addAlert('error', data.errorText);
                        return;
                    }

                    location.reload();
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
         * Toggle check source lists on target edit
         * @param val
         */
        this.toggleChecksSource = function (val) {
            $('.checks-source-list').addClass("hide");
            $("." + val + "-list").removeClass("hide");
        };
    };

    /**
     * GT selector object.
     */
    this.gtSelector = new function () {
        var _gtSelector = this;

        /**
         * Toggle GT category.
         */
        this.categoryToggle = function (id) {
            if ($('div.gt-category-content[data-id=' + id + ']').is(':visible')) {
                $('div.gt-category-content[data-id=' + id + ']').slideUp('slow');
            } else {
                $('div.gt-category-content[data-id=' + id + ']').slideDown('slow');
            }
        };

        /**
         * Toggle GT type.
         */
        this.typeToggle = function (id) {
            if ($('div.gt-category-type-content[data-id=' + id + ']').is(':visible')) {
                $('div.gt-category-type-content[data-id=' + id + ']').slideUp('slow');
            } else {
                $('div.gt-category-type-content[data-id=' + id + ']').slideDown('slow');
            }
        };
    };

    /**
     * GT check object.
     */
    this.gtCheck = new function () {
        var _gtCheck = this;

        this.runningCheck = undefined;
        this.updateIteration = 0;

        /**
         * Update status of running checks.
         */
        this.update = function (url) {
            var i, k, check, headingRow, minutes, seconds;

            if (_gtCheck.runningCheck) {
                _gtCheck.updateIteration++;

                check = _gtCheck.runningCheck;
                headingRow = $('div.check-header');

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

                $('td.status', headingRow).html(minutes.zeroPad(2) + ':' + seconds.zeroPad(2));
            }

            if (_gtCheck.updateIteration < 5) {
                setTimeout(function () {
                    _gtCheck.update(url);
                }, 1000);
            } else {
                _gtCheck.updateIteration = 0;

                data = [];
                data.push({name: 'YII_CSRF_TOKEN', value: system.csrf});

                $.ajax({
                    dataType: 'json',
                    url: url,
                    timeout: system.ajaxTimeout,
                    type: 'POST',
                    data: data,

                    success : function (data, textStatus) {
                        $('.loader-image').hide();

                        if (data.status == 'error') {
                            system.addAlert('error', data.errorText);
                            return;
                        }

                        data = data.data;

                        if (data.check) {
                            var check, table, tbody, tr, attachment, target;

                            check = data.check;

                            $('#ProjectGtCheckEditForm_result').val(check.result);
                            $('div.check-form div.table-result').html(check.tableResult);

                            if (check.startedText) {
                                $('div.check-form .automated-info-block')
                                    .html(check.startedText)
                                    .show();
                            } else {
                                $('div.check-form .automated-info-block').hide();
                            }

                            // attachments
                            if (check.attachments.length > 0) {
                                table = $('div.check-form .attachment-list');
                                tbody = table.find("tbody");
                                tbody.find("tr").remove();

                                for (k = 0; k < check.attachments.length; k++) {
                                    attachment = check.attachments[k];
                                    tr = $("<tr>");

                                    tr.attr("data-path", attachment.path);
                                    tr.attr("data-control-url", check.attachmentControlUrl);

                                    tr.append(
                                        $("<td>")
                                            .addClass("name")
                                            .append(
                                                $("<a>")
                                                    .attr("href", attachment.url)
                                                    .html(attachment.name)
                                            )
                                    );

                                    tr.append(
                                        $("<td>")
                                            .addClass("actions")
                                            .append(
                                                $("<a>")
                                                    .attr("href", "#del")
                                                    .attr("title", system.translate("Delete"))
                                                    .html('<i class="icon icon-remove"></i>')
                                                    .click(function () {
                                                        user.gtCheck.delAttachment(attachment.path);
                                                    })
                                            )
                                    );

                                    tbody.append(tr);
                                }

                                table.show();
                            }

                            // targets
                            if (check.targets.length > 0) {
                                table = $('.suggested-target-list');
                                tbody = table.find("tbody");
                                tbody.find("tr").remove();

                                for (k = 0; k < check.targets.length; k++) {
                                    target = check.targets[k];
                                    tr = $("<tr>");

                                    tr.attr("data-id", target.id);
                                    tr.attr("data-control-url", check.targetControlUrl);

                                    tr.append(
                                        $("<td>")
                                            .addClass("target")
                                            .append(target.host + " / ")
                                            .append(
                                                $("<a>")
                                                    .attr("href", "#")
                                                    .html(target.module.name)
                                            )
                                    );

                                    tr.append(
                                        $("<td>")
                                            .addClass("actions")
                                            .append(
                                                $("<a>")
                                                    .attr("id", "approve-link")
                                                    .attr("href", "#approve")
                                                    .attr("title", system.translate("Approve"))
                                                    .attr("onclick", "user.gtCheck.approveTarget(" + target.id + ");")
                                                    .html('<i class="icon icon-ok"></i>')
                                            )
                                            .append("&nbsp;")
                                            .append(
                                                $("<a>")
                                                    .attr("href", "#del")
                                                    .attr("title", system.translate("Delete"))
                                                    .attr("onclick", "user.gtCheck.delTarget(" + target.id + ");")
                                                    .html('<i class="icon icon-remove"></i>')
                                            )
                                    );

                                    tbody.append(tr);
                                }

                                table.parent().parent().show();
                            }

                            if (_gtCheck.runningCheck) {
                                _gtCheck.runningCheck.time = check.time;
                            }

                            if (check.finished) {
                                _gtCheck.runningCheck = undefined;
                                var headerRow = $('div.check-header');

                                headerRow.removeClass('in-progress');
                                $('td.status', headerRow).html('&nbsp;');

                                _gtCheck.setLoaded();

                                $('td.actions', headerRow).html('');
                                $('td.actions', headerRow).append(
                                    '<a href="#start" title="' + system.translate('Start') + '" onclick="user.gtCheck.start();"><i class="icon icon-play"></i></a> &nbsp; ' +
                                    '<a href="#reset" title="' + system.translate('Reset') + '" onclick="user.gtCheck.reset();"><i class="icon icon-refresh"></i></a>'
                                );
                            }
                        }

                        setTimeout(function () {
                            _gtCheck.update(url);
                        }, 1000);
                    },

                    error : function(jqXHR, textStatus, e) {
                        $('.loader-image').hide();
                        system.addAlert('error', system.translate('Request failed, please try again.'));
                    },

                    beforeSend : function (jqXHR, settings) {
                        $('.loader-image').show();
                    }
                });
            }
        };

        /**
         * Expand solution.
         */
        this.expandSolution = function (id) {
            $('span.solution-control[data-id=' + id + ']').html('<a href="#solution" onclick="user.gtCheck.collapseSolution(\'' + id + '\');"><i class="icon-chevron-up"></i></a>');
            $('div.solution-content[data-id=' + id + ']').slideDown('slow');
        };

        /**
         * Collapse solution.
         */
        this.collapseSolution = function (id) {
            $('span.solution-control[data-id=' + id + ']').html('<a href="#solution" onclick="user.gtCheck.expandSolution(\'' + id + '\');"><i class="icon-chevron-down"></i></a>');
            $('div.solution-content[data-id=' + id + ']').slideUp('slow');
        };

        /**
         * Expand result.
         */
        this.expandResult = function (id) {
            $('span.result-control[data-id=' + id + ']').html('<a href="#result" onclick="user.gtCheck.collapseResult(' + id + ');"><i class="icon-chevron-up"></i></a>');
            $('div.result-content[data-id=' + id + ']').slideDown('slow');
        };

        /**
         * Collapse result.
         */
        this.collapseResult = function (id) {
            $('span.result-control[data-id=' + id + ']').html('<a href="#result" onclick="user.gtCheck.expandResult(' + id + ');"><i class="icon-chevron-down"></i></a>');
            $('div.result-content[data-id=' + id + ']').slideUp('slow');
        };

        /**
         * Insert predefined result.
         */
        this.insertResult = function (result) {
            if (_gtCheck.runningCheck) {
                return;
            }

            var textarea = $('#ProjectGtCheckEditForm_result');

            result = result.replace(/\n<br>/g, '\n');
            result = result.replace(/<br>/g, '\n');
            result = result.replace(/&amp;/g, '&').replace(/&lt;/g, '<').replace(/&gt;/g, '>');

            if (result.match(/Problem:/ig)) {
                result = result + "\n\nTechnical Details:\n\n@cut\n";
            }

            textarea.val(result + '\n' + textarea.val());
        };

        /**
         * Set loading.
         */
        this.setLoading = function () {
            row = $('div.check-form');

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
        this.setLoaded = function () {
            row = $('div.check-form');

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
        this.getData = function () {
            var i, row, textareas, texts, checkboxes, radios, target, protocol, port, result, solutions, attachments,
                rating, data, solution, solutionTitle, saveSolution;

            row = $('div.check-form');

            texts = $('input[type="text"][name^="ProjectGtCheckEditForm[inputs]"]', row).map(
                function () {
                    return {
                        name  : $(this).attr('name'),
                        value : $(this).val()
                    }
                }
            ).get();

            textareas = $('textarea[name^="ProjectGtCheckEditForm[inputs]"]', row).map(
                function () {
                    return {
                        name  : $(this).attr('name'),
                        value : $(this).val()
                    }
                }
            ).get();

            checkboxes = $('input[type="checkbox"][name^="ProjectGtCheckEditForm[inputs]"]', row).map(
                function () {
                    return {
                        name  : $(this).attr('name'),
                        value : $(this).is(':checked') ? $(this).val() : '0'
                    }
                }
            ).get();

            radios = $('input[type="radio"][name^="ProjectGtCheckEditForm[inputs]"]:checked', row).map(
                function () {
                    return {
                        name  : $(this).attr('name'),
                        value : $(this).val()
                    }
                }
            ).get();

            target = $('input[name="ProjectGtCheckEditForm[target]"]', row).val();
            protocol = $('input[name="ProjectGtCheckEditForm[protocol]"]', row).val();
            port = $('input[name="ProjectGtCheckEditForm[port]"]', row).val();
            result = $('textarea[name="ProjectGtCheckEditForm[result]"]', row).val();

            solutions = $('input[name^="ProjectGtCheckEditForm[solutions]"]:checked', row).map(
                function () {
                    return {
                        name  : $(this).attr('name'),
                        value : $(this).val()
                    }
                }
            ).get();

            for (i = 0; i < solutions.length; i++) {
                if (solutions[i].value == system.constants.ProjectGtCheckEditForm.CUSTOM_SOLUTION_IDENTIFIER) {
                    solution = $('textarea[name="ProjectGtCheckEditForm[solution]"]', row).val();
                    solutionTitle = $('input[name="ProjectGtCheckEditForm[solutionTitle]"]', row).val();
                    saveSolution = $('input[name="ProjectGtCheckEditForm[saveSolution]"]', row).is(":checked");
                }
            }

            attachments = $('input[name^="ProjectGtCheckEditForm[attachmentTitles]"]', row).map(
                function () {
                    return {
                        name : $(this).attr('name'),
                        value: JSON.stringify({ path : $(this).data('path'), 'title' : $(this).val() })
                    }
                }
            ).get();

            rating = $('input[name="ProjectGtCheckEditForm[rating]"]:checked', row).val();

            if (target == undefined) {
                target = '';
            }

            if (protocol == undefined) {
                protocol = '';
            }

            if (port == undefined) {
                port = '';
            }

            if (result == undefined) {
                result = '';
            }

            if (rating == undefined) {
                rating = '';
            }

            data = [];

            data.push({name: 'ProjectGtCheckEditForm[target]', value: target});
            data.push({name: 'ProjectGtCheckEditForm[protocol]', value: protocol});
            data.push({name: 'ProjectGtCheckEditForm[port]', value: port});
            data.push({name: 'ProjectGtCheckEditForm[result]', value: result});
            data.push({name: 'ProjectGtCheckEditForm[rating]', value: rating});

            data.push({name: "ProjectGtCheckEditForm[solution]", value: solution ? solution : ""});
            data.push({name: "ProjectGtCheckEditForm[solutionTitle]", value: solutionTitle ? solutionTitle : ""});
            data.push({name: "ProjectGtCheckEditForm[tableResult]", value: _gtCheck.buildTableResult(row)});

            if (saveSolution) {
                data.push({name: "ProjectGtCheckEditForm[saveSolution]", value: "1"});
            }

            for (i = 0; i < texts.length; i++) {
                data.push(texts[i]);
            }

            for (i = 0; i < textareas.length; i++) {
                data.push(textareas[i]);
            }

            for (i = 0; i < checkboxes.length; i++) {
                data.push(checkboxes[i]);
            }

            for (i = 0; i < radios.length; i++) {
                data.push(radios[i]);
            }

            for (i = 0; i < solutions.length; i++) {
                data.push(solutions[i]);
            }

            for (i = 0; i < attachments.length; i++) {
                data.push(attachments[i]);
            }

            return data;
        };

        /**
         * Save the check.
         */
        this.save = function () {
            var row, headerRow, data, url, nextRow, rating;

            headerRow = $('div.check-header');
            row = $('div.check-form');
            url = row.data('save-url');

            data = _gtCheck.getData();
            data.push({name: 'YII_CSRF_TOKEN', value: system.csrf});

            $.ajax({
                dataType : 'json',
                url      : url,
                timeout  : system.ajaxTimeout,
                type     : 'POST',
                data     : data,

                success : function (data, textStatus) {
                    _gtCheck.setLoaded();

                    if (data.status == 'error') {
                        system.addAlert('error', data.errorText);
                        return;
                    }

                    data = data.data;

                    if (data.rating != undefined && data.rating != null) {
                        $('td.status', headerRow).html(
                            '<span class="label ' +
                            (ratings[data.rating].classN ? ratings[data.rating].classN : '') + '">' +
                            ratings[data.rating].text + '</span>'
                        );
                    } else {
                        $('td.status', headerRow).html('');
                    }

                    if (data.newSolution) {
                        var solution = data.newSolution;

                        $('div.check-form ul.solutions').append(
                            $("<li></li>")
                                .append(
                                    $("<div></div>")
                                        .addClass("solution-header")
                                        .append(
                                            $("<label></label>")
                                                .addClass(solution.multipleSolutions ? "checkbox" : "radio")
                                                .append(
                                                    $("<input>")
                                                        .attr("type", solution.multipleSolutions ? "checkbox" : "radio")
                                                        .attr("name", "ProjectGtCheckEditForm[solutions][]")
                                                        .val(solution.id)
                                                        .prop("checked", true)
                                                )
                                                .append(solution.title)
                                                .append(
                                                    $("<span></span>")
                                                        .addClass("solution-control")
                                                        .attr("data-id", solution.id)
                                                        .append(
                                                            $("<a></a>")
                                                                .attr("href", "#solution")
                                                                .click(function () {
                                                                    _gtCheck.expandSolution(solution.id);
                                                                })
                                                                .append(
                                                                    $("<i></i>")
                                                                        .addClass("icon-chevron-down")
                                                                )
                                                        )
                                                )
                                        )
                                )
                                .append(
                                    $("<div></div>")
                                        .addClass("solution-content")
                                        .addClass("hide")
                                        .attr("data-id", solution.id)
                                        .html(solution.solution)
                                )
                        );

                        $('input[name="ProjectGtCheckEditForm[solutions][]"].custom-solution').prop("checked", false);
                        $('textarea[name="ProjectGtCheckEditForm[solution]"]').data("wysihtml5").editor.clear();
                        $('input[name="ProjectGtCheckEditForm[solutionTitle]"]').val("");
                        $('input[name="ProjectGtCheckEditForm[saveSolution]"]').prop("checked", false);
                        _gtCheck.collapseSolution(system.constants.ProjectGtCheckEditForm.CUSTOM_SOLUTION_IDENTIFIER);
                    }

                    $('i.icon-refresh', headerRow).parent().remove();
                    $('td.actions', headerRow).append(
                        '<a href="#reset" title="' + system.translate('Reset') + '" onclick="user.gtCheck.reset();"><i class="icon icon-refresh"></i></a>'
                    );
                },

                error : function(jqXHR, textStatus, e) {
                    _gtCheck.setLoaded();
                    system.addAlert('error', system.translate('Request failed, please try again.'));
                },

                beforeSend : function (jqXHR, settings) {
                    _gtCheck.setLoading();
                }
            });
        };

        /**
         * Autosave the check.
         */
        this.autosave = function () {
            var row, headerRow, data, url, nextRow;

            row = $("div.check-form");
            url = row.data("autosave-url");

            data = {
                "YII_CSRF_TOKEN": system.csrf,
                "ProjectGtCheckEditForm[result]": $('textarea[name="ProjectGtCheckEditForm[result]"]', row).val()
            };

            $.ajax({
                dataType: "json",
                url: url,
                timeout: system.ajaxTimeout,
                type: "POST",
                data: data,

                success : function (data, textStatus) {
                    _gtCheck.setLoaded();

                    if (data.status == "error") {
                        system.addAlert("error", data.errorText);
                        return;
                    }
                },

                error : function(jqXHR, textStatus, e) {
                    _gtCheck.setLoaded();
                    system.addAlert("error", system.translate("Request failed, please try again."));
                },

                beforeSend : function (jqXHR, settings) {
                    _gtCheck.setLoading();
                }
            });
        };

        /**
         * Init autosave function for check id
         */
        this.initAutosave = function () {
            $(".check-form").each(function () {
                var row = $("div.check-form");

                $('textarea[name="ProjectGtCheckEditForm[result]"]', row).on("paste", function () {
                    setTimeout(function () {
                        _gtCheck.autosave();
                    }, 100);
                });
            });
        };

        /**
         * Initialize attachments form.
         */
        this.initProjectGtCheckAttachmentUploadForms = function () {
            $('input[name^="ProjectGtCheckAttachmentUploadForm"]').each(function () {
                var url = $(this).data('upload-url'), data = {};

                data['YII_CSRF_TOKEN'] = system.csrf;

                $(this).fileupload({
                    dataType: 'json',
                    url: url,
                    forceIframeTransport: true,
                    timeout: 120000,
                    formData: data,
                    dropZone:$('input[name^="ProjectGtCheckAttachmentUploadForm"]'),

                    done : function (e, data) {
                        $('.loader-image').hide();
                        $('#upload-message').hide();
                        $('#upload-link').show();

                        var json = data.result;

                        if (json.status == 'error') {
                            system.addAlert('error', json.errorText);
                            return;
                        }

                        data = json.data;

                        var tr = $('<tr>')
                            .attr('data-path', data.path)
                            .attr('data-control-url', data.controlUrl)
                            .append(
                                $('<td>')
                                    .addClass('info')
                                    .append(
                                        $('<span>')
                                            .attr('contenteditable', 'true')
                                            .addClass('single-line')
                                            .addClass('title')
                                            .blur(function () {
                                                $(this).siblings('input').val($(this).text());
                                            })
                                            .text(data.title),
                                        $('<input>')
                                            .attr('type', 'hidden')
                                            .attr('name', 'ProjectGtCheckEditForm[attachmentTitles][]')
                                            .attr('data-path', data.path)
                                            .val(data.title),
                                        $('<div>')
                                            .addClass('name')
                                            .addClass('content')
                                            .append(
                                                $('<a>')
                                                    .attr('href', data.url)
                                                    .text(data.name)
                                            )
                                    ),
                                $('<td>')
                                    .addClass('actions')
                                    .append(
                                        $('<a>')
                                            .attr('href', '#del')
                                            .attr('title', system.translate("Delete"))
                                            .attr('onclick', 'user.gtCheck.delAttachment(\'' + data.path + '\')')
                                            .append(
                                                $('<i>')
                                                    .addClass('icon')
                                                    .addClass('icon-remove')
                                            )
                                    )
                            );

                        if ($('div.check-form .attachment-list').length == 0)
                            $('div.check-form .upload-message').after('<table class="table attachment-list"><tbody></tbody></table>');

                        $('div.check-form .attachment-list').show();
                        $('div.check-form .attachment-list > tbody').append(tr);
                    },

                    fail : function (e, data) {
                        $('.loader-image').hide();
                        $('#upload-message').hide();
                        $('#upload-link').show();
                        system.addAlert('error', system.translate('Request failed, please try again.'));
                    },

                    start : function (e) {
                        $('.loader-image').show();
                        $('#upload-link').hide();
                        $('#upload-message').show();
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
                    'ProjectGtCheckAttachmentControlForm[operation]': operation,
                    'ProjectGtCheckAttachmentControlForm[path]': path,
                    'YII_CSRF_TOKEN': system.csrf
                },

                success : function (data, textStatus) {
                    $('.loader-image').hide();

                    if (data.status == 'error') {
                        system.addAlert('error', data.errorText);
                        return;
                    }

                    if (operation == 'delete') {
                        $('tr[data-path=' + path + ']').fadeOut('slow', undefined, function () {
                            var table = $('tr[data-path=' + path + ']').parent().parent();

                            $('tr[data-path=' + path + ']').remove();

                            if ($('tbody > tr', table).length == 0)
                                table.hide();
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
         * Control suggested target function.
         */
        this._controlTarget = function(id, operation) {
            var url = $('tr[data-id=' + id + ']').data('control-url');

            $.ajax({
                dataType : 'json',
                url      : url,
                timeout  : system.ajaxTimeout,
                type     : 'POST',

                data : {
                    'EntryControlForm[operation]': operation,
                    'EntryControlForm[id]': id,
                    'YII_CSRF_TOKEN': system.csrf
                },

                success : function (data, textStatus) {
                    $('.loader-image').hide();

                    if (data.status == 'error') {
                        system.addAlert('error', data.errorText);
                        return;
                    }

                    if (operation == 'delete') {
                        $('tr[data-id=' + id + ']').fadeOut('slow', undefined, function () {
                            var table = $('tr[data-id=' + id + ']').parent().parent();

                            $('tr[data-id=' + id + ']').remove();

                            if ($('tbody > tr', table).length == 0) {
                                table.parent().parent().hide();
                            }
                        });
                    } else if (operation == 'approve') {
                        $('tr[data-id=' + id + '] #approve-link').remove();
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
         * Delete attachment.
         */
        this.delAttachment = function (path) {
            if (confirm(system.translate('Are you sure that you want to delete this object?'))) {
                _gtCheck._controlAttachment(path, 'delete');
            }
        };

        /**
         * Delete suggested target.
         */
        this.delTarget = function (id) {
            if (confirm(system.translate('Are you sure that you want to delete this object?'))) {
                _gtCheck._controlTarget(id, 'delete');
            }
        };

        /**
         * Approve suggested target.
         */
        this.approveTarget = function (id) {
            _gtCheck._controlTarget(id, 'approve');
        };

        /**
         * Control check function.
         */
        this._control = function(operation) {
            var row, headerRow, url;

            headerRow = $('div.check-header');
            row = $('div.check-form');
            url = row.data('control-url');

            $.ajax({
                dataType : 'json',
                url      : url,
                timeout  : system.ajaxTimeout,
                type     : 'POST',

                data : {
                    'EntryControlForm[operation]' : operation,
                    'EntryControlForm[id]'        : 0,
                    'YII_CSRF_TOKEN'              : system.csrf
                },

                success : function (data, textStatus) {
                    _gtCheck.setLoaded();

                    if (data.status == 'error') {
                        system.addAlert('error', data.errorText);
                        return;
                    }

                    data = data.data;

                    if (operation == 'start') {
                        $('td.status',  headerRow).html('00:00');
                        $('td.actions', headerRow).html('');
                        $('td.actions', headerRow).append(
                            '<a href="#stop" title="' + system.translate('Stop') + '" onclick="user.gtCheck.stop();"><i class="icon icon-stop"></i></a> &nbsp; ' +
                            '<span class="disabled"><i class="icon icon-refresh" title="' +
                            system.translate('Reset') + '"></i></span>'
                        );

                        $('div.table-result', row).html('');

                        _gtCheck.setLoading();
                        $('.loader-image').hide();

                        _gtCheck.runningCheck = {
                            time: -1
                        };

                        headerRow.addClass('in-progress');
                    } else if (operation == 'reset') {
                        $('td.actions', headerRow).html('');
                        $('td.status', headerRow).html('&nbsp;');

                        if (data.automated) {
                            $('td.actions', headerRow).append(
                                '<a href="#start" title="' + system.translate('Start') + '" onclick="user.gtCheck.start();"><i class="icon icon-play"></i></a> &nbsp; '
                            );
                        }

                        $('td.actions', headerRow).append(
                            '<span class="disabled"><i class="icon icon-refresh" title="' +
                            system.translate('Reset') + '"></i></span>'
                        );

                        $('input[type="text"]', row).val('');
                        $('input[type="radio"]', row).prop('checked', false);
                        $('input[type="checkbox"]', row).prop('checked', false);
                        $('textarea', row).val('');
                        $('table.attachment-list', row).remove();
                        $('div.table-result', row).html('');

                        // port & protocol values
                        if (data.protocol != null && data.protocol != undefined) {
                            $('#ProjectGtCheckEditForm_protocol').val(data.protocol);
                        }

                        if (data.port != null && data.port != undefined) {
                            $('#ProjectGtCheckEditForm_port').val(data.port);
                        }

                        // input values
                        for (var i = 0; i < data.inputs.length; i++) {
                            var input, input_obj;

                            input = data.inputs[i];
                            input_obj = $("#" + input.id);

                            if (input_obj.is(":checkbox") || input_obj.is(":radio")) {
                                continue;
                            }

                            input_obj.val(input.value);
                        }
                    } else if (operation == 'stop') {
                        $('td.actions', headerRow).html('');
                        $('td.actions', headerRow).append(
                            '<span class="disabled"><i class="icon icon-stop" title="' +
                            system.translate('Stop') + '"></i></span> &nbsp; ' +
                            '<span class="disabled"><i class="icon icon-refresh" title="' +
                            system.translate('Reset') + '"></i></span>'
                        );

                        _gtCheck.setLoading();
                        $('.loader-image').hide();
                    } else if (operation == 'gt-next' || operation == 'gt-prev') {
                        $.cookie('gt_step', data.step, { path : '/' });
                        location.reload();
                    }

                    if (operation == "reset") {
                        $('input[name="ProjectGtCheckEditForm[rating]"][value=0]').prop("checked", true);
                    }
                },

                error : function(jqXHR, textStatus, e) {
                    _gtCheck.setLoaded();
                    system.addAlert('error', system.translate('Request failed, please try again.'));
                },

                beforeSend : function (jqXHR, settings) {
                    _gtCheck.setLoading();
                }
            });
        };

        /**
         * Start check.
         */
        this.start = function () {
            var row, headerRow, data, url, nextRow, rating;

            headerRow = $('div.check-header');
            row = $('div.check-form');
            url = row.data('save-url');

            if (!$("#ProjectGtCheckEditForm_target").val()) {
                alert(system.translate("No target specified!"));
                return;
            }

            data = _gtCheck.getData();
            data.push({name: 'YII_CSRF_TOKEN', value: system.csrf});

            _gtCheck.setLoading();

            $.ajax({
                dataType : 'json',
                url      : url,
                timeout  : system.ajaxTimeout,
                type     : 'POST',
                data     : data,

                success : function (data, textStatus) {
                    if (data.status == 'error') {
                        _gtCheck.setLoaded();
                        system.addAlert('error', data.errorText);

                        return;
                    }

                    _gtCheck._control('start');
                },

                error : function(jqXHR, textStatus, e) {
                    _gtCheck.setLoaded();
                    system.addAlert('error', system.translate('Request failed, please try again.'));
                }
            });
        };

        /**
         * Stop check.
         */
        this.stop = function () {
            _gtCheck._control('stop');
        };

        /**
         * Reset check.
         */
        this.reset = function () {
            if (confirm(system.translate('Are you sure that you want to reset this check?'))) {
                _gtCheck._control('reset');
            }
        };

        /**
         * Next check.
         */
        this.next = function () {
            _gtCheck._control('gt-next');
        };

        /**
         * Previous check.
         */
        this.prev = function () {
            _gtCheck._control('gt-prev');
        };

        /**
         * Delete table result entry
         * @param tableId
         * @param entryId
         */
        this.delTableResultEntry = function (tableId, entryId) {
            if (confirm(system.translate("Are you sure that you want to delete this object?"))) {
                var $table = $('table[data-table-id=' + tableId + ']');
                var $row = $table.find('[data-id=' + entryId + ']');

                $row.fadeOut("slow", function () {
                    $(this).remove();

                    if ($table.find("tr.data").length == 0) {
                        $table.remove();
                    }
                });
            }
        };

        /**
         * Add new enrty to table_result
         * @param tableId
         */
        this.newTableResultEntry = function (tableId) {
            var $table = $('table[data-table-id=' + tableId + ']');
            var $inputs = $table.find('.new-entry input');

            if ($inputs.filter(function () {
                return $.trim($(this).val()).length > 0
            }).length == 0) {
                alert(system.translate("At least one field must be filled!"));
                return;
            }

            var newEntryId = parseInt($table.find('tr.data').last().data('id')) + 1;
            var $newDataEntry = $('<tr>')
                .addClass('data')
                .attr('data-id', newEntryId);

            $.each($inputs, function (key, field) {
                $newDataEntry.append(
                    $('<td>')
                        .text($(field).val())
                );
                $(field).val('');
            });

            $newDataEntry.append(
                $('<td>')
                    .addClass('actions')
                    .append(
                        $('<a>')
                            .attr('href', '#del')
                            .attr('title', system.translate('Delete'))
                            .attr('onclick', "user.gtCheck.delTableResultEntry('" + tableId + "', '" + newEntryId + "');")
                            .append(
                                $('<i>')
                                    .addClass('icon')
                                    .addClass('icon-remove')
                            )
                    )
            );

            $table.find('tr.data').last().after($newDataEntry);
        };

        /**
         * Build table_result string for check
         * @param checkForm
         */
        this.buildTableResult = function (checkForm) {
            return user.check.buildTableResult(checkForm);
        };
    };
}

var user = new User();
