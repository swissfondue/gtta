<div class="active-header">
    <div class="pull-right">
        <ul class="nav nav-pills">
            <li><a href="<?php echo $this->createUrl('reporttemplate/edit', array( 'id' => $template->id )); ?>"><?php echo Yii::t('app', 'Edit'); ?></a></li>
            <li><a href="<?php echo $this->createUrl('reporttemplate/summary', array( 'id' => $template->id )); ?>"><?php echo Yii::t('app', 'Summary Blocks'); ?></a></li>
            <li class="active"><a href="<?php echo $this->createUrl('reporttemplate/sections', array( 'id' => $template->id )); ?>"><?php echo Yii::t('app', 'Vulnerability Sections'); ?></a></li>
        </ul>
    </div>
    <div class="pull-right buttons">
        <a class="btn" href="<?php echo $this->createUrl('reporttemplate/editsection', array( 'id' => $template->id )) ?>"><i class="icon icon-plus"></i> <?php echo Yii::t('app', 'New Section'); ?></a>
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
                            <tr data-id="<?php echo $section->id; ?>" data-control-url="<?php echo $this->createUrl('reporttemplate/controlsection'); ?>">
                                <td class="section">
                                    <a href="<?php echo $this->createUrl('reporttemplate/editsection', array( 'id' => $template->id, 'section' => $section->id )); ?>"><?php echo CHtml::encode($section->localizedTitle); ?></a>
                                </td>
                                <td class="actions">
                                    <a href="#del" title="<?php echo Yii::t('app', 'Delete'); ?>" onclick="system.control.del(<?php echo $section->id; ?>);"><i class="icon icon-remove"></i></a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>

                <?php if ($p->pageCount > 1): ?>
                    <div class="pagination">
                        <ul>
                            <li <?php if (!$p->prevPage) echo 'class="disabled"'; ?>><a href="<?php echo $this->createUrl('reporttemplate/sections', array( 'id' => $template->id, 'page' => $p->prevPage ? $p->prevPage : $p->page )); ?>" title="<?php echo Yii::t('app', 'Previous Page'); ?>">&laquo;</a></li>
                            <?php for ($i = 1; $i <= $p->pageCount; $i++): ?>
                                <li <?php if ($i == $p->page) echo 'class="active"'; ?>>
                                    <a href="<?php echo $this->createUrl('reporttemplate/sections', array( 'id' => $template->id, 'page' => $i )); ?>"><?php echo $i; ?></a>
                                </li>
                            <?php endfor; ?>
                            <li <?php if (!$p->nextPage) echo 'class="disabled"'; ?>><a href="<?php echo $this->createUrl('reporttemplate/sections', array( 'id' => $template->id, 'page' => $p->nextPage ? $p->nextPage : $p->page )); ?>" title="<?php echo Yii::t('app', 'Next Page'); ?>">&raquo;</a></li>
                        </ul>
                    </div>
                <?php endif; ?>
            <?php else: ?>
                <?php echo Yii::t('app', 'No sections yet.'); ?>
            <?php endif; ?>
        </div>
    </div>
</div>
