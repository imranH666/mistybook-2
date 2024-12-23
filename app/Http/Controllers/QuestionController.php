<?php

namespace App\Http\Controllers;

use App\Models\Answer;
use App\Models\Favourite;
use App\Models\Question;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;

class QuestionController extends Controller
{
    function question_answer() {
        $questions = Question::latest()->get();

        return view('Frontend.Question_Answer.question_answer', [
            'questions' => $questions,
        ]);
    }

    function question_store(Request $request) {
        $request->validate([
            'question' => 'required',
        ], [
            'question.required' => 'Please, write a question',
        ]);

        $user = Auth::guard('user')->user();
        $slug = preg_replace('/[^\p{L}\p{M}\p{N}\s-]+/u', '', $request->question);
        $slug = preg_replace('/[\s-]+/u', '-', trim($slug));
        $slug = uniqid().'-'.Str::lower($slug);
        $lang = app()->getLocale() == 'en' ? 'en' : 'bn';


        Question::insert([
            'user_id' => $user->id,
            'question' => $request->question,
            'slug' => $slug,
            'lang' => $lang,
            'created_at' => Carbon::now(),
        ]);
        return back()->with('store_question', 'Published your question');
    }

    function answer_store(Request $request) {
        $request->validate([
            'answer' => 'required',
        ], [
            'answer.required' => 'Please, Write a answer'
        ]);

        $user = Auth::guard('user')->user();
        $slug = Str::random(16).'_'.random_int(10000000, 999999999).Str::random(16).'_'.random_int(10000000, 999999999);
        $lang = app()->getLocale() == 'en' ? 'en' : 'bn';

        $content = $request->answer;
        if (preg_match_all('/<img src="data:image\/(.*?);base64,(.*?)"/', $content, $matches)) {
            $images = [];

            foreach ($matches[2] as $index => $data) {
                $imageData = base64_decode($data);
                $extension = $matches[1][$index];
                $filename = uniqid() . '.' . $extension;

                $maneger = new ImageManager(new Driver);
                $maneger->read($imageData)->toJpeg(80)->save(public_path('upload/answers/'.$filename));

                $imageUrl = asset('upload/answers/' . $filename);
                $images[] = $imageUrl; // প্রতিটি ইমেজের URL সংগ্রহ করা

                // ইমেজ ট্যাগ পরিবর্তন
                $content = str_replace($matches[0][$index], "<img src=\"$imageUrl\"", $content);
            }

            Answer::insert([
                'user_id' => $user->id,
                'question_id' => $request->question_id,
                'answer' => $content,
                'slug' => $slug,
                'lang' => $lang,
                'created_at' => Carbon::now()
            ]);
        }else {
            Answer::insert([
                'user_id' => $user->id,
                'question_id' => $request->question_id,
                'answer' => $content,
                'slug' => $slug,
                'lang' => $lang,
                'created_at' => Carbon::now()
            ]);
        }
        return back()->with('store_answer', 'Published your answer');
    }

    function question_view($slug) {
        $question = Question::where('slug', $slug)->first();

        if($question) {
            return view('Frontend.Question_Answer.question_view', [
                'question' => $question,
            ]);
        }else {
            abort(404, 'Question not found');
        }
    }

    function delete_question($id) {
        $user = Auth::guard('user')->user();
        $answers = Answer::where('question_id', $id)->get();

        $question = Question::find($id);
        if (!$question) {
            return back()->with('error', 'Question not found');
        }
        $question->delete();
        Favourite::where('question_id', $id)->delete();

        if($answers) {
            foreach ($answers as $answer) {
                $content = $answer->answer;
                if (preg_match_all('/<img src="(.*?)"/', $content, $matches)) {
                    $imageUrls = $matches[1];

                    foreach ($imageUrls as $imageUrl) {
                        $filePath = str_replace(asset('upload/answers/'), '', $imageUrl);
                        $fullPath = public_path('upload/answers/' . $filePath);

                        if (file_exists($fullPath)) {
                            unlink($fullPath);
                        }
                    }
                }
            }
        }else {
            return back()->with('question_not_found', 'Question not found');
        }
        Answer::where('question_id', $id)->delete();

        return back()->with('question_success', 'The Question was deleted successfully');
    }
}
