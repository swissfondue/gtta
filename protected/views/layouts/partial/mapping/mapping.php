<?= $this->renderPartial("/layouts/partial/mapping/mapping-filters", ["mapping" => $mapping, "nessusRatings" => $nessusRatings]); ?>

<div class="control-group nessus-vulns">
    <div class="controls">
        <?= $this->renderPartial("/layouts/partial/mapping/mapping-table", ["vulns" => $mapping->vulns("vulns:orderByPluginName"), "ratings" => $ratings]); ?>
    </div>
</div>

<?= $this->renderPartial("/layouts/partial/check-selector", ["mapping" => $mapping]); ?>