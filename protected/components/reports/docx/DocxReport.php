<?php

/**
 * Exceptions
 */
class EmptyScopeStack extends Exception {};
class InvalidScopeName extends Exception {};

/**
 * DOCX report plugin
 */
class DocxReport extends ReportPlugin {
    /**
     * Constants
     */
    const TAG_LIST = "list";
    const TAG_VAR = "var";
    const TAG_IF = "if";

    /**
     * @var VariableScopeStack scope stack
     */
    private $_scope = null;

    /**
     * Constructor
     * @param ReportTemplate $template
     * @param array $data
     */
    public function __construct(ReportTemplate $template, $data) {
        parent::__construct($template, $data);
        $project = $data["project"];

        $this->_fileName = sprintf(
            "%s - %s (%s).docx",
            Yii::t("app", "Penetration Test Report"),
            $project->name,
            $project->year
        );

        $this->_filePath = Yii::app()->params["tmpPath"] . "/" . hash("sha256", time() . rand() . $this->_fileName);
        $this->_scope = new VariableScopeStack($this->_data);
        $this->_scope->push(VariableScope::SCOPE_PROJECT, $this->_data["project"]);
    }

    /**
     * Insert static variables
     * @param $template
     * @return mixed
     */
    private function _insertStaticVariables($template) {
        $project = $this->_data["project"];

        $adminName = Yii::t("app", "N/A");
        $adminEmail = $adminName;

        if ($project->projectUsers) {
            foreach ($project->projectUsers as $user) {
                if ($user->admin) {
                    $adminName = $user->user->name;
                    $adminEmail = $user->user->email;
                    break;
                }
            }
        }

        $auditor = User::model()->findByPk(Yii::app()->user->id);

        $vars = array(
            "high_check_count" => $this->_data["checksHigh"],
            "med_check_count" => $this->_data["checksMed"],
            "low_check_count" => $this->_data["checksLow"],
            "info_check_count" => $this->_data["checksInfo"],
            "target_count" => count($this->_data["targets"]),
            "check_count" => $this->_data["checks"],
            "start_date" => implode(".", array_reverse(explode("-", $project->start_date))),
            "admin_name" => $adminName,
            "admin_email" => $adminEmail,
            "auditor_name" => $auditor->name,
            "auditor_email" => $auditor->email,
            "company" => $project->client->name,
            "project" => $project->name,
            "year" => $project->year,
            "deadline" => implode(".", array_reverse(explode("-", $project->deadline))),
            "rating" => sprintf("%.2f", $this->_data["rating"]),
            "date" => date("d.m.Y"),
            "time" => date("H:i:s"),
        );

        foreach ($vars as $key => $val) {
            $template = preg_replace("#\\{.{0,100}$key.{0,100}\\}#s", $val, $template);
        }

        return $template;
    }

    /**
     * Execute conditional blocks
     * @param DOMDocument $xml
     * @param DOMElement $context
     */
    private function _executeConditions(DOMDocument $xml, DOMElement $context) {
        while (true) {
            $xpath = new DOMXPath($xml);
            $xpath->registerNamespace("w", $xml->documentElement->lookupNamespaceUri("w"));
            $query = sprintf(".//w:sdt/w:sdtPr/w:tag[starts-with(@w:val, \"%s:\")]", self::TAG_IF);
            $conditions = $xpath->query($query, $context);

            if (!$conditions->length) {
                break;
            }

            $condition = $conditions->item(0);
            $blockBody = $condition->parentNode->parentNode;
            $conditionText = mb_substr($condition->getAttribute("w:val"), strlen(self::TAG_IF) + 1);
            $evaluator = new ConditionEvaluator($conditionText, $this->_scope->get());

            if ($evaluator->evaluate()) {
                // cut placeholder
                $blockBody->removeChild($condition->parentNode);
            } else {
                // cut the whole container
                $blockBody->parentNode->removeChild($blockBody);
            }
        }
    }

    /**
     * Expand lists
     * @param DOMDocument $xml
     * @param DOMElement $context
     * @throws Exception
     */
    private function _expandLists(DOMDocument $xml, DOMElement $context) {
        while (true) {
            $xpath = new DOMXPath($xml);
            $xpath->registerNamespace("w", $xml->documentElement->lookupNamespaceUri("w"));
            $query = sprintf(".//w:sdt/w:sdtPr/w:tag[starts-with(@w:val, \"%s:\")]", self::TAG_LIST);
            $lists = $xpath->query($query, $context);

            if (!$lists->length) {
                break;
            }

            $list = $lists->item(0);
            $name = mb_substr($list->getAttribute("w:val"), strlen(self::TAG_LIST) + 1);
            $scope = null;
            $filters = array();

            if (mb_strpos($name, ".") !== false) {
                $data = explode(".", $name);

                if (count($data) > 2) {
                    throw new Exception(Yii::t("app", "Only one scope level is supported."));
                }

                list($scope, $name) = $data;
            }

            if (mb_strpos($name, "|") !== false) {
                $data = explode("|", $name);
                $name = $data[0];

                foreach (array_slice($data, 1) as $pipe) {
                    $colon = mb_strpos($pipe, ":");

                    if ($colon === false) {
                        throw new Exception(Yii::t("app", "Invalid pipe operation: {pipe}.", array(
                            "{pipe}" => $pipe
                        )));
                    }

                    $operation = mb_substr($pipe, 0, $colon);

                    if ($operation == "filter") {
                        $filters[] = mb_substr($pipe, $colon + 1);
                    } else {
                        throw new Exception(Yii::t("app", "Unknown pipe operation: {operation}.", array(
                            "{operation}" => $operation
                        )));
                    }
                }
            }

            $blockBody = $list->parentNode->parentNode;
            $blockContent = $xpath->query("w:sdtContent", $blockBody)->item(0);
            $container = $xml->createElementNS($xml->lookupNamespaceUri("w"), "w:sdt");
            $container->appendChild($blockContent->cloneNode(true));

            $localScope = $this->_scope->get($scope);
            $objects = $localScope->getList($name, $filters);
            $current = $blockBody;

            foreach ($objects as $object) {
                $this->_scope->push($name, $object);
                $objectNode = $container->cloneNode(true);

                if ($current->nextSibling) {
                    $current->parentNode->insertBefore($objectNode, $current->nextSibling);
                } else {
                    $current->parentNode->appendChild($objectNode);
                }

                $this->_fillTemplate($xml, $objectNode);
                $this->_scope->pop();
                $current = $objectNode;
            }

            $blockBody->parentNode->removeChild($blockBody);
        }
    }

    /**
     * Get text run blocks array
     * @param $text
     * @param array $attributes
     * @return array
     */
    private function _getTextRunBlocks($text, $attributes=array()) {
        $block = array();
        $blocks = array();
        $pos = 0;
        $text = str_replace("&nbsp;", " ", $text);

        while (true) {
            $pos = mb_strpos($text, "<", $pos);

            if ($pos === false) {
                break;
            }

            if (mb_substr($text, $pos, 4) == "<br>" || mb_substr($text, $pos, 5) == "<br/>" || mb_substr($text, $pos, 6) == "<br />") {
                $endPos = mb_strpos($text, ">", $pos);
                $subText = mb_substr($text, 0, $pos);
                $block[] = $subText;

                $text = mb_substr($text, $endPos + 1);

                if (!$text) {
                    $text = "";
                }

                $pos = 0;
            } else if (mb_substr($text, $pos, 3) == "<b>") {
                $endPos = mb_strpos($text, "</b>", $pos);

                if ($endPos === false) {
                    break;
                }

                $block[] = mb_substr($text, 0, $pos);
                $blocks[] = array(
                    "attributes" => $attributes,
                    "block" => $block,
                );

                $subText = mb_substr($text, $pos + 3, $endPos - $pos - 3);
                $merged = array_merge($attributes, array("bold"));

                foreach ($this->_getTextRunBlocks($subText, $merged) as $run) {
                    $blocks[] = array(
                        "attributes" => $run["attributes"],
                        "block" => $run["block"],
                    );
                }

                $block = array();
                $text = mb_substr($text, $endPos + 4);

                if (!$text) {
                    $text = null;
                }

                $pos = 0;
            } else if (mb_substr($text, $pos, 4) == "<em>") {
                $endPos = mb_strpos($text, "</em>", $pos);

                if ($endPos === false) {
                    break;
                }

                $block[] = mb_substr($text, 0, $pos);
                $blocks[] = array(
                    "attributes" => $attributes,
                    "block" => $block,
                );

                $subText = mb_substr($text, $pos + 4, $endPos - $pos - 4);
                $merged = array_merge($attributes, array("italic"));

                foreach ($this->_getTextRunBlocks($subText, $merged) as $run) {
                    $blocks[] = array(
                        "attributes" => $run["attributes"],
                        "block" => $run["block"],
                    );
                }

                $block = array();
                $text = mb_substr($text, $endPos + 5);

                if (!$text) {
                    $text = null;
                }

                $pos = 0;
            } else if (mb_substr($text, $pos, 3) == "<u>") {
                $endPos = mb_strpos($text, "</u>", $pos);

                if ($endPos === false) {
                    break;
                }

                $block[] = mb_substr($text, 0, $pos);
                $blocks[] = array(
                    "attributes" => $attributes,
                    "block" => $block,
                );

                $subText = mb_substr($text, $pos + 3, $endPos - $pos - 3);
                $merged = array_merge($attributes, array("underline"));

                foreach ($this->_getTextRunBlocks($subText, $merged) as $run) {
                    $blocks[] = array(
                        "attributes" => $run["attributes"],
                        "block" => $run["block"],
                    );
                }

                $block = array();
                $text = mb_substr($text, $endPos + 4);

                if (!$text) {
                    $text = null;
                }

                $pos = 0;
            } else {
                $pos = $pos + 1;
            }
        }

        if ($text !== null) {
            $block[] = $text;
        }

        if ($block) {
            $blocks[] = array(
                "attributes" => $attributes,
                "block" => $block,
            );
        }

        return $blocks;
    }

    /**
     * Insert text
     * @param DOMDocument $xml
     * @param DOMNode $parentNode
     * @param $text
     */
    private function _insertText(DOMDocument $xml, DOMNode $parentNode, $text) {
        $ns = $xml->lookupNamespaceUri("w");

        // ul, ol, li
        $runs = $this->_getTextRunBlocks($text);

        foreach ($runs as $run) {
            $textRun = $xml->createElementNS($ns, "w:r");
            $runProperties = $xml->createElementNS($ns, "w:rPr");

            if (in_array("bold", $run["attributes"])) {
                $runProperties->appendChild($xml->createElementNS($ns, "w:b"));
            }

            if (in_array("italic", $run["attributes"])) {
                $runProperties->appendChild($xml->createElementNS($ns, "w:i"));
            }

            if (in_array("underline", $run["attributes"])) {
                $underline = $xml->createElementNS($ns, "w:u");
                $underline->setAttribute("w:val", "single");
                $runProperties->appendChild($underline);
            }

            if ($run["attributes"]) {
                $textRun->appendChild($runProperties);
            }

            $counter = 0;

            foreach ($run["block"] as $textBlock) {
                $textNode = $xml->createElementNS($ns, "w:t");
                $textNode->setAttribute("xml:space", "preserve");
                $textNode->appendChild($xml->createTextNode($textBlock));
                $textRun->appendChild($textNode);

                $counter++;

                if ($counter < count($run["block"])) {
                    $textRun->appendChild($xml->createElementNS($ns, "w:br"));
                }
            }

            $parentNode->appendChild($textRun);
        }
    }

    /**
     * Insert variable values
     * @param DOMDocument $xml
     * @param DOMElement $context
     */
    private function _insertVariables(DOMDocument $xml, DOMElement $context=null) {
        while (true) {
            $xpath = new DOMXPath($xml);
            $xpath->registerNamespace("w", $xml->documentElement->lookupNamespaceUri("w"));
            $query = sprintf(".//w:sdt/w:sdtPr/w:tag[starts-with(@w:val, \"%s:\")]", self::TAG_VAR);
            $vars = $xpath->query($query, $context);

            if (!$vars->length) {
                break;
            }

            $block = $vars->item(0);
            $blockBody = $block->parentNode->parentNode;
            $blockContent = $xpath->query("w:sdtContent", $blockBody)->item(0);
            $textRun = $xpath->query(".//w:r", $blockContent)->item(0);

            $name = mb_substr($block->getAttribute("w:val"), strlen(self::TAG_VAR) + 1);
            $evaluator = new VariableEvaluator($name, $this->_scope->get());
            $value = $evaluator->evaluate();
            $this->_insertText($xml, $textRun->parentNode, $value);

            // remove placeholders
            $textRun->parentNode->removeChild($textRun);
            $blockBody->removeChild($block->parentNode);
        }

        // remove placeholer styles
        $xpath = new DOMXPath($xml);
        $xpath->registerNamespace("w", $xml->documentElement->lookupNamespaceUri("w"));
        $styles = $xpath->query(".//w:rStyle[starts-with(@w:val, \"PlaceholderText\")]", $context);

        foreach ($styles as $style) {
            $style->parentNode->removeChild($style);
        }
    }

    /**
     * Fill template data
     * @param DOMDocument $xml
     * @param DOMElement $context
     */
    private function _fillTemplate(DOMDocument $xml, DOMElement $context=null) {
        $this->_expandLists($xml, $context);
        $this->_executeConditions($xml, $context);
        $this->_insertVariables($xml, $context);
    }

    /**
     * Process template
     * @param string $template
     * @return string
     */
    private function _processTemplate($template) {
        $template = $this->_insertStaticVariables($template);

        $xml = new DOMDocument();
        $xml->loadXML($template);
        $this->_fillTemplate($xml);

        return $xml->saveXML();
    }

    /**
     * Generate report
     */
    public function generate() {
        $tmpDir = Yii::app()->params["tmpPath"] . "/" . hash("sha256", time() . rand() . $this->_fileName);
        FileManager::createDir($tmpDir, 0777);
        $exception = null;

        try {
            $templatePath = Yii::app()->params["reports"]["file"]["path"] . "/" . $this->_template->file_path;
            $zip = new ZipArchive();

            if (!$zip->open($templatePath)) {
                throw new Exception("Error opening ZIP archive: " . $templatePath);
            }

            if (!$zip->extractTo($tmpDir)) {
                throw new Exception("Error extracting files to " . $tmpDir);
            }

            $zip->close();

            $documentXml = $tmpDir . "/word/document.xml";
            $template = @file_get_contents($documentXml);
            @file_put_contents($documentXml, $this->_processTemplate($template));

            $zip = new ZipArchive();

            if (file_exists($this->_filePath)) {
                FileManager::unlink($this->_filePath);
            }

            if (!$zip->open($this->_filePath, ZipArchive::CREATE)) {
                throw new Exception(Yii::t("app", "Unable to create the report."));
            }

            FileManager::zipDirectory($zip, $tmpDir);
            $zip->close();
        } catch (Exception $e) {
            $exception = $e;
        }

        // finally
        FileManager::rmDir($tmpDir);

        if ($exception) {
            throw $exception;
        }

        $this->_generated = true;
    }
}
