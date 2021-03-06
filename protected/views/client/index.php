<div class="active-header">
    <?php if (User::checkRole(User::ROLE_ADMIN)): ?>
        <div class="pull-right">
            <a class="btn" href="<?php echo $this->createUrl('client/edit') ?>"><i class="icon icon-plus"></i> <?php echo Yii::t('app', 'New Client'); ?></a>
        </div>
    <?php endif; ?>

    <div class="pull-right">
        <div class="search-form">
            <form class="form-search" action="<?php echo $this->createUrl('client/search'); ?>" method="post" onsubmit="return system.search.validate();">
                <input type="hidden" value="<?php echo Yii::app()->request->csrfToken; ?>" name="YII_CSRF_TOKEN">
                <input name="SearchForm[query]" class="search-query" type="text" value="<?php echo Yii::t('app', 'Search...'); ?>" onfocus="system.search.focus();" onblur="system.search.blur();">
            </form>
        </div>
    </div>

    <h1><?php echo CHtml::encode($this->pageTitle); ?></h1>
</div>

<hr>

<div class="container">
    <div class="row">
        <div class="span8">
            <?php if (count($clients) > 0): ?>
                <table class="table client-list">
                    <tbody>
                        <tr>
                            <th class="name"><?php echo Yii::t('app', 'Client'); ?></th>
                            <?php if (User::checkRole(User::ROLE_ADMIN)): ?>
                                <th class="actions">&nbsp;</th>
                            <?php endif; ?>
                        </tr>
                        <?php foreach ($clients as $client): ?>
                            <tr data-id="<?php echo $client->id; ?>" data-control-url="<?php echo $this->createUrl('client/control'); ?>">
                                <td class="name">
                                    <a href="<?php echo $this->createUrl('client/view', array( 'id' => $client->id )); ?>"><?php echo CHtml::encode($client->name); ?></a>
                                </td>
                                <?php if (User::checkRole(User::ROLE_ADMIN)): ?>
                                    <td class="actions">
                                        <a href="#del" title="<?php echo Yii::t('app', 'Delete'); ?>" onclick="system.control.del(<?php echo $client->id; ?>, '<?php echo Yii::t('app', 'WARNING! ALL PROJECTS RELATED TO THIS CLIENT WILL BE DELETED!'); ?>');"><i class="icon icon-remove"></i></a>
                                    </td>
                                <?php endif; ?>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>

                <?php echo $this->renderPartial('/layouts/partial/pagination', array('p' => $p, 'url' => 'client/index', 'params' => array())); ?>
            <?php else: ?>
                <?php echo Yii::t('app', 'No clients yet.'); ?>
            <?php endif; ?>
        </div>
    </div>
</div>
