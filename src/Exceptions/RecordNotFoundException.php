<?php

namespace SwithFr\SqlQuery\Exceptions;

use JetBrains\PhpStorm\Internal\LanguageLevelTypeAware;

class RecordNotFoundException extends \Exception
{
    public function __construct()
    {
        parent::__construct("Record not found", 404, null);
    }
}