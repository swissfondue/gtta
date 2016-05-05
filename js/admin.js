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
     * Relation Template object
     */
    this.relationTemplate = new function () {
        /**
         * Set XML node of mxGraph to form input field
         * @returns {boolean}
         */
        this.updateRelations = function () {
            var editor = admin.mxgraph.editor;

            if (!editor) {
                return false;
            }

            var enc = new mxCodec();
            var node = enc.encode(editor.graph.getModel());

            $('.relations-form-input').val(new XMLSerializer().serializeToString(node));
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

            cell.delStyle();

            if (!checkTied) {
                cell.setStyle("noCheckSelectedStyle");
            }

            if (stopper) {
                cell.setStyle("stoppedCellStyle");
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

            // Started check icon
            if (this.state.cell.isStartCheck()) {
                img = mxUtils.createImage('/js/mxgraph/grapheditor/images/play.png');
                img.style.width = '16px';
                img.style.height = '16px';

                this.domNode.appendChild(img);
            }

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
                            admin.relationTemplate.updateRelations();
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
                        admin.relationTemplate.updateRelations();
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

            // Check cell styles
            _mxgraph.editor.graph.getStylesheet().putCellStyle('stoppedCellStyle', stoppedCellStyle);
            _mxgraph.editor.graph.getStylesheet().putCellStyle('noCheckSelectedStyle', noCheckSelectedStyle);

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
                admin.relationTemplate.updateRelations();
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
    };
}

var admin = new Admin();
