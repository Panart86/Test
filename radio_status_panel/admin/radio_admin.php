<?php
/*-------------------------------------------------------+
| PHP-Fusion Content Management System
| Copyright (C) PHP-Fusion Inc
| https://www.php-fusion.co.uk/
+--------------------------------------------------------+
| Filename: radio_admin.php
| Author: Radio Status Panel Infusion
+--------------------------------------------------------*/
require_once "../../../maincore.php";

pageAccess('RSP');

require_once THEMES."templates/admin_header.php";
require_once INFUSIONS."radio_status_panel/infusion_db.php";
require_once RADIO_STATUS_LOCALE;

add_to_title($locale['global_200'].$locale['RSP_title']);

// Custom CSS
add_to_head("<style>
.radio-admin-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 30px;
    border-radius: 8px;
    margin-bottom: 30px;
    box-shadow: 0 4px 6px rgba(0,0,0,0.1);
}
.radio-admin-header h2 {
    margin: 0 0 10px 0;
    font-size: 28px;
    font-weight: bold;
}
.radio-admin-header p {
    margin: 0;
    opacity: 0.9;
}
.radio-stat-card {
    background: white;
    border-radius: 8px;
    padding: 20px;
    margin-bottom: 20px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    transition: all 0.3s ease;
}
.radio-stat-card:hover {
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    transform: translateY(-2px);
}
.radio-stat-icon {
    font-size: 36px;
    margin-bottom: 10px;
}
.radio-stat-icon.streams { color: #3498db; }
.radio-stat-icon.active { color: #27ae60; }
.radio-stat-icon.inactive { color: #95a5a6; }
.radio-stat-icon.settings { color: #e67e22; }
.radio-stat-number {
    font-size: 32px;
    font-weight: bold;
    margin: 10px 0;
}
.radio-stat-label {
    color: #7f8c8d;
    font-size: 14px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}
.stream-card {
    background: white;
    border-radius: 8px;
    padding: 20px;
    margin-bottom: 15px;
    border-left: 4px solid #3498db;
    box-shadow: 0 2px 4px rgba(0,0,0,0.08);
    transition: all 0.3s ease;
}
.stream-card:hover {
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    border-left-color: #2980b9;
}
.stream-card.inactive {
    border-left-color: #95a5a6;
    opacity: 0.7;
}
.stream-card-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 15px;
}
.stream-card-title {
    font-size: 18px;
    font-weight: bold;
    margin: 0;
    display: flex;
    align-items: center;
    gap: 10px;
}
.stream-card-meta {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
    gap: 15px;
    margin-top: 15px;
}
.stream-meta-item {
    display: flex;
    align-items: center;
    gap: 8px;
    color: #7f8c8d;
    font-size: 14px;
}
.stream-meta-item i {
    color: #3498db;
}
.action-buttons {
    display: flex;
    gap: 5px;
}
.form-section {
    background: #f8f9fa;
    padding: 20px;
    border-radius: 8px;
    margin-bottom: 20px;
}
.form-section-title {
    font-size: 16px;
    font-weight: bold;
    margin-bottom: 15px;
    color: #2c3e50;
    border-bottom: 2px solid #3498db;
    padding-bottom: 10px;
}
</style>");

// Actions
$action = isset($_GET['action']) ? $_GET['action'] : '';
$radio_id = isset($_GET['radio_id']) && isnum($_GET['radio_id']) ? intval($_GET['radio_id']) : 0;

// Delete stream
if ($action == 'delete' && $radio_id) {
    if (isset($_POST['confirm'])) {
        dbquery("DELETE FROM ".DB_RADIO_STATUS." WHERE radio_id='".$radio_id."'");
        addNotice('success', $locale['RSP_stream_deleted']);
        redirect(FUSION_SELF.fusion_get_aidlink());
    } else {
        opentable($locale['RSP_delete_stream']);
        echo "<div class='well text-center' style='padding:40px;'>";
        echo "<i class='fa fa-trash fa-3x text-danger' style='margin-bottom:20px;'></i>";
        echo "<h3>".$locale['RSP_delete_stream']."?</h3>";
        echo "<p style='color:#7f8c8d;'>Diese Aktion kann nicht rückgängig gemacht werden.</p>";
        echo openform('delete_form', 'post', FUSION_SELF.fusion_get_aidlink().'&amp;action=delete&amp;radio_id='.$radio_id);
        echo "<div style='margin-top:20px;'>";
        echo form_button('confirm', $locale['RSP_delete'], 'confirm', ['class' => 'btn-danger btn-lg m-r-10', 'icon' => 'fa fa-trash']);
        echo "<a href='".FUSION_SELF.fusion_get_aidlink()."' class='btn btn-default btn-lg'><i class='fa fa-times'></i> ".$locale['RSP_cancel']."</a>";
        echo "</div>";
        echo closeform();
        echo "</div>";
        closetable();
        require_once THEMES."templates/admin_footer.php";
        exit;
    }
}

// Edit/Add stream
if ($action == 'edit' || $action == 'add') {
    $data = [
        'radio_id' => 0,
        'radio_name' => '',
        'radio_server' => '',
        'radio_port' => 8000,
        'radio_mount' => '/',
        'radio_type' => 'shoutcast1',
        'radio_password' => '',
        'radio_status' => 1,
        'radio_order' => 0
    ];

    if ($action == 'edit' && $radio_id) {
        $result = dbquery("SELECT * FROM ".DB_RADIO_STATUS." WHERE radio_id='".$radio_id."'");
        if (dbrows($result)) {
            $data = dbarray($result);
        } else {
            redirect(FUSION_SELF.fusion_get_aidlink());
        }
    }

    if (isset($_POST['save_stream'])) {
        $radio_name = stripinput($_POST['radio_name']);
        $radio_server = stripinput($_POST['radio_server']);
        $radio_port = isnum($_POST['radio_port']) ? intval($_POST['radio_port']) : 8000;
        $radio_mount = stripinput($_POST['radio_mount']);
        $radio_type = stripinput($_POST['radio_type']);
        $radio_password = stripinput($_POST['radio_password']);
        $radio_status = isset($_POST['radio_status']) ? 1 : 0;
        $radio_order = isnum($_POST['radio_order']) ? intval($_POST['radio_order']) : 0;

        if ($radio_name && $radio_server) {
            if ($action == 'edit' && $radio_id) {
                dbquery("UPDATE ".DB_RADIO_STATUS." SET
                    radio_name='".$radio_name."',
                    radio_server='".$radio_server."',
                    radio_port='".$radio_port."',
                    radio_mount='".$radio_mount."',
                    radio_type='".$radio_type."',
                    radio_password='".$radio_password."',
                    radio_status='".$radio_status."',
                    radio_order='".$radio_order."'
                    WHERE radio_id='".$radio_id."'");
                addNotice('success', $locale['RSP_stream_updated']);
            } else {
                dbquery("INSERT INTO ".DB_RADIO_STATUS." (radio_name, radio_server, radio_port, radio_mount, radio_type, radio_password, radio_status, radio_order)
                    VALUES ('".$radio_name."', '".$radio_server."', '".$radio_port."', '".$radio_mount."', '".$radio_type."', '".$radio_password."', '".$radio_status."', '".$radio_order."')");
                addNotice('success', $locale['RSP_stream_added']);
            }
            redirect(FUSION_SELF.fusion_get_aidlink());
        } else {
            addNotice('danger', $locale['RSP_error_name']);
        }
    }

    opentable($action == 'edit' ? '<i class="fa fa-edit"></i> '.$locale['RSP_edit_stream'] : '<i class="fa fa-plus"></i> '.$locale['RSP_add_stream']);

    echo openform('stream_form', 'post', FUSION_SELF.fusion_get_aidlink().'&amp;action='.$action.($radio_id ? '&amp;radio_id='.$radio_id : ''));

    // Basic Information
    echo "<div class='form-section'>";
    echo "<div class='form-section-title'><i class='fa fa-info-circle'></i> Basis-Informationen</div>";

    echo "<div class='row'>";
    echo "<div class='col-xs-12 col-sm-6'>";
    echo "<div class='form-group'>";
    echo "<label>".$locale['RSP_stream_name']." <span class='required'>*</span></label>";
    echo "<input type='text' name='radio_name' value='".htmlspecialchars($data['radio_name'])."' class='form-control' placeholder='Mein Radio Stream' required />";
    echo "</div>";
    echo "</div>";

    echo "<div class='col-xs-12 col-sm-3'>";
    echo "<div class='form-group'>";
    echo "<label>".$locale['RSP_order']."</label>";
    echo "<input type='number' name='radio_order' value='".$data['radio_order']."' class='form-control' />";
    echo "</div>";
    echo "</div>";

    echo "<div class='col-xs-12 col-sm-3'>";
    echo "<div class='form-group'>";
    echo "<label>".$locale['RSP_status']."</label><br>";
    echo "<label class='label-checkbox' style='display:inline-block;margin-top:7px;'>";
    echo "<input type='checkbox' name='radio_status' value='1'".($data['radio_status'] ? ' checked' : '')." />";
    echo " <i class='fa fa-check-square-o'></i> ".$locale['RSP_active'];
    echo "</label>";
    echo "</div>";
    echo "</div>";
    echo "</div>";
    echo "</div>";

    // Server Configuration
    echo "<div class='form-section'>";
    echo "<div class='form-section-title'><i class='fa fa-server'></i> Server-Konfiguration</div>";

    echo "<div class='row'>";
    echo "<div class='col-xs-12 col-sm-6'>";
    echo "<div class='form-group'>";
    echo "<label>".$locale['RSP_server']." <span class='required'>*</span></label>";
    echo "<input type='text' name='radio_server' value='".htmlspecialchars($data['radio_server'])."' class='form-control' placeholder='stream.example.com' required />";
    echo "</div>";
    echo "</div>";

    echo "<div class='col-xs-12 col-sm-3'>";
    echo "<div class='form-group'>";
    echo "<label>".$locale['RSP_port']." <span class='required'>*</span></label>";
    echo "<input type='number' name='radio_port' value='".$data['radio_port']."' class='form-control' required />";
    echo "</div>";
    echo "</div>";

    echo "<div class='col-xs-12 col-sm-3'>";
    echo "<div class='form-group'>";
    echo "<label>".$locale['RSP_mount']."</label>";
    echo "<input type='text' name='radio_mount' value='".htmlspecialchars($data['radio_mount'])."' class='form-control' placeholder='/' />";
    echo "</div>";
    echo "</div>";
    echo "</div>";

    echo "<div class='row'>";
    echo "<div class='col-xs-12 col-sm-6'>";
    echo "<div class='form-group'>";
    echo "<label>".$locale['RSP_type']."</label>";
    echo "<select name='radio_type' class='form-control'>";
    echo "<option value='shoutcast1'".($data['radio_type'] == 'shoutcast1' ? ' selected' : '').">Shoutcast v1</option>";
    echo "<option value='shoutcast2'".($data['radio_type'] == 'shoutcast2' ? ' selected' : '').">Shoutcast v2</option>";
    echo "<option value='icecast'".($data['radio_type'] == 'icecast' ? ' selected' : '').">Icecast</option>";
    echo "</select>";
    echo "</div>";
    echo "</div>";

    echo "<div class='col-xs-12 col-sm-6'>";
    echo "<div class='form-group'>";
    echo "<label>".$locale['RSP_password']." <small class='text-muted'>(Optional)</small></label>";
    echo "<input type='password' name='radio_password' value='".htmlspecialchars($data['radio_password'])."' class='form-control' autocomplete='off' placeholder='Admin-Passwort für erweiterte Stats' />";
    echo "</div>";
    echo "</div>";
    echo "</div>";
    echo "</div>";

    echo "<div class='m-t-20'>";
    echo form_button('save_stream', $locale['RSP_save'], $locale['RSP_save'], ['class' => 'btn-primary btn-lg m-r-10', 'icon' => 'fa fa-check']);
    echo "<a href='".FUSION_SELF.fusion_get_aidlink()."' class='btn btn-default btn-lg'><i class='fa fa-times'></i> ".$locale['RSP_cancel']."</a>";
    echo "</div>";

    echo closeform();
    closetable();
    require_once THEMES."templates/admin_footer.php";
    exit;
}

// Settings
if ($action == 'settings') {
    if (isset($_POST['save_settings'])) {
        $settings = [
            'refresh_interval' => isnum($_POST['refresh_interval']) ? intval($_POST['refresh_interval']) : 10,
            'show_listeners' => isset($_POST['show_listeners']) ? 1 : 0,
            'show_current_song' => isset($_POST['show_current_song']) ? 1 : 0,
            'show_max_listeners' => isset($_POST['show_max_listeners']) ? 1 : 0,
            'show_bitrate' => isset($_POST['show_bitrate']) ? 1 : 0,
            'show_genre' => isset($_POST['show_genre']) ? 1 : 0
        ];

        foreach ($settings as $key => $value) {
            $check = dbquery("SELECT settings_name FROM ".DB_RADIO_SETTINGS." WHERE settings_name='".$key."'");
            if (dbrows($check)) {
                dbquery("UPDATE ".DB_RADIO_SETTINGS." SET settings_value='".$value."' WHERE settings_name='".$key."'");
            } else {
                dbquery("INSERT INTO ".DB_RADIO_SETTINGS." (settings_name, settings_value) VALUES ('".$key."', '".$value."')");
            }
        }

        addNotice('success', $locale['RSP_settings_updated']);
        redirect(FUSION_SELF.fusion_get_aidlink());
    }

    $settings = [];
    $result = dbquery("SELECT * FROM ".DB_RADIO_SETTINGS);
    while ($data = dbarray($result)) {
        $settings[$data['settings_name']] = $data['settings_value'];
    }

    opentable('<i class="fa fa-cog"></i> '.$locale['RSP_settings']);

    echo openform('settings_form', 'post', FUSION_SELF.fusion_get_aidlink().'&amp;action=settings');

    echo "<div class='form-section'>";
    echo "<div class='form-section-title'><i class='fa fa-refresh'></i> Aktualisierung</div>";
    echo "<div class='form-group'>";
    echo "<label>".$locale['RSP_refresh_interval']."</label>";
    echo "<div class='input-group' style='width:200px;'>";
    echo "<input type='number' name='refresh_interval' value='".(isset($settings['refresh_interval']) ? $settings['refresh_interval'] : 10)."' class='form-control' min='5' max='300' />";
    echo "<span class='input-group-addon'>Sekunden</span>";
    echo "</div>";
    echo "<small class='text-muted'>Wie oft sollen die Stream-Daten aktualisiert werden? (5-300 Sekunden)</small>";
    echo "</div>";
    echo "</div>";

    echo "<div class='form-section'>";
    echo "<div class='form-section-title'><i class='fa fa-eye'></i> Anzeigeoptionen</div>";
    echo "<div class='row'>";

    $options = [
        ['name' => 'show_listeners', 'icon' => 'fa-users', 'label' => $locale['RSP_show_listeners']],
        ['name' => 'show_current_song', 'icon' => 'fa-music', 'label' => $locale['RSP_show_current_song']],
        ['name' => 'show_max_listeners', 'icon' => 'fa-user-plus', 'label' => $locale['RSP_show_max_listeners']],
        ['name' => 'show_bitrate', 'icon' => 'fa-signal', 'label' => $locale['RSP_show_bitrate']],
        ['name' => 'show_genre', 'icon' => 'fa-tag', 'label' => $locale['RSP_show_genre']]
    ];

    foreach ($options as $opt) {
        echo "<div class='col-xs-12 col-sm-6 col-md-4'>";
        echo "<div class='checkbox'>";
        echo "<label style='font-size:15px;'>";
        echo "<input type='checkbox' name='".$opt['name']."' value='1'".(isset($settings[$opt['name']]) && $settings[$opt['name']] ? ' checked' : '')." />";
        echo " <i class='fa ".$opt['icon']."' style='margin-right:5px;color:#3498db;'></i> ".$opt['label'];
        echo "</label>";
        echo "</div>";
        echo "</div>";
    }

    echo "</div>";
    echo "</div>";

    echo "<div class='m-t-20'>";
    echo form_button('save_settings', $locale['RSP_save'], $locale['RSP_save'], ['class' => 'btn-primary btn-lg m-r-10', 'icon' => 'fa fa-check']);
    echo "<a href='".FUSION_SELF.fusion_get_aidlink()."' class='btn btn-default btn-lg'><i class='fa fa-times'></i> ".$locale['RSP_cancel']."</a>";
    echo "</div>";

    echo closeform();
    closetable();
    require_once THEMES."templates/admin_footer.php";
    exit;
}

// Main Dashboard
// Header
echo "<div class='radio-admin-header'>";
echo "<h2><i class='fa fa-broadcast-tower'></i> ".$locale['RSP_admin_title']."</h2>";
echo "<p>Verwalten Sie Ihre Radio-Streams und Einstellungen</p>";
echo "</div>";

// Statistics
$total_streams = dbcount("(radio_id)", DB_RADIO_STATUS);
$active_streams = dbcount("(radio_id)", DB_RADIO_STATUS, "radio_status='1'");
$inactive_streams = $total_streams - $active_streams;

echo "<div class='row m-b-20'>";

echo "<div class='col-xs-12 col-sm-6 col-md-3'>";
echo "<div class='radio-stat-card text-center'>";
echo "<div class='radio-stat-icon streams'><i class='fa fa-radio'></i></div>";
echo "<div class='radio-stat-number'>".$total_streams."</div>";
echo "<div class='radio-stat-label'>Gesamt Streams</div>";
echo "</div>";
echo "</div>";

echo "<div class='col-xs-12 col-sm-6 col-md-3'>";
echo "<div class='radio-stat-card text-center'>";
echo "<div class='radio-stat-icon active'><i class='fa fa-check-circle'></i></div>";
echo "<div class='radio-stat-number'>".$active_streams."</div>";
echo "<div class='radio-stat-label'>Aktive Streams</div>";
echo "</div>";
echo "</div>";

echo "<div class='col-xs-12 col-sm-6 col-md-3'>";
echo "<div class='radio-stat-card text-center'>";
echo "<div class='radio-stat-icon inactive'><i class='fa fa-pause-circle'></i></div>";
echo "<div class='radio-stat-number'>".$inactive_streams."</div>";
echo "<div class='radio-stat-label'>Inaktive Streams</div>";
echo "</div>";
echo "</div>";

$settings = [];
$result = dbquery("SELECT * FROM ".DB_RADIO_SETTINGS);
while ($data = dbarray($result)) {
    $settings[$data['settings_name']] = $data['settings_value'];
}
$refresh = isset($settings['refresh_interval']) ? $settings['refresh_interval'] : 10;

echo "<div class='col-xs-12 col-sm-6 col-md-3'>";
echo "<div class='radio-stat-card text-center'>";
echo "<div class='radio-stat-icon settings'><i class='fa fa-refresh'></i></div>";
echo "<div class='radio-stat-number'>".$refresh."s</div>";
echo "<div class='radio-stat-label'>Refresh Intervall</div>";
echo "</div>";
echo "</div>";

echo "</div>";

// Action Buttons
opentable($locale['RSP_manage']);

echo "<div class='m-b-20'>";
echo "<a class='btn btn-success btn-lg m-r-10' href='".FUSION_SELF.fusion_get_aidlink()."&amp;action=add'>";
echo "<i class='fa fa-plus'></i> ".$locale['RSP_add_stream'];
echo "</a>";
echo "<a class='btn btn-primary btn-lg' href='".FUSION_SELF.fusion_get_aidlink()."&amp;action=settings'>";
echo "<i class='fa fa-cog'></i> ".$locale['RSP_settings'];
echo "</a>";
echo "</div>";

// List streams
$result = dbquery("SELECT * FROM ".DB_RADIO_STATUS." ORDER BY radio_order ASC, radio_name ASC");

if (dbrows($result)) {
    while ($data = dbarray($result)) {
        $type_label = $data['radio_type'];
        if ($data['radio_type'] == 'shoutcast1') $type_label = 'Shoutcast v1';
        if ($data['radio_type'] == 'shoutcast2') $type_label = 'Shoutcast v2';
        if ($data['radio_type'] == 'icecast') $type_label = 'Icecast';

        echo "<div class='stream-card".($data['radio_status'] ? '' : ' inactive')."'>";
        echo "<div class='stream-card-header'>";
        echo "<div class='stream-card-title'>";
        echo "<i class='fa fa-radio'></i>";
        echo htmlspecialchars($data['radio_name']);
        if ($data['radio_status']) {
            echo " <span class='label label-success'><i class='fa fa-check'></i> Aktiv</span>";
        } else {
            echo " <span class='label label-default'><i class='fa fa-pause'></i> Inaktiv</span>";
        }
        echo "</div>";
        echo "<div class='action-buttons'>";
        echo "<a class='btn btn-sm btn-default' href='".FUSION_SELF.fusion_get_aidlink()."&amp;action=edit&amp;radio_id=".$data['radio_id']."' title='Bearbeiten'>";
        echo "<i class='fa fa-pencil'></i>";
        echo "</a>";
        echo "<a class='btn btn-sm btn-danger' href='".FUSION_SELF.fusion_get_aidlink()."&amp;action=delete&amp;radio_id=".$data['radio_id']."' title='Löschen'>";
        echo "<i class='fa fa-trash'></i>";
        echo "</a>";
        echo "</div>";
        echo "</div>";

        echo "<div class='stream-card-meta'>";
        echo "<div class='stream-meta-item'>";
        echo "<i class='fa fa-server'></i>";
        echo "<span><strong>Server:</strong> ".htmlspecialchars($data['radio_server']).":".htmlspecialchars($data['radio_port'])."</span>";
        echo "</div>";
        echo "<div class='stream-meta-item'>";
        echo "<i class='fa fa-folder-open'></i>";
        echo "<span><strong>Mount:</strong> ".htmlspecialchars($data['radio_mount'])."</span>";
        echo "</div>";
        echo "<div class='stream-meta-item'>";
        echo "<i class='fa fa-code'></i>";
        echo "<span><strong>Typ:</strong> ".$type_label."</span>";
        echo "</div>";
        echo "<div class='stream-meta-item'>";
        echo "<i class='fa fa-sort-numeric-asc'></i>";
        echo "<span><strong>Reihenfolge:</strong> ".$data['radio_order']."</span>";
        echo "</div>";
        echo "</div>";

        echo "</div>";
    }
} else {
    echo "<div class='well text-center' style='padding:60px;'>";
    echo "<i class='fa fa-broadcast-tower fa-4x text-muted' style='margin-bottom:20px;'></i>";
    echo "<h3>".$locale['RSP_no_streams']."</h3>";
    echo "<p class='text-muted'>Klicken Sie auf 'Stream hinzufügen' um Ihren ersten Radio-Stream zu konfigurieren.</p>";
    echo "</div>";
}

closetable();
