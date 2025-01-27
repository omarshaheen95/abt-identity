<?php

use App\Http\Controllers\Manager\ManagerController;
use App\Http\Controllers\Manager\SchoolController;
use App\Http\Controllers\Manager\SchedulingController;
use App\Http\Controllers\Manager\TextTranslationController;
use Illuminate\Support\Facades\Route;
use Spatie\Permission\Models\Permission;

Route::group([], function () {
Route::get('cer',function (){
    return view('general.certificate.certificate');
});
    Route::get('/home', [\App\Http\Controllers\Manager\SettingController::class,'home'])->name('home');

    //Set Local
    Route::get('lang/{local}', [\App\Http\Controllers\Manager\SettingController::class,'lang'])->name('switch-language');

    //Settings Management
    Route::get('settings', [\App\Http\Controllers\Manager\SettingController::class,'settings'])->name('settings.general');
    Route::post('settings', [\App\Http\Controllers\Manager\SettingController::class,'updateSettings'])->name('settings.updateSettings');
    //Statistics Route
    Route::post('statistics/student_login_data',  [\App\Http\Controllers\Manager\SettingController::class,'studentLoginData'])->name('statistics.student_login_data');
    Route::post('statistics/assessments_data',  [\App\Http\Controllers\Manager\SettingController::class,'assessmentsData'])->name('statistics.assessments_data');

    //School
    Route::resource('school', SchoolController::class)->except(['destroy']);
    Route::delete('delete-school', [SchoolController::class, 'deleteSchool'])->name('delete-school');
    Route::get('school_login/{id}', [SchoolController::class, 'schoolLogin'])->name('school-login');
    Route::post('export-schools', [SchoolController::class, 'schoolExport'])->name('export-schools');

    //SchoolGradeScheduling
    Route::get('scheduling/{id}', [SchedulingController::class, 'index'])->name('school.scheduling.index');
    Route::post('scheduling/update/{id}', [SchedulingController::class, 'update'])->name('school.scheduling.update');
    Route::post('general_scheduling', [SchedulingController::class, 'updateSchoolsGrades'])->name('school.general-scheduling');

    //Inspection
    Route::resource('inspection', \App\Http\Controllers\Manager\InspectionController::class)->except(['destroy']);
    Route::delete('delete-inspection', [\App\Http\Controllers\Manager\InspectionController::class, 'deleteInspection'])->name('delete-inspection');
    Route::get('login-inspection/{id}', [\App\Http\Controllers\Manager\InspectionController::class, 'inspectionLogin'])->name('login-inspection');
    Route::post('inspections-export', [\App\Http\Controllers\Manager\InspectionController::class, 'inspectionExport'])->name('export-inspections');

    //Manager
    Route::resource('manager', ManagerController::class)->except(['destroy']);
    Route::post('managers-export', [\App\Http\Controllers\Manager\ManagerController::class, 'export'])->name('manager.export');
    Route::delete('delete-manager', [\App\Http\Controllers\Manager\ManagerController::class, 'deleteManager'])->name('delete-manager');
    Route::get('edit-permissions/{id}', [\App\Http\Controllers\Manager\ManagerController::class, 'editPermissions'])->name('manager.edit-permissions');
    Route::post('update-permissions', [\App\Http\Controllers\Manager\ManagerController::class, 'updatePermissions'])->name('manager.update-permissions');
    Route::get('update-profile', [\App\Http\Controllers\Manager\ManagerController::class, 'viewUpdateProfile'])->name('edit-profile');
    Route::post('update-profile', [\App\Http\Controllers\Manager\ManagerController::class, 'updateProfile'])->name('update-profile');
    Route::get('update-password', [\App\Http\Controllers\Manager\ManagerController::class, 'viewUpdatePassword'])->name('edit-password');
    Route::post('update-password', [\App\Http\Controllers\Manager\ManagerController::class, 'updatePassword'])->name('update-password');

    //text translation
    Route::get('text_translation', [TextTranslationController::class,'index'])->name('text_translation.index');
    Route::post('update_translation/{lang}/{file}', [TextTranslationController::class,'updateTranslations'])->name('text_translation.update');

    //Level
    Route::resource('level', \App\Http\Controllers\Manager\LevelController::class)->except(['destroy']);
    Route::get('levelGrades', [\App\Http\Controllers\Manager\LevelController::class, 'levelGrades'])->name('level.levelGrades');
    Route::post('level-export', [\App\Http\Controllers\Manager\LevelController::class, 'export'])->name('level.export');
    Route::delete('delete-level', [\App\Http\Controllers\Manager\LevelController::class, 'deleteLevel'])->name('level.delete');
    Route::post('addGeneralLevels', [\App\Http\Controllers\Manager\LevelController::class, 'addGeneralLevels'])->name('level.addGeneralLevels');
    Route::post('level/activation', [\App\Http\Controllers\Manager\LevelController::class, 'activation'])->name('level.activation');

    //Year
    Route::resource('year', \App\Http\Controllers\Manager\YearController::class)->except(['destroy']);
    Route::delete('delete-year', [\App\Http\Controllers\Manager\YearController::class, 'deleteYear'])->name('year.delete');



    Route::get('term/{id}/questions_structure', [\App\Http\Controllers\Manager\QuestionController::class, 'showQuestionsStructure'])->name('term.questions-structure');
    Route::post('term/{id}/save_questions_structure', [\App\Http\Controllers\Manager\QuestionController::class, 'saveQuestionsStructure'])->name('term.save-questions-structure');

    Route::get('term/{id}/questions', [\App\Http\Controllers\Manager\QuestionController::class, 'showQuestions'])->name('term.questions');
    Route::post('term/{id}/question/update', [\App\Http\Controllers\Manager\QuestionController::class, 'updateQuestions'])->name('term.update-questions');
    Route::post('term/question/delete', [\App\Http\Controllers\Manager\QuestionController::class, 'deleteQuestion'])->name('term.delete-question');
    Route::post('term/question/delete-question-option', [\App\Http\Controllers\Manager\QuestionController::class, 'deleteOption'])->name('term.delete-question-option');

    Route::post('term/question/delete-file', [\App\Http\Controllers\Manager\QuestionController::class, 'deleteQuestionFile'])->name('term.delete-question-file');
    Route::post('term/question/delete-option-image', [\App\Http\Controllers\Manager\QuestionController::class, 'deleteOptionImageRequest'])->name('term.delete-option-image');

    //Question Standards
    Route::get('update_questions_standards/{year}', [\App\Http\Controllers\Manager\QuestionController::class, 'updateQuestionStandards'])->name('term.update-questions-standards');

    //Question File
    Route::resource('question-file', \App\Http\Controllers\Manager\QuestionFileController::class)->except(['destroy', 'edit', 'update']);
    Route::delete('question-file/delete', [\App\Http\Controllers\Manager\QuestionFileController::class, 'destroy'])->name('question-file.destroy');
    Route::get('download_question_file/{id}', [\App\Http\Controllers\Manager\QuestionFileController::class, 'downloadFile'])->name('question-file.download_file');


    //Term
    Route::resource('term', \App\Http\Controllers\Manager\TermController::class)->except(['destroy']);
    Route::post('term-export', [\App\Http\Controllers\Manager\TermController::class, 'export'])->name('term.export');
    Route::delete('delete-term', [\App\Http\Controllers\Manager\TermController::class, 'deleteTerm'])->name('term.delete');
    Route::get('terms_questions',[\App\Http\Controllers\Manager\TermController::class, 'termsQuestions'] )->name('term.terms-questions');
    Route::post('terms_questions_export', [\App\Http\Controllers\Manager\TermController::class, 'termsQuestionsExport'])->name('term.terms-questions-export');
    Route::get('terms_names/{level}', [\App\Http\Controllers\Manager\TermController::class, 'termsNames'])->name('term.terms-names');
    Route::get('students_not_submitted_terms', [\App\Http\Controllers\Manager\TermController::class, 'studentsNotSubmittedTerms'])->name('term.students-not-submitted-terms');
    Route::post('students_not_submitted_terms_export', [\App\Http\Controllers\Manager\TermController::class, 'studentsNotSubmittedTermsExport'])->name('term.students-not-submitted-terms-export');
    Route::get('term/{id}/preview', [\App\Http\Controllers\Manager\TermController::class, 'preview'])->name('term.preview');
    Route::post('addGeneralTerms', [\App\Http\Controllers\Manager\TermController::class, 'addGeneralTerms'])->name('term.addGeneralTerms');
    Route::post('term/activation', [\App\Http\Controllers\Manager\TermController::class, 'activation'])->name('term.activation');

    //Term Standards
    Route::get('standards', [\App\Http\Controllers\Manager\TermController::class, 'standards'])->name('term.standards');
    Route::post('standards_export', [\App\Http\Controllers\Manager\TermController::class, 'standardExport'])->name('term.standards-export');
    Route::get('edit_standards/{id}', [\App\Http\Controllers\Manager\TermController::class, 'editStandards'])->name('term.edit-standards');
    Route::post('update_standards/{id}', [\App\Http\Controllers\Manager\TermController::class, 'updateTermStandards'])->name('term.update-standards');


    //Student
    Route::resource('student', \App\Http\Controllers\Manager\Student\StudentController::class)->except(['destroy']);
    Route::delete('delete-student', [\App\Http\Controllers\Manager\Student\StudentController::class, 'delete'])->name('student.delete');
    Route::post('restore-student/{id}', [\App\Http\Controllers\Manager\Student\StudentController::class, 'restoreStudent'])->name('student.student-restore');
    Route::get('student-login/{id}', [\App\Http\Controllers\Manager\Student\StudentController::class, 'studentLogin'])->name('student.student-login');
    Route::get('student-report/{id}', [\App\Http\Controllers\Manager\Student\StudentController::class, 'studentReport'])->name('student.student-report');
    Route::post('student-export', [\App\Http\Controllers\Manager\Student\StudentController::class, 'studentExport'])->name('student.student-export');
    Route::post('student-marks-export', [\App\Http\Controllers\Manager\Student\StudentController::class, 'studentMarksExport'])->name('student.student-marks-export');
    Route::get('students-cards-export', [\App\Http\Controllers\Manager\Student\StudentController::class, 'studentsCards'])->name('student.student-cards-export');
    Route::get('student/{id}/card', [\App\Http\Controllers\Manager\Student\StudentController::class, 'studentCard'])->name('student-card');
    Route::post('students-cards-by-section', [\App\Http\Controllers\Manager\Student\StudentController::class, 'studentCardBySections'])->name('student.students-cards-by-section');
    Route::post('/pdfReports', [\App\Http\Controllers\Manager\Student\StudentController::class,'pdfReports'])->name('reports.pdfReports');


    //Copy Terms
    Route::get('copy_term', [\App\Http\Controllers\Manager\TermController::class, 'copyTermsView'])->name('term.copy_term_view');
    Route::post('copy_term', [\App\Http\Controllers\Manager\TermController::class, 'copyTerms'])->name('term.copy_term');

    //get levels by year
    Route::get('get-sections', [\App\Http\Controllers\Manager\Student\StudentController::class, 'getSectionsByYear'])->name('student.get-sections');

    Route::resource('students_files_import',\App\Http\Controllers\Manager\Student\StudentImportController::class)->except(['destroy']);
    Route::delete('delete-student-import', [\App\Http\Controllers\Manager\Student\StudentImportController::class, 'delete'])->name('students_files_import.delete');
    Route::get('student-import-error/{id}', [\App\Http\Controllers\Manager\Student\StudentImportController::class, 'showError'])->name('students_files_import.error');
    Route::get('students_files_import/{id}/export_cards', [\App\Http\Controllers\Manager\Student\StudentImportController::class, 'exportCards'])->name('students_files_import.export_cards');
    Route::get('students_files_import/{id}/export_excel', [\App\Http\Controllers\Manager\Student\StudentImportController::class, 'exportExcel'])->name('students_files_import.export_excel');
    //Logs
    Route::get('students_files_import/{id}/show_logs', [\App\Http\Controllers\Manager\Student\StudentImportController::class, 'showFromErrors'])->name('students_files_import.show_logs');
    Route::delete('students_files_import/delete_student_file_logs', [\App\Http\Controllers\Manager\Student\StudentImportController::class, 'deleteLogs'])->name('students_files_import.delete_logs');
    Route::post('students_files_import/save_student_data_logs', [\App\Http\Controllers\Manager\Student\StudentImportController::class, 'saveLogs'])->name('students_files_import.save_logs');

    //Marking Requests
    Route::resource('marking_requests', \App\Http\Controllers\Manager\MarkingRequestController::class)->except(['destroy']);
    Route::delete('marking_requests', [\App\Http\Controllers\Manager\MarkingRequestController::class, 'destroy'])->name('marking_requests.destroy');

    //StudentTerm
    Route::resource('student_term', \App\Http\Controllers\Manager\StudentTermController::class)->except(['destroy','index','show']);
    Route::get('student_term/{status}',[\App\Http\Controllers\Manager\StudentTermController::class,'index'])->name('student_term.index');
    Route::post('update-student-term/{id}', [\App\Http\Controllers\Manager\StudentTermController::class, 'updateTerm'])->name('student.update-student-term');
    Route::post('student-terms-export', [\App\Http\Controllers\Manager\StudentTermController::class, 'studentsTermsExport'])->name('student-term.export');
    Route::delete('delete-student-term', [\App\Http\Controllers\Manager\StudentTermController::class, 'deleteStudentTerm'])->name('student.delete-student-term');
    Route::post('restore-student-term/{id}', [\App\Http\Controllers\Manager\StudentTermController::class, 'restore'])->name('student-term-restore');
    Route::post('auto_correct_student_term', [\App\Http\Controllers\Manager\StudentTermController::class, 'autoCorrect'])->name('auto-correct-student-term');
    Route::get('student_term/{id}/certificate', [\App\Http\Controllers\GeneralController::class,'certificate'])->name('student-term.certificate');

    //Activity Log Controller
    Route::get('activity-log', [\App\Http\Controllers\Manager\ActivityLogController::class, 'index'])->name('activity-log.index');
    Route::get('activity-log/{id}', [\App\Http\Controllers\Manager\ActivityLogController::class, 'show'])->name('activity-log.show');
    Route::delete('activity-log', [\App\Http\Controllers\Manager\ActivityLogController::class, 'destroy'])->name('activity-log.delete');

    //Login Sessions
    Route::resource('login_sessions', \App\Http\Controllers\Manager\LoginSessionController::class);

    Route::get('seed',function (){
       Artisan::call('db:seed --class PermissionsTableSeeder');
        //Artisan::call('db:seed --class SettingsTableSeeder');

        $all_manager_permission = Permission::query()
            ->where('guard_name','manager')->get()->pluck('name')->toArray();
        Auth::guard('manager')->user()->syncPermissions($all_manager_permission);
        return redirect()->route('manager.home');

    });

    Route::get('get-levels-by-year/{id}', [\App\Http\Controllers\GeneralController::class, 'levelsByYear'])->name('get-levels-by-year');
    Route::get('get-terms-by-level/{id}', [\App\Http\Controllers\GeneralController::class, 'termsByLevel'])->name('get-terms-by-level');
    Route::get('copy_structure', function (){
        $terms = \App\Models\Term::query()->with(['question', 'level'])
            ->whereRelation('level', 'year_id', 2)->get();
        $arabs = $terms->filter(function ($value){
            return $value->level->arab == 1;
        });
        $non_arabs = $terms->filter(function ($value){
            return $value->level->arab == 0;
        });
        foreach ($arabs as $term){
            $non_arab_term = $non_arabs->filter(function ($value) use ($term){
                return $value->level->grade == $term->level->grade;
            })->first();
            if ($non_arab_term)
            {
                $non_arab_term->question()->delete();
                //replicate questions for non arab term
                foreach ($term->question as $question){
                    $new_question = $question->replicate();
                    $new_question->term_id = $non_arab_term->id;
                    $new_question->content = null;
                    $new_question->image = null;
                    $new_question->audio = null;
                    $new_question->question_reader = null;
                    $new_question->question_file_id = null;
                    $new_question->save();
                }
            }
        }
        return 'done';
    })->name('copy_structure');
});

