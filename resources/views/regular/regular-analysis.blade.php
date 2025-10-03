@extends('layouts.app')

@section('title', 'Analysis')

@section('content')
<div class="w-[calc(100vw-224px)] flex-1 flex flex-col p-10 gap-10">
    <div class="w-full flex justify-between">
        <h2 class="text-5xl font-bold text-gray-700">
            ANALYSIS
        </h2>
    </div>
    @livewire('regular-analysis-data')
</div>
@endsection