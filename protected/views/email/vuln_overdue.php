<html>
    <body>
        <p><?php echo Yii::t('app', 'Dear {name}', array( '{name}' => CHtml::encode($userName) )); ?>,</p>

        <p>
            <?php
                echo Yii::t('app', '{projectLink} project has overdued vulnerabilities', array(
                    '{projectLink}' => '<a href="' . Yii::app()->createAbsoluteUrl('vulntracker/vulns', array('id' => $projectId)) . '">' . CHtml::encode($projectName) . '</a>',
                ));
            ?>.
        </p>

        <div>
            ---<br>
            <?php echo Yii::app()->name; ?> <?php echo Yii::t('app', 'Notification System'); ?>
        </div>
    </body>
</html>