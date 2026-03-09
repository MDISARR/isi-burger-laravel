<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'ISI Burger' }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen bg-slate-50 text-slate-900">
    <header class="border-b border-slate-200 bg-white">
        <nav class="mx-auto flex w-full max-w-7xl items-center justify-between px-4 py-4 sm:px-6 lg:px-8">
            <a href="{{ route('catalog.index') }}" class="text-xl font-bold text-orange-600">ISI BURGER</a>
            <div class="flex items-center gap-2 text-sm sm:gap-3">
                <a href="{{ route('catalog.index') }}" class="rounded-md px-3 py-2 font-medium text-slate-700 hover:bg-orange-50 hover:text-orange-700">Catalogue</a>
                <a href="{{ route('admin.dashboard') }}" class="rounded-md px-3 py-2 font-medium text-slate-700 hover:bg-slate-100">Dashboard</a>
                <a href="{{ route('admin.burgers.index') }}" class="rounded-md px-3 py-2 font-medium text-slate-700 hover:bg-slate-100">Burgers</a>
                <a href="{{ route('admin.orders.index') }}" class="rounded-md px-3 py-2 font-medium text-slate-700 hover:bg-slate-100">Commandes</a>
            </div>
        </nav>
    </header>

    <main class="mx-auto w-full max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
        @if (session('success'))
            <div class="mb-4 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
                {{ session('success') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="mb-4 rounded-lg border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700">
                <p class="font-semibold">Certaines actions n'ont pas abouti :</p>
                <ul class="mt-2 list-disc pl-6">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{ $slot ?? '' }}
        @yield('content')
    </main>
</body>
</html>
