<?php

/**
 * Class ChecklistTemplateEditForm
 */
class ChecklistTemplateEditForm extends LocalizedFormModel {
    /**
     * @var string name
     */
    public $name;

    /**
     * @var string description
     */
    public $description;

    /**
     * @return array
     */
    public function rules() {
        return array(
            array( 'name', 'required' ),
            array( 'name', 'length', 'max' => 1000 ),
            array( 'localizedItems' , 'safe' ),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels() {
        return array(
            'name' => Yii::t('app', 'Name'),
            'description' => Yii::t('app', 'Description'),
        );
    }
}