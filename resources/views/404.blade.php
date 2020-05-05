@extends('layouts.app')

@section('content')
  <div class="p404_content">
    <h1>La page que vous recherchez </br> semble introuvable.</h1>
    <img src="@asset('images/404.png')" />
    <a href="/">Retour à la Page d’accueil</a>
  </div>
@endsection
