@extends('vendor.installer.layouts.master')

@section('template_title')
    {{ trans('installer_messages.welcome.templateTitle') }}
@endsection

@section('title')
    {{ trans('installer_messages.welcome.title') }}
@endsection

@section('container')
    <h4 class="text-center" style="margin-top: 0;">Laraku - Laravel 11 Starter Kit</h4>
    <p class="text-center" style="margin: 10px 0 40px;">
      {{ __('Thank you for purchasing Laraku. Click the button below to start the installation.') }}
    </p>
    <p class="text-center">
      <a href="{{ route('LaravelInstaller::requirements') }}" class="button">
        {{ trans('installer_messages.welcome.next') }}
        <i class="fa fa-angle-right fa-fw" aria-hidden="true"></i>
      </a>
    </p>
@endsection
