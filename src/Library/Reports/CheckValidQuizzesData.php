<?php


namespace sergeynilov\QuizzesInit\Library\Reports;

use App;
use sergeynilov\QuizzesInit\Enums\QuizValidationErrorEnum;
use sergeynilov\QuizzesInit\Library\AppLocale;
use sergeynilov\QuizzesInit\Library\Facades\QuizzesInitFacade;
use sergeynilov\QuizzesInit\Library\Services\Interfaces\DbRepositoryInterface;
use DB;
use Illuminate\Support\Str;

class CheckValidQuizzesData
{

    /* Test
    http://local-quizzes.com/test-check-valid-quizzes-data
         */

    /**
     *
     * Make validation for all banners by set of rules
     *
     * Example of use :
     *
     * $validationResult = (new CheckValidQuizzesData)
     * ->setMinTextLength(2)
     * ->setMaxTextLength(25)
     * ->setCheckBannerLogoImageFileUrl(true)
     * ->setCheckBgImageFileUrl(true)
     * ->setCheckOnlyActive(true)
     * ->validate()
     * ->getResults();
     * Create a new CheckValidQuizzesData instance.
     *
     * @return void
     */

    protected bool $checkEmptyQuizCategories = false;
    protected bool $checkEmptyQuizCategoriesIncludeInactive = false;

    protected bool $checkQuizzesWithAllLocales = false;
    protected bool $checkQuizzesWithAllLocalesIncludeInactive = false;


    protected int $emptyQuizCategoriesCount = 0;
    protected array $emptyQuizCategoriesIds = [];

    protected int $quizzesWithEmptyLocalesCount = 0;
    protected array $quizzesWithEmptyLocalesIds = [];

    protected int $quizAnswersWithEmptyLocalesCount = 0;
    protected array $quizAnswersWithEmptyLocalesIds = [];

    protected bool $checkTheOnlyIsCorrectQuizAnswer = false;
    protected bool $checkTheOnlyIsCorrectQuizAnswerIncludeInactive = false;

    protected array $results = [];
    /* @var DbRepositoryInterface implementation of DbRepositoryInterface provides all storage methods for data retrieving/saving */
    protected DbRepositoryInterface $dbRepositoryServiceInterface;


    /**
     *
     * Make validation for all banners by set of rules
     *
     * Example of use :
     *
     * $validationResult = (new CheckBannersBuilder)
     * ->setMinTextLength(2)
     * ->setMaxTextLength(25)
     * ->setCheckBannerLogoImageFileUrl(true)
     * ->setCheckBgImageFileUrl(true)
     * ->setCheckOnlyActive(true)
     * ->validate()
     * ->getResults();
     * Create a new CheckBannersBuilder instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->dbRepositoryServiceInterface = App::make(DbRepositoryInterface::class);
    }

    /*
    *
     * @param bool $value - Check that any quiz qustion for all locates filled
     *
     * @return self
     * */
    public function setCheckEmptyQuizCategories(bool $value, bool $includeInactive = false): self
    {
        $this->checkEmptyQuizCategories                = $value;
        $this->checkEmptyQuizCategoriesIncludeInactive = $includeInactive;

        return $this;
    }

//* @param bool $value - Check that any quiz/quizAnswers has all locate filled for question/text fields

    /*
    *
     * @param bool $value - Check that any quiz/quiz_answers question/text fields for all locates filled
     *
     * @return self
     * */
    public function setCheckQuizzesWithAllLocales(bool $value, bool $includeInactive = false): self
    {
        $this->checkQuizzesWithAllLocales                = $value;
        $this->checkQuizzesWithAllLocalesIncludeInactive = $includeInactive;

        return $this;
    }


    /*
    *
     * @param bool $value - Check that any quiz has only 1 Is Correct quiz answer
     *
     * @return self
     * */
    public function setCheckTheOnlyIsCorrectQuizAnswer(bool $value, bool $includeInactive = false): self
    {
        $this->checkTheOnlyIsCorrectQuizAnswer                = $value;
        $this->checkTheOnlyIsCorrectQuizAnswerIncludeInactive = $includeInactive;

        return $this;
    }

    public function makeChecking(): self
    {
        if ($this->checkEmptyQuizCategories) {
            $this->makeCheckEmptyQuizCategories();
        }

        if ($this->checkQuizzesWithAllLocales) {
            $this->makeCheckQuizzesWithAllLocales();
            $this->makeCheckQuizAnswersWithAllLocales();
        }

        if ($this->checkTheOnlyIsCorrectQuizAnswer) {
            $this->makeCheckTheOnlyIsCorrectQuizAnswer();
        }

        return $this;
    }

    public function makeDataStatistics(): self
    {
        $quizAnswersCount = 0;
        $quizzes          = $this->dbRepositoryServiceInterface::getQuizzesByByIncludeInactive($this->checkQuizzesWithAllLocalesIncludeInactive);
        foreach ($quizzes as $quiz) {
            $quizAnswersCount += count($this->dbRepositoryServiceInterface::getQuizAnswersByQuizId($quiz['id']));
        }

        $newUserQuizRequests = $this->dbRepositoryServiceInterface::getUserQuizRequests(isPassed: false, onlyExpired:
            false);
//        \Log::info(QuizzesInitFacade::varDump($newUserQuizRequests, ' -1 $newUserQuizRequests::'));

        $passedUserQuizRequests = $this->dbRepositoryServiceInterface::getUserQuizRequests(isPassed: true, onlyExpired:
            false);
//        \Log::info(varDump($passedUserQuizRequests, ' -1 $passedUserQuizRequests::'));

        $expiredUserQuizRequests = $this->dbRepositoryServiceInterface::getOnlyExpiredUserQuizRequests();
        \Log::info(QuizzesInitFacade::varDump($expiredUserQuizRequests, ' -1 $expiredUserQuizRequests::'));

//        $expiredUserQuizRequests = $this->dbRepositoryServiceInterface::(isPassed: false, onlyExpired: true);
//        \Log::info(varDump($expiredUserQuizRequests, ' -1 $expiredUserQuizRequests::'));

        $appLocales = AppLocale::getInstance()->getAppLocaleSelectionItems(false);

        $this->results[Str::headline(QuizValidationErrorEnum::QVE_NUMBER_OF_LOCALES)]                    = count($appLocales);
        $this->results[Str::headline(QuizValidationErrorEnum::QVE_NUMBER_OF_QUIZZES)]                    = count($quizzes);
        $this->results[Str::headline(QuizValidationErrorEnum::QVE_NUMBER_OF_QUIZ_ANSWERS)]               = $quizAnswersCount;
        $this->results[Str::headline(QuizValidationErrorEnum::QVE_NUMBER_OF_NEW_USER_QUIZ_REQUESTS)]     = count($newUserQuizRequests);
        $this->results[Str::headline(QuizValidationErrorEnum::QVE_NUMBER_OF_PASSED_USER_QUIZ_REQUESTS)]  = count($passedUserQuizRequests);
        $this->results[Str::headline(QuizValidationErrorEnum::QVE_NUMBER_OF_EXPIRED_USER_QUIZ_REQUESTS)] = count($expiredUserQuizRequests);
        $this->results[Str::headline(QuizValidationErrorEnum::QVE_NUMBER_OF_ACCEPTED_FOR_MEETING_USER_MEETINGS)]       = count($expiredUserQuizRequests);
        $this->results[Str::headline(QuizValidationErrorEnum::QVE_NUMBER_OF_MARKED_FOR_FUTURE_CONTACTS_USER_MEETINGS)] = count($expiredUserQuizRequests);

        return $this;
    }

    protected function makeCheckQuizzesWithAllLocales(): void
    {
        $quizzesToCheckAllLocales = $this->dbRepositoryServiceInterface::getQuizzesByByIncludeInactive($this->checkQuizzesWithAllLocalesIncludeInactive);
//        \Log::info(QuizzesInitFacade::varDump($quizzesToCheckAllLocales, ' -1 $quizzesToCheckAllLocales::'));

        $appLocales = AppLocale::getInstance('')->getAppLocaleSelectionItems(false);
        foreach ($quizzesToCheckAllLocales as $quiz) {
            foreach ($appLocales as $locale => $label) {
                if (empty($quiz['question'][$locale])) {
                    $this->quizzesWithEmptyLocalesCount++;
                    $this->quizzesWithEmptyLocalesIds[] = ['id' => $quiz['id'], 'locale' => $locale];
                }
            }
        }
        $this->results[Str::headline(   // Done
            QuizValidationErrorEnum::QVE_QUIZZES_WITH_EMPTY_LOCALES_COUNT)]
            = $this->quizzesWithEmptyLocalesCount;
        $this->results[Str::headline(   // Done
            QuizValidationErrorEnum::QVE_QUIZZES_WITH_EMPTY_LOCALES_IDS)]
            = $this->quizzesWithEmptyLocalesIds;
//        echo '<pre>' . count($this->results) . '::$this->results::' . print_r($this->results, true) . '</pre>';
    }

    protected function makeCheckQuizAnswersWithAllLocales(): void
    {
        $quizzes = $this->dbRepositoryServiceInterface::getQuizzesByByIncludeInactive($this->checkQuizzesWithAllLocalesIncludeInactive);
        $appLocales = AppLocale::getInstance('')->getAppLocaleSelectionItems(false);
        foreach ($quizzes as $quiz) {
            $quizAnswersToCheckAllLocales = $this->dbRepositoryServiceInterface::getQuizAnswersByQuizId($quiz['id']);
            foreach ($quizAnswersToCheckAllLocales as $quizAnswer) {
                foreach ($appLocales as $locale => $label) {
                    if (empty($quizAnswer['text'][$locale])) {
//                        \Log::info(QuizzesInitFacade::varDump($quizAnswer['id'], ' -1 INSIDE $quiz->id::'));
//                        \Log::info(QuizzesInitFacade::varDump($locale, ' -1 INSIDE $locale::'));
                        $this->quizAnswersWithEmptyLocalesCount++;
                        $this->quizAnswersWithEmptyLocalesIds[] = ['id' => $quizAnswer['id'], 'locale' => $locale];
                    }
                }
            }
        }
        $this->results[Str::headline(QuizValidationErrorEnum::QVE_QUIZ_ANSWERS_WITH_EMPTY_LOCALES_COUNT)] = $this->quizAnswersWithEmptyLocalesCount; // Done
        $this->results[Str::headline(QuizValidationErrorEnum::QVE_QUIZ_ANSWERS_WITH_EMPTY_LOCALES_IDS)]   = $this->quizAnswersWithEmptyLocalesIds; // Done
    }

    protected function makeCheckEmptyQuizCategories(): void
    {
        $emptyQuizCategories = $this->dbRepositoryServiceInterface::getEmptyQuizCategories();
        foreach ($emptyQuizCategories as $emptyQuizCategory) {
            $this->emptyQuizCategoriesCount++;
            $this->emptyQuizCategoriesIds[] = ['id' => $emptyQuizCategory['id']];

        }
        $this->results[Str::headline(QuizValidationErrorEnum::QVE_EMPTY_QUIZ_CATEGORIES)]     = $this->emptyQuizCategoriesCount; // Done
        $this->results[Str::headline(QuizValidationErrorEnum::QVE_EMPTY_QUIZ_CATEGORIES_IDS)] = $this->emptyQuizCategoriesIds; // Done
    }

    protected function makeCheckTheOnlyIsCorrectQuizAnswer(): void
    {
        $quizzesWithTooManyIsCorrect = $this->dbRepositoryServiceInterface::getQuizzesWithTooManyIsCorrect($this->checkTheOnlyIsCorrectQuizAnswerIncludeInactive);
        $ids = [];
        foreach ($quizzesWithTooManyIsCorrect as $quizWithTooManyIsCorrect) {
            $ids[] = $quizWithTooManyIsCorrect['id'];
        }
        $this->results[] = [
            Str::headline(
                QuizValidationErrorEnum::QVE_HAS_MORE_ONE_IS_CORRECT_QUIZ_ANSWERS) => count($quizzesWithTooManyIsCorrect),
            Str::headline(
                QuizValidationErrorEnum::QVE_MORE_ONE_IS_CORRECT_QUIZ_ANSWERS_IDS) => $ids,
        ];

        $quizzesWithoutIsCorrect = $this->dbRepositoryServiceInterface::getQuizzesWithoutIsCorrect($this->checkTheOnlyIsCorrectQuizAnswerIncludeInactive);
        $ids = [];
        foreach ($quizzesWithoutIsCorrect as $quizWithoutIsCorrect) {
            $ids[] = $quizWithoutIsCorrect['id'];
        }

        $this->results[Str::headline(QuizValidationErrorEnum::QVE_HAS_NO_IS_CORRECT_QUIZ_ANSWERS)]     = count($quizzesWithoutIsCorrect);
        $this->results[Str::headline(QuizValidationErrorEnum::QVE_HAS_NO_IS_CORRECT_QUIZ_ANSWERS_IDS)] = $ids;
    }

    /*
* @return array results of validation
* */
    public function getResults(): array
    {
//        \Log::info(QuizzesInitFacade::varDump($this->results, ' -1  $this->results::'));

        return $this->results;
    }


}
