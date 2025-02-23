<?php
/**************************************************************
 * index.php – Kalendarz wyścigowy 2025 (wersja stabilna)
 **************************************************************/

$dsn  = 'mysql:host=localhost;dbname=calendar_db;charset=utf8mb4';
$user = 'root';
$pass = '';

try {
    $pdo = new PDO($dsn, $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
} catch(Exception $e) {
    die("Błąd połączenia z bazą: " . $e->getMessage());
}

$seasonId = 1;
$seasonStart = '2025-01-01';
$seasonEnd   = '2025-12-31';
$yearStartTs = strtotime($seasonStart);
$yearEndTs   = strtotime($seasonEnd);

$sqlRounds = "
    SELECT 
      r.id AS round_id,
      r.name AS round_name,
      r.start_date,
      r.end_date,
      r.weight AS round_weight,
      
      ch.id AS championship_id,
      ch.name AS championship_name,
      ch.shortcut AS championship_shortcut,
      ch.color AS championship_color,
      ch.text_color AS championship_text_color,
      ch.weight AS championship_weight,
      
      s.id AS series_id,
      s.name AS series_name,
      s.color AS series_color,
      s.text_color AS series_text_color,
      s.weight AS series_weight
    FROM rounds r
    JOIN championships ch ON r.championship_id = ch.id
    JOIN series_championship sc ON ch.id = sc.championship_id
    JOIN series s ON sc.series_id = s.id
    WHERE r.season_id = :season_id 
      AND r.status = 1 
      AND ch.status = 1 
      AND s.status = 1
      AND r.start_date <= :season_end
      AND r.end_date >= :season_start
    ORDER BY r.start_date ASC, s.weight ASC, ch.weight ASC
";
$stmtRounds = $pdo->prepare($sqlRounds);
$stmtRounds->execute([
    ':season_id'    => $seasonId,
    ':season_start' => $seasonStart,
    ':season_end'   => $seasonEnd
]);
$rounds = $stmtRounds->fetchAll(PDO::FETCH_ASSOC);

$eventsByDay = [];
for($ts = $yearStartTs; $ts <= $yearEndTs; $ts += 86400){
    $dayStr = date('Y-m-d', $ts);
    $eventsByDay[$dayStr] = [];
}

foreach ($rounds as $row) {
    $startTs = strtotime($row['start_date']);
    $endTs   = strtotime($row['end_date']);
    if($startTs < $yearStartTs) $startTs = $yearStartTs;
    if($endTs > $yearEndTs) $endTs = $yearEndTs;
    for($t = $startTs; $t <= $endTs; $t += 86400){
        $dayStr = date('Y-m-d', $t);
        $eventsByDay[$dayStr][] = [
            'round_id'                => $row['round_id'],
            'round_name'              => $row['round_name'],
            'series_id'               => $row['series_id'],
            'series_name'             => $row['series_name'],
            'series_color'            => $row['series_color'],
            'series_text_color'       => $row['series_text_color'],
            'championship_id'         => $row['championship_id'],
            'championship_name'       => $row['championship_name'],
            'championship_shortcut'   => $row['championship_shortcut'],
            'championship_color'      => $row['championship_color'],
            'championship_text_color' => $row['championship_text_color'],
            'order_weight'            => $row['series_weight'] * 100 + $row['championship_weight']
        ];
    }
}
foreach($eventsByDay as $day => &$events) {
    usort($events, function($a, $b) {
        return $a['order_weight'] <=> $b['order_weight'];
    });
}
unset($events);

$stmtSeries = $pdo->query("SELECT * FROM series WHERE status = 1 ORDER BY weight ASC");
$seriesList = $stmtSeries->fetchAll(PDO::FETCH_ASSOC);

$filterSeries = [];
foreach ($seriesList as $s) {
    $filterSeries[$s['id']] = [
        'series' => $s,
        'championships' => []
    ];
}
$sqlChamps = "
    SELECT ch.*, sc.series_id 
    FROM championships ch
    JOIN series_championship sc ON ch.id = sc.championship_id
    WHERE ch.status = 1
    ORDER BY ch.weight ASC
";
$stmtChamps = $pdo->query($sqlChamps);
while ($ch = $stmtChamps->fetch(PDO::FETCH_ASSOC)) {
    $seriesId = $ch['series_id'];
    if(isset($filterSeries[$seriesId])){
        $filterSeries[$seriesId]['championships'][$ch['id']] = $ch;
    }
}

$stmtDrivers = $pdo->query("SELECT id, firstname, lastname, color, weight FROM drivers WHERE status = 1 ORDER BY weight ASC");
$driversList = $stmtDrivers->fetchAll(PDO::FETCH_ASSOC);

$stmtTeams = $pdo->query("SELECT id, name, color, weight FROM teams WHERE status = 1 ORDER BY weight ASC");
$teamsList = $stmtTeams->fetchAll(PDO::FETCH_ASSOC);

$driverRoundsMap = [];
$stmtDriverRounds = $pdo->query("SELECT driver_id, round_id FROM driver_round");
while($row = $stmtDriverRounds->fetch(PDO::FETCH_ASSOC)){
    $driverRoundsMap[$row['driver_id']][] = $row['round_id'];
}

$teamRoundsMap = [];
$stmtTeamRounds = $pdo->query("SELECT team_id, round_id FROM team_round");
while($row = $stmtTeamRounds->fetch(PDO::FETCH_ASSOC)){
    $teamRoundsMap[$row['team_id']][] = $row['round_id'];
}

$weekdaysFull = ['Poniedziałek','Wtorek','Środa','Czwartek','Piątek','Sobota','Niedziela'];
$monthNamesFull = [
  1=>"Styczeń",2=>"Luty",3=>"Marzec",4=>"Kwiecień",5=>"Maj",6=>"Czerwiec",
  7=>"Lipiec",8=>"Sierpień",9=>"Wrzesień",10=>"Październik",11=>"Listopad",12=>"Grudzień"
];
?>
<!DOCTYPE html>
<html lang="pl">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Kalendarz wyścigowy 2025</title>
  <style>
    /* Ukrycie paska przewijania */
    ::-webkit-scrollbar { display: none; }
    body { -ms-overflow-style: none; scrollbar-width: none; }

    /* Podstawowe style */
    html, body {
      width: 100%; max-width: 100%; margin: 0; padding: 0;
      background: #023E42; color: #fff; font-family: Arial, sans-serif;
      min-height: 100vh; display: flex; flex-direction: column;
    }
    header { background-color: #023E42; padding: 10px; text-align: center; }
    header h1 { font-size: 24px; margin: 0 auto; }
    
    .main-content { flex: 1; display: flex; width: 100%; }
    .calendar-container { flex: 1; order: 1; width: 100%; padding: 40px 60px; position: relative; }
    .calendar-wrapper { display: flex; flex-direction: row; align-items: flex-start; justify-content: space-between; width: 100%; }
    .calendar-center { flex: 1; }
    body:not(.single-month-active) .calendar-left,
    body:not(.single-month-active) .calendar-right { display: none; }
    .calendar-left, .calendar-right { display: flex; align-items: center; margin-top: 350px; }
    
    .month, #filtersPanel { background-color: #023E42; }
    
    body:not(.single-month-active) .nav-back-container { display: none; }
    .nav-back-container { margin-top: 10px; text-align: center; }
    .nav-back-container button {
      background: rgba(0, 0, 0, 0.7); color: #fff; border: none; border-radius: 4px; padding: 8px 16px;
      cursor: pointer; transition: background 0.2s, transform 0.2s;
    }
    .nav-back-container button:hover { background: rgba(0, 0, 0, 0.9); transform: scale(1.05); }
    
    .nav-button {
      background: rgba(0, 0, 0, 0.7); color: #fff; border: none; border-radius: 4px;
      width: 50px; height: 40px; cursor: pointer; transition: background 0.2s, transform 0.2s;
    }
    .nav-button:hover { background: rgba(0, 0, 0, 0.9); transform: scale(1.05); }
    .nav-icon { width: 16px; height: 16px; fill: #fff; }
    
    .weekdays { display: grid; grid-template-columns: repeat(7, 1fr); margin-bottom: 4px; }
    .weekdays div { text-align: center; font-weight: bold; font-size: 12px; color: #eee; }
    .days { display: grid; grid-template-columns: repeat(7, 1fr); }
    .day {
      background: #023E42; position: relative; min-height: 80px; border: 1px solid #fff;
      overflow: hidden; transition: background 0.3s;
    }
    .day:hover { background: #035765; }
    .day-number { position: absolute; top: 5px; left: 5px; font-size: 13px; font-weight: bold; }
    .event-bar {
      width: 100%; font-size: 12px; font-weight: bold; line-height: 14px; border-radius: 3px;
      margin-bottom: 2px; height: 20px; text-align: center; position: relative;
      overflow: hidden; white-space: nowrap; text-overflow: ellipsis; cursor: default;
    }
    .event-bar:hover::after {
      content: attr(data-round); position: absolute; bottom: 100%; left: 50%;
      transform: translate(-50%, -3px); background: #fff; color: #000; font-size: 10px;
      padding: 2px 5px; border-radius: 3px; pointer-events: none; z-index: 999;
    }
    
    /* Filtry – powiększone marginesy, czcionka i checkboxy */
    .filters-title { font-size: 16px; font-weight: bold; margin-bottom: 4px; }
    .filter-item { display: flex; align-items: center; margin-bottom: 8px; cursor: pointer; }
    .custom-checkbox {
      width: 20px; height: 20px; border-radius: 3px; background-color: #fff; margin-right: 8px;
      transition: background-color 0.2s;
    }
    .filter-name { font-size: 14px; }
    .filter-buttons { margin-top: 4px; display: flex; gap: 6px; }
    .filter-btn {
      background-color: #fff; color: #023E42; border: none; border-radius: 3px;
      padding: 6px 12px; cursor: pointer; font-size: 14px; transition: opacity 0.2s;
    }
    .filter-btn:hover { opacity: 0.8; }
    .filter-group { font-weight: bold; text-transform: uppercase; margin-top: 10px; }
    .subcategory { margin-left: 15px; }
    .toggle-row { display: flex; align-items: center; gap: 8px; margin-bottom: 10px; }
    .toggle-switch {
      width: 40px; height: 20px; background-color: #00A1A7; border-radius: 10px; position: relative;
      cursor: pointer; transition: background-color 0.2s;
    }
    .toggle-knob {
      width: 16px; height: 16px; background-color: #fff; border-radius: 50%;
      position: absolute; top: 2px; left: 2px; transition: left 0.2s;
    }
    .toggle-switch.active .toggle-knob { left: 22px; }
    .toggle-label { font-size: 12px; }
    
    #filtersPanel {
      width: 320px; background: #023E42; padding: 20px; overflow-y: auto; flex-shrink: 0; order: 2;
    }
    /* Przycisk zapisu preferencji */
    #savePreferencesContainer { margin-top: 20px; text-align: center; }
    #btnSavePreferences {
      background-color: #00A1A7; color: #fff; border: none; border-radius: 4px;
      padding: 8px 16px; font-size: 14px; cursor: pointer; transition: background 0.2s, transform 0.2s;
    }
    #btnSavePreferences:hover { background-color: #008f94; transform: scale(1.03); }
    
    /* Pop-alert – animowany komunikat zapisu */
    #popAlert {
      position: fixed; bottom: 20px; left: 50%; transform: translateX(-50%);
      background-color: rgba(0,0,0,0.8); color: #fff; padding: 10px 20px; border-radius: 4px;
      font-size: 14px; opacity: 0; pointer-events: none; transition: opacity 0.5s;
    }
    
    /* Układ kalendarza 12-miesięcznego – gdy brak klasy single-month-active */
    body:not(.single-month-active) .calendar-grid {
      display: grid; gap: 15px; grid-template-columns: repeat(3, 1fr);
    }
    @media (max-width: 1720px) {
      body:not(.single-month-active) .calendar-grid {
        grid-template-columns: repeat(2, 1fr);
      }
    }
    @media (max-width: 1400px) {
      body:not(.single-month-active) .calendar-grid {
        grid-template-columns: 1fr;
      }
    }
    .calendar-grid { display: grid; gap: 15px; grid-template-columns: repeat(3, 1fr); }
    .month {
      background-color: #023E42; padding: 10px; display: flex; flex-direction: column;
      border-radius: 6px; position: relative;
    }
    .month.single-month { grid-column: 1 / -1; background-color: #023E42; }
    .month-label { text-align: center; font-size: 18px; font-weight: bold; margin-bottom: 8px; }
    
    footer { background-color: #023E42; text-align: center; padding: 10px; color: #fff; }
  </style>
</head>
<body>
<header>
  <h1>Kalendarz wyścigowy 2025</h1>
</header>
 
<div class="main-content">
  <div class="calendar-container" id="calendarContainer">
    <div class="calendar-wrapper">
      <div class="calendar-left">
        <button class="nav-button" id="btnMonthPrev">
          <svg class="nav-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16">
            <path d="M10 12L4 8l6-4v8z"/>
          </svg>
        </button>
      </div>
      <div class="calendar-center">
        <div class="calendar-grid" id="calendarGrid">
          <?php 
          for($m = 1; $m <= 12; $m++){
              $daysInMonth = cal_days_in_month(CAL_GREGORIAN, $m, 2025);
              $firstTime   = strtotime("2025-$m-01");
              $firstDOW    = date('N', $firstTime);
              $emptyBefore = $firstDOW - 1;
              echo '<div class="month" data-month="'.$m.'" data-month-full="'.$monthNamesFull[$m].'">';
                  echo '<div class="month-label">'.$monthNamesFull[$m].'</div>';
                  echo '<div class="weekdays">';
                  foreach($weekdaysFull as $wd){
                      echo '<div>'.htmlspecialchars($wd).'</div>';
                  }
                  echo '</div>';
                  echo '<div class="days">';
                  for($i=0; $i<$emptyBefore; $i++){
                      echo '<div class="day"></div>';
                  }
                  for($d=1; $d<=$daysInMonth; $d++){
                      $dateStr = sprintf("2025-%02d-%02d", $m, $d);
                      echo '<div class="day" data-date="'.$dateStr.'">';
                          echo '<div class="day-number">'.$d.'</div>';
                          if(!empty($eventsByDay[$dateStr])){
                              echo '<div class="events-container">';
                              foreach($eventsByDay[$dateStr] as $evt){
                                  echo '<div class="event-bar" '
                                       .'data-round-id="'.$evt['round_id'].'" '
                                       .'data-series-id="'.$evt['series_id'].'" '
                                       .'data-championship-id="'.$evt['championship_id'].'" '
                                       .'data-round="'.htmlspecialchars($evt['round_name']).'" '
                                       .'data-championship-name="'.htmlspecialchars($evt['championship_name']).'" '
                                       .'style="background-color:'.$evt['championship_color'].';color:'.$evt['championship_text_color'].';">'
                                       .htmlspecialchars($evt['championship_shortcut'])
                                       .'</div>';
                              }
                              echo '</div>';
                          }
                      echo '</div>';
                  }
                  $usedCells = $emptyBefore + $daysInMonth;
                  $rest = 42 - $usedCells;
                  for($i=0; $i<$rest; $i++){
                      echo '<div class="day"></div>';
                  }
                  echo '</div>';
              echo '</div>';
          }
          ?>
        </div>
      </div>
      <div class="calendar-right">
        <button class="nav-button" id="btnMonthNext">
          <svg class="nav-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16">
            <path d="M6 4l6 4-6 4V4z"/>
          </svg>
        </button>
      </div>
    </div>
    <div class="nav-back-container" id="navBackContainer">
      <button id="btnMonthBack">
        <svg class="nav-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16">
          <path d="M4 6l4 4 4-4H4z"/>
        </svg>
      </button>
    </div>
  </div>
 
  <div id="filtersPanel">
    <div class="toggle-row">
      <div id="toggleSwitch" class="toggle-switch">
        <div class="toggle-knob"></div>
      </div>
      <div id="toggleLabel" class="toggle-label">Tryb: Serie/Mistrzostwa</div>
    </div>
 
    <div id="filtersSeries">
      <div class="filters-title">Serie / Mistrzostwa</div>
      <?php 
      foreach($filterSeries as $sData):
          $series = $sData['series'];
          $champs = $sData['championships'];
          $seriesColor = htmlspecialchars($series['color']);
          $seriesText = htmlspecialchars($series['text_color']);
          if(count($champs) <= 1):
      ?>
          <div class="filter-item" data-filterscope="series" data-series-id="<?php echo $series['id']; ?>" data-series-color="<?php echo $seriesColor; ?>" data-series-text="<?php echo $seriesText; ?>">
            <div class="custom-checkbox checked" style="background-color: <?php echo $seriesColor; ?>;"></div>
            <div class="filter-name"><?php echo htmlspecialchars($series['name']); ?></div>
          </div>
      <?php else: ?>
          <div class="filter-item filter-group" data-filterscope="seriesGroup" data-series-id="<?php echo $series['id']; ?>" data-series-color="<?php echo $seriesColor; ?>" data-series-text="<?php echo $seriesText; ?>">
            <div class="custom-checkbox checked" style="background-color: <?php echo $seriesColor; ?>;"></div>
            <div class="filter-name"><?php echo htmlspecialchars($series['name']); ?></div>
          </div>
          <?php foreach($champs as $champ): 
                  $champColor = htmlspecialchars($champ['color']);
                  $champText = htmlspecialchars($champ['text_color']);
          ?>
            <div class="filter-item subcategory" data-filterscope="championship" data-championship-id="<?php echo $champ['id']; ?>" data-series-id="<?php echo $series['id']; ?>" data-champ-color="<?php echo $champColor; ?>" data-champ-text="<?php echo $champText; ?>">
              <div class="custom-checkbox checked" style="background-color: <?php echo $champColor; ?>;"></div>
              <div class="filter-name"><?php echo htmlspecialchars($champ['name']); ?></div>
            </div>
          <?php endforeach; ?>
      <?php 
          endif;
      endforeach;
      ?>
      <div class="filter-buttons">
        <button class="filter-btn" id="btnCheckAllSeries">Pokaż wszystko</button>
        <button class="filter-btn" id="btnUncheckAllSeries">Ukryj wszystko</button>
      </div>
    </div>
 
    <div id="filtersDriversTeams" style="display:none;">
      <div class="filters-title">Kierowcy</div>
      <?php foreach($driversList as $dr): ?>
        <div class="filter-item" data-filterscope="driver" data-driver-id="<?php echo $dr['id']; ?>">
          <div class="custom-checkbox"></div>
          <div class="filter-name"><?php echo htmlspecialchars($dr['firstname'] . ' ' . $dr['lastname']); ?></div>
        </div>
      <?php endforeach; ?>
      <div class="filters-title" style="margin-top:10px;">Zespoły</div>
      <?php foreach($teamsList as $tm): ?>
        <div class="filter-item" data-filterscope="team" data-team-id="<?php echo $tm['id']; ?>">
          <div class="custom-checkbox"></div>
          <div class="filter-name"><?php echo htmlspecialchars($tm['name']); ?></div>
        </div>
      <?php endforeach; ?>
      <div class="filter-buttons">
        <button class="filter-btn" id="btnCheckAllDT">Pokaż wszystko</button>
        <button class="filter-btn" id="btnUncheckAllDT">Ukryj wszystko</button>
      </div>
    </div>
    <!-- Przycisk zapisu preferencji -->
    <div id="savePreferencesContainer">
      <button class="filter-btn" id="btnSavePreferences">Zapisz preferencje</button>
    </div>
  </div>
</div>
 
<!-- Pop-alert -->
<div id="popAlert">wybór został zapisany</div>
 
<script>
/* Inicjalizacja stanów filtrów */
let activeSeries = {};
let activeChampionships = {};
let activeDrivers = {};
let activeTeams = {};

document.querySelectorAll('[data-filterscope="series"]').forEach(item=>{
  const sid = parseInt(item.getAttribute('data-series-id'), 10);
  activeSeries[sid] = true;
});
document.querySelectorAll('[data-filterscope="seriesGroup"]').forEach(item=>{
  const sid = parseInt(item.getAttribute('data-series-id'), 10);
  activeSeries[sid] = true;
});
document.querySelectorAll('[data-filterscope="championship"]').forEach(item=>{
  const cid = parseInt(item.getAttribute('data-championship-id'), 10);
  activeChampionships[cid] = true;
});
<?php foreach($driversList as $dr): ?>
  activeDrivers[<?php echo $dr['id']; ?>] = false;
<?php endforeach; ?>
<?php foreach($teamsList as $tm): ?>
  activeTeams[<?php echo $tm['id']; ?>] = false;
<?php endforeach; ?>

// Wczytanie zapisanych preferencji z localStorage
if(localStorage.getItem('raceCalendarPreferences')){
  try {
    const savedPrefs = JSON.parse(localStorage.getItem('raceCalendarPreferences'));
    for(let sid in savedPrefs.activeSeries){
      if(activeSeries.hasOwnProperty(sid)) {
        activeSeries[sid] = savedPrefs.activeSeries[sid];
      }
    }
    for(let cid in savedPrefs.activeChampionships){
      if(activeChampionships.hasOwnProperty(cid)) {
        activeChampionships[cid] = savedPrefs.activeChampionships[cid];
      }
    }
    for(let did in savedPrefs.activeDrivers){
      if(activeDrivers.hasOwnProperty(did)) {
        activeDrivers[did] = savedPrefs.activeDrivers[did];
      }
    }
    for(let tid in savedPrefs.activeTeams){
      if(activeTeams.hasOwnProperty(tid)) {
        activeTeams[tid] = savedPrefs.activeTeams[tid];
      }
    }
  } catch(e) { console.error('Błąd parsowania preferencji', e); }
  // Po wczytaniu preferencji aktualizujemy stan checkboxów w filtrach
  updateFilterCheckboxes();
}

const driverRoundsMap = <?php echo json_encode($driverRoundsMap); ?>;
const teamRoundsMap = <?php echo json_encode($teamRoundsMap); ?>;

const toggleSwitch = document.getElementById('toggleSwitch');
const toggleLabel = document.getElementById('toggleLabel');
const filtersSeries = document.getElementById('filtersSeries');
const filtersDriversTeams = document.getElementById('filtersDriversTeams');

toggleSwitch.addEventListener('click', ()=>{
  toggleSwitch.classList.toggle('active');
  if(toggleSwitch.classList.contains('active')){
    toggleLabel.textContent = 'Tryb: Kierowcy/Zespoły';
    filtersSeries.style.display = 'none';
    filtersDriversTeams.style.display = 'block';
  } else {
    toggleLabel.textContent = 'Tryb: Serie/Mistrzostwa';
    filtersSeries.style.display = 'block';
    filtersDriversTeams.style.display = 'none';
  }
  applyFilters();
});

document.querySelectorAll('.filter-item').forEach(item=>{
  const cbox = item.querySelector('.custom-checkbox');
  const scope = item.getAttribute('data-filterscope');
  
  item.addEventListener('click', ()=>{
    if(scope === 'series'){
      const sid = parseInt(item.getAttribute('data-series-id'), 10);
      activeSeries[sid] = !activeSeries[sid];
      updateCheckbox(cbox, activeSeries[sid]);
    } else if(scope === 'seriesGroup'){
      const sid = parseInt(item.getAttribute('data-series-id'), 10);
      const newState = !activeSeries[sid];
      activeSeries[sid] = newState;
      updateCheckbox(cbox, newState);
      document.querySelectorAll('[data-filterscope="championship"][data-series-id="'+sid+'"]').forEach(sub=>{
        const cid = parseInt(sub.getAttribute('data-championship-id'), 10);
        activeChampionships[cid] = newState;
        updateCheckbox(sub.querySelector('.custom-checkbox'), newState);
      });
    } else if(scope === 'championship'){
      const cid = parseInt(item.getAttribute('data-championship-id'), 10);
      activeChampionships[cid] = !activeChampionships[cid];
      updateCheckbox(cbox, activeChampionships[cid]);
      const seriesId = parseInt(item.getAttribute('data-series-id'), 10);
      const subItems = document.querySelectorAll('[data-filterscope="championship"][data-series-id="'+seriesId+'"]');
      let allActive = true;
      subItems.forEach(function(subItem) {
        const subCid = parseInt(subItem.getAttribute('data-championship-id'), 10);
        if(!activeChampionships[subCid]) { allActive = false; }
      });
      const parent = document.querySelector('[data-filterscope="seriesGroup"][data-series-id="'+seriesId+'"]');
      if(parent) {
        activeSeries[seriesId] = allActive;
        updateCheckbox(parent.querySelector('.custom-checkbox'), allActive);
      }
    } else if(scope === 'driver'){
      const did = parseInt(item.getAttribute('data-driver-id'), 10);
      activeDrivers[did] = !activeDrivers[did];
      updateCheckbox(cbox, activeDrivers[did]);
    } else if(scope === 'team'){
      const tid = parseInt(item.getAttribute('data-team-id'), 10);
      activeTeams[tid] = !activeTeams[tid];
      updateCheckbox(cbox, activeTeams[tid]);
    }
    applyFilters();
  });
});

document.querySelectorAll('.custom-checkbox.checked').forEach(function(checkboxEl) {
  const parentScope = checkboxEl.parentElement.getAttribute('data-filterscope');
  if(parentScope === 'championship'){
    const champColor = checkboxEl.parentElement.getAttribute('data-champ-color');
    if(champColor) { checkboxEl.style.backgroundColor = champColor; }
  } else if(parentScope === 'series' || parentScope === 'seriesGroup'){
    const seriesColor = checkboxEl.parentElement.getAttribute('data-series-color');
    if(seriesColor) { checkboxEl.style.backgroundColor = seriesColor; }
  }
});

function updateCheckbox(checkboxEl, isActive){
  if(isActive){
    const parentScope = checkboxEl.parentElement.getAttribute('data-filterscope');
    if(parentScope === 'championship'){
      const champColor = checkboxEl.parentElement.getAttribute('data-champ-color');
      if(champColor){ checkboxEl.style.backgroundColor = champColor; }
      else { checkboxEl.classList.add('checked'); }
    } else if(parentScope === 'series' || parentScope === 'seriesGroup'){
      const seriesColor = checkboxEl.parentElement.getAttribute('data-series-color');
      if(seriesColor){ checkboxEl.style.backgroundColor = seriesColor; }
      else { checkboxEl.classList.add('checked'); }
    } else {
      checkboxEl.classList.add('checked');
    }
  } else {
    checkboxEl.classList.remove('checked');
    checkboxEl.style.backgroundColor = '#fff';
  }
}

// Funkcja, która przebiega po wszystkich elementach filtru i aktualizuje stan checkboxów
function updateFilterCheckboxes(){
  document.querySelectorAll('.filter-item').forEach(item => {
     const scope = item.getAttribute('data-filterscope');
     const checkbox = item.querySelector('.custom-checkbox');
     if(scope === 'series' || scope === 'seriesGroup'){
         const sid = parseInt(item.getAttribute('data-series-id'), 10);
         updateCheckbox(checkbox, activeSeries[sid]);
     } else if(scope === 'championship'){
         const cid = parseInt(item.getAttribute('data-championship-id'), 10);
         updateCheckbox(checkbox, activeChampionships[cid]);
     } else if(scope === 'driver'){
         const did = parseInt(item.getAttribute('data-driver-id'), 10);
         updateCheckbox(checkbox, activeDrivers[did]);
     } else if(scope === 'team'){
         const tid = parseInt(item.getAttribute('data-team-id'), 10);
         updateCheckbox(checkbox, activeTeams[tid]);
     }
  });
}

document.getElementById('btnCheckAllSeries').addEventListener('click', ()=>{
  document.querySelectorAll('[data-filterscope="series"], [data-filterscope="seriesGroup"], [data-filterscope="championship"]').forEach(el=>{
    if(el.hasAttribute('data-series-id')){
      const sid = parseInt(el.getAttribute('data-series-id'),10);
      activeSeries[sid] = true;
    }
    if(el.hasAttribute('data-championship-id')){
      const cid = parseInt(el.getAttribute('data-championship-id'),10);
      activeChampionships[cid] = true;
    }
    updateCheckbox(el.querySelector('.custom-checkbox'), true);
  });
  applyFilters();
});
document.getElementById('btnUncheckAllSeries').addEventListener('click', ()=>{
  document.querySelectorAll('[data-filterscope="series"], [data-filterscope="seriesGroup"], [data-filterscope="championship"]').forEach(el=>{
    if(el.hasAttribute('data-series-id')){
      const sid = parseInt(el.getAttribute('data-series-id'),10);
      activeSeries[sid] = false;
    }
    if(el.hasAttribute('data-championship-id')){
      const cid = parseInt(el.getAttribute('data-championship-id'),10);
      activeChampionships[cid] = false;
    }
    updateCheckbox(el.querySelector('.custom-checkbox'), false);
  });
  applyFilters();
});

const btnCheckAllDT = document.getElementById('btnCheckAllDT');
const btnUncheckAllDT = document.getElementById('btnUncheckAllDT');

btnCheckAllDT?.addEventListener('click', ()=>{
  document.querySelectorAll('[data-filterscope="driver"], [data-filterscope="team"]').forEach(el=>{
    if(el.hasAttribute('data-driver-id')){
      const did = parseInt(el.getAttribute('data-driver-id'),10);
      activeDrivers[did] = true;
    }
    if(el.hasAttribute('data-team-id')){
      const tid = parseInt(el.getAttribute('data-team-id'),10);
      activeTeams[tid] = true;
    }
    updateCheckbox(el.querySelector('.custom-checkbox'), true);
  });
  applyFilters();
});
btnUncheckAllDT?.addEventListener('click', ()=>{
  document.querySelectorAll('[data-filterscope="driver"], [data-filterscope="team"]').forEach(el=>{
    if(el.hasAttribute('data-driver-id')){
      const did = parseInt(el.getAttribute('data-driver-id'),10);
      activeDrivers[did] = false;
    }
    if(el.hasAttribute('data-team-id')){
      const tid = parseInt(el.getAttribute('data-team-id'),10);
      activeTeams[tid] = false;
    }
    updateCheckbox(el.querySelector('.custom-checkbox'), false);
  });
  applyFilters();
});

// Obsługa zapisu preferencji
document.getElementById('btnSavePreferences').addEventListener('click', ()=>{
  const prefs = {
    activeSeries,
    activeChampionships,
    activeDrivers,
    activeTeams
  };
  localStorage.setItem('raceCalendarPreferences', JSON.stringify(prefs));
  showPopAlert();
});

function showPopAlert(){
  const alertEl = document.getElementById('popAlert');
  alertEl.style.opacity = 1;
  setTimeout(()=>{
    alertEl.style.opacity = 0;
  }, 2000);
}

function applyFilters(){
  document.querySelectorAll('.event-bar').forEach(bar=>{
    const seriesId = parseInt(bar.getAttribute('data-series-id'),10);
    const champId = parseInt(bar.getAttribute('data-championship-id'),10);
    const roundId = parseInt(bar.getAttribute('data-round-id'),10);
    
    let show = false;
    if(activeSeries[seriesId] || activeChampionships[champId]) {
      show = true;
    }
    for(let did in activeDrivers){
      if(activeDrivers[did]){
        let rounds = driverRoundsMap[did] || [];
        if(rounds.includes(roundId)){
          show = true;
        }
      }
    }
    for(let tid in activeTeams){
      if(activeTeams[tid]){
        let rounds = teamRoundsMap[tid] || [];
        if(rounds.includes(roundId)){
          show = true;
        }
      }
    }
    bar.style.display = show ? 'block' : 'none';
  });
  adjustWeekHeights();
}

let singleMonthActive = false;
let currentMonthIndex = null;
const months = Array.from(document.querySelectorAll('.month'));
const btnMonthPrev = document.getElementById('btnMonthPrev');
const btnMonthNext = document.getElementById('btnMonthNext');
const btnMonthBack = document.getElementById('btnMonthBack');
const totalMonths = months.length;

months.forEach((mDiv, i)=>{
  mDiv.addEventListener('click', ()=>{
    if(!singleMonthActive){
      singleMonthActive = true;
      currentMonthIndex = i;
      document.body.classList.add('single-month-active');
      showSingleMonth(i);
    }
  });
});
btnMonthPrev.addEventListener('click', ()=>{
  if(currentMonthIndex > 0){
    currentMonthIndex--;
    showSingleMonth(currentMonthIndex);
  }
});
btnMonthNext.addEventListener('click', ()=>{
  if(currentMonthIndex < totalMonths - 1){
    currentMonthIndex++;
    showSingleMonth(currentMonthIndex);
  }
});
btnMonthBack.addEventListener('click', ()=>{
  singleMonthActive = false;
  currentMonthIndex = null;
  document.body.classList.remove('single-month-active');
  months.forEach(m=>{
    m.style.opacity = 0;
    m.style.display = 'flex';
    m.classList.remove('single-month');
    m.style.opacity = 1;
    m.querySelectorAll('.event-bar').forEach(bar=>{
      bar.textContent = bar.getAttribute('data-championship-name');
    });
  });
  adjustWeekHeights();
});

function showSingleMonth(idx){
  months.forEach((m, i)=>{
    if(i === idx){
      m.style.opacity = 0;
      m.style.display = 'flex';
      setTimeout(()=>{ m.classList.add('single-month'); m.style.opacity = 1; }, 150);
      m.querySelectorAll('.event-bar').forEach(bar=>{
        bar.textContent = bar.getAttribute('data-championship-name');
      });
    } else {
      m.style.display = 'none';
      m.classList.remove('single-month');
    }
  });
  adjustWeekHeights();
}

function adjustWeekHeights(){
  const grid = document.getElementById('calendarGrid');
  let columns = 3;
  const w = window.innerWidth;
  if(singleMonthActive){
    columns = 1;
  } else {
    if(w <= 1400) columns = 1;
    else if(w <= 1720) columns = 2;
    else columns = 3;
  }
  const allMonths = Array.from(grid.querySelectorAll('.month'));
  const visibleMonths = allMonths.filter(m => m.style.display !== 'none');
  
  for(let i = 0; i < visibleMonths.length; i += columns){
    let rowSlice = visibleMonths.slice(i, i + columns);
    let dayArrays = rowSlice.map(m => Array.from(m.querySelectorAll('.days .day')));
    for(let weekIndex = 0; weekIndex < 6; weekIndex++){
      let groupDays = [];
      for(let col = 0; col < rowSlice.length; col++){
        const offsetStart = weekIndex * 7;
        let slice = dayArrays[col].slice(offsetStart, offsetStart + 7);
        groupDays = groupDays.concat(slice);
      }
      let maxH = 0;
      groupDays.forEach(d => d.style.height = 'auto');
      groupDays.forEach(d => {
        const rect = d.getBoundingClientRect();
        if(rect.height > maxH) maxH = rect.height;
      });
      groupDays.forEach(d => d.style.height = maxH + 'px');
    }
  }
}

window.addEventListener('resize', adjustWeekHeights);
applyFilters();
</script>
 
<footer>
  <p>Made by Maciej Klaja</p>
  <p>wszystkie prawa zastrzeżone 2025</p>
  <a href="https://buycoffee.to/maciek-klaja" target="_blank"><img src="https://buycoffee.to/img/share-button-primary.png" style="width: 195px; height: 51px" alt="Postaw mi kawę na buycoffee.to"></a>
</footer>
</body>
</html>
<?php
/**************************************************************
 * index.php – Kalendarz wyścigowy 2025 (wersja stabilna)
 **************************************************************/

$dsn  = 'mysql:host=localhost;dbname=calendar_db;charset=utf8mb4';
$user = 'root';
$pass = '';

try {
    $pdo = new PDO($dsn, $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
} catch(Exception $e) {
    die("Błąd połączenia z bazą: " . $e->getMessage());
}

$seasonId = 1;
$seasonStart = '2025-01-01';
$seasonEnd   = '2025-12-31';
$yearStartTs = strtotime($seasonStart);
$yearEndTs   = strtotime($seasonEnd);

$sqlRounds = "
    SELECT 
      r.id AS round_id,
      r.name AS round_name,
      r.start_date,
      r.end_date,
      r.weight AS round_weight,
      
      ch.id AS championship_id,
      ch.name AS championship_name,
      ch.shortcut AS championship_shortcut,
      ch.color AS championship_color,
      ch.text_color AS championship_text_color,
      ch.weight AS championship_weight,
      
      s.id AS series_id,
      s.name AS series_name,
      s.color AS series_color,
      s.text_color AS series_text_color,
      s.weight AS series_weight
    FROM rounds r
    JOIN championships ch ON r.championship_id = ch.id
    JOIN series_championship sc ON ch.id = sc.championship_id
    JOIN series s ON sc.series_id = s.id
    WHERE r.season_id = :season_id 
      AND r.status = 1 
      AND ch.status = 1 
      AND s.status = 1
      AND r.start_date <= :season_end
      AND r.end_date >= :season_start
    ORDER BY r.start_date ASC, s.weight ASC, ch.weight ASC
";
$stmtRounds = $pdo->prepare($sqlRounds);
$stmtRounds->execute([
    ':season_id'    => $seasonId,
    ':season_start' => $seasonStart,
    ':season_end'   => $seasonEnd
]);
$rounds = $stmtRounds->fetchAll(PDO::FETCH_ASSOC);

$eventsByDay = [];
for($ts = $yearStartTs; $ts <= $yearEndTs; $ts += 86400){
    $dayStr = date('Y-m-d', $ts);
    $eventsByDay[$dayStr] = [];
}

foreach ($rounds as $row) {
    $startTs = strtotime($row['start_date']);
    $endTs   = strtotime($row['end_date']);
    if($startTs < $yearStartTs) $startTs = $yearStartTs;
    if($endTs > $yearEndTs) $endTs = $yearEndTs;
    for($t = $startTs; $t <= $endTs; $t += 86400){
        $dayStr = date('Y-m-d', $t);
        $eventsByDay[$dayStr][] = [
            'round_id'                => $row['round_id'],
            'round_name'              => $row['round_name'],
            'series_id'               => $row['series_id'],
            'series_name'             => $row['series_name'],
            'series_color'            => $row['series_color'],
            'series_text_color'       => $row['series_text_color'],
            'championship_id'         => $row['championship_id'],
            'championship_name'       => $row['championship_name'],
            'championship_shortcut'   => $row['championship_shortcut'],
            'championship_color'      => $row['championship_color'],
            'championship_text_color' => $row['championship_text_color'],
            'order_weight'            => $row['series_weight'] * 100 + $row['championship_weight']
        ];
    }
}
foreach($eventsByDay as $day => &$events) {
    usort($events, function($a, $b) {
        return $a['order_weight'] <=> $b['order_weight'];
    });
}
unset($events);

$stmtSeries = $pdo->query("SELECT * FROM series WHERE status = 1 ORDER BY weight ASC");
$seriesList = $stmtSeries->fetchAll(PDO::FETCH_ASSOC);

$filterSeries = [];
foreach ($seriesList as $s) {
    $filterSeries[$s['id']] = [
        'series' => $s,
        'championships' => []
    ];
}
$sqlChamps = "
    SELECT ch.*, sc.series_id 
    FROM championships ch
    JOIN series_championship sc ON ch.id = sc.championship_id
    WHERE ch.status = 1
    ORDER BY ch.weight ASC
";
$stmtChamps = $pdo->query($sqlChamps);
while ($ch = $stmtChamps->fetch(PDO::FETCH_ASSOC)) {
    $seriesId = $ch['series_id'];
    if(isset($filterSeries[$seriesId])){
        $filterSeries[$seriesId]['championships'][$ch['id']] = $ch;
    }
}

$stmtDrivers = $pdo->query("SELECT id, firstname, lastname, color, weight FROM drivers WHERE status = 1 ORDER BY weight ASC");
$driversList = $stmtDrivers->fetchAll(PDO::FETCH_ASSOC);

$stmtTeams = $pdo->query("SELECT id, name, color, weight FROM teams WHERE status = 1 ORDER BY weight ASC");
$teamsList = $stmtTeams->fetchAll(PDO::FETCH_ASSOC);

$driverRoundsMap = [];
$stmtDriverRounds = $pdo->query("SELECT driver_id, round_id FROM driver_round");
while($row = $stmtDriverRounds->fetch(PDO::FETCH_ASSOC)){
    $driverRoundsMap[$row['driver_id']][] = $row['round_id'];
}

$teamRoundsMap = [];
$stmtTeamRounds = $pdo->query("SELECT team_id, round_id FROM team_round");
while($row = $stmtTeamRounds->fetch(PDO::FETCH_ASSOC)){
    $teamRoundsMap[$row['team_id']][] = $row['round_id'];
}

$weekdaysFull = ['Poniedziałek','Wtorek','Środa','Czwartek','Piątek','Sobota','Niedziela'];
$monthNamesFull = [
  1=>"Styczeń",2=>"Luty",3=>"Marzec",4=>"Kwiecień",5=>"Maj",6=>"Czerwiec",
  7=>"Lipiec",8=>"Sierpień",9=>"Wrzesień",10=>"Październik",11=>"Listopad",12=>"Grudzień"
];
?>
<!DOCTYPE html>
<html lang="pl">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Kalendarz wyścigowy 2025</title>
  <style>
    /* Ukrycie paska przewijania */
    ::-webkit-scrollbar { display: none; }
    body { -ms-overflow-style: none; scrollbar-width: none; }

    /* Podstawowe style */
    html, body {
      width: 100%; max-width: 100%; margin: 0; padding: 0;
      background: #023E42; color: #fff; font-family: Arial, sans-serif;
      min-height: 100vh; display: flex; flex-direction: column;
    }
    header { background-color: #023E42; padding: 10px; text-align: center; }
    header h1 { font-size: 24px; margin: 0 auto; }
    
    .main-content { flex: 1; display: flex; width: 100%; }
    .calendar-container { flex: 1; order: 1; width: 100%; padding: 40px 60px; position: relative; }
    .calendar-wrapper { display: flex; flex-direction: row; align-items: flex-start; justify-content: space-between; width: 100%; }
    .calendar-center { flex: 1; }
    body:not(.single-month-active) .calendar-left,
    body:not(.single-month-active) .calendar-right { display: none; }
    .calendar-left, .calendar-right { display: flex; align-items: center; margin-top: 350px; }
    
    .month, #filtersPanel { background-color: #023E42; }
    
    body:not(.single-month-active) .nav-back-container { display: none; }
    .nav-back-container { margin-top: 10px; text-align: center; }
    .nav-back-container button {
      background: rgba(0, 0, 0, 0.7); color: #fff; border: none; border-radius: 4px; padding: 8px 16px;
      cursor: pointer; transition: background 0.2s, transform 0.2s;
    }
    .nav-back-container button:hover { background: rgba(0, 0, 0, 0.9); transform: scale(1.05); }
    
    .nav-button {
      background: rgba(0, 0, 0, 0.7); color: #fff; border: none; border-radius: 4px;
      width: 50px; height: 40px; cursor: pointer; transition: background 0.2s, transform 0.2s;
    }
    .nav-button:hover { background: rgba(0, 0, 0, 0.9); transform: scale(1.05); }
    .nav-icon { width: 16px; height: 16px; fill: #fff; }
    
    .weekdays { display: grid; grid-template-columns: repeat(7, 1fr); margin-bottom: 4px; }
    .weekdays div { text-align: center; font-weight: bold; font-size: 12px; color: #eee; }
    .days { display: grid; grid-template-columns: repeat(7, 1fr); }
    .day {
      background: #023E42; position: relative; min-height: 80px; border: 1px solid #fff;
      overflow: hidden; transition: background 0.3s;
    }
    .day:hover { background: #035765; }
    .day-number { position: absolute; top: 5px; left: 5px; font-size: 13px; font-weight: bold; }
    .event-bar {
      width: 100%; font-size: 12px; font-weight: bold; line-height: 14px; border-radius: 3px;
      margin-bottom: 2px; height: 20px; text-align: center; position: relative;
      overflow: hidden; white-space: nowrap; text-overflow: ellipsis; cursor: default;
    }
    .event-bar:hover::after {
      content: attr(data-round); position: absolute; bottom: 100%; left: 50%;
      transform: translate(-50%, -3px); background: #fff; color: #000; font-size: 10px;
      padding: 2px 5px; border-radius: 3px; pointer-events: none; z-index: 999;
    }
    
    /* Filtry – powiększone marginesy, czcionka i checkboxy */
    .filters-title { font-size: 16px; font-weight: bold; margin-bottom: 4px; }
    .filter-item { display: flex; align-items: center; margin-bottom: 8px; cursor: pointer; }
    .custom-checkbox {
      width: 20px; height: 20px; border-radius: 3px; background-color: #fff; margin-right: 8px;
      transition: background-color 0.2s;
    }
    .filter-name { font-size: 14px; }
    .filter-buttons { margin-top: 4px; display: flex; gap: 6px; }
    .filter-btn {
      background-color: #fff; color: #023E42; border: none; border-radius: 3px;
      padding: 6px 12px; cursor: pointer; font-size: 14px; transition: opacity 0.2s;
    }
    .filter-btn:hover { opacity: 0.8; }
    .filter-group { font-weight: bold; text-transform: uppercase; margin-top: 10px; }
    .subcategory { margin-left: 15px; }
    .toggle-row { display: flex; align-items: center; gap: 8px; margin-bottom: 10px; }
    .toggle-switch {
      width: 40px; height: 20px; background-color: #00A1A7; border-radius: 10px; position: relative;
      cursor: pointer; transition: background-color 0.2s;
    }
    .toggle-knob {
      width: 16px; height: 16px; background-color: #fff; border-radius: 50%;
      position: absolute; top: 2px; left: 2px; transition: left 0.2s;
    }
    .toggle-switch.active .toggle-knob { left: 22px; }
    .toggle-label { font-size: 12px; }
    
    #filtersPanel {
      width: 320px; background: #023E42; padding: 20px; overflow-y: auto; flex-shrink: 0; order: 2;
    }
    /* Przycisk zapisu preferencji */
    #savePreferencesContainer { margin-top: 20px; text-align: center; }
    #btnSavePreferences {
      background-color: #00A1A7; color: #fff; border: none; border-radius: 4px;
      padding: 8px 16px; font-size: 14px; cursor: pointer; transition: background 0.2s, transform 0.2s;
    }
    #btnSavePreferences:hover { background-color: #008f94; transform: scale(1.03); }
    
    /* Pop-alert – animowany komunikat zapisu */
    #popAlert {
      position: fixed; bottom: 20px; left: 50%; transform: translateX(-50%);
      background-color: rgba(0,0,0,0.8); color: #fff; padding: 10px 20px; border-radius: 4px;
      font-size: 14px; opacity: 0; pointer-events: none; transition: opacity 0.5s;
    }
    
    /* Układ kalendarza 12-miesięcznego – gdy brak klasy single-month-active */
    body:not(.single-month-active) .calendar-grid {
      display: grid; gap: 15px; grid-template-columns: repeat(3, 1fr);
    }
    @media (max-width: 1720px) {
      body:not(.single-month-active) .calendar-grid {
        grid-template-columns: repeat(2, 1fr);
      }
    }
    @media (max-width: 1400px) {
      body:not(.single-month-active) .calendar-grid {
        grid-template-columns: 1fr;
      }
    }
    .calendar-grid { display: grid; gap: 15px; grid-template-columns: repeat(3, 1fr); }
    .month {
      background-color: #023E42; padding: 10px; display: flex; flex-direction: column;
      border-radius: 6px; position: relative;
    }
    .month.single-month { grid-column: 1 / -1; background-color: #023E42; }
    .month-label { text-align: center; font-size: 18px; font-weight: bold; margin-bottom: 8px; }
    
    footer { background-color: #023E42; text-align: center; padding: 10px; color: #fff; }
  </style>
</head>
<body>
<header>
  <h1>Kalendarz wyścigowy 2025</h1>
</header>
 
<div class="main-content">
  <div class="calendar-container" id="calendarContainer">
    <div class="calendar-wrapper">
      <div class="calendar-left">
        <button class="nav-button" id="btnMonthPrev">
          <svg class="nav-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16">
            <path d="M10 12L4 8l6-4v8z"/>
          </svg>
        </button>
      </div>
      <div class="calendar-center">
        <div class="calendar-grid" id="calendarGrid">
          <?php 
          for($m = 1; $m <= 12; $m++){
              $daysInMonth = cal_days_in_month(CAL_GREGORIAN, $m, 2025);
              $firstTime   = strtotime("2025-$m-01");
              $firstDOW    = date('N', $firstTime);
              $emptyBefore = $firstDOW - 1;
              echo '<div class="month" data-month="'.$m.'" data-month-full="'.$monthNamesFull[$m].'">';
                  echo '<div class="month-label">'.$monthNamesFull[$m].'</div>';
                  echo '<div class="weekdays">';
                  foreach($weekdaysFull as $wd){
                      echo '<div>'.htmlspecialchars($wd).'</div>';
                  }
                  echo '</div>';
                  echo '<div class="days">';
                  for($i=0; $i<$emptyBefore; $i++){
                      echo '<div class="day"></div>';
                  }
                  for($d=1; $d<=$daysInMonth; $d++){
                      $dateStr = sprintf("2025-%02d-%02d", $m, $d);
                      echo '<div class="day" data-date="'.$dateStr.'">';
                          echo '<div class="day-number">'.$d.'</div>';
                          if(!empty($eventsByDay[$dateStr])){
                              echo '<div class="events-container">';
                              foreach($eventsByDay[$dateStr] as $evt){
                                  echo '<div class="event-bar" '
                                       .'data-round-id="'.$evt['round_id'].'" '
                                       .'data-series-id="'.$evt['series_id'].'" '
                                       .'data-championship-id="'.$evt['championship_id'].'" '
                                       .'data-round="'.htmlspecialchars($evt['round_name']).'" '
                                       .'data-championship-name="'.htmlspecialchars($evt['championship_name']).'" '
                                       .'style="background-color:'.$evt['championship_color'].';color:'.$evt['championship_text_color'].';">'
                                       .htmlspecialchars($evt['championship_shortcut'])
                                       .'</div>';
                              }
                              echo '</div>';
                          }
                      echo '</div>';
                  }
                  $usedCells = $emptyBefore + $daysInMonth;
                  $rest = 42 - $usedCells;
                  for($i=0; $i<$rest; $i++){
                      echo '<div class="day"></div>';
                  }
                  echo '</div>';
              echo '</div>';
          }
          ?>
        </div>
      </div>
      <div class="calendar-right">
        <button class="nav-button" id="btnMonthNext">
          <svg class="nav-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16">
            <path d="M6 4l6 4-6 4V4z"/>
          </svg>
        </button>
      </div>
    </div>
    <div class="nav-back-container" id="navBackContainer">
      <button id="btnMonthBack">
        <svg class="nav-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16">
          <path d="M4 6l4 4 4-4H4z"/>
        </svg>
      </button>
    </div>
  </div>
 
  <div id="filtersPanel">
    <div class="toggle-row">
      <div id="toggleSwitch" class="toggle-switch">
        <div class="toggle-knob"></div>
      </div>
      <div id="toggleLabel" class="toggle-label">Tryb: Serie/Mistrzostwa</div>
    </div>
 
    <div id="filtersSeries">
      <div class="filters-title">Serie / Mistrzostwa</div>
      <?php 
      foreach($filterSeries as $sData):
          $series = $sData['series'];
          $champs = $sData['championships'];
          $seriesColor = htmlspecialchars($series['color']);
          $seriesText = htmlspecialchars($series['text_color']);
          if(count($champs) <= 1):
      ?>
          <div class="filter-item" data-filterscope="series" data-series-id="<?php echo $series['id']; ?>" data-series-color="<?php echo $seriesColor; ?>" data-series-text="<?php echo $seriesText; ?>">
            <div class="custom-checkbox checked" style="background-color: <?php echo $seriesColor; ?>;"></div>
            <div class="filter-name"><?php echo htmlspecialchars($series['name']); ?></div>
          </div>
      <?php else: ?>
          <div class="filter-item filter-group" data-filterscope="seriesGroup" data-series-id="<?php echo $series['id']; ?>" data-series-color="<?php echo $seriesColor; ?>" data-series-text="<?php echo $seriesText; ?>">
            <div class="custom-checkbox checked" style="background-color: <?php echo $seriesColor; ?>;"></div>
            <div class="filter-name"><?php echo htmlspecialchars($series['name']); ?></div>
          </div>
          <?php foreach($champs as $champ): 
                  $champColor = htmlspecialchars($champ['color']);
                  $champText = htmlspecialchars($champ['text_color']);
          ?>
            <div class="filter-item subcategory" data-filterscope="championship" data-championship-id="<?php echo $champ['id']; ?>" data-series-id="<?php echo $series['id']; ?>" data-champ-color="<?php echo $champColor; ?>" data-champ-text="<?php echo $champText; ?>">
              <div class="custom-checkbox checked" style="background-color: <?php echo $champColor; ?>;"></div>
              <div class="filter-name"><?php echo htmlspecialchars($champ['name']); ?></div>
            </div>
          <?php endforeach; ?>
      <?php 
          endif;
      endforeach;
      ?>
      <div class="filter-buttons">
        <button class="filter-btn" id="btnCheckAllSeries">Pokaż wszystko</button>
        <button class="filter-btn" id="btnUncheckAllSeries">Ukryj wszystko</button>
      </div>
    </div>
 
    <div id="filtersDriversTeams" style="display:none;">
      <div class="filters-title">Kierowcy</div>
      <?php foreach($driversList as $dr): ?>
        <div class="filter-item" data-filterscope="driver" data-driver-id="<?php echo $dr['id']; ?>">
          <div class="custom-checkbox"></div>
          <div class="filter-name"><?php echo htmlspecialchars($dr['firstname'] . ' ' . $dr['lastname']); ?></div>
        </div>
      <?php endforeach; ?>
      <div class="filters-title" style="margin-top:10px;">Zespoły</div>
      <?php foreach($teamsList as $tm): ?>
        <div class="filter-item" data-filterscope="team" data-team-id="<?php echo $tm['id']; ?>">
          <div class="custom-checkbox"></div>
          <div class="filter-name"><?php echo htmlspecialchars($tm['name']); ?></div>
        </div>
      <?php endforeach; ?>
      <div class="filter-buttons">
        <button class="filter-btn" id="btnCheckAllDT">Pokaż wszystko</button>
        <button class="filter-btn" id="btnUncheckAllDT">Ukryj wszystko</button>
      </div>
    </div>
    <!-- Przycisk zapisu preferencji -->
    <div id="savePreferencesContainer">
      <button class="filter-btn" id="btnSavePreferences">Zapisz preferencje</button>
    </div>
  </div>
</div>
 
<!-- Pop-alert -->
<div id="popAlert">wybór został zapisany</div>
 
<script>
/* Inicjalizacja stanów filtrów */
let activeSeries = {};
let activeChampionships = {};
let activeDrivers = {};
let activeTeams = {};

document.querySelectorAll('[data-filterscope="series"]').forEach(item=>{
  const sid = parseInt(item.getAttribute('data-series-id'), 10);
  activeSeries[sid] = true;
});
document.querySelectorAll('[data-filterscope="seriesGroup"]').forEach(item=>{
  const sid = parseInt(item.getAttribute('data-series-id'), 10);
  activeSeries[sid] = true;
});
document.querySelectorAll('[data-filterscope="championship"]').forEach(item=>{
  const cid = parseInt(item.getAttribute('data-championship-id'), 10);
  activeChampionships[cid] = true;
});
<?php foreach($driversList as $dr): ?>
  activeDrivers[<?php echo $dr['id']; ?>] = false;
<?php endforeach; ?>
<?php foreach($teamsList as $tm): ?>
  activeTeams[<?php echo $tm['id']; ?>] = false;
<?php endforeach; ?>

// Wczytanie zapisanych preferencji z localStorage
if(localStorage.getItem('raceCalendarPreferences')){
  try {
    const savedPrefs = JSON.parse(localStorage.getItem('raceCalendarPreferences'));
    for(let sid in savedPrefs.activeSeries){
      if(activeSeries.hasOwnProperty(sid)) {
        activeSeries[sid] = savedPrefs.activeSeries[sid];
      }
    }
    for(let cid in savedPrefs.activeChampionships){
      if(activeChampionships.hasOwnProperty(cid)) {
        activeChampionships[cid] = savedPrefs.activeChampionships[cid];
      }
    }
    for(let did in savedPrefs.activeDrivers){
      if(activeDrivers.hasOwnProperty(did)) {
        activeDrivers[did] = savedPrefs.activeDrivers[did];
      }
    }
    for(let tid in savedPrefs.activeTeams){
      if(activeTeams.hasOwnProperty(tid)) {
        activeTeams[tid] = savedPrefs.activeTeams[tid];
      }
    }
  } catch(e) { console.error('Błąd parsowania preferencji', e); }
  // Po wczytaniu preferencji aktualizujemy stan checkboxów w filtrach
  updateFilterCheckboxes();
}

const driverRoundsMap = <?php echo json_encode($driverRoundsMap); ?>;
const teamRoundsMap = <?php echo json_encode($teamRoundsMap); ?>;

const toggleSwitch = document.getElementById('toggleSwitch');
const toggleLabel = document.getElementById('toggleLabel');
const filtersSeries = document.getElementById('filtersSeries');
const filtersDriversTeams = document.getElementById('filtersDriversTeams');

toggleSwitch.addEventListener('click', ()=>{
  toggleSwitch.classList.toggle('active');
  if(toggleSwitch.classList.contains('active')){
    toggleLabel.textContent = 'Tryb: Kierowcy/Zespoły';
    filtersSeries.style.display = 'none';
    filtersDriversTeams.style.display = 'block';
  } else {
    toggleLabel.textContent = 'Tryb: Serie/Mistrzostwa';
    filtersSeries.style.display = 'block';
    filtersDriversTeams.style.display = 'none';
  }
  applyFilters();
});

document.querySelectorAll('.filter-item').forEach(item=>{
  const cbox = item.querySelector('.custom-checkbox');
  const scope = item.getAttribute('data-filterscope');
  
  item.addEventListener('click', ()=>{
    if(scope === 'series'){
      const sid = parseInt(item.getAttribute('data-series-id'), 10);
      activeSeries[sid] = !activeSeries[sid];
      updateCheckbox(cbox, activeSeries[sid]);
    } else if(scope === 'seriesGroup'){
      const sid = parseInt(item.getAttribute('data-series-id'), 10);
      const newState = !activeSeries[sid];
      activeSeries[sid] = newState;
      updateCheckbox(cbox, newState);
      document.querySelectorAll('[data-filterscope="championship"][data-series-id="'+sid+'"]').forEach(sub=>{
        const cid = parseInt(sub.getAttribute('data-championship-id'), 10);
        activeChampionships[cid] = newState;
        updateCheckbox(sub.querySelector('.custom-checkbox'), newState);
      });
    } else if(scope === 'championship'){
      const cid = parseInt(item.getAttribute('data-championship-id'), 10);
      activeChampionships[cid] = !activeChampionships[cid];
      updateCheckbox(cbox, activeChampionships[cid]);
      const seriesId = parseInt(item.getAttribute('data-series-id'), 10);
      const subItems = document.querySelectorAll('[data-filterscope="championship"][data-series-id="'+seriesId+'"]');
      let allActive = true;
      subItems.forEach(function(subItem) {
        const subCid = parseInt(subItem.getAttribute('data-championship-id'), 10);
        if(!activeChampionships[subCid]) { allActive = false; }
      });
      const parent = document.querySelector('[data-filterscope="seriesGroup"][data-series-id="'+seriesId+'"]');
      if(parent) {
        activeSeries[seriesId] = allActive;
        updateCheckbox(parent.querySelector('.custom-checkbox'), allActive);
      }
    } else if(scope === 'driver'){
      const did = parseInt(item.getAttribute('data-driver-id'), 10);
      activeDrivers[did] = !activeDrivers[did];
      updateCheckbox(cbox, activeDrivers[did]);
    } else if(scope === 'team'){
      const tid = parseInt(item.getAttribute('data-team-id'), 10);
      activeTeams[tid] = !activeTeams[tid];
      updateCheckbox(cbox, activeTeams[tid]);
    }
    applyFilters();
  });
});

document.querySelectorAll('.custom-checkbox.checked').forEach(function(checkboxEl) {
  const parentScope = checkboxEl.parentElement.getAttribute('data-filterscope');
  if(parentScope === 'championship'){
    const champColor = checkboxEl.parentElement.getAttribute('data-champ-color');
    if(champColor) { checkboxEl.style.backgroundColor = champColor; }
  } else if(parentScope === 'series' || parentScope === 'seriesGroup'){
    const seriesColor = checkboxEl.parentElement.getAttribute('data-series-color');
    if(seriesColor) { checkboxEl.style.backgroundColor = seriesColor; }
  }
});

function updateCheckbox(checkboxEl, isActive){
  if(isActive){
    const parentScope = checkboxEl.parentElement.getAttribute('data-filterscope');
    if(parentScope === 'championship'){
      const champColor = checkboxEl.parentElement.getAttribute('data-champ-color');
      if(champColor){ checkboxEl.style.backgroundColor = champColor; }
      else { checkboxEl.classList.add('checked'); }
    } else if(parentScope === 'series' || parentScope === 'seriesGroup'){
      const seriesColor = checkboxEl.parentElement.getAttribute('data-series-color');
      if(seriesColor){ checkboxEl.style.backgroundColor = seriesColor; }
      else { checkboxEl.classList.add('checked'); }
    } else {
      checkboxEl.classList.add('checked');
    }
  } else {
    checkboxEl.classList.remove('checked');
    checkboxEl.style.backgroundColor = '#fff';
  }
}

// Funkcja, która przebiega po wszystkich elementach filtru i aktualizuje stan checkboxów
function updateFilterCheckboxes(){
  document.querySelectorAll('.filter-item').forEach(item => {
     const scope = item.getAttribute('data-filterscope');
     const checkbox = item.querySelector('.custom-checkbox');
     if(scope === 'series' || scope === 'seriesGroup'){
         const sid = parseInt(item.getAttribute('data-series-id'), 10);
         updateCheckbox(checkbox, activeSeries[sid]);
     } else if(scope === 'championship'){
         const cid = parseInt(item.getAttribute('data-championship-id'), 10);
         updateCheckbox(checkbox, activeChampionships[cid]);
     } else if(scope === 'driver'){
         const did = parseInt(item.getAttribute('data-driver-id'), 10);
         updateCheckbox(checkbox, activeDrivers[did]);
     } else if(scope === 'team'){
         const tid = parseInt(item.getAttribute('data-team-id'), 10);
         updateCheckbox(checkbox, activeTeams[tid]);
     }
  });
}

document.getElementById('btnCheckAllSeries').addEventListener('click', ()=>{
  document.querySelectorAll('[data-filterscope="series"], [data-filterscope="seriesGroup"], [data-filterscope="championship"]').forEach(el=>{
    if(el.hasAttribute('data-series-id')){
      const sid = parseInt(el.getAttribute('data-series-id'),10);
      activeSeries[sid] = true;
    }
    if(el.hasAttribute('data-championship-id')){
      const cid = parseInt(el.getAttribute('data-championship-id'),10);
      activeChampionships[cid] = true;
    }
    updateCheckbox(el.querySelector('.custom-checkbox'), true);
  });
  applyFilters();
});
document.getElementById('btnUncheckAllSeries').addEventListener('click', ()=>{
  document.querySelectorAll('[data-filterscope="series"], [data-filterscope="seriesGroup"], [data-filterscope="championship"]').forEach(el=>{
    if(el.hasAttribute('data-series-id')){
      const sid = parseInt(el.getAttribute('data-series-id'),10);
      activeSeries[sid] = false;
    }
    if(el.hasAttribute('data-championship-id')){
      const cid = parseInt(el.getAttribute('data-championship-id'),10);
      activeChampionships[cid] = false;
    }
    updateCheckbox(el.querySelector('.custom-checkbox'), false);
  });
  applyFilters();
});

const btnCheckAllDT = document.getElementById('btnCheckAllDT');
const btnUncheckAllDT = document.getElementById('btnUncheckAllDT');

btnCheckAllDT?.addEventListener('click', ()=>{
  document.querySelectorAll('[data-filterscope="driver"], [data-filterscope="team"]').forEach(el=>{
    if(el.hasAttribute('data-driver-id')){
      const did = parseInt(el.getAttribute('data-driver-id'),10);
      activeDrivers[did] = true;
    }
    if(el.hasAttribute('data-team-id')){
      const tid = parseInt(el.getAttribute('data-team-id'),10);
      activeTeams[tid] = true;
    }
    updateCheckbox(el.querySelector('.custom-checkbox'), true);
  });
  applyFilters();
});
btnUncheckAllDT?.addEventListener('click', ()=>{
  document.querySelectorAll('[data-filterscope="driver"], [data-filterscope="team"]').forEach(el=>{
    if(el.hasAttribute('data-driver-id')){
      const did = parseInt(el.getAttribute('data-driver-id'),10);
      activeDrivers[did] = false;
    }
    if(el.hasAttribute('data-team-id')){
      const tid = parseInt(el.getAttribute('data-team-id'),10);
      activeTeams[tid] = false;
    }
    updateCheckbox(el.querySelector('.custom-checkbox'), false);
  });
  applyFilters();
});

// Obsługa zapisu preferencji
document.getElementById('btnSavePreferences').addEventListener('click', ()=>{
  const prefs = {
    activeSeries,
    activeChampionships,
    activeDrivers,
    activeTeams
  };
  localStorage.setItem('raceCalendarPreferences', JSON.stringify(prefs));
  showPopAlert();
});

function showPopAlert(){
  const alertEl = document.getElementById('popAlert');
  alertEl.style.opacity = 1;
  setTimeout(()=>{
    alertEl.style.opacity = 0;
  }, 2000);
}

function applyFilters(){
  document.querySelectorAll('.event-bar').forEach(bar=>{
    const seriesId = parseInt(bar.getAttribute('data-series-id'),10);
    const champId = parseInt(bar.getAttribute('data-championship-id'),10);
    const roundId = parseInt(bar.getAttribute('data-round-id'),10);
    
    let show = false;
    if(activeSeries[seriesId] || activeChampionships[champId]) {
      show = true;
    }
    for(let did in activeDrivers){
      if(activeDrivers[did]){
        let rounds = driverRoundsMap[did] || [];
        if(rounds.includes(roundId)){
          show = true;
        }
      }
    }
    for(let tid in activeTeams){
      if(activeTeams[tid]){
        let rounds = teamRoundsMap[tid] || [];
        if(rounds.includes(roundId)){
          show = true;
        }
      }
    }
    bar.style.display = show ? 'block' : 'none';
  });
  adjustWeekHeights();
}

let singleMonthActive = false;
let currentMonthIndex = null;
const months = Array.from(document.querySelectorAll('.month'));
const btnMonthPrev = document.getElementById('btnMonthPrev');
const btnMonthNext = document.getElementById('btnMonthNext');
const btnMonthBack = document.getElementById('btnMonthBack');
const totalMonths = months.length;

months.forEach((mDiv, i)=>{
  mDiv.addEventListener('click', ()=>{
    if(!singleMonthActive){
      singleMonthActive = true;
      currentMonthIndex = i;
      document.body.classList.add('single-month-active');
      showSingleMonth(i);
    }
  });
});
btnMonthPrev.addEventListener('click', ()=>{
  if(currentMonthIndex > 0){
    currentMonthIndex--;
    showSingleMonth(currentMonthIndex);
  }
});
btnMonthNext.addEventListener('click', ()=>{
  if(currentMonthIndex < totalMonths - 1){
    currentMonthIndex++;
    showSingleMonth(currentMonthIndex);
  }
});
btnMonthBack.addEventListener('click', ()=>{
  singleMonthActive = false;
  currentMonthIndex = null;
  document.body.classList.remove('single-month-active');
  months.forEach(m=>{
    m.style.opacity = 0;
    m.style.display = 'flex';
    m.classList.remove('single-month');
    m.style.opacity = 1;
    m.querySelectorAll('.event-bar').forEach(bar=>{
      bar.textContent = bar.getAttribute('data-championship-name');
    });
  });
  adjustWeekHeights();
});

function showSingleMonth(idx){
  months.forEach((m, i)=>{
    if(i === idx){
      m.style.opacity = 0;
      m.style.display = 'flex';
      setTimeout(()=>{ m.classList.add('single-month'); m.style.opacity = 1; }, 150);
      m.querySelectorAll('.event-bar').forEach(bar=>{
        bar.textContent = bar.getAttribute('data-championship-name');
      });
    } else {
      m.style.display = 'none';
      m.classList.remove('single-month');
    }
  });
  adjustWeekHeights();
}

function adjustWeekHeights(){
  const grid = document.getElementById('calendarGrid');
  let columns = 3;
  const w = window.innerWidth;
  if(singleMonthActive){
    columns = 1;
  } else {
    if(w <= 1400) columns = 1;
    else if(w <= 1720) columns = 2;
    else columns = 3;
  }
  const allMonths = Array.from(grid.querySelectorAll('.month'));
  const visibleMonths = allMonths.filter(m => m.style.display !== 'none');
  
  for(let i = 0; i < visibleMonths.length; i += columns){
    let rowSlice = visibleMonths.slice(i, i + columns);
    let dayArrays = rowSlice.map(m => Array.from(m.querySelectorAll('.days .day')));
    for(let weekIndex = 0; weekIndex < 6; weekIndex++){
      let groupDays = [];
      for(let col = 0; col < rowSlice.length; col++){
        const offsetStart = weekIndex * 7;
        let slice = dayArrays[col].slice(offsetStart, offsetStart + 7);
        groupDays = groupDays.concat(slice);
      }
      let maxH = 0;
      groupDays.forEach(d => d.style.height = 'auto');
      groupDays.forEach(d => {
        const rect = d.getBoundingClientRect();
        if(rect.height > maxH) maxH = rect.height;
      });
      groupDays.forEach(d => d.style.height = maxH + 'px');
    }
  }
}

window.addEventListener('resize', adjustWeekHeights);
applyFilters();
</script>
 
<footer>
  <p>Made by Maciej Klaja</p>
  <p>wszystkie prawa zastrzeżone 2025</p>
  <a href="https://buycoffee.to/maciek-klaja" target="_blank"><img src="https://buycoffee.to/img/share-button-primary.png" style="width: 195px; height: 51px" alt="Postaw mi kawę na buycoffee.to"></a>
</footer>
</body>
</html>
