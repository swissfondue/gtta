<div id="filter" class="nessus-mapping-filters info-block" data-filter-url="<?= $this->createUrl("nessusmapping/filtervulns"); ?>">
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
    </div>

    <br>

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
    </div>

    <br>

    <div class="filter">
        <h3><?= Yii::t("app", "Sort"); ?></h3>

        <hr />

        <div class="row-fluid">
            <div class="span6">
                <select name="sortBy" class="max-width" onchange="admin.nessusMapping.filterItems(<?= $mapping->id ?>)">
                    <option value="<?php echo NessusMapping::FILTER_SORT_ISSUE ?>" <?php if ($sortBy == NessusMapping::FILTER_SORT_ISSUE) echo "selected"; ?>><?php echo Yii::t("app", "Issue"); ?></option>
                    <option value="<?php echo NessusMapping::FILTER_SORT_RATING ?>" <?php if ($sortBy == NessusMapping::FILTER_SORT_RATING) echo "selected"; ?>><?php echo Yii::t("app", "Rating"); ?></option>
                    <option value="<?php echo NessusMapping::FILTER_SORT_CHECK ?>" <?php if ($sortBy == NessusMapping::FILTER_SORT_CHECK) echo "selected"; ?>><?php echo Yii::t("app", "Check"); ?></option>
                </select>
            </div>

            <div class="span6">
                <select name="sortDirection" class="max-width" onchange="admin.nessusMapping.filterItems(<?= $mapping->id ?>)">
                    <option value="<?php echo NessusMapping::FILTER_SORT_ASCENDING; ?>" <?php if ($sortDirection == NessusMapping::FILTER_SORT_ASCENDING) echo "selected"; ?>><?php echo Yii::t("app", "Low to High"); ?></option>
                    <option value="<?php echo NessusMapping::FILTER_SORT_DESCENDING; ?>" <?php if ($sortDirection == NessusMapping::FILTER_SORT_DESCENDING) echo "selected"; ?>><?php echo Yii::t("app", "High to Low"); ?></option>
                </select>
            </div>
        </div>
    </div>
</div>

