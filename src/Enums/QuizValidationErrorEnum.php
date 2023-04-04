<?php declare(strict_types=1);

namespace sergeynilov\QuizzesInit\Enums;

use BenSampo\Enum\Enum;

final class QuizValidationErrorEnum extends Enum
{
    const QVE_NUMBER_OF_LOCALES = 'NumberOfLocales';
    const QVE_NUMBER_OF_QUIZZES = 'NumberOfQuizzes';
    const QVE_NUMBER_OF_QUIZ_ANSWERS = 'NumberOfQuizAnswers';
    const QVE_NUMBER_OF_NEW_USER_QUIZ_REQUESTS = 'NumberOfNewUserQuizRequests';
    const QVE_NUMBER_OF_PASSED_USER_QUIZ_REQUESTS = 'NumberOfPassedUserQuizRequests';
    const QVE_NUMBER_OF_EXPIRED_USER_QUIZ_REQUESTS = 'NumberOfExpiredUserQuizRequests';

//    const QVE_NUMBER_OF_WAITING_FOR_REVIEW_USER_MEETINGS = 'NumberOfWaitingForReviewUserMeetings';
    const QVE_NUMBER_OF_ACCEPTED_FOR_MEETING_USER_MEETINGS = 'NumberOAcceptedForMeetingUserMeetings';
    const QVE_NUMBER_OF_MARKED_FOR_FUTURE_CONTACTS_USER_MEETINGS = 'NumberOfMarkedForFutureContactsUserMeetings';
    /*              self::USER_MEETING_STATUS_WAITING_FOR_REVIEW   => 'Waiting for review',
            self::USER_MEETING_STATUS_ACCEPTED_FOR_MEETING   => 'Accepted for meeting',
            self::USER_MEETING_STATUS_MARKED_FOR_FUTURE_CONTACTS   => 'Marked for future contacts',
 */

    const QVE_EMPTY_QUIZ_CATEGORIES = 'EmptyQuizCategories';
    const QVE_EMPTY_QUIZ_CATEGORIES_IDS = 'EmptyQuizCategoriesIds';
    const QVE_HAS_MORE_ONE_IS_CORRECT_QUIZ_ANSWERS = 'HasMoreOneIsCorrectQuizAnswers';
    const QVE_MORE_ONE_IS_CORRECT_QUIZ_ANSWERS_IDS = 'MoreOneIsCorrectQuizAnswersIds';

    const QVE_HAS_NO_IS_CORRECT_QUIZ_ANSWERS = 'HasNoIsCorrectQuizAnswers';
    const QVE_HAS_NO_IS_CORRECT_QUIZ_ANSWERS_IDS = 'HasNoIsCorrectQuizAnswersIds';

    const QVE_QUIZZES_WITH_EMPTY_LOCALES_COUNT = 'QuizzesWithEmptyLocalesCount';
    const QVE_QUIZZES_WITH_EMPTY_LOCALES_IDS = 'QuizzesWithEmptyLocalesIds';

    const QVE_QUIZ_ANSWERS_WITH_EMPTY_LOCALES_COUNT = 'QuizAnswersWithEmptyLocalesCount';
    const QVE_QUIZ_ANSWERS_WITH_EMPTY_LOCALES_IDS = 'QuizAnswersWithEmptyLocalesIds';
}
