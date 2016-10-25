<?php

/**
 * Exception classes
 */
class ImportFileParsingException extends Exception {}
class NoValidTargetException extends Exception {}

/**
 * Class ImportManager
 */
class ImportManager {
    const TYPE_NESSUS     = "nessus";
    const TYPE_NESSUS_CSV = "nessus_csv";
    const TYPE_CSV        = "csv";
    const TYPE_TXT        = "txt";

    /**
     * Availible types
     * @var array
     */
    public static $types = array(
        self::TYPE_NESSUS => array(
            "name"  => "Nessus",
            "ext"   => "nessus"
        ),
        self::TYPE_NESSUS_CSV => array(
            "name"  => "Nessus CSV",
            "ext"   => "csv"
        ),
        self::TYPE_CSV => array(
            "name"  => "CSV File",
            "ext"   => "csv"
        ),
        self::TYPE_TXT => array(
            "name"  => "Text File",
            "ext"   => "txt"
        )
    );

    /**
     * Parse CSV file
     * @param $file
     */
    public static function parseCSV($file, $nessus=false) {
        $result = array();

        if (($handle = fopen($file, "r")) !== FALSE) {
            if ($nessus) {
                $titles = fgetcsv($handle, 1000, ",");
            }

            while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                $empty = true;
                $num = count($data);
                $rowData = array();

                for ($col = 0; $col < $num; $col++) {
                    if ($data[$col]) {
                        $empty = false;
                    }

                    if ($nessus) {
                        $rowData[$titles[$col]] = $data[$col];
                    } else {
                        $rowData[] = $data[$col];
                    }
                }

                if (!$empty) {
                    $result[] = $rowData;
                }
            }

            fclose($handle);
        }

        return $result;
    }

    /**
     * Parse TXT file
     * @param $file
     * @return array
     */
    public static function parseTXT($file) {
        $result = array();

        if (($handle = fopen($file, "r")) !== FALSE) {
            $data = fgets($handle, 1000);

            while ($data !== FALSE) {
                if (trim($data)) {
                    $rowData = explode(" ", $data);
                    $result[] = $rowData;
                }

                $data = fgets($handle, 1000);
            }

            fclose($handle);
        }

        return $result;
    }

    /**
     * Create new target(s) by importing from file
     * @param $path
     * @param string $type
     * @param $project
     * @param null $mappingId
     * @throws Exception
     * @throws ImportFileParsingException
     * @throws NoValidTargetException
     */
    public static function importTargets($path, $type=self::TYPE_NESSUS_CSV, $project, $mappingId = null) {
        if (!file_exists($path)) {
            throw new Exception("File not found.");
        }                
        
        $targetCache = [];
        $ids = [];

        switch ($type) {
            case self::TYPE_NESSUS_CSV:
                try {
                    $targets = self::parseCSV($path, true);
                } catch (Exception $e) {
                    throw new ImportFileParsingException();
                }

                if (!count($targets)) {
                    throw new NoValidTargetException();
                }

                $ids = [];

                foreach ($targets as $target) {
                    if (!isset($target["Host"])) {
                        throw new ImportFileParsingException();
                    }
                    
                    $host = $target["Host"];
                    
                    if (in_array($host, $targetCache)) {
                        continue;
                    }

                    $t = new Target();
                    $t->project_id = $project->id;
                    $t->host = $host;
                    $t->save();
                    $t->refresh();
                    $ids[] = $t->id;
                    
                    $targetCache[] = $host;
                }

                break;

            case self::TYPE_NESSUS:
                $nrm = new NessusReportManager();

                try {
                    $mapping = NessusMapping::model()->findByPk($mappingId);

                    if (!$mapping) {
                        throw new Exception();
                    }


                } catch (Exception $e) {
                    throw new Exception("Import failed.");
                }

                break;

            case self::TYPE_CSV:
                try {
                    $targets = self::parseCSV($path);
                } catch (Exception $e) {
                    throw new ImportFileParsingException();
                }

                if (!count($targets)) {
                    throw new NoValidTargetException();
                }

                foreach ($targets as $target) {
                    $targetData = array_map(function ($v) {return trim($v);}, explode(":", $target[0]));
                    
                    if (in_array($targetData[0], $targetCache)) {
                        continue;
                    }

                    $t = new Target();
                    $t->project_id = $project->id;
                    $t->host = $targetData[0];

                    if (count($targetData) > 1) {
                        $t->port = (int) $targetData[1];
                    }

                    $t->save();
                    $t->refresh();
                    $ids[] = $t->id;
                    
                    $targetCache[] = $targetData[0];
                }

                break;

            case self::TYPE_TXT:
                try {
                    $targets = self::parseTXT($path);
                } catch (Exception $e) {
                    throw new ImportFileParsingException();
                }

                if (!count($targets)) {
                    throw new NoValidTargetException();
                }

                foreach ($targets as $target) {
                    $targetData = array_map(function ($v) {return trim($v);}, explode(":", $target[0]));
                    
                    if (in_array($targetData[0], $targetCache)) {
                        continue;
                    }

                    $t = new Target();
                    $t->project_id = $project->id;
                    $t->host = $targetData[0];

                    if (count($targetData) > 1) {
                        $t->port = (int) $targetData[1];
                    }

                    $t->save();
                    $t->refresh();
                    $ids[] = $t->id;
                    
                    $targetCache[] = $targetData[0];
                }

                break;

            default:
                throw new Exception("Unknown file type.");
                break;
        }

        $references = Reference::model()->findAll();

        // by default all references must be bound to target
        foreach ($ids as $tId) {
            foreach ($references as $ref) {
                $targetRef = new TargetReference();
                $targetRef->target_id = $tId;
                $targetRef->reference_id = $ref->id;
                $targetRef->save();
            }
        }

        HostResolveJob::enqueue([
            "targets" => $ids
        ]);
    }

    /**
     * Import mapping from parsed nessus report
     * @param $parsedReport
     * @return NessusMapping
     * @throws Exception
     */
    public static function importMapping($parsedReport) {
        if (!is_array($parsedReport)) {
            throw new Exception("Report should be an array.");
        }

        $name = isset($parsedReport["name"]) ? $parsedReport["name"] : Yii::t("app", "N/A");

        $mapping = new NessusMapping();
        $mapping->name = $name;
        $mapping->save();
        $mapping->refresh();

        try {
            foreach ($parsedReport["hosts"] as $host) {
                foreach ($host["vulnerabilities"] as $v) {
                    $vuln = NessusMappingVuln::model()->findByAttributes([
                        "nessus_mapping_id" => $mapping->id,
                        "nessus_plugin_id" => $v["plugin_id"],
                        "nessus_host" => $host["name"]
                    ]);

                    if (!$vuln) {
                        $vuln = new NessusMappingVuln();
                        $vuln->nessus_mapping_id = $mapping->id;
                        $vuln->nessus_plugin_id = $v["plugin_id"];
                        $vuln->nessus_plugin_name = $v["plugin_name"];
                        $vuln->nessus_rating = $v["risk_factor"];
                        $vuln->nessus_host = $host["name"];

                        $vuln->save();
                    }
                }
            }
        } catch (Exception $e) {
            $mapping->delete();
            throw new Exception("Import failed.");
        }

        $mapping->refresh();

        return $mapping;
    }
}