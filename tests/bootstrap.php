<?php

use Tester\Environment;

require __DIR__ . '/../vendor/autoload.php';

Environment::setup();
Mockery::getConfiguration()->allowMockingNonExistentMethods(FALSE);
