<?php

/**
 * Report controller.
 */
class ReportController extends Controller {
    /**
	 * @return array action filters
	 */
	public function filters() {
		return [
            "https",
			"checkAuth",
            "showReports",
            "idle",
		];
	}

    /**
     * Display an effort estimation form.
     */
	public function actionEffort() {
        $references = Reference::model()->findAllByAttributes(
            [],
            ["order" => "t.name ASC"]
        );

        $language = Language::model()->findByAttributes([
            "code" => Yii::app()->language
        ]);

        if ($language) {
            $language = $language->id;
        }

        $categories = CheckCategory::model()->with([
            "l10n" => [
                "joinType" => "LEFT JOIN",
                "on" => "language_id = :language_id",
                "params" => ["language_id" => $language]
            ]
        ])->findAllByAttributes(
            [],
            ["order" => "COALESCE(l10n.name, t.name) ASC"]
        );

        $checks = Check::model()->with([
            "l10n" => [
                "joinType" => "LEFT JOIN",
                "on" => "language_id = :language_id",
                "params" => ["language_id" => $language]
            ],
            "control"
        ])->findAllByAttributes(
            [],
            ["order" => "t.sort_order ASC"]
        );

        $referenceArray = [];
        $checkArray = [];

        foreach ($references as $reference) {
            $referenceArray[] = [
                "id" => $reference->id,
                "name" => $reference->name
            ];
        }

        foreach ($categories as $category) {
            $checkCategory = [
                "id" => $category->id,
                "name" => $category->localizedName,
                "checks" => []
            ];

            foreach ($checks as $check) {
                if ($check->control->check_category_id == $category->id) {
                    $checkCategory["checks"][] = [
                        "effort" => $check->effort,
                        "reference" => $check->reference_id
                    ];
                }
            }

            $checkArray[] = $checkCategory;
        }

        $this->breadcrumbs[] = [Yii::t("app", "Effort Estimation"), ""];

        // display the page
        $this->pageTitle = Yii::t("app", "Effort Estimation");
		$this->render("effort", [
            "references" => $referenceArray,
            "checks" => $checkArray,
        ]);
    }
}
