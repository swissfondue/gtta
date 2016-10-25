<?php

return mergeArray(loadConfig(dirname(__FILE__), "params.template"), [
    "baseUrl" => "https://gtta.local",
]);
