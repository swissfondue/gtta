/**
 * System class.
 */
function System()
{
    var _self = this;

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

        $('html, body').animate({scrollTop:0}, 'fast', function () {
            $('.message-container > div').fadeIn('slow');

            if (_self._messageTimeout)
                clearTimeout(_self._messageTimeout);

            _self._messageTimeout = setTimeout(function () {
                $('.message-container > div').fadeOut('slow');
                _self._messageTimeout = null;
            }, _self.messageTimeout);
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
        if (sourceString in _self.l10nMessages)
            return _self.l10nMessages[sourceString];

        return sourceString;
    };
}

var system = new System();
