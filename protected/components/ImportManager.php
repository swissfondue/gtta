<?php

/**
 * Class ImportManager
 */
class ImportManager {
    const TYPE_CSV = "csv";
    const TYPE_NESSUS = "nessus";

    /**
     * Serialize csv file's content
     * @param $file
     */
    public function csvSerialize($file) {
        $result = array();

        if (($handle = fopen($file, "r")) !== FALSE) {
            $titles = fgetcsv($handle, 1000, ",");

            while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                $num = count($data);
                $rowData = array();

                for ($col = 0; $col < $num; $col++) {
                    $rowData[$titles[$col]] = $data[$col];
                }

                $result[] = $rowData;
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
    public static function importTargets($path, $type=self::TYPE_CSV, $project) {
        if (!file_exists($path)) {
            throw new Exception("File not found.");
        }

        $targets = array();

        switch ($type) {
            case self::TYPE_CSV:
                $targets = self::csvSerialize($path);

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

            default:
                throw new Exception("Unknown file type.");
                break;
        }
    }
}