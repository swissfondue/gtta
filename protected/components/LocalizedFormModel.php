<?php

/**
 * Base class for localized form model.
 */
class LocalizedFormModel extends CFormModel
{
    /**
     * @var array localized items.
     */
    public $localizedItems;

    /**
     * Get default value for localized item.
     */
    public function defaultL10n($languages, $itemName)
    {
        $value   = '';
        $default = null;

        foreach ($languages as $lang)
            if ($lang->default)
            {
                $default = $lang;
                break;
            }

        if ($default && isset($this->localizedItems[$default->id][$itemName]))
            $value = $this->localizedItems[$default->id][$itemName];

        if ($value === '')
            foreach ($languages as $lang)
            {
                if (isset($this->localizedItems[$lang->id][$itemName]))
                {
                    $value = $this->localizedItems[$lang->id][$itemName];

                    if ($value !== '')
                        break;
                }
            }

        return $value;
    }
}
