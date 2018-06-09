@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">

        <div class="col-sm-12">

            <ol class="breadcrumb">
                <li>Home</li>
            </ol>

            <h3>Hello, {{ Auth::user()->name }}.</h3>
            <p>Chat with someone today!</p>
        </div>
    </div>

    <div class="row">
        <div class="col-xs-6 stat-box-outer">
            <div class="stat-box">
                <h2>{{ $stats['messages_count'] }}</h2>
                <p>messages sent within users</p>
            </div>
        </div>

        <div class="col-xs-6 stat-box-outer">
            <div class="stat-box">
                <h2>{{ $stats['user_count'] }}</h2>
                <p>users joined ChatterApp so far</p>
            </div>
        </div>
    </div>
    <div class="row">

        @foreach ($users as $user)
        <div class="col-xs-6 col-sm-3 user-box-outer">
            <a href="{{ route('conversation', $user->id)}}">
                <div class="user-box">
                    <div class="user-photo">
                        <span class="glyphicon glyphicon-user glyphicon-super-large" aria-hidden="true"></span>
                    </div>
                    <div class="user-text">
                        Chat with {{$user->name}}
                    </div>
                    
                    
                </div>

            </a>
        </div>
        @endforeach

    </div>
</div>
@endsection

@section('additional_css')
<style>
    .glyphicon-super-large {
        font-size: 3em;
        text-decoration: none;
    }

    .user-box {
        text-align: center;
        padding: 40px 20px 40px 20px;
        background-color: #eee;
        text-decoration: none;
    }

    .user-box:hover {
        background-color: #777;
        color: white;
        text-decoration: none;
    }

    .user-text:hover {
        text-decoration: none;
    }

    .user-box-outer {
        margin-bottom: 10px;
    }

    .stat-box-outer {
        margin-bottom: 30px;
    }

    .stat-box {
        text-align: center;
        padding: 20px 20px 20px 20px;
        background-color: #eee;
        text-decoration: none;
    }
</style>
@endsection

@section('additional_js')
<script>
    $(document).ready(function(){
        $("#chatusernone").addClass('active');
    });
</script>
@endsection