@extends('layouts.app', ['title' => 'Dashboard'])

@section('content')
    <section class="grid gap-4 md:grid-cols-3">
        <article class="rounded-xl bg-white p-5 shadow-sm">
            <p class="text-sm text-slate-500">Commandes en cours</p>
            <p class="mt-2 text-3xl font-bold text-slate-900">{{ $ordersInProgress }}</p>
            <p class="mt-1 text-xs text-slate-500">Statuts En attente + En preparation</p>
        </article>

        <article class="rounded-xl bg-white p-5 shadow-sm">
            <p class="text-sm text-slate-500">Commandes validees aujourd'hui</p>
            <p class="mt-2 text-3xl font-bold text-slate-900">{{ $validatedToday }}</p>
            <p class="mt-1 text-xs text-slate-500">Statuts Prete ou Payee</p>
        </article>

        <article class="rounded-xl bg-white p-5 shadow-sm">
            <p class="text-sm text-slate-500">Recettes journalieres</p>
            <p class="mt-2 text-3xl font-bold text-orange-600">{{ number_format((float) $dailyRevenue, 0, ',', ' ') }} FCFA</p>
            <p class="mt-1 text-xs text-slate-500">Somme des paiements du jour</p>
        </article>
    </section>

    <section class="mt-6 grid gap-6 xl:grid-cols-2">
        <article class="rounded-xl bg-white p-5 shadow-sm">
            <h2 class="text-lg font-semibold text-slate-900">Nombre de commandes par mois</h2>
            <div class="mt-4 h-72">
                <canvas id="ordersByMonthChart"></canvas>
            </div>
        </article>

        <article class="rounded-xl bg-white p-5 shadow-sm">
            <h2 class="text-lg font-semibold text-slate-900">Produits vendus par categorie et par mois</h2>
            <div class="mt-4 h-72">
                <canvas id="categorySalesChart"></canvas>
            </div>
        </article>
    </section>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const ordersByMonth = @json($ordersByMonth);
        const categorySales = @json($categorySalesChart);

        new Chart(document.getElementById('ordersByMonthChart'), {
            type: 'line',
            data: {
                labels: ordersByMonth.labels,
                datasets: [{
                    label: 'Commandes',
                    data: ordersByMonth.values,
                    borderColor: '#f97316',
                    backgroundColor: 'rgba(249,115,22,0.2)',
                    borderWidth: 2,
                    fill: true,
                    tension: 0.3,
                }],
            },
            options: {
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: true },
                },
            },
        });

        new Chart(document.getElementById('categorySalesChart'), {
            type: 'bar',
            data: {
                labels: categorySales.labels,
                datasets: categorySales.datasets,
            },
            options: {
                maintainAspectRatio: false,
                responsive: true,
                scales: {
                    x: { stacked: true },
                    y: { stacked: true, beginAtZero: true },
                },
            },
        });
    </script>
@endsection
