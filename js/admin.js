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
        this.stop = function (targetId, checkId, guidedTest) {
            var id = targetId.toString() + '-' + checkId.toString();

            if (guidedTest) {
                id = 'gt-' + id;
            }

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
     * GT check object.
     */
    this.gtCheck = new function () {
        var _gtCheck = this;

        /**
         * Load types
         */
        this.loadTypes = function (obj, target) {
            var category = obj.val();

            target.find("option:gt(0)").remove();

            system.control.loadObjects(category, 'gt-type-list', function (data) {
                if (data && data.objects.length) {
                    for (var i = 0; i < data.objects.length; i++) {
                        var item = data.objects[i];
                        target.append($("<option>").attr("value", item.id).html(item.name));
                    }
                }
            });
        };

        /**
         * Load modules
         */
        this.loadModules = function (obj, target) {
            var type = obj.val();

            target.find("option:gt(0)").remove();

            system.control.loadObjects(type, 'gt-module-list', function (data) {
                if (data && data.objects.length) {
                    for (var i = 0; i < data.objects.length; i++) {
                        var item = data.objects[i];
                        target.append($("<option>").attr("value", item.id).html(item.name));
                    }
                }
            });
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
        this.regenerate = function (url) {
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
                            _package.regenerate(url);
                        }, 5000);
                    } else {
                        location.reload();
                    }
                },

                error: function(jqXHR, textStatus, e) {
                    $(".loader-image").hide();

                    if (textStatus == "timeout") {
                        setTimeout(function () {
                            _package.regenerate(url);
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
            $('#PackageEditForm_content').val('');

            if (val == '0') {
                $('#PackageEditForm_path')
                    .val('')
                    .closest('.control-group')
                    .show();
                $('#PackageEditForm_file').siblings('.del-button').hide();
            } else {
                $('#PackageEditForm_path')
                    .val(val)
                    .closest('.control-group')
                    .hide();
                $('#PackageEditForm_file').siblings('.del-button').show();
                this.loadFileContent();
            }
        };

        /**
         * Load package file content
         * @param package
         */
        this.loadFileContent = function () {
            var url = $('#PackageEditForm_file').data('url');
            var path = $('#PackageEditForm_file').val();

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

                    $('[id^=PackageEditForm]').removeAttr('disabled');
                    $('#PackageEditForm_content').val(content);

                    $(".loader-image").hide();
                },

                'error' : function (data) {
                    $('[id^=PackageEditForm]').removeAttr('disabled');
                    $(".loader-image").hide();
                    system.addAlert('error', system.translate('Request failed, please try again.'));
                },

                'beforeSend' : function () {
                    $('[id^=PackageEditForm]').attr('disabled', 'disabled');
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
                    $('#PackageEditForm_operation').val('save');
                    break;
                case 'delete':
                    if (!confirm(system.translate("Are you sure that you want to delete this object?"))) {
                        return;
                    }

                    $('#PackageEditForm_operation').val('delete');
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
                                    .attr("data-guided", option.guided)
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
                    var guided = $(elem).find("option:selected").data("guided");

                    if (!guided) {
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
                    } else {
                        system.control.loadObjects($(elem).val(), "gt-project-module-list", function (data) {
                            if (data && data.objects) {
                                moduleId.parent().parent().show();
                                targetId.parent().parent().hide();

                                var options = [];

                                for (var i = 0; i < data.objects.length; i++) {
                                    var option = data.objects[i];

                                    options.push($("<option></option>")
                                        .val(option.id)
                                        .html(option.name)
                                    );
                                }

                                moduleId.append(options);
                            }
                        });
                    }
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
}

var admin = new Admin();
