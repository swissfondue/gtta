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

        /**
         * Save the check.
         */
        this.save = function (id, goToNext) {
            var row, inputs, override, protocol, port, result, solutions, rating, data, url, nextRow;

            row = $('tr.content[data-id="' + id + '"]');
            url = row.attr('data-save-url');

            inputs = $('textarea[name^="TargetCheckEditForm_' + id + '[inputs]"]', row).map(
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

            if (!rating)
                rating = undefined;

            data = {};

            data['TargetCheckEditForm_' + id + '[overrideTarget]'] = override;
            data['TargetCheckEditForm_' + id + '[protocol]']       = protocol;
            data['TargetCheckEditForm_' + id + '[port]']           = port;
            data['TargetCheckEditForm_' + id + '[result]']         = result;
            data['TargetCheckEditForm_' + id + '[rating]']         = rating;

            for (i = 0; i < inputs.length; i++)
                data[inputs[i].name] = inputs[i].value;

            for (i = 0; i < solutions.length; i++)
                data[solutions[i].name] = solutions[i].value;

            data['YII_CSRF_TOKEN'] = system.csrf;

            $.ajax({
                dataType : 'json',
                url      : url,
                timeout  : system.ajaxTimeout,
                type     : 'POST',
                data     : data,

                success : function (data, textStatus) {
                    $('.loader-image').hide();
                    row.removeClass('processing');

                    if (data.status == 'error')
                    {
                        system.showMessage('error', data.errorText);
                        return;
                    }

                    if (rating)
                        $('tr.header[data-id="' + id + '"] > td.status').html('<span class="label ' + (ratings[rating].class ? ratings[rating].class : '') + '">' + ratings[rating].text + '</span>');

                    row.hide();

                    if (goToNext)
                    {
                        nextRow = $('tr.content[data-id="' + id + '"] + tr + tr');

                        if (nextRow)
                            nextRow.show();
                    }
                },

                error : function(jqXHR, textStatus, e) {
                    $('.loader-image').hide();
                    row.removeClass('processing');
                    system.showMessage('error', system.translate('Request failed, please try again.'));
                },

                beforeSend : function (jqXHR, settings) {
                    $('.loader-image').show();
                    row.addClass('processing');
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
        this.initTargetCheckAttachmentUploadForms = function ()
        {
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

                        if ($('tr.content[data-id="' + id + '"] .attachment-list').length == 0)
                            $('tr.content[data-id="' + id + '"] .upload-message').after('<table class="table attachment-list"><tbody></tbody></table>');

                        $('tr.content[data-id="' + id + '"] .attachment-list > tbody').append(tr);
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
            var url = $('tr.header[data-id=' + id + ']').data('control-url');

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

                    if (operation == 'start')
                    {
                        system.showMessage('success', system.translate('Check started.'));
                        location.reload();
                    }
                    else if (operation == 'reset')
                    {
                        system.showMessage('success', system.translate('Check reset.'));
                        location.reload();
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
         * Start check.
         */
        this.start = function (id) {
            _check._control(id, 'start');
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
