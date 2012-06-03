<div class="active-header">
    <div class="pull-right">
        <ul class="nav nav-pills">
            <li <?php if (!$category->advanced) echo 'class="active"'; ?>><a href="#basic" onclick="user.check.setAdvanced('<?php echo $this->createUrl('project/savecategory', array( 'id' => $project->id, 'target' => $target->id, 'category' => $category->check_category_id )); ?>', 0);"><?php echo Yii::t('app', 'Basic'); ?></a></li>
            <li <?php if ($category->advanced) echo 'class="active"'; ?>><a href="#advanced" onclick="user.check.setAdvanced('<?php echo $this->createUrl('project/savecategory', array( 'id' => $project->id, 'target' => $target->id, 'category' => $category->check_category_id )); ?>', 1);"><?php echo Yii::t('app', 'Advanced'); ?></a></li>
        </ul>
    </div>

    <h1><?php echo CHtml::encode($this->pageTitle); ?></h1>
</div>

<hr>

<div class="container">
    <div class="row">
        <div class="span8">
            <?php if (count($checks) > 0): ?>
                <table class="table target-check-list">
                    <tbody>
                        <tr>
                            <th class="name"><?php echo Yii::t('app', 'Check'); ?></th>
                            <th class="status">&nbsp;</th>
                        </tr>
                        <?php foreach ($checks as $check): ?>
                            <tr class="header" data-id="<?php echo $check->id; ?>">
                                <td class="name">
                                    <a href="#toggle" onclick="$('tr.content[data-id=<?php echo $check->id; ?>]').toggle();"><?php echo CHtml::encode($check->localizedName); ?></a>
                                    <?php if ($check->automated): ?>
                                        <i class="icon-cog" title="<?php echo Yii::t('app', 'Automated'); ?>"></i>
                                    <?php endif; ?>
                                </td>
                                <td class="status">
                                    <?php if ($check->targetChecks && $check->targetChecks[0]->status == TargetCheck::STATUS_FINISHED): ?>
                                        <?php
                                            switch ($check->targetChecks[0]->rating)
                                            {
                                                case TargetCheck::RATING_HIDDEN:
                                                    echo '<span class="label">' . $ratings[TargetCheck::RATING_HIDDEN] . '</span>';
                                                    break;

                                                case TargetCheck::RATING_INFO:
                                                    echo '<span class="label label-info">' . $ratings[TargetCheck::RATING_INFO] . '</span>';
                                                    break;

                                                case TargetChecK::RATING_LOW_RISK:
                                                    echo '<span class="label label-low-risk">' . $ratings[TargetCheck::RATING_LOW_RISK] . '</span>';
                                                    break;

                                                case TargetChecK::RATING_MED_RISK:
                                                    echo '<span class="label label-med-risk">' . $ratings[TargetCheck::RATING_MED_RISK] . '</span>';
                                                    break;

                                                case TargetChecK::RATING_HIGH_RISK:
                                                    echo '<span class="label label-high-risk">' . $ratings[TargetCheck::RATING_HIGH_RISK] . '</span>';
                                                    break;
                                            }
                                        ?>
                                    <?php else: ?>
                                        &nbsp;
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <tr class="content hidden-object" data-id="<?php echo $check->id; ?>" data-save-url="<?php echo $this->createUrl('project/savecheck', array( 'id' => $project->id, 'target' => $target->id, 'category' => $category->check_category_id, 'check' => $check->id )); ?>">
                                <td class="content" colspan="2">
                                    <div class="check-content">
                                        <table class="table check-form">
                                            <tbody>
                                                <?php if ($check->localizedBackgroundInfo): ?>
                                                    <tr>
                                                        <th>
                                                            <?php echo Yii::t('app', 'Background Info'); ?>
                                                        </th>
                                                        <td>
                                                            <?php echo CHtml::encode($check->localizedBackgroundInfo); ?>
                                                        </td>
                                                    </tr>
                                                <?php endif; ?>
                                                <?php if ($check->localizedImpactInfo): ?>
                                                    <tr>
                                                        <th>
                                                            <?php echo Yii::t('app', 'Impact Info'); ?>
                                                        </th>
                                                        <td>
                                                            <?php echo CHtml::encode($check->localizedImpactInfo); ?>
                                                        </td>
                                                    </tr>
                                                <?php endif; ?>
                                                <?php if ($check->localizedManualInfo): ?>
                                                    <tr>
                                                        <th>
                                                            <?php echo Yii::t('app', 'Manual Info'); ?>
                                                        </th>
                                                        <td>
                                                            <?php echo CHtml::encode($check->localizedManualInfo); ?>
                                                        </td>
                                                    </tr>
                                                <?php endif; ?>
                                                <?php if ($check->inputs && $check->automated): ?>
                                                    <?php foreach ($check->inputs as $input): ?>
                                                        <tr>
                                                            <th>
                                                                <?php echo CHtml::encode($input->localizedName); ?>
                                                            </th>
                                                            <td>
                                                                <textarea name="TargetCheckEditForm_<?php echo $check->id; ?>[inputs][<?php echo $input->id; ?>]" class="max-width" rows="2" id="TargetCheckEditForm_<?php echo $check->id; ?>_inputs_<?php echo $input->id; ?>"><?php
                                                                    $value = '';

                                                                    if ($check->targetCheckInputs)
                                                                        foreach ($check->targetCheckInputs as $inputValue)
                                                                            if ($inputValue->check_input_id == $input->id)
                                                                            {
                                                                                $value = $inputValue->value;
                                                                                break;
                                                                            }

                                                                    if (!$value && $input->localizedValue)
                                                                        $value = $input->localizedValue;

                                                                    if ($value)
                                                                        echo CHtml::encode($value);
                                                                ?></textarea>
                                                                <?php if ($input->localizedDescription): ?>
                                                                    <p class="help-block">
                                                                        <?php echo CHtml::encode($input->localizedDescription); ?>
                                                                    </p>
                                                                <?php endif; ?>
                                                            </td>
                                                        </tr>
                                                    <?php endforeach; ?>
                                                <?php endif; ?>
                                                <tr>
                                                    <th>
                                                        <?php echo Yii::t('app', 'Result'); ?>
                                                    </th>
                                                    <td>
                                                        <textarea name="TargetCheckEditForm_<?php echo $check->id; ?>[result]" class="max-width" rows="10" id="TargetCheckEditForm_<?php echo $check->id; ?>_result"><?php if ($check->targetChecks) echo $check->targetChecks[0]->result; ?></textarea>
                                                    </td>
                                                </tr>
                                                <?php if ($check->results): ?>
                                                    <tr>
                                                        <th>
                                                            <?php echo Yii::t('app', 'Insert Result'); ?>
                                                        </th>
                                                        <td>
                                                            <ul class="results">
                                                                <?php foreach ($check->results as $result): ?>
                                                                    <li>
                                                                        <a href="#insert" onclick="$('#TargetCheckEditForm_<?php echo $check->id; ?>_result').val($('#TargetCheckEditForm_<?php echo $check->id; ?>_result').val() + this.innerHTML + '\n');"><?php echo CHtml::encode($result->localizedResult); ?></a>
                                                                    </li>
                                                                <?php endforeach; ?>
                                                            </ul>
                                                        </td>
                                                    </tr>
                                                <?php endif; ?>
                                                <?php if ($check->solutions): ?>
                                                    <tr>
                                                        <th>
                                                            <?php echo Yii::t('app', 'Solution'); ?>
                                                        </th>
                                                        <td>
                                                            <ul class="solutions">
                                                                <?php foreach ($check->solutions as $solution): ?>
                                                                    <li>
                                                                        <?php
                                                                            $checked = false;

                                                                            if ($check->targetCheckSolutions)
                                                                                foreach ($check->targetCheckSolutions as $solutionValue)
                                                                                    if ($solutionValue->check_solution_id == $solution->id)
                                                                                    {
                                                                                        $checked = true;
                                                                                        break;
                                                                                    }
                                                                        ?>
                                                                        <?php if ($check->multiple_solutions): ?>
                                                                            <label class="checkbox">
                                                                                <input name="TargetCheckEditForm_<?php echo $check->id; ?>[solutions][]" type="checkbox" value="<?php echo $solution->id; ?>" <?php if ($checked) echo 'checked'; ?>>
                                                                        <?php else: ?>
                                                                            <label class="radio">
                                                                                <input name="TargetCheckEditForm_<?php echo $check->id; ?>[solutions][]" type="radio" value="<?php echo $solution->id; ?>" <?php if ($checked) echo 'checked'; ?>>
                                                                        <?php endif; ?>
                                                                            <?php echo CHtml::encode($solution->localizedSolution); ?>
                                                                        </label>
                                                                    </li>
                                                                <?php endforeach; ?>
                                                            </ul>
                                                        </td>
                                                    </tr>
                                                <?php endif; ?>
                                                <tr>
                                                    <th>
                                                        <?php echo Yii::t('app', 'Result Rating'); ?>
                                                    </th>
                                                    <td>
                                                        <ul class="rating">
                                                            <?php foreach(array( TargetCheck::RATING_HIDDEN, TargetCheck::RATING_INFO, TargetCheck::RATING_LOW_RISK, TargetCheck::RATING_MED_RISK, TargetCheck::RATING_HIGH_RISK ) as $rating): ?>
                                                                <li>
                                                                    <label class="radio">
                                                                        <input type="radio" name="TargetCheckEditForm_<?php echo $check->id; ?>[rating]" value="<?php echo $rating; ?>" <?php if ($check->targetChecks && $check->targetChecks[0]->rating == $rating) echo 'checked'; ?>>
                                                                        <?php echo $ratings[$rating]; ?>
                                                                    </label>
                                                                </li>
                                                            <?php endforeach; ?>
                                                        </ul>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>&nbsp;</td>
                                                    <td>
                                                        <button class="btn" onclick="user.check.save(<?php echo $check->id; ?>, false);"><?php echo Yii::t('app', 'Save'); ?></button>&nbsp;
                                                        <button class="btn" onclick="user.check.save(<?php echo $check->id; ?>, true);"><?php echo Yii::t('app', 'Save & Next'); ?></button>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <?php echo Yii::t('app', 'No checks in this category.'); ?>
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

<script>
    var ratings = {
        <?php
            $ratingNames = array();

            foreach ($ratings as $k => $v)
            {
                $class = null;

                switch ($k)
                {
                    case TargetCheck::RATING_INFO:
                        $class = 'label-info';
                        break;

                    case TargetCheck::RATING_LOW_RISK:
                        $class = 'label-low-risk';
                        break;

                    case TargetCheck::RATING_MED_RISK:
                        $class = 'label-med-risk';
                        break;

                    case TargetCheck::RATING_HIGH_RISK:
                        $class = 'label-high-risk';
                        break;
                }

                $ratingNames[] = $k . ':' . json_encode(array(
                    'text'  => CHtml::encode($v),
                    'class' => $class
                ));
            }

            echo implode(',', $ratingNames);
        ?>
    };
</script>