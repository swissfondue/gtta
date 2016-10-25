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

<?= $this->renderPartial("partial/mapping-filters", ["mapping" => $mapping, "nessusRatings" => $nessusRatings]); ?>

<div class="control-group nessus-vulns">
    <div class="controls">
        <?= $this->renderPartial("partial/mapping-table", ["vulns" => $mapping->vulns("vulns:orderByPluginName"), "ratings" => $ratings]); ?>
    </div>
</div>

<?= $this->renderPartial("/layouts/partial/check-selector", ["mapping" => $mapping]); ?>