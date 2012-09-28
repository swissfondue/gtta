<html>
    <body>
        <p><?php echo Yii::t('app', 'Dear {name}', array( '{name}' => CHtml::encode($userName) )); ?>,</p>

        <p>
            <?php
                echo Yii::t('app', '{projectLink} project has overdued vulnerabilities', array(
                    '{projectLink}' => '<a href="' . Yii::app()->createAbsoluteUrl('project/view', array( 'id' => $projectId )) . '">' . CHtml::encode($projectName) . '</a>',
                ));
            ?>:
        </p>

        <ul>
            <?php foreach ($targets as $target): ?>
                <li>
                    <a href="<?php echo Yii::app()->createAbsoluteUrl('project/target', array( 'id' => $projectId, 'target' => $target['target']->id )); ?>"><?php echo $target['target']->host; ?></a> - <?php echo $target['overdued']; ?> <?php echo Yii::t('app', 'vulnerability|vulnerabilities', $target['overdued']); ?>
                </li>
            <?php endforeach; ?>
        </ul>

        <div>
            ---<br>
            <?php echo Yii::app()->name; ?> <?php echo Yii::t('app', 'Notification System'); ?>
        </div>
    </body>
</html>