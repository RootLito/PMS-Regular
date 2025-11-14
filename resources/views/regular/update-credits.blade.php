@extends('layouts.app')

@section('title', 'Update Credits')

@section('content')
    <div class="flex-1">
        @livewire('update-leave-credits', ['id' => $id])
    </div>
@endsection
