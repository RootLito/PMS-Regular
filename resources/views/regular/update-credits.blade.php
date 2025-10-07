@extends('layouts.app')

@section('title', 'Update Credits')

@section('content')
<div class="w-[calc(100vw-224px)] flex-1 flex flex-col p-10 gap-10">
    
    @livewire('update-leave-credits', ['id' => $id])
</div>
@endsection