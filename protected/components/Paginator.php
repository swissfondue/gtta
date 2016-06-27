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

        $limit = Yii::app()->params["entriesPerPage"];

        if (Yii::app()->request && isset(Yii::app()->request->cookies["per_page_item_limit"])) {
            $limit = (int) Yii::app()->request->cookies["per_page_item_limit"]->value;
        }

        if ($limit > 0) {
            $this->pageCount = (int) $this->entryCount / $limit;
        } else {
            if ($this->entryCount) {
                $this->pageCount = (int) $this->entryCount / $this->entryCount;
            } else {
                $this->pageCount = 0;
            }
        }

        if ($this->entryCount % $limit > 0)
            $this->pageCount += 1;

        if ($this->pageCount == 0)
            $this->pageCount = 1;

        if ($this->page > $this->pageCount) {
            throw new CHttpException(404, 'Page not found.');
        }

        $this->prevPage = 0;
        $this->nextPage = 0;

        if ($this->page > 1)
            $this->prevPage = $this->page - 1;

        if ($this->page < $this->pageCount)
            $this->nextPage = $this->page + 1;
    }
}
