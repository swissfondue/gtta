<div class="active-header">
    <div class="pull-right buttons">
        <div class="search-form">
            <form class="form-search" action="<?php echo $this->createUrl('check/search'); ?>" method="post" onsubmit="return system.search.validate();">
                <input type="hidden" value="<?php echo Yii::app()->request->csrfToken; ?>" name="YII_CSRF_TOKEN">
                <input name="SearchForm[query]" class="search-query" type="text" value="<?php echo $model->query ? CHtml::encode($model->query) : Yii::t('app', 'Search...'); ?>" onfocus="system.search.focus();" onblur="system.search.blur();" />
            </form>
        </div>
    </div>

    <h1>
        <?php echo CHtml::encode($this->pageTitle); ?>
    </h1>
</div>

<hr>

<div class="container">
    <div class="row">
        <div class="span8">
            <?php if (count($checks) > 0): ?>
                <table class="table check-list">
                    <tbody>
                        <tr>
                            <th class="name"><?php echo Yii::t('app', 'Check'); ?></th>
                        </tr>
                        <?php foreach ($checks as $check): ?>
                            <tr>
                                <td class="name">
                                    <a href="<?php echo $this->createUrl('check/editcheck', array( 'id' => $check->check->control->check_category_id, 'control' => $check->check->check_control_id, 'check' => $check->check_id )); ?>"><?php echo CHtml::encode($check->name); ?></a>
                                    <?php if ($check->check->automated): ?>
                                        <i class="icon-cog" title="<?php echo Yii::t('app', 'Automated'); ?>"></i>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <?php echo Yii::t('app', 'No checks match your search criteria.'); ?>
            <?php endif; ?>
        </div>
    </div>
</div>
