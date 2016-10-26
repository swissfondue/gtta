<div class="nessus-mapping-filters" data-filter-url="<?= $this->createUrl("nessusmapping/filtervulns"); ?>">
    <div class="filter nessus-ratings">
        <h3><?= Yii::t("app", "Ratings") ?></h3>

        <hr />

        <div class="nessus-ratings-filter-container">
            <?php foreach ($nessusRatings as $nr): ?>
                <div class="nessus-rating">
                    <label>
                        <input type="checkbox" value="<?= $nr ?>" onchange="admin.nessusMapping.filterItems(<?= $mapping->id ?>)" checked="checked"/>
                        <?= CHtml::encode($nr) ?>
                    </label>
                </div>
            <?php endforeach; ?>
        </div>

        <hr />
    </div>

    <div class="filter nessus-hosts">
        <h3><?= Yii::t("app", "Hosts") ?></h3>

        <hr />

        <div class="nessus-hosts-filter-container">
            <?php foreach ($mapping->hosts as $host): ?>
                <div class="nessus-host">
                    <label>
                        <input type="checkbox" value="<?= $host ?>" onchange="admin.nessusMapping.filterItems(<?= $mapping->id ?>)" checked="checked"/>
                        <?= CHtml::encode($host) ?>
                    </label>
                </div>
            <?php endforeach; ?>
        </div>

        <hr />
    </div>
</div>
