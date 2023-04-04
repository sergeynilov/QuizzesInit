<?php

namespace sergeynilov\QuizzesInit\Http\Controllers\Reports;

use sergeynilov\QuizzesInit\Library\Reports\CheckValidQuizzesData;
use sergeynilov\QuizzesInit\Http\Controllers\Controller;
use sergeynilov\QuizzesInit\Library\Reports\GenerateQuizzesReport;
use sergeynilov\QuizzesInit\Library\Services\Interfaces\DbRepositoryInterface;
use sergeynilov\QuizzesInit\Models\UserMeeting;
use sergeynilov\QuizzesInit\Providers\Reports\UserMeetingsReportDetails;
use sergeynilov\QuizzesInit\Providers\Reports\GenerateUserMeetingsReportDetailsWord;

class ReportsController extends Controller
{
    protected DbRepositoryInterface $dbRepositoryServiceInterface;

    public function __construct()
    {
        $this->dbRepositoryServiceInterface = \App::make(DbRepositoryInterface::class);
    }

    public function index()
    {
        $quizCategoriesAvailableReports = $this->dbRepositoryServiceInterface->getQuizCategories(inactive: null,
            currentLocale: null);
        $reportUserMeetings             = $this->dbRepositoryServiceInterface::getUserMeetingsByStatus(status:
            UserMeeting::USER_MEETING_STATUS_WAITING_FOR_REVIEW, onlyExpired: false);
//        testCheckValidQuizzesData

        $companyName = $this->dbRepositoryServiceInterface::getSettingsValue('company_name');

        $checkValidQuizzesData = new CheckValidQuizzesData();
        $checkValidQuizzesData->setCheckEmptyQuizCategories(value: true, includeInactive: true);
        $checkValidQuizzesData->setCheckTheOnlyIsCorrectQuizAnswer(value: true, includeInactive: true);
        $checkValidQuizzesData->setCheckQuizzesWithAllLocales(value: true, includeInactive: true);
        $checkValidQuizzesData->makeDataStatistics();
        $checkValidQuizzesData->makeChecking();
        $quizzesDataStatistics = $checkValidQuizzesData->getResults();

//        echo '<pre>'.count($quizzesDataStatistics).'::$quizzesDataStatistics::'.print_r($quizzesDataStatistics,true).'</pre>';

        return view('QuizzesInit::available-reports',
            [
                'quizzesDataStatistics'          => $quizzesDataStatistics,
                'quizCategoriesAvailableReports' => $quizCategoriesAvailableReports,
                'reportUserMeetings'             => $reportUserMeetings,
                'companyName'                    => $companyName,
            ]);
    }

    public function showQuizCategory($quizCategoryId)
    {
        try {
            $generateQuizzesReport = new GenerateQuizzesReport();
            $generateQuizzesReport->setQuizCategoryId($quizCategoryId);
            $generateQuizzesReport->setShowIsCorrect(true);
            $generateQuizzesReport->setTableStyle('border: 2px double black; width: 100%; padding: 4px; margin: 4px; ');
            $generateQuizzesReport->setTableTdStyle('width: 100%; padding: 2px; margin: 2px');
            $generateQuizzesReport->setShowCreatedAt(true);
            $quizCategory = $this->dbRepositoryServiceInterface::getQuizCategory($quizCategoryId);
            $generateQuizzesReport->setQuizCategory($quizCategory);

            $includeAnswers = true;
            $quizzes        = $this->dbRepositoryServiceInterface::getQuizzesByQuizCategoryId($quizCategoryId, true);
            foreach ($quizzes as $key => $quiz) {
                if ($includeAnswers) {
                    $quizAnswers                  = $this->dbRepositoryServiceInterface::getQuizAnswersByQuizId($quiz['id']);
                    $quizzes[$key]['quizAnswers'] = $quizAnswers;
                }
            }
            $generateQuizzesReport->setQuizzes($quizzes);

            return $generateQuizzesReport->generate();
        } catch (\Exception | \Error $e) {
            echo '<pre>::$e->getMessage()::' . print_r($e->getMessage(), true) . '</pre>';
            die("-1 XXZ Exception");
//            \Log::info(QuizzesInitFacade::varDump($e->getMessage(), ' -1 $e->getMessage()::'));
//             \App\Library\AppCustomException::getInstance()::raiseChannelError(
//                errorMsg: $e->getMessage(),
//                exceptionClass: \Exception::class,
//                file: __FILE__,
//                line: __LINE__
//            );
        }
    }

    public function comingUserMeetingsReportShow($id)
    {
        $userMeeting = UserMeeting
            ::getById($id)
            ->with('userQuizRequest')
            ->with('userQuizRequest.quizCategory')
            ->with('userQuizRequest.userQuizzesHistory')
            ->with('userQuizRequest.userQuizzesHistory.userQuizzesHistoryDetails')
            ->firstOrFail()->toArray();

        if ($userMeeting['user_quiz_request']['user_quizzes_history']['summary_points'] === 0) {
            $userMeeting['summary_points_percent'] = 0;
        } else {
            $userMeeting['summary_points_percent'] = round($userMeeting['user_quiz_request']['user_quizzes_history']['summary_points'] / $userMeeting['user_quiz_request']['user_quizzes_history']['max_summary_points'] * 100,
                2);
        }

        $selectedLocale = $userMeeting['user_quiz_request']['user_quizzes_history']['selected_locale'];

        return view('coming-user-meetings-report-show', [
            'userMeeting'    => $userMeeting,
            'selectedLocale' => $selectedLocale,
        ]);
    }

    public function comingUserMeetingsReportGenerate($id)
    {
        $userMeetingsReportDetails = new UserMeetingsReportDetails();

        $userMeetingsReportDetails->setUserMeetingId($id);
        $userMeetingsReportDetails->retrieveUserMeetingDetails();
        $userMeetingsDetails                   = $userMeetingsReportDetails->getUserMeetingData();
        $generateUserMeetingsReportDetailsWord = new GenerateUserMeetingsReportDetailsWord();
        $generateUserMeetingsReportDetailsWord->setUserMeetingsDetails($userMeetingsDetails);
        $generateUserMeetingsReportDetailsWord->generate();

        return $generateUserMeetingsReportDetailsWord->download();
    }

}
