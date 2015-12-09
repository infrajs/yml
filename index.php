<?php

namespace infrajs\yml;

if (!is_file('vendor/autoload.php')) {
    chdir('../../../');
}
require_once('vendor/autoload.php');


Yml::show();