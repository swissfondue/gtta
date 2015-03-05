<?php

/**
 * Class TargetListAddForm
 */
class TargetListAddForm extends CFormModel {
    /**
     * @var string targetList.
     */
    public $targetList;

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        return array(
            array( 'targetList', 'required' ),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'targetList'  => Yii::t('app', 'Target List')
        );
    }
}