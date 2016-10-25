<div class="nessus-mapping">
    <h3><?= Yii::t("app", "Vulnerabilities") ?></h3>

    <hr/>

    <?php if (count($vulns)): ?>
        <table class="table">
            <thead>
            <tr>
                <th class="active"></th>
                <th class="nessus-issue"><?= Yii::t("app", "Nessus Issue") ?></th>
                <th class="nessus-rating"><?= Yii::t("app", "Nessus Rating") ?></th>
                <th class="mapped-check"><?= Yii::t("app", "Check") ?></th>
                <th class="mapped-result"><?= Yii::t("app", "Answer") ?></th>
                <th class="mapped-solution"><?= Yii::t("app", "Solution") ?></th>
                <th class="rating"><?= Yii::t("app", "Rating") ?></th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($vulns as $vuln): ?>
                <?= $this->renderPartial("/nessusmapping/partial/mapping-item", [
                    "vuln" => $vuln,
                    "ratings" => $ratings
                ]); ?>
            <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <?php echo Yii::t("app", "No vulnerabilities."); ?>
    <?php endif; ?>
</div>