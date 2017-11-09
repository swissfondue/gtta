<div class="active-header">
    <?php if (!isset($embedded) || !$embedded): ?>
        <div class="pull-right">
            <ul class="nav nav-pills">
                <li><a href="<?php echo $this->createUrl('check/editcheck', array( 'id' => $category->id, 'control' => $control->id, 'check' => $check->id )); ?>"><?php echo Yii::t('app', 'Edit'); ?></a></li>
                <?php if ($check->automated): ?>
                    <li><a href="<?php echo $this->createUrl('check/scripts', array( 'id' => $category->id, 'control' => $control->id, 'check' => $check->id )); ?>"><?php echo Yii::t('app', 'Scripts'); ?></a></li>
                <?php endif; ?>
                <li><a href="<?php echo $this->createUrl('check/results', array( 'id' => $category->id, 'control' => $control->id, 'check' => $check->id )); ?>"><?php echo Yii::t('app', 'Results'); ?></a></li>
                <li class="active"><a href="<?php echo $this->createUrl('check/solutions', array( 'id' => $category->id, 'control' => $control->id, 'check' => $check->id )); ?>"><?php echo Yii::t('app', 'Solutions'); ?></a></li>
                <li><a href="<?php echo $this->createUrl("check/sharecheck", array("id" => $category->id, "control" => $control->id, "check" => $check->id)); ?>"><?php echo Yii::t('app', "Share"); ?></a></li>
            </ul>
        </div>
    <?php endif; ?>

    <div class="pull-right buttons">
        <?php if (isset($embedded) && $embedded && count($solutions) > 0): ?>
            <a class="btn" onclick="user.check.expandAllSolutionForms()">
                <i class="icon icon-arrow-down"></i>
                <?php echo Yii::t("app", "Expand All"); ?>
            </a>
            &nbsp;
        <?php endif; ?>

        <a class="btn" href="<?php echo $this->createUrl('check/editsolution', array('id' => $category->id, 'control' => $control->id, 'check' => $check->id)) ?>"><i class="icon icon-plus"></i> <?php echo Yii::t('app', 'New Solution'); ?></a>
    </div>

    <h1><?php echo Yii::t('app', 'Solutions'); ?></h1>
</div>
<hr>
<div class="container">
    <div class="row">
        <div class="span12">
            <?php $solutionIds = []; if (count($solutions) > 0): ?>
                <table class="table solution-list">
                    <tbody>
                        <tr>
                            <th class="solution"><?php echo Yii::t('app', 'Solution'); ?></th>
                            <th class="actions">&nbsp;</th>
                        </tr>
                        <?php foreach ($solutions as $solution): $solutionIds[]=$solution->id;?>
                            <tr data-id="<?php echo $solution->id; ?>" data-control-url="<?php echo $this->createUrl('check/controlsolution'); ?>">
                                <td class="solution">
                                    <?=
                                        CHtml::ajaxLink(
                                            CHtml::encode($solution->localizedTitle),
                                            CController::createUrl("check/editsolution", ["id" => $category->id, "control" => $control->id, "check" => $check->id, "solution" => $solution->id]),
                                            ["update" => "#solution-div-" . $solution->id],
                                            ["id" => "solution-link-" . uniqid(), "class" => "solution-link"]
                                        );
                                    ?>
                                    <div class="solution-form" id="solution-div-<?php echo $solution->id;?>"></div>
                                </td>
                                <td class="actions">
                                    <a href="#del" title="<?php echo Yii::t('app', 'Delete'); ?>" onclick="system.control.del(<?php echo $solution->id; ?>);"><i class="icon icon-remove"></i></a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <?php echo Yii::t('app', 'No solutions yet.'); ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
    $(document).ready(function () {
        /**
         * Show solution-form
         */
        $(".solution-link").click(function (event) {
            var elem = $(this).next(".solution-form");
            if (elem.find('[id*="solution-form"]').is(":visible")) {
                elem.hide();
            }
            else {
                elem.show();
            }
        });
    });
</script>
