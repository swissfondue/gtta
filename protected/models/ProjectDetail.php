<?php

/**
 * This is the model class for table "project_details".
 *
 * The followings are the available columns in table 'project_details':
 * @property integer $id
 * @property integer $project_id
 * @property string $subject
 * @property string $content
 */
class ProjectDetail extends ActiveRecord implements IVariableScopeObject
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return ProjectDetail the static model class
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
		return 'project_details';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		return array(
            array( 'subject, project_id, content', 'required' ),
            array( 'subject', 'length', 'max' => 1000 ),
            array( 'project_id', 'numerical', 'integerOnly' => true ),
		);
	}

        /**
	 * @return array relational rules.
	 */
	public function relations()
	{
		return array(
			'project' => array( self::BELONGS_TO, 'Project', 'project_id' ),
		);
	}

        public function getVariable($name, VariableScope $scope) {
            $vars = array(
                "subject",
                "content",
            );

            if (!in_array($name, $vars)) {
                 throw new Exception(Yii::t("app", "Invalid variable: {var}.", array("{var}" => $name)));
            }

            return $this->$name;

        }

        public function getList($name, $filters, VariableScope $scope) {
            throw new Exception(Yii::t("app", "Invalid list: {list}.", array("{list}" => $name)));
        }

}
