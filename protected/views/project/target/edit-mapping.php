<div class="active-header">
    <div class="pull-right">
        <ul class="nav nav-pills">
            <li><a href="<?= $this->createUrl("nessusmapping/edit", ["id" => $mapping->id]); ?>"><?= Yii::t("app", "Edit"); ?></a></li>
            <li class="active"><a href="<?= $this->createUrl("nessusmapping/view", ["id" => $mapping->id]); ?>"><?= Yii::t("app", "Mapping"); ?></a></li>
        </ul>
    </div>

    <h1><?= CHtml::encode($this->pageTitle); ?></h1>
</div>

<hr>

<?=
    $this->renderPartial("/layouts/partial/mapping/mapping", [
        "mapping" => $mapping,
        "nessusRatings" => $nessusRatings,
        "ratings" => $ratings
    ]);
?>

<form class="form-horizontal" action="<?= $this->createUrl("project/applymapping"); ?>" method="post">
    <input type="hidden" value="<?= Yii::app()->request->csrfToken; ?>" name="YII_CSRF_TOKEN">
    <input type="hidden" value="<?= $project->id ?>" name="ProjectApplyMappingForm[projectId]" />
    <input type="hidden" value="<?= $mapping->id ?>" name="ProjectApplyMappingForm[mappingId]" />

    <fieldset>
        <div class="form-actions">
            <button type="submit" class="btn"><?php echo Yii::t("app", "Import"); ?></button>
        </div>
    </fieldset>
</form>
