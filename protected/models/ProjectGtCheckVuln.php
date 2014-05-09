<?php

/**
 * This is the model class for table "project_gt_check_vulns".
 *
 * The followings are the available columns in table 'project_gt_check_vulns':
 * @property integer $project_id
 * @property integer $gt_check_id
 * @property integer $user_id
 * @property string $deadline
 * @property string $status
 */
class ProjectGtCheckVuln extends ActiveRecord {
    /**
     * Vulnerability statuses.
     */
    const STATUS_OPEN = 'open';
    const STATUS_RESOLVED = 'resolved';

	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return ProjectGtCheckVuln the static model class
	 */
	public static function model($className=__CLASS__) {
		return parent::model($className);
	}

	/**
	 * @return string the associated database table name
	 */
	public function tableName() {
		return 'project_gt_check_vulns';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules() {
		return array(
            array('project_id, gt_check_id', 'required'),
            array('project_id, user_id, gt_check_id', 'numerical', 'integerOnly' => true),
            array('status', 'in', 'range' => array(self::STATUS_OPEN, self::STATUS_RESOLVED)),
		);
	}

    /**
	 * @return array relational rules.
	 */
	public function relations() {
		return array(
            'project' => array(self::BELONGS_TO, 'Project', 'project_id'),
            'check' => array(self::BELONGS_TO, 'GtCheck', 'gt_check_id'),
            'user' => array(self::BELONGS_TO, 'User', 'user_id'),
            'projectGtCheck' => array(self::BELONGS_TO, 'ProjectGtCheck', array('project_id', 'gt_check_id')),
		);
	}

    /**
     * Check if vulnerability is overdued.
     */
    public function getOverdued() {
        if (!$this->deadline) {
            return false;
        }

        if ($this->status == self::STATUS_RESOLVED) {
            return false;
        }

        $deadline = new DateTime($this->deadline . ' 00:00:00');
        $today = new DateTime();
        $today->setTime(0, 0, 0);

        if ($today > $deadline) {
            return true;
        }

        return false;
    }
}
