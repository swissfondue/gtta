<?php

return mergeArray(loadConfig(dirname(__FILE__), "params.template"), array(
    "baseUrl" => "https://gtta.local",
));
