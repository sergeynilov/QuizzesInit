<?php


namespace sergeynilov\QuizzesInit\Library;


use sergeynilov\QuizzesInit\Models\{UserMeeting, UserQuizRequest, UserQuizzesHistory, UserQuizzesHistoryDetail};
use Carbon\Carbon;

class ComplicatedRequestsTester
{


    /*
http://local-quizzes.com/test-complicated-requests-tester-methods
    */

    /* get percentage of valid answers from user_quizzes_history_details by $quizCategoryId */
    public function checkIsMethodFormMdels()
    {
//        $post->author()->is($user);
        // incorrect_answers_count
        $checkUserQuizzesHistory = UserQuizzesHistory::find(5);
        var_dump($checkUserQuizzesHistory->toArray());
        $userQuizzesHistories = UserQuizzesHistory::get();
        foreach( $userQuizzesHistories as $userQuizzesHistory ) {
            if($userQuizzesHistory->is($checkUserQuizzesHistory) ) {
                dd('found::' . $userQuizzesHistory->id);
            }
        }
    }
    /* get percentage of valid answers from user_quizzes_history_details by $quizCategoryId */
    public function getPercentageOfValidAnswerByQuizCategoryId($quizCategoryId)
    {

        $userQuizzesHistoryTbName = (new UserQuizzesHistory)->getTable();
        $userQuizzesHistoryDetailTbName = (new UserQuizzesHistoryDetail)->getTable();
        $ratedUserQuizzesHistories = UserQuizzesHistory
            ::getByQuizCategoryId($quizCategoryId)
            ->with('userQuizzesHistoryDetails')
            ->orderBy('incorrect_answers_count', 'asc')
            ->addSelect(['correct_answers_count' => UserQuizzesHistoryDetail
                ::selectRaw('count(*)')
                ->whereColumn($userQuizzesHistoryDetailTbName.'.user_quizzes_history_id', $userQuizzesHistoryTbName.'.id')
                ->whereRaw($userQuizzesHistoryDetailTbName . ".is_correct= 1 ")
            ])
            ->addSelect(['incorrect_answers_count' => UserQuizzesHistoryDetail
                ::selectRaw('count(*)')
                ->whereColumn($userQuizzesHistoryDetailTbName.'.user_quizzes_history_id', $userQuizzesHistoryTbName.'.id')
                ->whereRaw($userQuizzesHistoryDetailTbName . ".is_correct= 0 ")
            ])
            ->havingRaw('correct_answers_count > 0')
            ->get();
        var_dump($ratedUserQuizzesHistories->toArray());
        dd( $ratedUserQuizzesHistories->toArray() );
    }




    /* get all valid Quiz answers from by userQuizRequestId */
    public function getValidQuizAnswersFromUserQuizRequestId($userQuizRequestId)
    {
        /*  COMPLETED */
        $userQuizzesHistoryDetailsWithCorrectAnswers = UserQuizRequest::getById($userQuizRequestId)
//        ->with('userQuizzesHistory')
        ->with('userQuizzesHistory.userQuizzesHistoryDetailsWithCorrectAnswers')
        ->get();
        echo '<pre>'.count($userQuizzesHistoryDetailsWithCorrectAnswers).'::$userQuizzesHistoryDetailsWithCorrectAnswers::'.print_r($userQuizzesHistoryDetailsWithCorrectAnswers,true).'</pre>';
    }

    /* get all userQuizzesHistory models with valid Quiz answers from by userQuizRequestId with
    quiz_points >= $quizPoints */
    public function getValidQuizAnswersFromUserQuizRequestIdWithQuizPoints($userQuizRequestId, $quizPoints)
    {
        /*  COMPLETED */
        $userQuizzesHistoryDetailsWithCorrectAnswers = userQuizzesHistory::getByUserQuizRequestId($userQuizRequestId)
            ->whereHas('userQuizzesHistoryDetails', function($query) use($quizPoints){
                $query->where('is_correct', false)->whereRaw('quiz_points >= ' . $quizPoints);
            })
        ->get();
        echo '<pre>'.count($userQuizzesHistoryDetailsWithCorrectAnswers).'::$userQuizzesHistoryDetailsWithCorrectAnswers::'.print_r($userQuizzesHistoryDetailsWithCorrectAnswers,true).'</pre>';
    }
  /*
     public function onlyCancelledUserMeetings(): HasMany
    {
        return $this->hasMany(UserMeeting::class)->where('status',UserMeeting::USER_MEETING_STATUS_CANCELLED );
    }
*/

    public function testSetUserMeeting()
    {
        /*  COMPLETED */
        /*        \Log::info(varDump(-1, ' -1 HomeController.testSetUserMeeting::'));
                $userQuizRequests = UserQuizRequest::getById(1)->with('onlyCancelledUserMeetings')->get();
                echo '<pre>::$userQuizRequests::'.print_r($userQuizRequests,true).'</pre>';
                \Log::info(varDump($userQuizRequests, ' -1 $userQuizRequests::'));
                return;*/

        //    $table->enum('status', ['W', 'A', 'M', 'C', 'D'])->comment('W => Waiting for review,  A => Accepted for meeting, M=>Marked for future contacts, C=>Cancelled, D-Declined');
        $userMeeting = UserMeeting::findOrFail(2);
        $userMeeting->appointed_at = Carbon::now(config('app.timezone'))->addDays(1);
        $userMeeting->status = 'D'; // => Accepted for meeting;

        $userMeeting->updated_at = Carbon::now(config('app.timezone'));
        $userMeeting->save();
//        $userMeeting->
        //
    }


    // USE testSetUserMeeting
}
