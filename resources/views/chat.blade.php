@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
       <div class="col-8">
        <Chat :user="{{ Auth::user() }}"/>
       </div>
    </div>
</div>
@endsection