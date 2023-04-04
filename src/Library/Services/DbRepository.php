<?php

namespace sergeynilov\QuizzesInit\Library\Services;

use Illuminate\Database\Eloquent\Model;
use sergeynilov\QuizzesInit\Library\Services\Interfaces\DbRepositoryInterface;
use Carbon\Carbon;
use Illuminate\Support\MessageBag;
use sergeynilov\QuizzesInit\Models\{Quiz, QuizAnswer, QuizCategory, UserMeeting, UserQuizzesHistory, UserQuizzesHistoryDetail};
use sergeynilov\QuizzesInit\Models\{Settings, UserQuizRequest, UserQuizRequestCommunicationChannel};
use DB;
use Exception;
use sergeynilov\QuizzesInit\Exceptions\DbRepositoryInvalidDataException;
use DateConv;

class DbRepository implements DbRepositoryInterface
{
    protected static string $currentLocale = 'en';

    public function __construct()
    {
    }

    ///// Settings/settings Model/Table block BEGIN /////
    public static function setCurrentLocale(string $currentLocale): void
    {
        DbRepository::$currentLocale = $currentLocale;
    }
    ///// Settings/settings Model/Table block END /////

    ///// Settings/settings Model/Table block BEGIN /////

    public static function getSettingsValue(string $paramName): string
    {
        $settings = Settings::getByName($paramName)->first();

        return $settings->value ?? '';
    }
    ///// Settings/settings Model/Table block END /////

    ///// QuizCategory/quiz_categories Model/Table block BEGIN /////

    public static function getQuizCategories(bool $inactive = null, string $currentLocale = null): array
    {
        return QuizCategory::getByActive($inactive)
            ->withCount('quizzes')
            ->get()
            ->map(function ($quizItem) {
                $quizItem->locale_name = $quizItem->getTranslation('name', $currentLocale ?? DbRepository::$currentLocale);

                return $quizItem;
            })
            ->toArray();
    }


    public static function getQuizCategory(int $quizCategoryId): array
    {
        $quizCategory                     = QuizCategory::findOrFail($quizCategoryId);
        $quizCategoryArray                = $quizCategory->toArray();
        $quizCategoryArray['locale_name'] = $quizCategory->getTranslation('name', DbRepository::$currentLocale);

        return $quizCategoryArray;
    }

    public static function getEmptyQuizCategories(bool $includeInactive = null): array
    {
        return QuizCategory::getByIncludeInactive($includeInactive)
            ->whereDoesntHave('quizzes')
            ->get()
            ->toArray();
    }

    public static function getQuizCategoriesSelections(bool $active = null): array
    {
        return QuizCategory::getQuizCategoriesSelectionArray($active);
    }

    ///// QuizCategory/quiz_categories Model/Table block END /////

    ///// Quiz/quizzes Model/Table block BEGIN /////

    public static function getQuizzesByByIncludeInactive(bool $includeInactive = null): array
    {
        $quizzes = Quiz::getByIncludeInactive($includeInactive)->get();

        return $quizzes->toArray();
    }

    public static function getQuizzesWithTooManyIsCorrect(bool $includeInactive = null): array
    {
        return Quiz::getByIncludeInactive($includeInactive)
            ->whereHas('quizAnswers', function ($quizAnswer) {
                $quizAnswer->where('quiz_answers.is_correct', true)
                    ->select('quiz_answers.quiz_id', DB::raw('count(*) as total_quiz_answers'))
                    ->groupBy('quiz_answers.quiz_id')
                    ->havingRaw('count(*) > 1');
            })
            ->get()->toArray();
    }

    public static function getQuizzesWithoutIsCorrect(bool $includeInactive = null): array
    {
        return Quiz::getByIncludeInactive($includeInactive)
            ->whereDoesntHave('quizAnswers', function ($quizAnswer) {
                $quizAnswer->where('quiz_answers.is_correct', true);
            })
            ->get()
            ->toArray();
    }

    public static function getQuizzesByQuizCategoryId(int $quizCategoryId, bool $active = null): array
    {
        $quizzes = Quiz::getByActive($active)
            ->getByQuizCategoryId($quizCategoryId)
            ->get()
            ->map(function ($quizItem) {
                $quizItem->locale_question = $quizItem->getTranslation('question', DbRepository::$currentLocale);

                return $quizItem;
            });

        return $quizzes->toArray();
    }

    public static function saveUserQuizzesHistory(
        array $quizCategory,
        array $selectedQuizAnswers,
        string $selectedLocale,
        int $timeSpent,
        int $summaryOfPoints,
        int $maxSummaryOfPoints,
        array $userQuizRequest
    ): int|MessageBag {
//        try {
        DB::beginTransaction();
        $userQuizzesHistory = UserQuizzesHistory::create([
            'user_quiz_request_id' => $userQuizRequest['id'],
            'quiz_category_id'     => $quizCategory['id'],
            'quiz_category_name'   => $quizCategory['locale_name'],
            'selected_locale'      => $selectedLocale,
            'user_name'            => $userQuizRequest['user_name'],
            'user_email'           => $userQuizRequest['user_email'],
            'time_spent'           => $timeSpent,
            'summary_points'       => $summaryOfPoints,
            'max_summary_points'   => $maxSummaryOfPoints,
            'is_reviewed'          => false,
        ]);

        foreach ($selectedQuizAnswers as $selectedQuizAnswer) {
            $quizAnswer = QuizAnswer::findOrFail($selectedQuizAnswer['quiz_answer_id']);
            $quizAnswer->locale_text = $quizAnswer->getTranslation('text', DbRepository::$currentLocale);
            UserQuizzesHistoryDetail::create([
                'user_quizzes_history_id' => $userQuizzesHistory->id,
                'quiz_answer_id'          => $selectedQuizAnswer['quiz_answer_id'],
                'text'                    => $quizAnswer['locale_text'],
                'is_correct'              => $quizAnswer->is_correct,
                'quiz_points'             => $selectedQuizAnswer['quiz_points'],
            ]);
        }

        UserMeeting::create([
            'user_quiz_request_id' => $userQuizRequest['id'],
            'name'                 => 'Interview for user_name from ' . DateConv::getFormattedDateTime(Carbon::now(config('app.timezone'))),
            'user_name'            => $userQuizRequest['user_name'],
            'user_email'           => $userQuizRequest['user_email'],
            'appointed_at'         => null,
            'status'               => UserMeeting::USER_MEETING_STATUS_WAITING_FOR_REVIEW,
        ]);
        $userQuizRequest = UserQuizRequest::find($userQuizRequest['id']);
        if ( ! empty($userQuizRequest)) {
            $userQuizRequest->is_passed = true; // UNCOMMENTED
//                $userQuizRequest->selected_locale   = $selectedLocale;
//                $userQuizRequest->time_spent        = $timeSpent;
//                $userQuizRequest->summary_of_points = $summaryOfPoints;
            $userQuizRequest->updated_at = Carbon::now(config('app.timezone'));
            $userQuizRequest->save();
        }
        DB::commit();

        return $userQuizzesHistory->id;
    }
    ///// QuizCategory/quiz_categories Model/Table block END /////


    ///// QuizAnswer/quiz_answers Model/Table block BEGIN /////
    public static function getQuizAnswersByQuizId($quizId): array
    {
        $quizAnswers = QuizAnswer::getByQuizId($quizId)
            ->inRandomOrder()
            ->get()
            ->map(function ($quizAnswerItem) {
                $quizAnswerItem->locale_text = $quizAnswerItem->getTranslation('text', DbRepository::$currentLocale);

                return $quizAnswerItem;
            });

        return $quizAnswers->toArray();
    }

    public static function getQuizAnswer(int $quizAnswerId): array
    {
        $quizAnswer              = QuizAnswer::findOrFail($quizAnswerId);
        $quizAnswer->locale_text = $quizAnswer->getTranslation('text', DbRepository::$currentLocale);

        return $quizAnswer->toArray();
    }

    public static function getCorrectQuizAnswer(int $quizId): array
    {
        $correctQuizAnswer = QuizAnswer::getByQuizId($quizId)->getByIsCorrect(true)->first();
        throw_if(
            empty($correctQuizAnswer),
            DbRepositoryInvalidDataException::class,
            'Correct quiz answer for quiz with id: ' . $quizId . ' not found'
        );

        $correctQuizAnswer->locale_text = $correctQuizAnswer->getTranslation('text', DbRepository::$currentLocale);

        return $correctQuizAnswer->toArray();
    }
    ///// QuizAnswer/quiz_answers Model/Table block END /////

    ///// UserQuizRequest/user_quiz_requests Model/Table block BEGIN /////
    public static function getUserQuizRequestByHashedLink(string $hashedLink, bool $onlyNotIsPassed): array
    {
        if ($onlyNotIsPassed) {
            $userQuizRequest = UserQuizRequest::getByHashedLink($hashedLink)
                ->getOnlyNotPassed()
                ->with('quizCategory:id,name')
                ->first();
        } else {
            $userQuizRequest = UserQuizRequest::getByHashedLink($hashedLink)
                ->with('quizCategory:id,name')
                ->first();
        }

        return ! empty($userQuizRequest) ? $userQuizRequest->toArray() : [];
    }

    public static function getExpiredUserQuizRequests(): array
    {
        $userQuizRequests = UserQuizRequest
            ::getByIsPassed(false)
            ->getByOnlyExpired(true)
            ->get();

        return ! empty($userQuizRequests) ? $userQuizRequests->toArray() : [];
    }



    public static function storeUserQuizRequest(array $data): array|MessageBag
    {
        try {
            DB::beginTransaction();

            $userQuizRequest = UserQuizRequest::creaYYYYte($data);

            DB::commit();

            return $userQuizRequest->toArray();
        } catch (Exception $e) {
            DB::rollBack();

            echo '<pre>$e->getMessage()::' . print_r($e->getMessage(), true) . '</pre>';

            return \App\Library\AppCustomException::getInstance()::raiseChannelError(
                errorMsg: $e->getMessage(),
                exceptionClass: \Exception::class,
                file: __FILE__,
                line: __LINE__
            );
        }

    }

    public static function getFistNotPassedUserQuizRequest(): array
    {
        $userQuizRequest = UserQuizRequest::getOnlyNotPassed()->first();

        return ! empty($userQuizRequest) ? $userQuizRequest->toArray() : [];
    }

    public static function getUserQuizRequests(bool $isPassed = false, bool $onlyExpired = false): array
    {
        $userQuizRequests = UserQuizRequest
            ::getByIsPassed($isPassed)
            ->getByOnlyExpired($onlyExpired)
            ->get();

        return ! empty($userQuizRequests) ? $userQuizRequests->toArray() : [];
    }

    public static function getOnlyExpiredUserQuizRequests(): array
    {
        $userQuizRequests = UserQuizRequest
            ::getByIsPassed(false)
            ->getByOnlyExpired(true)
            ->get();

        return ! empty($userQuizRequests) ? $userQuizRequests->toArray() : [];
    }

    ///// UserQuizRequest/user_quiz_requests Model/Table block END /////


    ///// UserMeeting/user_meetings Model/Table block START /////
    public static function getUserMeetingsByStatus(string $status = null, bool $onlyExpired = false): array
    {
        $userMeetings = UserMeeting
        ::getByStatus($status)
            ->with('userQuizRequest.userQuizzesHistory')
            ->get()
            ->map(function ($userMeetingItem) {
                if($userMeetingItem->userQuizRequest->userQuizzesHistory->summary_points === 0) {
                    $userMeetingItem->summary_points_percent = 0;
                } else {
                    $userMeetingItem->summary_points_percent = round($userMeetingItem->userQuizRequest->userQuizzesHistory->summary_points / $userMeetingItem->userQuizRequest->userQuizzesHistory->max_summary_points * 100, 2);
                }
                return $userMeetingItem;
            })->sortByDesc('summary_points_percent');
        return ! empty($userMeetings) ? $userMeetings->toArray() : [];
    }

    public static function getUserMeetingById(int $id, bool $details = false): array
    {
        if ( ! $details) {
            $userMeeting = UserMeeting // TODO
            ::getByStatus($id)
                ->firstOrFail();
        } else {
            $userMeeting = UserMeeting // TODO
            ::getById($id)
                ->with('userQuizRequest')
                ->with('userQuizRequest.quizCategory')
                ->with('userQuizRequest.userQuizzesHistory')
                ->with('userQuizRequest.userQuizzesHistory.userQuizzesHistoryDetails')
                ->firstOrFail();
        }

        return ! empty($userMeeting) ? $userMeeting->toArray() : [];
    }
    ///// UserMeeting/user_meetings Model/Table block END /////


    ///// UserQuizzesHistory/user_quizzes_history Model/Table block BEGIN /////

    public static function getUserQuizzesHistoriesForReview(): array
    {
        $userQuizzesHistoriesForReview = UserQuizzesHistory::getByIsReviewed(UserQuizzesHistory::IS_REVIEWED_NO)
            ->orderBy('created_at', 'asc')
            ->get();

        return $userQuizzesHistoriesForReview->toArray();
    }
    ///// UserQuizzesHistory/user_quizzes_history Model/Table block END /////


    ///// UserQuizRequestCommunicationChannel/user_quiz_request_communication_channels Model/Table block START /////
    public static function addUserQuizRequestCommunicationChannel(
        int $userQuizRequestId,
        string $type,
        string $channel
    ): int|MessageBag {
        try {
            DB::beginTransaction();
            $userQuizRequestCommunicationChannel = UserQuizRequestCommunicationChannel::create([
                'user_quiz_request_id' => $userQuizRequestId,
                'type'                 => $type,
                'channel'              => $channel,
            ]);

            DB::commit();

            return $userQuizRequestCommunicationChannel->id;
        } catch (Exception $e) {
            DB::rollBack();

            return \App\Library\AppCustomException::getInstance()::raiseChannelError(
                errorMsg: $e->getMessage(),
                exceptionClass: \Exception::class,
                file: __FILE__,
                line: __LINE__
            );
        }
    }

    ///// UserQuizRequestCommunicationChannel/user_quiz_request_communication_channels Model/Table block END /////
}
