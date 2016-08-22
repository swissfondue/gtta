<div class="active-header">
    <?= $this->renderPartial("partial/menu", ["template" => $template]); ?>

    <div class="pull-right buttons">
        <a class="btn" href="<?php echo $this->createUrl('reporttemplate/editvulnsection', array( 'id' => $template->id )) ?>"><i class="icon icon-plus"></i> <?php echo Yii::t('app', 'New Section'); ?></a>
    </div>

    <h1><?php echo CHtml::encode($this->pageTitle); ?></h1>
</div>

<hr>

<div class="container">
    <div class="row">
        <div class="span8">
            <?php if (count($sections) > 0): ?>
                <table class="table section-list">
                    <tbody>
                        <tr>
                            <th class="section"><?php echo Yii::t('app', 'Section'); ?></th>
                            <th class="actions">&nbsp;</th>
                        </tr>
                        <?php foreach ($sections as $section): ?>
                            <tr data-id="<?php echo $section->id; ?>" data-control-url="<?php echo $this->createUrl('reporttemplate/controlvulnsection'); ?>">
                                <td class="section">
                                    <a href="<?php echo $this->createUrl('reporttemplate/editvulnsection', array( 'id' => $template->id, 'section' => $section->id )); ?>"><?php echo CHtml::encode($section->localizedTitle); ?></a>
                                </td>
                                <td class="actions">
                                    <a href="#del" title="<?php echo Yii::t('app', 'Delete'); ?>" onclick="system.control.del(<?php echo $section->id; ?>);"><i class="icon icon-remove"></i></a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>

                <?php echo $this->renderPartial('/layouts/partial/pagination', array('p' => $p, 'url' => 'reporttemplate/vulnsections', 'params' => array('id' => $template->id))); ?>
            <?php else: ?>
                <?php echo Yii::t('app', 'No sections yet.'); ?>
            <?php endif; ?>
        </div>
    </div>
</div>
