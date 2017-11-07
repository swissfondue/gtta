<?php
    if (isset($project)) {
        $checkSearchUrl = $this->createUrl("project/searchchecks", ["id" => $project->id]);
    } elseif (isset($mapping)) {
        $checkSearchUrl = $this->createUrl("nessusmapping/searchchecks", ["id" => $mapping->id]);
    }
?>

<div
    class="modal fade"
    id="issue-check-select-dialog"
    tabindex="-1"
    role="dialog"
    aria-labelledby="smallModal"
    aria-hidden="true"
    data-search-check-url="<?= $checkSearchUrl ?>"
    style="display: none">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">×</button>
                <h3><?= Yii::t("app", "Select Check") ?></h3>
            </div>
            <div class="modal-body">
                <input class="issue-search-query"
                       placeholder="<?= Yii::t("app", "Check name") ?>"
                       type="text" />
                <p class="help-block">
                    <?= Yii::t("app", "You may use quotes to find the exact phrase match."); ?>
                </p>
                <ul class="check-list"></ul>
                <span class="no-search-result" style="display:none"><?= Yii::t("app", "No checks found.") ?></span>
            </div>
        </div>
    </div>
</div>

<script>
    var chooseEvent = admin.issue.add;

    <?php if (isset($mapping)): ?>
        chooseEvent = admin.nessusMapping.updateItem;
    <?php endif; ?>

    $(function () {
        $("#issue-check-select-dialog input.issue-search-query").keyup(function (e) {
            // if alpha or backspace
            if (/[a-zA-Z0-9_ -]/.test(String.fromCharCode(e.keyCode)) || e.keyCode == 8) {
                admin.issue.searchChecks($(this).val(), chooseEvent)
            }
        });

        admin.issue.initCheckSelectDialog();
    });
</script>