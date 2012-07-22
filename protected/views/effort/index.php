<div class="active-header">
    <div class="pull-right buttons">
        <a class="btn hide" href="#print" id="print-button" onclick="system.effort.print();"><?php echo Yii::t('app', 'Print'); ?></a>
    </div>

    <h1><?php echo CHtml::encode($this->pageTitle); ?></h1>
</div>

<hr>

<div class="container effort-list-container hide">
    <div class="row">
        <div class="span8">
            <table class="table effort-list">
                <tbody>
                    <tr>
                        <th class="name">Category</th>
                        <th class="targets">Targets</th>
                        <th class="effort">Effort</th>
                        <th class="actions">&nbsp;</th>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="form-header hide">
    <hr>
    <h3><?php echo Yii::t('app', 'Add Check Category'); ?></h3>
</div>

<form class="form-horizontal" action="<?php echo Yii::app()->request->url; ?>" method="post" onsubmit="system.effort.add(); return false;">
    <input type="hidden" value="<?php echo Yii::app()->request->csrfToken; ?>" name="YII_CSRF_TOKEN">
    <input type="hidden" value="0" name="EffortEstimateForm[effort]" id="EffortEstimateForm_effort">

    <fieldset>
        <div class="control-group">
            <label class="control-label" for="EffortEstimateForm_categoryId"><?php echo Yii::t('app', 'Check Category'); ?></label>
            <div class="controls">
                <select class="input-xlarge" id="EffortEstimateForm_categoryId" name="EffortEstimateForm[categoryId]" onchange="system.effort.formChange(this);">
                    <option value="0"><?php echo Yii::t('app', 'Please select...'); ?></option>
                    <?php foreach ($checks as $check): ?>
                        <option value="<?php echo $check['id']; ?>"><?php echo CHtml::encode($check['name']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
        
        <div class="control-group">
            <label class="control-label" for="EffortEstimateForm_advanced"><?php echo Yii::t('app', 'Advanced'); ?></label>
            <div class="controls">
                <input type="checkbox" id="EffortEstimateForm_advanced" name="EffortEstimateForm[advanced]" value="1" checked onchange="system.effort.formChange(this);">
            </div>
        </div>

        <?php if (count($references)): ?>
            <div class="control-group">
                <label class="control-label"><?php echo Yii::t('app', 'References'); ?></label>
                <div class="controls">
                    <?php foreach ($references as $reference): ?>
                        <label class="checkbox">
                            <input type="checkbox" id="EffortEstimateForm_referenceIds_<?php echo $reference['id']; ?>" name="EffortEstimateForm[referenceIds][]" value="<?php echo $reference['id']; ?>" checked onchange="system.effort.formChange(this);">
                            <?php echo CHtml::encode($reference['name']); ?>
                        </label>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>
        
        <div class="control-group">
            <label class="control-label" for="EffortEstimateForm_targets"><?php echo Yii::t('app', 'Targets'); ?></label>
            <div class="controls">
                <input type="text" class="input-xlarge" id="EffortEstimateForm_targets" name="EffortEstimateForm[targets]" value="1" onkeyup="system.effort.formChange(this);">
            </div>
        </div>

        <div class="control-group">
            <label class="control-label"><?php echo Yii::t('app', 'Checks'); ?></label>
            <div class="controls form-text">
                <span id="checks">0</span>
            </div>
        </div>

        <div class="control-group">
            <label class="control-label"><?php echo Yii::t('app', 'Estimated Effort'); ?></label>
            <div class="controls form-text">
                <span id="estimated-effort">0</span> <?php echo Yii::t('app', 'minutes'); ?>
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn" disabled><?php echo Yii::t('app', 'Add'); ?></button>
        </div>
    </fieldset>
</form>

<script>
    var referenceList, checkList;

    referenceList = <?php echo json_encode($references); ?>;
    checkList     = <?php echo json_encode($checks); ?>;
</script>