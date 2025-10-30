@extends('layouts.app')

@section('title', 'Analysis')

@section('content')
    <div class="flex-1 flex flex-col p-10 gap-10 ">
        <div class="flex-1 ">
            @livewire('leave-credits-config')
        </div>
    </div>
@endsection
