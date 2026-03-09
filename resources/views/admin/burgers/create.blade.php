@extends('layouts.app', ['title' => 'Nouveau burger'])

@section('content')
    <section class="rounded-xl bg-white p-6 shadow-sm">
        <h1 class="text-2xl font-bold text-slate-900">Ajouter un burger</h1>
        <p class="mt-1 text-sm text-slate-600">Renseignez les informations produit du catalogue.</p>

        <form action="{{ route('admin.burgers.store') }}" method="POST" enctype="multipart/form-data" class="mt-5">
            @include('admin.burgers._form', ['method' => 'POST'])
        </form>
    </section>
@endsection
