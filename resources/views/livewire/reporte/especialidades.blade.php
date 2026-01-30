<div class="py-4">
    <div class="max-w-6xl mx-auto sm:px-4 lg:px-6">
        <!-- Título y Filtros de Fecha -->
        <div class="bg-white rounded-lg shadow px-3 py-2 mb-3">
            <div class="flex flex-wrap items-center justify-between gap-2">
                <div class="flex items-center gap-3">
                    <h1 class="text-base font-bold text-gray-800">Reporte de Especialidades</h1>
                    <span class="text-indigo-600 text-xs font-medium">
                        {{ \Carbon\Carbon::parse($fechaDesde)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($fechaHasta)->format('d/m/Y') }}
                    </span>
                </div>
                <div class="flex items-center gap-2">
                    <label class="text-xs text-gray-600">Desde:</label>
                    <input type="date" wire:model.lazy="fechaDesde" class="border-gray-300 rounded text-xs py-1 px-1 w-28 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                    <label class="text-xs text-gray-600">Hasta:</label>
                    <input type="date" wire:model.lazy="fechaHasta" class="border-gray-300 rounded text-xs py-1 px-1 w-28 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                </div>
            </div>
        </div>

        <!-- Tarjetas de resumen -->
        <div class="grid grid-cols-4 gap-2 mb-3">
            <div class="bg-gradient-to-r from-green-400 to-green-600 rounded shadow px-2 py-1 text-white">
                <p class="text-xs opacity-80">Agendadas</p>
                <p class="text-base font-bold">{{ $totalAgendado }}</p>
            </div>
            <div class="bg-gradient-to-r from-yellow-400 to-yellow-600 rounded shadow px-2 py-1 text-white">
                <p class="text-xs opacity-80">En Espera</p>
                <p class="text-base font-bold">{{ $totalEspera }}</p>
            </div>
            <div class="bg-gradient-to-r from-blue-400 to-blue-600 rounded shadow px-2 py-1 text-white">
                <p class="text-xs opacity-80">Pendientes</p>
                <p class="text-base font-bold">{{ $totalPendiente }}</p>
            </div>
            <div class="bg-gradient-to-r from-red-400 to-red-600 rounded shadow px-2 py-1 text-white">
                <p class="text-xs opacity-80">Rechazadas</p>
                <p class="text-base font-bold">{{ $totalRechazado }}</p>
            </div>
        </div>

        <!-- Gráficos -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 mb-4">
            <!-- Gráfico de Pastel - Distribución por Estado -->
            <div class="bg-white rounded-lg shadow px-4 py-2">
                <h3 class="text-sm font-semibold text-gray-800 mb-1">Distribución por Estado</h3>
                <div class="flex justify-center items-center" wire:ignore>
                    <canvas id="pieChart" style="max-width: 280px; max-height: 180px;"></canvas>
                </div>
            </div>

            <!-- Gráfico de Barras - Top Especialidades -->
            <div class="bg-white rounded-lg shadow p-4">
                <h3 class="text-sm font-semibold text-gray-800 mb-2">Top 10 Especialidades</h3>
                <div class="flex justify-center" wire:ignore>
                    <canvas id="barChart" style="max-width: 100%; max-height: 200px;"></canvas>
                </div>
            </div>
        </div>
        
        <!-- Datos para gráficos (hidden) -->
        <div id="chartData" 
             data-agendado="{{ $totalAgendado }}" 
             data-espera="{{ $totalEspera }}" 
             data-pendiente="{{ $totalPendiente }}" 
             data-rechazado="{{ $totalRechazado }}"
             data-especialidades="{{ json_encode($datosGraficos) }}"
             style="display: none;"></div>

        <!-- DataTable con Paginación -->
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <div class="px-4 py-2 border-b border-gray-200 bg-gray-50 flex items-center gap-4">
                <h2 class="text-sm font-semibold text-gray-800">Detalle por Especialidad</h2>
                <span class="text-xs text-gray-500">{{ $especialidades->total() }} especialidades</span>
                <button wire:click="exportarExcel" class="inline-flex items-center px-3 py-1 bg-green-600 hover:bg-green-700 text-white text-xs font-medium rounded shadow-sm transition-colors">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    Exportar Excel
                </button>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="px-3 py-2 text-left text-xs font-semibold text-gray-600 uppercase">Código</th>
                            <th class="px-3 py-2 text-left text-xs font-semibold text-gray-600 uppercase">Especialidad</th>
                            <th class="px-3 py-2 text-center text-xs font-semibold text-green-700 uppercase">Agend.</th>
                            <th class="px-3 py-2 text-center text-xs font-semibold text-yellow-700 uppercase">Espera</th>
                            <th class="px-3 py-2 text-center text-xs font-semibold text-blue-700 uppercase">Pend.</th>
                            <th class="px-3 py-2 text-center text-xs font-semibold text-red-700 uppercase">Rech.</th>
                            <th class="px-3 py-2 text-center text-xs font-semibold text-gray-700 uppercase">Total</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($especialidades as $especialidad)
                        <tr class="hover:bg-gray-50">
                            <td class="px-3 py-2 whitespace-nowrap text-xs font-medium text-gray-900">{{ $especialidad->servcod }}</td>
                            <td class="px-3 py-2 whitespace-nowrap text-xs text-gray-700">{{ Str::limit($especialidad->servnomb, 35) }}</td>
                            <td class="px-3 py-2 whitespace-nowrap text-center">
                                <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-green-100 text-green-800 text-xs font-semibold">{{ $especialidad->agendadas }}</span>
                            </td>
                            <td class="px-3 py-2 whitespace-nowrap text-center">
                                <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-yellow-100 text-yellow-800 text-xs font-semibold">{{ $especialidad->en_espera }}</span>
                            </td>
                            <td class="px-3 py-2 whitespace-nowrap text-center">
                                <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-blue-100 text-blue-800 text-xs font-semibold">{{ $especialidad->pendientes }}</span>
                            </td>
                            <td class="px-3 py-2 whitespace-nowrap text-center">
                                <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-red-100 text-red-800 text-xs font-semibold">{{ $especialidad->rechazadas }}</span>
                            </td>
                            <td class="px-3 py-2 whitespace-nowrap text-center">
                                <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-gray-200 text-gray-800 text-xs font-bold">{{ $especialidad->agendadas + $especialidad->en_espera + $especialidad->pendientes + $especialidad->rechazadas }}</span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="bg-gray-100">
                        <tr class="font-bold text-sm">
                            <td class="px-3 py-2 text-gray-900" colspan="2">TOTALES</td>
                            <td class="px-3 py-2 text-center text-green-700">{{ $totalAgendado }}</td>
                            <td class="px-3 py-2 text-center text-yellow-700">{{ $totalEspera }}</td>
                            <td class="px-3 py-2 text-center text-blue-700">{{ $totalPendiente }}</td>
                            <td class="px-3 py-2 text-center text-red-700">{{ $totalRechazado }}</td>
                            <td class="px-3 py-2 text-center text-gray-800">{{ $totalAgendado + $totalEspera + $totalPendiente + $totalRechazado }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
            <!-- Paginación -->
            <div class="px-4 py-3 border-t border-gray-200 bg-gray-50">
                {{ $especialidades->links() }}
            </div>
        </div>
    </div>

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Inicializar cuando carga la página
        document.addEventListener('DOMContentLoaded', function () {
            initCharts();
        });

        // Reinicializar cuando Livewire actualiza el componente
        document.addEventListener('livewire:load', function () {
            Livewire.hook('message.processed', (message, component) => {
                updateCharts();
            });
        });

        function initCharts() {
            const dataEl = document.getElementById('chartData');
            if (!dataEl) return;

            const totalAgendado = parseInt(dataEl.dataset.agendado) || 0;
            const totalEspera = parseInt(dataEl.dataset.espera) || 0;
            const totalPendiente = parseInt(dataEl.dataset.pendiente) || 0;
            const totalRechazado = parseInt(dataEl.dataset.rechazado) || 0;

            // Gráfico de Pastel
            const ctxPie = document.getElementById('pieChart');
            if (ctxPie) {
                window.pieChartInstance = new Chart(ctxPie.getContext('2d'), {
                    type: 'doughnut',
                    data: {
                        labels: ['Agendadas', 'En Espera', 'Pendientes', 'Rechazadas'],
                        datasets: [{
                            data: [totalAgendado, totalEspera, totalPendiente, totalRechazado],
                            backgroundColor: [
                                'rgba(34, 197, 94, 0.8)',
                                'rgba(234, 179, 8, 0.8)',
                                'rgba(59, 130, 246, 0.8)',
                                'rgba(239, 68, 68, 0.8)'
                            ],
                            borderColor: [
                                'rgba(34, 197, 94, 1)',
                                'rgba(234, 179, 8, 1)',
                                'rgba(59, 130, 246, 1)',
                                'rgba(239, 68, 68, 1)'
                            ],
                            borderWidth: 2
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: true,
                        plugins: {
                            legend: {
                                position: 'right',
                                labels: { 
                                    font: { size: 11 },
                                    padding: 15,
                                    usePointStyle: true,
                                    pointStyle: 'rect'
                                }
                            }
                        }
                    }
                });
            }

            // Datos para gráfico de barras
            let especialidades = [];
            try {
                especialidades = JSON.parse(dataEl.dataset.especialidades || '[]');
            } catch(e) { especialidades = []; }
            
            const top10 = especialidades
                .map(e => ({
                    nombre: e.servnomb.length > 18 ? e.servnomb.substring(0, 18) + '...' : e.servnomb,
                    total: parseInt(e.agendadas) + parseInt(e.en_espera) + parseInt(e.pendientes) + parseInt(e.rechazadas)
                }))
                .sort((a, b) => b.total - a.total)
                .slice(0, 10);

            // Gráfico de Barras
            const ctxBar = document.getElementById('barChart');
            if (ctxBar) {
                window.barChartInstance = new Chart(ctxBar.getContext('2d'), {
                    type: 'bar',
                    data: {
                        labels: top10.map(e => e.nombre),
                        datasets: [{
                            label: 'Total Solicitudes',
                            data: top10.map(e => e.total),
                            backgroundColor: 'rgba(99, 102, 241, 0.7)',
                            borderColor: 'rgba(99, 102, 241, 1)',
                            borderWidth: 1,
                            borderRadius: 4
                        }]
                    },
                    options: {
                        responsive: true,
                        indexAxis: 'y',
                        plugins: {
                            legend: { display: false }
                        },
                        scales: {
                            x: { beginAtZero: true },
                            y: { ticks: { font: { size: 9 } } }
                        }
                    }
                });
            }
        }

        function updateCharts() {
            const dataEl = document.getElementById('chartData');
            if (!dataEl) return;

            const totalAgendado = parseInt(dataEl.dataset.agendado) || 0;
            const totalEspera = parseInt(dataEl.dataset.espera) || 0;
            const totalPendiente = parseInt(dataEl.dataset.pendiente) || 0;
            const totalRechazado = parseInt(dataEl.dataset.rechazado) || 0;

            // Actualizar gráfico de pastel
            if (window.pieChartInstance) {
                window.pieChartInstance.data.datasets[0].data = [totalAgendado, totalEspera, totalPendiente, totalRechazado];
                window.pieChartInstance.update();
            }

            // Actualizar gráfico de barras
            let especialidades = [];
            try {
                especialidades = JSON.parse(dataEl.dataset.especialidades || '[]');
            } catch(e) { especialidades = []; }
            
            const top10 = especialidades
                .map(e => ({
                    nombre: e.servnomb.length > 18 ? e.servnomb.substring(0, 18) + '...' : e.servnomb,
                    total: parseInt(e.agendadas) + parseInt(e.en_espera) + parseInt(e.pendientes) + parseInt(e.rechazadas)
                }))
                .sort((a, b) => b.total - a.total)
                .slice(0, 10);

            if (window.barChartInstance) {
                window.barChartInstance.data.labels = top10.map(e => e.nombre);
                window.barChartInstance.data.datasets[0].data = top10.map(e => e.total);
                window.barChartInstance.update();
            }
        }
    </script>
</div>
