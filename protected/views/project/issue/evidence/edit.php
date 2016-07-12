<script src="<?php echo Yii::app()->request->baseUrl; ?>/js/jquery/jquery.ui.js"></script>
<script src="<?php echo Yii::app()->request->baseUrl; ?>/js/jquery/jquery.iframe-transport.js"></script>
<script src="<?php echo Yii::app()->request->baseUrl; ?>/js/jquery/jquery.fileupload.js"></script>
<script src="/ckeditor/ckeditor.js"></script>
<script src="/ckeditor/adapters/jquery.js"></script>

<div class="active-header">
    <h1><?= CHtml::encode($this->pageTitle); ?></h1>
</div>

<hr>


<div class="container">
    <div class="row">
        <?= $this->renderPartial("partial/left-menu", ["project" => $project]) ?>
        <div class="span8">
            <div class="check-form"
                 data-type="check"
                 data-id="<?php echo $targetCheck->id; ?>"
                 data-save-url="<?php echo $this->createUrl("project/savecheck", array("id" => $project->id, "target" => $targetCheck->target->id, "category" => $targetCheck->category->check_category_id, "check" => $targetCheck->id)); ?>">
                    <?= $this->renderCheckForm($targetCheck, $targetCheck->category, $language, true); ?>
            </div>
        </div>
        <div class="span4">
            <?php
                echo $this->renderPartial("partial/right-block", array(
                    "quickTargets" => $quickTargets,
                    "project" => $project,
                    "client" => $client,
                    "statuses" => $statuses,
                    "category" => $targetCheck->category,
                    "target" => $targetCheck->target
                ));
            ?>
        </div>
    </div>
</div>