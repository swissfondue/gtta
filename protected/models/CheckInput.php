<?php

/**
 * This is the model class for table "check_inputs".
 *
 * The followings are the available columns in table 'check_inputs':
 * @property integer $id
 * @property integer $check_script_id
 * @property string $name
 * @property string $description
 * @property string $value
 * @property integer $sort_order
 * @property integer $max_sort_order
 * @property string $type
 */
class CheckInput extends CActiveRecord {
    /**
     * Input types.
     */
    const TYPE_TEXT = 0;
    const TYPE_TEXTAREA = 1;
    const TYPE_CHECKBOX = 2;
    const TYPE_RADIO = 3;
    const TYPE_FILE = 4;

    /**
     * @var integer max sort order.
     */
    public $max_sort_order;

	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return CheckInput the static model class
	 */
	public static function model($className=__CLASS__) {
		return parent::model($className);
	}

	/**
	 * @return string the associated database table name
	 */
	public function tableName() {
		return 'check_inputs';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules() {
		return array(
            array('check_script_id, name', 'required' ),
            array('name', 'length', 'max' => 1000 ),
            array('sort_order', 'numerical', 'integerOnly' => true, 'min' => 0 ),
            array('type', 'in', 'range' => array(
                self::TYPE_TEXT,
                self::TYPE_TEXTAREA,
                self::TYPE_CHECKBOX,
                self::TYPE_RADIO,
                self::TYPE_FILE
            )),
            array('description, value', 'safe'),
		);
	}

    /**
	 * @return array relational rules.
	 */
	public function relations()
	{
		return array(
            'l10n' => array(self::HAS_MANY, 'CheckInputL10n', 'check_input_id'),
            'script' => array(self::BELONGS_TO, 'CheckScript', 'check_script_id'),
            'targetInputs' => array(self::HAS_MANY, 'TargetCheckInput', 'check_input_id'),
            'projectInputs' => array(self::HAS_MANY, 'ProjectGtCheckInput', 'check_input_id'),
		);
	}

    /**
     * @return string localized name.
     */
    public function getLocalizedName()
    {
        if ($this->l10n && count($this->l10n) > 0)
            return $this->l10n[0]->name != null ? $this->l10n[0]->name : $this->name;

        return $this->name;
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
     * Get file data
     * @return string
     */
    public function getFileData() {
        if ($this->type != self::TYPE_FILE) {
            throw new Exception('Invalid check input type');
        }

        $script = CheckScript::model()->with("package")->findByPk($this->check_script_id);
        $fileName = preg_replace('/[^a-zA-Z0-9]/', '_', $this->name) . '.txt';

        $pm = new PackageManager();
        $filePath = $pm->getFilesPath($script->package) . "/" . $fileName;
        $content = '';

        if (file_exists($filePath) && filesize($filePath)) {
            $fp = fopen($filePath, 'r');
            $content = fread($fp, filesize($filePath));
            fclose($fp);
        }

        return $content;
    }

    /**
     * Set file data
     * @param $data string
     */
    public function setFileData($data) {
        if ($this->type != self::TYPE_FILE) {
            throw new Exception('Invalid check input type');
        }

        $script = CheckScript::model()->findByPk($this->check_script_id);
        $fileName = preg_replace('/[^a-zA-Z0-9]/', '_', $this->name) . '.txt';

        $pm = new PackageManager();
        $filesPath = $pm->getFilesPath($script->package);
        $filePath =  $filesPath . "/" . $fileName;

        if (!is_dir($filesPath)) {
            @mkdir($filesPath, 0777, true);
        }

        $fp = fopen($filePath, 'w');
        fwrite($fp, $data);
        fclose($fp);
    }

    /**
     * Delete file
     */
    public function deleteFile() {
        if ($this->type != self::TYPE_FILE) {
            throw new Exception('Invalid check input type');
        }

        $script = CheckScript::model()->findByPk($this->check_script_id);
        $fileName = preg_replace('/[^a-zA-Z0-9]/', '_', $this->name) . '.txt';

        $pm = new PackageManager();
        $filePath = $pm->getFilesPath($script->package) . "/" . $fileName;

        if (file_exists($filePath)) {
            @unlink($filePath);
        }
    }
}