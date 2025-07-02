@php
use Filament\Resources\Pages\ListRecords;
@endphp

@extends('filament.resources.pages.list-records')

@section('content')
    <div class="flex flex-col space-y-6">
        {{ $this->table }}
    </div>
@endsection
