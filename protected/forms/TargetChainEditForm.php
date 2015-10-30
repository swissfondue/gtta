<?php

/**
 * This is the model class for target edit chain form.
 */
class TargetChainEditForm extends CFormModel
{
    /**
     * @var string relations
     */
    public $relations;

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        return array(
            array( 'relations', 'required' ),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'relations' => Yii::t('app', 'Relations'),
        );
    }
}