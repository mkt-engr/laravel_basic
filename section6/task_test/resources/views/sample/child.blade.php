<!-- resources/views/child.blade.phpとして保存 -->

@extends('sample.app')

@section('title', 'Page Title AAA')

@section('sidebar')
 @parent

<p>ここはメインのサイドバーに追加される</p>
@endsection

@section('content')
<p>ここが本文のコンテンツ</p>
@php
$users =[1,2,4];
@endphp

@foreach ($users as $user)
@if ($loop->first)
これは最初の繰り返し
@endif

@if ($loop->last)
これは最後の繰り返し
@endif

{{$user}}<br>

@endforeach
@endsection