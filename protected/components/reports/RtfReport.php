<?php

/**
 * RTF report plugin
 */
class RtfReport extends ReportPlugin {
    /**
     * Vulnerability list types
     */
    const VULNERABILITY_LIST_NORMAL = 0;
    const VULNERABILITY_LIST_SEPARATE_SECTION = 1;
    const VULNERABILITY_LIST_SEPARATE_TABLE = 2;

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
        $data = $this->_data;
        $targets = $data["targets"];

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
                case "{target.list}":
                    $list = new PHPRtfLite_List_Enumeration($this->rtf, PHPRtfLite_List_Enumeration::TYPE_CIRCLE);

                    foreach ($targets as $target) {
                        $list->addItem($target->hostPort, $this->textFont, $this->noPar);
                    }

                    $container->writeRtfCode("\\par ");
                    $container->addList($list);
                    $container->writeRtfCode("\\par ");

                    break;

                case "{target.stats}":
                    $table = $container->addTable(PHPRtfLite_Table::ALIGN_LEFT);
                    $table->addRows(count($targets) + 1);
                    $table->addColumnsList([$this->docWidth * 0.4, $this->docWidth * 0.2, $this->docWidth * 0.2, $this->docWidth * 0.2]);

                    $table->setBackgroundForCellRange("#E0E0E0", 1, 1, 1, 4);
                    $table->setFontForCellRange($this->boldFont, 1, 1, 1, 4);
                    $table->setFontForCellRange($this->textFont, 2, 1, count($targets) + 1, 4);
                    $table->setBorderForCellRange($this->thinBorder, 1, 1, count($targets) + 1, 4);
                    $table->setFirstRowAsHeader();

                    // set paddings
                    for ($row = 1; $row <= count($targets) + 1; $row++) {
                        for ($col = 1; $col <= 4; $col++) {
                            $table->getCell($row, $col)->setCellPaddings($this->cellPadding, $this->cellPadding, $this->cellPadding, $this->cellPadding);
                            $table->getCell($row, $col)->setVerticalAlignment(PHPRtfLite_Table_Cell::VERTICAL_ALIGN_CENTER);
                        }
                    }

                    $row = 1;

                    $table->getCell($row, 1)->writeText(Yii::t("app", "Target"));
                    $table->getCell($row, 2)->writeText(Yii::t("app", "Risk Stats"));
                    $table->getCell($row, 3)->writeText(Yii::t("app", "Completed"));
                    $table->getCell($row, 4)->writeText(Yii::t("app", "Checks"));

                    $row++;

                    foreach ($targets as $target) {
                        $table->getCell($row, 1)->writeText($target->hostPort, $this->textFont, $this->noPar);

                        if ($target->description) {
                            $table->getCell($row, 1)->writeText(" / ", $this->textFont);
                            $table->getCell($row, 1)->writeText($target->description, new PHPRtfLite_Font($this->fontSize, $this->fontFamily, "#909090"));
                        }

                        $table->getCell($row, 2)->writeText(
                            $target->highRiskCount,
                            new PHPRtfLite_Font($this->fontSize, $this->fontFamily, "#d63515")
                        );

                        $table->getCell($row, 2)->writeText(" / ", $this->textFont);
                        $table->getCell($row, 2)->writeText(
                            $target->medRiskCount,
                            new PHPRtfLite_Font($this->fontSize, $this->fontFamily, "#dace2f")
                        );

                        $table->getCell($row, 2)->writeText(" / ", $this->textFont);
                        $table->getCell($row, 2)->writeText(
                            $target->lowRiskCount,
                            new PHPRtfLite_Font($this->fontSize, $this->fontFamily, "#53a254")
                        );

                        $table->getCell($row, 2)->writeText(" / ", $this->textFont);
                        $table->getCell($row, 2)->writeText($target->infoCount, $this->textFont);

                        $count = $target->checkCount;
                        $finished = $target->finishedCount;

                        $table->getCell($row, 3)->writeText(
                            ($count ? sprintf("%.2f%%", $finished / $count * 100) : "0.00%") . " / " . $finished,
                            $this->textFont
                        );

                        $table->getCell($row, 4)->writeText($target->checkCount, $this->textFont);

                        $row++;
                    }

                    break;

                case "{target.weakest}":
                    $table = $container->addTable(PHPRtfLite_Table::ALIGN_LEFT);
                    $table->addRows(count($targets) + 1);
                    $table->addColumnsList([$this->docWidth * 0.4, $this->docWidth * 0.4, $this->docWidth * 0.2]);

                    $table->setBackgroundForCellRange("#E0E0E0", 1, 1, 1, 3);
                    $table->setFontForCellRange($this->boldFont, 1, 1, 1, 3);
                    $table->setFontForCellRange($this->textFont, 2, 1, count($targets) + 1, 3);
                    $table->setBorderForCellRange($this->thinBorder, 1, 1, count($targets) + 1, 3);
                    $table->mergeCellRange(1, 2, 1, 3);
                    $table->setFirstRowAsHeader();

                    // set paddings
                    for ($row = 1; $row <= count($targets) + 1; $row++) {
                        for ($col = 1; $col <= 3; $col++) {
                            $table->getCell($row, $col)->setCellPaddings($this->cellPadding, $this->cellPadding, $this->cellPadding, $this->cellPadding);
                            $table->getCell($row, $col)->setVerticalAlignment(PHPRtfLite_Table_Cell::VERTICAL_ALIGN_CENTER);
                        }
                    }

                    $row = 1;

                    $table->getCell($row, 1)->writeText(Yii::t("app", "Target"));
                    $table->getCell($row, 2)->writeText(Yii::t("app", "Weakest Control"));

                    $row++;

                    foreach ($targets as $target) {
                        $table->getCell($row, 1)->writeText($target->hostPort, $this->textFont, $this->noPar);

                        if ($target->description) {
                            $table->getCell($row, 1)->writeText(" / ", $this->textFont);
                            $table->getCell($row, 1)->writeText($target->description, new PHPRtfLite_Font($this->fontSize, $this->fontFamily, "#909090"));
                        }

                        $control = isset($data["weakestControls"][$target->id]) ? $data["weakestControls"][$target->id] : null;

                        if ($control && is_array($control) && isset($control["name"]) && isset($control["degree"])) {
                            $table->getCell($row, 2)->writeText($control ? $control["name"] : Yii::t("app", "N/A"));
                            $table->getCell($row, 3)->writeText($control ? $control["degree"] . "%" : Yii::t("app", "N/A"));
                        }

                        $row++;
                    }

                    break;

                case "{vuln.list}":
                    $table = $container->addTable(PHPRtfLite_Table::ALIGN_LEFT);
                    $rowCount = count($data["reducedChecks"]) > 5 ? 6 : count($data["reducedChecks"]) + 1;
                    $table->addRows($rowCount);
                    $table->addColumnsList([$this->docWidth * 0.4, $this->docWidth * 0.3, $this->docWidth * 0.3]);

                    $table->setBackgroundForCellRange("#E0E0E0", 1, 1, 1, 3);
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

                    $table->getCell($row, 1)->writeText(Yii::t("app", "Target"));
                    $table->getCell($row, 2)->writeText(Yii::t("app", "Check"));
                    $table->getCell($row, 3)->writeText(Yii::t("app", "Question"));

                    $row++;

                    $reducedChecks = $data["reducedChecks"];
                    usort($reducedChecks, ["ReportManager", "sortChecksByRating"]);

                    foreach ($reducedChecks as $check) {
                        $table->getCell($row, 1)->writeText($check["target"]["host"], $this->textFont, $this->noPar);

                        if ($check["target"]["description"]) {
                            $table->getCell($row, 1)->writeText(" / ", $this->textFont);
                            $table->getCell($row, 1)->writeText($check["target"]["description"], new PHPRtfLite_Font($this->fontSize, $this->fontFamily, "#909090"));
                        }

                        $table->getCell($row, 2)->writeText($check["name"]);
                        $table->getCell($row, 3)->writeText($check["question"] ? $check["question"] : Yii::t("app", "N/A"));

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
        $listTypes = ["ul", "ol"];
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
            $listElements = explode("<li>", $listBlock);

            $listObject = null;

            if ($listType == "ol") {
                $listObject = new PHPRtfLite_List_Numbering($this->rtf);
            } elseif ($listType == "ul") {
                $listObject = new PHPRtfLite_List_Enumeration($this->rtf, PHPRtfLite_List_Enumeration::TYPE_CIRCLE);
            }

            foreach ($listElements as $listElement) {
                $listElement = trim($listElement);

                if (!$listElement) {
                    continue;
                }

                $listElement = str_replace("</li>", "", $listElement);

                if ($substitute) {
                    $listElement = $this->_substituteScalarVars($listElement);
                }

                $listObject->addItem($listElement, $this->textFont, $this->noPar);
            }

            $container->writeRtfCode("\\par ");
            $container->addList($listObject);
            $container->writeRtfCode("\\par ");

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
    public function renderText(&$container, $text, $substitute=true) {
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

        $prm = new ReportManager();
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
     * @param ReportTemplateSummary $summary
     * @throws PHPRtfLite_Exception
     */
    private function _addSecurityLevelChart(PHPRtfLite_Container_Section $section, $summary) {
        $data = $this->_data;
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
     * Add reduced vulnerability list
     * @param PHPRtfLite_Container_Section $section
     * @throws PHPRtfLite_Exception
     */
    private function _addReducedVulnerabilityList(PHPRtfLite_Container_Section &$section) {
        $data = $this->_data;
        $template = $data["template"];

        $table = $section->addTable(PHPRtfLite_Table::ALIGN_LEFT);
        $table->addColumnsList([$this->docWidth * 0.15, $this->docWidth * 0.2, $this->docWidth * 0.65]);
        $table->addRows(7);

        $table->setFontForCellRange($this->boldFont, 1, 1, 1, 3);
        $table->setBackgroundForCellRange("#E0E0E0", 1, 1, 1, 3);
        $table->setFontForCellRange($this->textFont, 2, 1, 7, 3);
        $table->setBorderForCellRange($this->thinBorder, 1, 1, 7, 3);
        $table->setFirstRowAsHeader();

        // set paddings
        for ($row = 1; $row <= 7; $row++) {
            for ($col = 1; $col <= 3; $col++) {
                $table->getCell($row, $col)->setCellPaddings($this->cellPadding, $this->cellPadding, $this->cellPadding, $this->cellPadding);

                if ($row >= 2 && $row <= 4 && $col >= 1 && $col <= 2) {
                    $table->getCell($row, $col)->setVerticalAlignment(PHPRtfLite_Table_Cell::VERTICAL_ALIGN_TOP);
                } else {
                    $table->getCell($row, $col)->setVerticalAlignment(PHPRtfLite_Table_Cell::VERTICAL_ALIGN_CENTER);
                }
            }
        }

        $ratingImages = $this->_getRatingImages($template);

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

        $this->renderText($table->getCell(2, 3), $template->localizedHighDescription, true);
        $this->renderText($table->getCell(3, 3), $template->localizedMedDescription, true);
        $this->renderText($table->getCell(4, 3), $template->localizedLowDescription, true);
        $this->renderText($table->getCell(5, 3), $template->localizedNoneDescription, true);
        $this->renderText($table->getCell(6, 3), $template->localizedNoVulnDescription, true);
        $this->renderText($table->getCell(7, 3), $template->localizedInfoDescription, true);

        $section->writeText("\n");

        if (!count($data["reducedChecks"])) {
            $section->writeText("\n" . Yii::t("app", "No vulnerabilities found.") . "\n", $this->textFont, $this->noPar);
        } else {
            $table = $section->addTable(PHPRtfLite_Table::ALIGN_LEFT);
            $table->addRows(count($data["reducedChecks"]) + 1);
            $table->addColumnsList(array(
                $this->docWidth * 0.2,
                $this->docWidth * 0.2,
                $this->docWidth * 0.25,
                $this->docWidth * 0.25,
                $this->docWidth * 0.1
            ));

            $table->setBackgroundForCellRange("#E0E0E0", 1, 1, 1, 5);
            $table->setFontForCellRange($this->boldFont, 1, 1, 1, 5);
            $table->setFontForCellRange($this->textFont, 2, 1, count($data["reducedChecks"]) + 1, 5);
            $table->setBorderForCellRange($this->thinBorder, 1, 1, count($data["reducedChecks"]) + 1, 5);
            $table->setFirstRowAsHeader();

            // set paddings
            for ($row = 1; $row <= count($data["reducedChecks"]) + 1; $row++) {
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

            $reducedChecks = $data["reducedChecks"];
            usort($reducedChecks, array("ReportManager", "sortChecksByRating"));

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
                    $this->renderText($cell, "(" . $question . ")", false);
                }

                $problem = is_array($check["result"]) ? $check["result"]["value"] : $check["result"];

                $cell = $table->getCell($row, 3);

                if ($problem) {
                    if (Utils::isHtml($problem)) {
                        $this->renderText($cell, $problem, false);
                    } else {
                        $cell->writeText($problem);
                    }

                    $cell->writeText("<br><br>");
                }


                $cell = $table->getCell($row, 4);
                $this->renderText($cell, $check["solution"], false);

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
    }

    /**
     * Add vulnerability list
     * @param PHPRtfLite_Container_Section $section
     * @param int $sectionNumber
     * @param PHPRtfLite_Container_Section $toc
     */
    private function _addVulnerabilityList(PHPRtfLite_Container_Section &$section, $sectionNumber, $toc=null) {
        $data = $this->_data;
        $vulns = $data["data"];
        $template = $data["template"];
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

                if ($scn->localizedIntro) {
                    $this->renderText($section, $scn->localizedIntro . "<br><br>");
                }

                $this->_generateVulnerabilityList(
                    $section,
                    $sectionNumber . "." . $subsectionNumber,
                    self::VULNERABILITY_LIST_SEPARATE_TABLE,
                    $scn->check_category_id,
                    $toc
                );

                $subsectionNumber++;
            }
        }

        $this->_generateVulnerabilityList(
            $section,
            $sectionNumber . ($subsectionNumber > 1 ? "." . $subsectionNumber : ""),
            self::VULNERABILITY_LIST_NORMAL,
            null,
            $toc
        );
    }

    /**
     * Add attachment list
     * @param PHPRtfLite_Container_Section $section
     * @throws PHPRtfLite_Exception
     */
    private function _addAttachmentList(PHPRtfLite_Container_Section &$section) {
        $section->writeText("", $this->textFont, $this->noPar);
        $reportAttachments = $this->_data["attachments"];

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

     /**
      * Fulfillment degree image.
      * @param float $degree
      * @return string file path
      */
    private function _generateFulfillmentImage($degree) {
        $scale = imagecreatefrompng(Yii::app()->basePath . "/../images/fulfillment-stripe.png");
        imagealphablending($scale, false);
        imagesavealpha($scale, true);

        $image = imagecreatetruecolor(301, 30);

        $lineCoord = $degree * 3;
        $white = imagecolorallocate($image, 0xFF, 0xFF, 0xFF);
        $color = imagecolorallocate($image, 0x3A, 0x87, 0xAD);

        imagefilledrectangle($image, 0, 0, 301, 30, $white);
        imagefilledrectangle($image, 0, 6, $lineCoord, 24, $color);
        imagecopyresampled($image, $scale, 0, 0, 0, 0, 301, 30, 301, 30);

        $hashName = hash("sha256", rand() . time() . rand());
        $filePath = Yii::app()->params["reports"]["tmpFilesPath"] . "/" . $hashName . ".png";

        imagepng($image, $filePath, 0);
        imagedestroy($image);

        return $filePath;
    }

    /**
     * @param PHPRtfLite_Container_Section $section
     * @param $sectionNumber
     * @param $targets
     * @param PHPRtfLite_Container_Section|null $toc
     * @throws PHPRtfLite_Exception
     */
    private function _addFulfillmentDegreeChart(PHPRtfLite_Container_Section &$section, $sectionNumber, $targets, $toc=null) {
        $prm = new ReportManager();
        $data = $prm->getFulfillmentReportData($targets, $this->_language);
        $targetNumber = 1;

        foreach ($data as $target) {
            if ($toc) {
                $toc->writeHyperLink(
                    "#degree_" . $targetNumber,
                    "        " . $sectionNumber . "." . $targetNumber . ". " . $target["host"],
                    $this->textFont
                );

                $section->writeBookmark(
                    "degree_" . $targetNumber,
                    $sectionNumber . "." . $targetNumber . ". " . $target["host"],
                    $this->boldFont
                );

                if ($target["description"]) {
                    $font = new PHPRtfLite_Font($this->fontSize, $this->fontFamily, "#909090");

                    $toc->writeText(" / ", $this->textFont);
                    $toc->writeHyperLink(
                        "#degree_" . $targetNumber,
                        $target["description"],
                        $font
                    );
                    $toc->writeText("\n");

                    $section->writeText(" / ", $this->textFont);
                    $section->writeText($target["description"], $font);
                } else {
                    $toc->writeText("\n");
                }

                $targetNumber++;
            } else {
                $section->writeText($target["host"], $this->h3Font);

                if ($target["description"]) {
                    $section->writeText(" / ", $this->h3Font);
                    $section->writeText($target["description"], new PHPRtfLite_Font($this->h3Font->getSize(), $this->fontFamily, "#909090"));
                }
            }

            $section->writeText("\n");

            if (!count($target["controls"])) {
                $section->writeText("\n", $this->textFont);
                $section->writeText(Yii::t("app", "No checks.") . "\n\n", $this->textFont);
                continue;
            }

            $table = $section->addTable(PHPRtfLite_Table::ALIGN_LEFT);
            $table->addColumnsList([$this->docWidth * 0.28, $this->docWidth * 0.56, $this->docWidth * 0.16]);

            $row = 1;

            $table->addRow();
            $table->mergeCellRange(1, 2, 1, 3);

            $table->getCell($row, 1)->setCellPaddings($this->cellPadding, $this->cellPadding, $this->cellPadding, $this->cellPadding);
            $table->getCell($row, 1)->setVerticalAlignment(PHPRtfLite_Table_Cell::VERTICAL_ALIGN_CENTER);

            $table->getCell($row, 2)->setCellPaddings($this->cellPadding, $this->cellPadding, $this->cellPadding, $this->cellPadding);
            $table->getCell($row, 2)->setVerticalAlignment(PHPRtfLite_Table_Cell::VERTICAL_ALIGN_CENTER);

            $table->setFontForCellRange($this->boldFont, 1, 1, 1, 3);
            $table->setBackgroundForCellRange("#E0E0E0", 1, 1, 1, 3);
            $table->setBorderForCellRange($this->thinBorder, 1, 1, 1, 3);
            $table->setFirstRowAsHeader();

            $table->writeToCell($row, 1, Yii::t("app", "Control"));
            $table->writeToCell($row, 2, Yii::t("app", "Degree of Fulfillment"));

            $row++;

            usort($target["controls"], array("ReportManager", "sortControls"));

            foreach ($target["controls"] as $control) {
                $table->addRow();
                $table->getCell($row, 1)->setCellPaddings($this->cellPadding, $this->cellPadding, $this->cellPadding, $this->cellPadding);
                $table->getCell($row, 1)->setVerticalAlignment(PHPRtfLite_Table_Cell::VERTICAL_ALIGN_TOP);
                $table->getCell($row, 1)->setBorder($this->thinBorder);

                $table->getCell($row, 2)->setCellPaddings($this->cellPadding, $this->cellPadding, $this->cellPadding, $this->cellPadding);
                $table->getCell($row, 2)->setBorder($this->thinBorder);
                $table->getCell($row, 2)->setTextAlignment(PHPRtfLite_Table_Cell::TEXT_ALIGN_CENTER);

                $table->getCell($row, 3)->setCellPaddings($this->cellPadding, $this->cellPadding, $this->cellPadding, $this->cellPadding);
                $table->getCell($row, 3)->setBorder($this->thinBorder);

                $table->writeToCell($row, 1, $control["name"], $this->textFont);
                $table->addImageToCell($row, 2, $this->_generateFulfillmentImage($control["degree"]), null, $this->docWidth * 0.50);
                $table->writeToCell($row, 3, $control["degree"] . "%");

                $row++;
            }

            $table->setFontForCellRange($this->textFont, 1, 1, count($target["controls"]) + 1, 2);
        }
    }

    /**
     * Generate fulfillment degree report
     * @param Project $project
     * @param array $targets
     */
    public function generateFulfillmentDegreeReport(Project $project, $targets) {
        $section = $this->rtf->addSection();

        // footer
        $footer = $section->addFooter();
        $footer->writeText(Yii::t("app", "Degree of Fulfillment") . ": " . $project->name . ", ", $this->textFont, $this->noPar);
        $footer->writePlainRtfCode(
            "\\fs" . ($this->textFont->getSize() * 2) . " \\f" . $this->textFont->getFontIndex() . " " .
             Yii::t("app", "page {page} of {numPages}",
            array(
                "{page}"     => "{\\field{\\*\\fldinst {PAGE}}{\\fldrslt {1}}}",
                "{numPages}" => "{\\field{\\*\\fldinst {NUMPAGES}}{\\fldrslt {1}}}"
            )
        ));

        // title
        $section->writeText(Yii::t("app", "Degree of Fulfillment") . ": " . $project->name, $this->h1Font, $this->titlePar);
        $section->writeText("\n\n");

        $this->_addFulfillmentDegreeChart($section, null, $targets);

        $this->_fileName = Yii::t("app", "Degree of Fulfillment") . " - " . $project->name . " (" . $project->year . ").rtf";
        $hashName = hash("sha256", rand() . time() . $this->_fileName);
        $this->_filePath = Yii::app()->params["reports"]["tmpFilesPath"] . "/" . $hashName;

        $this->rtf->save($this->_filePath);
        $this->_generated = true;
    }

    /**
     * Generate risk matrix report.
     * @param Project $project
     * @param array $targets
     * @param RiskTemplate $template
     * @param array $matrix
     */
    public function generateRiskMatrixReport(Project $project, $targets, RiskTemplate $template, $matrix) {
        $section = $this->rtf->addSection();

        // footer
        $footer = $section->addFooter();
        $footer->writeText(Yii::t("app", "Risk Matrix") . ": " . $project->name . ", ", $this->textFont, $this->noPar);
        $footer->writePlainRtfCode(
            "\\fs" . ($this->textFont->getSize() * 2) . " \\f" . $this->textFont->getFontIndex() . " " .
             Yii::t("app", "page {page} of {numPages}",
            array(
                "{page}"     => "{\\field{\\*\\fldinst {PAGE}}{\\fldrslt {1}}}",
                "{numPages}" => "{\\field{\\*\\fldinst {NUMPAGES}}{\\fldrslt {1}}}"
            )
        ));

        // title
        $section->writeText(Yii::t("app", "Risk Matrix") . ": " . $project->name, $this->h1Font, $this->titlePar);
        $this->_addRiskMatrix($section, null, $targets, $template, $matrix);

        $this->_fileName = Yii::t("app", "Risk Matrix") . " - " . $project->name . " (" . $project->year . ").rtf";
        $hashName = hash("sha256", rand() . time() . $this->_fileName);
        $this->_filePath = Yii::app()->params["reports"]["tmpFilesPath"] . "/" . $hashName;

        $this->rtf->save($this->_filePath);
        $this->_generated = true;
    }

    /**
     * Add risk matrix
     * @param PHPRtfLite_Container_Section $section
     * @param int $sectionNumber
     * @param array $targets
     * @param RiskTemplate $template
     * @param array $matrix
     * @parma PHPRtfLite_Container_Section $toc
     */
    private function _addRiskMatrix(PHPRtfLite_Container_Section &$section, $sectionNumber, $targets, $template, $matrix, $toc=null) {
        if (!$template) {
            return;
        }

        $risks = RiskCategory::model()->with([
            "l10n" => [
                "joinType" => "LEFT JOIN",
                "on" => "language_id = :language_id",
                "params" => ["language_id" => $this->_language]
            ]
        ])->findAllByAttributes(
            ["risk_template_id" => $template->id],
            ["order" => "COALESCE(l10n.name, t.name) ASC"]
        );

        $rm = new ReportManager();
        $data = $rm->getRiskMatrixData($targets, $matrix, $risks, $this->_language);

        $section->writeText(Yii::t("app", "Risk Categories") . "\n", $this->h3Font, $this->noPar);
        
        $table = $section->addTable(PHPRtfLite_Table::ALIGN_LEFT);
        $table->addRows(count($risks) + 1);
        $table->addColumnsList(array( $this->docWidth * 0.11, $this->docWidth * 0.89 ));
        $table->setFontForCellRange($this->boldFont, 1, 1, 1, 2);
        $table->setBackgroundForCellRange("#E0E0E0", 1, 1, 1, 2);
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

        $table->writeToCell($row, 1, Yii::t("app", "Code"), $this->boldFont);
        $table->writeToCell($row, 2, Yii::t("app", "Risk Category"), $this->boldFont);

        $row++;
        $ctr = 0;

        foreach ($risks as $risk) {
            $ctr++;

            $table->writeToCell($row, 1, "R" . $ctr);
            $table->writeToCell($row, 2, $risk->localizedName);

            $row++;
        }

        $targetNumber = 1;

        $section->writeText(Yii::t("app", "Targets"), $this->h3Font, $this->h3Par);
        $section->writeText("\n\n", $this->textFont);

        foreach ($data as $target) {
            if ($toc) {
                $toc->writeHyperLink(
                    "#risk_matrix_" . $targetNumber,
                    "        " . $sectionNumber . "." . $targetNumber . ". " . $target["host"],
                    $this->textFont
                );

                $section->writeBookmark(
                    "risk_matrix_" . $targetNumber,
                    $sectionNumber . "." . $targetNumber . ". " . $target["host"],
                    $this->boldFont
                );

                if ($target["description"]) {
                    $font = new PHPRtfLite_Font($this->fontSize, $this->fontFamily, "#909090");

                    $toc->writeText(" / ", $this->textFont);
                    $toc->writeHyperLink(
                        "#risk_matrix_" . $targetNumber,
                        $target["description"],
                        $font
                    );
                    $toc->writeText("\n");

                    $section->writeText(" / ", $this->textFont);
                    $section->writeText($target["description"], $font);
                } else {
                    $toc->writeText("\n");
                }

                $targetNumber++;
            } else {
                $section->writeText($target["host"], $this->boldFont);

                if ($target["description"]) {
                    $section->writeText(" / ", $this->textFont);
                    $section->writeText($target["description"], new PHPRtfLite_Font($this->fontSize, $this->fontFamily, "#909090"));
                }
            }

            $section->writeText("\n");

            if (!$target["matrix"]) {
                $section->writeText("\n", $this->textFont);
                $section->writeText(Yii::t("app", "No checks.") . "\n\n", $this->textFont);
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

            $table->writeToCell(1, 1, "&uarr;<br>" . Yii::t("app", "Damage"));
            $table->writeToCell(5, 1, Yii::t("app", "Likelihood") . " &rarr;");

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

                    $bgColor = "#CCFFBB";

                    if (($row == 1 && $col >= 3) || ($row == 2 && $col >= 4) || ($row == 3 && $col >= 5)) {
                        $bgColor = "#FFBBBB";
                    }

                    $table->getCell($row, $col)->setBackgroundColor($bgColor);
                }
            }

            $matrix = $target["matrix"];

            for ($damage = 0; $damage < 4; $damage++) {
                for ($likelihood = 0; $likelihood < 4; $likelihood++) {
                    if (!isset($matrix[$damage][$likelihood])) {
                        continue;
                    }

                    $text = implode(", ", $matrix[$damage][$likelihood]);
                    $table->writeToCell(4 - $damage, $likelihood + 2, $text);
                }
            }
        }
    }

    /**
     * Compress report to zip file
     * @param null $attachments
     * @throws Exception
     */
    private function _compress($attachments=null) {
        if ($attachments === null) {
            return;
        }

        $originalName = $this->_fileName;
        $originalPath = $this->_filePath;
        $this->_fileName = $this->_fileName . ".zip";
        $hashName = hash("sha256", rand() . time() . $this->_fileName);
        $this->_filePath = Yii::app()->params["reports"]["tmpFilesPath"] . "/" . $hashName;

        $zip = new ZipArchive();

        if ($zip->open($this->_filePath, ZipArchive::CREATE) !== true) {
            throw new Exception("Unable to create report archive: {$this->_fileName}");
        }

        FileManager::zipFile($zip, $originalPath, $originalName);
        $zip->addEmptyDir("attachments");

        foreach ($attachments as $attachment) {
            $hostDir = "attachments/" . $attachment["host"];

            if (!$zip->locateName($hostDir)) {
                $zip->addEmptyDir($hostDir);
            }

            FileManager::zipFile($zip, $attachment["path"], $hostDir . "/" . $attachment["filename"]);
        }

        $zip->close();
    }

    /**
     * Generate vulnerability list
     * @param PHPRtfLite_Container_Section $section
     * @param int $sectionNumber
     * @param int $type
     * @param int|null $categoryId
     * @param PHPRtfLite_Container_Section|null $toc
     */
    private function _generateVulnerabilityList(&$section, $sectionNumber, $type=self::VULNERABILITY_LIST_NORMAL, $categoryId=null, $toc=null) {
        $data = $this->_data;
        $vulns = $data["data"];
        $infoLocation = $data["infoLocation"];
        $template = $data["template"];
        $fields = $data["fields"];
        $ratingImages = $this->_getRatingImages($template);

        $targetNumber = 1;

        foreach ($vulns as $target) {
            if (!$target["checkCount"]) {
                continue;
            }

            if ($type == self::VULNERABILITY_LIST_SEPARATE_TABLE &&
                !in_array($categoryId, $target["separate"]) ||
                $infoLocation == ProjectReportForm::INFO_LOCATION_SEPARATE_SECTION && $target["separateCount"] == $target["info"]
            ) {
                continue;
            }

            if ($type == self::VULNERABILITY_LIST_SEPARATE_SECTION && !$target["info"]) {
                continue;
            }

            if (
                $type == self::VULNERABILITY_LIST_NORMAL && (
                    $infoLocation == ProjectReportForm::INFO_LOCATION_SEPARATE_SECTION && $target["checkCount"] == $target["info"] + $target["separateCount"] ||
                    $target["checkCount"] == $target["separateCount"]
                )
            ) {
                continue;
            }

            $tableCount = 1;

            if ($type != self::VULNERABILITY_LIST_SEPARATE_SECTION && $infoLocation == ProjectReportForm::INFO_LOCATION_SEPARATE_TABLE && $target["info"]) {
                $tableCount = 2;
            }

            for ($tableNumber = 0; $tableNumber < $tableCount; $tableNumber++) {
                $subsectionNumber = substr($sectionNumber, strpos($sectionNumber, ".") + 1);

                $toc->writeHyperLink(
                    "#vulns_section_" . $subsectionNumber . "_" . $targetNumber,
                    "        " . $sectionNumber . "." . $targetNumber . ". " . $target["host"],
                    $this->textFont
                );

                $section->writeBookmark(
                    "vulns_section_" . $subsectionNumber . "_" . $targetNumber,
                    $sectionNumber . "." . $targetNumber . ". " . $target["host"],
                    $this->boldFont
                );

                if ($target["description"]) {
                    $font = new PHPRtfLite_Font($this->fontSize, $this->fontFamily, "#909090");

                    $toc->writeText(" / ", $this->textFont);
                    $toc->writeHyperLink(
                        "#vulns_section_" . $subsectionNumber . "_" . $targetNumber,
                        $target["description"],
                        $font
                    );

                    $section->writeText(" / ", $this->textFont);
                    $section->writeText($target["description"], $font);
                }

                if ($tableNumber == 1) {
                    $section->writeText(" - " . Yii::t("app", "Info Checks"), $this->textFont);
                }

                $section->writeText("\n", $this->textFont);
                $toc->writeText("\n", $this->textFont);

                $targetNumber++;

                $table = $section->addTable(PHPRtfLite_Table::ALIGN_LEFT);
                $table->addColumnsList(array($this->docWidth * 0.17, $this->docWidth * 0.83));

                $row = 1;

                foreach ($target["categories"] as $category) {
                    if (
                        $type == self::VULNERABILITY_LIST_SEPARATE_TABLE &&
                        (
                            $category["id"] != $categoryId ||
                            $infoLocation == ProjectReportForm::INFO_LOCATION_SEPARATE_SECTION && $category["info"] == $category["separate"] ||
                            $infoLocation == ProjectReportForm::INFO_LOCATION_SEPARATE_TABLE &&
                            (
                                $tableNumber == 0 && $category["separate"] == $category["info"] ||
                                $tableNumber == 1 && !$category["info"]
                            )
                        )
                    ) {
                        continue;
                    }

                    if ($type == self::VULNERABILITY_LIST_SEPARATE_SECTION && !$category["info"]) {
                        continue;
                    }

                    if (
                        $type == self::VULNERABILITY_LIST_NORMAL &&
                        (
                            $infoLocation == ProjectReportForm::INFO_LOCATION_SEPARATE_SECTION && $category["checkCount"] == $category["info"] + $category["separate"] ||
                            $category["checkCount"] == $category["separate"] ||
                            $infoLocation == ProjectReportForm::INFO_LOCATION_SEPARATE_TABLE &&
                            (
                                $tableNumber == 0 && $category["checkCount"] == $category["info"] + $category["separate"] ||
                                $tableNumber == 1 && !$category["info"]
                            )
                        )
                    ) {
                        continue;
                    }

                    $table->addRow();
                    $table->mergeCellRange($row, 1, $row, 2);

                    $table->getCell($row, 1)->setCellPaddings($this->cellPadding, $this->cellPadding, $this->cellPadding, $this->cellPadding);
                    $table->getCell($row, 1)->setBorder($this->thinBorder);
                    $table->setFontForCellRange($this->boldFont, $row, 1, $row, 1);
                    $table->setBackgroundForCellRange("#B0B0B0", $row, 1, $row, 1);
                    $table->writeToCell($row, 1, $category["name"]);

                    $row++;

                    foreach ($category["controls"] as $control) {
                        if (
                            $type == self::VULNERABILITY_LIST_SEPARATE_TABLE &&
                            (
                                !$control["separate"] ||
                                $infoLocation == ProjectReportForm::INFO_LOCATION_SEPARATE_SECTION && $control["info"] == $control["separate"] ||
                                $infoLocation == ProjectReportForm::INFO_LOCATION_SEPARATE_TABLE &&
                                (
                                    $tableNumber == 0 && $control["separate"] == $control["info"] ||
                                    $tableNumber == 1 && !$control["info"]
                                )
                            )
                        ) {
                            continue;
                        }

                        if ($type == self::VULNERABILITY_LIST_SEPARATE_SECTION && !$control["info"]) {
                            continue;
                        }

                        if (
                            $type == self::VULNERABILITY_LIST_NORMAL &&
                            (
                                $infoLocation == ProjectReportForm::INFO_LOCATION_SEPARATE_SECTION && $control["checkCount"] == $control["info"] + $control["separate"] ||
                                $control["checkCount"] == $control["separate"] ||
                                $infoLocation == ProjectReportForm::INFO_LOCATION_SEPARATE_TABLE &&
                                (
                                    $tableNumber == 0 && $control["checkCount"] == $control["info"] + $control["separate"] ||
                                    $tableNumber == 1 && !$control["info"]
                                )
                            )
                        ) {
                            continue;
                        }

                        $table->addRow();
                        $table->mergeCellRange($row, 1, $row, 2);

                        $table->getCell($row, 1)->setCellPaddings($this->cellPadding, $this->cellPadding, $this->cellPadding, $this->cellPadding);
                        $table->getCell($row, 1)->setBorder($this->thinBorder);
                        $table->setFontForCellRange($this->boldFont, $row, 1, $row, 1);
                        $table->setBackgroundForCellRange("#D0D0D0", $row, 1, $row, 1);
                        $table->writeToCell($row, 1, $control["name"]);

                        $row++;

                        foreach ($control["checks"] as $check) {
                            if (
                                $type == self::VULNERABILITY_LIST_SEPARATE_TABLE &&
                                (
                                    !$check["separate"] ||
                                    $infoLocation == ProjectReportForm::INFO_LOCATION_SEPARATE_SECTION && $check["info"] ||
                                    $infoLocation == ProjectReportForm::INFO_LOCATION_SEPARATE_TABLE &&
                                    (
                                        $tableNumber == 0 && $check["info"] ||
                                        $tableNumber == 1 && !$check["info"]
                                    )
                                )
                            ) {
                                continue;
                            }

                            if ($type == self::VULNERABILITY_LIST_SEPARATE_SECTION && !$check["info"]) {
                                continue;
                            }

                            if (
                                $type == self::VULNERABILITY_LIST_NORMAL &&
                                (
                                    $infoLocation == ProjectReportForm::INFO_LOCATION_SEPARATE_SECTION && $check["info"] ||
                                    $check["separate"] ||
                                    $infoLocation == ProjectReportForm::INFO_LOCATION_SEPARATE_TABLE &&
                                    (
                                        $tableNumber == 0 && $check["info"] ||
                                        $tableNumber == 1 && !$check["info"]
                                    )
                                )
                            ) {
                                continue;
                            }

                            $table->addRow();
                            $table->mergeCellRange($row, 1, $row, 2);

                            $table->getCell($row, 1)->setCellPaddings($this->cellPadding, $this->cellPadding, $this->cellPadding, $this->cellPadding);
                            $table->getCell($row, 1)->setBorder($this->thinBorder);
                            $table->setFontForCellRange($this->boldFont, $row, 1, $row, 1);
                            $table->setBackgroundForCellRange("#F0F0F0", $row, 1, $row, 1);

                            $table->getCell($row, 1)->writeBookmark(
                                "check_" . $target["id"] . "_" . $check["id"],
                                $check["name"]
                            );

                            $row++;

                            // reference info
                            $table->addRow();
                            $table->getCell($row, 1)->setCellPaddings($this->cellPadding, $this->cellPadding, $this->cellPadding, $this->cellPadding);
                            $table->getCell($row, 1)->setVerticalAlignment(PHPRtfLite_Table_Cell::VERTICAL_ALIGN_TOP);
                            $table->getCell($row, 1)->setBorder($this->thinBorder);
                            $table->getCell($row, 2)->setCellPaddings($this->cellPadding, $this->cellPadding, $this->cellPadding, $this->cellPadding);
                            $table->getCell($row, 2)->setBorder($this->thinBorder);

                            $table->writeToCell($row, 1, Yii::t("app", "Reference"));

                            $reference = $check["reference"] . ($check["referenceCode"] ? "-" . $check["referenceCode"] : "");
                            $referenceUrl = "";

                            if ($check["referenceCode"] && $check["referenceCodeUrl"]) {
                                $referenceUrl = $check["referenceCodeUrl"];
                            } else if ($check["referenceUrl"]) {
                                $referenceUrl = $check["referenceUrl"];
                            }

                            if ($referenceUrl) {
                                $table->getCell($row, 2)->writeHyperLink($referenceUrl, $reference, $this->linkFont);
                            } else {
                                $table->writeToCell($row, 2, $reference);
                            }

                            $row++;

                            if ($check["tableResult"]) {
                                $table->addRow();
                                $table->getCell($row, 1)->setCellPaddings($this->cellPadding, $this->cellPadding, $this->cellPadding, $this->cellPadding);
                                $table->getCell($row, 1)->setVerticalAlignment(PHPRtfLite_Table_Cell::VERTICAL_ALIGN_TOP);
                                $table->getCell($row, 1)->setBorder($this->thinBorder);
                                $table->getCell($row, 2)->setCellPaddings($this->cellPadding, $this->cellPadding, $this->cellPadding, $this->cellPadding);
                                $table->getCell($row, 2)->setBorder($this->thinBorderBR);

                                if ($check["result"]) {
                                    $table->mergeCellRange($row - 1, 1, $row, 1);
                                } else {
                                    $table->writeToCell($row, 1, Yii::t("app", "Result"));
                                }

                                $tableResult = new ResultTable();
                                $tableResult->parse($check["tableResult"]);

                                foreach ($tableResult->getTables() as $tResult) {
                                    $nestedTable = $table->getCell($row, 2)->addTable();
                                    $nestedTable->addRows($tResult["rowCount"] + 1);

                                    $columnWidths = array();
                                    $tableWidth = $this->docWidth * 0.83 - $this->cellPadding * 2;

                                    foreach ($tResult["columns"] as $column) {
                                        $columnWidths[] = (float)$column["width"] * $tableWidth;
                                    }

                                    $nestedTable->addColumnsList($columnWidths);

                                    $nestedTable->setFontForCellRange($this->boldFont, 1, 1, 1, $tResult["columnCount"]);
                                    $nestedTable->setBackgroundForCellRange("#E0E0E0", 1, 1, 1, $tResult["columnCount"]);
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

                                        $nestedTable->writeToCell($nestedRow, $nestedColumn, $column["name"]);
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

                            if (isset($check["fields"])) {
                                foreach ($check["fields"] as $field) {
                                    if (!in_array($field["name"], $fields)) {
                                        continue;
                                    }

                                    $table->addRow();
                                    $table->getCell($row, 1)->setCellPaddings($this->cellPadding, $this->cellPadding, $this->cellPadding, $this->cellPadding);
                                    $table->getCell($row, 1)->setVerticalAlignment(PHPRtfLite_Table_Cell::VERTICAL_ALIGN_TOP);
                                    $table->getCell($row, 1)->setBorder($this->thinBorder);
                                    $table->getCell($row, 2)->setCellPaddings($this->cellPadding, $this->cellPadding, $this->cellPadding, $this->cellPadding);
                                    $table->getCell($row, 2)->setBorder($this->thinBorder);

                                    $table->writeToCell($row, 1, $field["title"]);

                                    if (Utils::isHtml($field["value"])) {
                                        $this->renderText($table->getCell($row, 2), $field["value"], false);
                                    } else {
                                        $table->writeToCell($row, 2, $field["value"]);
                                    }

                                    $row++;
                                }
                            }

                            if ($check["solutions"]) {
                                $table->addRows(count($check["solutions"]));

                                $table->mergeCellRange($row, 1, $row + count($check["solutions"]) - 1, 1);

                                $table->getCell($row, 1)->setCellPaddings($this->cellPadding, $this->cellPadding, $this->cellPadding, $this->cellPadding);
                                $table->getCell($row, 1)->setVerticalAlignment(PHPRtfLite_Table_Cell::VERTICAL_ALIGN_TOP);
                                $table->getCell($row, 1)->setBorder($this->thinBorder);
                                $table->writeToCell($row, 1, Yii::t("app", "Solutions"));

                                foreach ($check["solutions"] as $solution) {
                                    $table->getCell($row, 1)->setBorder($this->thinBorder);
                                    $table->getCell($row, 2)->setCellPaddings($this->cellPadding, $this->cellPadding, $this->cellPadding, $this->cellPadding);
                                    $table->getCell($row, 2)->setBorder($this->thinBorder);

                                    $cell = $table->getCell($row, 2);
                                    $this->renderText($cell, $solution, false);

                                    $row++;
                                }
                            }

                            if ($check["images"]) {
                                $table->addRows(count($check["images"]));

                                $table->mergeCellRange($row, 1, $row + count($check["images"]) - 1, 1);

                                $table->getCell($row, 1)->setCellPaddings($this->cellPadding, $this->cellPadding, $this->cellPadding, $this->cellPadding);
                                $table->getCell($row, 1)->setVerticalAlignment(PHPRtfLite_Table_Cell::VERTICAL_ALIGN_TOP);
                                $table->getCell($row, 1)->setBorder($this->thinBorder);
                                $table->writeToCell($row, 1, Yii::t("app", "Attachments"));

                                foreach ($check["images"] as $image) {
                                    $table->getCell($row, 1)->setBorder($this->thinBorder);
                                    $table->getCell($row, 2)->setCellPaddings($this->cellPadding, $this->cellPadding, $this->cellPadding, $this->cellPadding);
                                    $table->getCell($row, 2)->setBorder($this->thinBorder);

                                    $table->writeToCell($row, 2, $image["title"]);
                                    $table->addImageToCell($row, 2, $image["image"], new PHPRtfLite_ParFormat(), $this->docWidth * 0.78);

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
     * Add info check list
     * @param PHPRtfLite_Container_Section $section
     * @param int $sectionNumber
     * @param PHPRtfLite_Container_Section $toc
     */
    private function _addInfoCheckList(&$section, $sectionNumber, $toc) {
        $this->_generateVulnerabilityList($section, $sectionNumber, self::VULNERABILITY_LIST_SEPARATE_SECTION, null, $toc);
    }

    /**
     * Generate report
     */
    public function generate() {
        $data = $this->_data;
        $project = $data["project"];
        $sections = $data["sections"];
        $template = $this->_template;
        $this->setup($data["pageMargin"], $data["cellPadding"], $data["fontSize"], $data["fontFamily"]);
        $section = $this->rtf->addSection();

        // footer
        $footer = $section->addFooter();
        $footer->writeText($template->localizedFooter, $this->footerFont, $this->noPar);
        $footer->writeText(Yii::t("app", "Penetration Test Report") . ": " . $project->name . " / " . $project->year . ", ", $this->footerFont, $this->noPar);
        $footer->writePlainRtfCode(
            "\\fs" . ($this->footerFont->getSize() * 2) . " \\f" . $this->footerFont->getFontIndex() . " " .
             Yii::t("app", "page {page} of {numPages}",
            array(
                "{page}"     => "{\\field{\\*\\fldinst {PAGE}}{\\fldrslt {1}}}",
                "{numPages}" => "{\\field{\\*\\fldinst {NUMPAGES}}{\\fldrslt {1}}}"
            )
        ));

        // title
        if ($data["title"]) {
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
        foreach ($sections as $s) {
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
                case ReportSection::TYPE_APPENDIX:
                    break;

                case ReportSection::TYPE_SUMMARY:
                    if ($summary) {
                        $this->renderText($section, $summary->localizedSummary . "<br>");
                    }

                    break;

                case ReportSection::TYPE_CHART_SECURITY_LEVEL:
                    $this->_addSecurityLevelChart($section, $summary);
                    break;

                case ReportSection::TYPE_CHART_VULNERABILITY_DISTRIBUTION:
                    $this->_addVulnDistributionChart($section);
                    break;

                case ReportSection::TYPE_CHART_DEGREE_OF_FULFILLMENT:
                    $this->_addFulfillmentDegreeChart($section, $sectionNumber, $data["targets"], $toc);
                    break;

                case ReportSection::TYPE_RISK_MATRIX:
                    $this->_addRiskMatrix(
                        $section,
                        $sectionNumber,
                        $data["targets"],
                        $data["risk"]["template"],
                        $data["risk"]["matrix"],
                        $toc
                    );

                    break;

                case ReportSection::TYPE_REDUCED_VULNERABILITY_LIST:
                    $this->_addReducedVulnerabilityList($section);
                    break;

                case ReportSection::TYPE_VULNERABILITIES:
                    $this->_addVulnerabilityList($section, $sectionNumber, $toc);
                    break;

                case ReportSection::TYPE_ATTACHMENTS:
                    $this->_addAttachmentList($section);
                    break;

                case ReportSection::TYPE_INFO_CHECKS:
                    $this->_addInfoCheckList($section, $sectionNumber, $toc);
                    break;
            }

            $section->insertPageBreak();
            $sectionNumber++;
        }

        $this->_fileName = Yii::t("app", "Penetration Test Report") . " - " . $project->name . " (" . $project->year . ").rtf";
        $hashName = hash("sha256", rand() . time() . $this->_fileName);
        $this->_filePath = Yii::app()->params["reports"]["tmpFilesPath"] . "/" . $hashName;

        $this->rtf->save($this->_filePath);

        if ($data["fileType"] === ProjectReportForm::FILE_TYPE_ZIP && $data["attachments"]) {
            $this->_compress($data["attachments"]);
        }

        $this->_generated = true;
    }

    /**
     * Generate time tracking report
     * @param Project $project
     * @throws PHPRtfLite_Exception
     */
    public function generateTimeTrackingReport(Project $project) {
        $records = $project->timeRecords;
        $section = $this->rtf->addSection();

        // main title
        $section->writeText(Yii::t("app", "Time Tracking Report"), $this->h2Font, $this->centerTitlePar);
        $section->writeText(" ", $this->h2Font, $this->noPar);

        $table = $section->addTable(PHPRtfLite_Table::ALIGN_CENTER);
        $table->addColumnsList(array($this->docWidth * 0.3, $this->docWidth * 0.5, $this->docWidth * 0.2));
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

        $table->getCell($row, 1)->writeText(Yii::t("app", "Time Added"));
        $table->getCell($row, 2)->writeText(Yii::t("app", "Description"));
        $table->getCell($row, 3)->writeText(Yii::t("app", "Time Logged"));

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
            $table->getCell($row, 3)->writeText($record->hours . " h", $this->textFont, $this->noPar);
            $row++;
        }

        // total hours
        $section->writeText(Yii::t("app", "Summary") . ": " . $project->trackedTime . " h", $this->h3Font, $this->rightPar);

        $this->_fileName = Yii::t("app", "Time Tracking Report") . " - " . $project->name . " (" . $project->year . ").rtf";
        $hashName = hash("sha256", rand() . time() . $this->_fileName);
        $this->_filePath = Yii::app()->params["reports"]["tmpFilesPath"] . "/" . $hashName;

        $this->rtf->save($this->_filePath);
        $this->_generated = true;
    }

    /**
     * Generate comparison report
     * @param Project $project1
     * @param Project $project2
     * @throws PHPRtfLite_Exception
     */
    public function generateComparisonReport(Project $project1, Project $project2) {
        $rm = new ReportManager();
        $targetsData = $rm->getComparisonReportData($project1, $project2);
        
        $section = $this->rtf->addSection();

        // footer
        $footer = $section->addFooter();
        $footer->writeText(Yii::t("app", "Projects Comparison") . ", ", $this->textFont, $this->noPar);
        $footer->writePlainRtfCode(
            "\\fs" . ($this->textFont->getSize() * 2) . " \\f" . $this->textFont->getFontIndex() . " " .
             Yii::t("app", "page {page} of {numPages}",
            array(
                "{page}" => "{\\field{\\*\\fldinst {PAGE}}{\\fldrslt {1}}}",
                "{numPages}" => "{\\field{\\*\\fldinst {NUMPAGES}}{\\fldrslt {1}}}"
            )
        ));

        // title
        $section->writeText(Yii::t("app", "Projects Comparison"), $this->h1Font, $this->titlePar);

        // detailed summary
        $section->writeText(Yii::t("app", "Target Comparison") . "<br>", $this->h3Font, $this->noPar);
        $table = $section->addTable(PHPRtfLite_Table::ALIGN_LEFT);

        $table->addRows(count($targetsData) + 1);
        $table->addColumnsList(array( $this->docWidth * 0.33, $this->docWidth * 0.33, $this->docWidth * 0.34 ));
        $table->setFontForCellRange($this->boldFont, 1, 1, 1, 3);
        $table->setBackgroundForCellRange("#E0E0E0", 1, 1, 1, 3);
        $table->setFontForCellRange($this->textFont, 2, 1, count($targetsData) + 1, 3);
        $table->setBorderForCellRange($this->thinBorder, 1, 1, count($targetsData) + 1, 3);
        $table->setFirstRowAsHeader();

        // set paddings
        for ($row = 1; $row <= count($targetsData) + 1; $row++) {
            for ($col = 1; $col <= 3; $col++) {
                $table->getCell($row, $col)->setCellPaddings(
                    $this->cellPadding,
                    $this->cellPadding,
                    $this->cellPadding,
                    $this->cellPadding
                );

                $table->getCell($row, $col)->setVerticalAlignment(PHPRtfLite_Table_Cell::VERTICAL_ALIGN_CENTER);
            }
        }

        $table->writeToCell(1, 1, Yii::t("app", "Target"));
        $table->writeToCell(1, 2, $project1->name . " (" . $project1->year . ")");
        $table->writeToCell(1, 3, $project2->name . " (" . $project2->year . ")");

        $row = 2;
        $system = System::model()->findByPk(1);

        foreach ($targetsData as $target) {
            $table->writeToCell($row, 1, $target["host"]);
            $table->addImageToCell($row, 2, $this->generateRatingImage($target["ratings"][0]), null, $this->docWidth * 0.30);
            $table->addImageToCell($row, 3, $this->generateRatingImage($target["ratings"][1]), null, $this->docWidth * 0.30);

            $table->getCell($row, 2)->setTextAlignment(PHPRtfLite_Table_Cell::TEXT_ALIGN_CENTER);
            $table->getCell($row, 3)->setTextAlignment(PHPRtfLite_Table_Cell::TEXT_ALIGN_CENTER);

            $row++;
        }

        $this->_fileName = Yii::t("app", "Projects Comparison") . ".rtf";
        $hashName = hash("sha256", rand() . time() . $this->_fileName);
        $this->_filePath = Yii::app()->params["reports"]["tmpFilesPath"] . "/" . $hashName;

        $this->rtf->save($this->_filePath);
        $this->_generated = true;
    }

}
