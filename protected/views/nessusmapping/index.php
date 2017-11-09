<h1><?= CHtml::encode($this->pageTitle); ?></h1>

<hr>

<div class="container">
    <div class="row">
        <div class="span8">
            <?php if (count($mappings) > 0): ?>
                <table class="table mapping-list">
                    <tbody>
                        <tr>
                            <th class="date"><?= Yii::t("app", "Created At"); ?></th>
                            <th class="name"><?= Yii::t("app", "Name"); ?></th>
                            <th class="actions">&nbsp;</th>
                        </tr>
                        <?php foreach ($mappings as $mapping): ?>
                            <tr data-id="<?= $mapping->id; ?>" data-control-url="<?= $this->createUrl("nessusmapping/control", ["id" => $mapping->id]); ?>">
                                <td class="date">
                                    <?= date("Y-m-d H:i:s", strtotime($mapping->created_at)); ?>
                                </td>
                                <td class="name">
                                    <a href="<?= $this->createUrl("nessusmapping/edit", ["id" => $mapping->id]) ?>"><?= CHtml::encode($mapping->name); ?></a>
                                </td>
                                <td class="actions">
                                    <a href="#del" title="<?= Yii::t("app", "Delete"); ?>" onclick="system.control.del(<?= $mapping->id; ?>, '<?= Yii::t("app", "WARNING! ALL CHECKS RELATED TO THIS MAPPING WILL BE DELETED!"); ?>');"><i class="icon icon-remove"></i></a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>

                <?= $this->renderPartial("/layouts/partial/pagination", ["p" => $p, "url" => "nessusmapping/index", "params" => []]); ?>
            <?php else: ?>
                <?= Yii::t("app", "No mappings yet."); ?>
            <?php endif; ?>
        </div>
    </div>
</div>