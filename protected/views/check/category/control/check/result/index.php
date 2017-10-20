<div class="active-header">
    <div class="pull-right buttons">
        <?php if (count($results) > 0): ?>
            <a class="btn" onclick="expandAllResultForms()">
                <?php echo Yii::t("app", "Expand All Results"); ?>
            </a>
        <?php endif; ?>
        <?php echo CHtml::ajaxLink(CHtml::encode('New Result'), CController::createUrl('check/editresult', array( 'id' => $category->id, 'control' => $control->id, 'check' => $check->id )),
            array('update' => '#result-div-new'),
            array('id' => 'result-link-'.uniqid(), 'class' => 'btn result-link', 'onclick' => "$('#result-div-new').show()")
        );?>
    </div>
    <h1><?php echo Yii::t('app', 'Results'); ?></h1>
    <div class="solution-form" id="result-div-new"></div>
</div>
<hr>
<div class="container">
    <div class="row">
        <div class="span12">
            <?php $resultIds = []; if (count($results) > 0): ?>
                <table class="table result-list">
                    <tbody>
                        <tr>
                            <th class="result"><?php echo Yii::t('app', 'Result'); ?></th>
                            <th class="actions">&nbsp;</th>
                        </tr>
                        <?php foreach ($results as $result): $resultIds[]=$result->id;?>
                            <tr data-id="<?php echo $result->id; ?>" data-control-url="<?php echo $this->createUrl('check/controlresult'); ?>">
                                <td class="result">
                                    <?php echo CHtml::ajaxLink(CHtml::encode($result->localizedTitle), CController::createUrl('check/editresult', array( 'id' => $category->id, 'control' => $control->id, 'check' => $check->id, 'result' => $result->id )),
                                        array('update' => '#result-div-'.$result->id),
                                        array('id' => 'result-link-'.$result->id, 'class' => 'result-link')
                                    );?>

                                    <div class="result-form" id="result-div-<?php echo $result->id;?>"></div>
                                </td>
                                <td class="actions">
                                    <a href="#del" title="<?php echo Yii::t('app', 'Delete'); ?>" onclick="system.control.del(<?php echo $result->id; ?>);"><i class="icon icon-remove"></i></a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <?php echo Yii::t('app', 'No results yet.'); ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
    $(document).ready(function () {
        /**
         * Show result-form
         *
         */
        $(".result-link").click(function (event) {
            var elem = $(this).next(".result-form");

            if (elem.find('[id*="result-form"]').is(":visible")) {
                elem.hide();
            }
            else {
                elem.show();
            }
        });
    });

    /**
     * Expand all result forms
     */
    function expandAllResultForms() {
        var results = <?php echo json_encode($resultIds)?>;
        results.forEach(function(id) {
            elem=$("#result-link-" + id);

            if (elem.find('[id*="result-form"]').length>0) {
                elem.toggle();
            }
            else {
                elem.click();
            }
        });
    }
</script>
