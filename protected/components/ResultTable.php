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
    private $_rowCount = 0;

    /**
     * @var integer column count
     */
    private $_columnCount = 0;

    /**
     * @var array column details
     */
    private $_columns = array();

    /**
     * @var array cell data
     */
    private $_data = array();

    /**
     * @var boolean is parsed
     */
    private $_parsed = false;

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

        $this->_columnCount = count($columns);

        // TODO: add total width control here
        foreach ($columns as $column)
            $this->_columns[] = array(
                'name'  => $column[self::ATTR_NAME],
                'width' => $column[self::ATTR_WIDTH]
            );

        $rows = $table->{self::TAG_ROW};

        foreach ($rows as $row)
        {
            $cells = array();

            $columns = $row->{self::TAG_CELL};

            foreach ($columns as $column)
                $cells[] = $column;

            if ($cells)
                $this->_data[] = $cells;
        }

        $this->_parsed = true;
    }

    /**
     * Render to HTML
     */
    public function toHTML()
    {
        if (!$this->_parsed)
            throw new Exception();

        $html = '<table class="table" width="100%"><tr>';

        foreach ($this->_columns as $column)
            $html .= '<th width="' . round(100 * $column['width']) . '%">' . $column['name'] . '</th>';

        $html .= '</tr>';

        foreach ($this->_data as $row)
        {
            $html .= '<tr>';

            foreach ($row as $cell)
                $html .= '<td>' . $cell . '</td>';

            $html .= '</tr>';
        }

        $html .= '</table>';

        return $html;
    }

    /**
     * Render to RTF
     */
    public function toRTF()
    {
        if (!$this->_parsed)
            throw new Exception();

        return '';
    }
}