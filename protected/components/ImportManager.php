<?php

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
        self::TYPE_NESSUS     => array(
            "name"  => "Nessus",
            "ext"   => "nessus"
        ),
        self::TYPE_NESSUS_CSV => array(
            "name"  => "Nessus CSV",
            "ext"   => "csv"
        ),
        self::TYPE_CSV        => array(
            "name"  => "CSV",
            "ext"   => "csv"
        ),
        self::TYPE_TXT        => array(
            "name"  => "TXT",
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
                $num = count($data);
                $rowData = array();

                for ($col = 0; $col < $num; $col++) {
                    if ($nessus) {
                        $rowData[$titles[$col]] = $data[$col];
                    } else {
                        $rowData[] = $data[$col];
                    }
                }

                $result[] = $rowData;
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
     * @return array
     * @throws Exception
     */
    public static function importTargets($path, $type=self::TYPE_NESSUS_CSV, $project) {
        if (!file_exists($path)) {
            throw new Exception("File not found.");
        }

        switch ($type) {
            case self::TYPE_NESSUS_CSV:
                $targets = self::parseCSV($path, true);

                foreach ($targets as $target) {
                    $t = new Target();
                    $t->project_id = $project->id;
                    $t->host = $target["Host"];
                    $t->port = $target["Port"];
                    $t->description = $target["Description"];
                    $t->save();
                }
                
                break;

            case self::TYPE_NESSUS:
                $content = FileManager::getFileContent($path);
                $report = new SimpleXMLElement($content);

                foreach ($report->xpath("//ReportHost") as $reportNode) {
                    $t = new Target();

                    foreach ($reportNode->HostProperties->tag as $property) {
                        $attributes = $property->attributes();

                        if ($attributes["name"] != "host-ip") {
                            continue;
                        }

                        $t->project_id = $project->id;
                        $t->host = $property;
                        $t->save();

                        break;
                    }
                }

                break;

            case self::TYPE_CSV:
                $targets = self::parseCSV($path);

                foreach ($targets as $target) {
                    $targetData = explode(":", $target[0]);

                    $t = new Target();
                    $t->project_id = $project->id;
                    $t->host       = $targetData[0];

                    if (count($targetData) > 1) {
                        $t->port = (int) $targetData[1];
                    }

                    $t->save();
                }

                break;

            case self::TYPE_TXT:
                $targets = self::parseTXT($path);

                foreach ($targets as $target) {
                    $targetData = explode(":", $target[0]);

                    $t = new Target();
                    $t->project_id = $project->id;
                    $t->host       = $targetData[0];

                    if (count($targetData) > 1) {
                        $t->port = (int) $targetData[1];
                    }

                    $t->save();
                }

                break;

            default:
                throw new Exception("Unknown file type.");
                break;
        }
    }
}