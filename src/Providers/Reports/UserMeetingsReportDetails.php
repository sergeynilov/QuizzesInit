<?php

namespace sergeynilov\QuizzesInit\Providers\Reports;

use App\Library\Facades\DateConv;
use sergeynilov\QuizzesInit\Library\Facades\QuizzesInitFacade;
use sergeynilov\QuizzesInit\Library\Services\Interfaces\DbRepositoryInterface;
use sergeynilov\QuizzesInit\Library\AppLocale;

use App;

/*
http://local-quizzes.com/test-coming-user-meetings-report
 */
class UserMeetingsReportDetails
{

    /** @var string current locate */
    protected $currentLocale = 'en'; /* NSN TODO */

    protected DbRepositoryInterface $dbRepositoryServiceInterface;
    protected static AppLocale $appLocale;

    /** @var int $userMeetingId - User Meeting Id which must be shown in the report */
    protected int $userMeetingId;

    /** @var int $userMeetingDetails - User Meeting with all related data, which must be shown in the report */
    protected array $userMeetingDetails = [];


    public function __construct()
    {
        $this->dbRepositoryServiceInterface = App::make(DbRepositoryInterface::class);
    }

    /*          $userMeetingsReportDetails = new UserMeetingsReportDetails();
        $userMeetingsReportDetails->setUserMeetingId($id);

        $userMeetingsReportDetails->retrieveUserMeetingDetails();
        $userMeetingsDetails = $userMeetingsReportDetails->getData();
        \Log::info(varDump($userMeetingsDetails, ' -1 $userMeetingsDetails::'));
 */
    public function retrieveUserMeetingDetails(): self
    {
        \Log::info(QuizzesInitFacade::varDump(-1, ' -1 retrieveUserMeetingDetails()::'));

        $this->userMeetingDetails       = $this->dbRepositoryServiceInterface::getUserMeetingById(id: $this->userMeetingId, details: true);
        return $this;
    }

    public function getUserMeetingData(): array
    {
        \Log::info(QuizzesInitFacade::varDump(-1, ' -1 getUserMeetingData()::'));

        return $this->userMeetingDetails;
    }

    public function setUserMeetingId(int $value): self
    {
        $this->userMeetingId = $value;

        return $this;
    }
}
