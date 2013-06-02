<?php

/**
 * Nmap port dependency processor
 */
class NmapPortProcessor extends DependencyProcessor {
    /**
     * Prepare host.
     */
    private function _prepare($host) {
        return str_replace(array("(", ")", " ", "\n", "\t"), "", $host);
    }

    /**
     * Get targets
     */
    protected function _get_targets($result, $tableResult, $condition) {
        $targets = array();

        if (!$tableResult) {
            return $targets;
        }

        try {
            $table = new ResultTable();
            $table->parse($tableResult);

            foreach ($table->getTables() as $tbl) {
                foreach ($tbl['data'] as $target) {
                    $host = $target[0];
                    $port = $target[1];

                    if ($host && $port && $port == $condition) {
                        if (strpos($host, ' (') === false) {
                            $targets[] = $this->_prepare($host);
                        } else {
                            foreach (explode(" (", $host) as $h) {
                                $targets[] = $this->_prepare($h);
                            }
                        }
                    }
                }
            }
        } catch (Exception $e) {
            // pass
        }

        return $targets;
    }
}