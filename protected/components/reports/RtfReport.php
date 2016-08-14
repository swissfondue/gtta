<?php

/**
 * RTF report plugin
 */
class RtfReport extends ReportPlugin {
    /**
     * @var PHPRtfLite
     */
    public $rtf;

    public $cellPadding;
    public $fontSize;
    public $fontFamily;
    public $thinBorder, $thinBorderTL, $thinBorderBR;
    public $h1Font, $h2Font, $h3Font, $textFont, $boldFont, $linkFont, $footerFont, $smallBoldFont;
    public $titlePar, $centerTitlePar, $h3Par, $centerPar, $leftPar, $rightPar, $noPar;
    public $docWidth;

    /**
     * RTF report setup
     * @param $pageMargin
     * @param $cellPadding
     * @param $fontSize
     * @param $fontFamily
     * @throws CException
     */
    public function setup($pageMargin=null, $cellPadding=null, $fontSize=null, $fontFamily=null) {
        // include all PHPRtfLite libraries
        Yii::setPathOfAlias("rtf", Yii::app()->basePath . "/extensions/PHPRtfLite/PHPRtfLite");
        Yii::import("rtf.Autoloader", true);
        PHPRtfLite_Autoloader::setBaseDir(Yii::app()->basePath . "/extensions/PHPRtfLite");
        Yii::registerAutoloader(array( "PHPRtfLite_Autoloader", "autoload" ), true);

        $pageMargin = $pageMargin ? $pageMargin : Yii::app()->params["reports"]["pageMargin"];
        $cellPadding = $cellPadding ? $cellPadding : Yii::app()->params["reports"]["cellPadding"];
        $fontSize = $fontSize ? $fontSize : Yii::app()->params["reports"]["fontSize"];
        $fontFamily = $fontFamily ? $fontFamily : Yii::app()->params["reports"]["font"];

        $this->rtf = new PHPRtfLite();
        $this->rtf->setCharset("UTF-8");
        $this->rtf->setMargins($pageMargin, $pageMargin, $pageMargin, $pageMargin);

        $this->cellPadding = $cellPadding;
        $this->fontSize = $fontSize;
        $this->fontFamily = $fontFamily;

        // borders
        $this->thinBorder = new PHPRtfLite_Border(
            $this->rtf,
            new PHPRtfLite_Border_Format(1, "#909090"),
            new PHPRtfLite_Border_Format(1, "#909090"),
            new PHPRtfLite_Border_Format(1, "#909090"),
            new PHPRtfLite_Border_Format(1, "#909090")
        );

        $this->thinBorderTL = new PHPRtfLite_Border(
            $this->rtf,
            new PHPRtfLite_Border_Format(1, "#909090"),
            new PHPRtfLite_Border_Format(1, "#909090"),
            new PHPRtfLite_Border_Format(0, "#909090"),
            new PHPRtfLite_Border_Format(0, "#909090")
        );

        $this->thinBorderBR = new PHPRtfLite_Border(
            $this->rtf,
            new PHPRtfLite_Border_Format(1, "#909090"),
            new PHPRtfLite_Border_Format(0, "#909090"),
            new PHPRtfLite_Border_Format(1, "#909090"),
            new PHPRtfLite_Border_Format(1, "#909090")
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

        $this->linkFont = new PHPRtfLite_Font($fontSize, $fontFamily, "#0088CC");
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
     * Get rating images
     * @param ReportTemplate $template
     * @return array
     */
    private function _getRatingImages(ReportTemplate $template) {
        $imageNames = [
            TargetCheck::RATING_NONE => "none",
            TargetCheck::RATING_NO_VULNERABILITY => "no_vuln",
            TargetCheck::RATING_INFO => "info",
            TargetCheck::RATING_LOW_RISK => "low",
            TargetCheck::RATING_MED_RISK => "med",
            TargetCheck::RATING_HIGH_RISK => "high",
        ];

        $images = array();

        foreach ($imageNames as $id => $name) {
            $img = $template->getRatingImage($id);
            $images[$id] = $img ?
                Yii::app()->params["reports"]["ratingImages"]["path"] . "/" . $img->path :
                Yii::app()->basePath . "/../images/" . $name . ".png";
        }

        return $images;
    }

    /**
     * Add title page
     * @param PHPRtfLite_Container_Section $section
     * @param ReportTemplate $template
     * @param Project $project
     */
    private function _addTitlePage(PHPRtfLite_Container_Section $section, ReportTemplate $template, Project $project) {
        if ($template->header_image_path) {
            $extension = "jpg";

            if ($template->header_image_type == "image/png")
                $extension = "png";

            $filePath = Yii::app()->params["reports"]["tmpFilesPath"] . "/" . $template->header_image_path . "." . $extension;

            if (@copy(
                Yii::app()->params["reports"]["headerImages"]["path"] . "/" . $template->header_image_path,
                $filePath
            )) {
                $section->addImage($filePath, $this->centerPar, $this->docWidth);
                @unlink($filePath);
            }
        }

        $section->writeText(Yii::t("app", "Penetration Test Report") . ": " . $project->name, $this->h1Font, $this->titlePar);
        $section->writeText(Yii::t("app", "Prepared for") . ":\n", $this->textFont, $this->noPar);

        $client = Client::model()->findByPk($project->client_id);

        $table = $section->addTable(PHPRtfLite_Table::ALIGN_LEFT);
        $table->addRows(1);
        $table->addColumnsList(array( $this->docWidth * 0.4, $this->docWidth * 0.6 ));

        $col = 1;

        if ($client->logo_path) {
            $extension = "jpg";

            if ($client->logo_type == "image/png")
                $extension = "png";

            $filePath = Yii::app()->params["clientLogos"]["tmpFilesPath"] . "/" . $client->logo_path . "." . $extension;

            if (@copy(
                Yii::app()->params["clientLogos"]["path"] . "/" . $client->logo_path,
                $filePath
            )) {
                $table->getCell(1, $col)->addImage($filePath, $this->leftPar, $this->docWidth * 0.35);
                @unlink($filePath);
                $col++;
            }
        }

        $table->getCell(1, $col)->writeText(Yii::t("app", "Company"), $this->boldFont, $this->titlePar);
        $table->getCell(1, $col)->writeText($client->name, $this->textFont, $this->noPar);

        if ($client->address)
            $table->getCell(1, $col)->writeText($client->address, $this->textFont, $this->noPar);

        if ($client->city || $client->state) {
            $address = array();

            if ($client->city) {
                $address[] = $client->city;
            }

            if ($client->state) {
                $address[] = $client->state;
            }

            $table->getCell(1, $col)->writeText(implode(", ", $address), $this->textFont, $this->noPar);
        }

        if ($client->country) {
            $table->getCell(1, $col)->writeText($client->country, $this->textFont, $this->noPar);
        }

        if ($client->postcode) {
            $table->getCell(1, $col)->writeText($client->postcode, $this->textFont, $this->noPar);
        }

        if ($client->website) {
            $table->getCell(1, $col)->writeHyperLink($client->website, $client->website, $this->linkFont, $this->noPar);
        }

        if ($client->contact_name || $client->contact_email || $client->contact_phone || $client->contact_fax) {
            $table->getCell(1, $col)->writeText(" ", $this->textFont, $this->noPar);

            if ($client->contact_name) {
                $table->getCell(1, $col)->writeText($client->contact_name, $this->textFont, $this->noPar);
            }

            if ($client->contact_email) {
                $table->getCell(1, $col)->writeHyperLink("mailto:" . $client->contact_email, $client->contact_email, $this->linkFont, $this->noPar);
            }

            if ($client->contact_phone) {
                $table->getCell(1, $col)->writeText(Yii::t("app", "Phone") . ": " . $client->contact_phone, $this->textFont, $this->noPar);
            }

            if ($client->contact_fax) {
                $table->getCell(1, $col)->writeText(Yii::t("app", "Fax") . ": " . $client->contact_fax, $this->textFont, $this->noPar);
            }
        }

        $section->insertPageBreak();

        $section->writeText(Yii::t("app", "Document Information") . "\n", $this->boldFont, $this->noPar);

        $table = $section->addTable(PHPRtfLite_Table::ALIGN_LEFT);
        $table->addRows(6);
        $table->addColumnsList(array( $this->docWidth * 0.4, $this->docWidth * 0.6 ));

        $user = User::model()->findByPk(Yii::app()->user->id);
        $owner = $user->name ? $user->name : $user->email;

        $table->getCell(1, 1)->writeText(Yii::t("app", "Owner"), $this->textFont, $this->noPar);
        $table->getCell(1, 2)->writeText($owner, $this->textFont, $this->noPar);

        $table->getCell(2, 1)->writeText(Yii::t("app", "Status"), $this->textFont, $this->noPar);
        $table->getCell(2, 2)->writeText(Yii::t("app", "Draft"), $this->textFont, $this->noPar);

        $table->getCell(3, 1)->writeText(Yii::t("app", "Originator"), $this->textFont, $this->noPar);
        $table->getCell(3, 2)->writeText($owner, $this->textFont, $this->noPar);

        $table->getCell(4, 1)->writeText(Yii::t("app", "Review"), $this->textFont, $this->noPar);
        $table->getCell(4, 2)->writeText($owner, $this->textFont, $this->noPar);

        $table->getCell(5, 1)->writeText(Yii::t("app", "File Name"), $this->textFont, $this->noPar);
        $table->getCell(5, 2)->writeText(Yii::t("app", "Project Report"), $this->textFont, $this->noPar);

        $table->getCell(6, 1)->writeText(Yii::t("app", "Modified"), $this->textFont, $this->noPar);
        $table->getCell(6, 2)->writeText(date("d.m.Y"), $this->textFont, $this->noPar);

        $section->writeText(Yii::t("app", "Changes") . "\n", $this->boldFont, $this->noPar);

        $table = $section->addTable(PHPRtfLite_Table::ALIGN_LEFT);
        $table->addRows(2);
        $table->addColumnsList(array( $this->docWidth * 0.4, $this->docWidth * 0.6 ));

        $table->getCell(1, 1)->writeText(Yii::t("app", "Version") . " / " . Yii::t("app", "Date"), $this->boldFont, $this->noPar);
        $table->getCell(2, 1)->writeText("1.0 / " . date("d.m.Y"), $this->textFont, $this->noPar);

        $table->getCell(1, 2)->writeText(Yii::t("app", "Notes"), $this->boldFont, $this->noPar);
        $table->getCell(2, 2)->writeText(Yii::t("app", "Draft"), $this->textFont, $this->noPar);
    }

    /**
     * Add ToC
     * @return PHPRtfLite_Container_Section
     */
    private function _addToc() {
        $toc = $this->rtf->addSection();
        $toc->writeText(Yii::t("app", "Table of Contents"), $this->h2Font, $this->h3Par);
        $toc->writeText("\n\n", $this->textFont);

        return $toc;
    }

    /**
     * Render tables
     * @param PHPRtfLite_Container_Section $container
     * @param string $table
     * @param string $text
     * @param bool $substitute
     * @return boolean
     */
    private function _renderTables(&$container, $table, $text, $substitute=true) {
        if (strpos($text, $table) === false) {
            return false;
        }

        $textBlocks = explode($table, $text);

        for ($i = 0; $i < count($textBlocks); $i++) {
            $this->renderText($container, $textBlocks[$i], $substitute);

            if ($i >= count($textBlocks) - 1) {
                continue;
            }

            switch ($table) {
                case '{target.list}':
                    $list = new PHPRtfLite_List_Enumeration($this->rtf, PHPRtfLite_List_Enumeration::TYPE_CIRCLE);

                    foreach ($this->project['targets'] as $target) {
                        $list->addItem($target->hostPort, $this->textFont, $this->noPar);
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
                        $table->getCell($row, 1)->writeText($target->hostPort, $this->textFont, $this->noPar);

                        if ($target->description) {
                            $table->getCell($row, 1)->writeText(' / ', $this->textFont);
                            $table->getCell($row, 1)->writeText($target->description, new PHPRtfLite_Font($this->fontSize, $this->fontFamily, '#909090'));
                        }

                        $table->getCell($row, 2)->writeText(
                            $target->highRiskCount,
                            new PHPRtfLite_Font($this->fontSize, $this->fontFamily, '#d63515')
                        );

                        $table->getCell($row, 2)->writeText(' / ', $this->textFont);
                        $table->getCell($row, 2)->writeText(
                            $target->medRiskCount,
                            new PHPRtfLite_Font($this->fontSize, $this->fontFamily, '#dace2f')
                        );

                        $table->getCell($row, 2)->writeText(' / ', $this->textFont);
                        $table->getCell($row, 2)->writeText(
                            $target->lowRiskCount,
                            new PHPRtfLite_Font($this->fontSize, $this->fontFamily, '#53a254')
                        );

                        $table->getCell($row, 2)->writeText(' / ', $this->textFont);
                        $table->getCell($row, 2)->writeText($target->infoCount, $this->textFont);

                        $count = $target->checkCount;
                        $finished = $target->finishedCount;

                        $table->getCell($row, 3)->writeText(
                            ($count ? sprintf('%.2f%%', $finished / $count * 100) : '0.00%') . ' / ' . $finished,
                            $this->textFont
                        );

                        $table->getCell($row, 4)->writeText($target->checkCount, $this->textFont);

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
                        $table->getCell($row, 1)->writeText($target->hostPort, $this->textFont, $this->noPar);

                        if ($target->description) {
                            $table->getCell($row, 1)->writeText(' / ', $this->textFont);
                            $table->getCell($row, 1)->writeText($target->description, new PHPRtfLite_Font($this->fontSize, $this->fontFamily, '#909090'));
                        }

                        $control = $this->project['weakestControls'][$target->id];

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
     * Render lists
     * @param PHPRtfLite_Container_Section $container
     * @param string $text
     * @param bool $substitute
     * @return string
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
            $this->renderText($container, $textBlock, $substitute);

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
            $this->renderText($container, $textBlock, $substitute);

            $rendered = true;
            break;
        }

        return $rendered;
    }

    /**
     * Render variable values
     * @param PHPRtfLite_Container_Section $container
     * @param string $text
     * @param bool $substitute
     */
    public function renderText(PHPRtfLite_Container_Section &$container, $text, $substitute=true) {
        if ($substitute && $this->_renderTables($container, "{target.list}", $text, $substitute)) {
            return;
        }

        if ($substitute && $this->_renderTables($container, "{target.stats}", $text, $substitute)) {
            return;
        }

        if ($substitute && $this->_renderTables($container, "{target.weakest}", $text, $substitute)) {
            return;
        }

        if ($substitute && $this->_renderTables($container, "{vuln.list}", $text, $substitute)) {
            return;
        }

        if ($this->_renderLists($container, $text, $substitute)) {
            return;
        }

        if ($substitute) {
            $text = $this->_substituteScalarVars($text);
        }

        $prm = new ProjectReportManager();
        $container->writeText($prm->prepareText($text), $this->textFont, $this->noPar);
    }

    /**
     * Substitute scalar variables
     * @param $text
     * @return string
     */
    private function _substituteScalarVars($text) {
        $data = $this->_data;
        $project = $data["project"];

        $text = str_replace("{client}", $project->client->name, $text);
        $text = str_replace("{project}", $project->name, $text);
        $text = str_replace("{year}", $project->year, $text);

        $deadline = implode(".", array_reverse(explode("-", $project->deadline)));
        $text = str_replace("{deadline}", $deadline, $text);

        $admin = Yii::t("app", "N/A");

        if ($project->projectUsers) {
            foreach ($project->projectUsers as $user) {
                if ($user->admin) {
                    $admin = $user->user->name ? $user->user->name : $user->user->email;
                    break;
                }
            }
        }

        $text = str_replace("{admin}", $admin, $text);
        $text = str_replace("{rating}", sprintf("%.2f", $data["rating"]), $text);
        $text = str_replace("{targets}", count($data["targets"]), $text);
        $text = str_replace("{checks}", $data["checks"], $text);
        $text = str_replace("{checks.info}", $data["checksInfo"], $text);
        $text = str_replace("{checks.med}", $data["checksMed"], $text);
        $text = str_replace("{checks.lo}", $data["checksLow"], $text);
        $text = str_replace("{checks.hi}", $data["checksHigh"], $text);

        return $text;
    }

    /**
     * Normalize X coordinate.
     * @param int $coordinate
     * @param int $min
     * @param int $max
     * @return int
     */
    private function _normalizeCoordinate($coordinate, $min, $max) {
        if ($coordinate < $min) {
            $coordinate = $min;
        } elseif ($coordinate > $max) {
            $coordinate = $max;
        }

        return $coordinate;
    }

    /**
     * Generate rating image.
     * @param float $rating
     * @return string image path
     */
    public function generateRatingImage($rating) {
        $system = System::model()->findByPk(1);
        $image = imagecreatefrompng(Yii::app()->basePath . '/../images/rating-stripe.png');
        $lineCoord = round(200 * $rating / $system->report_max_rating);
        $color = imagecolorallocate($image, 0, 0, 0);

        imageline($image, $lineCoord, 0, $lineCoord, 30, $color);

        $topArrow = array(
            $this->_normalizeCoordinate($lineCoord - 5, 0, 200), 0,
            $this->_normalizeCoordinate($lineCoord + 5, 0, 200), 0,
            $lineCoord, 5
        );

        $bottomArrow = array(
            $this->_normalizeCoordinate($lineCoord - 5, 0, 200), 29,
            $this->_normalizeCoordinate($lineCoord + 5, 0, 200), 29,
            $lineCoord, 24
        );

        imagefilledpolygon($image, $topArrow, count($topArrow) / 2, $color);
        imagefilledpolygon($image, $bottomArrow, count($bottomArrow) / 2, $color);

        $hashName = hash('sha256', rand() . time() . rand());
        $filePath = Yii::app()->params["reports"]["tmpFilesPath"] . '/' . $hashName . '.png';

        imagepng($image, $filePath, 0);
        imagedestroy($image);

        return $filePath;
    }

    /**
     * Add security level chart
     * @param PHPRtfLite_Container_Section $section
     * @param array $data
     * @param ReportTemplateSummary $summary
     * @throws PHPRtfLite_Exception
     */
    private function _addSecurityLevelChart(PHPRtfLite_Container_Section $section, $data, $summary) {
        $vulns = $data["data"];
        $section->addImage($this->generateRatingImage($data["rating"]), $this->centerPar);
        $section->writeText("Rating: " . sprintf("%.2f", $data["rating"]) . ($summary ? " (" . $summary->localizedTitle . ")" : "") . "\n", $this->textFont, $this->centerPar);

        $table = $section->addTable(PHPRtfLite_Table::ALIGN_LEFT);

        $table->addRows(count($vulns) + 1);
        $table->addColumnsList(array( $this->docWidth * 0.44, $this->docWidth * 0.39, $this->docWidth * 0.17 ));
        $table->mergeCellRange(1, 2, 1, 3);
        $table->setFontForCellRange($this->boldFont, 1, 1, 1, 3);
        $table->setBackgroundForCellRange("#E0E0E0", 1, 1, 1, 3);
        $table->setFontForCellRange($this->textFont, 2, 1, count($vulns) + 1, 3);
        $table->setBorderForCellRange($this->thinBorder, 1, 1, count($vulns) + 1, 3);
        $table->setFirstRowAsHeader();

        // set paddings
        for ($row = 1; $row <= count($vulns) + 1; $row++) {
            for ($col = 1; $col <= 3; $col++) {
                $table->getCell($row, $col)->setCellPaddings($this->cellPadding, $this->cellPadding, $this->cellPadding, $this->cellPadding);
                $table->getCell($row, $col)->setVerticalAlignment(PHPRtfLite_Table_Cell::VERTICAL_ALIGN_CENTER);
                $table->getCell($row, $col)->setTextAlignment(PHPRtfLite_Table_Cell::TEXT_ALIGN_LEFT);
            }
        }

        $table->writeToCell(1, 1, Yii::t("app", "Target"));
        $table->writeToCell(1, 2, Yii::t("app", "Rating"));

        $row = 2;

        foreach ($vulns as $target) {
            $table->writeToCell($row, 1, $target["host"]);

            if ($target["description"]) {
                $table->getCell($row, 1)->writeText(" / ", $this->textFont);
                $table->getCell($row, 1)->writeText($target["description"], new PHPRtfLite_Font($this->fontSize, $this->fontFamily, "#909090"));
            }

            $table->addImageToCell($row, 2, $this->generateRatingImage($target["rating"]), null, $this->docWidth * 0.34);
            $table->writeToCell($row, 3, sprintf("%.2f", $target["rating"]));

            $table->getCell($row, 2)->setTextAlignment(PHPRtfLite_Table_Cell::TEXT_ALIGN_CENTER);

            $row++;
        }
    }

    /**
     * Center text on image
     * @param $image
     * @param $text
     * @param $font
     * @param $color
     * @param $size
     * @param $x
     * @param $y
     * @param $width
     */
    private function _centerImageText($image, $text, $font, $color, $size, $x, $y, $width) {
        $box = imagettfbbox($size, 0, $font, $text);
        $offset = round(($width - ($box[2] - $box[1])) / 2);
        imagettftext($image, $size, 0, $x + $offset, $y, $color, $font, $text);
    }

    /**
     * Add vuln distribution chart
     * @param PHPRtfLite_Container_Section $section
     */
    private function _addVulnDistributionChart(PHPRtfLite_Container_Section &$section) {
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

        for ($i = 220; $i > 10; $i -= 50) {
            imageline($image, 30, $i, 570, $i, $medLineColor);
        }

        // get max number of checks
        $max = 0;
        $data = $this->_data;

        if ($data["checksHigh"] > $max) {
            $max = $data["checksHigh"];
        }

        if ($data["checksMed"] > $max) {
            $max = $data["checksMed"];
        }

        if ($data["checksLow"] > $max) {
            $max = $data["checksLow"];
        }

        // get chart scale
        $topValue = 1000;

        foreach ([5, 10, 25, 50, 100, 500] as $v) {
            if ($max <= $v) {
                $topValue = $v;
                break;
            }
        }

        $scaleStep = $topValue / 5;
        $step = 250 / $topValue;

        $font = Yii::app()->params["fonts"]["path"] . "/arial.ttf";

        for ($i = 0; $i <= 5; $i++) {
            $scale = $i * $scaleStep;
            imagettftext($image, 8, 0, 5, 270 - $i * 50 + 4, $lineColor, $font, $scale);
        }

        imagefilledrectangle($image, 50, 270 - $step * $data["checksHigh"], 190, 270, $highColor);
        imagerectangle($image, 50, 270 - $step * $data["checksHigh"], 190, 270, $lineColor);

        imagefilledrectangle($image, 230, 270 - $step * $data["checksMed"], 370, 270, $medColor);
        imagerectangle($image, 230, 270 - $step * $data["checksMed"], 370, 270, $lineColor);

        imagefilledrectangle($image, 410, 270 - $step * $data["checksLow"], 550, 270, $lowColor);
        imagerectangle($image, 410, 270 - $step * $data["checksLow"], 550, 270, $lineColor);

        $this->_centerImageText($image, Yii::t("app", "High Risk") . " (" . $data["checksHigh"] . ")", $font, $lineColor, 10, 30, 290, 180);
        $this->_centerImageText($image, Yii::t("app", "Med Risk") . " (" . $data["checksMed"] . ")", $font, $lineColor, 10, 210, 290, 180);
        $this->_centerImageText($image, Yii::t("app", "Low Risk") . " (" . $data["checksLow"] . ")", $font, $lineColor, 10, 390, 290, 180);

        $hashName = hash("sha256", rand() . time() . rand());
        $filePath = Yii::app()->params["reports"]["tmpFilesPath"] . "/" . $hashName . ".png";

        imagepng($image, $filePath, 0);
        imagedestroy($image);

        $section->addImage($filePath, $this->centerPar);
    }

    /**
     * Generate report
     */
    public function generate() {
        $data = $this->_data;
        $project = $data["project"];
        $vulns = $data["data"];
        $options = $data["options"];

        $template = $this->_template;

        $reportAttachments = $data["attachments"];
        $fileName = Yii::t("app", "Penetration Test Report") . " - " . $project->name . " (" . $project->year . ").rtf";
        $zipFileName = Yii::t("app", "Penetration Test Report") . " - " . $project->name . " (" . $project->year . ").zip";

        $this->setup($data["pageMargin"], $data["cellPadding"], $data["fontSize"], $data["fontFamily"]);
        $section = $this->rtf->addSection();

        // footer
        $footer = $section->addFooter();
        $footer->writeText($template->localizedFooter, $this->footerFont, $this->noPar);
        $footer->writeText(Yii::t("app", "Penetration Test Report") . ": " . $project->name . " / " . $project->year . ", ", $this->footerFont, $this->noPar);
        $footer->writePlainRtfCode(
            "\fs" . ($this->footerFont->getSize() * 2) . " \f" . $this->footerFont->getFontIndex() . " " .
             Yii::t("app", "page {page} of {numPages}",
            array(
                "{page}"     => "{\field{\*\fldinst {PAGE}}{\fldrslt {1}}}",
                "{numPages}" => "{\field{\*\fldinst {NUMPAGES}}{\fldrslt {1}}}"
            )
        ));

        $ratingImages = $this->_getRatingImages($template);

        // title
        if (in_array("title", $options)) {
            $this->_addTitlePage($section, $template, $project);
        } else {
            $section->writeText(Yii::t("app", "Penetration Test Report") . ": " . $project->name, $this->h2Font, $this->titlePar);
        }

        $toc = $this->_addToc();
        $section = $this->rtf->addSection();

        $sectionNumber = 1;
        $summary = null;

        /** @var ReportTemplateSummary $sum */
        foreach ($template->summary as $sum) {
            if ($data["rating"] >= $sum->rating_from && $data["rating"] <= $sum->rating_to) {
                $summary = $sum;
                break;
            }
        }

        /** @var ReportTemplateSection $s */
        foreach ($template->sections as $s) {
            $toc->writeHyperLink(
                "#section" . $sectionNumber,
                $sectionNumber . ". " . $s->title . "\n",
                $this->textFont
            );

            $section->writeBookmark(
                "section" . $sectionNumber,
                $sectionNumber . ". " . $s->title,
                $this->h2Font,
                $this->h3Par
            );

            $this->renderText($section, $s->content);

            switch ($s->type) {
                case ReportSection::TYPE_INTRO:
                    break;

                case ReportSection::TYPE_SUMMARY:
                    if ($summary) {
                        $this->renderText($section, $summary->localizedSummary . "<br>");
                    }

                    break;

                case ReportSection::TYPE_CHART_SECURITY_LEVEL:
                    $this->_addSecurityLevelChart($section, $data, $summary);
                    break;

                case ReportSection::TYPE_CHART_VULN_DISTR:
                    $this->_addVulnDistributionChart($section);
                    break;
            }

            $section->insertPageBreak();
            $sectionNumber++;
        }

        if (in_array("fulfillment", $options))
        {
            $toc->writeHyperLink(
                "#degree",
                "    " . $sectionNumber . "." . $subsectionNumber . ". " . Yii::t("app", "Degree of Fulfillment") . "\n",
                $this->textFont
            );

            $section->writeText("\n");
            $section->writeBookmark(
                "degree",
                $sectionNumber . "." . $subsectionNumber . ". " . Yii::t("app", "Degree of Fulfillment"),
                $this->h3Font,
                $this->h3Par
            );

            if ($template->localizedDegreeIntro)
                $this->_renderText($section, $template->localizedDegreeIntro . "<br><br>");

            $this->_generateFulfillmentDegreeReport($model, false, $section, $sectionNumber . "." . $subsectionNumber);

            $subsectionNumber++;
        }

        if (in_array("matrix", $options))
        {
            $toc->writeHyperLink(
                "#risk_matrix",
                "    " . $sectionNumber . "." . $subsectionNumber . ". " . Yii::t("app", "Risk Matrix") . "\n",
                $this->textFont
            );

            $section->writeBookmark(
                "risk_matrix",
                $sectionNumber . "." . $subsectionNumber . ". " . Yii::t("app", "Risk Matrix"),
                $this->h3Font,
                $this->h3Par
            );

            $riskMatrixModel = new RiskMatrixForm();
            $riskMatrixModel->attributes = $_POST["RiskMatrixForm"];

            if ($template->localizedRiskIntro)
                $this->_renderText($section, $template->localizedRiskIntro . "<br>");

            $this->_generateRiskMatrixReport($riskMatrixModel, $section, $sectionNumber . "." . $subsectionNumber);

            $subsectionNumber++;
        }

        $sectionNumber++;
        $section->insertPageBreak();

        $subsectionNumber = 1;

        // reduced vulnerability list
        if (in_array("vulns", $options))
        {
            $toc->writeHyperLink(
                "#reduced_vulns",
                "    " . $sectionNumber . ". " . Yii::t("app", "Results and Recommendations") . "\n",
                $this->textFont
            );

            $section->writeBookmark(
                "reduced_vulns",
                $sectionNumber . ". " . Yii::t("app", "Results and Recommendations"),
                $this->h2Font,
                $this->h3Par
            );

            if ($template->localizedReducedIntro)
                $this->_renderText($section, $template->localizedReducedIntro . "<br>");

            $table = $section->addTable(PHPRtfLite_Table::ALIGN_LEFT);
            $table->addColumnsList(array( $this->docWidth * 0.15, $this->docWidth * 0.2, $this->docWidth * 0.65 ));
            $table->addRows(7);

            $table->setFontForCellRange($this->boldFont, 1, 1, 1, 3);
            $table->setBackgroundForCellRange("#E0E0E0", 1, 1, 1, 3);
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


            $table->getCell(1, 1)->writeText(Yii::t("app", "Symbol"));
            $table->getCell(1, 2)->writeText(Yii::t("app", "Meaning"));
            $table->getCell(1, 3)->writeText(Yii::t("app", "Description"));

            $table->addImageToCell(2, 1, $ratingImages[TargetCheck::RATING_HIGH_RISK]);
            $table->addImageToCell(3, 1, $ratingImages[TargetCheck::RATING_MED_RISK]);
            $table->addImageToCell(4, 1, $ratingImages[TargetCheck::RATING_LOW_RISK]);
            $table->addImageToCell(5, 1, $ratingImages[TargetCheck::RATING_NONE]);
            $table->addImageToCell(6, 1, $ratingImages[TargetCheck::RATING_NO_VULNERABILITY]);
            $table->addImageToCell(7, 1, $ratingImages[TargetCheck::RATING_INFO]);

            $table->getCell(2, 2)->writeText(Yii::t("app", "High Risk"), $this->textFont);
            $table->getCell(3, 2)->writeText(Yii::t("app", "Med Risk"), $this->textFont);
            $table->getCell(4, 2)->writeText(Yii::t("app", "Low Risk"), $this->textFont);
            $table->getCell(5, 2)->writeText(Yii::t("app", "No Test Done"), $this->textFont);
            $table->getCell(6, 2)->writeText(Yii::t("app", "No Vulnerability"), $this->textFont);
            $table->getCell(7, 2)->writeText(Yii::t("app", "Information"), $this->textFont);

            $this->_renderText($table->getCell(2, 3), $template->localizedHighDescription, true);
            $this->_renderText($table->getCell(3, 3), $template->localizedMedDescription, true);
            $this->_renderText($table->getCell(4, 3), $template->localizedLowDescription, true);
            $this->_renderText($table->getCell(5, 3), $template->localizedNoneDescription, true);
            $this->_renderText($table->getCell(6, 3), $template->localizedNoVulnDescription, true);
            $this->_renderText($table->getCell(7, 3), $template->localizedInfoDescription, true);

            $section->writeText("\n");

            if (!count($this->project["reducedChecks"])) {
                $section->writeText("\n" . Yii::t("app", "No vulnerabilities found.") . "\n", $this->textFont, $this->noPar);
            } else {
                $table = $section->addTable(PHPRtfLite_Table::ALIGN_LEFT);
                $table->addRows(count($this->project["reducedChecks"]) + 1);
                $table->addColumnsList(array(
                    $this->docWidth * 0.2,
                    $this->docWidth * 0.2,
                    $this->docWidth * 0.25,
                    $this->docWidth * 0.25,
                    $this->docWidth * 0.1
                ));

                $table->setBackgroundForCellRange("#E0E0E0", 1, 1, 1, 5);
                $table->setFontForCellRange($this->boldFont, 1, 1, 1, 5);
                $table->setFontForCellRange($this->textFont, 2, 1, count($this->project["reducedChecks"]) + 1, 5);
                $table->setBorderForCellRange($this->thinBorder, 1, 1, count($this->project["reducedChecks"]) + 1, 5);
                $table->setFirstRowAsHeader();

                // set paddings
                for ($row = 1; $row <= count($this->project["reducedChecks"]) + 1; $row++) {
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

                $table->getCell($row, 1)->writeText(Yii::t("app", "Target / Reference"));
                $table->getCell($row, 2)->writeText(Yii::t("app", "Check Name") . " (" . Yii::t("app", "Question") . ")");
                $table->getCell($row, 3)->writeText(Yii::t("app", "Problem"));
                $table->getCell($row, 4)->writeText(Yii::t("app", "Solution"));
                $table->getCell($row, 5)->writeText(Yii::t("app", "Rating"));

                $row++;

                $reducedChecks = $this->project["reducedChecks"];
                usort($reducedChecks, array("ReportController", "sortChecksByRating"));

                foreach ($reducedChecks as $check) {
                    $table->getCell($row, 1)->writeHyperLink(
                        "#check_" . $check["target"]["id"] . "_" . $check["id"],
                        $check["target"]["host"],
                        $this->textFont
                    );

                    if ($check["target"]["description"]) {
                        $table->getCell($row, 1)->writeText(" / ", $this->textFont);
                        $table->getCell($row, 1)->writeText($check["target"]["description"], new PHPRtfLite_Font($this->fontSize, $this->fontFamily, "#909090"));
                    }

                    $table->getCell($row, 2)->writeHyperLink(
                        "#check_" . $check["target"]["id"] . "_" . $check["id"],
                        $check["name"],
                        $this->textFont,
                        $this->noPar
                    );

                    $cell = $table->getCell($row, 2);

                    if ($check["question"]) {
                        $question = is_array($check["question"]) ? $check["question"]["value"] : $check["question"];
                        $cell->writeText("<br>");
                        $this->_renderText($cell, "(" . $question . ")", false);
                    }

                    $problem = is_array($check["result"]) ? $check["result"]["value"] : $check["result"];

                    $cell = $table->getCell($row, 3);

                    if ($problem) {
                        if (Utils::isHtml($problem)) {
                            $this->_renderText($cell, $problem, false);
                        } else {
                            $cell->writeText($problem);
                        }

                        $cell->writeText("<br><br>");
                    }


                    $cell = $table->getCell($row, 4);
                    $this->_renderText($cell, $check["solution"], false);

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

                    $table->addImageToCell($row, 5, $image);

                    $row++;
                }
            }

            $sectionNumber++;
            $section->insertPageBreak();
        }

        // detailed report with vulnerabilities
        $toc->writeHyperLink(
            "#vulns",
            $sectionNumber . ". " . Yii::t("app", "Vulnerabilities") . "\n",
            $this->textFont
        );

        $section->writeBookmark(
            "vulns",
            $sectionNumber . ". " . Yii::t("app", "Vulnerabilities"),
            $this->h2Font,
            $this->h3Par
        );

        $subsectionNumber = 1;

        if ($data["hasSeparate"]) {
            foreach ($template->vulnSections as $scn) {
                // check if section has checks in it
                $checkCount = 0;

                foreach ($vulns as $target) {
                    foreach ($target["categories"] as $cat) {
                        if ($cat["id"] != $scn->check_category_id) {
                            continue;
                        }

                        foreach ($cat["controls"] as $ctrl) {
                            foreach ($ctrl["checks"] as $check) {
                                if ($check["separate"]) {
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

                $toc->writeHyperLink(
                    "#vulns_section_" . $subsectionNumber,
                    "    " . $sectionNumber . "." . $subsectionNumber . ". " . $scn->localizedTitle . "\n",
                    $this->textFont
                );

                $section->writeBookmark(
                    "vulns_section_" . $subsectionNumber,
                    $sectionNumber . "." . $subsectionNumber . ". " . $scn->localizedTitle . "\n",
                    $this->h3Font,
                    $this->noPar
                );

                if ($scn->localizedIntro)
                    $this->_renderText($section, $scn->localizedIntro . "<br><br>");

                $this->_generateVulnerabilityList(
                    $vulns,
                    $section,
                    $sectionNumber . "." . $subsectionNumber,
                    self::SEPARATE_VULN_LIST,
                    $ratingImages,
                    $model->infoChecksLocation,
                    $scn->check_category_id
                );

                $subsectionNumber++;
            }
        }

        $toc->writeHyperLink(
            "#vulns_section_" . $subsectionNumber,
            "    " . $sectionNumber . "." . $subsectionNumber . ". " . Yii::t("app", "Found Vulnerabilities") . "\n",
            $this->textFont
        );

        $section->writeBookmark(
            "vulns_section_" . $subsectionNumber,
            $sectionNumber . "." . $subsectionNumber . ". " . Yii::t("app", "Found Vulnerabilities") . "\n",
            $this->h3Font,
            $this->noPar
        );

        if ($template->localizedVulnsIntro) {
            $this->_renderText($section, $template->localizedVulnsIntro . "<br><br>");
        }

        $this->_generateVulnerabilityList($vulns, $section, $sectionNumber . "." . $subsectionNumber, self::NORMAL_VULN_LIST, $ratingImages, $model->infoChecksLocation);

        $subsectionNumber++;

        if ($this->project["hasInfo"] && $model->infoChecksLocation == ProjectReportForm::INFO_LOCATION_APPENDIX) {
            $toc->writeHyperLink(
                "#vulns_section_" . $subsectionNumber,
                "    " . $sectionNumber . "." . $subsectionNumber . ". " . Yii::t("app", "Additional Data") . "\n",
                $this->textFont
            );

            $section->writeBookmark(
                "vulns_section_" . $subsectionNumber,
                $sectionNumber . "." . $subsectionNumber . ". " . Yii::t("app", "Additional Data") . "\n",
                $this->h3Font,
                $this->noPar
            );

            if ($template->localizedInfoChecksIntro) {
                $this->_renderText($section, $template->localizedInfoChecksIntro . "<br><br>");
            }

            $this->_generateVulnerabilityList($vulns, $section, $sectionNumber . "." . $subsectionNumber, self::APPENDIX_VULN_LIST, $ratingImages);
        }

        $section->insertPageBreak();
        $sectionNumber++;

        // appendix
        if (in_array("appendix", $options) && $template->localizedAppendix) {
            $toc->writeHyperLink(
                "#appendix",
                $sectionNumber . ". " . Yii::t("app", "Appendix") . "\n",
                $this->textFont
            );

            $section->writeBookmark(
                "appendix",
                $sectionNumber . ". " . Yii::t("app", "Appendix"),
                $this->h2Font,
                $this->h3Par
            );

            $this->_renderText($section, $template->localizedAppendix, false);

            $section->insertPageBreak();
            $sectionNumber++;
        }

        // attachments
        if (in_array("attachments", $options)) {
            $toc->writeHyperLink(
                "#attachments",
                $sectionNumber . ". " . Yii::t("app", "Attachments") . "\n",
                $this->textFont
            );

            $section->writeBookmark(
                "attachments",
                $sectionNumber . ". " . Yii::t("app", "Attachments"),
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

            $table->setBackgroundForCellRange("#E0E0E0", 1, 1, 1, 4);
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

            $table->getCell($row, 1)->writeText(Yii::t("app", "Title"));
            $table->getCell($row, 2)->writeText(Yii::t("app", "Host"));
            $table->getCell($row, 3)->writeText(Yii::t("app", "Check"));
            $table->getCell($row, 4)->writeText(Yii::t("app", "File"));

            $row++;

            foreach ($reportAttachments as $attachment) {
                $table->getCell($row, 1)->writeText($attachment["title"]);
                $table->getCell($row, 2)->writeText($attachment["host"]);
                $table->getCell($row, 3)->writeText($attachment["check"]);
                $table->getCell($row, 4)->writeText($attachment["filename"]);
                $row++;
            }
        }

        $hashName = hash("sha256", rand() . time() . $fileName);
        $filePath = Yii::app()->params["reports"]["tmpFilesPath"] . "/" . $hashName;

        $this->rtf->save($filePath);

        if ($model->fileType == ProjectReportForm::FILE_TYPE_RTF) {
            $reportData = array("name" => $fileName, "path" => $filePath);
            $this->_generateReportFile($reportData);
        } else {
            if ($model->fileType == ProjectReportForm::FILE_TYPE_ZIP) {
                $reportData = array("name" => $fileName, "path" => $filePath, "zipName" => $zipFileName);
                $this->_generateReportFile($reportData, $reportAttachments);
            }
        }
    }
}
