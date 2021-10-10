@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">Dashboard</div>

                    <div class="card-body">
                        @if (session('status'))
                            <div class="alert alert-success" role="alert">
                                {{ session('status') }}
                            </div>
                        @endif

                        editです。
                        <form method="POST" action="{{ route('contact.update', ['id' => $contact->id]) }}">
                            @csrf
                            <input type="text" name="your_name" value="{{ $contact->your_name }}">
                            <br>
                            件名
                            <input type="text" name="title" value="{{ $contact->title }}">
                            <br>
                            メールアドレス
                            <input type="text" name="email" value="{{ $contact->email }}">
                            <br>
                            ホームページ
                            <input type="text" name="url" value="{{ $contact->url }}">
                            <br>
                            性別
                            <input type="radio" name="gender" id="" value="0" @if ($contact->gender === 0) checked @endif>男
                            <input type="radio" name="gender" id="" value="1" @if ($contact->gender === 1) checked @endif>女
                            <br>

                            <br>
                            お問い合わせ内容
                            <textarea name="contact" id="">{{ $contact->contact }}</textarea>
                            <br>
                            <input type="submit" name="btn btm-info" value="更新する">
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
