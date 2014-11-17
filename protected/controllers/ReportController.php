<?php

/**
 * Report controller.
 */
class ReportController extends Controller {
    private $rtf;
    private $cellPadding;
    private $fontSize;
    private $fontFamily;
    private $thinBorder, $thinBorderTL, $thinBorderBR;
    private $h1Font, $h2Font, $h3Font, $textFont, $boldFont, $linkFont, $footerFont, $smallBoldFont;
    private $titlePar, $centerTitlePar, $h3Par, $centerPar, $leftPar, $rightPar, $noPar;
    private $docWidth;
    private $project;
    private $toc;

    const NORMAL_VULN_LIST = 0;
    const APPENDIX_VULN_LIST = 1;
    const SEPARATE_VULN_LIST = 2;

    /**
	 * @return array action filters
	 */
	public function filters() {
		return array(
            'https',
			'checkAuth',
            'showReports',
            "idleOrRunning",
		);
	}

    /**
     * Normalize X coordinate.
     */
    private function _normalizeCoord($coord, $min, $max) {
        if ($coord < $min) {
            $coord = $min;
        } elseif ($coord > $max) {
            $coord = $max;
        }

        return $coord;
    }

    /**
     * Rating image.
     * @param float $rating
     * @param System $system
     */
    private function _generateRatingImage($rating, $system = null) {
        if ($system == null) {
            $system = System::model()->findByPk(1);
        }

        $image = imagecreatefrompng(Yii::app()->basePath . '/../images/rating-stripe.png');
        $lineCoord = round(200 * $rating / $system->report_max_rating);
        $color = imagecolorallocate($image, 0, 0, 0);

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
     * Center text on image.
     */
    private function _centerImageText($image, $text, $font, $color, $size, $x, $y, $width)
    {
        $box = imagettfbbox($size, 0, $font, $text);
        $offset = round(($width - ($box[2] - $box[1])) / 2);
        imagettftext($image, $size, 0, $x + $offset, $y, $color, $font, $text);
    }

    /**
     * Vuln distribution chart.
     */
    private function _generateVulnDistributionChart()
    {
        $image = imagecreatetruecolor(600, 300);
        $lineColor = imagecolorallocate($image, 0, 0, 0);
        $medLineColor = imagecolorallocate($image, 0xD0, 0xD0, 0xD0);
        $white = imagecolorallocate($image, 0xFF, 0xFF, 0xFF);
        $highColor = imagecolorallocate($image, 0xD6, 0x35, 0x15);
        $medColor = imagecolorallocate($image, 0xDA, 0xCE, 0x2F);
        $lowColor = imagecolorallocate($image, 0x53, 0xA2, 0x54);

        imagefilledrectangle($image, 0, 0, 600, 300, $white);

        imageline($image, 30, 10, 30, 270, $lineColor);
        imageline($image, 30, 270, 570, 270, $lineColor);

        for ($i = 220; $i > 10; $i -= 50)
            imageline($image, 30, $i, 570, $i, $medLineColor);

        // get max number of checks
        $max = 0;

        if ($this->project['checksHigh'] > $max)
            $max = $this->project['checksHigh'];

        if ($this->project['checksMed'] > $max)
            $max = $this->project['checksMed'];

        if ($this->project['checksLow'] > $max)
            $max = $this->project['checksLow'];

        // get chart scale
        $topValue = 0;

        if ($max <= 5)
            $topValue = 5;
        else if ($max <= 10)
            $topValue = 10;
        else if ($max <= 25)
            $topValue = 25;
        else if ($max <= 50)
            $topValue = 50;
        else if ($max <= 100)
            $topValue = 100;
        else if ($max <= 500)
            $topValue = 500;
        else
            $topValue = 1000;

        $scaleStep = $topValue / 5;
        $step = 250 / $topValue;

        $font = Yii::app()->params['fonts']['path'] . '/arial.ttf';

        for ($i = 0; $i <= 5; $i++)
        {
            $scale = $i * $scaleStep;
            imagettftext($image, 8, 0, 5, 270 - $i * 50 + 4, $lineColor, $font, $scale);
        }

        imagefilledrectangle($image, 50, 270 - $step * $this->project['checksHigh'], 190, 270, $highColor);
        imagerectangle($image, 50, 270 - $step * $this->project['checksHigh'], 190, 270, $lineColor);

        imagefilledrectangle($image, 230, 270 - $step * $this->project['checksMed'], 370, 270, $medColor);
        imagerectangle($image, 230, 270 - $step * $this->project['checksMed'], 370, 270, $lineColor);

        imagefilledrectangle($image, 410, 270 - $step * $this->project['checksLow'], 550, 270, $lowColor);
        imagerectangle($image, 410, 270 - $step * $this->project['checksLow'], 550, 270, $lineColor);

        $this->_centerImageText($image, Yii::t('app', 'High Risk') . ' (' . $this->project['checksHigh'] . ')', $font, $lineColor, 10, 30, 290, 180);
        $this->_centerImageText($image, Yii::t('app', 'Med Risk') . ' (' . $this->project['checksMed'] . ')', $font, $lineColor, 10, 210, 290, 180);
        $this->_centerImageText($image, Yii::t('app', 'Low Risk') . ' (' . $this->project['checksLow'] . ')', $font, $lineColor, 10, 390, 290, 180);

        $hashName = hash('sha256', rand() . time() . rand());
        $filePath = Yii::app()->params['tmpPath'] . '/' . $hashName . '.png';

        imagepng($image, $filePath, 0);
        imagedestroy($image);

        return $filePath;
    }

    /**
     * Prepare text for project report.
     */
    private function _prepareProjectReportText($text) {
        $text = preg_replace('~>\s*\n\s*<~', '><', $text);
        $text = str_replace(array("<br />", "<br/>"), "<br>", $text);
        $text = str_replace(array("\r", "\n", "\t"), ' ', $text);
        $text = str_replace(array("\\r", "\\n", "\\t"), "", $text);

        $text = strip_tags($text, '<b><i><u><br><ol><ul><li>');
        $text = preg_replace('~<br>\s+~', "<br>", $text);
        $text = preg_replace('~</ul>\s+~', "</ul>", $text);
        $text = preg_replace('~</ol>\s+~', "</ol>", $text);
        $text = preg_replace('~</li>\s+~', "</li>", $text);

        $text = preg_replace('~<ul>[^<]+~', "</ul>", $text);
        $text = preg_replace('~<ol>[^<]+~', "</ul>", $text);
        $text = preg_replace('~</li>[^<]+~', "</li>", $text);
        $text = trim($text);

        return $text;
    }

    /**
     * Sort checks by ratings
     */
    public static function sortChecksByRating($a, $b) {
        if ($a["ratingValue"] == $b["ratingValue"]) {
            return 0;
        }

        return $a["ratingValue"] < $b["ratingValue"] ? 1 : -1;
    }

    /**
     * Render tables
     */
    private function _renderTables($table, &$container, $text, $substitute=true) {
        if (strpos($text, $table) === false) {
            return false;
        }

        $textBlocks = explode($table, $text);
        $guided = $this->project['project']->guided_test;

        for ($i = 0; $i < count($textBlocks); $i++) {
            $this->_renderText($container, $textBlocks[$i], $substitute);

            if ($i >= count($textBlocks) - 1) {
                continue;
            }

            switch ($table) {
                case '{target.list}':
                    $list = new PHPRtfLite_List_Enumeration($this->rtf, PHPRtfLite_List_Enumeration::TYPE_CIRCLE);

                    foreach ($this->project['targets'] as $target) {
                        $list->addItem($guided ? $target["host"] : $target->host, $this->textFont, $this->noPar);
                    }

                    $container->writeRtfCode('\par ');
                    $container->addList($list);
                    $container->writeRtfCode('\par ');

                    break;

                case '{target.stats}':
                    $table = $container->addTable(PHPRtfLite_Table::ALIGN_LEFT);
                    $table->addRows(count($this->project['targets']) + 1);
                    $table->addColumnsList(array( $this->docWidth * 0.4, $this->docWidth * 0.2, $this->docWidth * 0.2, $this->docWidth * 0.2 ));

                    $table->setBackgroundForCellRange('#E0E0E0', 1, 1, 1, 4);
                    $table->setFontForCellRange($this->boldFont, 1, 1, 1, 4);
                    $table->setFontForCellRange($this->textFont, 2, 1, count($this->project['targets']) + 1, 4);
                    $table->setBorderForCellRange($this->thinBorder, 1, 1, count($this->project['targets']) + 1, 4);
                    $table->setFirstRowAsHeader();

                    // set paddings
                    for ($row = 1; $row <= count($this->project['targets']) + 1; $row++) {
                        for ($col = 1; $col <= 4; $col++) {
                            $table->getCell($row, $col)->setCellPaddings($this->cellPadding, $this->cellPadding, $this->cellPadding, $this->cellPadding);
                            $table->getCell($row, $col)->setVerticalAlignment(PHPRtfLite_Table_Cell::VERTICAL_ALIGN_CENTER);
                        }
                    }

                    $row = 1;

                    $table->getCell($row, 1)->writeText(Yii::t('app', 'Target'));
                    $table->getCell($row, 2)->writeText(Yii::t('app', 'Risk Stats'));
                    $table->getCell($row, 3)->writeText(Yii::t('app', 'Completed'));
                    $table->getCell($row, 4)->writeText(Yii::t('app', 'Checks'));

                    $row++;

                    foreach ($this->project['targets'] as $target) {
                        $table->getCell($row, 1)->writeText($guided ? $target["host"] : $target->host, $this->textFont, $this->noPar);

                        if (!$guided && $target->description) {
                            $table->getCell($row, 1)->writeText(' / ', $this->textFont);
                            $table->getCell($row, 1)->writeText($target->description, new PHPRtfLite_Font($this->fontSize, $this->fontFamily, '#909090'));
                        }

                        $table->getCell($row, 2)->writeText(
                            $guided ? $target["highRiskCount"] : $target->highRiskCount,
                            new PHPRtfLite_Font($this->fontSize, $this->fontFamily, '#d63515')
                        );

                        $table->getCell($row, 2)->writeText(' / ', $this->textFont);
                        $table->getCell($row, 2)->writeText(
                            $guided ? $target["medRiskCount"] : $target->medRiskCount,
                            new PHPRtfLite_Font($this->fontSize, $this->fontFamily, '#dace2f')
                        );

                        $table->getCell($row, 2)->writeText(' / ', $this->textFont);
                        $table->getCell($row, 2)->writeText(
                            $guided ? $target["lowRiskCount"] : $target->lowRiskCount,
                            new PHPRtfLite_Font($this->fontSize, $this->fontFamily, '#53a254')
                        );

                        $table->getCell($row, 2)->writeText(' / ', $this->textFont);
                        $table->getCell($row, 2)->writeText($guided ? $target["infoCount"] : $target->infoCount, $this->textFont);

                        $count = $guided ? $target["checkCount"] : $target->checkCount;
                        $finished = $guided ? $target["finishedCount"] : $target->finishedCount;

                        $table->getCell($row, 3)->writeText(
                            ($count ? sprintf('%.2f%%', $finished / $count * 100) : '0.00%') . ' / ' . $finished,
                            $this->textFont
                        );

                        $table->getCell($row, 4)->writeText($guided ? $target["checkCount"] : $target->checkCount, $this->textFont);

                        $row++;
                    }

                    break;

                case '{target.weakest}':
                    $table = $container->addTable(PHPRtfLite_Table::ALIGN_LEFT);
                    $table->addRows(count($this->project['targets']) + 1);
                    $table->addColumnsList(array( $this->docWidth * 0.4, $this->docWidth * 0.4, $this->docWidth * 0.2 ));

                    $table->setBackgroundForCellRange('#E0E0E0', 1, 1, 1, 3);
                    $table->setFontForCellRange($this->boldFont, 1, 1, 1, 3);
                    $table->setFontForCellRange($this->textFont, 2, 1, count($this->project['targets']) + 1, 3);
                    $table->setBorderForCellRange($this->thinBorder, 1, 1, count($this->project['targets']) + 1, 3);
                    $table->mergeCellRange(1, 2, 1, 3);
                    $table->setFirstRowAsHeader();

                    // set paddings
                    for ($row = 1; $row <= count($this->project['targets']) + 1; $row++) {
                        for ($col = 1; $col <= 3; $col++) {
                            $table->getCell($row, $col)->setCellPaddings($this->cellPadding, $this->cellPadding, $this->cellPadding, $this->cellPadding);
                            $table->getCell($row, $col)->setVerticalAlignment(PHPRtfLite_Table_Cell::VERTICAL_ALIGN_CENTER);
                        }
                    }

                    $row = 1;

                    $table->getCell($row, 1)->writeText(Yii::t('app', 'Target'));
                    $table->getCell($row, 2)->writeText(Yii::t('app', 'Weakest Control'));

                    $row++;

                    foreach ($this->project['targets'] as $target) {
                        $table->getCell($row, 1)->writeText($guided ? $target["host"] : $target->host, $this->textFont, $this->noPar);

                        if (!$guided && $target->description) {
                            $table->getCell($row, 1)->writeText(' / ', $this->textFont);
                            $table->getCell($row, 1)->writeText($target->description, new PHPRtfLite_Font($this->fontSize, $this->fontFamily, '#909090'));
                        }

                        $control = $this->project['weakestControls'][$guided ? $target["id"] : $target->id];

                        $table->getCell($row, 2)->writeText($control ? $control['name'] : Yii::t('app', 'N/A'));
                        $table->getCell($row, 3)->writeText($control ? $control['degree'] . '%' : Yii::t('app', 'N/A'));

                        $row++;
                    }

                    break;

                case '{vuln.list}':
                    $table = $container->addTable(PHPRtfLite_Table::ALIGN_LEFT);
                    $rowCount = count($this->project['reducedChecks']) > 5 ? 6 : count($this->project['reducedChecks']) + 1;
                    $table->addRows($rowCount);
                    $table->addColumnsList(array( $this->docWidth * 0.4, $this->docWidth * 0.3, $this->docWidth * 0.3 ));

                    $table->setBackgroundForCellRange('#E0E0E0', 1, 1, 1, 3);
                    $table->setFontForCellRange($this->boldFont, 1, 1, 1, 3);
                    $table->setFontForCellRange($this->textFont, 2, 1, $rowCount, 3);
                    $table->setBorderForCellRange($this->thinBorder, 1, 1, $rowCount, 3);
                    $table->setFirstRowAsHeader();

                    // set paddings
                    for ($row = 1; $row <= $rowCount; $row++) {
                        for ($col = 1; $col <= 3; $col++) {
                            $table->getCell($row, $col)->setCellPaddings($this->cellPadding, $this->cellPadding, $this->cellPadding, $this->cellPadding);
                            $table->getCell($row, $col)->setVerticalAlignment(PHPRtfLite_Table_Cell::VERTICAL_ALIGN_CENTER);
                        }
                    }

                    $row = 1;

                    $table->getCell($row, 1)->writeText(Yii::t('app', 'Target'));
                    $table->getCell($row, 2)->writeText(Yii::t('app', 'Check'));
                    $table->getCell($row, 3)->writeText(Yii::t('app', 'Question'));

                    $row++;

                    $reducedChecks = $this->project['reducedChecks'];
                    usort($reducedChecks, array('ReportController', 'sortChecksByRating'));

                    foreach ($reducedChecks as $check) {
                        $table->getCell($row, 1)->writeText($check['target']['host'], $this->textFont, $this->noPar);

                        if ($check['target']['description']) {
                            $table->getCell($row, 1)->writeText(' / ', $this->textFont);
                            $table->getCell($row, 1)->writeText($check['target']['description'], new PHPRtfLite_Font($this->fontSize, $this->fontFamily, '#909090'));
                        }

                        $table->getCell($row, 2)->writeText($check['name']);
                        $table->getCell($row, 3)->writeText($check['question'] ? $check['question'] : Yii::t('app', 'N/A'));

                        $row++;

                        if ($row > $rowCount) {
                            break;
                        }
                    }

                    break;

                default:
                    break;
            }
        }

        return true;
    }

    /**
     * Substitute scalar variables
     * @param $text
     * @return string text
     */
    private function _substituteScalarVars($text) {
        $text = str_replace("{client}", $this->project["project"]->client->name, $text);
        $text = str_replace("{project}", $this->project["project"]->name, $text);
        $text = str_replace("{year}", $this->project["project"]->year, $text);

        $deadline = implode(".", array_reverse(explode("-", $this->project["project"]->deadline)));
        $text = str_replace("{deadline}", $deadline, $text);

        $admin = Yii::t("app", "N/A");

        if ($this->project["project"]->projectUsers) {
            foreach ($this->project["project"]->projectUsers as $user) {
                if ($user->admin) {
                    $admin = $user->user->name ? $user->user->name : $user->user->email;
                    break;
                }
            }
        }

        $text = str_replace("{admin}", $admin, $text);
        $text = str_replace("{rating}", sprintf("%.2f", $this->project["rating"]), $text);
        $text = str_replace("{targets}", count($this->project["targets"]), $text);
        $text = str_replace("{checks}", $this->project["checks"], $text);
        $text = str_replace("{checks.info}", $this->project["checksInfo"], $text);
        $text = str_replace("{checks.med}", $this->project["checksMed"], $text);
        $text = str_replace("{checks.lo}", $this->project["checksLow"], $text);
        $text = str_replace("{checks.hi}", $this->project["checksHigh"], $text);

        return $text;
    }

    /**
     * Render lists.
     */
    private function _renderLists(&$container, $text, $substitute=true) {
        $listTypes = array("ul", "ol");
        $rendered = false;

        foreach ($listTypes as $listType) {
            $openTag = "<" . $listType . ">";
            $closeTag = "</" . $listType . ">";

            if (strpos($text, $openTag) === false) {
                continue;
            }

            $openTagPosition = strpos($text, $openTag);

            if ($openTagPosition === false) {
                continue;
            }

            $closeTagPosition = strpos($text, $closeTag, $openTagPosition);

            if ($closeTagPosition === false) {
                continue;
            }

            $textBlock = substr($text, 0, $openTagPosition);
            $this->_renderText($container, $textBlock, $substitute);

            $listBlock = substr($text, $openTagPosition + strlen($openTag), $closeTagPosition - $openTagPosition - strlen($openTag));
            $listBlock = trim($listBlock);
            $listElements = explode('<li>', $listBlock);

            $listObject = null;

            if ($listType == 'ol') {
                $listObject = new PHPRtfLite_List_Numbering($this->rtf);
            } elseif ($listType == 'ul') {
                $listObject = new PHPRtfLite_List_Enumeration($this->rtf, PHPRtfLite_List_Enumeration::TYPE_CIRCLE);
            }

            foreach ($listElements as $listElement) {
                $listElement = trim($listElement);

                if (!$listElement) {
                    continue;
                }

                $listElement = str_replace('</li>', '', $listElement);

                if ($substitute) {
                    $listElement = $this->_substituteScalarVars($listElement);
                }

                $listObject->addItem($listElement, $this->textFont, $this->noPar);
            }

            $container->writeRtfCode('\par ');
            $container->addList($listObject);
            $container->writeRtfCode('\par ');

            $textBlock = substr($text, $closeTagPosition + strlen($closeTag));
            $this->_renderText($container, $textBlock, $substitute);

            $rendered = true;
            break;
        }

        return $rendered;
    }

    /**
     * Render variable values
     */
    private function _renderText(&$container, $text, $substitute=true) {
        if ($substitute && $this->_renderTables('{target.list}', $container, $text, $substitute)) {
            return;
        }

        if ($substitute && $this->_renderTables('{target.stats}', $container, $text, $substitute)) {
            return;
        }

        if ($substitute && $this->_renderTables('{target.weakest}', $container, $text, $substitute)) {
            return;
        }

        if ($substitute && $this->_renderTables('{vuln.list}', $container, $text, $substitute)) {
            return;
        }

        if ($this->_renderLists($container, $text, $substitute)) {
            return;
        }

        if ($substitute) {
            $text = $this->_substituteScalarVars($text);
        }

        $container->writeText($this->_prepareProjectReportText($text), $this->textFont, $this->noPar);
    }

    /**
     * Set up RTF variables
     */
    private function _rtfSetup($model=null)
    {
        // include all PHPRtfLite libraries
        Yii::setPathOfAlias('rtf', Yii::app()->basePath . '/extensions/PHPRtfLite/PHPRtfLite');
        Yii::import('rtf.Autoloader', true);
        PHPRtfLite_Autoloader::setBaseDir(Yii::app()->basePath . '/extensions/PHPRtfLite');
        Yii::registerAutoloader(array( 'PHPRtfLite_Autoloader', 'autoload' ), true);

        if ($model) {
            $pageMargin = $model->pageMargin;
            $cellPadding = $model->cellPadding;
            $fontSize = $model->fontSize;
            $fontFamily = $model->fontFamily;
        } else {
            $pageMargin = Yii::app()->params["reports"]["pageMargin"];
            $cellPadding = Yii::app()->params["reports"]["cellPadding"];
            $fontSize = Yii::app()->params["reports"]["fontSize"];
            $fontFamily = Yii::app()->params["reports"]["font"];
        }

        $this->rtf = new PHPRtfLite();
        $this->rtf->setCharset('UTF-8');
        $this->rtf->setMargins($pageMargin, $pageMargin, $pageMargin, $pageMargin);

        $this->cellPadding = $cellPadding;
        $this->fontSize = $fontSize;
        $this->fontFamily = $fontFamily;

        // borders
        $this->thinBorder = new PHPRtfLite_Border(
            $this->rtf,
            new PHPRtfLite_Border_Format(1, '#909090'),
            new PHPRtfLite_Border_Format(1, '#909090'),
            new PHPRtfLite_Border_Format(1, '#909090'),
            new PHPRtfLite_Border_Format(1, '#909090')
        );

        $this->thinBorderTL = new PHPRtfLite_Border(
            $this->rtf,
            new PHPRtfLite_Border_Format(1, '#909090'),
            new PHPRtfLite_Border_Format(1, '#909090'),
            new PHPRtfLite_Border_Format(0, '#909090'),
            new PHPRtfLite_Border_Format(0, '#909090')
        );

        $this->thinBorderBR = new PHPRtfLite_Border(
            $this->rtf,
            new PHPRtfLite_Border_Format(1, '#909090'),
            new PHPRtfLite_Border_Format(0, '#909090'),
            new PHPRtfLite_Border_Format(1, '#909090'),
            new PHPRtfLite_Border_Format(1, '#909090')
        );

        // fonts
        $this->smallBoldFont = new PHPRtfLite_Font(round($this->fontSize * 0.8), $this->fontFamily);
        $this->smallBoldFont->setBold();

        $this->h1Font = new PHPRtfLite_Font($fontSize * 2, $fontFamily);
        $this->h1Font->setBold();

        $this->h2Font = new PHPRtfLite_Font(round($fontSize * 1.7), $fontFamily);
        $this->h2Font->setBold();

        $this->h3Font = new PHPRtfLite_Font(round($fontSize * 1.3), $fontFamily);
        $this->h3Font->setBold();

        $this->textFont = new PHPRtfLite_Font($fontSize, $fontFamily);

        $this->footerFont = new PHPRtfLite_Font(round($fontSize * 0.6), $fontFamily);

        $this->boldFont = new PHPRtfLite_Font($fontSize, $fontFamily);
        $this->boldFont->setBold();

        $this->linkFont = new PHPRtfLite_Font($fontSize, $fontFamily, '#0088CC');
        $this->linkFont->setUnderline();

        // paragraphs
        $this->titlePar = new PHPRtfLite_ParFormat(PHPRtfLite_ParFormat::TEXT_ALIGN_LEFT);
        $this->titlePar->setSpaceBefore(10);
        $this->titlePar->setSpaceAfter(10);

        $this->h3Par = new PHPRtfLite_ParFormat();
        $this->h3Par->setSpaceAfter(10);

        $this->centerTitlePar = new PHPRtfLite_ParFormat(PHPRtfLite_ParFormat::TEXT_ALIGN_CENTER);
        $this->centerTitlePar->setSpaceBefore(10);
        $this->centerTitlePar->setSpaceAfter(10);

        $this->centerPar = new PHPRtfLite_ParFormat(PHPRtfLite_ParFormat::TEXT_ALIGN_CENTER);
        $this->centerPar->setSpaceAfter(0);

        $this->leftPar = new PHPRtfLite_ParFormat(PHPRtfLite_ParFormat::TEXT_ALIGN_LEFT);
        $this->leftPar->setSpaceAfter(20);

        $this->rightPar = new PHPRtfLite_ParFormat(PHPRtfLite_ParFormat::TEXT_ALIGN_RIGHT);
        $this->rightPar->setSpaceAfter(0);

        $this->noPar = new PHPRtfLite_ParFormat();
        $this->noPar->setSpaceBefore(0);
        $this->noPar->setSpaceAfter(0);

        $this->docWidth = 21.0 - 2 * $pageMargin;
    }

    /**
     * Generate a vulnerability list.
     */
    private function _generateVulnerabilityList($data, &$section, $sectionNumber, $type = self::NORMAL_VULN_LIST, $ratingImages, $infoLocation = null, $categoryId = null)
    {
        $targetNumber = 1;

        foreach ($data as $target)
        {
            if (!$target['checkCount'])
                continue;

            if ($type == self::SEPARATE_VULN_LIST &&
                !in_array($categoryId, $target['separate']) ||
                $infoLocation == ProjectReportForm::INFO_LOCATION_APPENDIX && $target['separateCount'] == $target['info']
            )
                continue;

            if ($type == self::APPENDIX_VULN_LIST && !$target['info'])
                continue;

            if (
                $type == self::NORMAL_VULN_LIST &&
                (
                    $infoLocation == ProjectReportForm::INFO_LOCATION_APPENDIX && $target['checkCount'] == $target['info'] + $target['separateCount'] ||
                    $target['checkCount'] == $target['separateCount']
                )
            )
                continue;

            $tableCount = 1;

            if ($type != self::APPENDIX_VULN_LIST && $infoLocation == ProjectReportForm::INFO_LOCATION_TABLE && $target['info'])
                $tableCount = 2;

            for ($tableNumber = 0; $tableNumber < $tableCount; $tableNumber++)
            {
                $subsectionNumber = substr($sectionNumber, strpos($sectionNumber, '.') + 1);

                $this->toc->writeHyperLink(
                    '#vulns_section_' . $subsectionNumber . '_' . $targetNumber,
                    '        ' . $sectionNumber . '.' . $targetNumber . '. ' . $target['host'],
                    $this->textFont
                );

                $section->writeBookmark(
                    'vulns_section_' . $subsectionNumber . '_' . $targetNumber,
                    $sectionNumber . '.' . $targetNumber . '. ' . $target['host'],
                    $this->boldFont
                );

                if ($target['description'])
                {
                    $font = new PHPRtfLite_Font($this->fontSize, $this->fontFamily, '#909090');

                    $this->toc->writeText(' / ', $this->textFont);
                    $this->toc->writeHyperLink(
                        '#vulns_section_' . $subsectionNumber . '_' . $targetNumber,
                        $target['description'],
                        $font
                    );

                    $section->writeText(' / ', $this->textFont);
                    $section->writeText($target['description'], $font);
                }

                if ($tableNumber == 1)
                    $section->writeText(' - ' . Yii::t('app', 'Info Checks'), $this->textFont);

                $section->writeText("\n", $this->textFont);
                $this->toc->writeText("\n", $this->textFont);

                $targetNumber++;

                $table = $section->addTable(PHPRtfLite_Table::ALIGN_LEFT);
                $table->addColumnsList(array( $this->docWidth * 0.17, $this->docWidth * 0.83 ));

                $row = 1;

                foreach ($target['categories'] as $category)
                {
                    if (
                        $type == self::SEPARATE_VULN_LIST &&
                        (
                            $category['id'] != $categoryId ||
                            $infoLocation == ProjectReportForm::INFO_LOCATION_APPENDIX && $category['info'] == $category['separate'] ||
                            $infoLocation == ProjectReportForm::INFO_LOCATION_TABLE &&
                            (
                                $tableNumber == 0 && $category['separate'] == $category['info'] ||
                                $tableNumber == 1 && !$category['info']
                            )
                        )
                    )
                        continue;

                    if ($type == self::APPENDIX_VULN_LIST && !$category['info'])
                        continue;

                    if (
                        $type == self::NORMAL_VULN_LIST &&
                        (
                            $infoLocation == ProjectReportForm::INFO_LOCATION_APPENDIX && $category['checkCount'] == $category['info'] + $category['separate'] ||
                            $category['checkCount'] == $category['separate'] ||
                            $infoLocation == ProjectReportForm::INFO_LOCATION_TABLE &&
                            (
                                $tableNumber == 0 && $category['checkCount'] == $category['info'] + $category['separate'] ||
                                $tableNumber == 1 && !$category['info']
                            )
                        )
                    )
                        continue;

                    $table->addRow();
                    $table->mergeCellRange($row, 1, $row, 2);

                    $table->getCell($row, 1)->setCellPaddings($this->cellPadding, $this->cellPadding, $this->cellPadding, $this->cellPadding);
                    $table->getCell($row, 1)->setBorder($this->thinBorder);
                    $table->setFontForCellRange($this->boldFont, $row, 1, $row, 1);
                    $table->setBackgroundForCellRange('#B0B0B0', $row, 1, $row, 1);
                    $table->writeToCell($row, 1, $category['name']);

                    $row++;

                    foreach ($category['controls'] as $control)
                    {
                        if (
                            $type == self::SEPARATE_VULN_LIST &&
                            (
                                !$control['separate'] ||
                                $infoLocation == ProjectReportForm::INFO_LOCATION_APPENDIX && $control['info'] == $control['separate'] ||
                                $infoLocation == ProjectReportForm::INFO_LOCATION_TABLE &&
                                (
                                    $tableNumber == 0 && $control['separate'] == $control['info'] ||
                                    $tableNumber == 1 && !$control['info']
                                )
                            )
                        )
                            continue;

                        if ($type == self::APPENDIX_VULN_LIST && !$control['info'])
                            continue;

                        if (
                            $type == self::NORMAL_VULN_LIST &&
                            (
                                $infoLocation == ProjectReportForm::INFO_LOCATION_APPENDIX && $control['checkCount'] == $control['info'] + $control['separate'] ||
                                $control['checkCount'] == $control['separate'] ||
                                $infoLocation == ProjectReportForm::INFO_LOCATION_TABLE &&
                                (
                                    $tableNumber == 0 && $control['checkCount'] == $control['info'] + $control['separate'] ||
                                    $tableNumber == 1 && !$control['info']
                                )
                            )
                        )
                            continue;

                        $table->addRow();
                        $table->mergeCellRange($row, 1, $row, 2);

                        $table->getCell($row, 1)->setCellPaddings($this->cellPadding, $this->cellPadding, $this->cellPadding, $this->cellPadding);
                        $table->getCell($row, 1)->setBorder($this->thinBorder);
                        $table->setFontForCellRange($this->boldFont, $row, 1, $row, 1);
                        $table->setBackgroundForCellRange('#D0D0D0', $row, 1, $row, 1);
                        $table->writeToCell($row, 1, $control['name']);

                        $row++;

                        foreach ($control['checks'] as $check)
                        {
                            if (
                                $type == self::SEPARATE_VULN_LIST &&
                                (
                                    !$check['separate'] ||
                                    $infoLocation == ProjectReportForm::INFO_LOCATION_APPENDIX && $check['info'] ||
                                    $infoLocation == ProjectReportForm::INFO_LOCATION_TABLE &&
                                    (
                                        $tableNumber == 0 && $check['info'] ||
                                        $tableNumber == 1 && !$check['info']
                                    )
                                )
                            )
                                continue;

                            if ($type == self::APPENDIX_VULN_LIST && !$check['info'])
                                continue;

                            if (
                                $type == self::NORMAL_VULN_LIST &&
                                (
                                    $infoLocation == ProjectReportForm::INFO_LOCATION_APPENDIX && $check['info'] ||
                                    $check['separate'] ||
                                    $infoLocation == ProjectReportForm::INFO_LOCATION_TABLE &&
                                    (
                                        $tableNumber == 0 && $check['info'] ||
                                        $tableNumber == 1 && !$check['info']
                                    )
                                )
                            )
                                continue;

                            $table->addRow();
                            $table->mergeCellRange($row, 1, $row, 2);

                            $table->getCell($row, 1)->setCellPaddings($this->cellPadding, $this->cellPadding, $this->cellPadding, $this->cellPadding);
                            $table->getCell($row, 1)->setBorder($this->thinBorder);
                            $table->setFontForCellRange($this->boldFont, $row, 1, $row, 1);
                            $table->setBackgroundForCellRange('#F0F0F0', $row, 1, $row, 1);

                            $table->getCell($row, 1)->writeBookmark(
                                'check_' . $target['id'] . '_' . $check['id'],
                                $check['name']
                            );

                            $row++;

                            // reference info
                            $table->addRow();
                            $table->getCell($row, 1)->setCellPaddings($this->cellPadding, $this->cellPadding, $this->cellPadding, $this->cellPadding);
                            $table->getCell($row, 1)->setVerticalAlignment(PHPRtfLite_Table_Cell::VERTICAL_ALIGN_TOP);
                            $table->getCell($row, 1)->setBorder($this->thinBorder);
                            $table->getCell($row, 2)->setCellPaddings($this->cellPadding, $this->cellPadding, $this->cellPadding, $this->cellPadding);
                            $table->getCell($row, 2)->setBorder($this->thinBorder);

                            $table->writeToCell($row, 1, Yii::t('app', 'Reference'));

                            $reference    = $check['reference'] . ( $check['referenceCode'] ? '-' . $check['referenceCode'] : '' );
                            $referenceUrl = '';

                            if ($check['referenceCode'] && $check['referenceCodeUrl'])
                                $referenceUrl = $check['referenceCodeUrl'];
                            else if ($check['referenceUrl'])
                                $referenceUrl = $check['referenceUrl'];

                            if ($referenceUrl)
                                $table->getCell($row, 2)->writeHyperLink($referenceUrl, $reference, $this->linkFont);
                            else
                                $table->writeToCell($row, 2, $reference);

                            $row++;

                            if ($check['background']) {
                                $table->addRow();
                                $table->getCell($row, 1)->setCellPaddings($this->cellPadding, $this->cellPadding, $this->cellPadding, $this->cellPadding);
                                $table->getCell($row, 1)->setVerticalAlignment(PHPRtfLite_Table_Cell::VERTICAL_ALIGN_TOP);
                                $table->getCell($row, 1)->setBorder($this->thinBorder);
                                $table->getCell($row, 2)->setCellPaddings($this->cellPadding, $this->cellPadding, $this->cellPadding, $this->cellPadding);
                                $table->getCell($row, 2)->setBorder($this->thinBorder);

                                $table->writeToCell($row, 1, Yii::t('app', 'Background Info'));

                                $cell = $table->getCell($row, 2);
                                $this->_renderText($cell, $check['background'], false);

                                $row++;
                            }

                            if ($check['question']) {
                                $table->addRow();
                                $table->getCell($row, 1)->setCellPaddings($this->cellPadding, $this->cellPadding, $this->cellPadding, $this->cellPadding);
                                $table->getCell($row, 1)->setVerticalAlignment(PHPRtfLite_Table_Cell::VERTICAL_ALIGN_TOP);
                                $table->getCell($row, 1)->setBorder($this->thinBorder);
                                $table->getCell($row, 2)->setCellPaddings($this->cellPadding, $this->cellPadding, $this->cellPadding, $this->cellPadding);
                                $table->getCell($row, 2)->setBorder($this->thinBorder);

                                $table->writeToCell($row, 1, Yii::t('app', 'Question'));

                                $cell = $table->getCell($row, 2);
                                $this->_renderText($cell, $check['question'], false);

                                $row++;
                            }

                            if ($check['result']) {
                                $cutPos = mb_strpos($check["result"], "@cut", 0, "UTF-8");

                                if ($cutPos !== false) {
                                    $check["result"] = str_replace("@cut", "---", $check["result"]);
                                }

                                $table->addRow();
                                $table->getCell($row, 1)->setCellPaddings($this->cellPadding, $this->cellPadding, $this->cellPadding, $this->cellPadding);
                                $table->getCell($row, 1)->setVerticalAlignment(PHPRtfLite_Table_Cell::VERTICAL_ALIGN_TOP);
                                $table->getCell($row, 1)->setBorder($this->thinBorder);
                                $table->getCell($row, 2)->setCellPaddings($this->cellPadding, $this->cellPadding, $this->cellPadding, $this->cellPadding);
                                $table->getCell($row, 2)->setBorder($this->thinBorder);
                                $table->writeToCell($row, 1, Yii::t('app', 'Result'));

                                if (Utils::isHtml($check["result"])) {
                                    $this->_renderText($table->getCell($row, 2), $check["result"], false);
                                } else {
                                    $table->writeToCell($row, 2, $check["result"]);
                                }

                                $row++;
                            }

                            if ($check['tableResult']) {
                                $table->addRow();
                                $table->getCell($row, 1)->setCellPaddings($this->cellPadding, $this->cellPadding, $this->cellPadding, $this->cellPadding);
                                $table->getCell($row, 1)->setVerticalAlignment(PHPRtfLite_Table_Cell::VERTICAL_ALIGN_TOP);
                                $table->getCell($row, 1)->setBorder($this->thinBorder);
                                $table->getCell($row, 2)->setCellPaddings($this->cellPadding, $this->cellPadding, $this->cellPadding, $this->cellPadding);
                                $table->getCell($row, 2)->setBorder($this->thinBorderBR);

                                if ($check['result']) {
                                    $table->mergeCellRange($row - 1, 1, $row, 1);
                                } else {
                                    $table->writeToCell($row, 1, Yii::t('app', 'Result'));
                                }

                                $tableResult = new ResultTable();
                                $tableResult->parse($check['tableResult']);

                                foreach ($tableResult->getTables() as $tResult) {
                                    $nestedTable = $table->getCell($row, 2)->addTable();
                                    $nestedTable->addRows($tResult["rowCount"] + 1);

                                    $columnWidths = array();
                                    $tableWidth = $this->docWidth * 0.83 - $this->cellPadding * 2;

                                    foreach ($tResult["columns"] as $column) {
                                        $columnWidths[] = (float)$column['width'] * $tableWidth;
                                    }

                                    $nestedTable->addColumnsList($columnWidths);

                                    $nestedTable->setFontForCellRange($this->boldFont, 1, 1, 1, $tResult["columnCount"]);
                                    $nestedTable->setBackgroundForCellRange('#E0E0E0', 1, 1, 1, $tResult["columnCount"]);
                                    $nestedTable->setFontForCellRange($this->textFont, 2, 1, $tResult["rowCount"] + 1, $tResult["columnCount"]);
                                    $nestedTable->setBorderForCellRange($this->thinBorder, 1, 1, $tResult["rowCount"] + 1, $tResult["columnCount"]);
                                    $nestedTable->setFirstRowAsHeader();

                                    $nestedRow = 1;
                                    $nestedColumn = 1;

                                    foreach ($tResult["columns"] as $column) {
                                        $nestedTable->getCell($nestedRow, $nestedColumn)->setCellPaddings(
                                            $this->cellPadding,
                                            $this->cellPadding,
                                            $this->cellPadding,
                                            $this->cellPadding
                                        );

                                        $nestedTable->getCell($nestedRow, $nestedColumn)->setVerticalAlignment(
                                            PHPRtfLite_Table_Cell::VERTICAL_ALIGN_TOP
                                        );

                                        $nestedTable->writeToCell($nestedRow, $nestedColumn, $column['name']);
                                        $nestedColumn++;
                                    }

                                    foreach ($tResult["data"] as $dataRow) {
                                        $nestedRow++;
                                        $nestedColumn = 1;

                                        foreach ($dataRow as $dataCell) {
                                            $nestedTable->getCell($nestedRow, $nestedColumn)->setCellPaddings(
                                                $this->cellPadding,
                                                $this->cellPadding,
                                                $this->cellPadding,
                                                $this->cellPadding
                                            );

                                            $nestedTable->getCell($nestedRow, $nestedColumn)->setVerticalAlignment(
                                                PHPRtfLite_Table_Cell::VERTICAL_ALIGN_TOP
                                            );

                                            $nestedTable->writeToCell($nestedRow, $nestedColumn, $dataCell);
                                            $nestedColumn++;
                                        }
                                    }

                                    $table->writeToCell($row, 2, "\n");
                                }

                                $row++;
                            }

                            if (isset($check["poc"]) && $check["poc"] && $this->_system->checklist_poc) {
                                $table->addRow();
                                $table->getCell($row, 1)->setCellPaddings($this->cellPadding, $this->cellPadding, $this->cellPadding, $this->cellPadding);
                                $table->getCell($row, 1)->setVerticalAlignment(PHPRtfLite_Table_Cell::VERTICAL_ALIGN_TOP);
                                $table->getCell($row, 1)->setBorder($this->thinBorder);
                                $table->getCell($row, 2)->setCellPaddings($this->cellPadding, $this->cellPadding, $this->cellPadding, $this->cellPadding);
                                $table->getCell($row, 2)->setBorder($this->thinBorder);

                                $table->writeToCell($row, 1, Yii::t("app", "PoC"));
                                $table->writeToCell($row, 2, $check["poc"]);

                                $row++;
                            }
                            
                            if (isset($check["links"]) && $check["links"] && $this->_system->checklist_links) {
                                $table->addRow();
                                $table->getCell($row, 1)->setCellPaddings($this->cellPadding, $this->cellPadding, $this->cellPadding, $this->cellPadding);
                                $table->getCell($row, 1)->setVerticalAlignment(PHPRtfLite_Table_Cell::VERTICAL_ALIGN_TOP);
                                $table->getCell($row, 1)->setBorder($this->thinBorder);
                                $table->getCell($row, 2)->setCellPaddings($this->cellPadding, $this->cellPadding, $this->cellPadding, $this->cellPadding);
                                $table->getCell($row, 2)->setBorder($this->thinBorder);

                                $table->writeToCell($row, 1, Yii::t("app", "Links"));
                                $table->writeToCell($row, 2, $check["links"]);

                                $row++;
                            }

                            if ($check['solutions']) {
                                $table->addRows(count($check['solutions']));

                                $table->mergeCellRange($row, 1, $row + count($check['solutions']) - 1, 1);

                                $table->getCell($row, 1)->setCellPaddings($this->cellPadding, $this->cellPadding, $this->cellPadding, $this->cellPadding);
                                $table->getCell($row, 1)->setVerticalAlignment(PHPRtfLite_Table_Cell::VERTICAL_ALIGN_TOP);
                                $table->getCell($row, 1)->setBorder($this->thinBorder);
                                $table->writeToCell($row, 1, Yii::t('app', 'Solutions'));

                                foreach ($check['solutions'] as $solution)
                                {
                                    $table->getCell($row, 1)->setBorder($this->thinBorder);
                                    $table->getCell($row, 2)->setCellPaddings($this->cellPadding, $this->cellPadding, $this->cellPadding, $this->cellPadding);
                                    $table->getCell($row, 2)->setBorder($this->thinBorder);

                                    $cell = $table->getCell($row, 2);
                                    $this->_renderText($cell, $solution, false);

                                    $row++;
                                }
                            }

                            if ($check['images']) {
                                $table->addRows(count($check['images']));

                                $table->mergeCellRange($row, 1, $row + count($check['images']) - 1, 1);

                                $table->getCell($row, 1)->setCellPaddings($this->cellPadding, $this->cellPadding, $this->cellPadding, $this->cellPadding);
                                $table->getCell($row, 1)->setVerticalAlignment(PHPRtfLite_Table_Cell::VERTICAL_ALIGN_TOP);
                                $table->getCell($row, 1)->setBorder($this->thinBorder);
                                $table->writeToCell($row, 1, Yii::t('app', 'Attachments'));

                                foreach ($check['images'] as $image) {
                                    $table->getCell($row, 1)->setBorder($this->thinBorder);
                                    $table->getCell($row, 2)->setCellPaddings($this->cellPadding, $this->cellPadding, $this->cellPadding, $this->cellPadding);
                                    $table->getCell($row, 2)->setBorder($this->thinBorder);

                                    $table->writeToCell($row, 2, $image['title']);
                                    $table->addImageToCell($row, 2, $image['image'], new PHPRtfLite_ParFormat(), $this->docWidth * 0.78);

                                    $row++;
                                }
                            }

                            $table->addRow();

                            // rating
                            $table->getCell($row, 1)->setCellPaddings($this->cellPadding, $this->cellPadding, $this->cellPadding, $this->cellPadding);
                            $table->getCell($row, 1)->setBorder($this->thinBorder);
                            $table->getCell($row, 2)->setCellPaddings($this->cellPadding, $this->cellPadding, $this->cellPadding, $this->cellPadding);
                            $table->getCell($row, 2)->setBorder($this->thinBorder);

                            $table->writeToCell($row, 1, Yii::t("app", "Rating"));
                            $image = null;

                            switch ($check["rating"]) {
                                case TargetCheck::RATING_HIGH_RISK:
                                    $image = $ratingImages[TargetCheck::RATING_HIGH_RISK];
                                    break;

                                case TargetCheck::RATING_MED_RISK:
                                    $image = $ratingImages[TargetCheck::RATING_MED_RISK];
                                    break;

                                case TargetCheck::RATING_LOW_RISK:
                                    $image = $ratingImages[TargetCheck::RATING_LOW_RISK];
                                    break;

                                case TargetCheck::RATING_NONE:
                                    $image = $ratingImages[TargetCheck::RATING_NONE];
                                    break;

                                case TargetCheck::RATING_NO_VULNERABILITY:
                                    $image = $ratingImages[TargetCheck::RATING_NO_VULNERABILITY];
                                    break;

                                case TargetCheck::RATING_INFO:
                                    $image = $ratingImages[TargetCheck::RATING_INFO];
                                    break;
                            }

                            $table->addImageToCell($row, 2, $image);
                            $table->writeToCell($row, 2, " " . $check["ratingName"]);

                            $row++;
                        }
                    }
                }
            }
        }
    }

    /**
     * Get rating for the given check distribution
     * @param $totalChecks
     * @param $lowRisk
     * @param $medRisk
     * @param $highRisk
     * @return float rating
     */
    private function _getRating($totalChecks, $lowRisk, $medRisk, $highRisk) {
        $medDamping = 1;
        $lowDamping = 1;
        $pedestal = 0;
        $range = 0;

        if ($totalChecks == 0) {
            return 0.0;
        }

        /** @var System $system */
        $system = System::model()->findByPk(1);

        if ($highRisk > 0) {
            $pedestal = $system->report_high_pedestal;
            $medDamping = $system->report_high_damping_med;
            $lowDamping = $system->report_high_damping_low;
            $range = $system->report_max_rating - $system->report_high_pedestal;
        } elseif ($medRisk > 0) {
            $pedestal = $system->report_med_pedestal;
            $lowDamping = $system->report_med_damping_low;
            $range = $system->report_high_pedestal - $system->report_med_pedestal;
        } elseif ($lowRisk > 0) {
            $pedestal = $system->report_low_pedestal;
            $range = $system->report_med_pedestal - $system->report_low_pedestal;
        }

        $highRisk = $highRisk / $totalChecks * $range;
        $medRisk = $medRisk / $totalChecks * ($range - $highRisk) * $medDamping;
        $lowRisk = $lowRisk / $totalChecks * ($range - $highRisk - $medRisk) * $lowDamping;
        $rating = $highRisk + $medRisk + $lowRisk + $pedestal;

        return $rating;
    }

    /**
     * Get rating for checks array
     * @param array $checks
     * @return float rating
     */
    private function _getChecksRating($checks) {
        $totalChecks = 0;
        $lowRisk = 0;
        $medRisk = 0;
        $highRisk = 0;

        foreach ($checks as $check) {
            $totalChecks++;

            switch ($check["rating"]) {
                case TargetCheck::RATING_NONE:
                case TargetCheck::RATING_HIDDEN:
                case TargetCheck::RATING_INFO:
                    break;

                case TargetCheck::RATING_LOW_RISK:
                    $lowRisk++;
                    break;

                case TargetCheck::RATING_MED_RISK:
                    $medRisk++;
                    break;

                case TargetChecK::RATING_HIGH_RISK:
                    $highRisk++;
                    break;
            }
        }

        return $this->_getRating($totalChecks, $lowRisk, $medRisk, $highRisk);
    }

    /**
     * Get category rating
     * @param $category
     * @return float rating
     */
    private function _getCategoryRating($category) {
        $checks = array();

        foreach ($category["controls"] as $control) {
            foreach ($control["checks"] as $check) {
                $checks[] = $check;
            }
        }

        return $this->_getChecksRating($checks);
    }

    /**
     * Get target rating
     * @param $target
     * @return float rating
     */
    private function _getTargetRating($target) {
        $checks = array();

        foreach ($target["categories"] as $category) {
            foreach ($category["controls"] as $control) {
                foreach ($control["checks"] as $check) {
                    $checks[] = $check;
                }
            }
        }

        return $this->_getChecksRating($checks);
    }

    /**
     * Get total rating
     * @param $targets
     * @return float rating
     */
    private function _getTotalRating($targets) {
        $checks = array();

        foreach ($targets as $target) {
            foreach ($target["categories"] as $category) {
                foreach ($category["controls"] as $control) {
                    foreach ($control["checks"] as $check) {
                        $checks[] = $check;
                    }
                }
            }
        }

        return $this->_getChecksRating($checks);
    }

    /**
     * Project report
     * @param $targetIds
     * @param $templateCategoryIds
     * @param $project
     * @param $language
     * @return array
     */
    private function _projectReport($targetIds, $templateCategoryIds, $project, $language) {
        $criteria = new CDbCriteria();
        $criteria->addInCondition('id', $targetIds);
        $criteria->addColumnCondition(array('project_id' => $project->id));
        $criteria->order = 't.host ASC';
        $targets = Target::model()->with(array(
            'checkCount',
            'finishedCount',
            'infoCount',
            'lowRiskCount',
            'medRiskCount',
            'highRiskCount',
        ))->findAll($criteria);

        $this->project['targets'] = $targets;
        $this->_generateFulfillmentDegreeReport(null, true);

        $data = array();
        $hasInfo = true;
        $hasSeparate = false;

        $totalRating = 0.0;
        $totalCheckCount = 0;
        $checksHigh = 0;
        $checksMed = 0;
        $checksLow = 0;
        $checksInfo = 0;
        $reportAttachments = array();

        $reducedChecks = array();
        $ratings = TargetCheck::getRatingNames();

        foreach ($targets as $target) {
            $targetData = array(
                "id" => $target->id,
                "host" => $target->host,
                "description" => $target->description,
                "rating" => 0.0,
                "checkCount" => 0,
                "categories" => array(),
                "info" => 0,
                "separate" => array(),
                "separateCount" => 0,
            );

            // get all references (they are the same across all target categories)
            $referenceIds = array();

            $references = TargetReference::model()->findAllByAttributes(array(
                'target_id' => $target->id
            ));

            foreach ($references as $reference) {
                $referenceIds[] = $reference->reference_id;
            }

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
            ))->findAllByAttributes(
                array('target_id' => $target->id ),
                array('order' => 'COALESCE(l10n.name, category.name) ASC')
            );

            foreach ($categories as $category) {
                $categoryData = array(
                    "id"  => $category->check_category_id,
                    "name" => $category->category->localizedName,
                    "rating" => 0.0,
                    "checkCount" => 0,
                    "controls" => array(),
                    "info" => 0,
                    "separate" => 0,
                );

                // get all controls
                $controls = CheckControl::model()->with(array(
                    "customChecks" => array(
                        "alias" => "custom",
                        "on" => "custom.target_id = :target_id",
                        "params" => array("target_id" => $target->id),
                        "with" => "attachments",
                    ),
                    "l10n" => array(
                        "joinType" => "LEFT JOIN",
                        "on" => "language_id = :language_id",
                        "params" => array("language_id" => $language)
                    )
                ))->findAllByAttributes(
                    array("check_category_id" => $category->check_category_id),
                    array("order" => "t.sort_order ASC")
                );

                if (!$controls)
                    continue;

                foreach ($controls as $control) {
                    $controlData = array(
                        "id" => $control->id,
                        "name" => $control->localizedName,
                        "rating" => 0.0,
                        "checkCount" => 0,
                        "checks" => array(),
                        "info" => 0,
                        "separate" => 0,
                    );

                    foreach ($control->customChecks as $check) {
                        $checkData = array(
                            "id" => $check->target_id . "-" . $check->check_control_id,
                            "custom" => true,
                            "name" => $check->name,
                            "background" => $this->_prepareProjectReportText($check->background_info),
                            "question" => $this->_prepareProjectReportText($check->question),
                            "result" => $check->result,
                            "poc" => $check->poc,
                            "links" => $check->links,
                            "tableResult" => null,
                            "rating" => $check->rating,
                            "ratingName" => $ratings[$check->rating],
                            "ratingColor" => "#999999",
                            "solutions" => array(),
                            "images" => array(),
                            "reference" => "CUSTOM",
                            "referenceUrl" => null,
                            "referenceCode" => "CHECK-" . $check->reference,
                            "referenceCodeUrl" => null,
                            "info" => $check->rating == TargetCheck::RATING_INFO,
                            "separate" => in_array($category->check_category_id, $templateCategoryIds),
                        );

                        if ($check->solution) {
                            $checkData["solutions"][] = $this->_prepareProjectReportText($check->solution);
                        }

                        if ($checkData["info"]) {
                            $controlData["info"]++;
                            $categoryData["info"]++;
                            $targetData["info"]++;
                            $hasInfo = true;
                        }

                        if ($checkData["separate"]) {
                            $controlData["separate"]++;
                            $categoryData["separate"]++;

                            if (!in_array($category->check_category_id, $targetData["separate"])) {
                                $targetData["separate"][] = $category->check_category_id;
                            }

                            $targetData["separateCount"]++;

                            $hasSeparate = true;
                        }

                        switch ($check->rating) {
                            case TargetCustomCheck::RATING_INFO:
                                $checkData["ratingColor"] = "#3A87AD";
                                $checkData["ratingValue"] = 0;
                                $checksInfo++;
                                break;

                            case TargetCustomCheck::RATING_LOW_RISK:
                                $checkData["ratingColor"] = "#53A254";
                                $checkData["ratingValue"] = 1;
                                $checksLow++;
                                break;

                            case TargetCustomCheck::RATING_MED_RISK:
                                $checkData["ratingColor"] = "#DACE2F";
                                $checkData["ratingValue"] = 2;
                                $checksMed++;
                                break;

                            case TargetCustomCheck::RATING_HIGH_RISK:
                                $checkData["ratingColor"] = "#D63515";
                                $checkData["ratingValue"] = 3;
                                $checksHigh++;
                                break;
                        }

                        if ($check->attachments) {
                            foreach ($check->attachments as $attachment) {
                                if (in_array($attachment->type, array("image/jpeg", "image/png", "image/gif", "image/pjpeg"))) {
                                    $checkData["images"][] = array(
                                        'title' => $attachment->title,
                                        'image' => Yii::app()->params["attachments"]["path"] . "/" . $attachment->path
                                    );
                                } else {
                                    $reportAttachments[] = array(
                                        "host" => $target->host,
                                        "title" => $attachment->title,
                                        "check" => $check->name,
                                        "filename" => $attachment->name,
                                        "path" => Yii::app()->params["attachments"]["path"] . "/" . $attachment->path,
                                    );
                                }
                            }
                        }

                        if (in_array($check->rating, array(TargetCustomCheck::RATING_HIGH_RISK, TargetCustomCheck::RATING_MED_RISK, TargetCustomCheck::RATING_LOW_RISK))) {
                            $reducedChecks[] = array(
                                "target" => array(
                                    "id" => $target->id,
                                    "host" => $target->host,
                                    "description" => $target->description
                                ),
                                "id" => $checkData["id"],
                                "name" => $checkData["name"],
                                "question" => $checkData["question"],
                                "solution" => $checkData["solutions"] ? implode("\n", $checkData["solutions"]) : "",
                                "rating" => $checkData["rating"],
                                "result" => $checkData["result"],
                                "poc" => $checkData["poc"],
                                "links" => $checkData["links"],
                                "ratingValue" => $checkData["ratingValue"],
                                "custom" => true,
                            );
                        }

                        $controlData["checks"][] = $checkData;
                    }

                    $criteria = new CDbCriteria();
                    $criteria->order = 't.sort_order ASC, tcs.id ASC';
                    $criteria->addInCondition('t.reference_id', $referenceIds);
                    $criteria->addColumnCondition(array(
                        't.check_control_id' => $control->id
                    ));

                    if ($this->_system->demo) {
                        $criteria->addColumnCondition(array(
                            "t.demo" => true
                        ));
                    }

                    $criteria->together = true;

                    if (!$category->advanced) {
                        $criteria->addCondition('t.advanced = FALSE');
                    }

                    $checks = Check::model()->with(array(
                        "l10n" => array(
                            "joinType" => "LEFT JOIN",
                            "on" => "l10n.language_id = :language_id",
                            "params" => array("language_id" => $language)
                        ),
                        "targetChecks" => array(
                            "alias" => "tcs",
                            "joinType" => "INNER JOIN",
                            "on" => "tcs.target_id = :target_id AND tcs.status = :status AND tcs.rating != :hidden",
                            "params" => array(
                                "target_id" => $target->id,
                                "status" => TargetCheck::STATUS_FINISHED,
                                "hidden" => TargetCheck::RATING_HIDDEN,
                            ),
                            "with" => array(
                                "solutions" => array(
                                    "alias" => "tss",
                                    "joinType" => "LEFT JOIN",
                                    "with" => array(
                                        "solution" => array(
                                            "alias" => "tss_s",
                                            "joinType" => "LEFT JOIN",
                                            "with" => array(
                                                "l10n" => array(
                                                    "alias" => "tss_s_l10n",
                                                    "on" => "tss_s_l10n.language_id = :language_id",
                                                    "params" => array("language_id" => $language)
                                                )
                                            )
                                        )
                                    )
                                ),
                                "attachments",
                            )
                        ),
                        "_reference"
                    ))->findAll($criteria);

                    foreach ($checks as $check) {
                        $ctr = 0;

                        foreach ($check->targetChecks as $tc) {
                            $checkData = array(
                                "id" => $check->id,
                                "custom" => false,
                                "name" => $check->localizedName . ($ctr > 0 ? " " . ($ctr + 1) : ""),
                                "background" => $this->_prepareProjectReportText($check->localizedBackgroundInfo),
                                "question" => $this->_prepareProjectReportText($check->localizedQuestion),
                                "result" => $tc->result,
                                "poc" => $tc->poc,
                                "links" => $tc->links,
                                "tableResult" => $tc->table_result,
                                "rating" => 0,
                                "ratingName" => $ratings[$tc->rating],
                                "ratingColor" => "#999999",
                                "solutions" => array(),
                                "images" => array(),
                                "reference" => $check->_reference->name,
                                "referenceUrl" => $check->_reference->url,
                                "referenceCode" => $check->reference_code,
                                "referenceCodeUrl" => $check->reference_url,
                                "info" => $tc->rating == TargetCheck::RATING_INFO,
                                "separate" => in_array($category->check_category_id, $templateCategoryIds),
                            );

                            if ($tc->solution) {
                                $checkData["solutions"][] = $this->_prepareProjectReportText($tc->solution);
                            }

                            if ($checkData["info"]) {
                                $controlData["info"]++;
                                $categoryData["info"]++;
                                $targetData["info"]++;
                                $hasInfo = true;
                            }

                            if ($checkData["separate"]) {
                                $controlData["separate"]++;
                                $categoryData["separate"]++;

                                if (!in_array($category->check_category_id, $targetData["separate"])) {
                                    $targetData["separate"][] = $category->check_category_id;
                                }

                                $targetData["separateCount"]++;

                                $hasSeparate = true;
                            }

                            $checkData["rating"] = $tc->rating;

                            switch ($tc->rating) {
                                case TargetCheck::RATING_INFO:
                                    $checkData["ratingColor"] = "#3A87AD";
                                    $checkData["ratingValue"] = 0;
                                    $checksInfo++;
                                    break;

                                case TargetCheck::RATING_LOW_RISK:
                                    $checkData["ratingColor"] = "#53A254";
                                    $checkData["ratingValue"] = 1;
                                    $checksLow++;
                                    break;

                                case TargetCheck::RATING_MED_RISK:
                                    $checkData["ratingColor"] = "#DACE2F";
                                    $checkData["ratingValue"] = 2;
                                    $checksMed++;
                                    break;

                                case TargetCheck::RATING_HIGH_RISK:
                                    $checkData["ratingColor"] = "#D63515";
                                    $checkData["ratingValue"] = 3;
                                    $checksHigh++;
                                    break;
                            }

                            if ($tc->solutions) {
                                foreach ($tc->solutions as $solution) {
                                    $checkData["solutions"][] = $this->_prepareProjectReportText($solution->solution->localizedSolution);
                                }
                            }

                            if ($tc->attachments) {
                                foreach ($tc->attachments as $attachment) {
                                    if (in_array($attachment->type, array("image/jpeg", "image/png", "image/gif", "image/pjpeg"))) {
                                        $checkData["images"][] = array(
                                            'title' => $attachment->title,
                                            'image' => Yii::app()->params["attachments"]["path"] . "/" . $attachment->path
                                        );
                                    } else {
                                        $reportAttachments[] = array(
                                            "host" => $target->host,
                                            "title" => $attachment->title,
                                            "check" => $check->name,
                                            "filename" => $attachment->name,
                                            "path" => Yii::app()->params["attachments"]["path"] . "/" . $attachment->path,
                                        );
                                    }
                                }
                            }

                            if (in_array($tc->rating, array(TargetCheck::RATING_HIGH_RISK, TargetCheck::RATING_MED_RISK, TargetCheck::RATING_LOW_RISK))) {
                                $reducedChecks[] = array(
                                    "target" => array(
                                        "id" => $target->id,
                                        "host" => $target->host,
                                        "description" => $target->description
                                    ),
                                    "id" => $checkData["id"],
                                    "name" => $checkData["name"],
                                    "question" => $checkData["question"],
                                    "solution" => $checkData["solutions"] ? implode("\n", $checkData["solutions"]) : "",
                                    "rating" => $checkData["rating"],
                                    "result" => $checkData["result"],
                                    "poc" => $checkData["poc"],
                                    "links" => $checkData["links"],
                                    "ratingValue" => $checkData["ratingValue"],
                                );
                            }

                            $controlData["checks"][] = $checkData;
                            $ctr++;
                        }
                    }

                    $controlData["rating"] = $this->_getChecksRating($controlData["checks"]);
                    $controlData["checkCount"] = count($controlData["checks"]);
                    $categoryData["checkCount"] += count($controlData["checks"]);
                    $targetData["checkCount"] += count($controlData["checks"]);
                    $totalCheckCount += count($controlData["checks"]);

                    if ($controlData["checks"]) {
                        $categoryData["controls"][] = $controlData;
                    }
                }

                if ($categoryData["checkCount"]) {
                    $categoryData["rating"] = $this->_getCategoryRating($categoryData);

                    if ($categoryData["controls"]) {
                        $targetData["categories"][] = $categoryData;
                    }
                }
            }

            if ($targetData["checkCount"]) {
                $targetData["rating"] = $this->_getTargetRating($targetData);
            }

            $data[] = $targetData;
        }

        if ($totalCheckCount) {
            $totalRating = $this->_getTotalRating($data);
        }

        $this->project["rating"] = $totalRating;
        $this->project["checks"] = $totalCheckCount;
        $this->project["checksInfo"] = $checksInfo;
        $this->project["checksLow"] = $checksLow;
        $this->project["checksMed"] = $checksMed;
        $this->project["checksHigh"] = $checksHigh;
        $this->project["reducedChecks"] = $reducedChecks;
        $this->project["hasInfo"] = $hasInfo;
        $this->project["hasSeparate"] = $hasSeparate;

        $data = array(
            "data" => $data,
            "targets" => $targets,
            "project" => $project,
            "rating" => $totalRating,
            "checks" => $totalCheckCount,
            "checksInfo" => $checksInfo,
            "checksLow" => $checksLow,
            "checksMed" => $checksMed,
            "checksHigh" => $checksHigh,
            "attachments" => $reportAttachments
        );

        return $data;
    }

    /**
     * GT project report
     * @param $templateCategoryIds
     * @param $project
     * @param $language
     * @return array
     */
    private function _gtProjectReport($templateCategoryIds, $project, $language) {
        $targets = array();
        $projectTargets = array();

        $criteria = new CDbCriteria();
        $criteria->addColumnCondition(array('project_id' => $project->id));
        $criteria->order = 'target ASC';

        $checks = ProjectGtCheck::model()->findAll($criteria);
        $targetId = 1;

        foreach ($checks as $check) {
            if (!$check->target) {
                continue;
            }

            if (!in_array($check->target, $targets)) {
                $targets[] = $check->target;
                $projectTargets[$check->target] = array(
                    "id" => $targetId,
                    "host" => $check->target,
                    "checkCount" => 0,
                    "finishedCount" => 0,
                    "infoCount" => 0,
                    "lowRiskCount" => 0,
                    "medRiskCount" => 0,
                    "highRiskCount" => 0
                );
            }

            $projectTargets[$check->target]["checkCount"]++;

            if ($check->status = ProjectGtCheck::STATUS_FINISHED) {
                $projectTargets[$check->target]["finishedCount"]++;

                switch ($check->rating) {
                    case ProjectGtCheck::RATING_INFO:
                        $projectTargets[$check->target]["infoCount"]++;
                        break;

                    case ProjectGtCheck::RATING_LOW_RISK:
                        $projectTargets[$check->target]["lowRiskCount"]++;
                        break;

                    case ProjectGtCheck::RATING_MED_RISK:
                        $projectTargets[$check->target]["medRiskCount"]++;
                        break;

                    case ProjectGtCheck::RATING_HIGH_RISK:
                        $projectTargets[$check->target]["highRiskCount"]++;
                        break;

                    default:
                        break;
                }
            }
        }

        $this->project['targets'] = $projectTargets;
        $this->_generateFulfillmentDegreeReport(null, true);

        $data = array();
        $hasInfo = true;
        $hasSeparate = false;

        $totalRating = 0.0;
        $totalCheckCount = 0;
        $checksHigh = 0;
        $checksMed = 0;
        $checksLow = 0;
        $checksInfo = 0;
        $reportAttachments = array();

        $reducedChecks = array();
        $ratings = ProjectGtCheck::getRatingNames();
        $targetId = 1;

        foreach ($targets as $target) {
            $targetData = array(
                'id' => $targetId,
                'host' => $target,
                'description' => "",
                'rating' => 0.0,
                'checkCount' => 0,
                'categories' => array(),
                'info' => 0,
                'separate' => array(),
                'separateCount' => 0,
            );

            // prepare all checks
            $criteria = new CDBCriteria();
            $criteria->addCondition('t.project_id = :project AND t.target = :target AND t.status = :status AND rating != :hidden');

            $criteria->params = array(
                "hidden" => ProjectGtCheck::RATING_HIDDEN,
                "project" => $project->id,
                "target" => $target,
                "status" => ProjectGtCheck::STATUS_FINISHED,
            );

            $criteria->together = true;

            $checks = ProjectGtCheck::model()->with(array(
                'check' => array(
                    'with' => array(
                        'check' => array(
                            'alias' => 'innerCheck',
                            'with' => array(
                                'l10n' => array(
                                    'joinType' => 'LEFT JOIN',
                                    'on' => 'l10n.language_id = :language_id',
                                    'params' => array('language_id' => $language)
                                ),
                                'control' => array(
                                    'with' => array(
                                        'l10n' => array(
                                            'alias' => 'c_l10n',
                                            'joinType' => 'LEFT JOIN',
                                            'on' => 'c_l10n.language_id = :language_id',
                                            'params' => array('language_id' => $language)
                                        ),
                                        'category' => array(
                                            'with' => array(
                                                'l10n' => array(
                                                    'alias' => 'ca_l10n',
                                                    'joinType' => 'LEFT JOIN',
                                                    'on' => 'ca_l10n.language_id = :language_id',
                                                    'params' => array('language_id' => $language)
                                                ),
                                            ),
                                        ),
                                    ),
                                ),
                                '_reference',
                            ),
                        ),
                    ),
                ),
                'solutions' => array(
                    'with' => array(
                        'solution' => array(
                            'with' => array(
                                'l10n' => array(
                                    'alias' => 'l10n_s',
                                    'joinType' => 'LEFT JOIN',
                                    'on' => 'l10n_s.language_id = :language_id',
                                    'params' => array('language_id' => $language)
                                ),
                            ),
                        ),
                    ),
                ),
                'attachments',
            ))->findAll($criteria);

            $categories = array();

            foreach ($checks as $check) {
                $innerCheck = $check->check->check;

                if ($this->_system->demo && !$innerCheck->demo) {
                    continue;
                }

                $categoryId = $innerCheck->control->check_category_id;
                $controlId = $innerCheck->check_control_id;

                if (!array_key_exists($categoryId, $categories)) {
                    $categories[$categoryId] = array(
                        'id' => $categoryId,
                        'name' => $innerCheck->control->category->localizedName,
                        'controls' => array(),
                    );
                }

                if (!array_key_exists($innerCheck->check_control_id, $categories[$categoryId]["controls"])) {
                    $categories[$categoryId]["controls"][$controlId] = array(
                        'id' => $controlId,
                        'name' => $innerCheck->control->localizedName,
                        'checks' => array(),
                    );
                }

                $categories[$categoryId]["controls"][$controlId]["checks"][] = $check;
            }

            foreach ($categories as $category) {
                $categoryData = array(
                    'id'  => $category["id"],
                    'name' => $category["name"],
                    'rating' => 0.0,
                    'checkCount' => 0,
                    'controls' => array(),
                    'info' => 0,
                    'separate' => 0,
                );

                if (!$category["controls"]) {
                    continue;
                }

                foreach ($category["controls"] as $control) {
                    $controlData = array(
                        'id' => $control["id"],
                        'name' => $control["name"],
                        'rating' => 0.0,
                        'checkCount' => 0,
                        'checks' => array(),
                        'info' => 0,
                        'separate' => 0,
                    );

                    if (!$control["checks"]) {
                        continue;
                    }

                    foreach ($control["checks"] as $check) {
                        $innerCheck = $check->check->check;

                        if ($this->_system->demo && !$innerCheck->demo) {
                            continue;
                        }

                        $checkData = array(
                            "id" => $check->gt_check_id,
                            "name" => $innerCheck->localizedName,
                            "background" => $this->_prepareProjectReportText($innerCheck->localizedBackgroundInfo),
                            "question" => $this->_prepareProjectReportText($innerCheck->localizedQuestion),
                            "result" => $check->result,
                            "tableResult" => $check->table_result,
                            "rating" => 0,
                            "ratingName" => $ratings[$check->rating],
                            "ratingColor" => "#999999",
                            "solutions" => array(),
                            "images" => array(),
                            "reference" => $innerCheck->_reference->name,
                            "referenceUrl" => $innerCheck->_reference->url,
                            "referenceCode" => $innerCheck->reference_code,
                            "referenceCodeUrl" => $innerCheck->reference_url,
                            "info" => $check->rating == ProjectGtCheck::RATING_INFO,
                            "separate" => in_array($category["id"], $templateCategoryIds),
                        );

                        if ($check->solution) {
                            $checkData["solutions"][] = $this->_prepareProjectReportText($check->solution);
                        }

                        if ($checkData['info']) {
                            $controlData['info']++;
                            $categoryData['info']++;
                            $targetData['info']++;
                            $hasInfo = true;
                        }

                        if ($checkData['separate']) {
                            $controlData['separate']++;
                            $categoryData['separate']++;

                            if (!in_array($category["id"], $targetData['separate'])) {
                                $targetData['separate'][] = $category["id"];
                            }

                            $targetData['separateCount']++;

                            $hasSeparate = true;
                        }

                        $checkData["rating"] = $check->rating;

                        switch ($check->rating) {
                            case TargetCheck::RATING_INFO:
                                $checkData['ratingColor'] = '#3A87AD';
                                $checkData["ratingValue"] = 0;
                                $checksInfo++;
                                break;

                            case TargetCheck::RATING_LOW_RISK:
                                $checkData['ratingColor'] = '#53A254';
                                $checkData["ratingValue"] = 1;
                                $checksLow++;
                                break;

                            case TargetCheck::RATING_MED_RISK:
                                $checkData['ratingColor'] = '#DACE2F';
                                $checkData["ratingValue"] = 2;
                                $checksMed++;
                                break;

                            case TargetCheck::RATING_HIGH_RISK:
                                $checkData['ratingColor'] = '#D63515';
                                $checkData["ratingValue"] = 3;
                                $checksHigh++;
                                break;
                        }

                        if ($check->solutions) {
                            foreach ($check->solutions as $solution) {
                                $checkData['solutions'][] = $this->_prepareProjectReportText($solution->solution->localizedSolution);
                            }
                        }

                        if ($check->attachments) {
                            foreach ($check->attachments as $attachment) {
                                if (in_array($attachment->type, array('image/jpeg', 'image/png', 'image/gif', 'image/pjpeg'))) {
                                    $checkData['images'][] = array(
                                        'title' => $attachment->title,
                                        'image' => Yii::app()->params['attachments']['path'] . '/' . $attachment->path
                                    );
                                } else {
                                    $reportAttachments[] = array(
                                        "host" => $target->host,
                                        "title" => $attachment->title,
                                        "check" => $check->name,
                                        "filename" => $attachment->name,
                                        "path" => Yii::app()->params["attachments"]["path"] . "/" . $attachment->path,
                                    );
                                }
                            }
                        }

                        if (in_array($check->rating, array(TargetCheck::RATING_HIGH_RISK, TargetCheck::RATING_MED_RISK, TargetCheck::RATING_LOW_RISK))) {
                            $reducedChecks[] = array(
                                'target' => array(
                                    'id' => $targetId,
                                    'host' => $target,
                                    'description' => ""
                                ),
                                'id' => $checkData['id'],
                                'name' => $checkData['name'],
                                'question' => $checkData['question'],
                                'solution' => $checkData['solutions'] ? implode("\n", $checkData['solutions']) : Yii::t('app', 'N/A'),
                                'rating' => $check->rating,
                                "result" => $checkData["result"],
                                "ratingValue" => $checkData["ratingValue"],
                            );
                        }

                        // put checks with RATING_INFO rating to a separate category
                        $controlData['checks'][] = $checkData;
                    }

                    $controlData['rating'] = $this->_getChecksRating($controlData["checks"]);

                    $controlData['checkCount'] = count($checks);
                    $categoryData['checkCount'] += $controlData['checkCount'];
                    $targetData['checkCount'] += $controlData['checkCount'];
                    $totalCheckCount += $controlData['checkCount'];

                    if ($controlData['checks']) {
                        $categoryData['controls'][] = $controlData;
                    }
                }

                if ($categoryData['checkCount']) {
                    $categoryData['rating'] = $this->_getCategoryRating($categoryData);

                    if ($categoryData['controls']) {
                        $targetData['categories'][] = $categoryData;
                    }
                }
            }

            if ($targetData['checkCount']) {
                $targetData['rating'] = $this->_getTargetRating($targetData);
            }

            $data[] = $targetData;
            $targetId++;
        }

        if ($totalCheckCount) {
            $totalRating = $this->_getTotalRating($data);
        }

        $this->project['rating'] = $totalRating;
        $this->project['checks'] = $totalCheckCount;
        $this->project['checksInfo'] = $checksInfo;
        $this->project['checksLow'] = $checksLow;
        $this->project['checksMed'] = $checksMed;
        $this->project['checksHigh'] = $checksHigh;
        $this->project['reducedChecks'] = $reducedChecks;
        $this->project['hasInfo'] = $hasInfo;
        $this->project['hasSeparate'] = $hasSeparate;

        $data = array(
            "data" => $data,
            "targets" => $projectTargets,
            "project" => $project,
            "rating" => $totalRating,
            "checks" => $totalCheckCount,
            "checksInfo" => $checksInfo,
            "checksMed" => $checksMed,
            "checksHigh" => $checksHigh,
            "attachments" => $reportAttachments
        );

        return $data;
    }

    /**
     * Generate project function.
     */
    private function _generateProjectReport($model) {
        $clientId = $model->clientId;
        $projectId = $model->projectId;
        $targetIds = $model->targetIds;
        $options = $model->options;
        $templateId = $model->templateId;

        if (!$options) {
            $options = array();
        }

        $project = Project::model()->with(array(
            'projectUsers' => array(
                'with' => 'user'
            ),
            'client',
            'targets',
        ))->findByAttributes(array(
            'client_id' => $clientId,
            'id'        => $projectId
        ));

        if ($project === null) {
            Yii::app()->user->setFlash('error', Yii::t('app', 'Project not found.'));
            return;
        }

        if (!$project->checkPermission()) {
            Yii::app()->user->setFlash('error', Yii::t('app', 'Access denied.'));
            return;
        }

        $this->project = array(
            'project' => $project
        );

        if (!$project->guided_test && (!$targetIds || !count($targetIds))) {
            Yii::app()->user->setFlash('error', Yii::t('app', 'Please select at least 1 target.'));
            return;
        }

        $language = Language::model()->findByAttributes(array(
            'code' => Yii::app()->language
        ));

        if ($language) {
            $language = $language->id;
        }

        $template = ReportTemplate::model()->with(array(
            'l10n' => array(
                'joinType' => 'LEFT JOIN',
                'on'       => 'language_id = :language_id',
                'params'   => array( 'language_id' => $language )
            ),
            'summary' => array(
                'with' => array(
                    'l10n' => array(
                        'alias'  => 'summary_l10n',
                        'on'     => 'summary_l10n.language_id = :language_id',
                        'params' => array( 'language_id' => $language )
                    )
                )
            ),
            'sections' => array(
                'order' => 'sections.sort_order ASC',
                'with'  => array(
                    'l10n' => array(
                        'alias'  => 'section_l10n',
                        'on'     => 'section_l10n.language_id = :language_id',
                        'params' => array( 'language_id' => $language )
                    )
                )
            ),
        ))->findByPk($templateId);

        if ($template === null) {
            Yii::app()->user->setFlash('error', Yii::t('app', 'Template not found.'));
            return;
        }

        $templateCategoryIds = array();

        foreach ($template->sections as $section) {
            $templateCategoryIds[] = $section->check_category_id;
        }

        if ($project->guided_test) {
            $data = $this->_gtProjectReport($templateCategoryIds, $project, $language);
        } else {
            $data = $this->_projectReport($targetIds, $templateCategoryIds, $project, $language);
        }

        if ($template->type == ReportTemplate::TYPE_DOCX) {
            $plugin = ReportPlugin::getPlugin($template, $data);
            $plugin->generate();
            $plugin->sendOverHttp();

            exit();
        }

        $reportAttachments = $data["attachments"];
        $data = $data["data"];
        $fileName = Yii::t('app', 'Penetration Test Report') . ' - ' . $project->name . ' (' . $project->year . ').rtf';
        $zipFileName = Yii::t('app', 'Penetration Test Report') . ' - ' . $project->name . ' (' . $project->year . ').zip';

        $this->_rtfSetup($model);
        $section = $this->rtf->addSection();

        // footer
        $footer = $section->addFooter();
        $footer->writeText($template->localizedFooter, $this->footerFont, $this->noPar);
        $footer->writeText(Yii::t('app', 'Penetration Test Report') . ': ' . $project->name . ' / ' . $project->year . ', ', $this->footerFont, $this->noPar);
        $footer->writePlainRtfCode(
            '\fs' . ($this->footerFont->getSize() * 2) . ' \f' . $this->footerFont->getFontIndex() . ' ' .
             Yii::t('app', 'page {page} of {numPages}',
            array(
                '{page}'     => '{\field{\*\fldinst {PAGE}}{\fldrslt {1}}}',
                '{numPages}' => '{\field{\*\fldinst {NUMPAGES}}{\fldrslt {1}}}'
            )
        ));

        $ratingImages = $this->_getRatingImages($template);

        // title
        if (in_array('title', $options))
        {
            // header image
            if ($template->header_image_path)
            {
                $extension = 'jpg';

                if ($template->header_image_type == 'image/png')
                    $extension = 'png';

                $filePath = Yii::app()->params['tmpPath'] . '/' . $template->header_image_path . '.' . $extension;

                if (@copy(
                    Yii::app()->params['reports']['headerImages']['path'] . '/' . $template->header_image_path,
                    $filePath
                ))
                {
                    $section->addImage($filePath, $this->centerPar, $this->docWidth);
                    @unlink($filePath);
                }
            }

            $section->writeText(Yii::t('app', 'Penetration Test Report') . ': ' . $project->name, $this->h1Font, $this->titlePar);
            $section->writeText(Yii::t('app', 'Prepared for') . ":\n", $this->textFont, $this->noPar);

            $client = Client::model()->findByPk($clientId);

            $table = $section->addTable(PHPRtfLite_Table::ALIGN_LEFT);
            $table->addRows(1);
            $table->addColumnsList(array( $this->docWidth * 0.4, $this->docWidth * 0.6 ));

            $col = 1;

            if ($client->logo_path)
            {
                $extension = 'jpg';

                if ($client->logo_type == 'image/png')
                    $extension = 'png';

                $filePath = Yii::app()->params['tmpPath'] . '/' . $client->logo_path . '.' . $extension;

                if (@copy(
                    Yii::app()->params['clientLogos']['path'] . '/' . $client->logo_path,
                    $filePath
                ))
                {
                    $table->getCell(1, $col)->addImage($filePath, $this->leftPar, $this->docWidth * 0.35);
                    @unlink($filePath);
                    $col++;
                }
            }

            $table->getCell(1, $col)->writeText(Yii::t('app', 'Company'), $this->boldFont, $this->titlePar);
            $table->getCell(1, $col)->writeText($client->name, $this->textFont, $this->noPar);

            if ($client->address)
                $table->getCell(1, $col)->writeText($client->address, $this->textFont, $this->noPar);

            if ($client->city || $client->state)
            {
                $address = array();

                if ($client->city)
                    $address[] = $client->city;

                if ($client->state)
                    $address[] = $client->state;

                $table->getCell(1, $col)->writeText(implode(', ', $address), $this->textFont, $this->noPar);
            }

            if ($client->country)
                $table->getCell(1, $col)->writeText($client->country, $this->textFont, $this->noPar);

            if ($client->postcode)
                $table->getCell(1, $col)->writeText($client->postcode, $this->textFont, $this->noPar);

            if ($client->website)
                $table->getCell(1, $col)->writeHyperLink($client->website, $client->website, $this->linkFont, $this->noPar);

            if ($client->contact_name || $client->contact_email || $client->contact_phone || $client->contact_fax)
            {
                $table->getCell(1, $col)->writeText(' ', $this->textFont, $this->noPar);

                if ($client->contact_name)
                    $table->getCell(1, $col)->writeText($client->contact_name, $this->textFont, $this->noPar);

                if ($client->contact_email)
                    $table->getCell(1, $col)->writeHyperLink('mailto:' . $client->contact_email, $client->contact_email, $this->linkFont, $this->noPar);

                if ($client->contact_phone)
                    $table->getCell(1, $col)->writeText(Yii::t('app', 'Phone') . ': ' . $client->contact_phone, $this->textFont, $this->noPar);

                if ($client->contact_fax)
                    $table->getCell(1, $col)->writeText(Yii::t('app', 'Fax') . ': ' . $client->contact_fax, $this->textFont, $this->noPar);
            }

            $section->insertPageBreak();

            $section->writeText(Yii::t('app', 'Document Information') . "\n", $this->boldFont, $this->noPar);

            $table = $section->addTable(PHPRtfLite_Table::ALIGN_LEFT);
            $table->addRows(6);
            $table->addColumnsList(array( $this->docWidth * 0.4, $this->docWidth * 0.6 ));

            $user = User::model()->findByPk(Yii::app()->user->id);
            $owner = $user->name ? $user->name : $user->email;

            $table->getCell(1, 1)->writeText(Yii::t('app', 'Owner'), $this->textFont, $this->noPar);
            $table->getCell(1, 2)->writeText($owner, $this->textFont, $this->noPar);

            $table->getCell(2, 1)->writeText(Yii::t('app', 'Status'), $this->textFont, $this->noPar);
            $table->getCell(2, 2)->writeText(Yii::t('app', 'Draft'), $this->textFont, $this->noPar);

            $table->getCell(3, 1)->writeText(Yii::t('app', 'Originator'), $this->textFont, $this->noPar);
            $table->getCell(3, 2)->writeText($owner, $this->textFont, $this->noPar);

            $table->getCell(4, 1)->writeText(Yii::t('app', 'Review'), $this->textFont, $this->noPar);
            $table->getCell(4, 2)->writeText($owner, $this->textFont, $this->noPar);

            $table->getCell(5, 1)->writeText(Yii::t('app', 'File Name'), $this->textFont, $this->noPar);
            $table->getCell(5, 2)->writeText($fileName, $this->textFont, $this->noPar);

            $table->getCell(6, 1)->writeText(Yii::t('app', 'Modified'), $this->textFont, $this->noPar);
            $table->getCell(6, 2)->writeText(date('d.m.Y'), $this->textFont, $this->noPar);

            $section->writeText(Yii::t('app', 'Changes') . "\n", $this->boldFont, $this->noPar);

            $table = $section->addTable(PHPRtfLite_Table::ALIGN_LEFT);
            $table->addRows(2);
            $table->addColumnsList(array( $this->docWidth * 0.4, $this->docWidth * 0.6 ));

            $table->getCell(1, 1)->writeText(Yii::t('app', 'Version') . ' / ' . Yii::t('app', 'Date'), $this->boldFont, $this->noPar);
            $table->getCell(2, 1)->writeText('1.0 / ' . date('d.m.Y'), $this->textFont, $this->noPar);

            $table->getCell(1, 2)->writeText(Yii::t('app', 'Notes'), $this->boldFont, $this->noPar);
            $table->getCell(2, 2)->writeText(Yii::t('app', 'Draft'), $this->textFont, $this->noPar);

            $this->toc = $this->rtf->addSection();
            $this->toc->writeText(Yii::t('app', 'Table of Contents'), $this->h2Font, $this->h3Par);
            $this->toc->writeText("\n\n", $this->textFont);
            $section = $this->rtf->addSection();
        }
        else
            $section->writeText(Yii::t('app', 'Penetration Test Report') . ': ' . $project->name, $this->h2Font, $this->titlePar);

        $sectionNumber = 1;

        // introduction
        if (in_array('intro', $options) && $template->localizedIntro) {
            $this->toc->writeHyperLink(
                '#introduction',
                $sectionNumber . '. ' . Yii::t('app', 'Introduction') . "\n",
                $this->textFont
            );

            $section->writeBookmark(
                'introduction',
                $sectionNumber . '. ' . Yii::t('app', 'Introduction'),
                $this->h2Font,
                $this->h3Par
            );

            $this->_renderText($section, $template->localizedIntro);

            $section->insertPageBreak();
            $sectionNumber++;
        }

        // summary
        $this->toc->writeHyperLink(
            '#summary',
            $sectionNumber . '. ' . Yii::t('app', 'Summary') . "\n",
            $this->textFont
        );

        $section->writeBookmark(
            'summary',
            $sectionNumber . '. ' . Yii::t('app', 'Summary'),
            $this->h2Font,
            $this->h3Par
        );

        $subsectionNumber = 1;

        $summary = false;

        if (in_array('summary', $options) && $template->summary)
        {
            foreach ($template->summary as $sum)
                if ($this->project['rating'] >= $sum->rating_from && $this->project['rating'] <= $sum->rating_to)
                {
                    $summary = $sum;
                    break;
                }

            if ($summary)
            {
                $this->toc->writeHyperLink(
                    '#overview',
                    '    ' . $sectionNumber . '.' . $subsectionNumber . '. ' . Yii::t('app', 'Overview') . "\n",
                    $this->textFont
                );

                $section->writeBookmark(
                    'overview',
                    $sectionNumber . '.' . $subsectionNumber . '. ' . Yii::t('app', 'Overview') . "\n",
                    $this->h3Font,
                    $this->noPar
                );

                $this->_renderText($section, $summary->localizedSummary . "<br>");
                $subsectionNumber++;
            }
        }

        $this->toc->writeHyperLink(
            '#security_level',
            '    ' . $sectionNumber . '.' . $subsectionNumber . '. ' . Yii::t('app', 'Security Level') . "\n",
            $this->textFont
        );

        $section->writeBookmark(
            'security_level',
            $sectionNumber . '.' . $subsectionNumber . '. ' . Yii::t('app', 'Security Level') . "\n",
            $this->h3Font,
            $this->noPar
        );

        if ($template->localizedSecurityLevelIntro)
            $this->_renderText($section, $template->localizedSecurityLevelIntro . "<br>");

        $subsectionNumber++;

        $system = System::model()->findByPk(1);
        $section->addImage($this->_generateRatingImage($this->project['rating'], $system), $this->centerPar);
        $section->writeText('Rating: ' . sprintf('%.2f', $this->project['rating']) . ($summary ? ' (' . $summary->localizedTitle . ')' : '') . "\n", $this->textFont, $this->centerPar);

        $table = $section->addTable(PHPRtfLite_Table::ALIGN_LEFT);

        $table->addRows(count($data) + 1);
        $table->addColumnsList(array( $this->docWidth * 0.44, $this->docWidth * 0.39, $this->docWidth * 0.17 ));
        $table->mergeCellRange(1, 2, 1, 3);
        $table->setFontForCellRange($this->boldFont, 1, 1, 1, 3);
        $table->setBackgroundForCellRange('#E0E0E0', 1, 1, 1, 3);
        $table->setFontForCellRange($this->textFont, 2, 1, count($data) + 1, 3);
        $table->setBorderForCellRange($this->thinBorder, 1, 1, count($data) + 1, 3);
        $table->setFirstRowAsHeader();

        // set paddings
        for ($row = 1; $row <= count($data) + 1; $row++)
            for ($col = 1; $col <= 3; $col++)
            {
                $table->getCell($row, $col)->setCellPaddings($model->cellPadding, $model->cellPadding, $model->cellPadding, $model->cellPadding);
                $table->getCell($row, $col)->setVerticalAlignment(PHPRtfLite_Table_Cell::VERTICAL_ALIGN_CENTER);
                $table->getCell($row, $col)->setTextAlignment(PHPRtfLite_Table_Cell::TEXT_ALIGN_LEFT);
            }

        $table->writeToCell(1, 1, Yii::t('app', 'Target'));
        $table->writeToCell(1, 2, Yii::t('app', 'Rating'));

        $row = 2;
        $system = System::model()->findByPk(1);

        foreach ($data as $target) {
            $table->writeToCell($row, 1, $target['host']);

            if ($target['description']) {
                $table->getCell($row, 1)->writeText(' / ', $this->textFont);
                $table->getCell($row, 1)->writeText($target['description'], new PHPRtfLite_Font($this->fontSize, $this->fontFamily, '#909090'));
            }

            $table->addImageToCell($row, 2, $this->_generateRatingImage($target['rating'], $system), null, $this->docWidth * 0.34);
            $table->writeToCell($row, 3, sprintf('%.2f', $target['rating']));

            $table->getCell($row, 2)->setTextAlignment(PHPRtfLite_Table_Cell::TEXT_ALIGN_CENTER);

            $row++;
        }

        $this->toc->writeHyperLink(
            '#vuln_distribution',
            '    ' . $sectionNumber . '.' . $subsectionNumber . '. ' . Yii::t('app', 'Vulnerability Distribution') . "\n",
            $this->textFont
        );

        $section->writeBookmark(
            'vuln_distribution',
            $sectionNumber . '.' . $subsectionNumber . '. ' . Yii::t('app', 'Vulnerability Distribution') . "\n",
            $this->h3Font,
            $this->noPar
        );

        if ($template->localizedVulnDistributionIntro)
            $this->_renderText($section, $template->localizedVulnDistributionIntro . "<br>");

        $subsectionNumber++;
        $section->addImage($this->_generateVulnDistributionChart(), $this->centerPar);

        if (in_array('fulfillment', $options))
        {
            $this->toc->writeHyperLink(
                '#degree',
                '    ' . $sectionNumber . '.' . $subsectionNumber . '. ' . Yii::t('app', 'Degree of Fulfillment') . "\n",
                $this->textFont
            );

            $section->writeText("\n");
            $section->writeBookmark(
                'degree',
                $sectionNumber . '.' . $subsectionNumber . '. ' . Yii::t('app', 'Degree of Fulfillment'),
                $this->h3Font,
                $this->h3Par
            );

            if ($template->localizedDegreeIntro)
                $this->_renderText($section, $template->localizedDegreeIntro . "<br><br>");

            $this->_generateFulfillmentDegreeReport($model, false, $section, $sectionNumber . '.' . $subsectionNumber);

            $subsectionNumber++;
        }

        if (in_array('matrix', $options))
        {
            $this->toc->writeHyperLink(
                '#risk_matrix',
                '    ' . $sectionNumber . '.' . $subsectionNumber . '. ' . Yii::t('app', 'Risk Matrix') . "\n",
                $this->textFont
            );

            $section->writeBookmark(
                'risk_matrix',
                $sectionNumber . '.' . $subsectionNumber . '. ' . Yii::t('app', 'Risk Matrix'),
                $this->h3Font,
                $this->h3Par
            );

            $riskMatrixModel = new RiskMatrixForm();
            $riskMatrixModel->attributes = $_POST['RiskMatrixForm'];

            if ($template->localizedRiskIntro)
                $this->_renderText($section, $template->localizedRiskIntro . "<br>");

            $this->_generateRiskMatrixReport($riskMatrixModel, $section, $sectionNumber . '.' . $subsectionNumber);

            $subsectionNumber++;
        }

        $sectionNumber++;
        $section->insertPageBreak();

        $subsectionNumber = 1;

        // reduced vulnerability list
        if (in_array('vulns', $options))
        {
            $this->toc->writeHyperLink(
                '#reduced_vulns',
                '    ' . $sectionNumber . '. ' . Yii::t('app', 'Results and Recommendations') . "\n",
                $this->textFont
            );

            $section->writeBookmark(
                'reduced_vulns',
                $sectionNumber . '. ' . Yii::t('app', 'Results and Recommendations'),
                $this->h2Font,
                $this->h3Par
            );

            if ($template->localizedReducedIntro)
                $this->_renderText($section, $template->localizedReducedIntro . "<br>");

            $table = $section->addTable(PHPRtfLite_Table::ALIGN_LEFT);
            $table->addColumnsList(array( $this->docWidth * 0.15, $this->docWidth * 0.2, $this->docWidth * 0.65 ));
            $table->addRows(7);

            $table->setFontForCellRange($this->boldFont, 1, 1, 1, 3);
            $table->setBackgroundForCellRange('#E0E0E0', 1, 1, 1, 3);
            $table->setFontForCellRange($this->textFont, 2, 1, 7, 3);
            $table->setBorderForCellRange($this->thinBorder, 1, 1, 7, 3);
            $table->setFirstRowAsHeader();

            // set paddings
            for ($row = 1; $row <= 7; $row++) {
                for ($col = 1; $col <= 3; $col++) {
                    $table->getCell($row, $col)->setCellPaddings($model->cellPadding, $model->cellPadding, $model->cellPadding, $model->cellPadding);

                    if ($row >= 2 && $row <= 4 && $col >= 1 && $col <= 2) {
                        $table->getCell($row, $col)->setVerticalAlignment(PHPRtfLite_Table_Cell::VERTICAL_ALIGN_TOP);
                    } else {
                        $table->getCell($row, $col)->setVerticalAlignment(PHPRtfLite_Table_Cell::VERTICAL_ALIGN_CENTER);
                    }
                }
            }


            $table->getCell(1, 1)->writeText(Yii::t('app', 'Symbol'));
            $table->getCell(1, 2)->writeText(Yii::t('app', 'Meaning'));
            $table->getCell(1, 3)->writeText(Yii::t('app', 'Description'));

            $table->addImageToCell(2, 1, $ratingImages[TargetCheck::RATING_HIGH_RISK]);
            $table->addImageToCell(3, 1, $ratingImages[TargetCheck::RATING_MED_RISK]);
            $table->addImageToCell(4, 1, $ratingImages[TargetCheck::RATING_LOW_RISK]);
            $table->addImageToCell(5, 1, $ratingImages[TargetCheck::RATING_NONE]);
            $table->addImageToCell(6, 1, $ratingImages[TargetCheck::RATING_NO_VULNERABILITY]);
            $table->addImageToCell(7, 1, $ratingImages[TargetCheck::RATING_INFO]);

            $table->getCell(2, 2)->writeText(Yii::t('app', 'High Risk'), $this->textFont);
            $table->getCell(3, 2)->writeText(Yii::t('app', 'Med Risk'), $this->textFont);
            $table->getCell(4, 2)->writeText(Yii::t('app', 'Low Risk'), $this->textFont);
            $table->getCell(5, 2)->writeText(Yii::t('app', 'No Test Done'), $this->textFont);
            $table->getCell(6, 2)->writeText(Yii::t('app', 'No Vulnerability'), $this->textFont);
            $table->getCell(7, 2)->writeText(Yii::t('app', 'Information'), $this->textFont);

            $this->_renderText($table->getCell(2, 3), $template->localizedHighDescription, true);
            $this->_renderText($table->getCell(3, 3), $template->localizedMedDescription, true);
            $this->_renderText($table->getCell(4, 3), $template->localizedLowDescription, true);
            $this->_renderText($table->getCell(5, 3), $template->localizedNoneDescription, true);
            $this->_renderText($table->getCell(6, 3), $template->localizedNoVulnDescription, true);
            $this->_renderText($table->getCell(7, 3), $template->localizedInfoDescription, true);

            $section->writeText("\n");

            if (!count($this->project['reducedChecks'])) {
                $section->writeText("\n" . Yii::t('app', 'No vulnerabilities found.') . "\n", $this->textFont, $this->noPar);
            } else {
                $table = $section->addTable(PHPRtfLite_Table::ALIGN_LEFT);
                $table->addRows(count($this->project['reducedChecks']) + 1);
                $table->addColumnsList(array(
                    $this->docWidth * 0.2,
                    $this->docWidth * 0.2,
                    $this->docWidth * 0.25,
                    $this->docWidth * 0.25,
                    $this->docWidth * 0.1
                ));

                $table->setBackgroundForCellRange('#E0E0E0', 1, 1, 1, 5);
                $table->setFontForCellRange($this->boldFont, 1, 1, 1, 5);
                $table->setFontForCellRange($this->textFont, 2, 1, count($this->project['reducedChecks']) + 1, 5);
                $table->setBorderForCellRange($this->thinBorder, 1, 1, count($this->project['reducedChecks']) + 1, 5);
                $table->setFirstRowAsHeader();

                // set paddings
                for ($row = 1; $row <= count($this->project['reducedChecks']) + 1; $row++) {
                    for ($col = 1; $col <= 5; $col++) {
                        $table->getCell($row, $col)->setCellPaddings($this->cellPadding, $this->cellPadding, $this->cellPadding, $this->cellPadding);

                        if ($row > 1) {
                            $table->getCell($row, $col)->setVerticalAlignment(PHPRtfLite_Table_Cell::VERTICAL_ALIGN_TOP);
                        } else {
                            $table->getCell($row, $col)->setVerticalAlignment(PHPRtfLite_Table_Cell::VERTICAL_ALIGN_CENTER);
                        }
                    }
                }

                $row = 1;

                $table->getCell($row, 1)->writeText(Yii::t('app', 'Target / Reference'));
                $table->getCell($row, 2)->writeText(Yii::t('app', 'Check Name') . ' (' . Yii::t('app', 'Question') . ')');
                $table->getCell($row, 3)->writeText(Yii::t('app', 'Problem'));
                $table->getCell($row, 4)->writeText(Yii::t('app', 'Solution'));
                $table->getCell($row, 5)->writeText(Yii::t('app', 'Rating'));

                $row++;

                $reducedChecks = $this->project['reducedChecks'];
                usort($reducedChecks, array('ReportController', 'sortChecksByRating'));

                foreach ($reducedChecks as $check) {
                    $table->getCell($row, 1)->writeHyperLink(
                        '#check_' . $check['target']['id'] . '_' . $check['id'],
                        $check['target']['host'],
                        $this->textFont
                    );

                    if ($check['target']['description']) {
                        $table->getCell($row, 1)->writeText(' / ', $this->textFont);
                        $table->getCell($row, 1)->writeText($check['target']['description'], new PHPRtfLite_Font($this->fontSize, $this->fontFamily, '#909090'));
                    }

                    $table->getCell($row, 2)->writeHyperLink(
                        '#check_' . $check['target']['id'] . '_' . $check['id'],
                        $check['name'],
                        $this->textFont,
                        $this->noPar
                    );

                    $cell = $table->getCell($row, 2);

                    if ($check['question']) {
                        $cell->writeText("<br>");
                        $this->_renderText($cell, "(" . $check['question'] . ')', false);
                    }

                    $problem = $check["result"];
                    $details = null;

                    $startPos = mb_strpos($problem, "Problem:", 0, "UTF-8");

                    if ($startPos !== false) {
                        $cutPos = mb_strpos($problem, "@cut", 0, "UTF-8");

                        if ($cutPos === false) {
                            $cutPos = mb_strlen($problem, "UTF-8");
                        }

                        $problem = mb_substr($problem, $startPos, $cutPos - $startPos, "UTF-8");
                        $problem = str_replace("Problem: ", "", $problem);

                        $startPos = mb_strpos($problem, "Technical Details:", 0, "UTF-8");

                        if ($startPos !== false) {
                            $details = mb_substr($problem, $startPos, mb_strlen($problem, "UTF-8") - $startPos, "UTF-8");
                            $problem = mb_substr($problem, 0, $startPos);
                        }
                    } else {
                        $problem = Yii::t("app", "N/A");
                    }

                    $cell = $table->getCell($row, 3);

                    if (Utils::isHtml($problem)) {
                        $this->_renderText($cell, $problem, false);
                    } else {
                        $cell->writeText($problem);
                    }

                    if ($details) {
                        $cell->writeText("<br>");

                        if (Utils::isHtml($problem)) {
                            $this->_renderText($cell, $details, false);
                        } else {
                            $cell->writeText($details);
                        }
                    }

                    $cell = $table->getCell($row, 4);
                    $this->_renderText($cell, $check["solution"], false);

                    $image = null;

                    switch ($check['rating']) {
                        case TargetCheck::RATING_HIGH_RISK:
                            $image = $ratingImages[TargetCheck::RATING_HIGH_RISK];
                            break;

                        case TargetCheck::RATING_MED_RISK:
                            $image = $ratingImages[TargetCheck::RATING_MED_RISK];
                            break;

                        case TargetCheck::RATING_LOW_RISK:
                            $image = $ratingImages[TargetCheck::RATING_LOW_RISK];
                            break;

                        case TargetCheck::RATING_NONE:
                            $image = $ratingImages[TargetCheck::RATING_NONE];
                            break;

                        case TargetCheck::RATING_NO_VULNERABILITY:
                            $image = $ratingImages[TargetCheck::RATING_NO_VULNERABILITY];
                            break;

                        case TargetCheck::RATING_INFO:
                            $image = $ratingImages[TargetCheck::RATING_INFO];
                            break;
                    }

                    $table->addImageToCell($row, 5, $image);

                    $row++;
                }
            }

            $sectionNumber++;
            $section->insertPageBreak();
        }

        // detailed report with vulnerabilities
        $this->toc->writeHyperLink(
            '#vulns',
            $sectionNumber . '. ' . Yii::t('app', 'Vulnerabilities') . "\n",
            $this->textFont
        );

        $section->writeBookmark(
            'vulns',
            $sectionNumber . '. ' . Yii::t('app', 'Vulnerabilities'),
            $this->h2Font,
            $this->h3Par
        );

        $subsectionNumber = 1;

        if ($this->project["hasSeparate"]) {
            foreach ($template->sections as $scn) {
                // check if section has checks in it
                $checkCount = 0;

                foreach ($data as $target) {
                    foreach ($target['categories'] as $cat) {
                        if ($cat['id'] != $scn->check_category_id) {
                            continue;
                        }

                        foreach ($cat['controls'] as $ctrl) {
                            foreach ($ctrl['checks'] as $check) {
                                if ($check['separate']) {
                                    $checkCount++;
                                    break;
                                }
                            }

                            if ($checkCount > 0) {
                                break;
                            }
                        }
                    }

                    if ($checkCount > 0) {
                        break;
                    }
                }

                if ($checkCount == 0) {
                    continue;
                }

                $this->toc->writeHyperLink(
                    '#vulns_section_' . $subsectionNumber,
                    '    ' . $sectionNumber . '.' . $subsectionNumber . '. ' . $scn->localizedTitle . "\n",
                    $this->textFont
                );

                $section->writeBookmark(
                    'vulns_section_' . $subsectionNumber,
                    $sectionNumber . '.' . $subsectionNumber . '. ' . $scn->localizedTitle . "\n",
                    $this->h3Font,
                    $this->noPar
                );

                if ($scn->localizedIntro)
                    $this->_renderText($section, $scn->localizedIntro . "<br><br>");

                $this->_generateVulnerabilityList(
                    $data,
                    $section,
                    $sectionNumber . '.' . $subsectionNumber,
                    self::SEPARATE_VULN_LIST,
                    $ratingImages,
                    $model->infoChecksLocation,
                    $scn->check_category_id
                );

                $subsectionNumber++;
            }
        }

        $this->toc->writeHyperLink(
            '#vulns_section_' . $subsectionNumber,
            '    ' . $sectionNumber . '.' . $subsectionNumber . '. ' . Yii::t('app', 'Found Vulnerabilities') . "\n",
            $this->textFont
        );

        $section->writeBookmark(
            'vulns_section_' . $subsectionNumber,
            $sectionNumber . '.' . $subsectionNumber . '. ' . Yii::t('app', 'Found Vulnerabilities') . "\n",
            $this->h3Font,
            $this->noPar
        );

        if ($template->localizedVulnsIntro) {
            $this->_renderText($section, $template->localizedVulnsIntro . "<br><br>");
        }

        $this->_generateVulnerabilityList($data, $section, $sectionNumber . '.' . $subsectionNumber, self::NORMAL_VULN_LIST, $ratingImages, $model->infoChecksLocation);

        $subsectionNumber++;

        if ($this->project["hasInfo"] && $model->infoChecksLocation == ProjectReportForm::INFO_LOCATION_APPENDIX) {
            $this->toc->writeHyperLink(
                '#vulns_section_' . $subsectionNumber,
                '    ' . $sectionNumber . '.' . $subsectionNumber . '. ' . Yii::t('app', 'Additional Data') . "\n",
                $this->textFont
            );

            $section->writeBookmark(
                'vulns_section_' . $subsectionNumber,
                $sectionNumber . '.' . $subsectionNumber . '. ' . Yii::t('app', 'Additional Data') . "\n",
                $this->h3Font,
                $this->noPar
            );

            if ($template->localizedInfoChecksIntro) {
                $this->_renderText($section, $template->localizedInfoChecksIntro . "<br><br>");
            }

            $this->_generateVulnerabilityList($data, $section, $sectionNumber . '.' . $subsectionNumber, self::APPENDIX_VULN_LIST, $ratingImages);
        }

        $section->insertPageBreak();
        $sectionNumber++;

        // appendix
        if (in_array('appendix', $options) && $template->localizedAppendix) {
            $this->toc->writeHyperLink(
                '#appendix',
                $sectionNumber . '. ' . Yii::t('app', 'Appendix') . "\n",
                $this->textFont
            );

            $section->writeBookmark(
                'appendix',
                $sectionNumber . '. ' . Yii::t('app', 'Appendix'),
                $this->h2Font,
                $this->h3Par
            );

            $this->_renderText($section, $template->localizedAppendix, false);

            $section->insertPageBreak();
            $sectionNumber++;
        }

        // attachments
        if (in_array('attachments', $options)) {
            $this->toc->writeHyperLink(
                '#attachments',
                $sectionNumber . '. ' . Yii::t('app', 'Attachments') . "\n",
                $this->textFont
            );

            $section->writeBookmark(
                'attachments',
                $sectionNumber . '. ' . Yii::t('app', 'Attachments'),
                $this->h2Font,
                $this->h3Par
            );

            $section->writeText("", $this->textFont, $this->noPar);

            $table = $section->addTable(PHPRtfLite_Table::ALIGN_LEFT);
            $table->addRows(count($reportAttachments) + 1);
            $table->addColumnsList(array(
                $this->docWidth * 0.25,
                $this->docWidth * 0.25,
                $this->docWidth * 0.25,
                $this->docWidth * 0.25,
            ));

            $table->setBackgroundForCellRange('#E0E0E0', 1, 1, 1, 4);
            $table->setFontForCellRange($this->boldFont, 1, 1, 1, 4);
            $table->setFontForCellRange($this->textFont, 2, 1, count($reportAttachments) + 1, 4);
            $table->setBorderForCellRange($this->thinBorder, 1, 1, count($reportAttachments) + 1, 4);
            $table->setFirstRowAsHeader();

            // set paddings
            for ($row = 1; $row <= count($reportAttachments) + 1; $row++) {
                for ($col = 1; $col <= 4; $col++) {
                    $table->getCell($row, $col)->setCellPaddings($this->cellPadding, $this->cellPadding, $this->cellPadding, $this->cellPadding);

                    if ($row > 1) {
                        $table->getCell($row, $col)->setVerticalAlignment(PHPRtfLite_Table_Cell::VERTICAL_ALIGN_TOP);
                    } else {
                        $table->getCell($row, $col)->setVerticalAlignment(PHPRtfLite_Table_Cell::VERTICAL_ALIGN_CENTER);
                    }
                }
            }

            $row = 1;

            $table->getCell($row, 1)->writeText(Yii::t('app', 'Title'));
            $table->getCell($row, 2)->writeText(Yii::t('app', 'Host'));
            $table->getCell($row, 3)->writeText(Yii::t('app', 'Check'));
            $table->getCell($row, 4)->writeText(Yii::t('app', 'File'));

            $row++;

            foreach ($reportAttachments as $attachment) {
                $table->getCell($row, 1)->writeText($attachment['title']);
                $table->getCell($row, 2)->writeText($attachment['host']);
                $table->getCell($row, 3)->writeText($attachment['check']);
                $table->getCell($row, 4)->writeText($attachment['filename']);
                $row++;
            }
        }

        $hashName = hash('sha256', rand() . time() . $fileName);
        $filePath = Yii::app()->params['tmpPath'] . '/' . $hashName;

        $this->rtf->save($filePath);

        if ($model->fileType == ProjectReportForm::FILE_TYPE_RTF) {
            $reportData = array('name' => $fileName, 'path' => $filePath);
            $this->_generateReportFile($reportData);
        } else {
            if ($model->fileType == ProjectReportForm::FILE_TYPE_ZIP) {
                $reportData = array('name' => $fileName, 'path' => $filePath, 'zipName' => $zipFileName);
                $this->_generateReportFile($reportData, $reportAttachments);
            }
        }

        exit();
    }

    /**
     * Gives report file to user
     * @param $filepath
     * @param null $attachments
     */
    private function _generateReportFile ($data, $attachments = null) {
        if ($attachments !== null) {
            $reportFileName = $data['name'];
            $reportFilePath = $data['path'];
            $fileName = $data['zipName'];
            $hashName = hash('sha256', rand() . time() . $fileName);
            $filePath = Yii::app()->params['tmpPath'] . '/' . $hashName;

            $zip = new ZipArchive();

            if ($zip->open($filePath, ZipArchive::CREATE) !== true) {
                throw new Exception("Unable to create report archive: $fileName");
            }

            FileManager::zipFile($zip, $reportFilePath, $reportFileName);

            $zip->addEmptyDir('attachments');

            foreach ($attachments as $attachment) {
                $hostDir = 'attachments/' . $attachment['host'];

                if (!$zip->locateName($hostDir)) {
                    $zip->addEmptyDir($hostDir);
                }

                FileManager::zipFile($zip, $attachment["path"], $hostDir . '/' . $attachment["filename"]);
            }

            $zip->close();
        } else {
            $fileName = $data['name'];
            $filePath = $data['path'];
        }

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
        FileManager::unlink($filePath);
    }

    private function _generateFileAttachmentsList ($attachments) {
        $text = "<br/>";

        foreach ($attachments as $attachment) {
            $text = $text . sprintf("Host '%s'  -  Check '%s'  -  GTTA File '%s' attached <br/>",
                    $attachment["host"],
                    $attachment["check"],
                    $attachment["filename"]
                );
        }

        return $text;
    }

    private function _getRatingImages ($template) {
        $images = array();

        $high = $template->getRatingImage(TargetCheck::RATING_HIGH_RISK);

        if ($high) {
            $images[TargetCheck::RATING_HIGH_RISK] = Yii::app()->params['reports']['ratingImages']['path'] . "/" . $high->path;
        } else {
            $images[TargetCheck::RATING_HIGH_RISK] = Yii::app()->basePath . '/../images/high.png';
        }

        $med = $template->getRatingImage(TargetCheck::RATING_MED_RISK);

        if ($med) {
            $images[TargetCheck::RATING_MED_RISK] = Yii::app()->params['reports']['ratingImages']['path'] . "/" . $med->path;
        } else {
            $images[TargetCheck::RATING_MED_RISK] = Yii::app()->basePath . '/../images/med.png';
        }

        $low = $template->getRatingImage(TargetCheck::RATING_LOW_RISK);

        if ($low) {
            $images[TargetCheck::RATING_LOW_RISK] = Yii::app()->params['reports']['ratingImages']['path'] . "/" . $low->path;
        } else {
            $images[TargetCheck::RATING_LOW_RISK] = Yii::app()->basePath . '/../images/low.png';
        }

        $info = $template->getRatingImage(TargetCheck::RATING_INFO);

        if ($info) {
            $images[TargetCheck::RATING_INFO] = Yii::app()->params['reports']['ratingImages']['path'] . "/" . $info->path;
        } else {
            $images[TargetCheck::RATING_INFO] = Yii::app()->basePath . '/../images/info.png';
        }

        $none = $template->getRatingImage(TargetCheck::RATING_NONE);

        if ($none) {
            $images[TargetCheck::RATING_NONE] = Yii::app()->params['reports']['ratingImages']['path'] . "/" . $none->path;
        } else {
            $images[TargetCheck::RATING_NONE] = Yii::app()->basePath . '/../images/none.png';
        }

        $noVuln = $template->getRatingImage(TargetCheck::RATING_NO_VULNERABILITY);

        if ($noVuln) {
            $images[TargetCheck::RATING_NO_VULNERABILITY] = Yii::app()->params['reports']['ratingImages']['path'] . "/" . $noVuln->path;
        } else {
            $images[TargetCheck::RATING_NO_VULNERABILITY] = Yii::app()->basePath . '/../images/no_vuln.png';
        }

        return $images;

    }

    /**
     * Show project report form.
     */
    public function actionProject()
    {
        $model = new ProjectReportForm();

        if (isset($_POST['ProjectReportForm'])) {
            $model->attributes = $_POST['ProjectReportForm'];

            if ($model->validate()) {
                $this->_generateProjectReport($model);
            } else {
                Yii::app()->user->setFlash('error', Yii::t('app', 'Please fix the errors below.'));
            }
        }

        $criteria = new CDbCriteria();
        $criteria->order = 't.name ASC';

        if (!User::checkRole(User::ROLE_ADMIN)) {
            $projects = ProjectUser::model()->with('project')->findAllByAttributes(array(
                'user_id' => Yii::app()->user->id
            ));

            $clientIds = array();

            foreach ($projects as $project) {
                if (!in_array($project->project->client_id, $clientIds)) {
                    $clientIds[] = $project->project->client_id;
                }
            }

            $criteria->addInCondition('id', $clientIds);
        }

        $clients = Client::model()->findAll($criteria);

        $language = Language::model()->findByAttributes(array(
            'code' => Yii::app()->language
        ));

        if ($language) {
            $language = $language->id;
        }

        $criteria = new CDbCriteria();
        $criteria->order = 'COALESCE(l10n.name, t.name) ASC';
        $criteria->together = true;

        $templates = ReportTemplate::model()->with(array(
            'l10n' => array(
                'joinType' => 'LEFT JOIN',
                'on'       => 'language_id = :language_id',
                'params'   => array( 'language_id' => $language )
            )
        ))->findAll($criteria);

        $riskTemplates = RiskTemplate::model()->with(array(
            'l10n' => array(
                'joinType' => 'LEFT JOIN',
                'on'       => 'language_id = :language_id',
                'params'   => array( 'language_id' => $language )
            )
        ))->findAllByAttributes(
            array(),
            array( 'order' => 'COALESCE(l10n.name, t.name) ASC' )
        );

        $this->breadcrumbs[] = array(Yii::t('app', 'Project Report'), '');

        // display the report generation form
        $this->pageTitle = Yii::t('app', 'Project Report');
		$this->render('project', array(
            'model'         => $model,
            'clients'       => $clients,
            'templates'     => $templates,
            'riskTemplates' => $riskTemplates,
            'infoChecksLocation' => array(
                ProjectReportForm::INFO_LOCATION_TARGET   => Yii::t('app', 'in the main list'),
                ProjectReportForm::INFO_LOCATION_TABLE    => Yii::t('app', 'in a separate table'),
                ProjectReportForm::INFO_LOCATION_APPENDIX => Yii::t('app', 'in the appendix'),
            ),
        ));
    }

    /**
     * Comparison report
     * @param $project1
     * @param $project2
     * @return array
     * @throws CHttpException
     */
    private function _comparisonReport($project1, $project2) {
        $targets1 = Target::model()->findAllByAttributes(
            array('project_id' => $project1->id),
            array('order' => 't.host ASC')
        );

        $targets2 = Target::model()->findAllByAttributes(
            array('project_id' => $project2->id),
            array('order' => 't.host ASC')
        );

        // find corresponding targets
        $data = array();

        foreach ($targets1 as $target1) {
            foreach ($targets2 as $target2) {
                if ($target2->host == $target1->host) {
                    $data[] = array(
                        $target1,
                        $target2
                    );

                    break;
                }
            }
        }

        if (!$data) {
            throw new CHttpException(404, Yii::t('app', 'No targets to compare.'));
        }

        $targetsData = array();

        foreach ($data as $targets) {
            $targetData = array(
                'host' => $targets[0]->host,
                'ratings' => array()
            );

            foreach ($targets as $target) {
                $rating = 0;
                $checkCount = 0;

                // get all categories
                $categories = TargetCheckCategory::model()->findAllByAttributes(array(
                    'target_id' => $target->id
                ));

                // get all references (they are the same across all target categories)
                $referenceIds = array();

                $references = TargetReference::model()->findAllByAttributes(array(
                    'target_id' => $target->id
                ));

                foreach ($references as $reference) {
                    $referenceIds[] = $reference->reference_id;
                }

                $checksData = array();

                foreach ($categories as $category) {
                    $controls = CheckControl::model()->with(array(
                        "customChecks" => array(
                            "alias" => "custom",
                            "on" => "custom.target_id = :target_id",
                            "params" => array("target_id" => $target->id)
                        ),
                    ))->findAllByAttributes(array(
                        "check_category_id" => $category->check_category_id
                    ));

                    $controlIds = array();

                    foreach ($controls as $control) {
                        $controlIds[] = $control->id;

                        foreach ($control->customChecks as $custom) {
                            $checksData[] = array("rating" => $custom->rating);
                        }
                    }

                    $criteria = new CDbCriteria();
                    $criteria->addInCondition("reference_id", $referenceIds);
                    $criteria->addInCondition("check_control_id", $controlIds);

                    if ($this->_system->demo) {
                        $criteria->addColumnCondition(array(
                            "t.demo" => true
                        ));
                    }

                    if (!$category->advanced) {
                        $criteria->addCondition("advanced = FALSE");
                    }

                    $checks = Check::model()->with(array(
                        "targetChecks" => array(
                            "alias" => "tcs",
                            "joinType" => "INNER JOIN",
                            "on" => "tcs.target_id = :target_id AND tcs.status = :status AND tcs.rating != :hidden",
                            "params" => array(
                                "target_id" => $target->id,
                                "status" => TargetCheck::STATUS_FINISHED,
                                "hidden" => TargetCheck::RATING_HIDDEN,
                            ),
                        ),
                    ))->findAll($criteria);

                    if (!$checks) {
                        continue;
                    }

                    foreach ($checks as $check) {
                        foreach ($check->targetChecks as $tc) {
                            $checksData[] = array(
                                "rating" => $tc->rating
                            );
                        }
                    }
                }

                $targetData["ratings"][] = $this->_getChecksRating($checksData);
            }

            $targetsData[] = $targetData;
        }

        return $targetsData;
    }

    /**
     * Get GT project targets
     * @param $projectId
     * @return array
     */
    private function _getGtTargets($projectId) {
        $targets = array();

        $criteria = new CDbCriteria();
        $criteria->addColumnCondition(array('project_id' => $projectId));
        $criteria->order = 'target ASC';

        $checks = ProjectGtCheck::model()->findAll($criteria);

        foreach ($checks as $check) {
            if (!$check->target) {
                continue;
            }

            if (!in_array($check->target, $targets)) {
                $targets[] = $check->target;
            }
        }

        return $targets;
    }

    /**
     * Comparison report for GT projects
     * @param $project1
     * @param $project2
     * @return array
     * @throws CHttpException
     */
    private function _gtComparisonReport($project1, $project2) {
        $targets1 = $this->_getGtTargets($project1->id);
        $targets2 = $this->_getGtTargets($project2->id);

        // find corresponding targets
        $data = array();

        foreach ($targets1 as $target1) {
            foreach ($targets2 as $target2) {
                if ($target2 == $target1) {
                    $data[] = array(
                        array(
                            "target" => $target1,
                            "project" => $project1
                        ),
                        array(
                            "target" => $target2,
                            "project" => $project2
                        ),
                    );

                    break;
                }
            }
        }

        if (!$data) {
            throw new CHttpException(404, Yii::t('app', 'No targets to compare.'));
        }

        $targetsData = array();

        foreach ($data as $targets) {
            $targetData = array(
                'host' => $targets[0]["target"],
                'ratings' => array()
            );

            foreach ($targets as $target) {
                $rating = 0;
                $checkCount = 0;

                // get all checks
                $criteria = new CDBCriteria();
                $criteria->addCondition("project_id = :project AND target = :target AND status = :status AND rating != :hidden");
                $criteria->params = array(
                    "project" => $target["project"]->id,
                    "target" => $target["target"],
                    "status" => ProjectGtCheck::STATUS_FINISHED,
                    "hidden" => ProjectGtCheck::RATING_HIDDEN,
                );

                $checks = ProjectGtCheck::model()->with(array(
                    "check" => array(
                        "with" => array(
                            "check" => array(
                                "alias" => "innerCheck",
                            )
                        )
                    )
                ))->findAll($criteria);

                if (!$checks) {
                    continue;
                }

                $checksData = array();

                foreach ($checks as $check) {
                    $innerCheck = $check->check->check;

                    if ($this->_system->demo && !$innerCheck->demo) {
                        continue;
                    }

                    $checksData[] = array(
                        "rating" => $check->rating
                    );
                }

                $targetData['ratings'][] = $this->_getChecksRating($checksData);
            }

            $targetsData[] = $targetData;
        }

        return $targetsData;
    }

    /**
     * Generate comparison report.
     */
    private function _generateComparisonReport($model) {
        $clientId = $model->clientId;
        $projectId1 = $model->projectId1;
        $projectId2 = $model->projectId2;

        $project1 = Project::model()->findByAttributes(array(
            'client_id' => $clientId,
            'id'        => $projectId1
        ));

        if ($project1 === null) {
            Yii::app()->user->setFlash('error', Yii::t('app', 'First project not found.'));
            return;
        }

        if (!$project1->checkPermission()) {
            Yii::app()->user->setFlash('error', Yii::t('app', 'Access denied.'));
            return;
        }

        $project2 = Project::model()->findByAttributes(array(
            'client_id' => $clientId,
            'id'        => $projectId2
        ));

        if ($project2 === null) {
            Yii::app()->user->setFlash('error', Yii::t('app', 'Second project not found.'));
            return;
        }

        if (!$project2->checkPermission()) {
            Yii::app()->user->setFlash('error', Yii::t('app', 'Access denied.'));
            return;
        }

        if ($project1->guided_test != $project2->guided_test) {
            Yii::app()->user->setFlash('error', Yii::t('app', 'Guided Test projects can be compared only to Guided Test projects.'));
            return;
        }

        if ($project1->guided_test) {
            $targetsData = $this->_gtComparisonReport($project1, $project2);
        } else {
            $targetsData = $this->_comparisonReport($project1, $project2);
        }

        $this->_rtfSetup($model);
        $section = $this->rtf->addSection();

        // footer
        $footer = $section->addFooter();
        $footer->writeText(Yii::t('app', 'Projects Comparison') . ', ', $this->textFont, $this->noPar);
        $footer->writePlainRtfCode(
            '\fs' . ($this->textFont->getSize() * 2) . ' \f' . $this->textFont->getFontIndex() . ' ' .
             Yii::t('app', 'page {page} of {numPages}',
            array(
                '{page}' => '{\field{\*\fldinst {PAGE}}{\fldrslt {1}}}',
                '{numPages}' => '{\field{\*\fldinst {NUMPAGES}}{\fldrslt {1}}}'
            )
        ));

        // title
        $section->writeText(Yii::t('app', 'Projects Comparison'), $this->h1Font, $this->titlePar);

        // detailed summary
        $section->writeText(Yii::t('app', 'Target Comparison') . '<br>', $this->h3Font, $this->noPar);
        $table = $section->addTable(PHPRtfLite_Table::ALIGN_LEFT);

        $table->addRows(count($targetsData) + 1);
        $table->addColumnsList(array( $this->docWidth * 0.33, $this->docWidth * 0.33, $this->docWidth * 0.34 ));
        $table->setFontForCellRange($this->boldFont, 1, 1, 1, 3);
        $table->setBackgroundForCellRange('#E0E0E0', 1, 1, 1, 3);
        $table->setFontForCellRange($this->textFont, 2, 1, count($targetsData) + 1, 3);
        $table->setBorderForCellRange($this->thinBorder, 1, 1, count($targetsData) + 1, 3);
        $table->setFirstRowAsHeader();

        // set paddings
        for ($row = 1; $row <= count($targetsData) + 1; $row++) {
            for ($col = 1; $col <= 3; $col++) {
                $table->getCell($row, $col)->setCellPaddings(
                    $model->cellPadding,
                    $model->cellPadding,
                    $model->cellPadding,
                    $model->cellPadding
                );

                $table->getCell($row, $col)->setVerticalAlignment(PHPRtfLite_Table_Cell::VERTICAL_ALIGN_CENTER);
            }
        }

        $table->writeToCell(1, 1, Yii::t('app', 'Target'));
        $table->writeToCell(1, 2, $project1->name . ' (' . $project1->year . ')');
        $table->writeToCell(1, 3, $project2->name . ' (' . $project2->year . ')');

        $row = 2;
        $system = System::model()->findByPk(1);

        foreach ($targetsData as $target) {
            $table->writeToCell($row, 1, $target['host']);
            $table->addImageToCell($row, 2, $this->_generateRatingImage($target['ratings'][0], $system), null, $this->docWidth * 0.30);
            $table->addImageToCell($row, 3, $this->_generateRatingImage($target['ratings'][1], $system), null, $this->docWidth * 0.30);

            $table->getCell($row, 2)->setTextAlignment(PHPRtfLite_Table_Cell::TEXT_ALIGN_CENTER);
            $table->getCell($row, 3)->setTextAlignment(PHPRtfLite_Table_Cell::TEXT_ALIGN_CENTER);

            $row++;
        }

        $fileName = Yii::t('app', 'Projects Comparison') . '.rtf';
        $hashName = hash('sha256', rand() . time() . $fileName);
        $filePath = Yii::app()->params['tmpPath'] . '/' . $hashName;

        $this->rtf->save($filePath);

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
    public function actionComparison() {
        $model = new ProjectComparisonForm();

        if (isset($_POST['ProjectComparisonForm'])) {
            $model->attributes = $_POST['ProjectComparisonForm'];

            if ($model->validate()) {
                $this->_generateComparisonReport($model);
            } else {
                Yii::app()->user->setFlash('error', Yii::t('app', 'Please fix the errors below.'));
            }
        }

        $criteria = new CDbCriteria();
        $criteria->order = 't.name ASC';

        if (!User::checkRole(User::ROLE_ADMIN)) {
            $projects = ProjectUser::model()->with('project')->findAllByAttributes(array(
                'user_id' => Yii::app()->user->id
            ));

            $clientIds = array();

            foreach ($projects as $project) {
                if (!in_array($project->project->client_id, $clientIds)) {
                    $clientIds[] = $project->project->client_id;
                }
            }

            $criteria->addInCondition('id', $clientIds);
        }

        $clients = Client::model()->findAll($criteria);
        $this->breadcrumbs[] = array(Yii::t('app', 'Projects Comparison'), '');

        // display the report generation form
        $this->pageTitle = Yii::t('app', 'Projects Comparison');
		$this->render('comparison', array(
            'model' => $model,
            'clients' => $clients
        ));
    }

    /**
     * Fulfillment degree image.
     */
    private function _generateFulfillmentImage($degree)
    {
        $scale = imagecreatefrompng(Yii::app()->basePath . '/../images/fulfillment-stripe.png');
        imagealphablending($scale, false);
        imagesavealpha($scale, true);

        $image = imagecreatetruecolor(301, 30);

        $lineCoord = $degree * 3;
        $white     = imagecolorallocate($image, 0xFF, 0xFF, 0xFF);
        $color     = imagecolorallocate($image, 0x3A, 0x87, 0xAD);

        imagefilledrectangle($image, 0, 0, 301, 30, $white);
        imagefilledrectangle($image, 0, 6, $lineCoord, 24, $color);
        imagecopyresampled($image, $scale, 0, 0, 0, 0, 301, 30, 301, 30);

        $hashName = hash('sha256', rand() . time() . rand());
        $filePath = Yii::app()->params['tmpPath'] . '/' . $hashName . '.png';

        imagepng($image, $filePath, 0);
        imagedestroy($image);

        return $filePath;
    }

    /**
     * Sort controls.
     */
    public static function sortControls($a, $b) {
        return $a['degree'] > $b['degree'];
    }

    /**
     * Fulfillment report
     * @param $fullReport
     * @param $findWeakest
     * @param $model
     * @param $language
     * @return array
     */
    private function _fulfillmentReport($fullReport, $findWeakest, $model, $language) {
        $data = array();

        if (!$fullReport && !$findWeakest) {
            $criteria = new CDbCriteria();
            $criteria->addInCondition('id', $model->targetIds);
            $criteria->addColumnCondition(array('project_id' => $model->projectId));
            $criteria->order = 't.host ASC';
            $targets = Target::model()->findAll($criteria);
        } else {
            $targets = $this->project['targets'];
        }

        foreach ($targets as $target) {
            $targetData = array(
                'id'          => $target->id,
                'host'        => $target->host,
                'description' => $target->description,
                'controls'    => array(),
            );

            // get all references (they are the same across all target categories)
            $referenceIds = array();

            $references = TargetReference::model()->findAllByAttributes(array(
                'target_id' => $target->id
            ));

            foreach ($references as $reference) {
                $referenceIds[] = $reference->reference_id;
            }

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
            ))->findAllByAttributes(
                array( 'target_id' => $target->id  ),
                array( 'order'     => 'COALESCE(l10n.name, category.name) ASC' )
            );

            foreach ($categories as $category) {
                // get all controls
                $controls = CheckControl::model()->with(array(
                    'l10n' => array(
                        'joinType' => 'LEFT JOIN',
                        'on'       => 'language_id = :language_id',
                        'params'   => array( 'language_id' => $language )
                    )
                ))->findAllByAttributes(
                    array( 'check_category_id' => $category->check_category_id ),
                    array( 'order'             => 't.sort_order ASC' )
                );

                if (!$controls) {
                    continue;
                }

                foreach ($controls as $control) {
                    $controlData = array(
                        'name'   => $category->category->localizedName . ' / ' . $control->localizedName,
                        'degree' => 0.0,
                    );

                    $criteria = new CDbCriteria();

                    $criteria->addInCondition('t.reference_id', $referenceIds);
                    $criteria->addColumnCondition(array(
                        't.check_control_id' => $control->id
                    ));

                    if ($this->_system->demo) {
                        $criteria->addColumnCondition(array(
                            "t.demo" => true
                        ));
                    }

                    if (!$category->advanced) {
                        $criteria->addCondition('t.advanced = FALSE');
                    }

                    $checks = Check::model()->with(array(
                        "targetChecks" => array(
                            "alias" => "tcs",
                            "joinType" => "INNER JOIN",
                            "on" => "tcs.target_id = :target_id AND tcs.status = :status",
                            "params" => array(
                                "target_id" => $target->id,
                                "status" => TargetCheck::STATUS_FINISHED,
                            ),
                        ),
                    ))->findAll($criteria);

                    if (!$checks) {
                        continue;
                    }

                    foreach ($checks as $check) {
                        foreach ($check->targetChecks as $tc) {
                            switch ($tc->rating) {
                                case TargetCheck::RATING_HIDDEN:
                                case TargetCheck::RATING_INFO:
                                    $controlData['degree'] += 0;
                                    break;

                                case TargetCheck::RATING_LOW_RISK:
                                    $controlData['degree'] += 1;
                                    break;

                                case TargetCheck::RATING_MED_RISK:
                                    $controlData['degree'] += 2;
                                    break;

                                case TargetCheck::RATING_HIGH_RISK:
                                    $controlData['degree'] += 3;
                                    break;
                            }
                        }
                    }

                    $maxDegree = count($checks) * 3;
                    $controlData['degree'] = round(100 - $controlData['degree'] / $maxDegree * 100);
                    $targetData['controls'][] = $controlData;
                }
            }

            $data[] = $targetData;
        }

        return $data;
    }

    /**
     * Fulfillment report for GT
     * @param $fullReport
     * @param $findWeakest
     * @param $model
     * @param $language
     * @return array
     */
    private function _gtFulfillmentReport($fullReport, $findWeakest, $model, $language) {
        $data = array();

        $projectId = ($fullReport || $findWeakest) ? $this->project['project']->id : $model->projectId;
        $targets = $this->_getGtTargets($projectId);
        $targetId = 1;

        foreach ($targets as $target) {
            $targetData = array(
                'id' => $targetId,
                'host' => $target,
                'description' => "",
                'controls' => array(),
            );

            // get all checks
            $criteria = new CDBCriteria();
            $criteria->addColumnCondition(array(
                "project_id" => $projectId,
                "target" => $target,
                "t.status" => ProjectGtCheck::STATUS_FINISHED,
            ));

            $checks = ProjectGtCheck::model()->with(array(
                'check' => array(
                    'with' => array(
                        'check' => array(
                            'alias' => 'innerCheck',
                            'with' => array(
                                'l10n' => array(
                                    'joinType' => 'LEFT JOIN',
                                    'on' => 'l10n.language_id = :language_id',
                                    'params' => array('language_id' => $language)
                                ),
                                'control' => array(
                                    'with' => array(
                                        'l10n' => array(
                                            'alias' => 'c_l10n',
                                            'joinType' => 'LEFT JOIN',
                                            'on' => 'c_l10n.language_id = :language_id',
                                            'params' => array('language_id' => $language)
                                        ),
                                        'category' => array(
                                            'with' => array(
                                                'l10n' => array(
                                                    'alias' => 'ca_l10n',
                                                    'joinType' => 'LEFT JOIN',
                                                    'on' => 'ca_l10n.language_id = :language_id',
                                                    'params' => array('language_id' => $language)
                                                ),
                                            ),
                                        ),
                                    ),
                                ),
                            ),
                        ),
                    ),
                ),
            ))->findAll($criteria);

            $controls = array();

            foreach ($checks as $check) {
                $innerCheck = $check->check->check;

                if ($this->_system->demo && !$innerCheck->demo) {
                    continue;
                }

                if (!array_key_exists($innerCheck->check_control_id, $controls)) {
                    $controls[$innerCheck->check_control_id] = array(
                        "name" => $innerCheck->control->category->localizedName . ' / ' . $innerCheck->control->localizedName,
                        "degree" => 0.0,
                        "count" => 0,
                    );
                }

                switch ($check->rating) {
                    case ProjectGtCheck::RATING_HIDDEN:
                    case ProjectGtCheck::RATING_INFO:
                        $controls[$innerCheck->check_control_id]['degree'] += 0;
                        break;

                    case ProjectGtCheck::RATING_LOW_RISK:
                        $controls[$innerCheck->check_control_id]['degree'] += 1;
                        break;

                    case ProjectGtCheck::RATING_MED_RISK:
                        $controls[$innerCheck->check_control_id]['degree'] += 2;
                        break;

                    case ProjectGtCheck::RATING_HIGH_RISK:
                        $controls[$innerCheck->check_control_id]['degree'] += 3;
                        break;
                }

                $controls[$innerCheck->check_control_id]["count"]++;
            }

            foreach ($controls as $control) {
                $maxDegree = $control["count"] * 3;
                $control["degree"] = round(100 - $control['degree'] / $maxDegree * 100);
                $targetData["controls"][] = $control;
            }

            $data[] = $targetData;
            $targetId++;
        }

        return $data;
    }

    /**
     * Generate a Degree of Fulfillment report
     */
    private function _generateFulfillmentDegreeReport($model = null, $findWeakest = false, &$section = null, $sectionNumber = null) {
        $fullReport = true;

        if (!$section) {
            $fullReport = false;
        }

        $language = Language::model()->findByAttributes(array(
            'code' => Yii::app()->language
        ));

        if ($language) {
            $language = $language->id;
        }

        if (!$fullReport && !$findWeakest) {
            $project = Project::model()->findByAttributes(array(
                'client_id' => $model->clientId,
                'id' => $model->projectId
            ));

            if ($project === null) {
                Yii::app()->user->setFlash('error', Yii::t('app', 'Project not found.'));
                return;
            }

            if (!$project->checkPermission()) {
                Yii::app()->user->setFlash('error', Yii::t('app', 'Access denied.'));
                return;
            }

            if (!$project->guided_test && (!$model->targetIds || !count($model->targetIds))) {
                Yii::app()->user->setFlash('error', Yii::t('app', 'Please select at least 1 target.'));
                return;
            }
        } else {
            $project = $this->project['project'];
        }

        if (!$fullReport && !$findWeakest) {
            $this->_rtfSetup($model);
            $section = $this->rtf->addSection();

            // footer
            $footer = $section->addFooter();
            $footer->writeText(Yii::t('app', 'Degree of Fulfillment') . ': ' . $project->name . ', ', $this->textFont, $this->noPar);
            $footer->writePlainRtfCode(
                '\fs' . ($this->textFont->getSize() * 2) . ' \f' . $this->textFont->getFontIndex() . ' ' .
                 Yii::t('app', 'page {page} of {numPages}',
                array(
                    '{page}'     => '{\field{\*\fldinst {PAGE}}{\fldrslt {1}}}',
                    '{numPages}' => '{\field{\*\fldinst {NUMPAGES}}{\fldrslt {1}}}'
                )
            ));

            // title
            $section->writeText(Yii::t('app', 'Degree of Fulfillment') . ': ' . $project->name, $this->h1Font, $this->titlePar);
            $section->writeText("\n\n");
        }

        if ($project->guided_test) {
            $data = $this->_gtFulfillmentReport($fullReport, $findWeakest, $model, $language);
        } else {
            $data = $this->_fulfillmentReport($fullReport, $findWeakest, $model, $language);
        }

        $targetNumber = 1;

        if ($findWeakest) {
            $this->project['weakestControls'] = array();
        }

        foreach ($data as $target) {
            // dry run - just fill in the data
            if ($findWeakest) {
                $degree = 100.0;
                $weakest = null;

                foreach ($target['controls'] as $control) {
                    if ($control['degree'] < $degree) {
                        $weakest = $control;
                        $degree = $control['degree'];
                    }
                }

                $this->project['weakestControls'][$target['id']] = $weakest;

                continue;
            }

            if ($fullReport) {
                $this->toc->writeHyperLink(
                    '#degree_' . $targetNumber,
                    '        ' . $sectionNumber . '.' . $targetNumber . '. ' . $target['host'],
                    $this->textFont
                );

                $section->writeBookmark(
                    'degree_' . $targetNumber,
                    $sectionNumber . '.' . $targetNumber . '. ' . $target['host'],
                    $this->boldFont
                );

                if ($target['description']) {
                    $font = new PHPRtfLite_Font($this->fontSize, $this->fontFamily, '#909090');

                    $this->toc->writeText(' / ', $this->textFont);
                    $this->toc->writeHyperLink(
                        '#degree_' . $targetNumber,
                        $target['description'],
                        $font
                    );
                    $this->toc->writeText("\n");

                    $section->writeText(' / ', $this->textFont);
                    $section->writeText($target['description'], $font);
                } else {
                    $this->toc->writeText("\n");
                }

                $targetNumber++;
            } else {
                $section->writeText($target['host'], $this->h3Font);

                if ($target['description']) {
                    $section->writeText(' / ', $this->h3Font);
                    $section->writeText($target['description'], new PHPRtfLite_Font($this->h3Font->getSize(), $this->fontFamily, '#909090'));
                }
            }

            $section->writeText("\n");

            if (!count($target['controls'])) {
                $section->writeText("\n", $this->textFont);
                $section->writeText(Yii::t('app', 'No checks.') . "\n\n", $this->textFont);
                continue;
            }

            $table = $section->addTable(PHPRtfLite_Table::ALIGN_LEFT);
            $table->addColumnsList(array( $this->docWidth * 0.28, $this->docWidth * 0.56, $this->docWidth * 0.16 ));

            $row = 1;

            $table->addRow();
            $table->mergeCellRange(1, 2, 1, 3);

            $table->getCell($row, 1)->setCellPaddings($this->cellPadding, $this->cellPadding, $this->cellPadding, $this->cellPadding);
            $table->getCell($row, 1)->setVerticalAlignment(PHPRtfLite_Table_Cell::VERTICAL_ALIGN_CENTER);

            $table->getCell($row, 2)->setCellPaddings($this->cellPadding, $this->cellPadding, $this->cellPadding, $this->cellPadding);
            $table->getCell($row, 2)->setVerticalAlignment(PHPRtfLite_Table_Cell::VERTICAL_ALIGN_CENTER);

            $table->setFontForCellRange($this->boldFont, 1, 1, 1, 3);
            $table->setBackgroundForCellRange('#E0E0E0', 1, 1, 1, 3);
            $table->setBorderForCellRange($this->thinBorder, 1, 1, 1, 3);
            $table->setFirstRowAsHeader();

            $table->writeToCell($row, 1, Yii::t('app', 'Control'));
            $table->writeToCell($row, 2, Yii::t('app', 'Degree of Fulfillment'));

            $row++;

            usort($target['controls'], array('ReportController', 'sortControls'));

            foreach ($target['controls'] as $control) {
                $table->addRow();
                $table->getCell($row, 1)->setCellPaddings($this->cellPadding, $this->cellPadding, $this->cellPadding, $this->cellPadding);
                $table->getCell($row, 1)->setVerticalAlignment(PHPRtfLite_Table_Cell::VERTICAL_ALIGN_TOP);
                $table->getCell($row, 1)->setBorder($this->thinBorder);

                $table->getCell($row, 2)->setCellPaddings($this->cellPadding, $this->cellPadding, $this->cellPadding, $this->cellPadding);
                $table->getCell($row, 2)->setBorder($this->thinBorder);
                $table->getCell($row, 2)->setTextAlignment(PHPRtfLite_Table_Cell::TEXT_ALIGN_CENTER);

                $table->getCell($row, 3)->setCellPaddings($this->cellPadding, $this->cellPadding, $this->cellPadding, $this->cellPadding);
                $table->getCell($row, 3)->setBorder($this->thinBorder);

                $table->writeToCell($row, 1, $control['name'], $this->textFont);
                $table->addImageToCell($row, 2, $this->_generateFulfillmentImage($control['degree']), null, $this->docWidth * 0.50);
                $table->writeToCell($row, 3, $control['degree'] . '%');

                $row++;
            }

            $table->setFontForCellRange($this->textFont, 1, 1, count($target['controls']) + 1, 2);
        }

        if (!$fullReport && !$findWeakest) {
            $fileName = Yii::t('app', 'Degree of Fulfillment') . ' - ' . $project->name . ' (' . $project->year . ').rtf';
            $hashName = hash('sha256', rand() . time() . $fileName);
            $filePath = Yii::app()->params['tmpPath'] . '/' . $hashName;

            $this->rtf->save($filePath);

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
    }

    /**
     * Show degree of fulfillment report form.
     */
    public function actionFulfillment() {
        $model = new FulfillmentDegreeForm();

        if (isset($_POST['FulfillmentDegreeForm'])) {
            $model->attributes = $_POST['FulfillmentDegreeForm'];

            if ($model->validate()) {
                $this->_generateFulfillmentDegreeReport($model);
            } else {
                Yii::app()->user->setFlash('error', Yii::t('app', 'Please fix the errors below.'));
            }
        }

        $criteria = new CDbCriteria();
        $criteria->order = 't.name ASC';

        if (!User::checkRole(User::ROLE_ADMIN)) {
            $projects = ProjectUser::model()->with('project')->findAllByAttributes(array(
                'user_id' => Yii::app()->user->id
            ));

            $clientIds = array();

            foreach ($projects as $project) {
                if (!in_array($project->project->client_id, $clientIds)) {
                    $clientIds[] = $project->project->client_id;
                }
            }

            $criteria->addInCondition('id', $clientIds);
        }

        $clients = Client::model()->findAll($criteria);

        $this->breadcrumbs[] = array(Yii::t('app', 'Degree of Fulfillment'), '');

        // display the report generation form
        $this->pageTitle = Yii::t('app', 'Degree of Fulfillment');
		$this->render('fulfillment', array(
            'model'   => $model,
            'clients' => $clients
        ));
    }

    /**
     * Risk matrix report
     * @param $fullReport
     * @param $model
     * @param $risks
     * @param $language
     * @return array
     */
    private function _riskMatrixReport($fullReport, $model, $risks, $language) {
        $data = array();

        if (!$fullReport) {
            $criteria = new CDbCriteria();
            $criteria->addInCondition('id', $model->targetIds);
            $criteria->addColumnCondition(array('project_id' => $model->projectId));
            $criteria->order = 't.host ASC';
            $targets = Target::model()->findAll($criteria);
        } else {
            $targets = $this->project['targets'];
        }

        foreach ($targets as $target) {
            $mtrx = array();
            $referenceIds = array();

            $references = TargetReference::model()->findAllByAttributes(array(
                'target_id' => $target->id
            ));

            foreach ($references as $reference) {
                $referenceIds[] = $reference->reference_id;
            }

            $categories = TargetCheckCategory::model()->findAllByAttributes(
                array('target_id' => $target->id )
            );

            foreach ($categories as $category) {
                $controlIds = array();

                $controls = CheckControl::model()->findAllByAttributes(array(
                    'check_category_id' => $category->check_category_id
                ));

                foreach ($controls as $control) {
                    $controlIds[] = $control->id;
                }

                $criteria = new CDbCriteria();
                $criteria->order = 't.sort_order ASC';
                $criteria->addInCondition('t.reference_id', $referenceIds);
                $criteria->addInCondition('t.check_control_id', $controlIds);
                $criteria->together = true;

                if (!$category->advanced) {
                    $criteria->addCondition('t.advanced = FALSE');
                }

                $checks = Check::model()->with(array(
                    'l10n' => array(
                        'joinType' => 'LEFT JOIN',
                        'on' => 'l10n.language_id = :language_id',
                        'params' => array( 'language_id' => $language )
                    ),
                    'targetChecks' => array(
                        'alias' => 'tcs',
                        'joinType' => 'INNER JOIN',
                        'on' => 'tcs.target_id = :target_id AND tcs.status = :status AND (tcs.rating = :high OR tcs.rating = :med)',
                        'params' => array(
                            'target_id' => $target->id,
                            'status' => TargetCheck::STATUS_FINISHED,
                            'high' => TargetCheck::RATING_HIGH_RISK,
                            'med' => TargetCheck::RATING_MED_RISK,
                        ),
                    )
                ))->findAll($criteria);

                foreach ($checks as $check) {
                    if (!isset($model->matrix[$target->id][$check->id])) {
                        continue;
                    }

                    $ctr = 0;

                    foreach ($risks as $riskId => $risk) {
                        $ctr++;

                        if (!isset($model->matrix[$target->id][$check->id][$risk->id])) {
                            continue;
                        }

                        $riskName = 'R' . $ctr;

                        $damage = $model->matrix[$target->id][$check->id][$risk->id]['damage'] - 1;
                        $likelihood = $model->matrix[$target->id][$check->id][$risk->id]['likelihood'] - 1;

                        if (!isset($mtrx[$damage])) {
                            $mtrx[$damage] = array();
                        }

                        if (!isset($mtrx[$damage][$likelihood])) {
                            $mtrx[$damage][$likelihood] = array();
                        }

                        if (!in_array($riskName, $mtrx[$damage][$likelihood])) {
                            $mtrx[$damage][$likelihood][] = $riskName;
                        }
                    }
                }
            }

            $data[] = array(
                'host' => $target->host,
                'description' => $target->description,
                'matrix' => $mtrx
            );
        }

        return $data;
    }

    /**
     * Risk matrix report for GT projects
     * @param $fullReport
     * @param $model
     * @param $risks
     * @return array
     */
    private function _gtRiskMatrixReport($fullReport, $model, $risks) {
        $data = array();

        $projectId = $fullReport ? $this->project['project']->id : $model->projectId;
        $targets = $this->_getGtTargets($projectId);
        $targetId = 1;

        foreach ($targets as $target) {
            $mtrx = array();

            $criteria = new CDbCriteria();
            $criteria->addColumnCondition(array(
                "project_id" => $projectId,
                "target" => $target,
                "status" => ProjectGtCheck::STATUS_FINISHED,
            ));

            $criteria->addInCondition("rating", array(
                ProjectGtCheck::RATING_HIGH_RISK,
                ProjectGtCheck::RATING_MED_RISK,
            ));

            $checks = ProjectGtCheck::model()->findAll($criteria);

            foreach ($checks as $check) {
                if (!isset($model->matrix[$targetId][$check->gt_check_id])) {
                    continue;
                }

                $ctr = 0;

                foreach ($risks as $riskId => $risk) {
                    $ctr++;

                    if (!isset($model->matrix[$targetId][$check->gt_check_id][$risk->id])) {
                        continue;
                    }

                    $riskName = 'R' . $ctr;

                    $damage = $model->matrix[$targetId][$check->gt_check_id][$risk->id]['damage'] - 1;
                    $likelihood = $model->matrix[$targetId][$check->gt_check_id][$risk->id]['likelihood'] - 1;

                    if (!isset($mtrx[$damage])) {
                        $mtrx[$damage] = array();
                    }

                    if (!isset($mtrx[$damage][$likelihood])) {
                        $mtrx[$damage][$likelihood] = array();
                    }

                    if (!in_array($riskName, $mtrx[$damage][$likelihood])) {
                        $mtrx[$damage][$likelihood][] = $riskName;
                    }
                }
            }

            $data[] = array(
                'host' => $target,
                'description' => "",
                'matrix' => $mtrx
            );

            $targetId++;
        }

        return $data;
    }

    /**
     * Generate risk matrix report.
     */
    private function _generateRiskMatrixReport($model, &$section = null, $sectionNumber = null) {
        $fullReport = true;

        if (!$section) {
            $fullReport = false;
        }

        $language = Language::model()->findByAttributes(array(
            'code' => Yii::app()->language
        ));

        if ($language) {
            $language = $language->id;
        }

        if (!$fullReport) {
            $template = RiskTemplate::model()->findByAttributes(array(
                'id' => $model->templateId
            ));

            if ($template === null) {
                Yii::app()->user->setFlash('error', Yii::t('app', 'Template not found.'));
                return;
            }

            $project = Project::model()->findByAttributes(array(
                'client_id' => $model->clientId,
                'id' => $model->projectId
            ));

            if ($project === null) {
                Yii::app()->user->setFlash('error', Yii::t('app', 'Project not found.'));
                return;
            }

            if (!$project->checkPermission()) {
                Yii::app()->user->setFlash('error', Yii::t('app', 'Access denied.'));
                return;
            }

            if (!$project->guided_test && (!$model->targetIds || !count($model->targetIds))) {
                Yii::app()->user->setFlash('error', Yii::t('app', 'Please select at least 1 target.'));
                return;
            }
        } else {
            $template = RiskTemplate::model()->findByAttributes(array(
                'id' => $model->templateId
            ));

            if ($template === null) {
                Yii::app()->user->setFlash('error', Yii::t('app', 'Template not found.'));
                return;
            }

            $project = $this->project['project'];
        }

        $risks = RiskCategory::model()->with(array(
            'l10n' => array(
                'joinType' => 'LEFT JOIN',
                'on'       => 'language_id = :language_id',
                'params'   => array( 'language_id' => $language )
            )
        ))->findAllByAttributes(
            array('risk_template_id' => $template->id),
            array('order' => 'COALESCE(l10n.name, t.name) ASC')
        );

        if ($project->guided_test) {
            $data = $this->_gtRiskMatrixReport($fullReport, $model, $risks);
        } else {
            $data = $this->_riskMatrixReport($fullReport, $model, $risks, $language);
        }

        if (!$fullReport) {
            $this->_rtfSetup($model);
            $section = $this->rtf->addSection();

            // footer
            $footer = $section->addFooter();
            $footer->writeText(Yii::t('app', 'Risk Matrix') . ': ' . $project->name . ', ', $this->textFont, $this->noPar);
            $footer->writePlainRtfCode(
                '\fs' . ($this->textFont->getSize() * 2) . ' \f' . $this->textFont->getFontIndex() . ' ' .
                 Yii::t('app', 'page {page} of {numPages}',
                array(
                    '{page}'     => '{\field{\*\fldinst {PAGE}}{\fldrslt {1}}}',
                    '{numPages}' => '{\field{\*\fldinst {NUMPAGES}}{\fldrslt {1}}}'
                )
            ));

            // title
            $section->writeText(Yii::t('app', 'Risk Matrix') . ': ' . $project->name, $this->h1Font, $this->titlePar);
            $section->writeText(Yii::t('app', 'Risk Categories') . "\n", $this->h3Font, $this->noPar);
        }

        $table = $section->addTable(PHPRtfLite_Table::ALIGN_LEFT);
        $table->addRows(count($risks) + 1);
        $table->addColumnsList(array( $this->docWidth * 0.11, $this->docWidth * 0.89 ));
        $table->setFontForCellRange($this->boldFont, 1, 1, 1, 2);
        $table->setBackgroundForCellRange('#E0E0E0', 1, 1, 1, 2);
        $table->setFontForCellRange($this->textFont, 2, 1, count($risks), 2);
        $table->setBorderForCellRange($this->thinBorder, 1, 1, count($risks) + 1, 2);

        // set paddings
        for ($row = 1; $row <= count($risks) + 1; $row++) {
            for ($col = 1; $col <= 2; $col++) {
                $table->getCell($row, $col)->setCellPaddings($this->cellPadding, $this->cellPadding, $this->cellPadding, $this->cellPadding);
                $table->getCell($row, $col)->setVerticalAlignment(PHPRtfLite_Table_Cell::VERTICAL_ALIGN_TOP);
            }
        }

        $row = 1;

        $table->writeToCell($row, 1, Yii::t('app', 'Code'), $this->boldFont);
        $table->writeToCell($row, 2, Yii::t('app', 'Risk Category'), $this->boldFont);

        $row++;
        $ctr = 0;

        foreach ($risks as $risk) {
            $ctr++;

            $table->writeToCell($row, 1, 'R' . $ctr);
            $table->writeToCell($row, 2, $risk->localizedName);

            $row++;
        }

        if (!$fullReport) {
            $section->writeText(Yii::t('app', 'Targets'), $this->h3Font, $this->h3Par);
            $section->writeText("\n\n", $this->textFont);
        }

        $targetNumber = 1;

        foreach ($data as $target) {
            if ($fullReport) {
                $this->toc->writeHyperLink(
                    '#risk_matrix_' . $targetNumber,
                    '        ' . $sectionNumber . '.' . $targetNumber . '. ' . $target['host'],
                    $this->textFont
                );

                $section->writeBookmark(
                    'risk_matrix_' . $targetNumber,
                    $sectionNumber . '.' . $targetNumber . '. ' . $target['host'],
                    $this->boldFont
                );

                if ($target['description']) {
                    $font = new PHPRtfLite_Font($this->fontSize, $this->fontFamily, '#909090');

                    $this->toc->writeText(' / ', $this->textFont);
                    $this->toc->writeHyperLink(
                        '#risk_matrix_' . $targetNumber,
                        $target['description'],
                        $font
                    );
                    $this->toc->writeText("\n");

                    $section->writeText(' / ', $this->textFont);
                    $section->writeText($target['description'], $font);
                } else {
                    $this->toc->writeText("\n");
                }

                $targetNumber++;
            } else {
                $section->writeText($target['host'], $this->boldFont);

                if ($target['description']) {
                    $section->writeText(' / ', $this->textFont);
                    $section->writeText($target['description'], new PHPRtfLite_Font($this->fontSize, $this->fontFamily, '#909090'));
                }
            }

            $section->writeText("\n");

            if (!$target['matrix']) {
                $section->writeText("\n", $this->textFont);
                $section->writeText(Yii::t('app', 'No checks.') . "\n\n", $this->textFont);
                continue;
            }

            $table = $section->addTable(PHPRtfLite_Table::ALIGN_LEFT);

            $table->addRows(5);
            $table->addColumnsList(array( $this->docWidth * 0.12, $this->docWidth * 0.22, $this->docWidth * 0.22, $this->docWidth * 0.22, $this->docWidth * 0.22 ));

            $table->mergeCellRange(1, 1, 4, 1);
            $table->mergeCellRange(5, 1, 5, 5);

            $table->setFontForCellRange($this->smallBoldFont, 1, 1, 5, 1);
            $table->setFontForCellRange($this->smallBoldFont, 5, 1, 5, 1);
            $table->setBorderForCellRange($this->thinBorderTL, 1, 1, 5, 1);
            $table->setBorderForCellRange($this->thinBorderBR, 5, 1, 5, 5);

            $table->setFontForCellRange($this->textFont, 1, 2, 4, 5);
            $table->setBorderForCellRange($this->thinBorder, 1, 2, 4, 5);

            $table->writeToCell(1, 1, '&uarr;<br>' . Yii::t('app', 'Damage'));
            $table->writeToCell(5, 1, Yii::t('app', 'Likelihood') . ' &rarr;');

            // set paddings
            for ($row = 1; $row <= 5; $row++) {
                for ($col = 1; $col <= 5; $col++) {
                    $table->getCell($row, $col)->setCellPaddings($this->cellPadding, $this->cellPadding, $this->cellPadding, $this->cellPadding);

                    if ($col == 1 || $row == 5) {
                        $table->getCell($row, $col)->setVerticalAlignment(PHPRtfLite_Table_Cell::VERTICAL_ALIGN_CENTER);
                        $table->getCell($row, $col)->setTextAlignment(PHPRtfLite_Table_Cell::TEXT_ALIGN_CENTER);

                        continue;
                    }

                    $table->getCell($row, $col)->setVerticalAlignment(PHPRtfLite_Table_Cell::VERTICAL_ALIGN_TOP);
                    $table->getCell($row, $col)->setTextAlignment(PHPRtfLite_Table_Cell::TEXT_ALIGN_CENTER);

                    $bgColor = '#CCFFBB';

                    if (($row == 1 && $col >= 3) || ($row == 2 && $col >= 4) || ($row == 3 && $col >= 5)) {
                        $bgColor = '#FFBBBB';
                    }

                    $table->getCell($row, $col)->setBackgroundColor($bgColor);
                }
            }

            $matrix = $target['matrix'];

            for ($damage = 0; $damage < 4; $damage++) {
                for ($likelihood = 0; $likelihood < 4; $likelihood++) {
                    if (!isset($matrix[$damage][$likelihood])) {
                        continue;
                    }

                    $text = implode(', ', $matrix[$damage][$likelihood]);
                    $table->writeToCell(4 - $damage, $likelihood + 2, $text);
                }
            }
        }

        if (!$fullReport) {
            $fileName = Yii::t('app', 'Risk Matrix') . ' - ' . $project->name . ' (' . $project->year . ').rtf';
            $hashName = hash('sha256', rand() . time() . $fileName);
            $filePath = Yii::app()->params['tmpPath'] . '/' . $hashName;

            $this->rtf->save($filePath);

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
    }

    /**
     * Show risk matrix report form.
     */
    public function actionRiskMatrix() {
        $model = new RiskMatrixForm();

        if (isset($_POST['RiskMatrixForm'])) {
            $model->attributes = $_POST['RiskMatrixForm'];

            if ($model->validate()) {
                $this->_generateRiskMatrixReport($model);
            } else {
                Yii::app()->user->setFlash('error', Yii::t('app', 'Please fix the errors below.'));
            }
        }

        $criteria = new CDbCriteria();
        $criteria->order = 't.name ASC';

        if (!User::checkRole(User::ROLE_ADMIN)) {
            $projects = ProjectUser::model()->with('project')->findAllByAttributes(array(
                'user_id' => Yii::app()->user->id
            ));

            $clientIds = array();

            foreach ($projects as $project) {
                if (!in_array($project->project->client_id, $clientIds)) {
                    $clientIds[] = $project->project->client_id;
                }
            }

            $criteria->addInCondition('id', $clientIds);
        }

        $clients = Client::model()->findAll($criteria);

        $this->breadcrumbs[] = array(Yii::t('app', 'Risk Matrix'), '');

        $language = Language::model()->findByAttributes(array(
            'code' => Yii::app()->language
        ));

        if ($language) {
            $language = $language->id;
        }

        $templates = RiskTemplate::model()->with(array(
            'l10n' => array(
                'joinType' => 'LEFT JOIN',
                'on'       => 'language_id = :language_id',
                'params'   => array('language_id' => $language)
            )
        ))->findAllByAttributes(
            array(),
            array('order' => 'COALESCE(l10n.name, t.name) ASC')
        );

        // display the report generation form
        $this->pageTitle = Yii::t('app', 'Risk Matrix');
		$this->render('risk_matrix', array(
            'model'     => $model,
            'clients'   => $clients,
            'templates' => $templates
        ));
    }

    /**
     * Display an effort estimation form.
     */
	public function actionEffort()
	{
        $references = Reference::model()->findAllByAttributes(
            array(),
            array( 'order' => 't.name ASC' )
        );

        $language = Language::model()->findByAttributes(array(
            'code' => Yii::app()->language
        ));

        if ($language)
            $language = $language->id;

        $categories = CheckCategory::model()->with(array(
            'l10n' => array(
                'joinType' => 'LEFT JOIN',
                'on'       => 'language_id = :language_id',
                'params'   => array( 'language_id' => $language )
            )
        ))->findAllByAttributes(
            array(),
            array( 'order' => 'COALESCE(l10n.name, t.name) ASC' )
        );

        $checks = Check::model()->with(array(
            'l10n' => array(
                'joinType' => 'LEFT JOIN',
                'on'       => 'language_id = :language_id',
                'params'   => array( 'language_id' => $language )
            ),
            'control'
        ))->findAllByAttributes(
            array(),
            array( 'order' => 't.sort_order ASC' )
        );

        $referenceArray = array();
        $checkArray     = array();

        foreach ($references as $reference)
            $referenceArray[] = array(
                'id'   => $reference->id,
                'name' => $reference->name
            );

        foreach ($categories as $category)
        {
            $checkCategory = array(
                'id'     => $category->id,
                'name'   => $category->localizedName,
                'checks' => array()
            );

            foreach ($checks as $check)
                if ($check->control->check_category_id == $category->id)
                    $checkCategory['checks'][] = array(
                        'effort'    => $check->effort,
                        'advanced'  => $check->advanced,
                        'reference' => $check->reference_id
                    );

            $checkArray[] = $checkCategory;
        }

        $this->breadcrumbs[] = array(Yii::t('app', 'Effort Estimation'), '');

        // display the page
        $this->pageTitle = Yii::t('app', 'Effort Estimation');
		$this->render('effort', array(
            'references' => $referenceArray,
            'checks'     => $checkArray,
        ));
    }

    /**
     * Prepare text for vulnerabilities report.
     */
    private function _prepareVulnExportText($text) {
        $text = str_replace(array("\r", "\n"), '', $text);
        $text = str_replace(array("<br>", "<br/>", "<br />"), "\n", $text);
        $text = strip_tags($text);
        $text = html_entity_decode($text, ENT_COMPAT, 'UTF-8');

        return $text;
    }

    /**
     * Vulnerability export
     * @param $project
     * @param $language
     * @param $model
     */
    private function _projectVulnExport($project, $language, $model) {
        $criteria = new CDbCriteria();
        $criteria->addColumnCondition(array(
            'project_id' => $project->id
        ));
        $criteria->addInCondition('id', $model->targetIds);
        $criteria->order = 'host ASC';

        $targets = Target::model()->findAll($criteria);

        if (!$targets) {
            Yii::app()->user->setFlash('error', Yii::t('app', 'Targets not found.'));
            return array();
        }

        $data = array();
        $ratings = TargetCheck::getRatingNames();

        $statuses = array(
            TargetCheckVuln::STATUS_OPEN => Yii::t('app', 'Open'),
            TargetCheckVuln::STATUS_RESOLVED => Yii::t('app', 'Resolved'),
        );

        foreach ($targets as $target) {
            // get all references (they are the same across all target categories)
            $referenceIds = array();

            $references = TargetReference::model()->findAllByAttributes(array(
                'target_id' => $target->id
            ));

            if (!$references) {
                continue;
            }

            foreach ($references as $reference) {
                $referenceIds[] = $reference->reference_id;
            }

            // get all categories
            $categories = TargetCheckCategory::model()->with('category')->findAllByAttributes(
                array( 'target_id' => $target->id  ),
                array( 'order'     => 'category.name ASC' )
            );

            if (!$categories) {
                continue;
            }

            foreach ($categories as $category) {
                // get all controls
                $controls = CheckControl::model()->findAllByAttributes(
                    array( 'check_category_id' => $category->check_category_id ),
                    array( 'order'             => 't.sort_order ASC' )
                );

                if (!$controls) {
                    continue;
                }

                foreach ($controls as $control) {
                    $criteria = new CDbCriteria();
                    $criteria->order = 't.sort_order ASC, tcs.id ASC';
                    $criteria->addInCondition('t.reference_id', $referenceIds);
                    $criteria->addColumnCondition(array(
                        't.check_control_id' => $control->id
                    ));

                    if ($this->_system->demo) {
                        $criteria->addColumnCondition(array(
                            "t.demo" => true
                        ));
                    }

                    $criteria->together = true;

                    if (!$category->advanced) {
                        $criteria->addCondition('t.advanced = FALSE');
                    }

                    $checks = Check::model()->with(array(
                        "l10n" => array(
                            "joinType" => "LEFT JOIN",
                            "on" => "l10n.language_id = :language_id",
                            "params" => array("language_id" => $language)
                        ),
                        "targetChecks" => array(
                            "alias" => "tcs",
                            "joinType" => "INNER JOIN",
                            "on" => "tcs.target_id = :target_id AND tcs.status = :status AND tcs.rating != :hidden",
                            "params" => array(
                                "target_id" => $target->id,
                                "status" => TargetCheck::STATUS_FINISHED,
                                "hidden" => TargetCheck::RATING_HIDDEN,
                            ),
                            "with" => array(
                                "vuln" => array(
                                    "with" => "user"
                                ),
                                "solutions" => array(
                                    "alias" => "tss",
                                    "joinType" => "LEFT JOIN",
                                    "with" => array(
                                        "solution" => array(
                                            "alias" => "tss_s",
                                            "joinType" => "LEFT JOIN",
                                            "with" => array(
                                                "l10n" => array(
                                                    "alias" => "tss_s_l10n",
                                                    "on" => "tss_s_l10n.language_id = :language_id",
                                                    "params" => array("language_id" => $language)
                                                )
                                            )
                                        )
                                    )
                                ),
                            )
                        ),
                        "_reference"
                    ))->findAll($criteria);

                    $criteria = new CDbCriteria();
                    $criteria->addCondition("target_id = :target_id");
                    $criteria->addCondition("rating != :hidden");
                    $criteria->addCondition("check_control_id = :check_control_id");
                    $criteria->params = array(
                        'target_id' => $target->id,
                        'hidden' => TargetCustomCheck::RATING_HIDDEN,
                        'check_control_id' => $control->id
                    );

                    $customChecks = TargetCustomCheck::model()->with(array(
                            'vuln' => array(
                                'with' => 'user'
                            )
                        )
                    )->findAll($criteria);

                    if (!$checks && !$customChecks) {
                        continue;
                    }

                    foreach ($checks as $check) {
                        $ctr = 0;

                        foreach ($check->targetChecks as $tc) {
                            $row = $this->_getVulnExportRow(array(
                                'type'      => 'check',
                                'check'     => $tc,
                                'model'     => $model,
                                'target'    => $target,
                                'ctr'       => $ctr,
                                'ratings'   => $ratings,
                                'statuses'  => $statuses,
                            ));

                            if (!$row) {
                                continue;
                            }

                            $data[] = $row;
                            $ctr++;
                        }
                    }

                    foreach ($customChecks as $cc) {
                        $row = $this->_getVulnExportRow(array(
                            'type'      => 'custom',
                            'check'     => $cc,
                            'model'     => $model,
                            'target'    => $target,
                            'ratings'   => $ratings,
                            'statuses'  => $statuses,
                        ));

                        if (!$row) {
                            continue;
                        }

                        $data[] = $row;
                    }
                }
            }
        }

        return $data;
    }

    /**
     * Returns vuln export row data
     * @param $data
     * @return null
     */
    private function _getVulnExportRow($data) {
        $type = $data['type'];
        $check = $data['check'];
        $model = $data['model'];
        $target = $data['target'];
        $ratings = $data['ratings'];
        $statuses = $data['statuses'];
        $ctr = null;

        if ($type == TargetCheck::TYPE) {
            $ctr = $data['ctr'];
        }

        if (!in_array($check->rating, $model->ratings)) {
            return null;
        }

        $row = array();

        if (in_array(TargetCheck::COLUMN_TARGET, $model->columns)) {
            $row[TargetCheck::COLUMN_TARGET] = $target->host;
        }

        if (in_array(TargetCheck::COLUMN_NAME, $model->columns)) {
            if ($type == TargetCheck::TYPE) {
                $row[TargetCheck::COLUMN_NAME] = $check->check->localizedName . ($ctr > 0 ? " " . ($ctr + 1) : "");
            } elseif ($type == TargetCustomCheck::TYPE) {
                $row[TargetCheck::COLUMN_NAME] = $check->name ? $check->name : 'CUSTOM-CHECK-' . $check->reference;
            }
        }

        if (in_array(TargetCheck::COLUMN_REFERENCE, $model->columns)) {
            if ($type == TargetCheck::TYPE) {
                $row[TargetCheck::COLUMN_REFERENCE] = $check->check->_reference->name .
                    ($check->check->reference_code ? '-' . $check->check->reference_code : '');
            } elseif ($type == TargetCustomCheck::TYPE) {
                $row[TargetCheck::COLUMN_REFERENCE] = "CUSTOM-CHECK-" . $check->reference;
            }
        }

        if (in_array(TargetCheck::COLUMN_BACKGROUND_INFO, $model->columns)) {
            if ($type == TargetCheck::TYPE) {
                $row[TargetCheck::COLUMN_BACKGROUND_INFO] = $this->_prepareVulnExportText($check->check->localizedBackgroundInfo);
            } elseif ($type == TargetCustomCheck::TYPE) {
                $row[TargetCheck::COLUMN_BACKGROUND_INFO] = $check->background_info;
            }
        }

        if (in_array(TargetCheck::COLUMN_QUESTION, $model->columns)) {
            if ($type == TargetCheck::TYPE) {
                $row[TargetCheck::COLUMN_QUESTION] = $this->_prepareVulnExportText($check->check->localizedQuestion);
            } elseif ($type == TargetCustomCheck::TYPE) {
                $row[TargetCheck::COLUMN_QUESTION] = $check->question;
            }
        }

        if (in_array(TargetCheck::COLUMN_RESULT, $model->columns)) {
            $row[TargetCheck::COLUMN_RESULT] = $check->result;
        }

        if (in_array(TargetCheck::COLUMN_SOLUTION, $model->columns)) {
            if ($type == TargetCheck::TYPE) {
                $solutions = array();

                foreach ($check->solutions as $solution) {
                    $solutions[] = $this->_prepareVulnExportText($solution->solution->localizedSolution);
                }

                if ($check->solution) {
                    $solutions[] = $this->_prepareVulnExportText($check->solution);
                }

                $row[TargetCheck::COLUMN_SOLUTION] = implode("\n", $solutions);
            } elseif ($type == TargetCustomCheck::TYPE) {
                $row[TargetCheck::COLUMN_SOLUTION] = $check->solution;
            }
        }

        if (in_array(TargetCheck::COLUMN_ASSIGNED_USER, $model->columns)) {
            $user = $check->vuln && $check->vuln->user ? $check->vuln->user : null;

            if ($user) {
                $row[TargetCheck::COLUMN_ASSIGNED_USER] = $user->name ? $user->name : $user->email;
            } else {
                $row[TargetCheck::COLUMN_ASSIGNED_USER] = '';
            }
        }

        if (in_array(TargetCheck::COLUMN_RATING, $model->columns)) {
            $row[TargetCheck::COLUMN_RATING] = $ratings[$check->rating];
        }

        if (in_array(TargetCheck::COLUMN_STATUS, $model->columns)) {
            $vuln = $check->vuln ? $check->vuln : null;
            $row[TargetCheck::COLUMN_STATUS] = $statuses[$vuln ? $vuln->status : TargetCheckVuln::STATUS_OPEN];
        }

        return $row;
    }

    /**
     * Vulnerability export for GT projects
     * @param $project
     * @param $language
     * @param $model
     */
    private function _gtProjectVulnExport($project, $language, $model) {
        $criteria = new CDbCriteria();
        $criteria->addColumnCondition(array(
            't.project_id' => $project->id
        ));
        $criteria->order = 'target ASC';

        $gtChecks = ProjectGtCheck::model()->with(array(
            'check' => array(
                'with' => array(
                    'check' => array(
                        'alias' => 'innerCheck',
                        'with' => array(
                            'l10n' => array(
                                'joinType' => 'LEFT JOIN',
                                'on' => 'l10n.language_id = :language_id',
                                'params' => array('language_id' => $language)
                            ),
                            '_reference',
                        ),
                    ),
                ),
            ),
            'solutions' => array(
                'joinType' => 'LEFT JOIN',
                'with' => array(
                    'solution' => array(
                        'joinType' => 'LEFT JOIN',
                        'with' => array(
                            'l10n' => array(
                                'alias' => 's_l10n',
                                'on' => 's_l10n.language_id = :language_id',
                                'params' => array('language_id' => $language)
                            )
                        )
                    )
                )
            ),
            'vuln' => array(
                'with' => 'user'
            )
        ))->findAll($criteria);

        if (!$gtChecks) {
            Yii::app()->user->setFlash('error', Yii::t('app', 'Checks not found.'));
            return array();
        }

        $data = array();
        $ratings = ProjectGtCheck::getRatingNames();

        $statuses = array(
            TargetCheckVuln::STATUS_OPEN => Yii::t('app', 'Open'),
            TargetCheckVuln::STATUS_RESOLVED => Yii::t('app', 'Resolved'),
        );

        foreach ($gtChecks as $check) {
            if (!in_array($check->rating, $model->ratings)) {
                continue;
            }

            $row = array();
            $innerCheck = $check->check->check;

            if ($this->_system->demo && !$innerCheck->demo) {
                continue;
            }

            if (in_array(TargetCheck::COLUMN_TARGET, $model->columns)) {
                $row[TargetCheck::COLUMN_TARGET] = $check->target;
            }

            if (in_array(TargetCheck::COLUMN_NAME, $model->columns)) {
                $row[TargetCheck::COLUMN_NAME] = $innerCheck->localizedName;
            }

            if (in_array(TargetCheck::COLUMN_REFERENCE, $model->columns)) {
                $row[TargetCheck::COLUMN_REFERENCE] = $innerCheck->_reference->name .
                    ($innerCheck->reference_code ? '-' . $innerCheck->reference_code : '');
            }

            if (in_array(TargetCheck::COLUMN_BACKGROUND_INFO, $model->columns)) {
                $row[TargetCheck::COLUMN_BACKGROUND_INFO] = $this->_prepareVulnExportText($innerCheck->localizedBackgroundInfo);
            }

            if (in_array(TargetCheck::COLUMN_QUESTION, $model->columns)) {
                $row[TargetCheck::COLUMN_QUESTION] = $this->_prepareVulnExportText($innerCheck->localizedQuestion);
            }

            if (in_array(TargetCheck::COLUMN_RESULT, $model->columns)) {
                $row[TargetCheck::COLUMN_RESULT] = $check->result;
            }

            if (in_array(TargetCheck::COLUMN_SOLUTION, $model->columns)) {
                $solutions = array();

                foreach ($check->solutions as $solution) {
                    $solutions[] = $this->_prepareVulnExportText($solution->solution->localizedSolution);
                }

                $row[TargetCheck::COLUMN_SOLUTION] = implode("\n", $solutions);
            }

            if (in_array(TargetCheck::COLUMN_ASSIGNED_USER, $model->columns)) {
                $user = $check->vuln && $check->vuln->user ? $check->vuln->user : null;

                if ($user) {
                    $row[TargetCheck::COLUMN_ASSIGNED_USER] = $user->name ? $user->name : $user->email;
                } else {
                    $row[TargetCheck::COLUMN_ASSIGNED_USER] = '';
                }
            }

            if (in_array(TargetCheck::COLUMN_RATING, $model->columns)) {
                $row[TargetCheck::COLUMN_RATING] = $ratings[$check->rating];
            }

            if (in_array(TargetCheck::COLUMN_STATUS, $model->columns)) {
                $row[TargetCheck::COLUMN_STATUS] = $statuses[$check->vuln ? $check->vuln->status : TargetCheckVuln::STATUS_OPEN];
            }

            $data[] = $row;
        }

        return $data;
    }

    /**
     * Generate vulnerabilities report.
     */
    private function _generateVulnExportReport($model) {
        $project = Project::model()->findByPk($model->projectId);

        if (!$project) {
            Yii::app()->user->setFlash('error', Yii::t('app', 'Project not found.'));
            return;
        }

        if (!$project->checkPermission()) {
            Yii::app()->user->setFlash('error', Yii::t('app', 'Access denied.'));
            return;
        }

        $language = Language::model()->findByAttributes(array(
            'code' => Yii::app()->language
        ));

        if ($language) {
            $language = $language->id;
        }

        if ($project->guided_test) {
            $data = $this->_gtProjectVulnExport($project, $language, $model);
        } else {
            $data = $this->_projectVulnExport($project, $language, $model);
        }

        if (!$data) {
            return;
        }

        $header = array();

        if ($model->header) {
            if (in_array(TargetCheck::COLUMN_TARGET, $model->columns)) {
                $header[TargetCheck::COLUMN_TARGET] = Yii::t('app', 'Target');
            }

            if (in_array(TargetCheck::COLUMN_NAME, $model->columns)) {
                $header[TargetCheck::COLUMN_NAME] = Yii::t('app', 'Name');
            }

            if (in_array(TargetCheck::COLUMN_REFERENCE, $model->columns)) {
                $header[TargetCheck::COLUMN_REFERENCE] = Yii::t('app', 'Reference');
            }

            if (in_array(TargetCheck::COLUMN_BACKGROUND_INFO, $model->columns)) {
                $header[TargetCheck::COLUMN_BACKGROUND_INFO] = Yii::t('app', 'Background Info');
            }

            if (in_array(TargetCheck::COLUMN_QUESTION, $model->columns)) {
                $header[TargetCheck::COLUMN_QUESTION] = Yii::t('app', 'Question');
            }

            if (in_array(TargetCheck::COLUMN_RESULT, $model->columns)) {
                $header[TargetCheck::COLUMN_RESULT] = Yii::t('app', 'Result');
            }

            if (in_array(TargetCheck::COLUMN_SOLUTION, $model->columns)) {
                $header[TargetCheck::COLUMN_SOLUTION] = Yii::t('app', 'Solution');
            }

            if (in_array(TargetCheck::COLUMN_ASSIGNED_USER, $model->columns)) {
                $header[TargetCheck::COLUMN_ASSIGNED_USER] = Yii::t('app', 'Assigned');
            }

            if (in_array(TargetCheck::COLUMN_RATING, $model->columns)) {
                $header[TargetCheck::COLUMN_RATING] = Yii::t('app', 'Rating');
            }

            if (in_array(TargetCheck::COLUMN_STATUS, $model->columns)) {
                $header[TargetCheck::COLUMN_STATUS] = Yii::t('app', 'Status');
            }
        }

        // include all PHPExcel libraries
        Yii::setPathOfAlias('xls', Yii::app()->basePath . '/extensions/PHPExcel');
        Yii::import('xls.PHPExcel.Shared.ZipStreamWrapper', true);
        Yii::import('xls.PHPExcel.Shared.String', true);
        Yii::import('xls.PHPExcel', true);
        Yii::registerAutoloader(array( 'PHPExcel_Autoloader', 'Load' ), true);

        $title = Yii::t('app', '{project} Vulnerability Export', array(
            '{project}' => $project->name . ' (' . $project->year . ')'
        )) . ' - ' . date('Y-m-d');

        $xl = new PHPExcel();

        $xl->getDefaultStyle()->getFont()->setName('Helvetica');
        $xl->getDefaultStyle()->getFont()->setSize(12);
        $xl->getProperties()->setTitle($title);
        $xl->setActiveSheetIndex(0);

        $sheet = $xl->getActiveSheet();
        $sheet->getDefaultRowDimension()->setRowHeight(30);

        $row  = 1;
        $cols = range('A', 'Z');

        if ($header) {
            $col = 0;

            foreach ($header as $type => $value) {
                $sheet->getCell($cols[$col] . $row)->setValue($value);
                $width = 0;

                switch ($type) {
                    case TargetCheck::COLUMN_BACKGROUND_INFO:
                    case TargetCheck::COLUMN_QUESTION:
                    case TargetCheck::COLUMN_RESULT:
                    case TargetCheck::COLUMN_SOLUTION:
                        $width = 30;
                        break;

                    default:
                        $width = 20;
                }

                $sheet
                    ->getStyle($cols[$col] . $row)
                    ->getBorders()
                    ->getBottom()
                    ->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);

                $sheet
                    ->getStyle($cols[$col] . $row)
                    ->getBorders()
                    ->getRight()
                    ->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);

                $sheet->getColumnDimension($cols[$col])->setWidth($width);
                $col++;
            }

            $row++;
        }

        $lastCol = $cols[count($header) - 1];
        $range = 'A1:' . $lastCol . '1';

        $sheet
            ->getStyle($range)
            ->getFill()
            ->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
            ->getStartColor()->setARGB('FFE0E0E0');

        $sheet
            ->getStyle($range)
            ->getAlignment()
            ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)
            ->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);

        $sheet
            ->getStyle($range)
            ->getFont()
            ->setBold(true);

        $sheet->getRowDimension(1)->setRowHeight(40);

        foreach ($data as $dataRow) {
            $col = 0;

            foreach ($dataRow as $type => $value) {
                $sheet->getCell($cols[$col] . $row)->setValue("\n" . $value . "\n");

                $sheet
                    ->getStyle($cols[$col] . $row)
                    ->getBorders()
                    ->getBottom()
                    ->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);

                $sheet
                    ->getStyle($cols[$col] . $row)
                    ->getBorders()
                    ->getRight()
                    ->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);

                $col++;
            }

            $range = 'A' . $row . ':' . $lastCol . $row;

            $sheet
                ->getStyle($range)
                ->getAlignment()
                ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT)
                ->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);

            $sheet
                ->getStyle($range)
                ->getAlignment()
                ->setWrapText(true);

            $sheet
                ->getStyle($range)
                ->getAlignment()
                ->setIndent(1);

            $sheet->getRowDimension($row)->setRowHeight(-1);

            $row++;
        }

        $fileName = $title . '.xlsx';

        // give user a file
        header('Content-Description: File Transfer');
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $fileName . '"');
        header('Content-Transfer-Encoding: binary');
        header('Expires: 0');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Pragma: public');

        ob_clean();
        flush();

        $writer = PHPExcel_IOFactory::createWriter($xl, 'Excel2007');
        $writer->save('php://output');

        exit();
    }

    /**
     * Show vulnerability export report form.
     */
    public function actionVulnExport() {
        $model = new VulnExportReportForm();

        if (isset($_POST['VulnExportReportForm'])) {
            $model->attributes = $_POST['VulnExportReportForm'];
            $model->header = isset($_POST['VulnExportReportForm']['header']);

            if ($model->validate()) {
                $this->_generateVulnExportReport($model);
            } else {
                Yii::app()->user->setFlash('error', Yii::t('app', 'Please fix the errors below.'));
            }
        }

        $criteria = new CDbCriteria();
        $criteria->order = 't.name ASC';

        if (!User::checkRole(User::ROLE_ADMIN)) {
            $projects = ProjectUser::model()->with('project')->findAllByAttributes(array(
                'user_id' => Yii::app()->user->id
            ));

            $clientIds = array();

            foreach ($projects as $project) {
                if (!in_array($project->project->client_id, $clientIds)) {
                    $clientIds[] = $project->project->client_id;
                }
            }

            $criteria->addInCondition('id', $clientIds);
        }

        $clients = Client::model()->findAll($criteria);

        $this->breadcrumbs[] = array(Yii::t('app', 'Vulnerability Export'), '');

        // display the report generation form
        $this->pageTitle = Yii::t('app', 'Vulnerability Export');
		$this->render('vuln_export', array(
            'model' => $model,
            'clients' => $clients,
            'ratings' => TargetCheck::getRatingNames(),
            'columns' => array(
                TargetCheck::COLUMN_TARGET => Yii::t('app', 'Target'),
                TargetCheck::COLUMN_NAME => Yii::t('app', 'Name'),
                TargetCheck::COLUMN_REFERENCE => Yii::t('app', 'Reference'),
                TargetCheck::COLUMN_BACKGROUND_INFO => Yii::t('app', 'Background Info'),
                TargetCheck::COLUMN_QUESTION => Yii::t('app', 'Question'),
                TargetCheck::COLUMN_RESULT => Yii::t('app', 'Result'),
                TargetCheck::COLUMN_SOLUTION => Yii::t('app', 'Solution'),
                TargetCheck::COLUMN_RATING => Yii::t('app', 'Rating'),
                TargetCheck::COLUMN_ASSIGNED_USER => Yii::t('app', 'Assigned'),
                TargetCheck::COLUMN_STATUS => Yii::t('app', 'Status'),
            )
        ));
    }

    /**
     * Report project tracked time
     * @param $id
     */
    public function actionTrackedTime($id) {
        $projectId = (int) $id;
        $this->_generateTrackedTimeReport($projectId);
    }

    /**
     * Generates project tracked time report
     * @param $id
     */
    private function _generateTrackedTimeReport($id) {
        $projectId = (int) $id;
        $project = Project::model()->findByPk($projectId);

        if ($project === null) {
            Yii::app()->user->setFlash('error', Yii::t('app', 'Project not found.'));
            return;
        }

        if (!$project->checkPermission()) {
            Yii::app()->user->setFlash('error', Yii::t('app', 'Access denied.'));
            return;
        }

        $records = $project->timeRecords;

        $fileName = Yii::t('app', 'Time Tracking Report') . ' - ' . $project->name . ' (' . $project->year . ').rtf';

        $this->_rtfSetup();
        $section = $this->rtf->addSection();

        // main title
        $section->writeText(Yii::t("app", "Time Tracking Report"), $this->h2Font, $this->centerTitlePar);
        $section->writeText(" ", $this->h2Font, $this->noPar);

        $table = $section->addTable(PHPRtfLite_Table::ALIGN_CENTER);

        $table->addColumnsList(array( $this->docWidth * 0.3, $this->docWidth * 0.5, $this->docWidth * 0.2 ));
        $table->addRows(count($records) + 1);

        $table->setBackgroundForCellRange('#E0E0E0', 1, 1, 1, 3);
        $table->setFontForCellRange($this->boldFont, 1, 1, 1, 3);
        $table->setFontForCellRange($this->textFont, 2, 1, count($records) + 1, 3);
        $table->setBorderForCellRange($this->thinBorder, 1, 1, count($records) + 1, 3);
        $table->setFirstRowAsHeader();

        // set paddings
        for ($row = 1; $row <= count($records) + 1; $row++) {
            for ($col = 1; $col <= 3; $col++) {
                $table->getCell($row, $col)->setCellPaddings($this->cellPadding, $this->cellPadding, $this->cellPadding, $this->cellPadding);

                if ($row > 1) {
                    $table->getCell($row, $col)->setVerticalAlignment(PHPRtfLite_Table_Cell::VERTICAL_ALIGN_TOP);
                } else {
                    $table->getCell($row, $col)->setVerticalAlignment(PHPRtfLite_Table_Cell::VERTICAL_ALIGN_CENTER);
                }
            }
        }

        $row = 1;

        $table->getCell($row, 1)->writeText(Yii::t('app', 'Time Added'));
        $table->getCell($row, 2)->writeText(Yii::t('app', 'Description'));
        $table->getCell($row, 3)->writeText(Yii::t('app', 'Time Logged'));

        $row++;

        // filling positions list
        foreach ($records as $record) {
            $table->getCell($row, 1)->setCellPaddings(
                $this->cellPadding,
                $this->cellPadding,
                $this->cellPadding,
                $this->cellPadding
            );
            $table->getCell($row, 2)->setCellPaddings(
                $this->cellPadding,
                $this->cellPadding,
                $this->cellPadding,
                $this->cellPadding
            );
            $table->getCell($row, 1)->writeText(DateTimeFormat::toISO($record->create_time), $this->textFont, $this->noPar);
            $table->getCell($row, 2)->writeText($record->description, $this->textFont, $this->noPar);
            $table->getCell($row, 3)->writeText($record->hours . ' h', $this->textFont, $this->noPar);
            $row++;
        }

        // total hours
        $section->writeText(Yii::t("app", "Summary") . ': ' . $project->trackedTime . ' h', $this->h3Font, $this->rightPar);

        $hashName = hash('sha256', rand() . time() . $fileName);
        $filePath = Yii::app()->params['tmpPath'] . '/' . $hashName;
        $this->rtf->save($filePath);

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
}