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
                        system.showMessage('error', data.errorText);
                        callback();

                        return;
                    }

                    callback(data.data);
                },

                error : function(jqXHR, textStatus, e) {
                    $('.loader-image').hide();
                    _user._setLoaded();
                    system.showMessage('error', system.translate('Request failed, please try again.'));
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

                $(this).fileupload({
                    dataType             : 'json',
                    url                  : url,
                    forceIframeTransport : true,
                    timeout              : 120000,
                    formData             : data,

                    done : function (e, data) {
                        $('.loader-image').hide();
                        $('.upload-message').hide();
                        $('.file-input').show();

                        var json = data.result;

                        if (json.status == 'error')
                        {
                            system.showMessage('error', json.errorText);
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
                        $('.upload-message').hide();
                        $('.file-input').show();
                        system.showMessage('error', system.translate('Request failed, please try again.'));
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
                        system.showMessage('error', data.errorText);
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
                    system.showMessage('error', system.translate('Request failed, please try again.'));
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
            if (confirm(system.translate('Are you sure that you want to delete this object?')))
                _reportTemplate._controlHeaderImage(id, 'delete');
        };
    };

    /**
     * Client object.
     */
    this.client = new function () {
        var _client = this;

        /**
         * Initialize logo upload form.
         */
        this.initLogoUploadForm = function () {
            $('input[name^="ClientLogoUploadForm"]').each(function () {
                var url  = $(this).data('upload-url'),
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
                        $('.upload-message').hide();
                        $('.file-input').show();

                        var json = data.result;

                        if (json.status == 'error')
                        {
                            system.showMessage('error', json.errorText);
                            return;
                        }

                        data = json.data;

                        // refresh the image
                        var d = new Date();

                        if ($('.logo-image > img').length)
                            $('.logo-image > img').attr('src', data.url + '?' + d.getTime());
                        else
                            $('.logo-image').html('<img src="' + data.url + '?' + d.getTime() + '">');

                        $('.delete-logo-link').show();
                    },

                    fail : function (e, data) {
                        $('.loader-image').hide();
                        $('.upload-message').hide();
                        $('.file-input').show();
                        system.showMessage('error', system.translate('Request failed, please try again.'));
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
                        system.showMessage('error', data.errorText);
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
                    system.showMessage('error', system.translate('Request failed, please try again.'));
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
            if (confirm(system.translate('Are you sure that you want to delete this object?')))
                _client._controlLogo(id, 'delete');
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
                        system.showMessage('error', data.errorText);
                        return;
                    }

                    data = data.data;

                    if (operation == 'stop')
                        $('div.process-monitor[data-id="' + id + '"]').addClass('disabled');
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
         * Stop process.
         */
        this.stop = function (targetId, checkId) {
            _process._control(targetId.toString() + '-' + checkId.toString(), 'stop');
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
}

var admin = new Admin();
