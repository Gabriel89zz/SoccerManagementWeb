<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Core\UserController;
use App\Http\Controllers\DashboardController;


use App\Http\Controllers\Organization\TeamController;
use App\Http\Controllers\Core\CountryController;
use App\Http\Controllers\Core\CityController;
use App\Http\Controllers\Core\PositionController;
use App\Http\Controllers\Core\FormationController;
use App\Http\Controllers\Core\AwardController;
use App\Http\Controllers\Core\EventTypeController;
use App\Http\Controllers\Core\InjuryTypeController;
use App\Http\Controllers\Core\SocialMediaPlatformController;
use App\Http\Controllers\Core\SponsorshipTypeController;

use App\Http\Controllers\Organization\AcademyController;
use App\Http\Controllers\Organization\AgencyController;
use App\Http\Controllers\Organization\ConfederationController;
use App\Http\Controllers\Organization\KitSponsorController;
use App\Http\Controllers\Organization\StadiumController;
use App\Http\Controllers\Organization\TeamKitController;
use App\Http\Controllers\Organization\TeamSocialMediaController;
use App\Http\Controllers\Organization\TeamSponsorshipController;
use App\Http\Controllers\Organization\SponsorController;

use App\Http\Controllers\People\AgentController;
use App\Http\Controllers\People\CoachController;
use App\Http\Controllers\People\PlayerController;
use App\Http\Controllers\People\RefereeController;
use App\Http\Controllers\People\ScoutController;
use App\Http\Controllers\People\StaffMemberController;

use App\Http\Controllers\Competition\CompetitionController;
use App\Http\Controllers\Competition\CompetitionSeasonController;
use App\Http\Controllers\Competition\CompetitionSeasonTeamController;
use App\Http\Controllers\Competition\CompetitionStageController;
use App\Http\Controllers\Competition\CompetitionTypeController;
use App\Http\Controllers\Competition\GroupController;
use App\Http\Controllers\Competition\SeasonController;

use App\Http\Controllers\Management\InjuryController;
use App\Http\Controllers\Management\ScoutingReportController;
use App\Http\Controllers\Management\SquadCoachController;
use App\Http\Controllers\Management\SquadController;
use App\Http\Controllers\Management\SquadMemberController;
use App\Http\Controllers\Management\SquadStaffController;
use App\Http\Controllers\Management\TransferHistoryController;

use App\Http\Controllers\MatchDay\CardController;
use App\Http\Controllers\MatchDay\FoulController;
use App\Http\Controllers\MatchDay\GoalController;
use App\Http\Controllers\MatchDay\LineupPlayerController;
use App\Http\Controllers\MatchDay\MatchEventController;
use App\Http\Controllers\MatchDay\MatchGameController;
use App\Http\Controllers\MatchDay\MatchOfficialController;
use App\Http\Controllers\MatchDay\ShotController;
use App\Http\Controllers\MatchDay\SubstitutionController;
use App\Http\Controllers\MatchDay\MatchLineupController;

use App\Http\Controllers\Stats\GroupStandingController;
use App\Http\Controllers\Stats\LeagueStandingController;
use App\Http\Controllers\Stats\PlayerAwardController;
use App\Http\Controllers\Stats\PlayerSeasonStatController;
use App\Http\Controllers\Stats\TeamAwardController;
use App\Http\Controllers\Stats\TeamMatchStatController;

use App\Http\Controllers\Reports\ReportController;

// 1. Ruta Raíz
Route::get('/', function () {
    // Si ya estoy logueado, vamos directo al Dashboard
    if (auth()->check()) {
        return redirect()->route('dashboard');
    }
    // Si no, al login
    return redirect()->route('login');
});

// 2. Rutas de Autenticación (Login, Logout, Registro)
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [RegisterController::class, 'register']);

// 3. Rutas Protegidas (Solo requieren estar logueado)
Route::middleware('auth')->group(function () {
    
    // Dashboard General
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    // --- MÓDULO ORGANIZATION ---
    Route::resource('teams', TeamController::class);
    Route::resource('academies', AcademyController::class);
    Route::resource('agencies', AgencyController::class);
    Route::resource('confederations', ConfederationController::class);
    Route::resource('kit-sponsors', KitSponsorController::class);
    Route::resource('stadiums', StadiumController::class);
    Route::resource('team-kits', TeamKitController::class);
    Route::resource('team-social-medias', TeamSocialMediaController::class);
    Route::resource('team-sponsorships', TeamSponsorshipController::class);
    Route::resource('sponsors', SponsorController::class);

    Route::get('/api/cities', [App\Http\Controllers\Core\CityController::class, 'search'])->name('api.cities');

    // --- MÓDULO CORE ---
    Route::resource('countries', CountryController::class);
    Route::resource('cities', CityController::class);
    Route::resource('positions', PositionController::class);
    Route::resource('formations', FormationController::class);
    Route::resource('awards', AwardController::class);
    Route::resource('event-types', EventTypeController::class);
    Route::resource('injury-types', InjuryTypeController::class);
    Route::resource('social-media-platforms', SocialMediaPlatformController::class);
    Route::resource('sponsorship-types', SponsorshipTypeController::class);
    Route::resource('users', UserController::class);

    // --- MÓDULO PEOPLE ---
    Route::resource('agents', AgentController::class);
    Route::resource('coaches', CoachController::class);
    Route::resource('players', PlayerController::class);
    Route::resource('referees', RefereeController::class);
    Route::resource('scouts', ScoutController::class);
    Route::resource('staff-members', StaffMemberController::class);
    
    // --- MÓDULO COMPETITION ---
    Route::resource('competitions', CompetitionController::class);
    Route::resource('competition-seasons', CompetitionSeasonController::class);
    Route::resource('competition-season-teams', CompetitionSeasonTeamController::class);
    Route::resource('competition-stages', CompetitionStageController::class);
    Route::resource('competition-types', CompetitionTypeController::class);
    Route::resource('groups', GroupController::class);
    Route::resource('seasons', SeasonController::class);

    // --- MÓDULO MANAGMENT ---
    Route::resource('injuries', InjuryController::class);
    Route::resource('scouting-reports', ScoutingReportController::class);
    Route::resource('squad-coaches', SquadCoachController::class);
    Route::resource('squads', SquadController::class);
    Route::resource('squad-members', SquadMemberController::class);
    Route::resource('squad-staff', SquadStaffController::class);
    Route::resource('transfer-histories', TransferHistoryController::class);


    // --- MÓDULO MATCHDAY ---
    Route::resource('cards', CardController::class);
    Route::resource('fouls', FoulController::class);
    Route::resource('goals', GoalController::class);
    Route::resource('lineup-players', LineupPlayerController::class);
    Route::resource('match-events', MatchEventController::class);
    Route::resource('matches', MatchGameController::class);
    Route::resource('match-officials', MatchOfficialController::class);
    Route::resource('shots', ShotController::class);
    Route::resource('substitutions', SubstitutionController::class);
    Route::resource('match-lineups', MatchLineupController::class);
    // Rutas para búsqueda AJAX de jugadores
    Route::get('/api/players/search', [LineupPlayerController::class, 'searchPlayers'])->name('api.players.search');


    // --- MÓDULO STAT ---
    Route::resource('group-standings', GroupStandingController::class);
    Route::resource('league-standings', LeagueStandingController::class);
    Route::resource('player-awards', PlayerAwardController::class);
    Route::resource('player-season-stats', PlayerSeasonStatController::class);
    Route::resource('team-awards', TeamAwardController::class);
    Route::resource('team-match-stats', TeamMatchStatController::class);



    // Rutas para búsqueda AJAX de jugadores (si ya tienes una, puedes reutilizarla)
Route::get('/api/goals/players/search', [GoalController::class, 'searchPlayers'])->name('api.goals.players.search');
// Ruta opcional para filtrar por equipo
Route::get('/api/goals/players/by-team', [GoalController::class, 'searchPlayersByTeam'])->name('api.goals.players.by-team');

Route::get('/api/cards/players/search', [\App\Http\Controllers\MatchDay\CardController::class, 'searchPlayers'])->name('api.cards.players.search');

Route::get('/api/fouls/players/search', [FoulController::class, 'searchPlayers'])->name('api.fouls.players.search');

    // Ruta general para búsqueda de jugadores (reutilizable)
Route::get('/api/players/search', [\App\Http\Controllers\People\PlayerController::class, 'searchPlayers'])
    ->name('api.players.search');

// Rutas para búsqueda AJAX de jugadores (para Shots)
Route::get('/api/shots/players/search', [ShotController::class, 'searchPlayers'])
    ->name('api.shots.players.search');

Route::get('/api/substitutions/players/search', [SubstitutionController::class, 'searchPlayers'])
    ->name('api.substitutions.players.search');

Route::get('/api/match-events/players/search', [MatchEventController::class, 'searchPlayers'])
    ->name('api.match-events.players.search');

    Route::get('/api/match-lineups/matches/search', [MatchLineupController::class, 'searchMatches'])->name('api.match-lineups.matches.search');
Route::get('/api/match-lineups/match/{id}', [MatchLineupController::class, 'getMatchInfo'])->name('api.match-lineups.match.info');

Route::get('/api/lineups/search', [LineupPlayerController::class, 'searchLineups'])->name('api.lineups.search');

Route::get('/api/matches/search', [App\Http\Controllers\MatchDay\GoalController::class, 'searchMatches'])->name('api.matches.search');

Route::get('/api/cards/matches/search', [App\Http\Controllers\MatchDay\CardController::class, 'searchMatches'])->name('api.cards.matches.search');
Route::get('/api/fouls/matches/search', [App\Http\Controllers\MatchDay\FoulController::class, 'searchMatches'])->name('api.fouls.matches.search');
Route::get('/api/shots/matches/search', [App\Http\Controllers\MatchDay\ShotController::class, 'searchMatches'])->name('api.shots.matches.search');
Route::get('/api/substitutions/matches/search', [App\Http\Controllers\MatchDay\SubstitutionController::class, 'searchMatches'])->name('api.substitutions.matches.search');
Route::get('/api/match-events/matches/search', [App\Http\Controllers\MatchDay\MatchEventController::class, 'searchMatches'])->name('api.match-events.matches.search');
Route::get('/api/match-officials/matches/search', [App\Http\Controllers\MatchDay\MatchOfficialController::class, 'searchMatches'])->name('api.match-officials.matches.search');

Route::get('/api/injuries/matches/search', [App\Http\Controllers\Management\InjuryController::class, 'searchMatches'])->name('api.injuries.matches.search');
Route::get('/api/injuries/players/search', [App\Http\Controllers\Management\InjuryController::class, 'searchPlayers'])->name('api.injuries.players.search');

Route::get('/api/squad-members/players/search', [App\Http\Controllers\Management\SquadMemberController::class, 'searchPlayers'])->name('api.squad-members.players.search');

Route::get('/api/transfer-histories/players/search', [App\Http\Controllers\Management\TransferHistoryController::class, 'searchPlayers'])->name('api.transfers.players.search');
Route::get('/api/transfer-histories/teams/search', [App\Http\Controllers\Management\TransferHistoryController::class, 'searchTeams'])->name('api.transfers.teams.search');

Route::get('/api/scouting-reports/players/search', [App\Http\Controllers\Management\ScoutingReportController::class, 'searchPlayers'])->name('api.scouting.players.search');
Route::get('/api/scouting-reports/matches/search', [App\Http\Controllers\Management\ScoutingReportController::class, 'searchMatches'])->name('api.scouting.matches.search');

Route::get('/api/player-awards/players/search', [App\Http\Controllers\Stats\PlayerAwardController::class, 'searchPlayers'])->name('api.player-awards.players.search');

Route::get('/api/player-season-stats/players/search', [App\Http\Controllers\Stats\PlayerSeasonStatController::class, 'searchPlayers'])->name('api.season-stats.players.search');

Route::get('/api/team-match-stats/matches/search', [App\Http\Controllers\Stats\TeamMatchStatController::class, 'searchMatches'])->name('api.team-match-stats.matches.search');

// --- RUTAS ESPECÍFICAS PARA LINEUP PLAYER (Para evitar conflictos) ---
Route::get('/api/lineup-players/specific/players', [LineupPlayerController::class, 'searchPlayers'])
     ->name('api.lineup-players.specific.players');

Route::get('/api/lineup-players/specific/lineups', [LineupPlayerController::class, 'searchLineups'])
     ->name('api.lineup-players.specific.lineups');


Route::prefix('reports')->name('reports.')->group(function () {
    Route::get('/', [ReportController::class, 'index'])->name('index');
    Route::get('/{key}', [ReportController::class, 'show'])->name('show');
    Route::get('/{key}/export', [ReportController::class, 'export'])->name('export');
});
});