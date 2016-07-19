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
    public function defaultL10n($languages, $itemName, $field = false)
    {
        $values = $this->localizedItems;

        if ($field) {
            $values = $this->fields;
        }

        $value   = '';
        $default = null;

        foreach ($languages as $lang)
            if ($lang->default)
            {
                $default = $lang;
                break;
            }

        if ($default && isset($values[$default->id][$itemName]))
            $value = $values[$default->id][$itemName];

        if ($value === '')
            foreach ($languages as $lang)
            {
                if (isset($values[$lang->id][$itemName]))
                {
                    $value = $values[$lang->id][$itemName];

                    if ($value !== '')
                        break;
                }
            }

        return $value;
    }
}
