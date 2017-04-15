<?php
    $action = Yii::app()->controller->action->id;
?>

<div class="pull-right">
    <ul class="nav nav-pills">
        <li <?= $action == "edit" ? "class=\"active\"" : ""; ?>><a href="<?php echo $this->createUrl("reporttemplate/edit", array("id" => $template->id)); ?>"><?php echo Yii::t("app", "Edit"); ?></a></li>
        <li <?= $action == "sections" ? "class=\"active\"" : ""; ?>><a href="<?php echo $this->createUrl("reporttemplate/sections", array("id" => $template->id)); ?>"><?php echo Yii::t("app", "Sections"); ?></a></li>
        <li <?= $action == "summary" ? "class=\"active\"" : ""; ?>><a href="<?php echo $this->createUrl("reporttemplate/summary", array("id" => $template->id)); ?>"><?php echo Yii::t("app", "Summary Blocks"); ?></a></li>
        <li <?= $action == "vulnsections" ? "class=\"active\"" : ""; ?>><a href="<?php echo $this->createUrl("reporttemplate/vulnsections", array("id" => $template->id)); ?>"><?php echo Yii::t("app", "Vulnerability Sections"); ?></a></li>
    </ul>
</div>