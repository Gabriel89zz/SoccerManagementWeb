@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4 bg-white p-4 rounded shadow-sm">
        <div class="d-flex align-items-center">
            <div class="bg-primary text-white rounded p-3 me-3">
                <i class="fas {{ $reportConfig['icon'] }} fa-2x"></i>
            </div>
            <div>
                <h4 class="mb-0 fw-bold">{{ $reportConfig['title'] }}</h4>
                <small class="text-muted">{{ $reportConfig['view'] }}</small>
            </div>
        </div>
        <div>
            <a href="{{ route('reports.export', $key) }}" class="btn btn-success me-2">
                <i class="fas fa-file-csv me-2"></i> Exportar CSV
            </a>
            <a href="{{ route('reports.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-2"></i> Volver
            </a>
        </div>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-body p-0">
            
            <!-- Barra de Búsqueda -->
            <div class="p-3 border-bottom bg-light">
                <form action="{{ route('reports.show', $key) }}" method="GET">
                    <div class="input-group" style="max-width: 400px;">
                        <span class="input-group-text bg-white border-end-0"><i class="fas fa-search text-muted"></i></span>
                        <input type="text" name="search" class="form-control border-start-0 ps-0" 
                               placeholder="Buscar en el reporte..." value="{{ request('search') }}">
                        <button class="btn btn-primary" type="submit">Buscar</button>
                    </div>
                </form>
            </div>

            <!-- Tabla Dinámica -->
            <div class="table-responsive">
                <table class="table table-hover table-striped align-middle mb-0" style="font-size: 0.9rem;">
                    <thead class="bg-dark text-white">
                        <tr>
                            @foreach($columns as $col)
                                <th class="text-uppercase py-3 text-nowrap" style="font-size: 0.8rem; letter-spacing: 0.5px;">
                                    {{ str_replace('_', ' ', $col) }}
                                </th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($data as $row)
                            <tr>
                                @foreach($columns as $col)
                                    <td class="py-3 text-nowrap">
                                        {{-- LÓGICA DE FORMATO INTELIGENTE --}}
                                        @php
                                            $value = $row->$col;
                                            $colLower = strtolower($col);
                                            // Palabras clave para detectar dinero
                                            $isMoney = \Illuminate\Support\Str::contains($colLower, ['money', 'cost', 'value', 'fee', 'balance', 'spending', 'income', 'eur', 'usd', 'budget']);
                                            // Palabras clave para detectar balances (rojo/verde)
                                            $isBalance = \Illuminate\Support\Str::contains($colLower, ['balance', 'profit', 'net']);
                                        @endphp

                                        @if($isMoney && is_numeric($value))
                                            {{-- Formato de Moneda --}}
                                            <span class="@if($isBalance) {{ $value < 0 ? 'text-danger fw-bold' : 'text-success fw-bold' }} @endif">
                                                {{ $value < 0 ? '-' : '' }}€ {{ number_format(abs($value), 2) }}
                                            </span>
                                        @elseif(is_numeric($value) && strpos($value, '.') !== false) 
                                            {{-- Formato Decimal Genérico (si no es dinero pero tiene decimales) --}}
                                            {{ number_format($value, 2) }}
                                        @else
                                            {{-- Texto Normal --}}
                                            {{ $value }}
                                        @endif
                                    </td>
                                @endforeach
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ count($columns) }}" class="text-center py-5 text-muted">
                                    <i class="fas fa-inbox fa-3x mb-3"></i><br>
                                    No se encontraron datos para este reporte.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <!-- Paginación -->
            <div class="d-flex justify-content-between align-items-center p-3 border-top bg-light">
                <div class="text-muted small">
                    Mostrando {{ $data->firstItem() ?? 0 }} a {{ $data->lastItem() ?? 0 }} de {{ $data->total() }} registros
                </div>
                <div>
                    {{ $data->links('pagination::bootstrap-5') }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection