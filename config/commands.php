<?php

use App\Commands\AddCommand;
use App\Commands\TestCommand;

return [

    'test' => [TestCommand::class, 'test'],
    'add'  => [AddCommand::class,  'add' ],

];
