<?php

/**
 * This is the model class for reference edit form.
 */
class RelationTemplateEditForm extends LocalizedFormModel
{
    /**
     * @var string name.
     */
    public $name;

    /**
     * @var string url.
     */
    public $relations;

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        return array(
            array( 'name', 'required' ),
            array( 'name', 'length', 'max' => 1000 ),
            array( 'localizedItems, relations', 'safe' ),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'name' => Yii::t('app', 'Name'),
            'relations'  => Yii::t('app', 'Relations'),
        );
    }
}