<?php

/**
 * Result table class.
 */
class ResultTable {
    /**
     * Tag names
     */
    const TAG_MAIN    = 'gtta-table';
    const TAG_ROW     = 'row';
    const TAG_CELL    = 'cell';
    const TAG_COLUMNS = 'columns';
    const TAG_COLUMN  = 'column';

    /**
     * Attributes
     */
    const ATTR_NAME  = 'name';
    const ATTR_WIDTH = 'width';

    /**
     * @var array tables.
     */
    private $_tables;

    /**
     * Clean string from HTML entities
     * @param $in
     * @param null $offset
     * @return string
     */
    private function _cleanString($in, $offset=null) {
        $out = trim($in);

        if (empty($out)) {
            return $out;
        }

        $start = @strpos($out, "&", $offset);

        if ($start === false) {
            return $out;
        }

        $end = @strpos($out, ";", $start);

        if ($end === false) {
             return $out;
        }

        if ($end > $start + 7) {
             $out = $this->_cleanString($out, $start + 1);
        } else {
             $clean = substr($out, 0, $start);
             $clean .= substr($out, $end + 1);
             $out = $this->_cleanString($clean, $start + 1);
        }

        return $out;
    }

    /**
     * Parse
     */
    public function parse($content) {
        $this->_tables = array();

        try {
            $document = new SimpleXMLElement(
                "<document>" . $this->_cleanString($content). "</document>",
                LIBXML_NOERROR
            );

            $tableList = $document->{self::TAG_MAIN};

            if (!$tableList) {
                throw new Exception();
            }

            foreach ($tableList as $table) {
                $tableData = array(
                    'columnCount' => 0,
                    'rowCount' => 0,
                    'columns' => array(),
                    'data' => array(),
                );

                $columns = $table->{self::TAG_COLUMNS};

                if (!$columns) {
                    throw new Exception();
                }

                $columns = $columns->{self::TAG_COLUMN};

                if (!$columns) {
                    throw new Exception();
                }

                $tableData['columnCount'] = count($columns);

                // TODO: add total width control here
                foreach ($columns as $column) {
                    $tableData['columns'][] = array(
                        'name' => $column[self::ATTR_NAME],
                        'width' => $column[self::ATTR_WIDTH]
                    );
                }

                $rows = $table->{self::TAG_ROW};
                $tableData['rowCount'] = count($rows);

                foreach ($rows as $row) {
                    $cells = array();
                    $columns = $row->{self::TAG_CELL};

                    foreach ($columns as $column) {
                        $cells[] = $column;
                    }

                    if ($cells) {
                        $tableData['data'][] = $cells;
                    }
                }

                $this->_tables[] = $tableData;
            }
        } catch (Exception $e) {}
    }

    /**
     * Get tables
     */
    public function getTables() {
        return $this->_tables;
    }
}