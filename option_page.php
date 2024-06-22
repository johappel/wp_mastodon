<?php
// Füge Menüpunkt hinzu
function mastodon_profile_menu() {
    add_options_page('Mastodon Profile Einstellungen', 'Mastodon Profile', 'manage_options', 'mastodon-profile-settings', 'mastodon_profile_options_page');
}
add_action('admin_menu', 'mastodon_profile_menu');

// Registriere Einstellungen
function mastodon_profile_register_settings() {
    register_setting('mastodon_profile_options', 'mastodon_instances');
    register_setting('mastodon_profile_options', 'mastodon_profile_template');
}
add_action('admin_init', 'mastodon_profile_register_settings');

// Optionsseite HTML
function mastodon_profile_options_page() {
    ?>
    <div class="wrap">
        <h1>Mastodon Profile Einstellungen</h1>
        <form method="post" action="options.php">
            <?php settings_fields('mastodon_profile_options'); ?>
            <?php do_settings_sections('mastodon_profile_options'); ?>

            <h2>Mastodon Instanzen</h2>
            <?php
            $instances = get_option('mastodon_instances', array());
            foreach ($instances as $name => $instance) {
                echo "<div>";
                echo "<input type='text' name='mastodon_instances[$name][name]' value='$name' placeholder='Name'>";
                echo "<input type='text' name='mastodon_instances[$name][url]' value='{$instance['url']}' placeholder='URL'>";
                echo "<input type='text' name='mastodon_instances[$name][token]' value='{$instance['token']}' placeholder='Token'>";
                echo "</div>";
            }
            ?>
            <div>
                <input type="text" name="mastodon_instances[new][name]" placeholder="Neuer Name">
                <input type="text" name="mastodon_instances[new][url]" placeholder="Neue URL">
                <input type="text" name="mastodon_instances[new][token]" placeholder="Neuer Token">
            </div>

            <p>Hilfe für die Eingabe: <a href="https://mastodon.social/settings/applications/new" target="_blank">Access token generieren</a></p>

            <h2>Profil Template</h2>
            <textarea name="mastodon_profile_template" rows="10" cols="50"><?php echo esc_textarea(get_option('mastodon_profile_template', '')); ?></textarea>

            <p>Verfügbare Platzhalter: %displayname%, %username%, %id%, %followers%, %following%, %statuses%, %note%, %url%</p>

            <?php submit_button(); ?>
        </form>
    </div>
    <?php
}
