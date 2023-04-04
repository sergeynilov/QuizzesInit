<?php

namespace sergeynilov\QuizzesInit\Http\Controllers;

use sergeynilov\QuizzesInit\Exceptions\DbRepositoryInvalidDataException;
use sergeynilov\QuizzesInit\Models\UserQuizRequest;
use sergeynilov\QuizzesInit\Library\AppLocale;
use sergeynilov\QuizzesInit\Library\ComplicatedRequestsTester;
use sergeynilov\QuizzesInit\Library\QuizzesWizard;

//use sergeynilov\QuizzesInit\Library\ManagerChecksNewUserQuizzes;
use sergeynilov\QuizzesInit\Library\Services\Interfaces\DbRepositoryInterface;
use BotMan\BotMan\BotMan;
use BotMan\BotMan\BotManFactory;
use BotMan\BotMan\Cache\LaravelCache;
use BotMan\BotMan\Drivers\DriverManager;
use BotMan\BotMan\Messages\Attachments\Image;
use BotMan\BotMan\Messages\Incoming\Answer;
use BotMan\BotMan\Messages\Outgoing\OutgoingMessage;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Auth;

/*
 *
http://local-quizzes.com/test-complicated-requests-tester-methods
*/

class ComplicatedRequestsTesterMethodsController extends Controller
{
    protected DbRepositoryInterface $dbRepositoryServiceInterface;
    private bool $quizzesDemoMode = false;
    private string $siteName;
    protected AppLocale $appLocale;
    protected $complicatedRequestsTester;

    public function __construct()
    {
        $this->dbRepositoryServiceInterface = \App::make(DbRepositoryInterface::class);
        $this->quizzesDemoMode              = config('app.quizzes_in_debug_mode');
        $this->complicatedRequestsTester              = new ComplicatedRequestsTester();
    }
    // http://local-quizzes.com/test-complicated-requests-tester-methods
    public function index()
    {



//        $this->complicatedRequestsTester->checkIsMethodFormMdels();
//        $this->complicatedRequestsTester->getPercentageOfValidAnswerByQuizCategoryId(1);
        $userQuizRequests = UserQuizRequest::getById(1)
            ->where('is_passed', false)
            ->where('user_name', 'like', '%John Doe%')
            ->get();

        echo '<pre>$userQuizRequests->toArray()::'.print_r($userQuizRequests->toArray(),true).'</pre>';
/* 'user_name', 'user_quiz_request_id', 'user_email', 'is_passed'
        echo '<pre>'.count($userQuizRequests).'::$userQuizRequests::'.print_r($userQuizRequests,true).'</pre>'; */
        /* get all valid Quiz answers from by userQuizRequestId */

//        $this->complicatedRequestsTester->getValidQuizAnswersFromUserQuizRequestId(1);


        /* get all valid Quiz answers from by userQuizRequestId with
quiz_points >= $quizPoints */
//        public function getValidQuizAnswersFromUserQuizRequestIdWithQuizPoints($userQuizRequestId, $quizPoints)
//        $this->complicatedRequestsTester->getValidQuizAnswersFromUserQuizRequestIdWithQuizPoints(1, 2);
    }
}
