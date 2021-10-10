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

                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul>
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                        createです。
                        <form action="{{ route('contact.store') }}" method="POST">
                            @csrf
                            氏名

                            <input type="text" name="your_name" value="">
                            <br>
                            件名
                            <input type="text" name="title" value="">
                            <br>
                            メールアドレス
                            <input type="text" name="email" value="">
                            <br>
                            ホームページ
                            <input type="text" name="url" value="">
                            <br>
                            性別
                            <input type="radio" name="gender" id="" value="0">男
                            <input type="radio" name="gender" id="" value="1">女
                            <br>
                            {{-- 年齢
                            <select name="age" id="">
                                <option value="">選択してください</option>
                                <option value="1">~19歳</option>
                                <option value="2">20歳~29歳</option>
                                <option value="3">30歳~39歳</option>
                                <option value="3">40歳~49歳</option>
                                <option value="3">50歳~59歳</option>
                                <option value="3">60歳~</option>
                            </select> --}}
                            <br>
                            お問い合わせ内容
                            <textarea name="contact" id=""></textarea>
                            <br>
                            <input type="checkbox" name="caution" id="" value="1">
                            注意事項のチェック

                            <input type="submit" name="btn btm-info" value="登録する">

                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
