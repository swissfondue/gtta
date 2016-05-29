<div class="active-header">
    <h1>
        <?php echo CHtml::encode($this->pageTitle); ?>
    </h1>
</div>

<hr>

<div class="container">
    <div class="row">
        <div class="span8">
            <table class="table category-list">
                <tbody>
                <tr>
                    <th class="name"><?php echo Yii::t("app", "Category"); ?></th>
                </tr>
                <tr>
                    <td class="name">
                        <a href="<?php echo $this->createUrl("customization/checks/fields"); ?>"><?php echo Yii::t("app", "Fields"); ?></a>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
