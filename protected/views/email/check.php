<html>
    <body>
        <p><?php echo Yii::t('app', 'Dear {name}', array( '{name}' => CHtml::encode($userName) )); ?>,</p>

        <p>
            <?php
                echo Yii::t('app', '{checkLink} check on target {targetLink} has been finished.', array(
                    '{checkLink}'  => '<a href="' . Yii::app()->createAbsoluteUrl('project/checks', array('id' => $projectId, 'target' => $targetId, 'category' => $categoryId)) . '#check-' . $checkId . '">' . CHtml::encode($checkName) . '</a>',
                    '{targetLink}' => '<a href="' . Yii::app()->createAbsoluteUrl('project/target', array('id' => $projectId, 'target' => $targetId)) . '">' . CHtml::encode($targetHost) . '</a>',
                ));
            ?>
        </p>

        <div>
            ---<br>
            <?php echo Yii::app()->name; ?> <?php echo Yii::t('app', 'Notification System'); ?>
        </div>
    </body>
</html>