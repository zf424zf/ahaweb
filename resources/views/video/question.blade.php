@extends('layout.main')
@section('title','选择问题')
@section('resource')
@endsection
@section('content')
    <div class="bg">
        <a href="">
            <img src="{{staticFile('images/active/act-beg-hea-logo.png')}}" alt="logo" class="active-logo">
        </a>
        <div class="select-question">
            <img src="{{staticFile('images/active/select-question-h.png')}}" alt="select-question" class="select-question-h">
        </div>
        <form class="select-question-list" action="">
            <ul>
                @foreach($question as $item)
                <li class="">
                    <label for="">
                        <input type="radio" name="select-question">
                        <img src="{{staticFile('images/active/select-question-default.png')}}" alt="default" class="default">
                        <img class="on" src="{{staticFile('images/active/select-question-on.png')}}" alt="on">
                        <span class="list-q">
							问题{{$loop->index + 1}}
						</span>
                        <span class="list-h">
							{{$item['question']}}
						</span>
                    </label>
                </li>
                    @endforeach
            </ul>
            <a href="./question.html" class="select-question-next">
                <img src="{{staticFile('images/active/select-question-next.png')}}" alt="next">
            </a>
        </form>
    </div>
@endsection

