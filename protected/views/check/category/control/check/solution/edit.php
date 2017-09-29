<script src="/ckeditor/ckeditor.js"></script>
<script src="/ckeditor/adapters/jquery.js"></script>
<?php if ($view == Check::VIEW_SHARED): ?>
    <style>
        table .span6 {
            float: left;
        }
    </style>
<?php endif; ?>
<hr>
<?php if ($solution->isNewRecord) {
    echo "<h2>" . Yii::t('app', 'New Solution') . "</h2><br>";
} ?>
<form id="solution-form-<?=$solution->id?>" class="form-horizontal" action="<?php echo Yii::app()->request->url; ?>" method="post">
    <input type="hidden" value="<?php echo Yii::app()->request->csrfToken; ?>" name="YII_CSRF_TOKEN">
    <fieldset>
        <?php if ($view == Check::VIEW_TABBED): ?>
            <ul class="nav nav-tabs" id="languages-tab">
                <?php foreach ($languages as $language): ?>
                    <li<?php if ($language->default) echo ' class="active"'; ?>>
                        <a href="#<?php echo CHtml::encode($language->code); ?>">
                            <img src="<?php echo Yii::app()->baseUrl; ?>/images/languages/<?php echo CHtml::encode($language->code); ?>.png"
                                 alt="<?php echo CHtml::encode($language->name); ?>">
                            <?php echo CHtml::encode($language->name); ?>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
        <div class=<?= ($view == Check::VIEW_TABBED) ? "tab-content" : "row" ?>>
            <?php foreach ($languages as $language): ?>
                <div class="<?= (($view == Check::VIEW_SHARED) ? "span6" : ("tab-pane" . $language->default ? " active" : "")) ?>"
                     id="<?php echo CHtml::encode($language->code); ?>">
                    <?php if ($view == Check::VIEW_SHARED): ?>
                        <a>
                            <img src="<?php echo Yii::app()->baseUrl; ?>/images/languages/<?php echo CHtml::encode($language->code); ?>.png"
                                 alt="<?php echo CHtml::encode($language->name); ?>">
                            <?php echo CHtml::encode($language->name); ?>
                        </a>
                    <?php endif; ?>
                    <div class="control-group <?php if ($model->getError('title')) echo 'error'; ?>">
                        <label class="control-label"
                               for="CheckSolutionEditForm_localizedItems_<?php echo CHtml::encode($language->id); ?>_title"><?php echo Yii::t('app', 'Title'); ?></label>
                        <div class="controls">
                            <input type="text" class="input-xlarge"
                                   id="CheckSolutionEditForm_localizedItems_<?php echo CHtml::encode($language->id); ?>_title"
                                   name="CheckSolutionEditForm[localizedItems][<?php echo CHtml::encode($language->id); ?>][title]"
                                   value="<?php echo isset($model->localizedItems[$language->id]) ? CHtml::encode($model->localizedItems[$language->id]['title']) : ''; ?>">
                            <?php if ($model->getError('title')): ?>
                                <p class="help-block"><?php echo $model->getError('title'); ?></p>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="control-group <?php if ($model->getError('solution')) echo 'error'; ?>">
                        <label class="control-label"
                               for="CheckSolutionEditForm_localizedItems_<?php echo CHtml::encode($language->id); ?>_solution"><?php echo Yii::t('app', 'Solution'); ?></label>
                        <div class="controls">
                            <textarea class="wysiwyg" style="height:200px;"
                                      id="CheckSolutionEditForm_localizedItems_<?php echo CHtml::encode($language->id); ?>_solution"
                                      name="CheckSolutionEditForm[localizedItems][<?php echo CHtml::encode($language->id); ?>][solution]"><?php echo isset($model->localizedItems[$language->id]) ? str_replace('&', '&amp;', $model->localizedItems[$language->id]['solution']) : ''; ?></textarea>
                            <?php if ($model->getError('solution')): ?>
                                <p class="help-block"><?php echo $model->getError('solution'); ?></p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        <hr>
        <div class="control-group <?php if ($model->getError('sortOrder')) echo 'error'; ?>">
            <label class="control-label"
                   for="CheckSolutionEditForm_sortOrder"><?php echo Yii::t('app', 'Sort Order'); ?></label>
            <div class="controls">
                <input type="text" class="input-xlarge" id="CheckSolutionEditForm_sortOrder"
                       name="CheckSolutionEditForm[sortOrder]"
                       value="<?php echo $model->sortOrder ? $model->sortOrder : 0; ?>">
                <?php if ($model->getError('sortOrder')): ?>
                    <p class="help-block"><?php echo $model->getError('sortOrder'); ?></p>
                <?php endif; ?>
            </div>
        </div>
        <div class="form-actions">
            <button type="submit" class="btn"><?php echo Yii::t('app', 'Save'); ?></button>
            <?php if ($solution->isNewRecord): ?>
                <button type="button" class="btn" onclick="$('#solution-div-new').hide();"><?php echo Yii::t('app', 'Close'); ?></button>
            <?php endif; ?>
        </div>
    </fieldset>
</form>
<script>
    /**
     *Submit form without redirect
     */
    $(function () {
        var solId = <?php echo json_encode($solution->id)?>;
        $("#solution-form-" + (solId?solId:'')).on("submit", function (e) {
            e.preventDefault();
            $.ajax({
                url: $(this).attr("action"),
                type: 'POST',
                data: $(this).serialize(),
                success: function (data) {
                    <?php if ($solution->isNewRecord):  ?>
                    $("#solution-div-new").hide();
                    window.location.reload();
                    <?php else: ?>
                    $("#solution-div-" + solId).hide();
                    <?php endif; ?>
                },
                error: function (data) {
                    system.addAlert('error', "Fill all required fields.");
                }
            });
        });
    });

    $('#languages-tab a').click(function (e) {
        e.preventDefault();
        $(this).tab('show');
    });

    $(function () {
        $(".wysiwyg").ckeditor();
    });
</script>