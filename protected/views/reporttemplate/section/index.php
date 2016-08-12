<script src="/ckeditor/ckeditor.js"></script>
<script src="/ckeditor/adapters/jquery.js"></script>
<script src="<?php echo Yii::app()->request->baseUrl; ?>/js/jquery/jquery.ui.js"></script>
<script src="<?php echo Yii::app()->request->baseUrl; ?>/js/sortable.js"></script>

<div class="active-header">
    <?= $this->renderPartial("partial/menu", ["template" => $template]); ?>

    <div class="pull-right buttons">
        <a class="btn" href="#add" onclick="admin.reportTemplate.sections.showAddForm();">
            <i class="icon icon-plus"></i>
            <?php echo Yii::t("app", "Add"); ?>
        </a>
    </div>

    <h1><?php echo CHtml::encode($this->pageTitle); ?></h1>
</div>

<hr>

<div class="container">
    <div class="row">
        <div class="span3">
            <ul class="sortable-section-list">
                <?php foreach ($template->sections as $section): ?>
                    <li data-section-id="<?= $section->id; ?>" data-section-type="<?= $section->type ?>" onclick="admin.reportTemplate.sections.select(this);">
                        <?php if (ReportSection::isChart($section->type)): ?>
                            <i class="icon icon-picture"></i>
                        <?php endif; ?>

                        <?= CHtml::encode($section->title); ?>

                        <a href="#remove" class="remove">
                            <i class="icon icon-remove"></i>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>

        <div class="span9 add-section hide">
            <h3><?= Yii::t("app", "Available Sections") ?></h3>

            <div>
                <ul class="available-section-list">
                    <?php foreach (ReportSection::getValidTypes() as $section): ?>
                        <li data-section-type="<?= $section ?>">
                            <?php if (ReportSection::isChart($section)): ?>
                                <i class="icon icon-picture"></i>
                            <?php endif; ?>

                            <?= ReportSection::getTypeTitles()[$section] ?>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>

            <hr>

            <a class="btn" href="#add" onclick="admin.reportTemplate.sections.closeAddForm();"><?= Yii::t("app", "Cancel"); ?></a>
        </div>

        <div class="span9 edit-section">
        </div>
    </div>
</div>

<div class="section-form-template section-form hide">
    <form class="form-horizontal">
        <fieldset>
            <div class="control-group">
                <label class="control-label"><?php echo Yii::t("app", "Title"); ?></label>

                <div class="controls">
                    <input type="text" class="input-xlarge" name="ReportTemplateSectionEditForm[title]" value="">
                </div>
            </div>

            <div class="control-group">
                <label class="control-label"><?php echo Yii::t("app", "Type"); ?></label>
                <div class="controls form-text" data-field-type=""></div>
            </div>

            <div class="control-group">
                <label class="control-label"><?php echo Yii::t("app", "Content"); ?></label>

                <div class="controls">
                    <textarea class="wysiwyg" name="ReportTemplateSectionEditForm[content]"></textarea>
                </div>
            </div>

            <div class="control-group">
                <div class="controls">
                    <button type="submit" class="btn">
                        <?php echo Yii::t("app", "Save"); ?>
                    </button>
                </div>
            </div>
        </fieldset>
    </form>
</div>

<?php
    $sections = [];

    foreach ($template->sections as $section) {
        $sections[$section->id] = [
            "type" => $section->type,
            "title" => $section->title,
            "content" => $section->content,
        ];
    }
?>

<script>
    $(function () {
        var sections = <?= json_encode($sections); ?>,
            fieldTypes = <?= json_encode(ReportSection::getTypeTitles()); ?>;

        admin.reportTemplate.sections.init(sections, fieldTypes);
    });
</script>