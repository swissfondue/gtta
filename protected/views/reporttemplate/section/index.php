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

<form class="form-horizontal" action="<?= $this->createUrl("") ?>" method="post">
    <input type="hidden" value="<?php echo Yii::app()->request->csrfToken; ?>" name="YII_CSRF_TOKEN">

    <fieldset>
        <div class="container">
            <div class="row">
                <div class="span3">
                    <ul class="sortable-section-list">
                        <?php foreach ($template->sections as $section): ?>
                            <li data-section-type="<?= $section->type ?>">
                                <?php if (ReportSection::isChart($section->type)): ?>
                                    <i class="icon icon-picture"></i>
                                <?php endif; ?>

                                <?= CHtml::encode($section->title); ?>

                                <a href="#remove" onclick="admin.reportTemplate.sections.del(this);">
                                    <i class="js-remove icon icon-remove"></i>
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
                    xxx
                </div>
            </div>
        </div>
    </fieldset>
</form>

<script>
    $(function () {
        admin.reportTemplate.sections.init();
    });
</script>