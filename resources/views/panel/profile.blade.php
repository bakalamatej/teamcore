@extends('layouts.panel-layout')

@section('content')
    <div class="space-y-6">
        <div class="p-4 sm:p-8 bg-white shadow-xl rounded-lg">
            <div class="max-w-xl">
                @include('panel.partials.update-profile-information-form')
            </div>
        </div>

        <div class="p-4 sm:p-8 bg-white shadow-xl rounded-lg">
            <div class="max-w-xl">
                @include('panel.partials.update-password-form')
            </div>
        </div>

        <div class="p-4 sm:p-8 bg-white shadow-xl rounded-lg">
            <div class="max-w-xl">
                @include('panel.partials.delete-user-form')
            </div>
        </div>
    </div>
@endsection
