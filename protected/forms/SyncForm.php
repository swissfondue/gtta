<?php

/**
 * This is the model class for packages sync
 */
class SyncForm extends CFormModel
{
    /**
     * @var string merge strategy
     */
    public $strategy;

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        return array(
            array("strategy", "in", "range" => array(System::GIT_MERGE_STRATEGY_THEIRS, System::GIT_MERGE_STRATEGY_OURS)),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'strategy' => Yii::t('app', 'Strategy'),
        );
    }
}