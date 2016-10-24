<tr data-item-id="<?= $vuln->id ?>">
    <td>
        <input type="checkbox" id="NessusMappingVulns_<?= $vuln->id; ?>" name="NessusMappingEditVulnsForm[vulns][]" value="<?= $vuln->id; ?>">
    </td>
    <td>
        <label for="NessusMappingVulns_<?= $vuln->id; ?>">
            <?= CHtml::encode($vuln->nessus_plugin_name); ?>
        </label>
    </td>
    <td>
        <?= CHtml::encode($vuln->nessus_rating); ?>
    </td>
    <td class="<?= $vuln->check ? "mapped" : "not-mapped" ?>">
        <a href="#" onclick="admin.nessusMapping.showCheckSearchPopup(<?= $vuln->id ?>)">
            <?= CHtml::encode($vuln->check ? $vuln->check->name :  Yii::t("app", "Not Mapped")); ?>
        </a>
    </td>
    <td>
        <?php if ($vuln->check): ?>
            <select>
                <option value="0" class="<?= !$vuln->result ? "active" : "" ?>"><?= Yii::t("app", "N/A"); ?></option>
                <?php foreach ($vuln->check->results as $result): ?>
                    <option class="<?= $vuln->result && $vuln->result->id == $result->id ? "active" : "" ?>" value="<?= $result->id ?>"><?= $result->title ?></option>
                <?php endforeach; ?>
            </select>
        <?php endif; ?>
    </td>
    <td>
        <?php if ($vuln->check): ?>
            <select>
                <option value="0" class="<?= !$vuln->solution ? "active" : "" ?>"><?= Yii::t("app", "N/A"); ?></option>
                <?php foreach ($vuln->check->solutions as $solution): ?>
                    <option class="<?= $vuln->solution && $vuln->solution->id == $solution->id ? "active" : "" ?>" value="<?= $solution->id ?>"><?= $solution->title ?></option>
                <?php endforeach; ?>
            </select>
        <?php endif; ?>
    </td>
    <td>
        <?php if ($vuln->check): ?>
            <select>
                <option value="0" class="<?= !$vuln->rating ? "active" : "" ?>"><?= Yii::t("app", "N/A"); ?></option>
                <?php foreach ($ratings as $rating): ?>
                    <option class="<?= $vuln->rating == $rating ? "active" : "" ?>" value="<?= $rating ?>"><?= TargetCheck::getRatingNames()[$rating] ?></option>
                <?php endforeach; ?>
            </select>
        <?php endif; ?>
    </td>
</tr>