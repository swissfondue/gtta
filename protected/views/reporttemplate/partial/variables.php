<div id="var-list-icon" class="pull-right expand-collapse-icon" onclick="system.toggleBlock('#var-list');">
    <i class="icon-chevron-up"></i>
</div>

<h3><a href="#toggle" onclick="system.toggleBlock('#var-list');"><?php echo Yii::t("app", "Variable List"); ?></a></h3>

<div class="info-block" id="var-list">
    <table class="table client-details">
        <tr>
            <th>
                {client}
            </th>
            <td>
                <?php echo Yii::t("app", "Client name"); ?>
            </td>
        </tr>
        <tr>
            <th>
                {project}
            </th>
            <td>
                <?php echo Yii::t("app", "Project name"); ?>
            </td>
        </tr>
        <tr>
            <th>
                {year}
            </th>
            <td>
                <?php echo Yii::t("app", "Project year"); ?>
            </td>
        </tr>
        <tr>
            <th>
                {deadline}
            </th>
            <td>
                <?php echo Yii::t("app", "Project deadline"); ?>
            </td>
        </tr>
        <tr>
            <th>
                {admin}
            </th>
            <td>
                <?php echo Yii::t("app", "Project admin"); ?>
            </td>
        </tr>
        <tr>
            <th>
                {rating}
            </th>
            <td>
                <?php echo Yii::t("app", "Project rating"); ?>
            </td>
        </tr>
        <tr>
            <td colspan="2">
                <br><hr>
            </td>
        </tr>
        <tr>
            <th>
                {targets}
            </th>
            <td>
                <?php echo Yii::t("app", "Number of targets"); ?>
            </td>
        </tr>
        <tr>
            <th>
                {target.list}
            </th>
            <td>
                <?php echo Yii::t("app", "List of targets"); ?>
            </td>
        </tr>
        <tr>
            <th>
                {target.stats}
            </th>
            <td>
                <?php echo Yii::t("app", "List of targets with statistics"); ?>
            </td>
        </tr>
        <tr>
            <th>
                {target.weakest}
            </th>
            <td>
                <?php echo Yii::t("app", "List of targets with a name of the weakest control"); ?>
            </td>
        </tr>
        <tr>
            <th>
                {vuln.list}
            </th>
            <td>
                <?php echo Yii::t("app", "List of top 5 most dangerous vulnerabilities"); ?>
            </td>
        </tr>
        <tr>
            <td colspan="2">
                <br><hr>
            </td>
        </tr>
        <tr>
            <th>
                {checks}
            </th>
            <td>
                <?php echo Yii::t("app", "Number of finished checks"); ?>
            </td>
        </tr>
        <tr>
            <th>
                {checks.hi}
            </th>
            <td>
                <?php echo Yii::t("app", "Number of high risk checks"); ?>
            </td>
        </tr>
        <tr>
            <th>
                {checks.med}
            </th>
            <td>
                <?php echo Yii::t("app", "Number of med risk checks"); ?>
            </td>
        </tr>
        <tr>
            <th>
                {checks.lo}
            </th>
            <td>
                <?php echo Yii::t("app", "Number of low risk checks"); ?>
            </td>
        </tr>
        <tr>
            <th>
                {checks.info}
            </th>
            <td>
                <?php echo Yii::t("app", "Number of info rating checks"); ?>
            </td>
        </tr>
    </table>
</div>