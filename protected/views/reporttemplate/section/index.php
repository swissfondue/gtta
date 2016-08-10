<script src="/ckeditor/ckeditor.js"></script>
<script src="/ckeditor/adapters/jquery.js"></script>
<script src="<?php echo Yii::app()->request->baseUrl; ?>/js/jquery/jquery.ui.js"></script>
<script src="<?php echo Yii::app()->request->baseUrl; ?>/js/Sortable.min.js"></script>

<div class="active-header">
    <?php if (!$template->isNewRecord && $template->type == ReportTemplate::TYPE_RTF): ?>
        <div class="pull-right">
            <ul class="nav nav-pills">
                <li><a href="<?php echo $this->createUrl('reporttemplate/edit', array( 'id' => $template->id )); ?>"><?php echo Yii::t('app', 'Edit'); ?></a></li>
                <li class="active"><a href="<?php echo $this->createUrl('reporttemplate/sections', array( 'id' => $template->id )); ?>"><?php echo Yii::t('app', 'Sections'); ?></a></li>
                <li><a href="<?php echo $this->createUrl('reporttemplate/summary', array( 'id' => $template->id )); ?>"><?php echo Yii::t('app', 'Summary Blocks'); ?></a></li>
                <li><a href="<?php echo $this->createUrl('reporttemplate/vulnsections', array( 'id' => $template->id )); ?>"><?php echo Yii::t('app', 'Vulnerability Sections'); ?></a></li>
            </ul>
        </div>
    <?php endif; ?>
    <h1><?php echo CHtml::encode($this->pageTitle); ?></h1>
</div>

<hr>

<form class="form-horizontal" action="<?= $this->createUrl("") ?>" method="post">
    <input type="hidden" value="<?php echo Yii::app()->request->csrfToken; ?>" name="YII_CSRF_TOKEN">

    <fieldset>
        <div class="container">
            <div class="row">
                <div class="span3">
                    <ul id="section-list" class="info-block nav nav-pills nav-stacked">
                        <?php foreach ($template->sections as $section): ?>
                            <li data-type-id="<?= $section->type ?>"><a href="#"><?= $section->title; ?></a></li>
                        <?php endforeach; ?>
                    </ul>
                    <hr>
                    <h3><?= Yii::t("app", "Available Chart Blocks") ?></h3>
                    <ul id="chart-list" class="info-block nav nav-pills nav-stacked">
                        <?php foreach (ReportSection::getChartTypes() as $typeId): ?>
                            <li data-type-id="<?= $typeId ?>"><a href="#"><?= ReportSection::getTypeTitles()[$typeId] ?></a></li>
                        <?php endforeach; ?>
                    </ul>
                </div>

                <div class="span8">
                    <div class="tab-content">
                        <?php foreach ($languages as $language): ?>
                            <div class="tab-pane<?php if ($language->id == $system->language->id) echo ' active'; ?>" id="<?php echo CHtml::encode($language->code); ?>"></div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </fieldset>
</form>

<script>
    $('#languages-tab a').click(function (e) {
        e.preventDefault();
        $(this).tab('show');
    });

    $(function () {
        $(".wysiwyg").ckeditor();
        Sortable.create($("#section-list")[0], {
            group: {
                name: "report-sections",
                put: true,
                pull: false
            }
        });
        Sortable.create($("#chart-list")[0], {
            sort: false,
            group: {
                name: "report-sections",
                pull: "clone",
                put: false
            }
        });
    });
</script>