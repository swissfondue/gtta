<h1><?php echo CHtml::encode($this->pageTitle); ?></h1>

<hr>

<div class="container">
    <div class="row">
        <div class="span8">
            <?php if (count($entries) > 0): ?>
                <table class="table login-history">
                    <tbody>
                        <tr>
                            <th class="time"><?php echo Yii::t('app', 'Time'); ?></th>
                            <th class="name"><?php echo Yii::t('app', 'Name'); ?></th>
                        </tr>
                        <?php foreach ($entries as $entry): ?>
                            <tr>
                                <td class="time">
                                    <?php echo substr($entry->create_time, 0, strpos($entry->create_time, '.')); ?>
                                </td>
                                <td class="name">
                                    <?php if ($entry->user): ?>
                                        <a href="<?php echo $this->createUrl('user/edit', array( 'id' => $entry->user_id )); ?>">
                                            <?php echo CHtml::encode($entry->user ? ($entry->user->name ? $entry->user->name : $entry->user->email) : $entry->user_name); ?>
                                        </a>
                                    <?php else: ?>
                                        <?php echo CHtml::encode($entry->user ? ($entry->user->name ? $entry->user->name : $entry->user->email) : $entry->user_name); ?>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>

                <?php echo $this->renderPartial('/layouts/partial/pagination', array('p' => $p, 'url' => 'history/logins', 'params' => array())); ?>
            <?php else: ?>
                <?php echo Yii::t('app', 'No history entries yet.'); ?>
            <?php endif; ?>
        </div>
    </div>
</div>
