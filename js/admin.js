/**
 * Admin namespace.
 */
function Admin()
{
    /**
     * Category object.
     */
    this.category = new function () {
        var _categoryObj = this;

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
                        system.showMessage('error', data.errorText);
                        return;
                    }

                    if (operation == 'delete')
                    {
                        $('tr[data-id=' + id + ']').fadeOut('slow', undefined, function () {
                            $('tr[data-id=' + id + ']').remove();
                            system.showMessage('success', system.translate('Category successfully deleted.'));

                            if ($('.category-list > tbody > tr').length == 1)
                            {
                                $('.category-list').remove();
                                $('.span8').append(system.translate('No categories yet.'));
                            }
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
         * Delete.
         */
        this.del = function (id) {
            if (confirm(system.translate('Are you sure that you want to delete this category?')))
                _categoryObj._control(id, 'delete');
        };
    };

    /**
     * Check object.
     */
    this.check = new function () {
        var _checkObj = this;

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
                        system.showMessage('error', data.errorText);
                        return;
                    }

                    if (operation == 'delete')
                    {
                        $('tr[data-id=' + id + ']').fadeOut('slow', undefined, function () {
                            $('tr[data-id=' + id + ']').remove();
                            system.showMessage('success', system.translate('Check successfully deleted.'));

                            if ($('.check-list > tbody > tr').length == 1)
                            {
                                $('.check-list').remove();
                                $('.span8').append(system.translate('No checks yet.'));
                            }
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
         * Delete.
         */
        this.del = function (id) {
            if (confirm(system.translate('Are you sure that you want to delete this check?')))
                _checkObj._control(id, 'delete');
        };
    };

    /**
     * Check input object.
     */
    this.checkInput = new function () {
        var _checkInputObj = this;

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
                        system.showMessage('error', data.errorText);
                        return;
                    }

                    if (operation == 'delete')
                    {
                        $('tr[data-id=' + id + ']').fadeOut('slow', undefined, function () {
                            $('tr[data-id=' + id + ']').remove();
                            system.showMessage('success', system.translate('Input successfully deleted.'));

                            if ($('.input-list > tbody > tr').length == 1)
                            {
                                $('.input-list').remove();
                                $('.span8').append(system.translate('No inputs yet.'));
                            }
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
         * Delete.
         */
        this.del = function (id) {
            if (confirm(system.translate('Are you sure that you want to delete this input?')))
                _checkInputObj._control(id, 'delete');
        };
    };

    /**
     * Check solution object.
     */
    this.checkSolution = new function () {
        var _checkSolutionObj = this;

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
                        system.showMessage('error', data.errorText);
                        return;
                    }

                    if (operation == 'delete')
                    {
                        $('tr[data-id=' + id + ']').fadeOut('slow', undefined, function () {
                            $('tr[data-id=' + id + ']').remove();
                            system.showMessage('success', system.translate('Solution successfully deleted.'));

                            if ($('.solution-list > tbody > tr').length == 1)
                            {
                                $('.solution-list').remove();
                                $('.span8').append(system.translate('No solutions yet.'));
                            }
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
         * Delete.
         */
        this.del = function (id) {
            if (confirm(system.translate('Are you sure that you want to delete this solution?')))
                _checkSolutionObj._control(id, 'delete');
        };
    };

    /**
     * Check result object.
     */
    this.checkResult = new function () {
        var _checkResultObj = this;

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
                        system.showMessage('error', data.errorText);
                        return;
                    }

                    if (operation == 'delete')
                    {
                        $('tr[data-id=' + id + ']').fadeOut('slow', undefined, function () {
                            $('tr[data-id=' + id + ']').remove();
                            system.showMessage('success', system.translate('Result successfully deleted.'));

                            if ($('.result-list > tbody > tr').length == 1)
                            {
                                $('.result-list').remove();
                                $('.span8').append(system.translate('No results yet.'));
                            }
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
         * Delete.
         */
        this.del = function (id) {
            if (confirm(system.translate('Are you sure that you want to delete this result?')))
                _checkResultObj._control(id, 'delete');
        };
    };

    /**
     * User object.
     */
    this.user = new function () {
        var _userObj = this;

        /**
         * Display/hide client field.
         */
        this.toggleClientField = function () {
            if ($('#UserEditForm_role').val() == 'client')
                $('#client-input').show();
            else
                $('#client-input').hide();
        };

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
                        system.showMessage('error', data.errorText);
                        return;
                    }

                    if (operation == 'delete')
                    {
                        $('tr[data-id=' + id + ']').fadeOut('slow', undefined, function () {
                            $('tr[data-id=' + id + ']').remove();
                            system.showMessage('success', system.translate('User successfully deleted.'));

                            if ($('.user-list > tbody > tr').length == 1)
                            {
                                $('.user-list').remove();
                                $('.span8').append(system.translate('No users yet.'));
                            }
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
         * Delete.
         */
        this.del = function (id) {
            if (confirm(system.translate('Are you sure that you want to delete this user?')))
                _userObj._control(id, 'delete');
        };
    };
}

var admin = new Admin();
