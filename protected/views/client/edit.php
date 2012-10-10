<div class="active-header">
    <?php if (!$client->isNewRecord): ?>
        <div class="pull-right">
            <ul class="nav nav-pills">
                <li><a href="<?php echo $this->createUrl('client/view', array( 'id' => $client->id )); ?>"><?php echo Yii::t('app', 'View'); ?></a></li>
                <li class="active"><a href="<?php echo $this->createUrl('client/edit', array( 'id' => $client->id )); ?>"><?php echo Yii::t('app', 'Edit'); ?></a></li>
            </ul>
        </div>
    <?php endif; ?>

    <h1><?php echo CHtml::encode($this->pageTitle); ?></h1>
</div>

<hr>

<form class="form-horizontal" action="<?php echo Yii::app()->request->url; ?>" method="post">
    <input type="hidden" value="<?php echo Yii::app()->request->csrfToken; ?>" name="YII_CSRF_TOKEN">

    <fieldset>
        <div class="control-group <?php if ($model->getError('name')) echo 'error'; ?>">
            <label class="control-label" for="ClientEditForm_name"><?php echo Yii::t('app', 'Name'); ?></label>
            <div class="controls">
                <input type="text" class="input-xlarge" id="ClientEditForm_name" name="ClientEditForm[name]" value="<?php echo CHtml::encode($model->name); ?>">
                <?php if ($model->getError('name')): ?>
                    <p class="help-block"><?php echo $model->getError('name'); ?></p>
                <?php endif; ?>
            </div>
        </div>

        <div class="control-group <?php if ($model->getError('country')) echo 'error'; ?>">
            <label class="control-label" for="ClientEditForm_country"><?php echo Yii::t('app', 'Country'); ?></label>
            <div class="controls">
                <input type="text" class="input-xlarge" id="ClientEditForm_country" name="ClientEditForm[country]" value="<?php echo CHtml::encode($model->country); ?>">
                <?php if ($model->getError('country')): ?>
                    <p class="help-block"><?php echo $model->getError('country'); ?></p>
                <?php endif; ?>
            </div>
        </div>
        
        <div class="control-group <?php if ($model->getError('state')) echo 'error'; ?>">
            <label class="control-label" for="ClientEditForm_state"><?php echo Yii::t('app', 'State'); ?></label>
            <div class="controls">
                <input type="text" class="input-xlarge" id="ClientEditForm_state" name="ClientEditForm[state]" value="<?php echo CHtml::encode($model->state); ?>">
                <?php if ($model->getError('state')): ?>
                    <p class="help-block"><?php echo $model->getError('state'); ?></p>
                <?php endif; ?>
            </div>
        </div>
        
        <div class="control-group <?php if ($model->getError('city')) echo 'error'; ?>">
            <label class="control-label" for="ClientEditForm_city"><?php echo Yii::t('app', 'City'); ?></label>
            <div class="controls">
                <input type="text" class="input-xlarge" id="ClientEditForm_city" name="ClientEditForm[city]" value="<?php echo CHtml::encode($model->city); ?>">
                <?php if ($model->getError('city')): ?>
                    <p class="help-block"><?php echo $model->getError('city'); ?></p>
                <?php endif; ?>
            </div>
        </div>
        
        <div class="control-group <?php if ($model->getError('address')) echo 'error'; ?>">
            <label class="control-label" for="ClientEditForm_address"><?php echo Yii::t('app', 'Address'); ?></label>
            <div class="controls">
                <input type="text" class="input-xlarge" id="ClientEditForm_address" name="ClientEditForm[address]" value="<?php echo CHtml::encode($model->address); ?>">
                <?php if ($model->getError('address')): ?>
                    <p class="help-block"><?php echo $model->getError('address'); ?></p>
                <?php endif; ?>
            </div>
        </div>

        <div class="control-group <?php if ($model->getError('postcode')) echo 'error'; ?>">
            <label class="control-label" for="ClientEditForm_postcode"><?php echo Yii::t('app', 'Postal Code'); ?></label>
            <div class="controls">
                <input type="text" class="input-xlarge" id="ClientEditForm_postcode" name="ClientEditForm[postcode]" value="<?php echo CHtml::encode($model->postcode); ?>">
                <?php if ($model->getError('postcode')): ?>
                    <p class="help-block"><?php echo $model->getError('postcode'); ?></p>
                <?php endif; ?>
            </div>
        </div>

        <div class="control-group <?php if ($model->getError('website')) echo 'error'; ?>">
            <label class="control-label" for="ClientEditForm_website"><?php echo Yii::t('app', 'Website'); ?></label>
            <div class="controls">
                <input type="text" class="input-xlarge" id="ClientEditForm_website" name="ClientEditForm[website]" value="<?php echo CHtml::encode($model->website); ?>">
                <?php if ($model->getError('website')): ?>
                    <p class="help-block"><?php echo $model->getError('website'); ?></p>
                <?php endif; ?>
            </div>
        </div>

        <div class="control-group <?php if ($model->getError('contactName')) echo 'error'; ?>">
            <label class="control-label" for="ClientEditForm_contactName"><?php echo Yii::t('app', 'Contact Name'); ?></label>
            <div class="controls">
                <input type="text" class="input-xlarge" id="ClientEditForm_contactName" name="ClientEditForm[contactName]" value="<?php echo CHtml::encode($model->contactName); ?>">
                <?php if ($model->getError('contactName')): ?>
                    <p class="help-block"><?php echo $model->getError('contactName'); ?></p>
                <?php endif; ?>
            </div>
        </div>

        <div class="control-group <?php if ($model->getError('contactEmail')) echo 'error'; ?>">
            <label class="control-label" for="ClientEditForm_contactEmail"><?php echo Yii::t('app', 'Contact E-mail'); ?></label>
            <div class="controls">
                <input type="text" class="input-xlarge" id="ClientEditForm_contactEmail" name="ClientEditForm[contactEmail]" value="<?php echo CHtml::encode($model->contactEmail); ?>">
                <?php if ($model->getError('contactEmail')): ?>
                    <p class="help-block"><?php echo $model->getError('contactEmail'); ?></p>
                <?php endif; ?>
            </div>
        </div>
        
        <div class="control-group <?php if ($model->getError('contactPhone')) echo 'error'; ?>">
            <label class="control-label" for="ClientEditForm_contactPhone"><?php echo Yii::t('app', 'Contact Phone'); ?></label>
            <div class="controls">
                <input type="text" class="input-xlarge" id="ClientEditForm_contactPhone" name="ClientEditForm[contactPhone]" value="<?php echo CHtml::encode($model->contactPhone); ?>">
                <?php if ($model->getError('contactPhone')): ?>
                    <p class="help-block"><?php echo $model->getError('contactPhone'); ?></p>
                <?php endif; ?>
            </div>
        </div>

        <div class="control-group <?php if ($model->getError('contactFax')) echo 'error'; ?>">
            <label class="control-label" for="ClientEditForm_contactFax"><?php echo Yii::t('app', 'Contact Fax'); ?></label>
            <div class="controls">
                <input type="text" class="input-xlarge" id="ClientEditForm_contactFax" name="ClientEditForm[contactFax]" value="<?php echo CHtml::encode($model->contactFax); ?>">
                <?php if ($model->getError('contactFax')): ?>
                    <p class="help-block"><?php echo $model->getError('contactFax'); ?></p>
                <?php endif; ?>
            </div>
        </div>
        
        <div class="form-actions">
            <button type="submit" class="btn"><?php echo Yii::t('app', 'Save'); ?></button>
        </div>
    </fieldset>
</form>
