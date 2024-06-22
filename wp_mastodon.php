<?php
/*
Plugin Name: Mastodon Profile Shortcode
Description: Zeigt Mastodon-Benutzerprofile über einen Shortcode an.
Version: 0.0.1
Author: Joachim Happel
*/

// Einbinden der Optionsseite
require_once plugin_dir_path(__FILE__) . 'option_page.php';

// Shortcode-Funktion
function mastodon_profile_shortcode($atts) {
    $atts = shortcode_atts(array(
        'name' => '',
        'user' => '',
    ), $atts);

    $instances = get_option('mastodon_instances', array());
    $instance = isset($instances[$atts['name']]) ? $instances[$atts['name']] : null;

    if (!$instance) {
        return 'Mastodon-Instanz nicht gefunden.';
    }

    $base_url = $instance['url'];
    $access_token = $instance['token'];

    $headers = array('Authorization' => "Bearer $access_token");
    $search_url = "$base_url/api/v2/search";
    $params = array(
        'q' => $atts['user'],
        'type' => 'accounts',
        'limit' => 1
    );

    $response = wp_remote_get(add_query_arg($params, $search_url), array('headers' => $headers));

    if (is_wp_error($response)) {
        return 'Fehler beim Abrufen des Profils.';
    }

    $body = wp_remote_retrieve_body($response);
    $results = json_decode($body, true);

    if (empty($results['accounts'])) {
        return 'Kein Benutzer gefunden.';
    }

    $user = $results['accounts'][0];
    $template = get_option('mastodon_profile_template', '');

    // Ersetzen der Platzhalter im Template
    $replacements = array(
        '%displayname%' => $user['display_name'],
        '%username%' => $user['acct'],
        '%id%' => $user['id'],
        '%followers%' => $user['followers_count'],
        '%following%' => $user['following_count'],
        '%statuses%' => $user['statuses_count'],
        '%note%' => $user['note'],
        '%url%' => $user['url']
    );

    $output = str_replace(array_keys($replacements), array_values($replacements), $template);

    return $output;
}
add_shortcode('mastodon', 'mastodon_profile_shortcode');
