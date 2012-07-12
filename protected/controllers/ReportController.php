<?php

/**
 * Report controller.
 */
class ReportController extends Controller
{
    /**
	 * @return array action filters
	 */
	public function filters()
	{
		return array(
			'checkAuth',
            'checkUser',
            'ajaxOnly + projectlist, targetlist',
            'postOnly + projectlist, targetlist',
		);
	}

    /**
     * Normalize X coordinate.
     */
    private function _normalizeCoord($coord, $min, $max)
    {
        if ($coord < $min)
            $coord = $min;
        elseif ($coord > $max)
            $coord = $max;

        return $coord;
    }

    /**
     * Rating image.
     */
    private function _generateRatingImage($rating)
    {
        $image     = imagecreatefrompng(Yii::app()->basePath . '/../images/gradient.png');
        $lineCoord = round($rating * 40);
        $color     = imagecolorallocate($image, 0, 0, 0);

        imageline($image, $lineCoord, 0, $lineCoord, 30, $color);

        $topArrow = array(
            $this->_normalizeCoord($lineCoord - 5, 0, 200), 0,
            $this->_normalizeCoord($lineCoord + 5, 0, 200), 0,
            $lineCoord, 5
        );

        $bottomArrow = array(
            $this->_normalizeCoord($lineCoord - 5, 0, 200), 29,
            $this->_normalizeCoord($lineCoord + 5, 0, 200), 29,
            $lineCoord, 24
        );

        imagefilledpolygon($image, $topArrow, count($topArrow) / 2, $color);
        imagefilledpolygon($image, $bottomArrow, count($bottomArrow) / 2, $color);

        $hashName = hash('sha256', rand() . time() . rand());
        $filePath = Yii::app()->params['tmpPath'] . '/' . $hashName . '.png';

        imagepng($image, $filePath, 0);
        imagedestroy($image);

        return $filePath;
    }

    /**
     * Generate project function.
     */
    private function _generateProjectReport($clientId, $projectId, $targetIds)
    {
        $project = Project::model()->findByAttributes(array(
            'client_id' => $clientId,
            'id'        => $projectId
        ));

        if ($project === null)
        {
            Yii::app()->user->setFlash('error', Yii::t('app', 'Project doesn\\\'t exist.'));
            return;
        }

        if (!$targetIds || !count($targetIds))
        {
            Yii::app()->user->setFlash('error', Yii::t('app', 'Please select at least 1 target.'));
            return;
        }

        $language = Language::model()->findByAttributes(array(
            'code' => Yii::app()->language
        ));

        if ($language)
            $language = $language->id;

        $criteria = new CDbCriteria();
        $criteria->addInCondition('id', $targetIds);
        $criteria->addColumnCondition(array( 'project_id' => $project->id ));
        $targets = Target::model()->findAll($criteria);

        $data = array();
        $overallRating = 0.0;

        $ratings = array(
            TargetCheck::RATING_HIDDEN    => Yii::t('app', 'Hidden'),
            TargetCheck::RATING_INFO      => Yii::t('app', 'Info'),
            TargetCheck::RATING_LOW_RISK  => Yii::t('app', 'Low Risk'),
            TargetCheck::RATING_MED_RISK  => Yii::t('app', 'Med Risk'),
            TargetCheck::RATING_HIGH_RISK => Yii::t('app', 'High Risk'),
        );

        foreach ($targets as $target)
        {
            $targetData = array(
                'host'       => $target->host,
                'rating'     => 0.0,
                'categories' => array(),
            );

            // get all categories
            $categories = TargetCheckCategory::model()->with(array(
                'category' => array(
                    'with' => array(
                        'l10n' => array(
                            'joinType' => 'LEFT JOIN',
                            'on'       => 'language_id = :language_id',
                            'params'   => array( 'language_id' => $language )
                        )
                    )
                )
            ))->findAllByAttributes(array(
                'target_id' => $target->id
            ));

            if (!$categories)
                continue;

            foreach ($categories as $category)
            {
                $categoryData = array(
                    'name'   => $category->category->localizedName,
                    'rating' => 0.0,
                    'checks' => array()
                );

                $params = array(
                    'check_category_id' => $category->check_category_id
                );

                if (!$category->advanced)
                    $params['advanced'] = 'FALSE';

                $checks = Check::model()->with(array(
                    'l10n' => array(
                        'joinType' => 'LEFT JOIN',
                        'on'       => 'l10n.language_id = :language_id',
                        'params'   => array( 'language_id' => $language )
                    ),
                    'targetChecks' => array(
                        'alias'    => 'tcs',
                        'joinType' => 'INNER JOIN',
                        'on'       => 'tcs.target_id = :target_id AND tcs.status = :status AND tcs.rating != :hidden',
                        'params'   => array(
                            'target_id' => $target->id,
                            'status'    => TargetCheck::STATUS_FINISHED,
                            'hidden'    => TargetCheck::RATING_HIDDEN,
                        ),
                    ),
                    'targetCheckSolutions' => array(
                        'alias'    => 'tss',
                        'joinType' => 'LEFT JOIN',
                        'on'       => 'tss.target_id = :target_id',
                        'params'   => array( 'target_id' => $target->id ),
                        'with'     => array(
                            'solution' => array(
                                'alias'    => 'tss_s',
                                'joinType' => 'LEFT JOIN',
                                'with'     => array(
                                    'l10n' => array(
                                        'alias'  => 'tss_s_l10n',
                                        'on'     => 'tss_s_l10n.language_id = :language_id',
                                        'params' => array( 'language_id' => $language )
                                    )
                                )
                            )
                        )
                    ),
                    'targetCheckAttachments' => array(
                        'alias'    => 'tas',
                        'joinType' => 'LEFT JOIN',
                        'on'       => 'tas.target_id = :target_id',
                        'params'   => array( 'target_id' => $target->id )
                    )
                ))->findAllByAttributes(
                    $params,
                    array( 'order' => 't.name ASC' )
                );

                if (!$checks)
                    continue;

                foreach ($checks as $check)
                {
                    $checkData = array(
                        'name'       => $check->localizedName,
                        'background' => $check->localizedBackgroundInfo,
                        'reference'  => $check->localizedReference,
                        'question'   => $check->localizedQuestion,
                        'result'     => $check->targetChecks[0]->result,
                        'rating'     => 0,
                        'ratingName' => $ratings[$check->targetChecks[0]->rating],
                        'solutions'  => array(),
                        'images'     => array(),
                    );

                    switch ($check->targetChecks[0]->rating)
                    {
                        case TargetCheck::RATING_HIDDEN:
                            $checkData['rating'] = 0;
                            break;

                        case TargetCheck::RATING_INFO:
                            $checkData['rating'] = 1;
                            break;

                        case TargetCheck::RATING_LOW_RISK:
                            $checkData['rating'] = 2;
                            break;

                        case TargetCheck::RATING_MED_RISK:
                            $checkData['rating'] = 3;
                            break;

                        case TargetCheck::RATING_HIGH_RISK:
                            $checkData['rating'] = 4;
                            break;
                    }

                    if ($check->targetCheckSolutions)
                        foreach ($check->targetCheckSolutions as $solution)
                            $checkData['solutions'][] = $solution->solution->localizedSolution;

                    if ($check->targetCheckAttachments)
                        foreach ($check->targetCheckAttachments as $attachment)
                            if (in_array($attachment->type, array( 'image/jpeg', 'image/png', 'image/gif' )))
                                $checkData['images'][] = Yii::app()->params['attachments']['path'] . '/' . $attachment->path;

                    $categoryData['checks'][] = $checkData;
                    $categoryData['rating']  += $checkData['rating'];
                }

                $categoryData['rating'] /= count($checks);

                $targetData['categories'][] = $categoryData;
                $targetData['rating']      += $categoryData['rating'];
            }

            if ($targetData['categories'])
                $targetData['rating'] /= count($targetData['categories']);

            $data[]         = $targetData;
            $overallRating += $targetData['rating'];
        }

        if ($data)
            $overallRating /= count($data);

        // include all PHPRtfLite libraries
        Yii::setPathOfAlias('rtf', Yii::app()->basePath . '/extensions/PHPRtfLite/PHPRtfLite');
        Yii::import('rtf.Autoloader', true);
        PHPRtfLite_Autoloader::setBaseDir(Yii::app()->basePath . '/extensions/PHPRtfLite');
        Yii::registerAutoloader(array( 'PHPRtfLite_Autoloader', 'autoload' ), true);

        $rtf = new PHPRtfLite();
        $rtf->setCharset('UTF-8');
        $rtf->setMargins(1.5, 1, 1.5, 1);

        // borders
        $thinBorder = new PHPRtfLite_Border(
            $rtf,
            new PHPRtfLite_Border_Format(1, '#909090'),
            new PHPRtfLite_Border_Format(1, '#909090'),
            new PHPRtfLite_Border_Format(1, '#909090'),
            new PHPRtfLite_Border_Format(1, '#909090')
        );

        // fonts
        $h1Font = new PHPRtfLite_Font(24, 'Helvetica');
        $h1Font->setBold();

        $h2Font = new PHPRtfLite_Font(20, 'Helvetica');
        $h2Font->setBold();

        $h3Font = new PHPRtfLite_Font(16, 'Helvetica');
        $h3Font->setBold();

        $textFont = new PHPRtfLite_Font(12, 'Helvetica');

        $boldFont = new PHPRtfLite_Font(12, 'Helvetica');
        $boldFont->setBold();

        // paragraphs
        $titlePar = new PHPRtfLite_ParFormat(PHPRtfLite_ParFormat::TEXT_ALIGN_CENTER);
        $titlePar->setSpaceBefore(0);

        $projectPar = new PHPRtfLite_ParFormat(PHPRtfLite_ParFormat::TEXT_ALIGN_CENTER);
        $projectPar->setSpaceAfter(20);

        $h3Par = new PHPRtfLite_ParFormat();
        $h3Par->setSpaceAfter(10);

        $centerPar = new PHPRtfLite_ParFormat(PHPRtfLite_ParFormat::TEXT_ALIGN_CENTER);
        $centerPar->setSpaceAfter(20);

        $leftPar = new PHPRtfLite_ParFormat(PHPRtfLite_ParFormat::TEXT_ALIGN_LEFT);
        $leftPar->setSpaceAfter(20);

        $noPar = new PHPRtfLite_ParFormat();
        $noPar->setSpaceBefore(0);
        $noPar->setSpaceAfter(0);

        // title
        $section = $rtf->addSection();
        $section->writeText(Yii::t('app', 'Project Report'), $h1Font, $titlePar);
        $section->writeText($project->name . ' (' . $project->year . ')', $h2Font, $projectPar);

        // overall summary
        $section->writeText(Yii::t('app', 'Overall Summary'), $h3Font, $h3Par);
        $image = $section->addImage($this->_generateRatingImage($overallRating), $centerPar);

        // detailed summary
        $section->writeText(Yii::t('app', 'Detailed Summary') . '<br>', $h3Font, $noPar);
        $table = $section->addTable(PHPRtfLite_Table::ALIGN_LEFT);

        $table->addRows(count($data) + 1);
        $table->addColumnsList(array( 8, 7, 3 ));
        $table->mergeCellRange(1, 2, 1, 3);
        $table->setFontForCellRange($boldFont, 1, 1, 1, 2);
        $table->setBackgroundForCellRange('#E0E0E0', 1, 1, 1, 2);
        $table->setFontForCellRange($textFont, 2, 1, count($data) + 1, 3);
        $table->setBorderForCellRange($thinBorder, 1, 1, count($data) + 1, 3);
        $table->setFirstRowAsHeader();

        // set paddings
        for ($row = 1; $row <= count($data) + 1; $row++)
            for ($col = 1; $col <= 3; $col++)
            {
                $table->getCell($row, $col)->setCellPaddings(0.2, 0.2, 0.2, 0.2);
                $table->getCell($row, $col)->setVerticalAlignment(PHPRtfLite_Table_Cell::VERTICAL_ALIGN_CENTER);
            }

        $table->writeToCell(1, 1, Yii::t('app', 'Target'));
        $table->writeToCell(1, 2, Yii::t('app', 'Rating'));

        $row = 2;

        foreach ($data as $target)
        {
            $table->writeToCell($row, 1, $target['host']);
            $table->addImageToCell($row, 2, $this->_generateRatingImage($target['rating']));
            $table->writeToCell($row, 3, sprintf('%.2f', $target['rating']));

            $table->getCell($row, 2)->setTextAlignment(PHPRtfLite_Table_Cell::TEXT_ALIGN_CENTER);

            $row++;
        }

        // detailed report
        $section->writeText(Yii::t('app', 'Detailed Report'), $h3Font, $h3Par);

        foreach ($data as $target)
        {
            $section->writeText(Yii::t('app', 'Target') . ': ' . $target['host'] . '<br>', $boldFont, $noPar);

            if (!count($target['categories']))
            {
                $section->writeText(Yii::t('app', 'No finished checks.') . '<br>', $textFont, $noPar);
                continue;
            }

            $table = $section->addTable(PHPRtfLite_Table::ALIGN_LEFT);
            $table->addColumnsList(array( 5, 13 ));

            $row = 1;

            foreach ($target['categories'] as $category)
            {
                $table->addRow();
                $table->mergeCellRange($row, 1, $row, 2);

                $table->getCell($row, 1)->setCellPaddings(0.2, 0.2, 0.2, 0.2);
                $table->getCell($row, 1)->setBorder($thinBorder);
                $table->setFontForCellRange($boldFont, $row, 1, $row, 1);
                $table->setBackgroundForCellRange('#E0E0E0', $row, 1, $row, 1);
                $table->writeToCell($row, 1, $category['name']);

                $row++;

                foreach ($category['checks'] as $check)
                {
                    $table->addRow();
                    $table->mergeCellRange($row, 1, $row, 2);

                    $table->getCell($row, 1)->setCellPaddings(0.2, 0.2, 0.2, 0.2);
                    $table->getCell($row, 1)->setBorder($thinBorder);
                    $table->setFontForCellRange($boldFont, $row, 1, $row, 1);
                    $table->setBackgroundForCellRange('#F0F0F0', $row, 1, $row, 1);
                    $table->writeToCell($row, 1, $check['name']);

                    $row++;

                    if ($check['background'])
                    {
                        $table->addRow();
                        $table->getCell($row, 1)->setCellPaddings(0.2, 0.2, 0.2, 0.2);
                        $table->getCell($row, 1)->setVerticalAlignment(PHPRtfLite_Table_Cell::VERTICAL_ALIGN_TOP);
                        $table->getCell($row, 1)->setBorder($thinBorder);
                        $table->getCell($row, 2)->setCellPaddings(0.2, 0.2, 0.2, 0.2);
                        $table->getCell($row, 2)->setBorder($thinBorder);

                        $table->writeToCell($row, 1, Yii::t('app', 'Background Info'));
                        $table->writeToCell($row, 2, $check['background']);

                        $row++;
                    }

                    if ($check['reference'])
                    {
                        $table->addRow();
                        $table->getCell($row, 1)->setCellPaddings(0.2, 0.2, 0.2, 0.2);
                        $table->getCell($row, 1)->setVerticalAlignment(PHPRtfLite_Table_Cell::VERTICAL_ALIGN_TOP);
                        $table->getCell($row, 1)->setBorder($thinBorder);
                        $table->getCell($row, 2)->setCellPaddings(0.2, 0.2, 0.2, 0.2);
                        $table->getCell($row, 2)->setBorder($thinBorder);

                        $table->writeToCell($row, 1, Yii::t('app', 'Reference'));
                        $table->writeToCell($row, 2, $check['reference']);

                        $row++;
                    }

                    if ($check['question'])
                    {
                        $table->addRow();
                        $table->getCell($row, 1)->setCellPaddings(0.2, 0.2, 0.2, 0.2);
                        $table->getCell($row, 1)->setVerticalAlignment(PHPRtfLite_Table_Cell::VERTICAL_ALIGN_TOP);
                        $table->getCell($row, 1)->setBorder($thinBorder);
                        $table->getCell($row, 2)->setCellPaddings(0.2, 0.2, 0.2, 0.2);
                        $table->getCell($row, 2)->setBorder($thinBorder);

                        $table->writeToCell($row, 1, Yii::t('app', 'Question'));
                        $table->writeToCell($row, 2, $check['question']);

                        $row++;
                    }

                    if ($check['result'])
                    {
                        $table->addRow();
                        $table->getCell($row, 1)->setCellPaddings(0.2, 0.2, 0.2, 0.2);
                        $table->getCell($row, 1)->setVerticalAlignment(PHPRtfLite_Table_Cell::VERTICAL_ALIGN_TOP);
                        $table->getCell($row, 1)->setBorder($thinBorder);
                        $table->getCell($row, 2)->setCellPaddings(0.2, 0.2, 0.2, 0.2);
                        $table->getCell($row, 2)->setBorder($thinBorder);

                        $table->writeToCell($row, 1, Yii::t('app', 'Result'));
                        $table->writeToCell($row, 2, $check['result']);

                        $row++;
                    }

                    if ($check['solutions'])
                    {
                        $table->addRows(count($check['solutions']));

                        $table->mergeCellRange($row, 1, $row + count($check['solutions']) - 1, 1);

                        $table->getCell($row, 1)->setCellPaddings(0.2, 0.2, 0.2, 0.2);
                        $table->getCell($row, 1)->setVerticalAlignment(PHPRtfLite_Table_Cell::VERTICAL_ALIGN_TOP);
                        $table->getCell($row, 1)->setBorder($thinBorder);
                        $table->writeToCell($row, 1, Yii::t('app', 'Solutions'));

                        foreach ($check['solutions'] as $solution)
                        {
                            $table->getCell($row, 1)->setBorder($thinBorder);
                            $table->getCell($row, 2)->setCellPaddings(0.2, 0.2, 0.2, 0.2);
                            $table->getCell($row, 2)->setBorder($thinBorder);
                            $table->writeToCell($row, 2, $solution);

                            $row++;
                        }
                    }

                    if ($check['images'])
                    {
                        $table->addRows(count($check['images']));

                        $table->mergeCellRange($row, 1, $row + count($check['images']) - 1, 1);

                        $table->getCell($row, 1)->setCellPaddings(0.2, 0.2, 0.2, 0.2);
                        $table->getCell($row, 1)->setVerticalAlignment(PHPRtfLite_Table_Cell::VERTICAL_ALIGN_TOP);
                        $table->getCell($row, 1)->setBorder($thinBorder);
                        $table->writeToCell($row, 1, Yii::t('app', 'Attachments'));

                        foreach ($check['images'] as $image)
                        {
                            $table->getCell($row, 1)->setBorder($thinBorder);
                            $table->getCell($row, 2)->setCellPaddings(0.2, 0.2, 0.2, 0.2);
                            $table->getCell($row, 2)->setBorder($thinBorder);

                            list($imageWidth, $imageHeight) = getimagesize($image);

                            $ratio       = $imageWidth / $imageHeight;
                            $imageWidth  = 12; // cm
                            $imageHeight = $imageWidth / $ratio;

                            $table->addImageToCell($row, 2, $image, new PHPRtfLite_ParFormat(), $imageWidth, $imageHeight);

                            $row++;
                        }
                    }

                    $table->addRow();

                    // rating
                    $table->getCell($row, 1)->setCellPaddings(0.2, 0.2, 0.2, 0.2);
                    $table->getCell($row, 1)->setBorder($thinBorder);
                    $table->getCell($row, 2)->setCellPaddings(0.2, 0.2, 0.2, 0.2);
                    $table->getCell($row, 2)->setBorder($thinBorder);

                    $table->writeToCell($row, 1, Yii::t('app', 'Rating'));
                    $table->writeToCell($row, 2, $check['ratingName'] . ' (' . $check['rating'] . ')');

                    $row++;
                }
            }
        }

        $fileName = $project->name . ' (' . $project->year . ') - ' . date('Y-m-d') . '.rtf';
        $hashName = hash('sha256', rand() . time() . $fileName);
        $filePath = Yii::app()->params['tmpPath'] . '/' . $hashName;

        $rtf->save($filePath);

        // give user a file
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . $fileName . '"');
        header('Content-Transfer-Encoding: binary');
        header('Expires: 0');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Pragma: public');
        header('Content-Length: ' . filesize($filePath));

        ob_clean();
        flush();

        readfile($filePath);

        exit();
    }

    /**
     * Show project report form.
     */
    public function actionProject()
    {
        $model = new ProjectReportForm();

        if (isset($_POST['ProjectReportForm']))
        {
            $model->attributes = $_POST['ProjectReportForm'];

            if ($model->validate())
                $this->_generateProjectReport($model->clientId, $model->projectId, $model->targetIds);
            else
                Yii::app()->user->setFlash('error', Yii::t('app', 'Please fix the errors below.'));
        }

        $clients = Client::model()->findAllByAttributes(
            array(),
            array( 'order' => 't.name ASC' )
        );

        $this->breadcrumbs[Yii::t('app', 'Project Report')] = '';

        // display the report generation form
        $this->pageTitle = Yii::t('app', 'Project Report');
		$this->render('project', array(
            'model'   => $model,
            'clients' => $clients
        ));
    }

    /**
     * Generate comparison report.
     */
    private function _generateComparisonReport($clientId, $projectId1, $projectId2)
    {
        $project1 = Project::model()->findByAttributes(array(
            'client_id' => $clientId,
            'id'        => $projectId1
        ));

        if ($project1 === null)
        {
            Yii::app()->user->setFlash('error', Yii::t('app', 'First project doesn\\\'t exist.'));
            return;
        }

        $project2 = Project::model()->findByAttributes(array(
            'client_id' => $clientId,
            'id'        => $projectId2
        ));

        if ($project2 === null)
        {
            Yii::app()->user->setFlash('error', Yii::t('app', 'Second project doesn\\\'t exist.'));
            return;
        }

        $language = Language::model()->findByAttributes(array(
            'code' => Yii::app()->language
        ));

        if ($language)
            $language = $language->id;

        $targets1 = Target::model()->findAllByAttributes(array(
            'project_id' => $project1->id
        ));

        $targets2 = Target::model()->findAllByAttributes(array(
            'project_id' => $project2->id
        ));

        // find corresponding targets
        $data = array();

        foreach ($targets1 as $target1)
            foreach ($targets2 as $target2)
                if ($target2->host == $target1->host)
                {
                    $data[] = array(
                        $target1,
                        $target2
                    );

                    break;
                }

        if (!$data)
            throw new CHttpException(404, Yii::t('app', 'No targets to compare.'));

        $targetsData = array();

        foreach ($data as $targets)
        {
            $targetData = array(
                'host'    => $targets[0]->host,
                'ratings' => array()
            );

            foreach ($targets as $target)
            {
                $rating     = 0;
                $checkCount = 0;

                // get all categories
                $categories = TargetCheckCategory::model()->findAllByAttributes(array(
                    'target_id' => $target->id
                ));

                if (!$categories)
                    continue;

                foreach ($categories as $category)
                {
                    $params = array(
                        'check_category_id' => $category->check_category_id
                    );

                    if (!$category->advanced)
                        $params['advanced'] = 'FALSE';

                    $checks = Check::model()->with(array(
                        'targetChecks' => array(
                            'alias'    => 'tcs',
                            'joinType' => 'INNER JOIN',
                            'on'       => 'tcs.target_id = :target_id AND tcs.status = :status AND tcs.rating != :hidden',
                            'params'   => array(
                                'target_id' => $target->id,
                                'status'    => TargetCheck::STATUS_FINISHED,
                                'hidden'    => TargetCheck::RATING_HIDDEN,
                            ),
                        ),
                    ))->findAllByAttributes(
                        $params
                    );

                    if (!$checks)
                        continue;

                    foreach ($checks as $check)
                    {
                        switch ($check->targetChecks[0]->rating)
                        {
                            case TargetCheck::RATING_INFO:
                                $rating += 1;
                                break;

                            case TargetCheck::RATING_LOW_RISK:
                                $rating += 2;
                                break;

                            case TargetCheck::RATING_MED_RISK:
                                $rating += 3;
                                break;

                            case TargetCheck::RATING_HIGH_RISK:
                                $rating += 4;
                                break;
                        }

                        $checkCount++;
                    }
                }

                if ($checkCount)
                    $rating /= $checkCount;

                $targetData['ratings'][] = $rating;
            }

            $targetsData[] = $targetData;
        }

        // include all PHPRtfLite libraries
        Yii::setPathOfAlias('rtf', Yii::app()->basePath . '/extensions/PHPRtfLite/PHPRtfLite');
        Yii::import('rtf.Autoloader', true);
        PHPRtfLite_Autoloader::setBaseDir(Yii::app()->basePath . '/extensions/PHPRtfLite');
        Yii::registerAutoloader(array( 'PHPRtfLite_Autoloader', 'autoload' ), true);

        $rtf = new PHPRtfLite();
        $rtf->setCharset('UTF-8');
        $rtf->setMargins(1.5, 1, 1.5, 1);

        // borders
        $thinBorder = new PHPRtfLite_Border(
            $rtf,
            new PHPRtfLite_Border_Format(1, '#909090'),
            new PHPRtfLite_Border_Format(1, '#909090'),
            new PHPRtfLite_Border_Format(1, '#909090'),
            new PHPRtfLite_Border_Format(1, '#909090')
        );

        // fonts
        $h1Font = new PHPRtfLite_Font(24, 'Helvetica');
        $h1Font->setBold();

        $h2Font = new PHPRtfLite_Font(20, 'Helvetica');
        $h2Font->setBold();

        $h3Font = new PHPRtfLite_Font(16, 'Helvetica');
        $h3Font->setBold();

        $textFont = new PHPRtfLite_Font(12, 'Helvetica');

        $boldFont = new PHPRtfLite_Font(12, 'Helvetica');
        $boldFont->setBold();

        // paragraphs
        $titlePar = new PHPRtfLite_ParFormat(PHPRtfLite_ParFormat::TEXT_ALIGN_CENTER);
        $titlePar->setSpaceBefore(0);

        $projectPar = new PHPRtfLite_ParFormat(PHPRtfLite_ParFormat::TEXT_ALIGN_CENTER);
        $projectPar->setSpaceAfter(20);

        $noPar = new PHPRtfLite_ParFormat();
        $noPar->setSpaceBefore(0);
        $noPar->setSpaceAfter(0);

        // title
        $section = $rtf->addSection();
        $section->writeText(Yii::t('app', 'Project Comparison'), $h1Font, $titlePar);
        $section->writeText($project1->name . ' (' . $project1->year . ')<br>' . $project2->name . ' (' . $project2->year . ')', $h2Font, $projectPar);

        // detailed summary
        $section->writeText(Yii::t('app', 'Target Comparison') . '<br>', $h3Font, $noPar);
        $table = $section->addTable(PHPRtfLite_Table::ALIGN_LEFT);

        $table->addRows(count($targetsData) + 1);
        $table->addColumnsList(array( 6, 6, 6 ));
        $table->setFontForCellRange($boldFont, 1, 1, 1, 3);
        $table->setBackgroundForCellRange('#E0E0E0', 1, 1, 1, 3);
        $table->setFontForCellRange($textFont, 2, 1, count($targetsData) + 1, 3);
        $table->setBorderForCellRange($thinBorder, 1, 1, count($targetsData) + 1, 3);
        $table->setFirstRowAsHeader();

        // set paddings
        for ($row = 1; $row <= count($targetsData) + 1; $row++)
            for ($col = 1; $col <= 3; $col++)
            {
                $table->getCell($row, $col)->setCellPaddings(0.2, 0.2, 0.2, 0.2);
                $table->getCell($row, $col)->setVerticalAlignment(PHPRtfLite_Table_Cell::VERTICAL_ALIGN_CENTER);
            }

        $table->writeToCell(1, 1, Yii::t('app', 'Target'));
        $table->writeToCell(1, 2, $project1->name . ' (' . $project1->year . ')');
        $table->writeToCell(1, 3, $project2->name . ' (' . $project2->year . ')');

        $row = 2;

        foreach ($targetsData as $target)
        {
            $table->writeToCell($row, 1, $target['host']);
            $table->addImageToCell($row, 2, $this->_generateRatingImage($target['ratings'][0]));
            $table->addImageToCell($row, 3, $this->_generateRatingImage($target['ratings'][1]));

            $table->getCell($row, 2)->setTextAlignment(PHPRtfLite_Table_Cell::TEXT_ALIGN_CENTER);
            $table->getCell($row, 3)->setTextAlignment(PHPRtfLite_Table_Cell::TEXT_ALIGN_CENTER);

            $row++;
        }

        $fileName = Yii::t('app', 'Project Comparison') . ' - ' . date('Y-m-d') . '.rtf';
        $hashName = hash('sha256', rand() . time() . $fileName);
        $filePath = Yii::app()->params['tmpPath'] . '/' . $hashName;

        $rtf->save($filePath);

        // give user a file
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . $fileName . '"');
        header('Content-Transfer-Encoding: binary');
        header('Expires: 0');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Pragma: public');
        header('Content-Length: ' . filesize($filePath));

        ob_clean();
        flush();

        readfile($filePath);

        exit();
    }

    /**
     * Show comparison report form.
     */
    public function actionComparison()
    {
        $model = new ProjectComparisonForm();

        if (isset($_POST['ProjectComparisonForm']))
        {
            $model->attributes = $_POST['ProjectComparisonForm'];

            if ($model->validate())
                $this->_generateComparisonReport($model->clientId, $model->projectId1, $model->projectId2);
            else
                Yii::app()->user->setFlash('error', Yii::t('app', 'Please fix the errors below.'));
        }

        $clients = Client::model()->findAllByAttributes(
            array(),
            array( 'order' => 't.name ASC' )
        );

        $this->breadcrumbs[Yii::t('app', 'Projects Comparison')] = '';

        // display the report generation form
        $this->pageTitle = Yii::t('app', 'Projects Comparison');
		$this->render('comparison', array(
            'model'   => $model,
            'clients' => $clients
        ));
    }

    /**
     * Project list.
     */
    public function actionProjectList()
    {
        $response = new AjaxResponse();

        try
        {
            $model = new EntryControlForm();
            $model->attributes = $_POST['EntryControlForm'];

            if (!$model->validate())
            {
                $errorText = '';

                foreach ($model->getErrors() as $error)
                {
                    $errorText = $error[0];
                    break;
                }

                throw new Exception($errorText);
            }

            $client = Client::model()->findByPk($model->id);

            if (!$client)
                throw new CHttpException(404, Yii::t('app', 'Client not found.'));

            $projects = Project::model()->findAllByAttributes(
                array( 'client_id' => $client->id ),
                array( 'order'     => 't.name ASC, t.year ASC' )
            );

            $projectList = array();

            foreach ($projects as $project)
                $projectList[] = array(
                    'id'   => $project->id,
                    'name' => CHtml::encode($project->name) . ' (' . $project->year . ')',
                );

            $response->addData('projects', $projectList);
        }
        catch (Exception $e)
        {
            $response->setError($e->getMessage());
        }

        echo $response->serialize();
    }

    /**
     * Target list.
     */
    public function actionTargetList()
    {
        $response = new AjaxResponse();

        try
        {
            $model = new EntryControlForm();
            $model->attributes = $_POST['EntryControlForm'];

            if (!$model->validate())
            {
                $errorText = '';

                foreach ($model->getErrors() as $error)
                {
                    $errorText = $error[0];
                    break;
                }

                throw new Exception($errorText);
            }

            $project = Project::model()->findByPk($model->id);

            if (!$project)
                throw new CHttpException(404, Yii::t('app', 'Project not found.'));

            $targets = Target::model()->findAllByAttributes(
                array( 'project_id' => $project->id ),
                array( 'order'      => 't.host ASC' )
            );

            $targetList = array();

            foreach ($targets as $target)
                $targetList[] = array(
                    'id'   => $target->id,
                    'host' => $target->host,
                );

            $response->addData('targets', $targetList);
        }
        catch (Exception $e)
        {
            $response->setError($e->getMessage());
        }

        echo $response->serialize();
    }
}