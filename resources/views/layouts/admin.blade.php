<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Soccer Manager Admin</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Select2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />

    <style>
        body {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            background-color: #f4f6f9;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .wrapper {
            display: flex;
            flex: 1;
            width: 100%;
        }

        /* ESTILOS DEL SIDEBAR */
        #sidebar {
            min-width: 260px;
            max-width: 260px;
            background: #212529;
            color: #bbaaaa;
            transition: all 0.3s;
        }

        #sidebar .sidebar-header {
            padding: 20px;
            background: #1a1d20;
            border-bottom: 1px solid #4b545c;
        }

        #sidebar .nav-link {
            color: #c2c7d0;
            padding: 12px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
            text-decoration: none;
        }

        #sidebar .nav-link:hover,
        #sidebar .nav-link[aria-expanded="true"] {
            color: #fff;
            background: rgba(255, 255, 255, 0.1);
        }

        #sidebar .nav-link i.fa-chevron-down {
            font-size: 0.75rem;
            transition: transform 0.3s;
        }

        #sidebar .nav-link[aria-expanded="true"] i.fa-chevron-down {
            transform: rotate(180deg);
        }

        /* SUBMENUS (Las tablas dentro de cada módulo) */
        .submenu {
            background: #2c3034;
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .submenu .nav-item {
            width: 100%;
        }

        .submenu .nav-link {
            padding-left: 45px;
            font-size: 0.9rem;
            background: transparent;
            border: none;
        }

        .submenu .nav-link:hover {
            color: #3498db;
        }

        .submenu .nav-link i {
            font-size: 0.8rem;
            margin-right: 10px;
            opacity: 0.7;
        }

        /* CONTENIDO PRINCIPAL */
        #content {
            width: 100%;
            padding: 20px;
            overflow-y: auto;
        }

        .pagination {
            justify-content: center;
            margin-top: 20px;
        }
    </style>
</head>

<body>

    <!-- NAVBAR SUPERIOR -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark border-bottom border-secondary">
        <div class="container-fluid">
            <a class="navbar-brand fw-bold" href="{{ route('dashboard') }}">
                <i class="fas fa-futbol me-2"></i> SOCCER MANAGER
            </a>
            <div class="d-flex align-items-center">
                <span class="text-light me-3 small">
                    User: <strong>{{ Auth::user()->first_name ?? 'Guest' }}</strong>
                </span>
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-danger btn-sm rounded-pill px-3">
                        <i class="fas fa-sign-out-alt"></i> Logout
                    </button>
                </form>
            </div>
        </div>
    </nav>

    <div class="wrapper">
        <!-- SIDEBAR ACORDEÓN -->
        <nav id="sidebar">
            <div class="sidebar-content">

                <!-- Dashboard -->
                <a href="{{ route('dashboard') }}" class="nav-link">
                    <span><i class="fas fa-tachometer-alt me-2 text-warning"></i> Dashboard</span>
                </a>

                <!-- MODULE: SECURITY (SOLO ADMIN) -->
                @if(Auth::check() && Auth::user()->isAdmin())
                    <a href="#menuSecurity" data-bs-toggle="collapse" class="nav-link">
                        <span><i class="fas fa-lock me-2 text-danger"></i> Security</span>
                        <i class="fas fa-chevron-down"></i>
                    </a>
                    <div class="collapse" id="menuSecurity">
                        <ul class="submenu">
                            <li class="nav-item">
                                <a href="{{ route('users.index') }}" class="nav-link"><i class="far fa-circle"></i> Users &
                                    Audit</a>
                            </li>
                        </ul>
                    </div>
                @endif

                <!-- 1. CORE / CONFIG -->
                <a href="#menuCore" data-bs-toggle="collapse" class="nav-link">
                    <span><i class="fas fa-cogs me-2 text-info"></i> Core / Config</span>
                    <i class="fas fa-chevron-down"></i>
                </a>
                <div class="collapse" id="menuCore"> <!-- 'show' lo deja abierto al inicio -->
                    <ul class="submenu">
                        <li class="nav-item"><a href="{{ route('countries.index') }}" class="nav-link"><i
                                    class="far fa-circle"></i> Countries</a></li>
                        <li class="nav-item"><a href="{{ route('cities.index') }}" class="nav-link"><i
                                    class="far fa-circle"></i> Cities</a></li>
                        <li class="nav-item"><a href="{{ route('positions.index') }}" class="nav-link"><i
                                    class="far fa-circle"></i> Positions</a></li>
                        <li class="nav-item"><a href="{{ route('formations.index') }}" class="nav-link"><i
                                    class="far fa-circle"></i> Formations</a></li>
                        <li class="nav-item"><a href="{{ route('awards.index') }}" class="nav-link"><i
                                    class="far fa-circle"></i> Awards</a></li>
                        <li class="nav-item"><a href="{{ route('event-types.index') }}" class="nav-link"><i
                                    class="far fa-circle"></i> Event Types</a></li>
                        <li class="nav-item"><a href="{{ route('injury-types.index') }}" class="nav-link"><i
                                    class="far fa-circle"></i> Injury Types</a></li>
                        <li class="nav-item"><a href="{{ route('social-media-platforms.index') }}" class="nav-link"><i
                                    class="far fa-circle"></i> Social Media Platforms</a></li>
                        <li class="nav-item"><a href="{{ route('sponsorship-types.index') }}" class="nav-link"><i
                                    class="far fa-circle"></i> Sponsorship Types</a></li>
                    </ul>
                </div>

                <!-- 2. ORGANIZATION -->
                <a href="#menuOrg" data-bs-toggle="collapse" class="nav-link">
                    <span><i class="fas fa-building me-2 text-primary"></i> Organization</span>
                    <i class="fas fa-chevron-down"></i>
                </a>
                <div class="collapse" id="menuOrg">
                    <ul class="submenu">
                        <li class="nav-item">
                            <a href="{{ route('teams.index') }}" class="nav-link">
                                <i class="far fa-circle"></i> Teams
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('stadiums.index') }}" class="nav-link">
                                <i class="far fa-circle"></i> Stadiums
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('academies.index') }}" class="nav-link">
                                <i class="far fa-circle"></i> Academies
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('agencies.index') }}" class="nav-link">
                                <i class="far fa-circle"></i> Agencies
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('sponsors.index') }}" class="nav-link">
                                <i class="far fa-circle"></i> Sponsors
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('confederations.index') }}" class="nav-link">
                                <i class="far fa-circle"></i> Confederations
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('kit-sponsors.index') }}" class="nav-link">
                                <i class="far fa-circle"></i> Kit Sponsors
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('team-kits.index') }}" class="nav-link">
                                <i class="far fa-circle"></i> Team Kits
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('team-social-medias.index') }}" class="nav-link">
                                <i class="far fa-circle"></i> Team Social Media
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('team-sponsorships.index') }}" class="nav-link">
                                <i class="far fa-circle"></i> Team Sponsorships
                            </a>
                        </li>
                    </ul>
                </div>

                <!-- 3. PEOPLE -->
                <a href="#menuPeople" data-bs-toggle="collapse" class="nav-link">
                    <span><i class="fas fa-users me-2 text-success"></i> People</span>
                    <i class="fas fa-chevron-down"></i>
                </a>
                <div class="collapse" id="menuPeople">
                    <ul class="submenu">
                        <li class="nav-item">
                            <a href="{{ route('players.index') }}" class="nav-link">
                                <i class="far fa-circle"></i> Players
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('coaches.index') }}" class="nav-link">
                                <i class="far fa-circle"></i> Coaches
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('referees.index') }}" class="nav-link">
                                <i class="far fa-circle"></i> Referees
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('agents.index') }}" class="nav-link">
                                <i class="far fa-circle"></i> Agents
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('scouts.index') }}" class="nav-link">
                                <i class="far fa-circle"></i> Scouts
                            </a>
                        </li>

                    </ul>
                </div>

                <!-- 4. COMPETITION -->
                <a href="#menuComp" data-bs-toggle="collapse" class="nav-link">
                    <span><i class="fas fa-trophy me-2 text-warning"></i> Competition</span>
                    <i class="fas fa-chevron-down"></i>
                </a>
                <div class="collapse" id="menuComp">
                    <ul class="submenu">
                        <li class="nav-item">
                            <a href="{{ route('competitions.index') }}" class="nav-link">
                                <i class="far fa-circle"></i> Competitions
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('competition-types.index') }}" class="nav-link">
                                <i class="far fa-circle"></i> Competition Types
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('seasons.index') }}" class="nav-link">
                                <i class="far fa-circle"></i> Seasons
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('competition-seasons.index') }}" class="nav-link">
                                <i class="far fa-circle"></i> Competition Seasons
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('competition-season-teams.index') }}" class="nav-link">
                                <i class="far fa-circle"></i> Season Teams
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('competition-stages.index') }}" class="nav-link">
                                <i class="far fa-circle"></i> Competition Stages
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('groups.index') }}" class="nav-link">
                                <i class="far fa-circle"></i> Groups
                            </a>
                        </li>
                    </ul>
                </div>

                <!-- 5. MATCHDAY -->
                <a href="#menuMatch" data-bs-toggle="collapse" class="nav-link">
                    <span><i class="fas fa-calendar-day me-2 text-danger"></i> MatchDay</span>
                    <i class="fas fa-chevron-down"></i>
                </a>
                <div class="collapse" id="menuMatch">
                    <ul class="submenu">
                        <li class="nav-item"><a href="{{ route('matches.index') }}" class="nav-link text"><i
                                    class="far fa-circle"></i> Matches</a></li>
                        <li class="nav-item"><a href="{{ route('match-lineups.index') }}" class="nav-link text"><i
                                    class="far fa-circle"></i> Match Lineups</a></li>
                        <li class="nav-item"><a href="{{ route('lineup-players.index') }}" class="nav-link text"><i
                                    class="far fa-circle"></i> Lineups</a></li>
                        <li class="nav-item"><a href="{{ route('goals.index') }}" class="nav-link text"><i
                                    class="far fa-circle"></i> Goals</a></li>
                        <li class="nav-item"><a href="{{ route('cards.index') }}" class="nav-link text"><i
                                    class="far fa-circle"></i> Cards</a></li>
                        <li class="nav-item"><a href="{{ route('fouls.index') }}" class="nav-link text"><i
                                    class="far fa-circle"></i> Fouls</a></li>
                        <li class="nav-item"><a href="{{ route('shots.index') }}" class="nav-link textuted"><i
                                    class="far fa-circle"></i> Shots</a></li>
                        <li class="nav-item"><a href="{{ route('substitutions.index') }}" class="nav-link text"><i
                                    class="far fa-circle"></i> Substitutions</a></li>
                        <li class="nav-item"><a href="{{ route('match-events.index') }}" class="nav-link text"><i
                                    class="far fa-circle"></i> Match Events</a></li>
                        <li class="nav-item"><a href="{{ route('match-officials.index') }}" class="nav-link text"><i
                                    class="far fa-circle"></i> Match Officials</a></li>
                    </ul>
                </div>

                <!-- 6. MANAGEMENT -->
                <a href="#menuMgmt" data-bs-toggle="collapse" class="nav-link">
                    <span><i class="fas fa-briefcase me-2 text-secondary"></i> Management</span>
                    <i class="fas fa-chevron-down"></i>
                </a>
                <div class="collapse" id="menuMgmt">
                    <ul class="submenu">
                        <li class="nav-item"><a href="{{ route('squads.index') }}" class="nav-link text"><i
                                    class="far fa-circle"></i> Squads</a></li>
                        <li class="nav-item"><a href="{{ route('squad-members.index') }}" class="nav-link text"><i
                                    class="far fa-circle"></i> Squad Members</a></li>

                        <li class="nav-item"><a href="{{ route('squad-coaches.index') }}" class="nav-link text"><i
                                    class="far fa-circle"></i> Squad Coaches</a></li>
                        <li class="nav-item"><a href="{{ route('injuries.index') }}" class="nav-link text"><i
                                    class="far fa-circle"></i> Injuries</a></li>
                        <li class="nav-item"><a href="{{ route('transfer-histories.index') }}" class="nav-link text"><i
                                    class="far fa-circle"></i> Transfers History</a></li>
                        <li class="nav-item"><a href="{{ route('scouting-reports.index') }}" class="nav-link text"><i
                                    class="far fa-circle"></i> Scouting Reports</a></li>

                    </ul>
                </div>


                <!-- 7. STATS & STANDINGS -->
                <a href="#menuStats" data-bs-toggle="collapse" class="nav-link">
                    <span><i class="fas fa-chart-bar me-2 text-success"></i> Stats</span>
                    <i class="fas fa-chevron-down"></i>
                </a>
                <div class="collapse" id="menuStats">
                    <ul class="submenu">
                        <li class="nav-item"><a href="{{ route('league-standings.index') }}" class="nav-link text"><i
                                    class="far fa-circle"></i> League Standings</a></li>
                        <li class="nav-item"><a href="{{ route('player-awards.index') }}" class="nav-link text"><i
                                    class="far fa-circle"></i> Player Awards</a></li>
                        <li class="nav-item"><a href="{{ route('player-season-stats.index') }}" class="nav-link text"><i
                                    class="far fa-circle"></i> Player Season Stats</a></li>
                        <li class="nav-item"><a href="{{ route('team-awards.index') }}" class="nav-link text"><i
                                    class="far fa-circle"></i> Team Awards</a></li>
                        <li class="nav-item"><a href="{{ route('team-match-stats.index') }}" class="nav-link text"><i
                                    class="far fa-circle"></i> Team Match Stats</a></li>
                    </ul>
                </div>

                <!-- 8. ANALYTICS (NUEVO) -->
                <a href="{{ route('reports.index') }}" class="nav-link">
                    <span><i class="fas fa-chart-pie me-2 text-info"></i> Analytics</span>
                </a>

            </div>
        </nav>

        <!-- CONTENT -->
        <div id="content">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @yield('content')
        </div>
    </div>

    <!-- SCRIPTS OBLIGATORIOS PARA EL FUNCIONAMIENTO -->
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <!-- Bootstrap Bundle incluye Popper (Necesario para desplegables y collapse) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Select2 -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script>
        $(document).ready(function () {
            // Activar buscador dinámico en selects
            $('.select2-search').select2({
                theme: 'bootstrap-5',
                width: '100%',
                placeholder: $(this).data('placeholder')
            });
        });
    </script>

</body>

</html>