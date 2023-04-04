<?php

namespace sergeynilov\QuizzesInit\Library\Reports;

use App\Library\Facades\DateConv;
use Carbon\Carbon;
use App;
use PDF;
use Str;

// http://local-quizzes.com/quizzes/reports

class GenerateQuizzesReport
{
    /** @var string current locate */
    protected $currentLocale = 'en';


    /** @var int int $quizCategoryId - Quiz Category Id, which must be shown in the report */
    protected int $quizCategoryId;
    /** @var array $quizCategory - Quiz Category which must be shown in the report */
    protected array $quizCategory;
    protected array $quizzes;
    protected string $tableStyle = ' border:2px double black; width: 100%';
    protected string $tableTdStyle = ' border:1px dotted green; padding : 20px';
    protected bool $showIsCorrect = false;
    protected bool $showCreatedAt = false;

    /** @var array - quiz answers related to quizzes */
//    protected $quizAnswers = [];


    public function __construct()
    {
    }

    public function setQuizCategoryId(int $value)
    {
        $this->quizCategoryId = $value;
    }

    public function setTableStyle(string $value)
    {
        $this->tableStyle = $value;
    }

    public function setTableTdStyle(string $value)
    {
        $this->tableTdStyle = $value;
    }

    public function setShowIsCorrect(bool $value)
    {
        $this->showIsCorrect = $value;
    }

    public function setShowCreatedAt(bool $value)
    {
        $this->showCreatedAt = $value;
    }

    public function setQuizCategory(array $value)
    {
        $this->quizCategory = $value;
    }
    public function setQuizzes(array $value)
    {
        $this->quizzes = $value;
    }

    public function generate()
    {
        $data = [
            'title'          => 'Quizzes of "' . $this->quizCategory['locale_name'] .'" quiz category',
            'quizzes'        => $this->quizzes,
            'tableStyle'     => $this->tableStyle,
            'tableTdStyle'   => $this->tableTdStyle,
            'showIsCorrect'  => $this->showIsCorrect,
            'showCreatedAt'  => $this->showCreatedAt,
            'todayFormatted' => DateConv::getFormattedDate(Carbon::now(config('app.timezone')))
        ];

        $pdf = PDF::loadView('report/quizzes-report', $data);
        $filename = 'report/quizzes-report-' . Str::slug($this->quizCategory['locale_name']) . '.pdf';
        return $pdf->download($filename);
    }


//GenerateQuizzes
}
