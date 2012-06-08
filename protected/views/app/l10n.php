$(function () {
    var _messages = {
        'Request failed, please try again.'                 : '<?php echo Yii::t('app', 'Request failed, please try again.'); ?>',
        'Are you sure that you want to delete this object?' : '<?php echo Yii::t('app', 'Are you sure that you want to delete this object?'); ?>',
        'Object deleted.'                                   : '<?php echo Yii::t('app', 'Object deleted.'); ?>',
        'Delete'                                            : '<?php echo Yii::t('app', 'Delete'); ?>',
        'Start'                                             : '<?php echo Yii::t('app', 'Start'); ?>',
        'Reset'                                             : '<?php echo Yii::t('app', 'Reset'); ?>',
        'Are you sure that you want to reset this check?'   : '<?php echo Yii::t('app', 'Are you sure that you want to reset this check?'); ?>',
        'Check reset.'                                      : '<?php echo Yii::t('app', 'Check reset.'); ?>',
        'Check started.'                                    : '<?php echo Yii::t('app', 'Check started.'); ?>'
    };

    system.l10nMessages = _messages;
});