@extends('layouts.app')

@section('title', 'Position')

@section('content')
    <div class="flex-1 flex flex-col p-10 gap-10">
        <div class="w-full flex justify-between">
            <h2 class="text-5xl font-bold text-gray-700">
                POSITION
            </h2>
        </div>
        @livewire('regular-position-data')
    </div>
@endsection