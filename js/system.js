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

    /**
     * Shows a message.
     */
    this.showMessage = function (eventType, message) {
        $('.message-container').html('');

        $('<div />', {
            'class' : 'alert alert-' + eventType + ' hidden-object',
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
                        _system.showMessage('error', data.errorText);
                        return;
                    }

                    if (operation == 'delete')
                    {
                        $('tr[data-id=' + id + ']').fadeOut('slow', undefined, function () {
                            $('tr[data-id=' + id + ']').remove();
                            _system.showMessage('success', system.translate('Object deleted.'));

                            if ($('table.table > tbody > tr').length == 1)
                                location.reload();
                        });
                    }
                },

                error : function(jqXHR, textStatus, e) {
                    $('.loader-image').hide();
                    _system.showMessage('error', system.translate('Request failed, please try again.'));
                },

                beforeSend : function (jqXHR, settings) {
                    $('.loader-image').show();
                }
            });
        };

        /**
         * Delete object.
         */
        this.del = function (id) {
            $('tr[data-id=' + id + ']').addClass('delete-row');

            if (confirm(_system.translate('Are you sure that you want to delete this object?')))
                _system_control._control(id, 'delete');
            else
                $('tr[data-id=' + id + ']').removeClass('delete-row');
        };
    };

    /**
     * Project object.
     */
    this.project = new function () {
        var _project = this;

        /**
         * Show export vulnerabilities form.
         */
        this.exportVulnForm = function () {
            $('input[name="ProjectVulnExportForm[header]"]').prop('checked', true);
            $('input[name="ProjectVulnExportForm[ratings][]"]').prop('checked', true);
            $('input[name="ProjectVulnExportForm[columns][]"]').prop('checked', true);

            $('#export-modal').modal();
        };

        /**
         * Export vulnerabilities form change.
         */
        this.exportVulnFormChange = function (e) {
            if ($('input[name="ProjectVulnExportForm[ratings][]"]:checked').length > 0 &&
                $('input[name="ProjectVulnExportForm[columns][]"]:checked').length > 0)
                $('#export-button').prop('disabled', false);
            else
                $('#export-button').prop('disabled', true);
        };

        /**
         * Export vulnerabilities form submit.
         */
        this.exportVulnFormSubmit = function () {
            $('#export-modal').modal('hide');
            $('#ProjectVulnExportForm').submit();
        };
    };

    /**
     * Effort object.
     */
    this.effort = new function () {
        var _effort = this;

        this.list = [];

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
                $('.form-actions > button[type="submit"]').prop('disabled', true);
        };

        /**
         * Form has been changed.
         */
        this.formChange = function (e) {
            var category, targets, valid;

            valid = false;

            category = $('#EffortEstimateForm_categoryId').val();
            targets  = parseInt($('#EffortEstimateForm_targets').val());

            if (category > 0 && targets > 0)
                valid = true;

            if (valid)
            {
                $('.form-actions > button[type="submit"]').prop('disabled', false);
                _effort._calculateEffort();
            }
            else
            {
                $('.form-actions > button[type="submit"]').prop('disabled', true);
                $('#checks').html('0');
                $('#estimated-effort').html('0');
                $('#EffortEstimateForm_effort').val(0);
            }
        };

        this._drawTable = function () {
            var item, tr, totalEffort, totalTargets;

            totalEffort  = 0;
            totalTargets = 0;

            $('table.effort-list > tbody').find('tr:gt(0)').remove();
            console.log(_effort.list.length);

            for (var i = 0; i < _effort.list.length; i++)
            {
                item = _effort.list[i];

                tr = '<tr data-id="' + item.id + '"><td class="name">' + item.name + '</td>' +
                    '<td class="targets">' + item.targets + '</td>' + '<td class="effort">' + item.effort + '</td>' +
                    '<td class="actions"><a href="#del" title="' + system.translate('Delete') +
                    '" onclick="system.effort.del(' + item.id + ')"><i class="icon icon-remove"></i></a></td</tr>'

                $('table.effort-list > tbody').append(tr);

                totalTargets += item.targets;
                totalEffort  += item.effort;
            }

            if (_effort.list.length > 0)
            {
                tr = '<tr><td class="name">' + system.translate('Total') + '</td><td class="targets">'  +
                    totalTargets + '</td><td class="effort" colspan="2">' + totalEffort + ' ' +
                    system.translate('minutes') + '</td></tr>';

                $('table.effort-list > tbody').append(tr);

                if (!$('.effort-list-container').is(':visible'))
                    $('.effort-list-container').slideDown('slow');

                $('.form-header').slideDown('slow');
                $('#print-button').show();
            }
            else
            {
                $('.effort-list-container').slideUp('slow');
                $('.form-header').slideUp('slow');
                $('#print-button').hide();
            }
        };

        /**
         * Add effort to the table.
         */
        this.add = function () {
            _effort.list.push({
                id      : $('#EffortEstimateForm_categoryId').val(),
                name    : $('#EffortEstimateForm_categoryId option:selected').text(),
                targets : parseInt($('#EffortEstimateForm_targets').val()),
                effort  : parseInt($('#EffortEstimateForm_effort').val())
            });

            _effort._drawTable();

            $('#EffortEstimateForm_categoryId option:selected').prop('disabled', true);

            // refresh the form
            $('#EffortEstimateForm_categoryId').val(0);
            $('#EffortEstimateForm_advanced').prop('checked', true);
            $('input[name="EffortEstimateForm[referenceIds][]"]').prop('checked', true);
            $('#EffortEstimateForm_targets').val(1);

            $('#checks').html('0');
            $('#estimated-effort').html('0');
            $('#EffortEstimateForm_effort').val(0);

            $('.form-actions > button[type="submit"]').prop('disabled', true);
        };

        /**
         * Delete effort from the table.
         */
        this.del = function (id) {
            for (var i = 0; i < _effort.list.length; i++)
                if (_effort.list[i].id == id)
                {
                    _effort.list.splice(i, 1);
                    break;
                }

            console.log(id);

            $('#EffortEstimateForm_categoryId option[value=' + id + ']').prop('disabled', false);

            $('tr[data-id=' + id + ']').addClass('delete-row');
            $('tr[data-id=' + id + ']').fadeOut('slow', undefined, function () {
                _effort._drawTable();
            });
        };

        /**
         * Print effort.
         */
        this.print = function () {
            var content, win;

            content = $('.effort-list-container > .row > .span8').html();
            content = content.replace(/<i class="icon icon-remove"><\/i>/g, '');

            win = window.open('', 'printWindow', 'location=0,status=0,width=620,height=500');

            win.document.writeln(
                '<!DOCTYPE html>' +
                '<html><head>' +
                '<title>' + system.translate('Estimated Effort') + '</title>' +
                '<meta charset="utf-8">' +
                '<link rel="stylesheet" type="text/css" href="/css/bootstrap.css">' +
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
