<?php

namespace sergeynilov\QuizzesInit\Enums;

use BenSampo\Enum\Enum;

final class DatetimeOutputFormat extends Enum
{
    public const dofAgoFormat =   'ago_format';
    public const dofAsText =   'astext';
    public const dofAsNumbers = 'numbers';
}
