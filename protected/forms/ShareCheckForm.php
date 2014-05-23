<?php

/**
 * This is the model class for share check form.
 */
class ShareCheckForm extends CFormModel {
	/**
     * @var string external control id
     */
    public $externalControlId;

    /**
     * @var string external reference id
     */
    public $externalReferenceId;

    /**
	 * @return array validation rules for model attributes.
	 */
	public function rules() {
		return array(
            array("externalControlId, externalReferenceId", "required"),
            array("externalControlId, externalReferenceId", "numerical", "integerOnly" => true),
            array("externalControlId", "checkControl"),
            array("externalReferenceId", "checkReference"),
		);
	}

    /**
	 * Checks if external control id exists.
	 */
	public function checkControl($attribute, $params) {
        /** @var System $system */
		$system = System::model()->findByPk(1);
        $catalogs = json_decode($system->community_catalogs_cache);

        if (!$catalogs) {
            $this->addError("externalControlId", Yii::t("app", "Invalid control."));
            return false;
        }

        $found = false;

        foreach ($catalogs->categories as $cat) {
            foreach ($cat->controls as $ctrl) {
                if ($ctrl->id == $this->externalControlId) {
                    $found = true;
                    break;
                }
            }

            if ($found) {
                break;
            }
        }

        if (!$found) {
            $this->addError("externalControlId", Yii::t("app", "Invalid control."));
            return false;
        }

        return true;
	}

     /**
	 * Checks if external reference id exists.
	 */
	public function checkReference($attribute, $params) {
        /** @var System $system */
		$system = System::model()->findByPk(1);
        $catalogs = json_decode($system->community_catalogs_cache);

        if (!$catalogs) {
            $this->addError("externalReferenceId", Yii::t("app", "Invalid reference."));
            return false;
        }

        $found = false;

        foreach ($catalogs->references as $ref) {
            if ($ref->id == $this->externalReferenceId) {
                $found = true;
                break;
            }
        }

        if (!$found) {
            $this->addError("externalReferenceId", Yii::t("app", "Invalid reference."));
            return false;
        }

        return true;
	}
}