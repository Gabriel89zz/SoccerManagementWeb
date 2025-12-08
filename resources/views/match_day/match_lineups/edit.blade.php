@extends('layouts.admin')

@section('content')
<div class="card shadow" style="max-width: 900px; margin: 0 auto;">
    <div class="card-header bg-warning text-dark"><h5 class="mb-0">Edit Tactical Lineup</h5></div>
    <div class="card-body">

        @if($errors->any())
            <div class="alert alert-danger">
                {{ $errors->first() }}
            </div>
        @endif

        <form action="{{ route('match-lineups.update', $lineup->match_lineup_id) }}" method="POST">
            @csrf @method('PUT')
            
            <!-- SECCIÓN 1: CONTEXTO DEL PARTIDO -->
            <h6 class="text-primary mb-3"><i class="fas fa-futbol me-2"></i>Match Selection</h6>
            <div class="row mb-3">
                <div class="col-md-12">
                    <label class="form-label">Match <span class="text-danger">*</span></label>
                    <select name="match_id" id="match_select" class="form-select select2-search" required
                            data-placeholder="Type to search matches..." 
                            data-ajax-url="{{ route('api.match-lineups.matches.search') }}">
                        @if($lineup->match)
                            <option value="{{ $lineup->match_id }}" selected>
                                {{ $lineup->match->homeTeam->name ?? '?' }} vs 
                                {{ $lineup->match->awayTeam->name ?? '?' }} - 
                                {{ $lineup->match->match_date ? $lineup->match->match_date->format('Y-m-d H:i') : 'TBD' }}
                            </option>
                        @endif
                    </select>
                    <div class="form-text">Start typing team names, date (YYYY-MM-DD), or match ID</div>
                </div>
            </div>

            <!-- SECCIÓN 2: DETALLES TÁCTICOS -->
            <h6 class="text-primary mb-3 mt-4"><i class="fas fa-clipboard-list me-2"></i>Tactical Setup</h6>
            <div class="row mb-3">
                <div class="col-md-4">
                    <label class="form-label">Team <span class="text-danger">*</span></label>
                    <select name="team_id" id="team_select" class="form-select select2-search" required>
                        @foreach($teams as $team)
                            <option value="{{ $team->team_id }}" {{ $lineup->team_id == $team->team_id ? 'selected' : '' }}>
                                {{ $team->name }}
                            </option>
                        @endforeach
                    </select>
                    
                </div>
                <div class="col-md-4">
                    <label class="form-label">Formation <span class="text-danger">*</span></label>
                    <select name="formation_id" class="form-select select2-search" required>
                        @foreach($formations as $f)
                            <option value="{{ $f->formation_id }}" {{ $lineup->formation_id == $f->formation_id ? 'selected' : '' }}>
                                {{ $f->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Head Coach <span class="text-danger">*</span></label>
                    <select name="coach_id" class="form-select select2-search" required>
                        @foreach($coaches as $coach)
                            <option value="{{ $coach->coach_id }}" {{ $lineup->coach_id == $coach->coach_id ? 'selected' : '' }}>
                                {{ $coach->full_name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="text-end">
                <a href="{{ route('match-lineups.index') }}" class="btn btn-secondary me-2">Cancel</a>
                <button type="submit" class="btn btn-warning">Update</button>
            </div>
        </form>
    </div>
</div>

@section('scripts')
<script>
document.addEventListener("DOMContentLoaded", function() {
    // Configurar Select2 para campos normales
    $('.select2-search').not('#match_select').select2({
        theme: 'bootstrap-5',
        width: '100%'
    });
    
    // Configurar Select2 con AJAX para partidos
    $('#match_select').select2({
        theme: 'bootstrap-5',
        width: '100%',
        placeholder: $(this).data('placeholder'),
        allowClear: true,
        ajax: {
            url: $(this).data('ajax-url'),
            dataType: 'json',
            delay: 300,
            data: function(params) {
                return {
                    q: params.term || '',
                    page: params.page || 1
                };
            },
            processResults: function(data, params) {
                params.page = params.page || 1;
                return {
                    results: data.results,
                    pagination: {
                        more: data.pagination.more
                    }
                };
            },
            cache: true
        },
        minimumInputLength: 1,
        templateResult: function(match) {
            if (match.loading) return 'Searching...';
            
            var $container = $(
                '<div class="match-option">' +
                    '<div><strong>' + match.text + '</strong></div>' +
                    '<small class="text-muted">' + match.home_team + ' vs ' + match.away_team + '</small>' +
                '</div>'
            );
            return $container;
        }
    });

    // Cuando se selecciona un partido, filtrar equipos disponibles
    $('#match_select').on('change', function() {
        var matchId = $(this).val();
        
        if (matchId) {
            $.ajax({
              // Línea 128 - Corrige esta línea:
url: "{{ route('api.match-lineups.match.info', ':id') }}".replace(':id', matchId),
                method: 'GET',
                success: function(data) {
                    // Habilitar o deshabilitar opciones del select de equipos
                    $('#team_select option').each(function() {
                        var $option = $(this);
                        var teamId = $option.val();
                        
                        if (teamId && teamId !== '' && 
                            teamId != data.home_team_id && 
                            teamId != data.away_team_id) {
                            $option.hide().prop('disabled', true);
                        } else {
                            $option.show().prop('disabled', false);
                        }
                    });
                    
                    // Actualizar Select2
                    $('#team_select').trigger('change.select2');
                },
                error: function() {
                    console.log('Error loading match info');
                }
            });
        } else {
            // Si no hay partido seleccionado, mostrar todos los equipos
            $('#team_select option').each(function() {
                $(this).show().prop('disabled', false);
            });
            $('#team_select').trigger('change.select2');
        }
    });
    
    // Cargar información del partido actual al editar
    @if($lineup->match_id)
        $.ajax({
            url: "{{ route('api.match-lineups.match.info', $lineup->match_id) }}",
            method: 'GET',
            success: function(data) {
                // Filtrar equipos disponibles
                $('#team_select option').each(function() {
                    var $option = $(this);
                    var teamId = $option.val();
                    
                    if (teamId && teamId !== '' && 
                        teamId != data.home_team_id && 
                        teamId != data.away_team_id) {
                        $option.hide().prop('disabled', true);
                    }
                });
                $('#team_select').trigger('change.select2');
            }
        });
    @endif
});
</script>
@endsection
@endsection