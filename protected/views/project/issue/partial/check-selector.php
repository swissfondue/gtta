<div
    class="modal fade"
    id="issue-check-select-dialog"
    tabindex="-1"
    role="dialog"
    aria-labelledby="smallModal"
    aria-hidden="true"
    data-search-check-url="<?= $this->createUrl("project/searchchecks", ["id" => $project->id]) ?>">
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
                <ul class="check-list"></ul>
                <span class="no-search-result" style="display:none"><?= Yii::t("app", "No checks found.") ?></span>
            </div>
        </div>
    </div>
</div>

<script>
    $(function () {
        $("#issue-check-select-dialog input.issue-search-query").keyup(function (e) {
            // if alpha or backspace
            if (/[a-zA-Z0-9_ -]/.test(String.fromCharCode(e.keyCode)) || e.keyCode == 8) {
                admin.issue.searchChecks($(this).val())
            }
        });

        admin.issue.initCheckSelectDialog();
    });
</script>