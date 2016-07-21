<?php
    /** @var TargetCheck $check */
    $ratings = TargetCheck::getRatingNames();

    switch ($check->rating) {
        case TargetCheck::RATING_INFO:
            echo "<span class=\"label label-info\">" . $ratings[TargetCheck::RATING_INFO] . "</span>";
            break;

        case TargetCheck::RATING_LOW_RISK:
            echo "<span class=\"label label-low-risk\">" . $ratings[TargetCheck::RATING_LOW_RISK] . "</span>";
            break;

        case TargetCheck::RATING_MED_RISK:
            echo "<span class=\"label label-med-risk\">" . $ratings[TargetCheck::RATING_MED_RISK] . "</span>";
            break;

        case TargetCheck::RATING_HIGH_RISK:
            echo "<span class=\"label label-high-risk\">" . $ratings[TargetCheck::RATING_HIGH_RISK] . "</span>";
            break;

        default:
            echo "<span class=\"label\">" . $ratings[$check->rating] . "</span>";
            break;
    }
?>