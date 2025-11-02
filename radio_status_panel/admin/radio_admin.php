<?php
/*-------------------------------------------------------+
| PHP-Fusion Content Management System
| Copyright (C) PHP-Fusion Inc
| https://www.php-fusion.co.uk/
+--------------------------------------------------------+
| Filename: radio_admin.php
| Author: Radio Status Panel Infusion
+--------------------------------------------------------*/
require_once "../../maincore.php";

pageAccess('RSP');

require_once THEMES."templates/admin_header.php";
require_once INFUSIONS."radio_status_panel/infusion_db.php";
require_once RADIO_STATUS_LOCALE;

// Breadcrumbs
add_to_title($locale['global_200'].$locale['RSP_title']);
\PHPFusion\BreadCrumbs::getInstance()->addBreadCrumb([
    'link' => INFUSIONS.'radio_status_panel/admin/radio_admin.php'.fusion_get_aidlink(),
    'title' => $locale['RSP_title']
]);

// Actions
$action = isset($_GET['action']) ? $_GET['action'] : '';
$radio_id = isset($_GET['radio_id']) && isnum($_GET['radio_id']) ? $_GET['radio_id'] : 0;

// Delete stream
if ($action == 'delete' && $radio_id) {
    if (isset($_POST['confirm'])) {
        dbquery("DELETE FROM ".DB_RADIO_STATUS." WHERE radio_id='".$radio_id."'");
        addNotice('success', $locale['RSP_stream_deleted']);
        redirect(clean_request('', ['action', 'radio_id'], false));
    } else {
        opentable($locale['RSP_delete_stream']);
        echo "<div class='well text-center'>";
        echo "<p>".$locale['RSP_delete_stream']."?</p>";
        echo openform('delete_form', 'post', FUSION_REQUEST);
        echo form_button('confirm', $locale['RSP_delete'], 'confirm', ['class' => 'btn-danger m-r-10']);
        echo form_button('cancel', $locale['RSP_cancel'], 'cancel', ['class' => 'btn-default', 'type' => 'button', 'onclick' => 'history.back()']);
        echo closeform();
        echo "</div>";
        closetable();
    }
}

// Edit/Add stream
else if ($action == 'edit' || $action == 'add') {
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
        }
    }

    if (isset($_POST['save_stream'])) {
        $input = [
            'radio_name' => form_sanitizer($_POST['radio_name'], '', 'radio_name'),
            'radio_server' => form_sanitizer($_POST['radio_server'], '', 'radio_server'),
            'radio_port' => form_sanitizer($_POST['radio_port'], 8000, 'radio_port'),
            'radio_mount' => form_sanitizer($_POST['radio_mount'], '/', 'radio_mount'),
            'radio_type' => form_sanitizer($_POST['radio_type'], 'shoutcast1', 'radio_type'),
            'radio_password' => form_sanitizer($_POST['radio_password'], '', 'radio_password'),
            'radio_status' => isset($_POST['radio_status']) ? 1 : 0,
            'radio_order' => form_sanitizer($_POST['radio_order'], 0, 'radio_order')
        ];

        if (defender::safe()) {
            if ($action == 'edit' && $radio_id) {
                // Update existing stream
                $input['radio_id'] = $radio_id;
                dbquery_insert(DB_RADIO_STATUS, $input, 'update');
                addNotice('success', $locale['RSP_stream_updated']);
            } else {
                // Add new stream
                dbquery_insert(DB_RADIO_STATUS, $input, 'save');
                addNotice('success', $locale['RSP_stream_added']);
            }
            redirect(clean_request('', ['action', 'radio_id'], false));
        }
    }

    opentable($action == 'edit' ? $locale['RSP_edit_stream'] : $locale['RSP_add_stream']);

    echo openform('stream_form', 'post', FUSION_REQUEST);
    echo "<div class='row'>";
    echo "<div class='col-xs-12 col-sm-8'>";

    echo form_text('radio_name', $locale['RSP_stream_name'], $data['radio_name'], [
        'required' => true,
        'error_text' => $locale['RSP_error_name'],
        'inline' => true
    ]);

    echo form_text('radio_server', $locale['RSP_server'], $data['radio_server'], [
        'required' => true,
        'error_text' => $locale['RSP_error_server'],
        'placeholder' => 'example.com',
        'inline' => true
    ]);

    echo form_text('radio_port', $locale['RSP_port'], $data['radio_port'], [
        'required' => true,
        'type' => 'number',
        'error_text' => $locale['RSP_error_port'],
        'inline' => true,
        'width' => '150px'
    ]);

    echo form_text('radio_mount', $locale['RSP_mount'], $data['radio_mount'], [
        'inline' => true,
        'width' => '200px',
        'placeholder' => '/'
    ]);

    echo form_select('radio_type', $locale['RSP_type'], $data['radio_type'], [
        'options' => [
            'shoutcast1' => 'Shoutcast v1',
            'shoutcast2' => 'Shoutcast v2',
            'icecast' => 'Icecast'
        ],
        'inline' => true
    ]);

    echo form_text('radio_password', $locale['RSP_password'], $data['radio_password'], [
        'type' => 'password',
        'inline' => true,
        'autocomplete_off' => true
    ]);

    echo form_text('radio_order', $locale['RSP_order'], $data['radio_order'], [
        'type' => 'number',
        'inline' => true,
        'width' => '100px'
    ]);

    echo form_checkbox('radio_status', $locale['RSP_status'], $data['radio_status'], [
        'inline' => true
    ]);

    echo "</div>";
    echo "</div>";

    echo form_button('save_stream', $locale['RSP_save'], $locale['RSP_save'], ['class' => 'btn-primary m-r-10']);
    echo form_button('cancel', $locale['RSP_cancel'], $locale['RSP_cancel'], ['class' => 'btn-default', 'type' => 'button', 'onclick' => 'history.back()']);

    echo closeform();
    closetable();
}

// Settings
else if ($action == 'settings') {
    if (isset($_POST['save_settings'])) {
        $input = [
            'refresh_interval' => form_sanitizer($_POST['refresh_interval'], 10, 'refresh_interval'),
            'show_listeners' => isset($_POST['show_listeners']) ? 1 : 0,
            'show_current_song' => isset($_POST['show_current_song']) ? 1 : 0,
            'show_max_listeners' => isset($_POST['show_max_listeners']) ? 1 : 0,
            'show_bitrate' => isset($_POST['show_bitrate']) ? 1 : 0,
            'show_genre' => isset($_POST['show_genre']) ? 1 : 0
        ];

        if (defender::safe()) {
            foreach ($input as $key => $value) {
                $result = dbquery("SELECT settings_name FROM ".DB_RADIO_SETTINGS." WHERE settings_name='".$key."'");
                if (dbrows($result)) {
                    dbquery("UPDATE ".DB_RADIO_SETTINGS." SET settings_value='".$value."' WHERE settings_name='".$key."'");
                } else {
                    dbquery("INSERT INTO ".DB_RADIO_SETTINGS." (settings_name, settings_value) VALUES ('".$key."', '".$value."')");
                }
            }
            addNotice('success', $locale['RSP_settings_updated']);
            redirect(FUSION_REQUEST);
        }
    }

    // Load current settings
    $settings = [];
    $result = dbquery("SELECT * FROM ".DB_RADIO_SETTINGS);
    while ($data = dbarray($result)) {
        $settings[$data['settings_name']] = $data['settings_value'];
    }

    opentable($locale['RSP_settings']);

    echo openform('settings_form', 'post', FUSION_REQUEST);
    echo "<div class='row'>";
    echo "<div class='col-xs-12 col-sm-8'>";

    echo form_text('refresh_interval', $locale['RSP_refresh_interval'],
        isset($settings['refresh_interval']) ? $settings['refresh_interval'] : 10, [
        'type' => 'number',
        'inline' => true,
        'width' => '150px'
    ]);

    echo form_checkbox('show_listeners', $locale['RSP_show_listeners'],
        isset($settings['show_listeners']) ? $settings['show_listeners'] : 1, [
        'inline' => true
    ]);

    echo form_checkbox('show_current_song', $locale['RSP_show_current_song'],
        isset($settings['show_current_song']) ? $settings['show_current_song'] : 1, [
        'inline' => true
    ]);

    echo form_checkbox('show_max_listeners', $locale['RSP_show_max_listeners'],
        isset($settings['show_max_listeners']) ? $settings['show_max_listeners'] : 1, [
        'inline' => true
    ]);

    echo form_checkbox('show_bitrate', $locale['RSP_show_bitrate'],
        isset($settings['show_bitrate']) ? $settings['show_bitrate'] : 1, [
        'inline' => true
    ]);

    echo form_checkbox('show_genre', $locale['RSP_show_genre'],
        isset($settings['show_genre']) ? $settings['show_genre'] : 1, [
        'inline' => true
    ]);

    echo "</div>";
    echo "</div>";

    echo form_button('save_settings', $locale['RSP_save'], $locale['RSP_save'], ['class' => 'btn-primary']);
    echo closeform();
    closetable();
}

// List streams
else {
    opentable($locale['RSP_admin_title']);

    // Add button
    echo "<div class='m-b-20'>";
    echo "<a class='btn btn-success' href='".clean_request('action=add', ['action', 'radio_id'], false)."'>";
    echo "<i class='fa fa-plus'></i> ".$locale['RSP_add_stream'];
    echo "</a> ";
    echo "<a class='btn btn-default' href='".clean_request('action=settings', ['action', 'radio_id'], false)."'>";
    echo "<i class='fa fa-cog'></i> ".$locale['RSP_settings'];
    echo "</a>";
    echo "</div>";

    // List streams
    $result = dbquery("SELECT * FROM ".DB_RADIO_STATUS." ORDER BY radio_order ASC, radio_name ASC");

    if (dbrows($result)) {
        echo "<table class='table table-responsive table-striped'>";
        echo "<thead>";
        echo "<tr>";
        echo "<th>".$locale['RSP_stream_name']."</th>";
        echo "<th>".$locale['RSP_server']."</th>";
        echo "<th>".$locale['RSP_type']."</th>";
        echo "<th>".$locale['RSP_status']."</th>";
        echo "<th class='text-right'>".$locale['RSP_order']."</th>";
        echo "<th class='text-center'>Actions</th>";
        echo "</tr>";
        echo "</thead>";
        echo "<tbody>";

        while ($data = dbarray($result)) {
            $status_badge = $data['radio_status'] ? "<span class='label label-success'>".$locale['RSP_active']."</span>" :
                                                    "<span class='label label-default'>".$locale['RSP_inactive']."</span>";

            $type_label = $data['radio_type'];
            if ($data['radio_type'] == 'shoutcast1') $type_label = 'Shoutcast v1';
            if ($data['radio_type'] == 'shoutcast2') $type_label = 'Shoutcast v2';
            if ($data['radio_type'] == 'icecast') $type_label = 'Icecast';

            echo "<tr>";
            echo "<td><strong>".htmlspecialchars($data['radio_name'])."</strong></td>";
            echo "<td>".htmlspecialchars($data['radio_server']).":".htmlspecialchars($data['radio_port'])."</td>";
            echo "<td>".$type_label."</td>";
            echo "<td>".$status_badge."</td>";
            echo "<td class='text-right'>".$data['radio_order']."</td>";
            echo "<td class='text-center'>";
            echo "<a class='btn btn-sm btn-default' href='".clean_request('action=edit&radio_id='.$data['radio_id'], ['action', 'radio_id'], false)."'>";
            echo "<i class='fa fa-pencil'></i> ".$locale['RSP_edit'];
            echo "</a> ";
            echo "<a class='btn btn-sm btn-danger' href='".clean_request('action=delete&radio_id='.$data['radio_id'], ['action', 'radio_id'], false)."'>";
            echo "<i class='fa fa-trash'></i> ".$locale['RSP_delete'];
            echo "</a>";
            echo "</td>";
            echo "</tr>";
        }

        echo "</tbody>";
        echo "</table>";
    } else {
        echo "<div class='well text-center'>";
        echo $locale['RSP_no_streams'];
        echo "</div>";
    }

    closetable();
}

require_once THEMES."templates/admin_footer.php";
