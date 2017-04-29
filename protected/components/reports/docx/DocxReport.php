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
     * @var string template dir
     */
    private $_templateDir = null;

    /**
     * @var VariableScopeStack scope stack
     */
    private $_scope = null;

    /**
     * @var int numbering id
     */
    private $_numberingId = 1;

    /**
     * @var int abstract numbering id
     */
    private $_abstractNumberingId = 0;

    /**
     * @var array lists
     */
    private $_lists = array();

    /**
     * @var int relation id
     */
    private $_relationId = 1;

    /**
     * @var int drawing object non-visual property id
     */
    private $_drawingObjectPropertyId = 1;

    /**
     * @var int non-visual drawing property id
     */
    private $_nonVisualDrawingPropertyId = 0;

    /**
     * @var int document width
     */
    private $_documentWidth = 0;

    /**
     * @var array images
     */
    private $_images = array();

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

        FileManager::createDir(Yii::app()->params["reports"]["tmpFilesPath"], 0777);
        $this->_filePath = Yii::app()->params["reports"]["tmpFilesPath"] . "/" . hash("sha256", time() . rand() . $this->_fileName);
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
     * @param bool $replaceLineFeeds
     * @return array
     */
    private function _getTextRunBlocks($text, $attributes=array(), $replaceLineFeeds=false) {
        $block = array();
        $blocks = array();
        $pos = 0;
        $text = str_replace("&nbsp;", " ", $text);

        if ($replaceLineFeeds) {
            $text = str_replace("\n", "<br>", $text);
        }

        while (true) {
            $pos = mb_strpos($text, "<", $pos);
            $found = false;

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
                $found = true;
            } else {
                $tags = array(
                    "b" => "bold",
                    "strong" => "bold",
                    "em" => "italic",
                    "i" => "italic",
                    "u" => "underline",
                );

                foreach ($tags as $tag => $attribute) {
                    $tagLength = strlen($tag) + 2;

                    if (mb_substr($text, $pos, $tagLength) == "<" . $tag . ">") {
                        $endPos = mb_strpos($text, "</" . $tag . ">", $pos);

                        if ($endPos === false) {
                            break;
                        }

                        $block[] = mb_substr($text, 0, $pos);
                        $blocks[] = array(
                            "attributes" => $attributes,
                            "block" => $block,
                        );

                        $subText = mb_substr($text, $pos + $tagLength, $endPos - $pos - $tagLength);
                        $merged = array_merge($attributes, array($attribute));

                        foreach ($this->_getTextRunBlocks($subText, $merged, $replaceLineFeeds) as $run) {
                            $blocks[] = array(
                                "attributes" => $run["attributes"],
                                "block" => $run["block"],
                            );
                        }

                        $block = array();
                        $text = mb_substr($text, $endPos + $tagLength + 1);

                        if (!$text) {
                            $text = null;
                        }

                        $pos = 0;
                        $found = true;

                        break;
                    }
                }
            }

            if (!$found) {
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
     * Get paragraphs from text
     * @param $text
     * @param bool $replaceLineFeeds
     * @return array
     */
    private function _getParagraphs($text, $replaceLineFeeds=false) {
        $paragraphs = array();
        $pos = 0;

        while (true) {
            $pos = mb_strpos($text, "<", $pos);

            if ($pos === false) {
                break;
            }

            $tags = array(
                "ul" => "bullet",
                "ol" => "decimal",
            );

            $found = false;

            foreach ($tags as $tag => $type) {
                $tagLength = strlen($tag) + 2;

                if (mb_substr($text, $pos, $tagLength) == "<" . $tag . ">") {
                    $endPos = mb_strpos($text, "</" . $tag . ">", $pos);

                    if ($endPos === false) {
                        break;
                    }

                    $paragraphs[] = array(
                        "attributes" => array(),
                        "runs" => $this->_getTextRunBlocks(mb_substr($text, 0, $pos), array(), $replaceLineFeeds),
                    );

                    $listText = mb_substr($text, $pos + $tagLength, $endPos - $pos - $tagLength);
                    $liPos = 0;

                    while (true) {
                        $liPos = mb_strpos($listText, "<", $liPos);

                        if ($liPos === false) {
                            break;
                        }

                        if (mb_substr($listText, $liPos, 4) == "<li>") {
                            $endLiPos = mb_strpos($listText, "</li>", $liPos);

                            if ($endLiPos === false) {
                                break;
                            }

                            $paragraphs[] = array(
                                "attributes" => array(
                                    "type" => "list",
                                    "list" => array(
                                        "numberingId" => $this->_numberingId,
                                        "abstractNumberingId" => $this->_abstractNumberingId,
                                        "type" => $type,
                                    ),
                                ),
                                "runs" => $this->_getTextRunBlocks(
                                    mb_substr($listText, $liPos + 4, $endLiPos - $liPos - 4),
                                    array(),
                                    $replaceLineFeeds
                                ),
                            );

                            $listText = mb_substr($listText, $endLiPos + 5);
                            $liPos = 0;
                        } else {
                            $liPos++;
                        }
                    }

                    $text = mb_substr($text, $endPos + $tagLength + 1);

                    if (!$text) {
                        $text = null;
                    }

                    $pos = 0;

                    if (!in_array($this->_abstractNumberingId, array_keys($this->_lists))) {
                        $this->_lists[$this->_abstractNumberingId] = array(
                            "abstractNumberingId" => $this->_abstractNumberingId,
                            "numberingId" => $this->_numberingId,
                            "type" => $type,
                        );
                    }

                    $this->_numberingId++;
                    $this->_abstractNumberingId++;
                    $found = true;

                    break;
                }
            }

            if (!$found) {
                $pos += 1;
            }
        }

        if ($text !== null) {
            $paragraphs[] = array(
                "attributes" => array(),
                "runs" => $this->_getTextRunBlocks($text, array(), $replaceLineFeeds),
            );
        }

        return $paragraphs;
    }

    /**
     * Insert text
     * @param DOMDocument $xml
     * @param DOMNode $parentNode
     * @param string $text
     * @param bool $replaceLineFeeds
     */
    private function _insertText(DOMDocument $xml, DOMNode $parentNode, $text, $replaceLineFeeds) {
        $attributes = array();
        $paragraphs = $this->_getParagraphs($text, $replaceLineFeeds);

        if (count($paragraphs) > 1 && $parentNode->tagName == "w:p") {
            foreach ($parentNode->attributes as $attr => $value) {
                $attributes[$attr] = $value->nodeValue;
            }

            $superParent = $parentNode->parentNode;
            $superParent->removeChild($parentNode);
            $parentNode = $superParent;
        }

        $ns = $xml->lookupNamespaceUri("w");

        foreach ($paragraphs as $para) {
            $paragraph = null;

            if (count($paragraphs) > 1) {
                $paragraph = $xml->createElementNS($ns, "w:p");
                $paragraphProperties = $xml->createElementNS($ns, "w:pPr");

                foreach ($attributes as $attr => $value) {
                    $paragraph->setAttribute($attr, $value);
                }

                if ($para["attributes"]) {
                    $pAttributes = $para["attributes"];

                    if (isset($pAttributes["type"]) && $pAttributes["type"] == "list") {
                        $style = $xml->createElementNS($ns, "w:pStyle");
                        $style->setAttribute("w:val", "ListParagraph");
                        $paragraphProperties->appendChild($style);
                        $numPr = $xml->createElementNS($ns, "w:numPr");
                        $ilvl = $xml->createElementNS($ns, "w:ilvl");
                        $ilvl->setAttribute("w:val", "0");
                        $numId = $xml->createElementNS($ns, "w:numId");
                        $numId->setAttribute("w:val", $pAttributes["list"]["numberingId"]);
                        $numPr->appendChild($numId);
                        $numPr->appendChild($ilvl);
                        $paragraphProperties->appendChild($numPr);
                    }

                    $paragraph->appendChild($paragraphProperties);
                }
            }

            foreach ($para["runs"] as $run) {
                $textRun = $xml->createElementNS($ns, "w:r");
                $textRunProperties = $xml->createElementNS($ns, "w:rPr");

                if (in_array("bold", $run["attributes"])) {
                    $textRunProperties->appendChild($xml->createElementNS($ns, "w:b"));
                }

                if (in_array("italic", $run["attributes"])) {
                    $textRunProperties->appendChild($xml->createElementNS($ns, "w:i"));
                }

                if (in_array("underline", $run["attributes"])) {
                    $underline = $xml->createElementNS($ns, "w:u");
                    $underline->setAttribute("w:val", "single");
                    $textRunProperties->appendChild($underline);
                }

                if ($run["attributes"]) {
                    $textRun->appendChild($textRunProperties);
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

                if (count($paragraphs) > 1) {
                    $paragraph->appendChild($textRun);
                } else {
                    $parentNode->appendChild($textRun);
                }
            }

            if (count($paragraphs) > 1) {
                $parentNode->appendChild($paragraph);
            }
        }
    }

    /**
     * Insert text attachment
     * @param DOMDocument $xml
     * @param DOMNode $parentNode
     * @param $attachment
     * @param $replaceLineFeeds
     */
    private function _insertTextAttachment(DOMDocument $xml, DOMNode $parentNode, $attachment, $replaceLineFeeds) {
        $text = FileManager::getFileContent($attachment["file"]);
        if (!$text) {
            return;
        }

        $this->_insertText($xml, $parentNode, $text, $replaceLineFeeds);
    }

    /**
     * Insert attached image
     * @param DOMDocument $xml
     * @param DOMNode $parentNode
     * @param array $image
     */
    private function _insertImage(DOMDocument $xml, DOMNode $parentNode, $image) {
        $w = $xml->lookupNamespaceUri("w");
        $wp = $xml->lookupNamespaceUri("wp");
        $a = "http://schemas.openxmlformats.org/drawingml/2006/main";
        $pic = "http://schemas.openxmlformats.org/drawingml/2006/picture";

        if (!$image) {
            return;
        }

        $mediaDir = $this->_templateDir . "/word/media";

        if (!is_dir($mediaDir)) {
            FileManager::createDir($mediaDir, 0777);
        }

        $extensions = array(
            "image/jpeg" => "jpg",
            "image/png" => "png",
            "image/gif" => "gif",
            "image/pjpeg" => "jpg",
        );

        $internalName = hash("sha256", time() . rand() . $image["file"]) . "." . $extensions[$image["type"]];
        FileManager::copy($image["file"], $mediaDir . "/" . $internalName);

        list($wpx, $hpx) = getimagesize($image["file"]);
        $ratio = $wpx / $hpx;
        $width = $this->_pixelsToEmu($wpx);
        $height = $this->_pixelsToEmu($hpx);

        if ($width > $this->_documentWidth) {
            $width = $this->_documentWidth;
            $height = (int) round($width / $ratio);
        }

        $run = $xml->createElementNS($w, "w:r");
        $drawing = $xml->createElementNS($w, "w:drawing");
        $inline = $xml->createElementNS($wp, "wp:inline");

        $extent = $xml->createElementNS($wp, "wp:extent");
        $extent->setAttribute("cx", $width);
        $extent->setAttribute("cy", $height);
        $inline->appendChild($extent);

        $docPr = $xml->createElementNS($wp, "wp:docPr");
        $docPr->setAttribute("id", (string) $this->_drawingObjectPropertyId);
        $docPr->setAttribute("name", $image["name"]);
        $inline->appendChild($docPr);

        $frame = $xml->createElementNS($wp, "wp:cNvGraphicFramePr");
        $frameLocks = $xml->createElementNS($a, "a:graphicFrameLocks");
        $frameLocks->setAttribute("noChangeAspect", "1");
        $frame->appendChild($frameLocks);
        $inline->appendChild($frame);

        $graphic = $xml->createElementNS($a, "a:graphic");
        $data = $xml->createElementNS($a, "a:graphicData");
        $data->setAttribute("uri", "http://schemas.openxmlformats.org/drawingml/2006/picture");
        $picture = $xml->createElementNS($pic, "pic:pic");
        $props = $xml->createElementNS($pic, "pic:nvPicPr");
        $cnvPr = $xml->createElementNS($pic, "pic:cNvPr");
        $cnvPr->setAttribute("id", $this->_nonVisualDrawingPropertyId);
        $cnvPr->setAttribute("name", $image["name"]);
        $props->appendChild($cnvPr);
        $props->appendChild($xml->createElementNS($pic, "pic:cNvPicPr"));
        $picture->appendChild($props);

        $blip = $xml->createElementNS($pic, "pic:blipFill");
        $aBlip = $xml->createElementNS($a, "a:blip");
        $aBlip->setAttribute("r:embed", "rId" . $this->_relationId);
        $aBlip->setAttribute("cstate", "print");
        $blip->appendChild($aBlip);
        $stretch = $xml->createElementNS($a, "a:stretch");
        $stretch->appendChild($xml->createElementNS($a, "a:fillRect"));
        $blip->appendChild($stretch);
        $picture->appendChild($blip);

        $sppr = $xml->createElementNS($pic, "pic:spPr");
        $xfrm = $xml->createElementNS($a, "a:xfrm");
        $off = $xml->createElementNS($a, "a:off");
        $off->setAttribute("x", "0");
        $off->setAttribute("y", "0");
        $xfrm->appendChild($off);
        $ext = $xml->createElementNS($a, "a:ext");
        $ext->setAttribute("cx", $width);
        $ext->setAttribute("cy", $height);
        $xfrm->appendChild($ext);
        $sppr->appendChild($xfrm);
        $geom = $xml->createElementNS($a, "a:prstGeom");
        $geom->setAttribute("prst", "rect");
        $sppr->appendChild($geom);
        $picture->appendChild($sppr);

        $data->appendChild($picture);
        $graphic->appendChild($data);
        $inline->appendChild($graphic);
        $drawing->appendChild($inline);
        $run->appendChild($drawing);
        $parentNode->appendChild($run);

        $this->_images[] = array(
            "relationId" => $this->_relationId,
            "internalName" => $internalName,
        );

        $this->_relationId++;
        $this->_drawingObjectPropertyId++;
        $this->_nonVisualDrawingPropertyId++;
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

            // remove placeholders
            $parent = $textRun->parentNode;
            $parent->removeChild($textRun);
            $blockBody->removeChild($block->parentNode);

            // line feeds
            $replaceLineFeeds = false;

            if ($this->_scope->get()->getName() == VariableScope::SCOPE_CHECK && $name == "result") {
                $replaceLineFeeds = true;
            }

            if ($this->_scope->get()->getName() == VariableScope::SCOPE_ATTACHMENT && $name == "image") {
                // If the attachment is not an image but text-based file, attach it as text.
                if ($value["type"] == "text/plain") {
                    $replaceLineFeeds = true;
                    $this->_insertTextAttachment($xml, $parent, $value, $replaceLineFeeds);
                } else {
                    $this->_insertImage($xml, $parent, $value);
               }
            } else {
                // Decode HTML-entities back to normal characters.
                $value = html_entity_decode($value, ENT_QUOTES);
                $this->_insertText($xml, $parent, $value, $replaceLineFeeds);
            }
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
     * Convert twips to EMU
     * @param $twips
     * @param $dpi
     * @return integer
     */
    private function _twipsToEMU($twips, $dpi=72) {
        return (int) round($twips / 20 / $dpi * 914400);
    }

    /**
     * Convert pixels to EMU
     * @param $pixels
     * @param int $dpi
     * @return int
     */
    private function _pixelsToEmu($pixels, $dpi=72) {
        return (int) round($pixels * 914400 / $dpi);
    }

    /**
     * Parse template data
     * @param DOMDocument $xml
     */
    private function _parseTemplate(DOMDocument $xml) {
        $xpath = new DOMXPath($xml);
        $xpath->registerNamespace("w", $xml->documentElement->lookupNamespaceUri("w"));
        $xpath->registerNamespace("wp", $xml->documentElement->lookupNamespaceUri("wp"));
        $xpath->registerNamespace("pic", $xml->documentElement->lookupNamespaceUri("pic"));
        $drawingObjectProperties = $xpath->query(".//wp:docPr");

        // count images
        foreach ($drawingObjectProperties as $property) {
            $id = (int) $property->getAttribute("id");

            if ($id > $this->_drawingObjectPropertyId) {
                $this->_drawingObjectPropertyId= $id;
            }
        }

        if ($this->_drawingObjectPropertyId > 1) {
            $this->_drawingObjectPropertyId++;
        }

        // count images
        $nonVisualDrawingProperties = $xpath->query(".//pic:cNvPr");

        foreach ($nonVisualDrawingProperties as $property) {
            $id = (int) $property->getAttribute("id");

            if ($id > $this->_nonVisualDrawingPropertyId) {
                $this->_nonVisualDrawingPropertyId= $id;
            }
        }

        if ($this->_nonVisualDrawingPropertyId > 0) {
            $this->_nonVisualDrawingPropertyId++;
        }

        // parse document size (for images width calculation)
        $pageSize = $xpath->query(".//w:pgSz");

        if ($pageSize->length) {
            $pageSize = $pageSize->item(0);
            $this->_documentWidth = (int) $pageSize->getAttribute("w:w");
        }

        $margin = $xpath->query(".//w:pgMar");

        if ($margin->length) {
            $margin = $margin->item(0);
            $left = (int) $margin->getAttribute("w:left");
            $right = (int) $margin->getAttribute("w:right");
            $this->_documentWidth = $this->_documentWidth - $left - $right;
        }

        if (!$this->_documentWidth) {
            $this->_documentWidth = 11906;
        }

        $this->_documentWidth = $this->_twipsToEMU($this->_documentWidth);
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
        $this->_parseTemplate($xml);
        $this->_fillTemplate($xml);

        return $xml->saveXML();
    }

    /**
     * Parse numbering for lists
     * @param $template
     */
    private function _parseNumbering($template) {
        $xml = new DOMDocument();
        $xml->loadXML($template);
        $xpath = new DOMXPath($xml);
        $xpath->registerNamespace("w", $xml->documentElement->lookupNamespaceUri("w"));
        $abstractNumberings = $xpath->query("/w:numbering/w:abstractNum");
        $numberings = $xpath->query("/w:numbering/w:num");

        $this->_abstractNumberingId = 0;
        $this->_numberingId = 1;

        foreach ($abstractNumberings as $numbering) {
            $id = (int) $numbering->getAttribute("w:abstractNumId");

            if ($id > $this->_abstractNumberingId) {
                $this->_abstractNumberingId = $id;
            }
        }

        foreach ($numberings as $numbering) {
            $id = (int) $numbering->getAttribute("w:numId");

            if ($id > $this->_numberingId) {
                $this->_numberingId = $id;
            }
        }

        if ($this->_abstractNumberingId > 0) {
            $this->_abstractNumberingId++;
        }

        if ($this->_numberingId > 1) {
            $this->_numberingId++;
        }
    }

    /**
     * Insert numbering for lists
     * @param $template
     * @return string
     */
    private function _insertNumbering($template) {
        $xml = new DOMDocument();
        $xml->loadXML($template);
        $ns = $xml->lookupNamespaceUri("w");

        $abstractNumberings = array();
        $numberings = array();

        foreach ($this->_lists as $list) {
            $abstractNum = $xml->createElementNS($ns, "w:abstractNum");
            $abstractNum->setAttribute("w:abstractNumId", $list["abstractNumberingId"]);

            $lvl = $xml->createElementNS($ns, "w:lvl");
            $lvl->setAttribute("w:ilvl", "0");
            $start = $xml->createElementNS($ns, "w:start");
            $start->setAttribute("w:val", "1");
            $numFmt = $xml->createElementNS($ns, "w:numFmt");
            $lvlText = $xml->createElementNS($ns, "w:lvlText");
            $lvlJc = $xml->createElementNS($ns, "w:lvlJc");
            $lvlJc->setAttribute("w:val", "left");
            $lvlRestart = $xml->createElementNS($ns, "w:lvlRestart");
            $lvlRestart->setAttribute("w:val", "1");

            if ($list["type"] == "bullet") {
                $numFmt->setAttribute("w:val", "bullet");
                $lvlText->setAttribute("w:val", "o");
            } else {
                $numFmt->setAttribute("w:val", "decimal");
                $lvlText->setAttribute("w:val", "%1.");
            }

            $lvl->appendChild($start);
            $lvl->appendChild($numFmt);
            $lvl->appendChild($lvlText);
            $lvl->appendChild($lvlJc);
            $lvl->appendChild($lvlRestart);
            $abstractNum->appendChild($lvl);

            $num = $xml->createElementNS($ns, "w:num");
            $num->setAttribute("w:numId", $list["numberingId"]);
            $abstractNumId = $xml->createElementNS($ns, "w:abstractNumId");
            $abstractNumId->setAttribute("w:val", $list["abstractNumberingId"]);
            $num->appendChild($abstractNumId);

            $abstractNumberings[] = $abstractNum;
            $numberings[] = $num;
        }

        $xpath = new DOMXPath($xml);
        $xpath->registerNamespace("w", $xml->documentElement->lookupNamespaceUri("w"));
        $existingAbstract = $xpath->query("/w:numbering/w:abstractNum");

        if ($abstractNumberings && $numberings) {
            /** @var DOMNode $current */
            $current = null;

            foreach ($existingAbstract as $abstract) {
                if ($abstract->nextSibling->tagName != "w:abstractNum") {
                    $current = $abstract;
                    break;
                }
            }

            foreach ($abstractNumberings as $abstract) {
                if ($current) {
                    if ($current->nextSibling) {
                        $current->parentNode->insertBefore($abstract, $current->nextSibling);
                    } else {
                        $current->parentNode->appendChild($abstract);
                    }
                } else {
                    $xml->documentElement->appendChild($abstract);
                }

                $current = $abstract;
            }

            // add numberings at the end
            foreach ($numberings as $numbering) {
                $xml->documentElement->appendChild($numbering);
            }
        }

        return $xml->saveXML();
    }

    /**
     * Parse relations
     * @param $template
     */
    private function _parseRelations($template) {
        $xml = new DOMDocument();
        $xml->loadXML($template);

        foreach ($xml->documentElement->childNodes as $relation) {
            $id = (int) substr($relation->getAttribute("Id"), 3);

            if ($id > $this->_relationId) {
                $this->_relationId = $id;
            }
        }

        if ($this->_relationId > 1) {
            $this->_relationId++;
        }
    }

    /**
     * Insert relations
     * @param $template
     * @return string
     */
    private function _insertRelations($template) {
        $xml = new DOMDocument();
        $xml->loadXML($template);

        foreach ($this->_images as $img) {
            $rel = $xml->createElement("Relationship");
            $rel->setAttribute("Id", "rId" . $img["relationId"]);
            $rel->setAttribute("Type", "http://schemas.openxmlformats.org/officeDocument/2006/relationships/image");
            $rel->setAttribute("Target", "media/" . $img["internalName"]);
            $xml->documentElement->appendChild($rel);
        }

        return $xml->saveXML();
    }

    /**
     * Insert additional content types
     * @param $template
     * @return string
     */
    private function _insertContentTypes($template) {
        $xml = new DOMDocument();
        $xml->loadXML($template);

        $lastContentType = null;
        $xpath = new DOMXPath($xml);
        $contentTypes = $xpath->query("/Default");

        foreach ($contentTypes as $ct) {
            if ($ct->nextSibling->tagName != "Default") {
                $lastContentType = $ct;
                break;
            }
        }

        if ($lastContentType) {
            $lastContentType = $lastContentType->nextSibling;
        }

        $types = array(
            "jpg" => "image/jpeg",
            "png" => "image/png",
            "gif" => "image/gif",
        );

        foreach ($types as $ext => $type) {
            foreach ($xml->documentElement->childNodes as $oldNode) {
                $sameExtension = $oldNode->getAttribute("Extension") == $ext;
                $sameType = $oldNode->getAttribute("ContentType") == $type;

                if ($sameExtension && $sameType) {
                    // Skip the $type as it already exists on the template.
                    continue 2;
                } else if (!$sameExtension && !$sameType) {
                    continue;
                } else {
                   throw new Exception("The template contained unexpected ContentType<->Extension mapping.");
                }
            }

            $node = $xml->createElement("Default");
            $node->setAttribute("Extension", $ext);
            $node->setAttribute("ContentType", $type);

            if ($lastContentType) {
                $xml->documentElement->insertBefore($node, $lastContentType->nextSibling);
            } else {
                $xml->documentElement->appendChild($node);
            }
        }

        return $xml->saveXML();
    }

    /**
     * Generate report
     */
    public function generate() {
        FileManager::createDir(Yii::app()->params["reports"]["tmpFilesPath"], 0777);
        $this->_templateDir = Yii::app()->params["reports"]["tmpFilesPath"] . "/" . hash("sha256", time() . rand() . $this->_fileName);

        FileManager::createDir($this->_templateDir, 0777);
        $exception = null;

        try {
            $templatePath = Yii::app()->params["reports"]["file"]["path"] . "/" . $this->_template->file_path;
            $zip = new ZipArchive();

            if ($zip->open($templatePath) !== true) {
                throw new Exception("Error opening ZIP archive: " . $templatePath);
            }

            if ($zip->extractTo($this->_templateDir) !== true) {
                throw new Exception("Error extracting files to " . $this->_templateDir);
            }

            $zip->close();

            // parse numbering
            $numberingXml = $this->_templateDir . "/word/numbering.xml";
            $numbering = @file_get_contents($numberingXml);
            $this->_parseNumbering($numbering);

            // parse relations
            $relationsXml = $this->_templateDir . "/word/_rels/document.xml.rels";
            $relations = @file_get_contents($relationsXml);
            $this->_parseRelations($relations);

            // process template
            $documentXml = $this->_templateDir . "/word/document.xml";
            $template = @file_get_contents($documentXml);
            @file_put_contents($documentXml, $this->_processTemplate($template));

            // insert list numbering
            @file_put_contents($numberingXml, $this->_insertNumbering($numbering));

            // insert relations
            @file_put_contents($relationsXml, $this->_insertRelations($relations));

            // insert content types
            $contentXml = $this->_templateDir . "/[Content_Types].xml";
            $contentTypes = @file_get_contents($contentXml);
            @file_put_contents($contentXml, $this->_insertContentTypes($contentTypes));

            $zip = new ZipArchive();

            if (file_exists($this->_filePath)) {
                FileManager::unlink($this->_filePath);
            }

            if (!$zip->open($this->_filePath, ZipArchive::CREATE)) {
                throw new Exception(Yii::t("app", "Unable to create the report."));
            }

            FileManager::zipDirectory($zip, $this->_templateDir);
            $zip->close();
        } catch (Exception $e) {
            $exception = $e;
        }

        // finally
        FileManager::rmDir($this->_templateDir);

        if ($exception) {
            throw $exception;
        }

        $this->_generated = true;
    }
}
