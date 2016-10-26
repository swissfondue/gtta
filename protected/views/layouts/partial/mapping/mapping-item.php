<tr class="nessus-mapping-vuln"
    data-item-id="<?= $vuln->id ?>"
    data-update-url="<?= $this->createUrl("nessusmapping/updatevuln"); ?>"
    data-check-id="<?= $vuln->check ? $vuln->check->id : null ?>"
    data-result-id="<?= $vuln->result ? $vuln->result->id : null?>"
    data-solution-id="<?= $vuln->result ? $vuln->result->id : null ?>"
    data-rating="<?= $vuln->rating ? $vuln->rating : null ?>">

    <td class="active">
        <input type="checkbox"
               id="NessusMappingVulns_<?= $vuln->id; ?>"
               name="NessusMappingEditVulnsForm[vulns][]"
               value="<?= $vuln->id; ?>" <?= $vuln->active ? "checked" : "" ?>
               onchange="admin.nessusMapping.saveItem(<?= $vuln->id ?>)">
    </td>

    <td class="nessus-issue">
        <label for="NessusMappingVulns_<?= $vuln->id; ?>">
            <?= CHtml::encode($vuln->nessus_plugin_name); ?>
        </label>
    </td>

    <td class="nessus-rating">
        <?= CHtml::encode($vuln->nessus_rating); ?>
    </td>

    <td class="mapped-check <?= $vuln->check ? "mapped" : "not-mapped" ?>">
        <a href="#" onclick="admin.nessusMapping.showCheckSearchPopup(<?= $vuln->id ?>)">
            <?= CHtml::encode($vuln->check ? $vuln->check->name :  Yii::t("app", "Not Mapped")); ?>
        </a>
    </td>

    <td class="mapped-result">
        <?php if ($vuln->check): ?>
            <select onchange="admin.nessusMapping.saveItem(<?= $vuln->id ?>)">
                <option value="0" <?= !$vuln->result ? " selected" : "" ?>><?= Yii::t("app", "N/A"); ?></option>
                <?php foreach ($vuln->check->results as $result): ?>
                    <option <?= $vuln->result && $vuln->result->id == $result->id ? " selected" : "" ?> value="<?= $result->id ?>">
                        <?= CHtml::encode($result->title) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        <?php else: ?>
            <i class="icon-minus"></i>
        <?php endif; ?>
    </td>

    <td class="mapped-solution">
        <?php if ($vuln->check): ?>
            <select onchange="admin.nessusMapping.saveItem(<?= $vuln->id ?>)">
                <option value="0" <?= !$vuln->solution ? " selected" : "" ?>><?= Yii::t("app", "N/A"); ?></option>
                <?php foreach ($vuln->check->solutions as $solution): ?>
                    <option <?= $vuln->solution && $vuln->solution->id == $solution->id ? " selected" : "" ?> value="<?= $solution->id ?>">
                        <?= CHtml::encode($solution->title) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        <?php else: ?>
            <i class="icon-minus"></i>
        <?php endif; ?>
    </td>

    <td class="rating">
        <?php if ($vuln->check): ?>
            <select onchange="admin.nessusMapping.saveItem(<?= $vuln->id ?>)">
                <option value="0" class="<?= !$vuln->rating ? "active" : "" ?>"><?= Yii::t("app", "N/A"); ?></option>
                <?php foreach ($ratings as $rating): ?>
                    <option <?= $vuln->rating == $rating ? " selected" : "" ?> value="<?= $rating ?>">
                        <?= TargetCheck::getRatingNames()[$rating] ?>
                    </option>
                <?php endforeach; ?>
            </select>
        <?php else: ?>
            <i class="icon-minus"></i>
        <?php endif; ?>
    </td>
</tr>