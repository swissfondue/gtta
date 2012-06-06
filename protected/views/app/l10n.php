$(function () {
    var _messages = {
        'Request failed, please try again.'                 : '<?php echo Yii::t('app', 'Request failed, please try again.'); ?>',
        'Are you sure that you want to delete this object?' : '<?php echo Yii::t('app', 'Are you sure that you want to delete this object?'); ?>',
        'Object successfully deleted.'                      : '<?php echo Yii::t('app', 'Object successfully deleted.'); ?>',
        'Delete'                                            : '<?php echo Yii::t('app', 'Delete'); ?>'
    };

    system.l10nMessages = _messages;
});