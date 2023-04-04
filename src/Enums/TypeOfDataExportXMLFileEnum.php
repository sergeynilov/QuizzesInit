<?php declare(strict_types=1);

namespace sergeynilov\QuizzesInit\Enums;

use BenSampo\Enum\Enum;

/**
 * @method static static OptionOne()
 * @method static static OptionTwo()
 * @method static static OptionThree()
 */
final class TypeOfDataExportXMLFileEnum extends Enum
{
    const TYDEXFE_LOCALES = 'Locales';
    const TYDEXFE_QUIZZES = 'Quizzes';
    const TYDEXFE_QUIZ_ANSWERES = 'QuizAnswers';
}
