@extends('layouts.app')

@section('content')


@if (!Auth::user()->isAdmin)
  @include('home.homeUser')
@else
  @include('home.homeAdmin')
@endif

 

@endsection

