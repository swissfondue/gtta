<div class="active-header">
    <div class="pull-right">
        <ul class="nav nav-pills">
            <li><a href="<?php echo $this->createUrl("check/viewcontrol", array("id" => $category->id, "control" => $control->id)); ?>"><?php echo Yii::t("app", "View"); ?></a></li>
            <li><a href="<?php echo $this->createUrl("check/editcontrol", array("id" => $category->id, "control" => $control->id)); ?>"><?php echo Yii::t("app", "Edit"); ?></a></li>
            <li class="active"><a href="<?php echo $this->createUrl("check/sharecontrol", array("id" => $category->id, "control" => $control->id)); ?>"><?php echo Yii::t("app", "Share"); ?></a></li>
        </ul>
    </div>

    <h1><?php echo CHtml::encode($this->pageTitle); ?></h1>
</div>

<hr>

<p>
    <?php echo Yii::t("app", "If you press the button below, all checks in this control will be shared with the community and will be available for everyone with a valid GTTA license."); ?>
    <?php echo Yii::t("app", "Please make sure that you really want to share the control and your checks don't contain any sensitive information, because this action is irreversible."); ?>
</p>

<br>

<form class="form-horizontal" action="<?php echo Yii::app()->request->url; ?>" method="post">
    <input type="hidden" value="<?php echo Yii::app()->request->csrfToken; ?>" name="YII_CSRF_TOKEN">
    <input type="hidden" name="ShareForm[share]" value="1">

    <fieldset>
        <div class="form-actions">
            <button type="submit" class="btn"><?php echo Yii::t("app", "Share"); ?></button>
        </div>
    </fieldset>
</form>
