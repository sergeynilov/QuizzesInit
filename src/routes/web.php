<?php
//use packages/sergeynilov/QuizzesInit/src/Http/Controllers/Reports/ReportsController.php
use sergeynilov\QuizzesInit\Http\Controllers\Reports\ReportsController;

Route::get('/quizzes_count', function () {
    return 'quizzes_count';
}); // ->middleware(['auth', 'verified'])->name('dashboard');


Route::get('quizzes/reports', [ReportsController::class, 'index'])->name('quizzesReports');
Route::get('quizzes/reports/show-quiz-category/{id}', [ReportsController::class, 'showQuizCategory'])->name('quizzesReportsShowQuizCategory');

Route::get('quizzes/reports/coming-user-meetings-report/show/{id}', [ReportsController::class, 'comingUserMeetingsReportShow'])->name('comingUserMeetingsReportShow');

Route::post('quizzes/reports/coming-user-meetings-report/generate/{id}', [ReportsController::class, 'comingUserMeetingsReportGenerate'])->name('comingUserMeetingsReportGenerate');

Route::get('/quizzes', function () {
    return view('QuizzesInit::index');
});
