<?php

namespace App\Http\Controllers\Manager;

use App\Exceptions\GeneralException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Manager\SaveQuestionsStructureRequest;
use App\Models\FillBlankQuestion;
use App\Models\MatchQuestion;
use App\Models\OptionQuestion;
use App\Models\Question;
use App\Models\QuestionStandard;
use App\Models\SortQuestion;
use App\Models\Subject;
use App\Models\Term;
use App\Models\TFQuestion;
use App\Services\QuestionStandardService;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class QuestionController extends Controller
{

    public function __construct()
    {

        $this->middleware('permission:show questions structure')->only('showQuestionsStructure');
        $this->middleware('permission:add edit questions structure')->only('saveQuestionsStructure');
        $this->middleware('permission:show questions content')->only('showQuestions');
        $this->middleware('permission:edit questions content')
            ->only(['updateQuestions','deleteOption','deleteMatchOptionImage','deleteQuestionFile']);
    }

    /**
     * Show term questions structure
     * @param $id
     * @return Application|Factory|View
     */
    public function showQuestionsStructure($id){
        $term = Term::with('question')->where('id',$id)->first();
        $questions = $term->getRelation('question');
        $title = t('Assessment Questions Structure');
        $types = Question::getQuestionTypes();
        $subjects = Subject::all();
        return view('manager.term.questions_structure',
            compact('title','term','questions','types','subjects'));

    }


    /**
     * create and update and delete term questions structure
     * @param SaveQuestionsStructureRequest $request
     * @param $id
     * @return RedirectResponse
     */
    public function saveQuestionsStructure(SaveQuestionsStructureRequest $request,$id){
        $data = $request->validated();
        $marks = 0;

        $db_questions = Question::with(['tf_question','sort_question','match_question','fill_blank_question','option_question','question_standard'])
            ->where('term_id',$id)->get();
        $term_questions_ids = $db_questions->pluck('id');
        $request_questions_ids = collect($request['questions'])->pluck('id')->toArray();



        foreach ( $data['questions'] as $question){
            if (!isset($question['type'])){
                throw ValidationException::withMessages([t('The question type is required')]);
            }
            if (!$question['type']){
                throw ValidationException::withMessages([t('The question type is required')]);
            }
            if (!isset($question['mark'])){
                throw ValidationException::withMessages([t('The question mark is required')]);
            }
            if (!$question['mark']){
                throw ValidationException::withMessages([t('The question mark is required')]);
            }
            if (!$question['subject_id']){
                throw ValidationException::withMessages([t('The question subject is required')]);
            }

            $marks+= $question['mark'];
        }

        //check marks sum for subjects
        $subjects = Subject::all();
        foreach ($subjects as $subject){
            $subject_marks_sum = 0;
            //get marks for every subject
            foreach ( $data['questions'] as $question){
                if ($question['subject_id'] == $subject->id){
                    $subject_marks_sum +=$question['mark'];
                }
            }
            //check marks sum
            if ($subject_marks_sum<$subject->mark ||$subject_marks_sum>$subject->mark){
                throw ValidationException::withMessages(['The '. $subject->name.'subject marks sum must equal 25']);
            }

        }


        //check if marks sum equal 100
        if ($marks == 100) {

            //compare ids between request questions ids and db questions ids and delete question from db if not found
            // in request ids
            foreach ($term_questions_ids as $q_id) {
                if (!in_array($q_id, $request_questions_ids)) {
                    Question::destroy($q_id);
                }
            }

            //create or update question
            $update = 0;
            foreach ($data['questions'] as $question) {
                if (isset($question['id'])) {//Just can update mark
                    $updated_data = [
                        'mark' => $question['mark'],
                    ];
                    if (!$this->questionHasData($db_questions->where('id', $question['id'])->first())) {
                        $updated_data['type'] = $question['type'];
                        $updated_data['subject_id'] = $question['subject_id'];
                    }
                    $update += Question::query()
                        ->where('id', $question['id'])
                        ->where('term_id', $id)
                        ->update($updated_data);
                } else {
                    Question::query()
                        ->create([
                            'term_id' => $id,
                            'type' => $question['type'],
                            'subject_id' => $question['subject_id'],
                            'mark' => $question['mark']
                        ]);
                }

            }


        } else {
            return redirect()->back()
                ->with('title', t('Questions Structure'))
                ->withErrors([t('TheQuestionsNotAdded') . $marks]);
        }
        return redirect()->route('manager.term.index')
            ->with('title',t('Add Questions Structure'))
            ->with('message', t('Successfully Updated'));
    }


    private function questionHasData(Question $question)
    {
        switch ($question->type){
            case 'true_false':
                return (bool)$question->tf_question;
            case 'multiple_choice':
                return (bool)$question->option_question->count()>0;
            case 'matching':
                return (bool)$question->match_question->count()>0;
            case 'sorting':
                return (bool)$question->sort_question->count()>0;
            case 'fill_blank':
                return (bool)$question->fill_blank_question->count()>0;
            case 'article':
               return !is_null($question->content);
        }
        return false;
    }


    public function showQuestions($id){
        $title = t('Assessment Questions Content');
        $term = Term::query()->where('id',$id)->first();
        $questions = Question::with(['tf_question','option_question','match_question','sort_question','fill_blank_question','question_standard','subject'])
            ->where('term_id', $id)->get()->groupBy('subject_id');
        $subjects = Subject::all();

        return view('manager.term.questions',
            compact('title','subjects','term','questions'));

    }

    /**
     * Crate or update questions with upload files
     * @param Request $request
     * @param $id
     */
    public function updateQuestions(Request $request,$id){
       $request->validate(['question_data'=>'required|array']);

        DB::transaction(function () use ($request,$id){
            foreach ($request['question_data'] as $question){
                //update main question
                $this->updateQuestion($question,$id);

                //update
                switch ($question['type']){
                    case 'true_false':
                        $this->createOrUpdateTFQuestion($question);
                        break;
                    case 'multiple_choice':
                        $this->createOrUpdateOptionQuestion($question);
                        break;
                    case 'matching':
                        $this->createOrUpdateMatchQuestion($question);
                        break;
                    case 'sorting':
                        $this->createOrUpdateSortQuestion($question);
                        break;
                    case 'fill_blank':
                        $this->createOrUpdateFillBlankQuestion($question);
                        break;
                    case 'article':
                        // Article Question not need to update any relation
                    break;
                }

            }
        });
        return $this->sendResponse(null,t('Successfully Updated'));
    }


    /**
     * update the main question
     * @param $question
     * @param $term_id
     */
    private function updateQuestion($question,$term_id){

        $question_data = [];
//        dd($question);

        if (isset($question['image'])){
            $this->deleteQF($question['id'],'image');
            $image = $this->uploadFile($question['image']);
            $question_data['image'] = $image;
        }
        if (isset($question['audio'])){
            $this->deleteQF($question['id'],'audio');
            $audio = $this->uploadFile($question['audio']);
            $question_data['audio'] = $audio;
        }
        if (isset($question['question_reader'])){
            $this->deleteQF($question['id'],'question_reader');
            $question_reader = $this->uploadFile($question['question_reader']);
            $question_data['question_reader'] = $question_reader;
        }

        //update or create question standard
        if (isset($question['question_standard']) && $question['question_standard']){
            QuestionStandard::query()->updateOrCreate(
                ['question_id' => $question['id']],
                [
                    'question_id' => $question['id'],
                    'standard' => $question['question_standard'],
                    'mark' => $question['mark']
                ]
            );
        }

        //update the main question
        if (isset($question['content']) && $question['content']){
            $question_data['content'] = strip_tags($question['content']);
            Question::query()
                ->where('id', $question['id'])
                ->where('term_id', $term_id)
                ->update($question_data);
        }else{
            throw ValidationException::withMessages(['question_data['.$question["id"].'][content]' => t('The question content is required')]);
        }



    }



    private function createOrUpdateTFQuestion($question)
    {
        return TFQuestion::query()->updateOrCreate(
            ['question_id' => $question['id']],
            [
                'question_id' => $question['id'],
                'result' => $question['correct_answer_value']
            ]
        );
    }


    private function createOrUpdateOptionQuestion($question){

        if (isset($question['options']) && is_array($question['options']) && $question['options']){
          foreach ($question['options'] as $key=>$option){

//              if (!isset($option['content'])){
//                  Log::alert('no option question');
//                  throw ValidationException::withMessages([t('The option content is required')]);
//              }
//              if (!$option['content']){
//                  Log::alert('no option question');
//                  throw ValidationException::withMessages([t('The option content is required')]);
//              }
              if (!isset($question['correct_answer_index'])){
                  throw ValidationException::withMessages([t('The correct_answer_index is required')]);
              }

              $path = null;
              if (isset($option['image'])){
               $path = $this->uploadFile($option['image']);
              }
              if (isset($option['id']) && $option['id']){
                  $data = ['content'=>$option['content'],'result'=>$key==$question['correct_answer_index']?1:0];
                  if ($path){
                      $data['image'] = $path;
                  }
                  OptionQuestion::query()->where('id',$option['id'])->update($data);
              }else{
                  $data = [
                      'question_id'=>$question['id'],
                      'content'=>$option['content'],
                      'result'=>$key==$question['correct_answer_index']?1:0,
                  ];
                  if ($path){
                      $data['image'] = $path;
                  }
                  OptionQuestion::query()->create($data);
              }
          }
        }
    }

    private function createOrUpdateMatchQuestion($question){
       // dd($question);
        if (isset($question['options']) && is_array($question['options']) && $question['options']){
            foreach ($question['options'] as $key=>$option){

//                if (!isset($option['content'])){
//                    Log::alert('no option match');
//                    throw ValidationException::withMessages([t('The option content is required')]);
//                }
//                if (!$option['content']){
//                    Log::alert('no option match');
//                    throw ValidationException::withMessages([t('The option content is required')]);
//                }
                if (is_null($option['answer'])){
                    throw ValidationException::withMessages([t('The option answer is required')]);
                }
                $data = [
                    'content' => $option['content'],
                    'result' => $option['answer'],
                ];

                if (isset($option['image'])){
                    $data['image'] = $this->uploadFile($option['image']);
                }

                if (isset($option['id']) && $option['id']){
                    MatchQuestion::query()->where('id',$option['id'])->update($data);
                }else{
                    $data['question_id'] = $question['id'];
                    $data['uid'] = Str::uuid();
                    MatchQuestion::query()->create($data);
                }
            }
        }
    }

    private function createOrUpdateSortQuestion($question){
        if (isset($question['options']) && is_array($question['options']) && $question['options']){
            $count = 1;
            foreach ($question['options'] as $option){

                if (!isset($option['content'])){
                    Log::alert('no option sort');
                    throw ValidationException::withMessages([t('The option content is required')]);
                }


                $data = [
                    'content' => $option['content'],
                    'ordered' => $count,
                ];
                if (isset($option['image'])){
                    $data['image'] = $this->uploadFile($option['image']);
                }

                if (isset($option['id']) && $option['id']){
                    SortQuestion::query()->where('id',$option['id'])->update($data);
                }else{
                    $data['question_id'] = $question['id'];
                    $data['uid'] = Str::uuid();
                    SortQuestion::query()->create($data);
                }
                $count++;
            }
        }
    }
    private function createOrUpdateFillBlankQuestion($question){
        $question_id = $question['id'];

        if ($question['blanks_count'] !== $question['fields_count']) {
            throw  new GeneralException(t('The number of fields does not match the number of blanks'));
        }
        if (isset($question['old']) && count($question['old']) > 0) {
            //get old blanks for this question and if blank in old blanks =>update
            // else if the user delete blank we delete it here

            FillBlankQuestion::query()
                ->whereNotIn('id', array_keys($question['old']))
                ->where('question_id', $question_id)
                ->delete();

            foreach ($question['old'] as $id => $blank) {
                FillBlankQuestion::query()->where('id', $id)->update(
                    [
                        'content' => $blank['content'],
                    ]
                );
            }
        }

        if (isset($question['new']) && count($question['new']) > 0) {
            foreach ($question['new'] as $blank) {
                FillBlankQuestion::query()->create(
                    [
                        'uid' => \Str::uuid(),
                        'question_id' => $question_id,
                        'content' => $blank['content'],
                    ]
                );
            }
        }    }

    public function deleteOption(Request $request)
    {
        $request->validate(['id' => 'required', 'type' => 'required']);
        $result = false;
        if ($request['type'] == 2) {
            $this->deleteOptionImage($request['id'],2);
            $result = OptionQuestion::query()->where('id', $request['id'])->delete();
        } else if ($request['type'] == 3) {
            $this->deleteOptionImage($request['id'],3);
            $result = MatchQuestion::query()->where('id', $request['id'])->delete();
        } else if ($request['type'] == 4) {
            $result = SortQuestion::query()->where('id', $request['id'])->delete();
        }
        return response()->json([
            'status' => (bool)$result,
            'message' => (bool)$result ? t('Option Deleted Successfully') : t('Option Not Deleted')
        ]);
    }



    public function deleteQuestionFile(Request $request){
        $request->validate(['id'=>'required','file_type'=>'required']);
        return $this->deleteQF($request['id'],$request['file_type']);
    }
    //delete question file [image - audio]
    private function deleteQF($id,$file_type){
        $type = null;
        if ($file_type == 'image') {
            $type = 'image';
        } else if ($file_type == 'audio') {
            $type = 'audio';
        } else if ($file_type == 'question_reader') {
            $type = 'question_reader';
        }

        $question = Question::query()->findOrFail($id);

        if ($question[$type]){
            deleteFile($question[$type]);
            $question->update([$type => null]);
        }

        return response()->json([
            'status' => (bool)$question,
            'message' => (bool)$question ? t('Deleted Successfully') : t('Not Deleted')
        ]);
    }


    //delete image directly from option
    public function deleteOptionImageRequest(Request $request){
        $request->validate(['id'=>'required','type'=>'required']);
        return $this->deleteOptionImage($request['id'],$request['type']);
    }
    private function deleteOptionImage($id,$type){
        $option = null;
        if ($type == 2) {
            $option = OptionQuestion::query()->findOrFail($id);

        }else if ($type == 3) {
            $option = MatchQuestion::query()->findOrFail($id);
        }else if ($type == 4) {
            $option = SortQuestion::query()->findOrFail($id);
        }
        if ($option['image']){
            deleteFile($option['image']);
            $option->update(['image' => null]);

        }
        return response()->json([
            'status' => (bool)$option,
            'message' => (bool)$option ? t('Deleted Successfully').$type : t('Not Deleted')
        ]);
    }


    private function uploadFile($file, $path = '/questions')
    {
        $result = uploadNewFile($file,$path);
        return $result['path'];
    }

    public function updateQuestionStandards()
    {
//        $terms = Term::query()->whereHas('question', function ($query) {
//            $query->whereDoesntHave('question_standard');
//        })->whereRelation('level', 'year_id', 2)->get();
//        dd($terms);
        $question_standards = new QuestionStandardService(1);
        //arabs standards
        $question_standards->setQuestionsStandards(1);
        //non arabs standards
        $question_standards->setQuestionsStandards(0);

        return 'Done';
    }




}
