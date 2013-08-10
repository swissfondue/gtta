<?php

/**
 * This is the model class for check copy form.
 */
class CheckCopyForm extends LocalizedFormModel
{
	/**
     * @var integer id.
     */
    public $id;

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		return array(
			array('id', 'required'),
            array('id', 'numerical', 'integerOnly' => true),
            array('id', 'checkExists'),
		);
	}
    
    /**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => Yii::t('app', 'Check'),
		);
	}

    /**
     * Check if check exists.
     */
    public function checkExists($attribute, $params)
    {
        $check = Check::model()->findByPk($this->id);

        if (!$check) {
            $this->addError('id', Yii::t('app', 'Check not found.'));
            return false;
        }

        return true;
    }
}