<?php declare(strict_types=1);

namespace sergeynilov\QuizzesInit\Enums;

use BenSampo\Enum\Enum;

final class WordTextLineEnum extends Enum
{
    const WTL_HEADER_TEXT = 'HeaderText';
    const WTL_SUBHEADER_TEXT = 'SubHeaderText';
    const WTL_CONTENT_TEXT = 'ContentText';
    const WTL_NOTICE_TEXT = 'NoticeText';
}
