@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
    <div class="w-full h-full flex flex-col p-10 gap-10">
        <div class="w-full flex justify-between">
            <h2 class="text-5xl font-bold text-gray-700">
                DASHBOARD OVERVIEW
            </h2>
        </div>
        <div class="flex-1">
            @livewire('regular-dashboard-data')
        </div>
    </div>
@endsection
