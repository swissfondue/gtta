<?php

/**
 * This is the model class for table "gt_checks".
 *
 * The followings are the available columns in table 'gt_checks':
 * @property integer $id
 * @property integer $gt_module_id
 * @property integer $check_id
 * @property integer $sort_order
 * @property string $description
 * @property string $target_description
 * @property integer $max_sort_order
 * @property integer $gt_dependency_processor_id
 */
class GtCheck extends ActiveRecord
{
    /**
     * @var integer max sort order.
     */
    public $max_sort_order;

	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return GtCheck the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'gt_checks';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		return array(
            array('gt_module_id, check_id, sort_order', 'required'),
            array('gt_module_id, check_id, sort_order, gt_dependency_processor_id', 'numerical', 'integerOnly' => true),
		);
	}

    /**
	 * @return array relational rules.
	 */
	public function relations()
	{
		return array(
            'l10n' => array(self::HAS_MANY, 'GtCheckL10n', 'gt_check_id'),
            'module' => array(self::BELONGS_TO, 'GtModule', 'gt_module_id'),
            'check' => array(self::BELONGS_TO, 'Check', 'check_id'),
            'projectChecks' => array(self::HAS_MANY, 'ProjectGtCheck', 'gt_check_id'),
            'projectCheckInputs' => array(self::HAS_MANY, 'ProjectGtCheckInput', 'gt_check_id'),
            'projectCheckSolutions' => array(self::HAS_MANY, 'ProjectGtCheckSolution', 'gt_check_id'),
            'projectCheckAttachments' => array(self::HAS_MANY, 'ProjectGtCheckAttachment', 'gt_check_id'),
            'processor' => array(self::BELONGS_TO, 'GtDependencyProcessor', 'gt_dependency_processor_id'),
            'dependencies' => array(self::HAS_MANY, 'GtCheckDependency', 'gt_check_id'),
            'suggestedTargets' => array(self::HAS_MANY, 'ProjectGtSuggestedTarget', 'gt_check_id'),
		);
	}

    /**
     * @return string localized description.
     */
    public function getLocalizedDescription()
    {
        if ($this->l10n && count($this->l10n) > 0)
            return $this->l10n[0]->description != null ? $this->l10n[0]->description : $this->description;

        return $this->description;
    }

    /**
     * @return string localized target description.
     */
    public function getLocalizedTargetDescription()
    {
        if ($this->l10n && count($this->l10n) > 0)
            return $this->l10n[0]->target_description != null ? $this->l10n[0]->target_description : $this->target_description;

        return $this->target_description;
    }

    /**
     * @return boolean is running.
     */
    public function getIsRunning()
    {
        return
            $this->check->automated &&
            $this->projectChecks &&
            in_array($this->projectChecks[0]->status, array(
                ProjectGtCheck::STATUS_IN_PROGRESS,
                ProjectGtCheck::STATUS_STOP
            ));
    }
}
