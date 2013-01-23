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
    const ATTR_ROWS  = 'rows';
    const ATTR_NAME  = 'name';
    const ATTR_WIDTH = 'width';

    /**
     * @var integer row count
     */
    public $rowCount = 0;

    /**
     * @var integer column count
     */
    public $columnCount = 0;

    /**
     * @var array column details
     */
    public $columns = array();

    /**
     * @var array cell data
     */
    public $data = array();

    /**
     * Parse
     */
    public function parse($content)
    {
        $table = new SimpleXMLElement($content);

        $columns = $table->{self::TAG_COLUMNS};

        if (!$columns)
            throw new Exception();

        $columns = $columns->{self::TAG_COLUMN};

        if (!$columns)
            throw new Exception();

        $this->columnCount = count($columns);

        // TODO: add total width control here
        foreach ($columns as $column)
            $this->columns[] = array(
                'name'  => $column[self::ATTR_NAME],
                'width' => $column[self::ATTR_WIDTH]
            );

        $rows = $table->{self::TAG_ROW};
        $this->rowCount = count($rows);

        foreach ($rows as $row)
        {
            $cells = array();

            $columns = $row->{self::TAG_CELL};

            foreach ($columns as $column)
                $cells[] = $column;

            if ($cells)
                $this->data[] = $cells;
        }
    }
}