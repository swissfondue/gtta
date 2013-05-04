<html>
    <body>
        <p><?php echo Yii::t('app', 'Dear {name}', array( '{name}' => CHtml::encode($userName) )); ?>,</p>

        <p>
            <?php
                echo Yii::t("app", "You or someone else has requested a password recovery for your account in {appName}.", array(
                    "{appName}" => Yii::app()->name
                ));
            ?>

            <?php
                echo Yii::t("app", "In order to recover your password please follow this link - {link}.", array(
                    "{link}" => '<a href="' . $url . '">' . $url . '</a>'
                ));
            ?>
        </p>

        <div>
            ---<br>
            <?php echo Yii::app()->name; ?> <?php echo Yii::t('app', 'Notification System'); ?>
        </div>
    </body>
</html>