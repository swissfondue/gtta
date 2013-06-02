<?php

/**
 * Result table class.
 */
class ResultTable
{
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
     * Parse
     */
    public function parse($content)
    {
        $document = new SimpleXMLElement("<document>" . $content . "</document>");
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
                    'name'  => $column[self::ATTR_NAME],
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
    }

    /**
     * Get tables
     */
    public function getTables() {
        return $this->_tables;
    }
}