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

            _check.destroyEditor("TargetCheckEditForm_" + id + "_result");

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
        this.toggleControl = function (id, callback) {
            if ($("a[data-type=control-link][data-id=" + id + "]").hasClass("disabled")) {
                return;
            }

            if ($("div.control-body[data-id=" + id + "]").is(":visible")) {
                _check.collapseControl(id, callback);
            } else {
                _check.expandControl(id, callback);
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
            if (_check.isRunning(id)) {
                return;
            }

            var original = result;
            result = result.replace(/\n<br>/g, '\n').replace(/<br>\n/g, '\n').replace(/\n<br \/>/g, '\n').replace(/<br \/>\n/g, '\n');
            result = result.replace(/<br>/g, '\n').replace(/<br \/>/g, '\n');

            var editor = _check.getEditor("TargetCheckEditForm_" + id + "_result");

            if (editor) {
                result = original + "<br><br>";

                var range = editor.createRange();
                range.moveToElementEditStart(range.root);
                editor.getSelection().selectRanges([range]);
                editor.insertHtml(result);
            } else {
                if (result.isHTML()) {
                    result = original + "<br><br>";
                } else {
                    result = result + "\n\n";
                }

                var textarea = $('#TargetCheckEditForm_' + id + '_result');
                textarea.val(result + textarea.val());
                textarea.trigger('change');
            }
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
                attachments, rating, data, solution, solutionTitle, saveSolution, scripts, timeouts, fields;

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

            override = $('textarea[name="TargetCheckEditForm_' + id + '[overrideTarget]"]', row).val();
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

            fields = $(".check-form[data-id=" + id +"]").find(".target-check-field").map(
                function () {
                    if ($(this).attr("type") == "radio" && !$(this).is(":checked")) {
                        return;
                    }

                    return {
                        name: $(this).attr("name"),
                        value: _check.fieldValue($(this))
                    }
                }
            );

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
            data.push({ name : 'TargetCheckEditForm_' + id + '[resultTitle]',    value : resultTitle   });
            data.push({ name : 'TargetCheckEditForm_' + id + '[rating]',         value : rating   });

            data.push({name: "TargetCheckEditForm_" + id + "[solution]", value: solution ? solution : ""});
            data.push({name: "TargetCheckEditForm_" + id + "[solutionTitle]", value: solutionTitle ? solutionTitle : ""});
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

            for (i = 0; i < fields.length; i++) {
                data.push(fields[i]);
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
         * Get field value
         * @param el
         * @returns {*}
         */
        this.fieldValue = function (el) {
            switch ($(el).prop("tagName")) {
                case "INPUT":
                    var type = $(el).attr("type");

                    if (type == "text" || type == "radio") {
                        return $(el).val();
                    } else if (type == "checkbox") {
                        return $(el).is(":checked")
                    }

                    break;

                case "TEXTAREA":
                    return $(el).val();

                default:
                    console.error("Invalid field tagname.");
                    return null;
            }
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
            var i, row, name, background, question, result, rating, data, solution, solutionTitle, createCheck, attachments;

            row = $('div.check-form[data-type=custom-check][data-id=' + id + ']');

            name = $('input[name="TargetCustomCheckEditForm_' + id + '[name]"]', row).val();
            background = $('textarea[name="TargetCustomCheckEditForm_' + id + '[backgroundInfo]"]', row).val();
            question = $('textarea[name="TargetCustomCheckEditForm_' + id + '[question]"]', row).val();
            rating = $('input[name="TargetCustomCheckEditForm_' + id + '[rating]"]:checked', row).val();
            solutionTitle = $('input[name="TargetCustomCheckEditForm_' + id + '[solutionTitle]"]', row).val();
            solution = $('textarea[name="TargetCustomCheckEditForm_' + id + '[solution]"]', row).val();
            createCheck = $('input[name="TargetCustomCheckEditForm_' + id + '[createCheck]"]', row).is(":checked");

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
            var i, row, name, background, question, result, rating, data, solution, solutionTitle, createCheck;

            row = $('div.check-form[data-type=custom-template][data-id=' + id + ']');
            name = $('input[name="TargetCustomCheckTemplateEditForm_' + id + '[name]"]', row).val();
            background = $('textarea[name="TargetCustomCheckTemplateEditForm_' + id + '[backgroundInfo]"]', row).val();
            question = $('textarea[name="TargetCustomCheckTemplateEditForm_' + id + '[question]"]', row).val();
            rating = $('input[name="TargetCustomCheckTemplateEditForm_' + id + '[rating]"]:checked', row).val();
            solutionTitle = $('input[name="TargetCustomCheckTemplateEditForm_' + id + '[solutionTitle]"]', row).val();
            solution = $('textarea[name="TargetCustomCheckTemplateEditForm_' + id + '[solution]"]', row).val();
            createCheck = $('input[name="TargetCustomCheckTemplateEditForm_' + id + '[createCheck]"]', row).is(":checked");
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
            if (_check.ckeditors[id]) {
                try {
                    _check.ckeditors[id].destroy();
                } catch (e) {
                    // pass
                }

                delete _check.ckeditors[id];
            }
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
         * Toggle check source lists on target edit
         * @param val
         */
        this.toggleChecksSource = function (val) {
            $('.checks-source-list').addClass("hide");
            $('.references-list').addClass("hide");
            $("." + val + "-mode").removeClass("hide");
        };
    };

    /**
     * Target object
     */
    this.target = new function () {
        var _target = this;

        /**
         * Current target id on target chain editing
         * @type {null}
         */
        this.targetId = null;

        /**
         * Target check chain object
         */
        this.chain = new function () {
            var _chain = this;

            /**
             * Control chain
             * @param action
             * @param tId
             * @param url
             */
            this.control = function (action, tId, url) {
                $(".chain-start-button, .chain-stop-button, .chain-reset-button").prop("disabled", true);

                switch (action) {
                    case "start":
                        _chain.start(tId, url, function () {
                            $('.chain-start-button').addClass('hide');
                            $('.chain-stop-button').removeClass('hide').prop("disabled", false);
                            $(".chain-reset-button").prop("disabled", true);

                            setTimeout(function () {
                                _chain.updateActiveCheck($('#activeChainCheck').data('url'));
                            }, 5000);
                        });
                        break;

                    case "stop":
                        _chain.stop(tId, url, function () {
                            $('.chain-start-button').removeClass("hide").prop("disabled", false);
                            $('.chain-stop-button').addClass("hide");
                            $(".chain-reset-button").prop("disabled", false);
                        });
                        break;

                    case "reset":
                        _chain.reset(tId, url, function () {
                            $(".chain-start-button, .chain-stop-button, .chain-reset-button").prop("disabled", false);
                            $('#activeChainCheck').addClass('hide');
                        });
                        break;

                    default:
                        console.error("Invalid chain control action.");
                }
            };

            /**
             * Start target check chain
             * @param targetId
             * @param url
             * @param successCallback
             */
            this.start = function (targetId, url, successCallback) {
                $('.loader-image').show();

                $.ajax({
                    dataType: 'json',
                    url: url,
                    timeout: system.ajaxTimeout,
                    type: 'POST',
                    data: {
                        "EntryControlForm[id]"        : targetId,
                        "EntryControlForm[operation]" : 'start',
                        "YII_CSRF_TOKEN"               : system.csrf
                    },

                    success : function (data, textStatus) {
                        $('.loader-image').hide();

                        if (data.status == 'error') {
                            system.addAlert('error', data.errorText);

                            return;
                        }

                        $(".chain-start-button").addClass("hide");
                        $(".chain-stop-button").removeClass("hide");

                        if (successCallback) {
                            successCallback();
                        }
                    },

                    error : function(jqXHR, textStatus, e) {
                        $('.loader-image').hide();
                        system.addAlert('error', system.translate('Request failed, please try again.'));
                    }
                });
            };

            /**
             * Stop target check chain
             * @param targetId
             * @param url
             * @param successCallback
             */
            this.stop = function (targetId, url, successCallback) {
                $('.loader-image').show();

                $.ajax({
                    dataType: 'json',
                    url: url,
                    timeout: system.ajaxTimeout,
                    type: 'POST',
                    data: {
                        "EntryControlForm[id]"        : targetId,
                        "EntryControlForm[operation]" : 'stop',
                        "YII_CSRF_TOKEN"               : system.csrf
                    },

                    success : function (data, textStatus) {
                        $('.loader-image').hide();

                        if (data.status == 'error') {
                            system.addAlert('error', data.errorText);

                            return;
                        }

                        if (successCallback) {
                            successCallback();
                        }
                    },

                    error : function(jqXHR, textStatus, e) {
                        $('.loader-image').hide();
                        system.addAlert('error', system.translate('Request failed, please try again.'));
                    }
                });
            };

            /**
             * Stop target check chain
             * @param targetId
             * @param url
             * @param successCallback
             */
            this.reset = function (targetId, url, successCallback) {
                $('.loader-image').show();
                $(".chain-reset-button, .chain-start-button, .chain-stop-button").prop("disabled", true);

                $.ajax({
                    dataType: 'json',
                    url: url,
                    timeout: system.ajaxTimeout,
                    type: 'POST',
                    data: {
                        "EntryControlForm[id]"        : targetId,
                        "EntryControlForm[operation]" : 'reset',
                        "YII_CSRF_TOKEN"               : system.csrf
                    },

                    success : function (data, textStatus) {
                        $('.loader-image').hide();

                        if (data.status == 'error') {
                            system.addAlert('error', data.errorText);
                        }

                        if (successCallback) {
                            successCallback();
                        }
                    },

                    error : function(jqXHR, textStatus, e) {
                        $('.loader-image').hide();
                        system.addAlert('error', system.translate('Request failed, please try again.'));
                    }
                });
            };

            /**
             * Show chain messages
             */
            this.messages = function (url) {
                var currentTarget = parseInt($(".relations-graph").data("target-id"));
                $('.loader-image').show();

                $.ajax({
                    dataType: "json",
                    url: url,
                    timeout: system.ajaxTimeout,
                    type: "POST",
                    data: {
                        "YII_CSRF_TOKEN": system.csrf
                    },

                    "success": function (response) {
                        var status = parseInt(response.data.status);
                        var check = response.data.check;
                        var messages = response.data.messages;
                        var currentTarget = parseInt($(".relations-graph").data("target-id"));

                        if (status == system.constants.Target.CHAIN_STATUS_ACTIVE && Object.keys(check).length) {
                            user.mxgraph.setCheckActive(check);
                        } else {
                            user.mxgraph.activeCheck = null;
                            user.mxgraph.refreshChecks();

                            $("#activeChainCheck").addClass("hide");
                            $(".chain-start-button").removeClass("hide");
                            $(".chain-stop-button").addClass("hide");
                        }

                        $.each(messages, function (key, value) {
                            var status = parseInt(value.status);
                            var id = parseInt(value.id);
                            var message = value.message;

                            // Chain is idle
                            if (status == 0 && id == currentTarget) {
                                $(".chain-reset-button, .chain-start-button, .chain-stop-button").prop("disabled", false);
                            }

                            system.addAlert("success", message);

                            if (id) {
                                switch (status) {
                                    case system.constants.Target.CHAIN_STATUS_IDLE:
                                    case system.constants.Target.CHAIN_STATUS_STOPPED:
                                    case system.constants.Target.CHAIN_STATUS_INTERRUPTED:
                                        $(".chain-start-button").removeClass("hide");
                                        $(".chain-stop-button").addClass("hide");

                                        break;

                                    case system.constants.Target.CHAIN_STATUS_ACTIVE:
                                        $(".chain-start-button").addClass("hide");
                                        $(".chain-stop-button").removeClass("hide");

                                        break;

                                    default:
                                        break;
                                }
                            }
                        });

                        $(".loader-image").hide();
                    },

                    "error" : function (data) {
                        $(".loader-image").hide();
                        system.addAlert("error", system.translate("Request failed, please try again."));
                    },

                    "beforeSend" : function () {
                        $(".loader-image").show();
                    }
                });
            };
        };
    };

    /**
     * Time session
     */
    this.timesession = new function () {
        var _timesession = this;

        this.refreshTimeout  = 5000; // One minute
        this.counterInterval = 1000; // Update counter values every second

        /**
         * Show project list
         */
        this.showProjectList = function () {
            $('#time-session-project-select').modal("show");
        };

        /**
         * Checks if project selected
         * @returns {Number|length|*|jQuery}
         */
        this.projectSelected = function () {
            return $('.time-session-project').val() != '0';
        };

        /**
         * Start time session
         * @param url
         */
        this.start = function (url, callback) {
            var modal = $('#time-session-project-select');

            if (_timesession.projectSelected()) {
                var projectId = $('.time-session-project').val();

                $.ajax({
                    dataType : 'json',
                    url      : url,
                    timeout  : system.ajaxTimeout,
                    type     : 'POST',
                    data     : {
                        "EntryControlForm[id]"        : projectId,
                        "EntryControlForm[operation]" : 'start',
                        "YII_CSRF_TOKEN"               : system.csrf
                    },

                    success : function (data, textStatus) {
                        $('.loader-image').hide();

                        if (data.status == 'error') {
                            system.addAlert('error', data.errorText);
                            return;
                        }

                        data = data.data;

                        modal.modal("hide");

                        $(".start-control").addClass('hide');
                        $(".stop-control").removeClass('hide');

                        if (callback) {
                            callback(data);
                        } else {
                            location.reload();
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
            } else {
                _timesession.showProjectList();
            }
        };

        /**
         * Stop time session
         * @param url
         * @param callback
         */
        this.stop = function (url, callback) {
            if (_timesession.projectSelected()) {
                var projectId = $('.time-session-project').val();

                $.ajax({
                    dataType : 'json',
                    url      : url,
                    timeout  : system.ajaxTimeout,
                    type     : 'POST',
                    data     : {
                        "EntryControlForm[id]"        : projectId,
                        "EntryControlForm[operation]" : "stop",
                        "YII_CSRF_TOKEN"              : system.csrf
                    },

                    success : function (data, textStatus) {
                        $('.loader-image').hide();

                        if (data.status == 'error') {
                            system.addAlert('error', data.errorText);
                            return;
                        }

                        data = data.data;

                        $(".stop-control").addClass('hide');
                        $(".start-control").removeClass('hide');

                        if (callback) {
                            callback(data);
                        } else {
                            location.reload();
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
            }
        };

        /**
         * Start time session counter
         */
        this.startCounter = function () {
            setInterval(function () {
                var seconds = parseInt($('.counter').find('.seconds').text());
                var minutes = parseInt($('.counter').find('.minutes').text());
                var hours   = parseInt($('.counter').find('.hours').text());

                seconds++;

                if (seconds == 60) {
                    seconds = 0;
                    minutes++;
                }

                if (minutes == 60) {
                    minutes = 0;
                    hours++;
                }

                if (hours == 24) {
                    seconds = 0;
                    minutes = 0;
                    hours   = 0;
                }

                $('.counter').find('.seconds').text(("0" + seconds).slice(-2));
                $('.counter').find('.minutes').text(("0" + minutes).slice(-2));
                $('.counter').find('.hours').text(("0" + hours).slice(-2));

            }, _timesession.counterInterval);
        };
    };

    /**
     * MxGraph object
     */
    this.mxgraph = new function () {
        var _mxgraph = this;

        /**
         * Cell types
         * @type {string}
         */
        this.CELL_TYPE_CHECK = 'check';
        this.CELL_TYPE_FILTER = 'filter';

        /**
         * Check & filter lists
         * @type {null}
         */
        this.checkCategories = [];
        this.filters         = [];

        /**
         * MxGraph objects handlers
         * @type {null}
         */
        this.editor     = null;
        this.properties = null;

        /**
         * Current target
         * @type {null}
         */
        this.target = null;

        /**
         * Current running check
         * @type {null}
         */
        this.activeCheck = null;

        /**
         * Returns all graph cells
         * @returns {*}
         */
        this.getAllCells = function () {
            return _mxgraph.editor.graph.getChildVertices(_mxgraph.editor.graph.getDefaultParent())
        };

        /**
         * Check graph object handler
         * @param state
         */
        this.mxCheckHandler = function (state) {
            mxVertexHandler.apply(this, arguments);

            _mxgraph.updateCheckStyles(state.cell);
        };

        /**
         * Refresh check cell styles
         * @param cell
         */
        this.updateCheckStyles = function (cell) {
            var checkTied = parseInt(cell.getAttribute('check_id'));
            var stopper = parseInt(cell.getAttribute('stopped'));
            var startCheck = parseInt(cell.getAttribute('start_check'));

            cell.delStyle();

            if (!checkTied) {
                cell.setNoCheck();
            }

            if (stopper) {
                cell.setStopped();
            }

            if (startCheck) {
                cell.setStart();
            }

            if (cell.id == _mxgraph.activeCheck) {
                if (startCheck) {
                    cell.setActiveStart();
                } else {
                    cell.setActive();
                }
            }

            _mxgraph.editor.graph.refresh();
        };

        /**
         * Filter graph object handler
         * @param state
         */
        this.mxFilterHandler = function (state) {
            mxVertexHandler.apply(this, arguments);
        };

        /**
         * Common cell handler init
         */
        this.mxCellHandlerInit = function () {
            mxVertexHandler.prototype.init.apply(this, arguments);

            this.domNode = document.createElement('div');
            this.domNode.style.position = 'absolute';
            this.domNode.style.whiteSpace = 'nowrap';
            var cell = _mxgraph.editor.graph.getSelectionCell();

            if (cell.getAttribute("type") == _mxgraph.CELL_TYPE_CHECK) {
                _mxgraph.mxCheckHandlerInit.call(this);
            } else if (cell.getAttribute("type") == _mxgraph.CELL_TYPE_FILTER) {
                _mxgraph.mxFilterHandlerInit.call(this);
            }
        };

        /**
         * Init mxCheckHandler
         */
        this.mxCheckHandlerInit = function () {
            var md = (mxClient.IS_TOUCH) ? 'touchstart' : 'mousedown';

            // Settings
            var img = mxUtils.createImage('/js/mxgraph/grapheditor/images/settings.png');
            img.style.cursor = 'pointer';
            img.style.width = '16px';
            img.style.height = '16px';
            mxEvent.addListener(img, md, mxUtils.bind(this, function(evt) {
                // Disables dragging the image
                mxEvent.consume(evt);
            }));

            mxEvent.addListener(img, 'click', mxUtils.bind(this, function(evt) {
                var graph = _mxgraph.editor.graph;
                var cell = graph.getSelectionCell();
                var bounds = this.graph.getCellBounds(cell);

                if (_mxgraph.properties) {
                    _mxgraph.properties.destroy();
                }

                var graphOffset = mxUtils.getOffset(_mxgraph.editor.graph.container);
                var x = graphOffset.x + 10;
                var y = graphOffset.y;

                if (bounds != null)
                {
                    x += bounds.x + Math.min(100, bounds.width);
                    y += bounds.y;
                }

                var form = $('<div>');

                var checkId = cell.getAttribute("check_id");
                var controlId = cell.getAttribute("control_id");
                var categoryId = cell.getAttribute("category_id");

                var $properties, $categoryList, $controlList, $checkList;

                var $categories = $('<select>')
                    .addClass('category-list')
                    .addClass('max-width')
                    .append(
                    $('<option>')
                        .attr('value', '0')
                        .text('Select category...')
                )
                    .change(function() {
                        var category = $(this).val();
                        $properties = $(_mxgraph.properties.getElement());
                        $categoryList = $(this);
                        $controlList = $properties.find('.control-list');
                        $checkList = $properties.find('.check-list');

                        $categoryList.addClass('disabled');
                        $controlList
                            .addClass('disabled')
                            .find('option[value=0]')
                            .siblings()
                            .remove();

                        $checkList
                            .addClass('disabled')
                            .find('option[value=0]')
                            .siblings()
                            .remove();

                        if (parseInt(category)) {
                            system.control.loadObjects(category, "category-control-list", function (data) {
                                var controls = data.objects;

                                $.each(controls, function (key, value) {
                                    $controlList.append(
                                        $('<option>')
                                            .attr("value", value.id)
                                            .text(value.name)
                                    );
                                });

                                $categoryList.removeClass('disabled');
                                $controlList.removeClass('disabled');
                            });
                        } else {
                            $categoryList.removeClass('disabled');
                            $controlList.addClass('disabled');
                        }
                    });

                var $controls = $('<select>')
                    .addClass('control-list')
                    .addClass('max-width')
                    .addClass('disabled')
                    .append(
                    $('<option>')
                        .attr('value', '0')
                        .text('Select control...')
                )
                    .change(function () {
                        var control = $(this).val();
                        $properties = $(_mxgraph.properties.getElement());
                        $categoryList = $properties.find('.category-list');
                        $checkList = $properties.find('.check-list');
                        $controlList = $properties.find('.control-list');

                        $categoryList.addClass('disabled');
                        $controlList.addClass('disabled');
                        $checkList
                            .addClass('disabled')
                            .find('option[value=0]')
                            .siblings()
                            .remove();

                        if (parseInt(control)) {
                            system.control.loadObjects(control, 'control-check-list', function (data) {
                                $.each(data.objects, function (key, value) {
                                    $checkList.append(
                                        $('<option>')
                                            .attr('value', value.id)
                                            .text(value.name)
                                    );
                                });

                                $categoryList.removeClass('disabled');
                                $controlList.removeClass('disabled');
                                $checkList.removeClass('disabled');
                            });
                        } else {
                            $categoryList.removeClass('disabled');
                            $controlList.removeClass('disabled');
                            $checkList.addClass('disabled');
                        }
                    });

                var $checks = $('<select>')
                    .addClass('check-list')
                    .addClass('max-width')
                    .addClass('disabled')
                    .append(
                    $('<option>')
                        .attr('value', '0')
                        .text('Select check...')
                );

                var $okButton = $('<button>')
                    .text('OK')
                    .click(function () {
                        $properties = $(_mxgraph.properties.getElement());
                        $categoryList = $properties.find('.category-list');
                        $controlList = $properties.find('.control-list');
                        $checkList = $properties.find('.check-list');

                        var category = $categoryList.val();
                        var control = $controlList.val();
                        var check = $checkList.val();
                        var checkName;

                        if (parseInt(category) && parseInt(control) && parseInt(check)) {
                            checkName = $checkList.find('option[value=' + check + ']').text();

                            cell.setAttribute("category_id", category);
                            cell.setAttribute("control_id", control);
                            cell.setAttribute("check_id", check);
                            cell.setAttribute("label", checkName);

                            cell.delStyle();

                            _mxgraph.editor.graph.refresh();
                            _mxgraph.properties.destroy();
                            user.relationTemplate.updateRelations();
                        }
                    });

                var $cancelButton = $('<button>')
                    .text('Cancel')
                    .click(function () {
                        _mxgraph.properties.destroy();
                    });

                form.append($categories, $controls, $checks, $okButton, $cancelButton);

                _mxgraph.properties = new mxWindow("Check Properties", form[0], x, y, 250, 170, false);
                _mxgraph.properties.setVisible(true);

                $properties = $(_mxgraph.properties.getElement());
                $categoryList = $properties.find('.category-list');
                $checkList = $properties.find('.check-list');
                $controlList = $properties.find('.control-list');

                $.each(_mxgraph.checkCategories, function (key, value) {
                    var option = $('<option>')
                        .attr('value', value.id)
                        .text(value.name);

                    if (parseInt(categoryId) && parseInt(controlId) && parseInt(checkId) && value.id == categoryId) {
                        option.attr('selected', 'selected');

                        system.control.loadObjects(categoryId, "category-control-list", function (data) {
                            var controls = data.objects;

                            $.each(controls, function (key, value) {
                                var option = $('<option>')
                                    .attr("value", value.id)
                                    .text(value.name);

                                if (value.id == controlId) {
                                    option.attr('selected', 'selected');
                                }

                                $controlList.append(option);
                            });

                            system.control.loadObjects(controlId, 'control-check-list', function (data) {
                                $.each(data.objects, function (key, value) {
                                    var option = $('<option>')
                                        .attr('value', value.id)
                                        .text(value.name);

                                    if (value.id == checkId) {
                                        option.attr('selected', 'selected');
                                    }

                                    $checkList.append(option);
                                });

                                $categoryList.removeClass('disabled');
                                $controlList.removeClass('disabled');
                                $checkList.removeClass('disabled');
                            });
                        });
                    }

                    $categories.append(option);
                });
            }));

            this.domNode.appendChild(img);

            // Delete button
            img = mxUtils.createImage('/js/mxgraph/grapheditor/images/delete.gif');
            img.style.cursor = 'pointer';
            img.style.width = '16px';
            img.style.height = '16px';
            mxEvent.addListener(img, md,
                mxUtils.bind(this, function(evt)
                {
                    // Disables dragging the image
                    mxEvent.consume(evt);
                })
            );

            mxEvent.addListener(img, 'click', mxUtils.bind(this, function(evt)
            {
                _mxgraph.editor.graph.removeCells([this.state.cell]);
                mxEvent.consume(evt);
            }));

            this.domNode.appendChild(img);
            _mxgraph.editor.graph.container.appendChild(this.domNode);
            this.redrawTools();
        };

        /**
         * Init mxFilterHandler
         */
        this.mxFilterHandlerInit = function () {
            var md = (mxClient.IS_TOUCH) ? 'touchstart' : 'mousedown';

            // Settings
            var img = mxUtils.createImage('/js/mxgraph/grapheditor/images/settings.png');
            img.style.cursor = 'pointer';
            img.style.width = '16px';
            img.style.height = '16px';
            mxEvent.addListener(img, md, mxUtils.bind(this, function(evt) {
                // Disables dragging the image
                mxEvent.consume(evt);
            }));

            mxEvent.addListener(img, 'click', mxUtils.bind(this, function(evt) {
                var graph = _mxgraph.editor.graph;
                var cell = graph.getSelectionCell();
                var bounds = this.graph.getCellBounds(cell);
                var model = graph.getModel();

                if (_mxgraph.properties) {
                    _mxgraph.properties.destroy();
                }

                var graphOffset = mxUtils.getOffset(_mxgraph.editor.graph.container);
                var x = graphOffset.x + 10;
                var y = graphOffset.y;

                if (bounds != null)
                {
                    x += bounds.x + Math.min(100, bounds.width);
                    y += bounds.y;
                }

                var form = $('<div>');
                var current = cell.getAttribute("filter_name");

                var filters = $('<select>')
                    .addClass('filter_name')
                    .addClass('max-width')
                    .append(
                    $('<option>')
                        .attr('value', '0')
                        .text('Select filter...')
                );

                $.each(_mxgraph.filters, function (key, value) {
                    var option = $('<option>')
                        .attr('value', value.name)
                        .text(value.title);

                    if (value.name == current) {
                        option.attr('selected', 'selected');
                    }

                    filters.append(option);
                });

                var values = $('<input>')
                    .addClass('filter_values')
                    .addClass('max-width')
                    .attr("placeholder", "Enter filter values here...")
                    .val(cell.getAttribute("filter_values"));

                var okFunction = function (cell) {
                    var name = $('.mxWindow .filter_name' ).val();
                    var title = $('.mxWindow .filter_name option:selected' ).text();
                    var values = $('.mxWindow .filter_values').val();

                    if (name != '0' && values) {
                        cell.setAttribute('filter_name', name);
                        cell.setAttribute('label', title);
                        cell.setAttribute("filter_values", values);

                        graph.refresh();
                        _mxgraph.properties.destroy();
                        user.relationTemplate.updateRelations();
                    }
                };

                var cancelFunction = function () {
                    _mxgraph.properties.destroy();
                };

                var buttons = $('<div>')
                    .append(
                    $('<button>')
                        .text('OK')
                        .click(function () {
                            okFunction(cell);
                        }),
                    $('<button>')
                        .text('Cancel')
                        .click(cancelFunction)
                );

                form.append(filters, values, buttons);

                _mxgraph.properties = new mxWindow("Filter Properties", form[0], x, y, 230, 120, false);
                _mxgraph.properties.setVisible(true);
            }));

            this.domNode.appendChild(img);

            // Delete button
            img = mxUtils.createImage('/js/mxgraph/grapheditor/images/delete.gif');
            img.style.cursor = 'pointer';
            img.style.width = '16px';
            img.style.height = '16px';
            mxEvent.addListener(img, md,
                mxUtils.bind(this, function(evt)
                {
                    // Disables dragging the image
                    mxEvent.consume(evt);
                })
            );

            mxEvent.addListener(img, 'click', mxUtils.bind(this, function(evt)
                {
                    _mxgraph.editor.graph.removeCells([this.state.cell]);
                    mxEvent.consume(evt);
                })
            );

            this.domNode.appendChild(img);
            _mxgraph.editor.graph.container.appendChild(this.domNode);
            this.redrawTools();
        };

        /**
         * Init
         * @param editor
         */
        this.init = function (editor) {
            _mxgraph.editor = editor;
            _mxgraph.editor.graph.setConnectable(true);
            _mxgraph.editor.graph.connectionHandler.createTarget = true;
            _mxgraph.editor.graph.createHandler = function (state) {
                if (state != null && this.model.isVertex(state.cell)) {
                    if (state.cell.isCheck()) {
                        return new _mxgraph.mxCheckHandler(state);
                    } else if (state.cell.isFilter()) {
                        return new _mxgraph.mxFilterHandler(state);
                    }
                }

                return mxGraph.prototype.createHandler.apply(this, arguments);
            };

            _mxgraph.mxCheckHandler.prototype = new mxVertexHandler();
            _mxgraph.mxCheckHandler.prototype.constructor = _mxgraph.mxCheckHandler;
            _mxgraph.mxCheckHandler.prototype.domNode = null;
            _mxgraph.mxCheckHandler.prototype.init = _mxgraph.mxCellHandlerInit;

            _mxgraph.mxCheckHandler.prototype.redraw = function() {
                mxVertexHandler.prototype.redraw.apply(this);
                this.redrawTools();
            };

            _mxgraph.mxCheckHandler.prototype.redrawTools = function () {
                if (this.state != null && this.domNode != null) {
                    var dy = (mxClient.IS_VML && document.compatMode == 'CSS1Compat') ? 20 : 4;
                    this.domNode.style.left = (this.state.x + this.state.width - 30) + 'px';
                    this.domNode.style.top = (this.state.y - 20 - dy) + 'px';
                }
            };

            _mxgraph.mxCheckHandler.prototype.destroy = function (sender, me) {
                mxVertexHandler.prototype.destroy.apply(this, arguments);

                if (this.domNode != null) {
                    this.domNode.parentNode.removeChild(this.domNode);
                    this.domNode = null;
                }
            };

            _mxgraph.mxFilterHandler.prototype = new mxVertexHandler();
            _mxgraph.mxFilterHandler.prototype.constructor = _mxgraph.mxFilterHandler;
            _mxgraph.mxFilterHandler.prototype.domNode = null;
            _mxgraph.mxFilterHandler.prototype.init = _mxgraph.mxCellHandlerInit;

            _mxgraph.mxFilterHandler.prototype.redraw = function() {
                mxVertexHandler.prototype.redraw.apply(this);
                this.redrawTools();
            };

            _mxgraph.mxFilterHandler.prototype.redrawTools = function () {
                if (this.state != null && this.domNode != null) {
                    var dy = (mxClient.IS_VML && document.compatMode == 'CSS1Compat') ? 20 : 4;
                    this.domNode.style.left = (this.state.x + this.state.width - 30) + 'px';
                    this.domNode.style.top = (this.state.y - 20 - dy) + 'px';
                }
            };

            _mxgraph.mxFilterHandler.prototype.destroy = function (sender, me) {
                mxVertexHandler.prototype.destroy.apply(this, arguments);

                if (this.domNode != null) {
                    this.domNode.parentNode.removeChild(this.domNode);
                    this.domNode = null;
                }
            };

            // Crisp rendering in SVG except for connectors, actors, cylinder, ellipses
            mxShape.prototype.crisp = true;
            mxActor.prototype.crisp = false;
            mxCylinder.prototype.crisp = false;
            mxEllipse.prototype.crisp = false;
            mxDoubleEllipse.prototype.crisp = false;
            mxConnector.prototype.crisp = false;

            // Enables guides
            mxGraphHandler.prototype.guidesEnabled = true;

            // Alt disables guides
            mxGuide.prototype.isEnabledForEvent = function(evt)
            {
                return !mxEvent.isAltDown(evt);
            };

            // Enables snapping waypoints to terminals
            mxEdgeHandler.prototype.snapToTerminals = true;

            // Defines an icon for creating new connections in the connection handler.
            // This will automatically disable the highlighting of the source vertex.
            mxConnectionHandler.prototype.connectImage = new mxImage('/js/mxgraph/grapheditor/images/connector.gif', 16, 16);

            // Enables connections in the graph and disables
            // reset of zoom and translate on root change
            // (ie. switch between XML and graphical mode).
            _mxgraph.editor.graph.setConnectable(true);

            _mxgraph.editor.graph.getModel().addListener(mxEvent.CHANGE, function () {
                user.relationTemplate.updateRelations();
            });

            // Create select actions in page
            var node = document.getElementById('zoomActions');
            mxUtils.write(node, 'Zoom: ');
            mxUtils.linkAction(node, 'In', _mxgraph.editor, 'zoomIn');
            mxUtils.write(node, ', ');
            mxUtils.linkAction(node, 'Out', _mxgraph.editor, 'zoomOut');
            mxUtils.write(node, ', ');
            mxUtils.linkAction(node, 'Actual', _mxgraph.editor, 'actualSize');
            mxUtils.write(node, ', ');
            mxUtils.linkAction(node, 'Fit', _mxgraph.editor, 'fit');
        };

        /**
         * Build graph by XML
         * @param xml
         */
        this.buildByXML = function (xml) {
            var doc = mxUtils.parseXml(xml);
            var dec = new mxCodec(doc);
            dec.decode(doc.documentElement, _mxgraph.editor.graph.getModel());

            _mxgraph.refreshChecks();
        };

        /**
         * Refresh graph
         */
        this.refreshChecks = function () {
            $.each(_mxgraph.getAllCells(), function (key, cell) {
                if (cell.getAttribute('type') == _mxgraph.CELL_TYPE_CHECK) {
                    _mxgraph.updateCheckStyles(cell);
                }
            });
        };

        /**
         * Returns target check url
         * @param url
         * @param target
         * @param check
         * @param callback
         */
        this.getCheckLink = function (url, target, check, callback) {
            $.ajax({
                dataType: "json",
                url: url,
                timeout: system.ajaxTimeout,
                type: "POST",

                data: {
                    YII_CSRF_TOKEN: system.csrf,
                    'TargetCheckLinkForm[target]' : target,
                    'TargetCheckLinkForm[check]'  : check
                },

                success: function (data, textStatus) {
                    $('.loader-image').hide();

                    if (data.status == "error") {
                        system.addAlert("error", data.errorText);
                        return;
                    }

                    url = data.data.url;

                    window.location.replace(url);

                    if (callback) {
                        callback(url);
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
         * Set cell as active check
         * @param check
         */
        this.setCheckActive = function (check) {
            var activeChainCheck = $("#activeChainCheck");
            activeChainCheck.removeClass("hide");
            activeChainCheck.find(".check-name").text(check["name"]);
            _mxgraph.activeCheck = check.id;
            _mxgraph.refreshChecks();
        };
    };

    /**
     * Relation Template object
     */
    this.relationTemplate = new function () {
        /**
         * Set XML node of mxGraph to form input field
         * @returns {boolean}
         */
        this.updateRelations = function () {
            var editor = user.mxgraph.editor;

            if (!editor) {
                return false;
            }

            var enc = new mxCodec();
            var node = enc.encode(editor.graph.getModel());

            $('.relations-form-input').val(new XMLSerializer().serializeToString(node));
        };
    };
}

var user = new User();
