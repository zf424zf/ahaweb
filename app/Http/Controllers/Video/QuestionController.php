<?php
/**
 * Created by PhpStorm.
 * User: 70427
 * Date: 2017/8/24
 * Time: 17:06
 */

namespace App\Http\Controllers\Video;


class QuestionController
{
    public function questionList(){
        $question = app('api')->get('question')->data();
        return view('video.question', ['question' => $question]);
    }
}