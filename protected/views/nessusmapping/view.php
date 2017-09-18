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

<?= $this->renderPartial("/layouts/partial/mapping/mapping", [
    "mapping" => $mapping,
    "nessusRatings" => $nessusRatings,
    "ratings" => $ratings,
    "sortBy" => $sortBy,
    "sortDirection" => $sortDirection
]); ?>