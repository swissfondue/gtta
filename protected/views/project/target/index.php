<div class="active-header">
    <div class="pull-right">
        <ul class="nav nav-pills">
            <li class="active"><a href="<?php echo $this->createUrl('project/target', array( 'id' => $project->id, 'target' => $target->id )); ?>"><?php echo Yii::t('app', 'View'); ?></a></li>
            <li><a href="<?php echo $this->createUrl('project/edittarget', array( 'id' => $project->id, 'target' => $target->id )); ?>"><?php echo Yii::t('app', 'Edit'); ?></a></li>
        </ul>
    </div>

    <h1><?php echo CHtml::encode($this->pageTitle); ?></h1>
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
                            <th class="stats">&nbsp;</th>
                            <th class="percent">&nbsp;</th>
                            <th class="actions">&nbsp;</th>
                        </tr>
                        <?php foreach ($categories as $category): ?>
                            <tr>
                                <td class="name">
                                    <a href="<?php echo $this->createUrl('project/checks', array( 'id' => $project->id, 'target' => $target->id, 'category' => $category->check_category_id )); ?>"><?php echo CHtml::encode($category->category->localizedName); ?></a>
                                </td>
                                <td class="stats">
                                    <span class="high-risk"><?php echo $category->high_risk_count; ?></span> /
                                    <span class="med-risk"><?php echo $category->med_risk_count; ?></span> /
                                    <span class="low-risk"><?php echo $category->low_risk_count; ?></span>
                                </td>
                                <td class="percent">
                                    <?php echo $category->check_count ? sprintf('%.2f', ($category->finished_count / $category->check_count) * 100) : '0.00'; ?>%
                                </td>
                                <td class="actions">
                                    <a href="#del" title="<?php echo Yii::t('app', 'Delete'); ?>" onclick="category.del(<?php echo $category->check_category_id; ?>);"><i class="icon icon-remove"></i></a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>

                <div class="pagination">
                    <ul>
                        <li <?php if (!$p->prevPage) echo 'class="disabled"'; ?>><a href="<?php echo $this->createUrl('project/target', array( 'id' => $project->id, 'target' => $target->id, 'page' => $p->prevPage ? $p->prevPage : $p->page )); ?>" title="<?php echo Yii::t('app', 'Previous Page'); ?>">&laquo;</a></li>
                        <?php for ($i = 1; $i <= $p->pageCount; $i++): ?>
                            <li <?php if ($i == $p->page) echo 'class="active"'; ?>>
                                <a href="<?php echo $this->createUrl('project/target', array( 'id' => $project->id, 'target' => $target->id, 'page' => $i )); ?>"><?php echo $i; ?></a>
                            </li>
                        <?php endfor; ?>
                        <li <?php if (!$p->nextPage) echo 'class="disabled"'; ?>><a href="<?php echo $this->createUrl('project/target', array( 'id' => $project->id, 'target' => $target->id, 'page' => $p->nextPage ? $p->nextPage : $p->page )); ?>" title="<?php echo Yii::t('app', 'Next Page'); ?>">&raquo;</a></li>
                    </ul>
                </div>
            <?php else: ?>
                <?php echo Yii::t('app', 'No categories yet.'); ?>
            <?php endif; ?>
        </div>
        <div class="span4">
            <h3><a href="#toggle" onclick="$('#project-info').slideToggle('slow');"><?php echo Yii::t('app', 'Project Information'); ?></a></h3>

            <div class="info-block" id="project-info">
                <table class="table client-details">
                    <tbody>
                        <tr>
                            <th>
                                <?php echo Yii::t('app', 'Client'); ?>
                            </th>
                            <td>
                                <a href="<?php echo $this->createUrl('client/view', array( 'id' => $client->id )); ?>"><?php echo CHtml::encode($client->name); ?></a>
                            </td>
                        </tr>
                        <tr>
                            <th>
                                <?php echo Yii::t('app', 'Year'); ?>
                            </th>
                            <td>
                                <?php echo CHtml::encode($project->year); ?>
                            </td>
                        </tr>
                        <tr>
                            <th>
                                <?php echo Yii::t('app', 'Deadline'); ?>
                            </th>
                            <td>
                                <?php echo CHtml::encode($project->deadline); ?>
                            </td>
                        </tr>
                        <tr>
                            <th>
                                <?php echo Yii::t('app', 'Status'); ?>
                            </th>
                            <td>
                                <?php echo $statuses[$project->status]; ?>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <?php if ($project->details): ?>
                <h3><a href="#toggle" onclick="$('#project-details').slideToggle('slow');"><?php echo Yii::t('app', 'Project Details'); ?></a></h3>

                <div class="info-block" id="project-details">
                    <?php
                        $counter = 0;
                        foreach ($project->details as $detail):
                    ?>
                        <div class="project-detail <?php if (!$counter) echo 'borderless'; ?>">
                            <div class="subject"><?php echo CHtml::encode($detail->subject); ?></div>
                            <div class="content"><?php echo CHtml::encode($detail->content); ?></div>
                        </div>
                    <?php
                            $counter++;
                        endforeach;
                    ?>
                </div>
            <?php endif; ?>

            <?php if ($client->hasDetails): ?>
                <h3><a href="#toggle" onclick="$('#client-address').slideToggle('slow');"><?php echo Yii::t('app', 'Client Address'); ?></a></h3>

                <div class="info-block hidden-object" id="client-address">
                    <table class="table client-details">
                        <tbody>
                            <?php if ($client->country): ?>
                                <tr>
                                    <th>
                                        <?php echo Yii::t('app', 'Country'); ?>
                                    </th>
                                    <td>
                                        <?php echo CHtml::encode($client->country); ?>
                                    </td>
                                </tr>
                            <?php endif; ?>
                            <?php if ($client->state): ?>
                                <tr>
                                    <th>
                                        <?php echo Yii::t('app', 'State'); ?>
                                    </th>
                                    <td>
                                        <?php echo CHtml::encode($client->state); ?>
                                    </td>
                                </tr>
                            <?php endif; ?>
                            <?php if ($client->city): ?>
                                <tr>
                                    <th>
                                        <?php echo Yii::t('app', 'City'); ?>
                                    </th>
                                    <td>
                                        <?php echo CHtml::encode($client->city); ?>
                                    </td>
                                </tr>
                            <?php endif; ?>
                            <?php if ($client->address): ?>
                                <tr>
                                    <th>
                                        <?php echo Yii::t('app', 'Address'); ?>
                                    </th>
                                    <td>
                                        <?php echo CHtml::encode($client->address); ?>
                                    </td>
                                </tr>
                            <?php endif; ?>
                            <?php if ($client->postcode): ?>
                                <tr>
                                    <th>
                                        <?php echo Yii::t('app', 'P.C.'); ?>
                                    </th>
                                    <td>
                                        <?php echo CHtml::encode($client->postcode); ?>
                                    </td>
                                </tr>
                            <?php endif; ?>
                            <?php if ($client->website): ?>
                                <tr>
                                    <th>
                                        <?php echo Yii::t('app', 'Website'); ?>
                                    </th>
                                    <td>
                                        <a href="<?php echo CHtml::encode($client->website); ?>"><?php echo CHtml::encode($client->website); ?></a>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
            <?php if ($client->hasContact): ?>
                <h3><a href="#toggle" onclick="$('#client-contact').slideToggle('slow');"><?php echo Yii::t('app', 'Client Contact'); ?></a></h3>

                <div class="info-block hidden-object" id="client-contact">
                    <table class="table client-details">
                        <tbody>
                            <?php if ($client->contact_name): ?>
                                <tr>
                                    <th>
                                        <?php echo Yii::t('app', 'Name'); ?>
                                    </th>
                                    <td>
                                        <?php echo CHtml::encode($client->contact_name); ?>
                                    </td>
                                </tr>
                            <?php endif; ?>
                            <?php if ($client->contact_email): ?>
                                <tr>
                                    <th>
                                        <?php echo Yii::t('app', 'E-mail'); ?>
                                    </th>
                                    <td>
                                        <a href="mailto:<?php echo CHtml::encode($client->contact_email); ?>"><?php echo CHtml::encode($client->contact_email); ?></a>
                                    </td>
                                </tr>
                            <?php endif; ?>
                            <?php if ($client->contact_phone): ?>
                                <tr>
                                    <th>
                                        <?php echo Yii::t('app', 'Phone'); ?>
                                    </th>
                                    <td>
                                        <?php echo CHtml::encode($client->contact_phone); ?>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
