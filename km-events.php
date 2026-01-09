<?php
/**
 * Plugin Name: Konzertmeister Events
 * Description: Holt die Konzertmeister-Termine als HTML und rendert sie ohne iFrame.
 *              Styling per CSS-Variablen; Farben, Trenner, Rahmen & Hover im Admin (KM Events).
 * Version:     2.6.0
 * Requires at least: 6.5
 * Requires PHP: 8.0
 * License:     GPLv2 or later
 * Author:      Pascal Heitzmann
 * Author URI:  https://heizi.ch/
 */

if (!defined('ABSPATH')) { exit; }

define('KME_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('KME_PLUGIN_URL', plugin_dir_url(__FILE__));
define('KME_OPTION_KEY', 'kme_options');

/** Defaults – dein Standard */
function kme_default_options() {
  return [
    // Allgemeine Farben (HEX/RGB/RGBA/HSL)
    'text_color'         => '#0E1111',
    'background_color'   => '#E9E6ED',
    'weekday_badge_bg'   => '#9B82D9',
    'separator_color'    => '#9B82D9',

    // Toggles
    'enable_background'  => 1,
    'show_location'      => 1,

    // Horizontale Trenner
    'sep_h_enabled'      => 1,
    'sep_h_width'        => 1,   // px

    // Vertikaler Trenner
    'sep_v_enabled'      => 1,
    'sep_v_width'        => 5,   // px

    // Rahmen
    'border_enabled'     => 0,   // 0 per default
    'border_color'       => '#0E1111',
    'border_width'       => 1,   // px
    'border_radius'      => 12,  // px

    // Hover
    'hover_effect'       => 'none', // none|glow|lift|shade|underline

    // KM URL
    'km_url'             => '',
  ];
}
function kme_get_options() {
  $defaults = kme_default_options();
  $saved = get_option(KME_OPTION_KEY, []);
  return array_merge($defaults, is_array($saved) ? $saved : []);
}

/** Shortcode: [km_events] */
add_shortcode('km_events', function() {
  $o = kme_get_options();

  // Frontend-CSS laden
  wp_enqueue_style('konzertmeister-events', KME_PLUGIN_URL . 'km-events.css', [], '2.6.0');

  // CSS-Variablen generieren
  $bg   = !empty($o['enable_background']) ? $o['background_color'] : 'transparent';
  $bw   = max(0, (int)$o['border_width']).'px';
  $br   = max(0, (int)$o['border_radius']).'px';
  $sepH = max(0, (int)$o['sep_h_width']).'px';
  $sepV = max(0, (int)$o['sep_v_width']).'px';

  $inline = ":root{"
    ."--kme-text:{$o['text_color']};"
    ."--kme-bg:{$bg};"
    ."--kme-badge:{$o['weekday_badge_bg']};"
    ."--kme-sep:{$o['separator_color']};"
    ."--kme-border-color:{$o['border_color']};"
    ."--kme-border-width:{$bw};"
    ."--kme-border-radius:{$br};"
    ."--kme-sep-h-width:{$sepH};"
    ."--kme-sep-v-width:{$sepV};"
  ."}";
  wp_add_inline_style('konzertmeister-events', $inline);

  // Wrapper-Klassen
  $cls = trim(
    (!empty($o['show_location']) ? 'kme-show-location' : 'kme-hide-location').' '.
    (!empty($o['border_enabled'])? 'kme-border-on' : 'kme-border-off').' '.
    (!empty($o['sep_h_enabled']) ? 'kme-sep-h-on'  : 'kme-sep-h-off').' '.
    (!empty($o['sep_v_enabled']) ? 'kme-sep-v-on'  : 'kme-sep-v-off').' '.
    ('kme-hover-'.preg_replace('/[^a-z]/','', strtolower($o['hover_effect'])))
  );

  // Falls keine URL gesetzt wurde
  if (empty($o['km_url'])) {
    $msg = current_user_can('manage_options')
      ? 'Bitte die Konzertmeister-URL unter <strong>KM Events</strong> eintragen.'
      : 'Aktuell sind keine Termine vorhanden.';
    return '<div class="km-appointment-list proxied '.$cls.'"><div class="km-no-events">'.$msg.'</div></div>';
  }

  // KM abrufen
  $ua = 'WP-Konzertmeister-Events/2.6.0 (+'.get_bloginfo('name').')';
  $resp = wp_remote_get($o['km_url'], [
    'timeout'   => 12,
    'headers'   => ['Accept'=>'text/html','User-Agent'=>$ua],
    'sslverify' => true,
  ]);
  if (is_wp_error($resp)) {
    return '<div class="km-appointment-list proxied '.$cls.'"><div class="km-no-events">Fehler beim Laden der Termine.</div></div>';
  }
  $html = wp_remote_retrieve_body($resp);
  if (!is_string($html) || trim($html)==='') {
    return '<div class="km-appointment-list proxied '.$cls.'"><div class="km-no-events">Aktuell sind keine Termine vorhanden.</div></div>';
  }

  // Bereinigung & Extrakt der km-appointment-list
  $clean_html = $html;
  if (class_exists('DOMDocument')) {
    libxml_use_internal_errors(true);
    $dom = new DOMDocument('1.0','UTF-8');
    $dom->loadHTML('<?xml encoding="UTF-8">'.$html, LIBXML_HTML_NOIMPLIED|LIBXML_HTML_NODEFDTD);
    $xp = new DOMXPath($dom);

    // Unerwünschte Tags entfernen
    foreach (['script','style','link','meta','base','iframe','noscript'] as $tag) {
      $nodes = $dom->getElementsByTagName($tag);
      for ($i=$nodes->length-1; $i>=0; $i--) {
        $n=$nodes->item($i);
        if ($n && $n->parentNode) { $n->parentNode->removeChild($n); }
      }
    }
    // Footer/Branding
    foreach ($xp->query('//*[contains(concat(" ", normalize-space(@class), " "), " list-footer ")]') as $n) {
      if ($n->parentNode) { $n->parentNode->removeChild($n); }
    }
    // Nur der Inhalt innerhalb des Containers
    $container = $xp->query('//*[contains(concat(" ", normalize-space(@class), " "), " km-appointment-list ")]')->item(0);
    if ($container) {
      $inner = '';
      foreach ($container->childNodes as $child) { $inner .= $dom->saveHTML($child); }
      $clean_html = $inner;
    } else {
      $clean_html = $dom->saveHTML();
    }
    libxml_clear_errors();
  }

  // Whitelist
  $allowed = [
    'div'=>['class'=>[],'id'=>[],'style'=>[]],
    'section'=>['class'=>[],'id'=>[],'style'=>[]],
    'article'=>['class'=>[],'id'=>[],'style'=>[]],
    'header'=>['class'=>[],'id'=>[],'style'=>[]],
    'footer'=>['class'=>[],'id'=>[],'style'=>[]],
    'p'=>['class'=>[],'style'=>[]],
    'span'=>['class'=>[],'style'=>[]],
    'strong'=>['class'=>[],'style'=>[]],
    'em'=>['class'=>[],'style'=>[]],
    'b'=>['class'=>[],'style'=>[]],
    'i'=>['class'=>[],'style'=>[]],
    'u'=>['class'=>[],'style'=>[]],
    'small'=>['class'=>[],'style'=>[]],
    'sup'=>['class'=>[],'style'=>[]],
    'sub'=>['class'=>[],'style'=>[]],
    'br'=>[],
    'time'=>['datetime'=>[],'class'=>[],'style'=>[]],
    'a'=>['href'=>[],'title'=>[],'class'=>[],'style'=>[],'target'=>[],'rel'=>[]],
    'ul'=>['class'=>[],'style'=>[]],
    'ol'=>['class'=>[],'style'=>[]],
    'li'=>['class'=>[],'style'=>[]],
    'table'=>['class'=>[],'style'=>[]],
    'thead'=>['class'=>[],'style'=>[]],
    'tbody'=>['class'=>[],'style'=>[]],
    'tr'=>['class'=>[],'style'=>[]],
    'th'=>['class'=>[],'style'=>[]],
    'td'=>['class'=>[],'style'=>[]],
  ];
  $clean_html = wp_kses($clean_html, $allowed);

  if (trim($clean_html)==='') {
    return '<div class="km-appointment-list proxied '.$cls.'"><div class="km-no-events">Aktuell sind keine Termine vorhanden.</div></div>';
  }

  return '<div class="km-appointment-list proxied '.$cls.'">'.$clean_html.'</div>';
});

/** Plugin-Actions (Settings-Link) */
add_filter('plugin_action_links_' . plugin_basename(__FILE__), function($links) {
  $settings_url = admin_url('admin.php?page=kme-settings');
  array_unshift($links, '<a href="'.esc_url($settings_url).'">Einstellungen</a>');
  return $links;
});

/** Autor-Link */
add_filter('plugin_row_meta', function($links, $file){
  if ($file === plugin_basename(__FILE__)) {
    $links[] = '<a href="https://heizi.ch/" target="_blank" rel="noopener">Autor</a>';
  }
  return $links;
}, 10, 2);

/** Admin UI laden */
require_once KME_PLUGIN_DIR . 'admin.php';
