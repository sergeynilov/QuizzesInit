@extends('layouts.app')

@section('content')
    <h3 class="mb-2 text-2xl font-bold tracking-tight text-gray-900 dark:text-white flex items-center justify-between">Available reports</h3>
    <h4 class="mb-2 text-xl font-bold tracking-tight text-gray-900 dark:text-white flex items-center justify-between">Quizzes data statistics</h4>

    <table class="table-auto text-sm text-left text-gray-500 dark:text-gray-400">
        <tbody >
        @foreach($quizzesDataStatistics as $key =>$value)
            <tr  class="bg-white dark:bg-gray-800">
                <td class="px-3 py-2">
                    {{ $key }}
                </td>
                <td class="px-3 py-2 border-b">
                    @if(!is_array($value))
                        <strong>{{ $value }}</strong>
                    @else

                        @if($key === 'Quizzes With Empty Locales Ids')
                            @foreach($value as $subValue)
                                @if(!empty($subValue['id']) and !empty($subValue['locale']))
                                    quiz with id = {{ $subValue['id'] }} and '{{ $subValue ['locale'] }}' locale,
                                @endif
                            @endforeach
                        @endif

                        @if($key === 'Empty Quiz Categories Ids')
                            @foreach($value as $subValue)
                                @if(!empty($subValue['id']))
                                    category with id = {{ $subValue['id'] }},
                                @endif
                            @endforeach
                        @endif


                        @if($key === 'Has No Is Correct Quiz Answers Ids')
                            @foreach($value as $subValue)
                                quiz with invalid number of Is Correct in Quiz Answers = {{ $subValue }},
                            @endforeach
                        @endif

                    @endif
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>




    <h4 class="mb-2 text-xl font-bold tracking-tight text-gray-900 dark:text-white flex items-center justify-between">Quiz categories</h4>

{{--    packages/sergeynilov/QuizzesInit/src/views/available-reports.blade.php--}}
{{--    {{ print_r($quizCategoriesAvailableReports, true) }}--}}

    <table class="table-auto w-full text-sm text-left text-gray-500 dark:text-gray-400">
        <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
        <tr class="mb-3 font-normal text-gray-700 dark:text-gray-400">
            <th class="px-3 py-2">Name</th>
            <th class="px-3 py-2">Active</th>
            <th class="px-3 py-2">Quizzes count</th>
            <th class="px-3 py-2"></th>
        </tr>
        </thead>
        <tbody >
        @foreach($quizCategoriesAvailableReports as $quizCategoriesAvailableReport)
            <tr  class="bg-white dark:bg-gray-800">
                <td class="px-3 py-2">
                    {{ $quizCategoriesAvailableReport['id'] }} / {{ $quizCategoriesAvailableReport['locale_name'] }}
                </td>
                <td class="px-3 py-2">
                    {{ \sergeynilov\QuizzesInit\Models\QuizCategory::getActiveLabel($quizCategoriesAvailableReport['active']) }}
                </td>
                <td class="px-3 py-2">
                    {{ $quizCategoriesAvailableReport['quizzes_count'] }}
                </td>
                <td class="px-3 py-2">
                    <a href="{{ route('quizzesReportsShowQuizCategory', $quizCategoriesAvailableReport['id']) }}"
                       class="editor_form_btn_save mr-4">
                        View
                    </a>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>




{{--    {{ print_r($reportUserMeetings, true) }}--}}

    @if(count($reportUserMeetings) === 0)
        <h4 class="mb-2 text-xl font-bold tracking-tight text-gray-900 dark:text-white flex items-center justify-between">No user meetings yet</h4>
    @endif

    @if(count($reportUserMeetings) > 0)
        <h4 class="mb-2 text-xl font-bold tracking-tight text-gray-900 dark:text-white flex items-center justify-between">User meetings waiting for review (into word file)</h4>
    <table class="table-auto w-full text-sm text-left text-gray-500 dark:text-gray-400">
        <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
        <tr class="mb-3 font-normal text-gray-700 dark:text-gray-400">
            <th class="px-3 py-2">User</th>
            <th class="px-3 py-2">Category</th>
            <th class="px-3 py-2">Summary points percent</th>
            <th class="px-3 py-2">Points</th>
            <th class="px-3 py-2">Status</th>
            <th class="px-3 py-2">Appointed at</th>
            <th class="px-3 py-2">Created at</th>
            <th class="px-3 py-2"></th>
        </tr>
        </thead>
        <tbody >
        @foreach($reportUserMeetings as $reportUserMeeting)
            <tr  class="bg-white dark:bg-gray-800">
                <td class="px-3 py-2">
                    {{ $reportUserMeeting['id'] }} / {{ $reportUserMeeting['user_name'] }}
                    / {{ $reportUserMeeting['user_email'] }} / Status : {{ $reportUserMeeting['status'] }}
                </td>
                <td class="px-3 py-2">
                    {{ $reportUserMeeting['user_quiz_request']['user_quizzes_history']['quiz_category_name'] }}
                </td>
                <td class="px-3 py-2">
                    <strong>{{ $reportUserMeeting['summary_points_percent'] }}%</strong>
                </td>
                <td class="px-3 py-2">
                    <strong>{{ $reportUserMeeting['user_quiz_request']['user_quizzes_history']['summary_points'] }}</strong>
                    from {{ $reportUserMeeting['user_quiz_request']['user_quizzes_history']['max_summary_points'] }}
                </td>
                <td class="px-3 py-2">
                    {{ \sergeynilov\QuizzesInit\Models\UserMeeting::getUserMeetingLabel($reportUserMeeting['status']) }}
                </td>
                <td class="px-3 py-2">
                    {{ \DateConv::getFormattedDateTime($reportUserMeeting['appointed_at']) }}
                </td>
                <td class="px-3 py-2">
                    {{ \DateConv::getFormattedDateTime($reportUserMeeting['created_at']) }}
                </td>
                <td class="px-3 py-2">
                    <a href="{{ route('comingUserMeetingsReportShow', $reportUserMeeting['id']) }}"
                       class="editor_form_btn_save mr-4">
                        Details
                    </a>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
    @endif

    @if(!empty($companyName))
        <h5 class="ml-2 mt-4 mb-4 text-md text-gray-900 dark:text-white flex">{{ $companyName }}</h5>
    @endif



@endsection
