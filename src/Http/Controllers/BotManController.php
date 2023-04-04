<?php

//packages/sergeynilov/QuizzesInit/src/Http/Controllers/BotManController.php
//namespace App\Http\Controllers;
namespace sergeynilov\QuizzesInit\Http\Controllers;

use sergeynilov\QuizzesInit\Exceptions\DbRepositoryInvalidDataException;
use sergeynilov\QuizzesInit\Library\AppLocale;
use sergeynilov\QuizzesInit\Library\Facades\QuizzesInitFacade;
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
http://local-quizzes.com/run-quiz/
*/

class BotManController extends Controller
{
    protected DbRepositoryInterface $dbRepositoryServiceInterface;
    private bool $quizzesDemoMode = false;
    private string $siteName;
    protected AppLocale $appLocale;

    public function __construct()
    {
        $this->dbRepositoryServiceInterface = \App::make(DbRepositoryInterface::class);
        $this->quizzesDemoMode = config('app.quizzes_in_debug_mode');
    }

    public function index(string $hashedLink = '')
    {
//        \Log::info(QuizzesInitFacade::varDump($hashedLink, ' -1 $hashedLink::'));
        //         ], $conversation_cache_time ?? $this->config['config']['conversation_cache_time'] ?? 30);

        // config/botman/config.php
        $conversationCacheTime                 = config('botman.config.conversation_cache_time');
        \Log::info(QuizzesInitFacade::varDump($conversationCacheTime, ' -1 $conversationCacheTime::'));
        //     'conversation_cache_time' => 10080, // 1 week

        \Cache::put('currentHashedLink', $hashedLink, $conversationCacheTime);
//        session()->put('currentHashedLink', $hashedLink);
        $currentUserQuizRequest = null;
        if ( ! empty($hashedLink)) {
            $currentUserQuizRequest = $this->dbRepositoryServiceInterface->getUserQuizRequestByHashedLink($hashedLink,
                true);
        }

//        \Log::info(QuizzesInitFacade::varDump($this->quizzesDemoMode, ' -1 $this->quizzesDemoMode::'));
        if (empty($currentUserQuizRequest) and $this->quizzesDemoMode) { // FOR DEBUGGING
            $currentUserQuizRequest = $this->loadDemoData();
            if(!empty($currentUserQuizRequest)) {
//                \Log::info(QuizzesInitFacade::varDump($currentUserQuizRequest['hashed_link'], ' -1 currentHashedLink $currentUserQuizRequest[hashed_link] ::'));
                \Cache::put('currentHashedLink', $currentUserQuizRequest['hashed_link'], $conversationCacheTime);
//                session()->put('currentHashedLink', $currentUserQuizRequest['hashed_link']);
            }
        }

        if ( ! empty($currentUserQuizRequest['quiz_category_id'])) {
            $quizCategory = $this->dbRepositoryServiceInterface->getQuizCategory($currentUserQuizRequest['quiz_category_id']);
        }

        $this->siteName = $this->dbRepositoryServiceInterface::getSettingsValue('site_name');

        $managerAccessText = '';
        if (Auth::user() and Auth::user()->can(ACCESS_PERMISSION_MANAGER_LABEL)) {
            $managerAccessText = __('common.As you have manager access, you can check results of quizzes');
        }

        return view('quiz.index',
            [
                                     'quizzesDemoMode'   => $this->quizzesDemoMode,
                                     'userQuizRequest'   => $currentUserQuizRequest,
                                     'quizCategoryName'  => $quizCategory['locale_name'] ?? '',
                                     'siteName'          => $this->siteName,
                                     'managerAccessText' => $managerAccessText,
                                     'quizCategoriesSelections' => $this->dbRepositoryServiceInterface::getQuizCategoriesSelections(true),
        ]);
    }

    protected function loadDemoData(): array
    {
        $userQuizRequest = $this->dbRepositoryServiceInterface->getFistNotPassedUserQuizRequest();
//        \Log::info(QuizzesInitFacade::varDump($userQuizRequest, ' -1 loadDemoData $userQuizRequest::'));
        return !empty($userQuizRequest) ? $userQuizRequest : [];
    }

    public function handle()
    {
        $this->siteName = $this->dbRepositoryServiceInterface::getSettingsValue('site_name');

        DriverManager::loadDriver(\BotMan\Drivers\Web\WebDriver::class);
        $config = array_merge([
            'config' => config('botman.config')
        ], [
            'web' => config('botman.web', [])
        ]);
        $botman = BotManFactory::create($config, new LaravelCache);

        $botman->hears('{message}', function ($botman, $message) {
//            \Log::info(QuizzesInitFacade::varDump($message, ' -1 $message::'));
            if ($message === 'start') {

                $currentUserQuizRequest = '';
//                $currentHashedLink = session()->get('currentHashedLink');
                $currentHashedLink = \Cache::get('currentHashedLink');
                \Log::info(QuizzesInitFacade::varDump($currentHashedLink, ' -10 $currentHashedLink::'));
                if ( ! empty($currentHashedLink)) {
                    $currentUserQuizRequest = $this->dbRepositoryServiceInterface->getUserQuizRequestByHashedLink($currentHashedLink,
                        true);
                    \Log::info(QuizzesInitFacade::varDump($currentHashedLink, ' -19 GOT NEW $currentHashedLink::'));
                }
                \Cache::forget('currentHashedLink');
//                if (empty($currentUserQuizRequest) and $this->quizzesDemoMode) { // FOR DEBUGGING
//                    $currentUserQuizRequest = $this->loadDemoData();
//                }

                if (empty($currentUserQuizRequest) or $currentUserQuizRequest['is_passed']) {
                    \Log::info(QuizzesInitFacade::varDump($currentUserQuizRequest, ' -1 This User quiz request does not exist or was already passed::'));
                    $message = OutgoingMessage::create('This User quiz request does not exist or was already passed !')
                        ->withAttachment(Image::url('/images/quiz_info.png'));
                    $botman->reply($message);
                    return;
                }

                $expiresAt = Carbon::parse($currentUserQuizRequest['expires_at'])->endOfDay();
                if ($expiresAt->isPast()) {
                    \Log::info(QuizzesInitFacade::varDump($currentUserQuizRequest, '  This User quiz request is already expired !'));
                    $message = OutgoingMessage::create('This User quiz request is already expired !')
                        ->withAttachment(Image::url('/images/quiz_info.png'));
                    $botman->reply($message);
                    return;
                }

                $quizzesWizard = new QuizzesWizard;
                $quizzesWizard->setUserQuizRequest($currentUserQuizRequest);
                $quizzesWizard->setShowCorrectQuizAnswerOnWrongAnswer(true);
                $quizzesWizard->setShowFeedbackOnCorrectWrongAnswer(true);
                $quizzesWizard->setShowFinalQuizResults(true);
                $quizzesWizard->setShowAskPreferableCommunicationChannel(true);
                $botman->startConversation($quizzesWizard);
            }

        });

        $botman->listen();
    }

    public function askName($botman)
    {
        $botman->ask('Hello! What is Your Name?', function (Answer $answer) {
            $name = $answer->getText();

            $this->say('Nice to meet you ' . $name);
        });
    }
}
