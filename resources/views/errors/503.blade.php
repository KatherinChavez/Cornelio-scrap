@extends('errors.illustrated-layout')

@section('title', __('Mantenimiento'))
{{--@section('code', '503')--}}
@section('message', __($exception->getMessage() ?: 'Servicio en mantenimiento, intentalo mas tarde'))
