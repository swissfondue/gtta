<div class="container">
    <div class="row">
        <div class="span8">
            <div class="control-group nessus-vulns">
                <div class="controls">
                    <?= $this->renderPartial("/layouts/partial/mapping/mapping-table", ["vulns" => $mapping->vulns("vulns:orderByPluginName"), "ratings" => $ratings]); ?>
                </div>
            </div>
        </div>
        <div class="span4">
            <?= $this->renderPartial("/layouts/partial/mapping/mapping-filters", ["mapping" => $mapping, "nessusRatings" => $nessusRatings, "sortBy" => 1, "sortDirection" => 1]); ?>
        </div>
    </div>
</div>
<?= $this->renderPartial("/layouts/partial/check-selector", ["mapping" => $mapping]); ?>