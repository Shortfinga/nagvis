<?php

class WorldmapError extends MapSourceError {}

define('MATCH_WORLDMAP_ZOOM', '/^1?[0-9]$/');

// Register this source as being selectable by the user
global $selectable;
$selectable = true;

// options to be modifiable by the user(url)
global $viewParams;
$viewParams = array(
    'worldmap' => array(
        'backend_id',
        'worldmap_center',
        'worldmap_zoom',
    )
);

// Config variables to be registered for this source
global $configVars;
$configVars = array(
    'worldmap_center' => array(
        'must'      => false,
        'default'   => '51.505,-0.09',
        'match'     => MATCH_LATLONG,
        'section'   => 'worldmap',
    ),
    'worldmap_zoom' => array(
        'must'      => false,
        'default'   => 13,
        'match'     => MATCH_WORLDMAP_ZOOM,
        'section'   => 'worldmap',
    ),
);

// Global config vars not to show for worldmaps
$hiddenConfigVars = array(
    'zoom',
    'zoombar',
);

// The worldmap database object
$DB = null;

function worldmap_init_schema() {
    global $DB, $CORE;
    // Create initial db scheme if needed
    if (!$DB->tableExist('objects')) {
        $DB->exec('CREATE TABLE objects '
                 .'(object_id VARCHAR(20),'
                 .' lat REAL,'
                 .' lng REAL,'
                 .' lat2 REAL,' // needed for line objects
                 .' lng2 REAL,'
                 .' object TEXT,'
                 .' PRIMARY KEY(object_id))');
        $DB->exec('CREATE INDEX latlng ON objects (lat,long)');
        $DB->exec('CREATE INDEX latlng2 ON objects (lat2,long2)');
        $DB->createVersionTable();
    }
    //else {
    //    // Maybe an update is needed
    //    $DB->updateDb();

    //    // Only apply the new version when this is the real release or newer
    //    // (While development the version string remains on the old value)
    //    //if($CORE->versionToTag(CONST_VERSION) >= 1060100)
    //        $DB->updateDbVersion();
    //}
}

function worldmap_init_db() {
    global $DB;
    if ($DB !== null)
        return; // only init once
    $DB = new CoreSQLiteHandler();
    if (!$DB->open(cfg('paths', 'cfg').'worldmap.db'))
        throw new NagVisException(l('Unable to open the worldmap database ([DB])',
                     Array('DB' => cfg('paths', 'cfg').'worldmap.db')));

    worldmap_init_schema();
}

// Returns the minimum bounds needed to be able to display all objects
function get_bounds_worldmap($MAPCFG, $map_name, &$map_config) {
    global $DB;
    worldmap_init_db();

    $q = 'SELECT min(lat) as min_lat, min(lng) as min_lng, '
        .'max(lat) as max_lat, max(lng) as max_lng '
        .'FROM objects';
    $b = $DB->fetchAssoc($DB->query($q));
    return array(array($b['min_lat'], $b['min_lng']),
                 array($b['max_lat'], $b['max_lng']));
}

function worldmap_get_objects_by_bounds($sw_lng, $sw_lat, $ne_lng, $ne_lat) {
    global $DB;
    worldmap_init_db();

    $q = 'SELECT object FROM objects WHERE'
        .'(('.$sw_lat.' < '.$ne_lat.' AND lat BETWEEN '.$sw_lat.' AND '.$ne_lat.')'
        .' OR ('.$ne_lat.' < '.$sw_lat.' AND lat BETWEEN '.$ne_lat.' AND '.$sw_lat.')'
        .'AND '
        .'('.$sw_lng.' < '.$ne_lng.' AND lng BETWEEN '.$sw_lng.' AND '.$ne_lng.')'
        .' OR ('.$ne_lng.' < '.$sw_lng.' AND lng BETWEEN '.$ne_lng.' AND '.$sw_lng.'))'
        .'OR '
        .'(('.$sw_lat.' < '.$ne_lat.' AND lat2 BETWEEN '.$sw_lat.' AND '.$ne_lat.')'
        .' OR ('.$ne_lat.' < '.$sw_lat.' AND lat2 BETWEEN '.$ne_lat.' AND '.$sw_lat.')'
        .'AND '
        .'('.$sw_lng.' < '.$ne_lng.' AND lng2 BETWEEN '.$sw_lng.' AND '.$ne_lng.')'
        .' OR ('.$ne_lng.' < '.$sw_lng.' AND lng2 BETWEEN '.$ne_lng.' AND '.$sw_lng.'))';

    $RES = $DB->query($q);
    $objects = array();
    while ($data = $DB->fetchAssoc($RES)) {
        $obj = json_decode($data['object'], true);
        $objects[$obj['object_id']] = $obj;
    }
    return $objects;
}

function has_obj_worldmap($MAPCFG, $map_name, &$map_config, $obj_id) {
    global $DB;
    worldmap_init_db();
    $q = 'SELECT COUNT(*) AS num FROM objects WHERE object_id='.$DB->escape($obj_id);
    return $DB->count($q) > 0;
}

function del_obj_worldmap($MAPCFG, $map_name, &$map_config, $obj_id) {
    global $DB;
    worldmap_init_db();

    $q = 'DELETE FROM objects WHERE object_id='.$DB->escape($obj_id);
    if ($DB->exec($q))
        return true;
    else
        throw new WorldmapError(l('Failed to delete object: [E]: [Q]', array(
            'E' => json_encode($DB->error()), 'Q' => $q)));
}

function add_obj_worldmap($MAPCFG, $map_name, &$map_config, $obj_id) {
    global $DB;
    worldmap_init_db();
    $obj = $map_config[$obj_id];

    if ($obj['type'] == 'global')
        return false; // adding global section (during map creation)

    $lat  = $obj['x'];
    $lng  = $obj['y'];
    $lat2 = 'NULL';
    $lng2 = 'NULL';

    // Handle lines and so on
    if ($MAPCFG->getValue($obj_id, 'view_type') == 'line' || $obj['type'] == 'line') {
        $x = explode(',', $obj['x']);
        $y = explode(',', $obj['y']);
        $lat  = $x[0];
        $lng  = $y[0];
        $lat2 = $x[count($x)-1];
        $lng2 = $y[count($y)-1];
    }

    $q = 'INSERT INTO objects (object_id, lat, lng, lat2, lng2, object)'
        .' VALUES'
        .'    ('.$DB->escape($obj_id).','
        .'     '.$DB->escape($lat).','
        .'     '.$DB->escape($lng).','
        .'     '.$lat2.','
        .'     '.$lng2.','
        .'     '.$DB->escape(json_encode($obj)).')';

    if ($DB->exec($q))
        return true;
    else
        throw new WorldmapError(l('Failed to add object: [E]: [Q]', array(
            'E' => json_encode($DB->error()), 'Q' => $q)));
}

function process_worldmap($MAPCFG, $map_name, &$map_config) {
    $bbox = val($_GET, 'bbox', null);
    if ($bbox === null)
        return; // do nothing

    list($sw_lng, $sw_lat, $ne_lng, $ne_lat) = explode(',', $bbox);
    $map_config = array_merge($map_config, worldmap_get_objects_by_bounds($sw_lng, $sw_lat, $ne_lng, $ne_lat));
}

function changed_worldmap($MAPCFG, $compare_time) {
    return true; // some kind of cache possible?
}

?>
