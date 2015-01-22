<div class="active-header">
    <div class="pull-right buttons">
        <div class="btn-group" data-toggle="buttons-radio">
            <button id="clear" class="btn" disabled="disabled" onclick="admin.job.clearLog('<?php echo $this->createUrl("monitor/controllog"); ?>');">
                <i class="icon icon-trash"></i>  <?php echo Yii::t("app", "Clear"); ?>
            </button>
        </div>
    </div>

    <h1><?php echo CHtml::encode($this->pageTitle); ?></h1>
</div>

<hr>

<form class="form-horizontal" id="BgLogForm">
    <fieldset>
        <div class="control-group">
            <label class="control-label" for="BgLogForm_job"><?php echo Yii::t('app', 'Process'); ?></label>
            <div class="controls">
                <select class="input-xlarge" id="BgLogForm_job" data-url="<?php print $this->createUrl('monitor/log'); ?>" data-control-url="<?php print $this->createUrl("monitor/controllog"); ?>" onchange="admin.job.getLog($(this).val());">
                    <option value="0" selected="selected"><?php echo Yii::t("app", "Select Job"); ?></option>
                    <?php foreach ($jobs as $job): ?>
                        <option value="<?php echo $job; ?>"><?php echo $job; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <div class="control-group">
            <label class="control-label" for="BgLogForm_log"><?php echo Yii::t('app', 'Log Content'); ?></label>
            <div class="controls">
                <textarea class="input-xxlarge monospace" rows="20" id="BgLogForm_log" wrap="off" readonly="readonly"></textarea>
            </div>
        </div>
    </fieldset>
</form>