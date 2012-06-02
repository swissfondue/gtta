/**
 * User namespace.
 */
function User()
{
    /**
     * Check object.
     */
    this.check = new function () {
        /**
         * Save the check.
         */
        this.save = function (id, goToNext) {
            var row, inputs, result, solutions, rating, data, url, nextRow;

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

            result = $('textarea[name="TargetCheckEditForm_' + id + '[result]"]', row).val(),

            solutions = $('input[name^="TargetCheckEditForm_' + id + '[solutions]"]:checked', row).map(
                function () {
                    return {
                        name  : $(this).attr('name'),
                        value : $(this).val()
                    }
                }
            ).get();

            rating = $('input[name="TargetCheckEditForm_' + id + '[rating]"]:checked', row).val();

            data = {};

            data['TargetCheckEditForm_' + id + '[result]'] = result;
            data['TargetCheckEditForm_' + id + '[rating]'] = rating;

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
    };
}

var user = new User();
