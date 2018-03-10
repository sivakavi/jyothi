@extends('student.layouts.student')

@section('title', 'Test')
@section('content')
    <style>
        .question{
            margin-top: 20px;
            margin-left: 30px;
            font-size: 20px;
            font-weight: bold;
        }
        .answer{
            margin-left: 50px;
        }
        .correct-answer{
            font-size:18px;
            margin-bottom:20px;
            margin-top:20px;
            word-wrap: break-word;
        }
        .correct-answer span{
            color:#0B8;
        }
        .inline{
            display: inline-block;
        }
        .inline + .inline{
            margin-left:10px;
        }
        .radio{
            color:#999;
            font-size:15px;
            position:relative;
        }
        .radio span{
            position:relative;
            padding-left:20px;
        }
        .radio span:after{
            content:'';
            width:15px;
            height:15px;
            border:3px solid;
            position:absolute;
            left:0;
            top:1px;
            border-radius:100%;
            -ms-border-radius:100%;
            -moz-border-radius:100%;
            -webkit-border-radius:100%;
            box-sizing:border-box;
            -ms-box-sizing:border-box;
            -moz-box-sizing:border-box;
            -webkit-box-sizing:border-box;
        }
        .radio input[type="radio"]{
            cursor: pointer; 
            position:absolute;
            width:100%;
            height:100%;
            z-index: 1;
            opacity: 0;
            filter: alpha(opacity=0);
            -ms-filter: "progid:DXImageTransform.Microsoft.Alpha(Opacity=0)"
        }
        .radio input[type="radio"]:checked + span{
            color:#0B8;  
        }
        .radio input[type="radio"]:checked + span:before{
            content:'';
        width:5px;
        height:5px;
        position:absolute;
        background:#0B8;
        left:5px;
        top:6px;
        border-radius:100%;
        -ms-border-radius:100%;
        -moz-border-radius:100%;
        -webkit-border-radius:100%;
        }
    </style>
    <div class="page-header clearfix"></div>
    <div class="margin-top-30">
        <?php $i=1; ?>
        @foreach($questions as $question)
            <div class="row top_tiles">
                <p class="question"><span>Q {{ $i }}</span> - {{ $question['question']}}?</p>
                @if($question['image'])
                    <img src="{{ asset('uploads/'.$question['image']) }}" class="img-responsive"/>
                @endif
                <div class="answer">
                    <label class="radio inline"> 
                        <input type="radio" name="{{ $question['id'] }}" value="1" required>
                        <span> {{ $question['answer1']}} </span> 
                    </label><br>
                    <label class="radio inline"> 
                        <input type="radio" name="{{ $question['id'] }}" value="2">
                        <span>{{ $question['answer2']}} </span> 
                    </label><br>
                    <label class="radio inline"> 
                        <input type="radio" name="{{ $question['id'] }}" value="3">
                        <span>{{ $question['answer3']}} </span> 
                    </label><br>
                    <label class="radio inline"> 
                        <input type="radio" name="{{ $question['id'] }}" value="4">
                        <span>{{ $question['answer4']}} </span> 
                    </label><br>
                </div>
                <?php $correctAnswer = $question['correct_answer'];?>
                <div class="answer correct-answer hide"> Correct Answer : <span>{{ $question["answer$correctAnswer"] }} </span></div>
                @if($question['description'])
                    <div class="answer correct-answer explanation hide"> Explanation  : {{ $question['description'] }}</div>
                @endif
            </div>
            <?php $i++; ?>
        @endforeach
    <div class="well well-sm margin-top-50">
        <button id="submitTest" type="submit" class="btn btn-primary btn-lm">Submit</button>
    </div>
@endsection

@section('scripts')
    @parent
    {{ Html::script(mix('assets/admin/js/dashboard.js')) }}
    <script>
        $(function() {
            $( "#submitTest" ).click(function() {
                var names = [];
                var valid = 1;
                $('input:radio').each(function () {
                    var rname = $(this).attr('name');
                    if ($.inArray(rname, names) == -1) names.push(rname);
                });

                //do validation for each group
                $.each(names, function (i, name) {
                    if ($('input[name="' + name + '"]:checked').length == 0) {
                        valid = 0;
                    }
                });
                if(valid==0){
                    alert("Please fill all Questions");
                    return false;
                }
                else{
                    $('.correct-answer').removeClass('hide');
                }
            });
        });
    </script>
@endsection

@section('styles')
    @parent
    {{ Html::style(mix('assets/admin/css/dashboard.css')) }}
@endsection