<div class="active-header">
    <div class="pull-right">
        <a class="btn" href="<?php echo $this->createUrl("checklisttemplate/editcategory") ?>"><i class="icon icon-plus"></i> <?php echo Yii::t("app", "New Category"); ?></a>&nbsp;
    </div>

    <div class="pull-right">
        <div class="search-form">
            <form class="form-search" action="<?php echo $this->createUrl('category/search'); ?>" method="post" onsubmit="return system.search.validate();">
                <input type="hidden" value="<?php echo Yii::app()->request->csrfToken; ?>" name="YII_CSRF_TOKEN">
                <input name="SearchForm[query]" class="search-query" type="text" value="<?php echo Yii::t('app', 'Search...'); ?>" onfocus="system.search.focus();" onblur="system.search.blur();">
            </form>
        </div>
    </div>

    <h1>
        <?php echo CHtml::encode($this->pageTitle); ?>
        <?php if ($count): ?>
            <span class="header-detail">(<?php echo $count; ?>)</span>
        <?php endif; ?>
    </h1>
</div>

<hr>

<div class="container">
    <div class="row">
        <div class="span8">
            <?php if (count($categories) > 0): ?>
                <table class="table category-list">
                    <tbody>
                    <tr>
                        <th class="name"><?php echo Yii::t('app', 'Category'); ?></th>
                        <th class="check-count"><?php echo Yii::t('app', 'Checks'); ?></th>
                        <th class="actions">&nbsp;</th>
                    </tr>
                    <?php foreach ($categories as $category): ?>
                        <tr data-id="<?php echo $category->id; ?>" data-control-url="<?php echo $this->createUrl('checklisttemplate/controlcategory'); ?>">
                            <td class="name">
                                <a href="<?php echo $this->createUrl('checklisttemplate/viewcategory', array( 'id' => $category->id )); ?>"><?php echo CHtml::encode($category->localizedName); ?></a>
                            </td>
                            <td>
                                <?php echo count($category->templates); ?>
                            </td>
                            <td class="actions">
                                <a href="#del" title="<?php echo Yii::t('app', 'Delete'); ?>" onclick="system.control.del(<?php echo $category->id; ?>, '<?php echo Yii::t('app', 'WARNING! ALL CHECKS WITHIN THIS CATEGORY WILL BE DELETED!'); ?>');"><i class="icon icon-remove"></i></a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>

                <?php echo $this->renderPartial('/layouts/partial/pagination', array('p' => $p, 'url' => 'check/index', 'params' => array())); ?>
            <?php else: ?>
                <?php echo Yii::t('app', 'No categories yet.'); ?>
            <?php endif; ?>
        </div>
    </div>
</div>
