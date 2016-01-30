<div class="active-header">
    <h1><?php echo CHtml::encode($this->pageTitle); ?></h1>
</div>

<hr>

<form id="object-selection-form" class="form-horizontal" action="<?php echo Yii::app()->request->url; ?>" method="post" data-object-list-url="<?php print $this->createUrl("app/objectlist"); ?>">
    <input type="hidden" value="<?php echo Yii::app()->request->csrfToken; ?>" name="YII_CSRF_TOKEN">

    <fieldset>
        <ul class="nav nav-tabs" id="languages-tab">
            <?php foreach ($languages as $language): ?>
                <li<?php if ($language->default) echo ' class="active"'; ?>>
                    <a href="#<?php echo CHtml::encode($language->code); ?>">
                        <img src="<?php echo Yii::app()->baseUrl; ?>/images/languages/<?php echo CHtml::encode($language->code); ?>.png" alt="<?php echo CHtml::encode($language->name); ?>">
                        <?php echo CHtml::encode($language->name); ?>
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>

        <div class="tab-content">
            <?php foreach ($languages as $language): ?>
                <div class="tab-pane<?php if ($language->default) echo ' active'; ?>" id="<?php echo CHtml::encode($language->code); ?>">
                    <div class="control-group <?php if ($model->getError('name')) echo 'error'; ?>">
                        <label class="control-label" for="RelationTemplateEditForm_localizedItems_<?php echo CHtml::encode($language->id); ?>_name"><?php echo Yii::t('app', 'Name'); ?></label>
                        <div class="controls">
                            <input type="text" class="input-xlarge" id="RelationTemplateEditForm_localizedItems_<?php echo CHtml::encode($language->id); ?>_name" name="RelationTemplateEditForm[localizedItems][<?php echo CHtml::encode($language->id); ?>][name]" value="<?php echo isset($model->localizedItems[$language->id]) ? CHtml::encode($model->localizedItems[$language->id]['name']) : ''; ?>">
                            <?php if ($model->getError('name')): ?>
                                <p class="help-block"><?php echo $model->getError('name'); ?></p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="control-group relations-graph <?php if ($model->getError('relations')) echo 'error'; ?>">
            <label class="control-label"><?php echo Yii::t('app', 'Relations'); ?></label>
            <div class="controls">
                <table border="0" width="730px">
                    <tr>
                        <td valign="top">
                            <div id="graph"></div>
                        </td>
                        <td id="toolbar" valign="top"></td>
                    </tr>
                </table>

                <?php if ($model->getError('relations')): ?>
                    <p class="help-block"><?php echo $model->getError('relations'); ?></p>
                <?php endif; ?>

                <div id="zoomActions">
                </div>
            </div>
        </div>
        <input type="hidden" class="relations-form-input" id="RelationTemplateEditForm_relations" name="RelationTemplateEditForm[relations]" />

        <div class="form-actions">
            <button type="submit" class="btn"><?php echo Yii::t('app', 'Save'); ?></button>
        </div>
    </fieldset>
</form>
<script>
    function onInit(editor) {
        admin.mxgraph.init.call(this, editor);
    }

    var configNode = mxUtils.load("<?php echo Yii::app()->request->baseUrl; ?>/js/mxgraph/grapheditor/config/main.xml").getDocumentElement();
    admin.mxgraph.editor = new mxEditor(configNode);

    <?php foreach ($categories as $category): ?>
        admin.mxgraph.checkCategories.push({
            id : <?php print $category->id; ?>,
            name : "<?php print $category->localizedName; ?>"
        });
    <?php endforeach; ?>

    <?php foreach ($filters as $filter): ?>
    admin.mxgraph.filters.push({ name: "<?php print $filter['name']; ?>", title: "<?php print $filter['title']; ?>" });
    <?php endforeach; ?>

    $('#languages-tab a').click(function (e) {
        e.preventDefault();
        $(this).tab('show');
    });

    <?php if ($model->relations): ?>
        admin.mxgraph.buildByXML('<?php print $model->relations; ?>');
    <?php elseif (!$template->isNewRecord): ?>
        admin.mxgraph.buildByXML('<?php print $template->relations; ?>');
    <?php endif; ?>
</script>