<div class="active-header">
    <div class="pull-right buttons">
        <div class="search-form">
            <form class="form-search" action="<?php echo $this->createUrl('client/search'); ?>" method="post" onsubmit="return system.search.validate();">
                <input type="hidden" value="<?php echo Yii::app()->request->csrfToken; ?>" name="YII_CSRF_TOKEN">
                <input name="SearchForm[query]" class="search-query" type="text" value="<?php echo $model->query ? CHtml::encode($model->query) : Yii::t('app', 'Search...'); ?>" onfocus="system.search.focus();" onblur="system.search.blur();">
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
            <?php if (count($clients) > 0): ?>
                <table class="table client-list">
                    <tbody>
                        <tr>
                            <th class="name"><?php echo Yii::t('app', 'Name'); ?></th>
                        </tr>
                        <?php foreach ($clients as $client): ?>
                            <tr>
                                <td class="name">
                                    <a href="<?php echo $this->createUrl('client/view', array( 'id' => $client->id )); ?>"><?php echo CHtml::encode($client->name); ?></a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <?php echo Yii::t('app', 'No clients match your search criteria.'); ?>
            <?php endif; ?>
        </div>
    </div>
</div>
