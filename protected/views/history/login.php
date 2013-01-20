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

                <?php if ($p->pageCount > 1): ?>
                    <div class="pagination">
                        <ul>
                            <li <?php if (!$p->prevPage) echo 'class="disabled"'; ?>><a href="<?php echo $this->createUrl('history/logins', array( 'page' => $p->prevPage ? $p->prevPage : $p->page )); ?>" title="<?php echo Yii::t('app', 'Previous Page'); ?>">&laquo;</a></li>
                            <?php for ($i = 1; $i <= $p->pageCount; $i++): ?>
                                <li <?php if ($i == $p->page) echo 'class="active"'; ?>>
                                    <a href="<?php echo $this->createUrl('history/logins', array( 'page' => $i )); ?>"><?php echo $i; ?></a>
                                </li>
                            <?php endfor; ?>
                            <li <?php if (!$p->nextPage) echo 'class="disabled"'; ?>><a href="<?php echo $this->createUrl('history/logins', array( 'page' => $p->nextPage ? $p->nextPage : $p->page )); ?>" title="<?php echo Yii::t('app', 'Next Page'); ?>">&raquo;</a></li>
                        </ul>
                    </div>
                <?php endif; ?>
            <?php else: ?>
                <?php echo Yii::t('app', 'No history entries yet.'); ?>
            <?php endif; ?>
        </div>
    </div>
</div>
