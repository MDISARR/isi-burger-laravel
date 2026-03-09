@extends('layouts.app', ['title' => 'Modifier burger'])

@section('content')
    <section class="rounded-xl bg-white p-6 shadow-sm">
        <h1 class="text-2xl font-bold text-slate-900">Modifier {{ $burger->name }}</h1>
        <p class="mt-1 text-sm text-slate-600">Mettez a jour les informations du burger.</p>

        <form action="{{ route('admin.burgers.update', $burger) }}" method="POST" enctype="multipart/form-data" class="mt-5">
            @include('admin.burgers._form', ['method' => 'PUT'])
        </form>
    </section>
@endsection
