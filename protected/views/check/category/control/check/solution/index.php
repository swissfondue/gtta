<div class="active-header">
    <div class="pull-right buttons">
        <?php if (count($solutions) > 0): ?>
        <a class="btn" onclick="expandAllSolutionForms()">
            <?php echo Yii::t("app", "Expand All Solutions"); ?>
        </a>
        <?php endif; ?>
        <?php echo CHtml::ajaxLink(CHtml::encode('New Solution'), CController::createUrl('check/editsolution', array( 'id' => $category->id, 'control' => $control->id, 'check' => $check->id )),
            array('update' => '#solution-div-new'),
            array('id' => 'solution-link-'.uniqid(), 'class' => 'btn solution-link','onclick' => "$('#solution-div-new').show()")
        );?>
    </div>
    <h1><?php echo Yii::t('app', 'Solutions'); ?></h1>
    <div class="solution-form" id="solution-div-new"></div>
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
                                    <?php echo CHtml::ajaxLink(CHtml::encode($solution->localizedTitle), CController::createUrl('check/editsolution', array( 'id' => $category->id, 'control' => $control->id, 'check' => $check->id, 'solution' => $solution->id )),
                                        array('update' => '#solution-div-'.$solution->id),
                                        array('id' => 'solution-link-'.$solution->id, 'class' => 'solution-link')
                                    );?>
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

    /**
     * Expand all solution forms
     */
    function expandAllSolutionForms() {
        var solutions = <?php echo json_encode($solutionIds)?>;
        solutions.forEach(function(id) {
            elem=$("#solution-link-" + id);
            if (elem.find('[id*="solution-form"]').length>0) {
                elem.toggle();
            }
            else {
                elem.click();
            }
        });
    }
</script>
