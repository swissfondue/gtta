<?php

/**
 * Paginator class.
 */
class Paginator
{
    public $page;
    public $prevPage;
    public $nextPage;

    public $entryCount;
    public $pageCount;

    /**
     * Constructor.
     */
    public function __construct($entryCount, $page)
    {
        $this->page       = $page;
        $this->entryCount = $entryCount;

        $this->pageCount = (int) ($this->entryCount / Yii::app()->params['entriesPerPage']);

        if ($this->entryCount % Yii::app()->params['entriesPerPage'] > 0)
            $this->pageCount += 1;

        if ($this->pageCount == 0)
            $this->pageCount = 1;

        if ($this->page > $this->pageCount)
            throw new CHttpException(404, 'Page not found.');

        $this->prevPage = 0;
        $this->nextPage = 0;

        if ($this->page > 1)
            $this->prevPage = $this->page - 1;

        if ($this->page < $this->pageCount)
            $this->nextPage = $this->page + 1;
    }
}
