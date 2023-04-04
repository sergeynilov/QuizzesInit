<?php

namespace sergeynilov\QuizzesInit\Providers\Reports;

use sergeynilov\QuizzesInit\Enums\WordTextLineEnum;
use App\Library\Facades\DateConv;
use sergeynilov\QuizzesInit\Library\Facades\QuizzesInitFacade;
use sergeynilov\QuizzesInit\Models\UserQuizzesHistoryDetail;
use sergeynilov\QuizzesInit\Library\AppLocale;
use sergeynilov\QuizzesInit\Library\Services\Interfaces\DbRepositoryInterface;
use sergeynilov\QuizzesInit\Providers\Reports\Interfaces\PhpWordReportInterface;
use Str;

//packages/sergeynilov/QuizzesInit/src/Providers/Reports/Interfaces/PhpWordReportInterface.php

class GenerateUserMeetingsReportDetailsWord extends PhpWordReport implements PhpWordReportInterface
{

    /** @var int $userMeetingsDetails - User Meeting Details with all related data, which must be shown in the report */
    protected array $userMeetingsDetails;

    public function setUserMeetingsDetails(array $value): self
    {
        $this->userMeetingsDetails = $value;

        return $this;
    }

    public function generate(): self
    {
        $this->setReportFontName('Courier New');
        $this->setReportFontSize(12);
        $this->setOutputFileFormat('Word2007'); // Possible values 'ODText' / 'RTF' / 'Word2007' / 'HTML' / 'PDF'

        // Creating the new document with defined properties
        $this->initPhpWord();

        // Add lines of content with different sizes
        $this->addTextLine('Results of quiz', WordTextLineEnum::WTL_HEADER_TEXT);

        $this->addTextLine('By ' . $this->userMeetingsDetails['user_name'] . ' / ' . $this->userMeetingsDetails['user_email'],
            WordTextLineEnum::WTL_CONTENT_TEXT, addTextBreak: true);

//        $this->addDoubledFormattedTextLine(leftText: 'By ' . $this->userMeetingsDetails['user_name'], rightText:
//            $this->userMeetingsDetails['user_email'],
//            wordTextLineEnum: WordTextLineEnum::WTL_SUBHEADER_TEXT, addTextBreak: true);

        $this->addTextLine('On' . ' ' . DateConv::getFormattedDateTime($this->userMeetingsDetails['created_at']) . '/' . $this->userMeetingsDetails['user_email'],
            WordTextLineEnum::WTL_SUBHEADER_TEXT);

        $selectedLocale = $this->userMeetingsDetails['user_quiz_request']['user_quizzes_history']['selected_locale'];
        $this->addTextLine('Selected Language: ' . $selectedLocale, WordTextLineEnum::WTL_CONTENT_TEXT);

        $this->addTextLine('Selected Language: ' . AppLocale::getInstance($selectedLocale)->getAppLocaleLabel($selectedLocale),
            WordTextLineEnum::WTL_CONTENT_TEXT);

        $localeImageUrl = AppLocale::getInstance($selectedLocale)->getLocaleImageUrlByLocale($selectedLocale);
//        \Log::info(QuizzesInitFacade::varDump(url($localeImageUrl), ' -1 url($localeImageUrl)::'));
        $this->addImageIcon(url($localeImageUrl),
            'Selected Language: ' . AppLocale::getInstance($selectedLocale)->getAppLocaleLabel($selectedLocale),
            WordTextLineEnum::WTL_SUBHEADER_TEXT, addTextBreak: true);

//        $this->addHorizontalLine(addTextBreak: true);

        $this->addTextLine('Quiz Category: ' . $this->userMeetingsDetails['user_quiz_request']['user_quizzes_history']['quiz_category_name'],
            WordTextLineEnum::WTL_SUBHEADER_TEXT);

        $this->addTextLine('Status: ' . \sergeynilov\QuizzesInit\Models\UserMeeting::getUserMeetingLabel($this->userMeetingsDetails['status']),
            WordTextLineEnum::WTL_CONTENT_TEXT);

        $this->addTextLine('Is reviewed: ' . \sergeynilov\QuizzesInit\Models\UserQuizzesHistory::getIsReviewedLabel($this->userMeetingsDetails['user_quiz_request']['user_quizzes_history']['is_reviewed']),
            WordTextLineEnum::WTL_CONTENT_TEXT);

//        $this->addDoubleFormattedTextLine('Is reviewed: ' . \sergeynilov\QuizzesInit\Models\UserQuizzesHistory::getIsReviewedLabel($this->userMeetingsDetails['user_quiz_request']['user_quizzes_history']['is_reviewed']),
//            WordTextLineEnum::WTL_CONTENT_TEXT);

        $this->addTextLine('Time spent: ' . QuizzesInitFacade::timeSpentLabel(0,
                $this->userMeetingsDetails['user_quiz_request']['user_quizzes_history']['time_spent'], 'before'),
            WordTextLineEnum::WTL_CONTENT_TEXT, addTextBreak: true);

        if ($this->userMeetingsDetails['user_quiz_request']['user_quizzes_history']['summary_points'] === 0) {
            $summaryPointsPercent = 0;
        } else {
            $summaryPointsPercent = round($this->userMeetingsDetails['user_quiz_request']['user_quizzes_history']['summary_points'] / $this->userMeetingsDetails['user_quiz_request']['user_quizzes_history']['max_summary_points'] * 100,
                2);
        }
        $this->addTextLine('Summary of points: ' . $this->userMeetingsDetails['user_quiz_request']['user_quizzes_history']['summary_points'] . ' ( ' .
                           $this->userMeetingsDetails['user_quiz_request']['user_quizzes_history']['max_summary_points'] . ' ' .
                           'maximum possible' . ' ) with <strong>' . $summaryPointsPercent . ' percent</strong>',
            WordTextLineEnum::WTL_CONTENT_TEXT, addTextBreak: true);

        $this->addHorizontalLine(addTextBreak: true);
//        $this->addListing(['Item # 1', 'Item # 2', 'Item # 3']);

        // Add tables of user quizzes history details rows and defined columns
        if ( ! empty($this->userMeetingsDetails['user_quiz_request']['user_quizzes_history']['user_quizzes_history_details'])) {
            $tableColumns = [
                ['column_id' => 'id', 'title' => 'id(will be removed)', 'width' => 2000, 'align' => 'right'],
                ['column_id' => 'text', 'title' => 'Text', 'width' => 6500],
                ['column_id' => 'is_correct', 'title' => 'is correct', 'width' => 1500],
                ['column_id' => 'quiz_points', 'title' => 'quiz points', 'width' => 1500, 'align' => 'right'],
            ];
            //    protected function addTableWithData(array $dataArray, array $columnsArray, string $titleWordTextLineEnum): void
            $this->addTableWithData(dataArray:
                $this->userMeetingsDetails['user_quiz_request']['user_quizzes_history']['user_quizzes_history_details'],
                columnsArray: $tableColumns,
                titleWordTextLineEnum: WordTextLineEnum::WTL_HEADER_TEXT);
        }

        return $this;
    }

    public function download(): \Symfony\Component\HttpFoundation\BinaryFileResponse
    {
        $objWriter = \PhpOffice\PhpWord\IOFactory::createWriter($this->phpWord, $this->outputFileFormat);
        $filename = Str::headline('Results of quiz for ' . $this->userMeetingsDetails['user_name']) . '.docx';
        $objWriter->save(storage_path($filename));

        return response()->download(storage_path($filename));
    }

}
