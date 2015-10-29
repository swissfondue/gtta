<?php

/**
 * This is the model class for get check link.
 */
class TargetCheckLinkForm extends CFormModel
{
    /**
     * @var integer target id.
     */
    public $target;

    /**
     * @var integer check id.
     */
    public $check;

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        return array(
            array( 'target, check', 'required' ),
            array( 'target, check', 'numerical', 'integerOnly' => true ),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'target'               => Yii::t('app', 'Target'),
            'check'                 => Yii::t('app', 'Check'),
        );
    }
}