<div class="pull-right buttons hide" id="print-button">
    <a class="btn" href="#print" onclick="system.report.printEffort();"><i class="icon icon-print"></i> <?php echo Yii::t('app', 'Print'); ?></a>
</div>

<div class="pull-right buttons">
    <a class="btn" href="#add-category" onclick="system.report.effortForm();"><i class="icon icon-plus"></i> <?php echo Yii::t('app', 'Add Category'); ?></a>
</div>

<h1><?php echo CHtml::encode($this->pageTitle); ?></h1>

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

<div id="placeholder-text"><?php echo Yii::t('app', 'No categories yet.'); ?></div>

<div class="modal hide" id="effort-modal">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">×</button>
        <h3><?php echo Yii::t('app', 'Add Category'); ?></h3>
    </div>
    <div class="modal-body">
        <div class="modal-text">
            <?php echo Yii::t('app', 'Please select a category that should be included into the estimation.'); ?>
        </div>

        <form id="EffortEstimateForm" class="form-horizontal" action="<?php echo Yii::app()->request->url; ?>" method="post" onsubmit="system.report.addEffort(); return false;">
            <input type="hidden" value="<?php echo Yii::app()->request->csrfToken; ?>" name="YII_CSRF_TOKEN">
            <input type="hidden" value="0" name="EffortEstimateForm[effort]" id="EffortEstimateForm_effort">

            <fieldset>
                <div class="control-group">
                    <label class="control-label" for="EffortEstimateForm_categoryId"><?php echo Yii::t('app', 'Check Category'); ?></label>
                    <div class="controls">
                        <select class="input-xlarge" id="EffortEstimateForm_categoryId" name="EffortEstimateForm[categoryId]" onchange="system.report.effortFormChange(this);">
                            <option value="0"><?php echo Yii::t('app', 'Please select...'); ?></option>
                            <?php foreach ($checks as $check): ?>
                                <option value="<?php echo $check['id']; ?>"><?php echo CHtml::encode($check['name']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <?php if (count($references)): ?>
                    <div class="control-group">
                        <label class="control-label"><?php echo Yii::t('app', 'References'); ?></label>
                        <div class="controls">
                            <?php foreach ($references as $reference): ?>
                                <label class="checkbox">
                                    <input type="checkbox" id="EffortEstimateForm_referenceIds_<?php echo $reference['id']; ?>" name="EffortEstimateForm[referenceIds][]" value="<?php echo $reference['id']; ?>" checked onchange="system.report.effortFormChange(this);">
                                    <?php echo CHtml::encode($reference['name']); ?>
                                </label>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>

                <div class="control-group">
                    <label class="control-label" for="EffortEstimateForm_targets"><?php echo Yii::t('app', 'Targets'); ?></label>
                    <div class="controls">
                        <input type="text" class="input-xlarge" id="EffortEstimateForm_targets" name="EffortEstimateForm[targets]" value="1" onkeyup="system.report.effortFormChange(this);">
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
            </fieldset>
        </form>
    </div>
    <div class="modal-footer">
        <button class="btn" data-dismiss="modal"><?php echo Yii::t('app', 'Cancel'); ?></button>
        <button id="add-button" class="btn btn-primary" onclick="system.report.effortFormSubmit();" disabled><?php echo Yii::t('app', 'Add'); ?></button>
    </div>
</div>

<script>
    var referenceList, checkList;

    referenceList = <?php echo json_encode($references, JSON_NUMERIC_CHECK); ?>;
    checkList     = <?php echo json_encode($checks, JSON_NUMERIC_CHECK); ?>;
</script>