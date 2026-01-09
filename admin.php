<?php
if (!defined('ABSPATH')) { exit; }

/** Admin-Menü */
add_action('admin_menu', function() {
  add_menu_page(
    'KM Events',
    'KM Events',
    'manage_options',
    'kme-settings',
    'kme_render_settings_page',
    KME_PLUGIN_URL . 'assets/menu-icon.svg',
    61
  );
});

/** Admin-Assets */
add_action('admin_enqueue_scripts', function($hook) {
  if ($hook !== 'toplevel_page_kme-settings') return;

  // Frontend-CSS (für Vorschau)
  wp_enqueue_style('konzertmeister-events', KME_PLUGIN_URL . 'km-events.css', [], '2.6.0');

  // Aktuelle Optionen → Variablen
  $o = kme_get_options();
  $bg   = !empty($o['enable_background']) ? $o['background_color'] : 'transparent';
  $bw   = max(0, (int)$o['border_width']).'px';
  $br   = max(0, (int)$o['border_radius']).'px';
  $sepH = max(0, (int)$o['sep_h_width']).'px';
  $sepV = max(0, (int)$o['sep_v_width']).'px';
  $inline = ":root{--kme-text:{$o['text_color']};--kme-bg:{$bg};--kme-badge:{$o['weekday_badge_bg']};--kme-sep:{$o['separator_color']};--kme-border-color:{$o['border_color']};--kme-border-width:{$bw};--kme-border-radius:{$br};--kme-sep-h-width:{$sepH};--kme-sep-v-width:{$sepV};}";
  wp_add_inline_style('konzertmeister-events', $inline);

  // Admin-Styles
  wp_enqueue_style('kme-admin', KME_PLUGIN_URL . 'admin.css', [], '2.6.0');

  // jQuery + Live-JS inkl. Presets
  wp_enqueue_script('jquery');
  wp_add_inline_script('jquery', <<<'JS'
  jQuery(function($){
    // Presets
    const PRESETS = {
    KMlight: {
        label: 'Konzertmeister hell',
        text_color:       '#0d5789',
        background_color: '#ffffff',
        enable_background: true,
        weekday_badge_bg: '#2ba9e0',
        separator_color:  '#dddddd',
        sep_h_enabled:    true,
        sep_h_width:      1,
        sep_v_enabled:    true,
        sep_v_width:      5,
        border_enabled:   true,
        border_color:     '#dddddd',
        border_width:     1,
        border_radius:    2,
        hover_effect:     'none',
        show_location:    true
      },
    KMdark: {
        label: 'Konzertmeister dunkel',
        text_color:       '#ffffff',
        background_color: '#2c2c2c',
        enable_background: true,
        weekday_badge_bg: '#dddddd',
        separator_color:  '#dddddd',
        sep_h_enabled:    true,
        sep_h_width:      1,
        sep_v_enabled:    true,
        sep_v_width:      5,
        border_enabled:   true,
        border_color:     '#dddddd',
        border_width:     1,
        border_radius:    2,
        hover_effect:     'none',
        show_location:    true
    },
        violettLight: {
        label: 'Violett Light',
        text_color:       '#0E1111',
        background_color: '#E9E6ED',
        enable_background: true,
        weekday_badge_bg: '#9B82D9',
        separator_color:  '#9B82D9',
        sep_h_enabled:    true,
        sep_h_width:      1,
        sep_v_enabled:    true,
        sep_v_width:      5,
        border_enabled:   false,
        border_color:     '#0E1111',
        border_width:     1,
        border_radius:    12,
        hover_effect:     'none',
        show_location:    true
      },
      violettDark: {
        label: 'Violett Dark',
        text_color:       '#EDECF3',
        background_color: '#1F1B24',
        enable_background: true,
        weekday_badge_bg: '#9B82D9',
        separator_color:  '#9B82D9',
        sep_h_enabled:    true,
        sep_h_width:      1,
        sep_v_enabled:    true,
        sep_v_width:      4,
        border_enabled:   0,
        border_color:     '#9B82D9',
        border_width:     1,
        border_radius:    12,
        hover_effect:     'shade',
        show_location:    true
      },
      creamBrass: {
        label: 'Cream Brass',
        text_color:       '#2A2722',
        background_color: 'rgba(254,245,187,0.20)',
        enable_background: true,
        weekday_badge_bg: '#FEF5BB',
        separator_color:  '#FEF5BB',
        sep_h_enabled:    true,
        sep_h_width:      1,
        sep_v_enabled:    true,
        sep_v_width:      5,
        border_enabled:   1,
        border_color:     'rgba(254,245,187,0.55)',
        border_width:     1,
        border_radius:    12,
        hover_effect:     'glow',
        show_location:    true
      },
      darkStage: {
        label: 'Dark Stage',
        text_color:       '#E8E8E8',
        background_color: '#121417',
        enable_background: true,
        weekday_badge_bg: '#6EE7F0',
        separator_color:  '#6EE7F0',
        sep_h_enabled:    true,
        sep_h_width:      1,
        sep_v_enabled:    true,
        sep_v_width:      5,
        border_enabled:   1,
        border_color:     'rgba(255,255,255,0.15)',
        border_width:     1,
        border_radius:    12,
        hover_effect:     'lift',
        show_location:    true
      }
    };

    function normColor(v){
      v=(v||'').trim();
      if (/^[0-9A-Fa-f]{3}([0-9A-Fa-f]{3})?$/.test(v)) v = '#'+v; // Hex ohne #
      return v;
    }
    function intPx(v, def){ v=parseInt(v,10); if(isNaN(v)||v<0) v=def; return v+'px'; }

    var liveTag = $('#kme-live-vars');
    if(!liveTag.length){ liveTag=$('<style id="kme-live-vars"></style>').appendTo('head'); }

    function refreshLivePreview(){
      var text  = normColor($('input[name="kme_options[text_color]"]').val());
      var bg    = normColor($('input[name="kme_options[background_color]"]').val());
      var badge = normColor($('input[name="kme_options[weekday_badge_bg]"]').val());
      var sep   = normColor($('input[name="kme_options[separator_color]"]').val());
      var bcol  = normColor($('input[name="kme_options[border_color]"]').val());

      var bwidth = intPx($('input[name="kme_options[border_width]"]').val(), 1);
      var brad   = intPx($('input[name="kme_options[border_radius]"]').val(), 12);
      var sepHW  = intPx($('input[name="kme_options[sep_h_width]"]').val(), 1);
      var sepVW  = intPx($('input[name="kme_options[sep_v_width]"]').val(), 5);

      var bgEnabled = $('input[name="kme_options[enable_background]"]').is(':checked');
      var bgFinal = bgEnabled ? (bg||'transparent') : 'transparent';

      var css=':root{';
      if(text)  css+='--kme-text:'+text+';';
      css+='--kme-bg:'+bgFinal+';';
      if(badge) css+='--kme-badge:'+badge+';';
      if(sep)   css+='--kme-sep:'+sep+';';
      if(bcol)  css+='--kme-border-color:'+bcol+';';
      css+='--kme-border-width:'+bwidth+';';
      css+='--kme-border-radius:'+brad+';';
      css+='--kme-sep-h-width:'+sepHW+';';
      css+='--kme-sep-v-width:'+sepVW+';';
      css+='}';
      liveTag.text(css);

      var $wrap = $('.kme-preview .km-appointment-list.proxied');
      $wrap.toggleClass('kme-show-location', $('input[name="kme_options[show_location]"]').is(':checked'));
      $wrap.toggleClass('kme-hide-location', !$('input[name="kme_options[show_location]"]').is(':checked'));
      $wrap.toggleClass('kme-border-on', $('input[name="kme_options[border_enabled]"]').is(':checked'));
      $wrap.toggleClass('kme-border-off', !$('input[name="kme_options[border_enabled]"]').is(':checked'));
      $wrap.toggleClass('kme-sep-h-on', $('input[name="kme_options[sep_h_enabled]"]').is(':checked'));
      $wrap.toggleClass('kme-sep-h-off', !$('input[name="kme_options[sep_h_enabled]"]').is(':checked'));
      $wrap.toggleClass('kme-sep-v-on', $('input[name="kme_options[sep_v_enabled]"]').is(':checked'));
      $wrap.toggleClass('kme-sep-v-off', !$('input[name="kme_options[sep_v_enabled]"]').is(':checked'));

      var hover = $('select[name="kme_options[hover_effect]"]').val() || 'none';
      $wrap.removeClass(function(i,c){ return (c.match(/kme-hover-\S+/g)||[]).join(' '); })
           .addClass('kme-hover-'+hover);
    }

    // Color-Picker Textfeld
    $('.kme-colorpair').each(function(){
      var $pair=$(this), $picker=$pair.find('input[type="color"]'), $text=$pair.find('input[type="text"][name^="kme_options["]');
      var init = normColor($text.val());
      if(/^(#[0-9A-Fa-f]{3}|#[0-9A-Fa-f]{6})$/.test(init)) $picker.val(init);
      $picker.on('input change', function(){ $text.val($(this).val()).trigger('change'); });
      $text.on('input change', refreshLivePreview);
    });

    // Toggles
    $('input[name="kme_options[enable_background]"], input[name="kme_options[show_location]"], input[name="kme_options[border_enabled]"], input[name="kme_options[sep_h_enabled]"], input[name="kme_options[sep_v_enabled]"]').on('change', refreshLivePreview);
    $('input[name="kme_options[border_width]"], input[name="kme_options[border_radius]"], input[name="kme_options[sep_h_width]"], input[name="kme_options[sep_v_width]"]').on('input change', refreshLivePreview);
    $('select[name="kme_options[hover_effect]"]').on('change', refreshLivePreview);

    // Preset anwenden
    function setCheckbox(name, val){ $('input[name="kme_options['+name+']"]').prop('checked', !!val).trigger('change'); }
    function setNumber(name, val){ $('input[name="kme_options['+name+']"]').val(val).trigger('change'); }
    function setColor(name, val){
      val = normColor(val);
      var $text = $('input[name="kme_options['+name+']"]');
      $text.val(val).trigger('change');
      var $picker = $text.closest('.kme-colorpair').find('input[type="color"]');
      if (/^(#[0-9A-Fa-f]{3}|#[0-9A-Fa-f]{6})$/.test(val)) $picker.val(val);
    }
    function setSelect(name, val){ $('select[name="kme_options['+name+']"]').val(val).trigger('change'); }

    $('#kme-preset-apply').on('click', function(e){
      e.preventDefault();
      var key = $('#kme-preset').val();
      var p = PRESETS[key]; if(!p) return;

      setColor('text_color', p.text_color);
      setColor('background_color', p.background_color);
      setCheckbox('enable_background', p.enable_background);
      setColor('weekday_badge_bg', p.weekday_badge_bg);
      setColor('separator_color', p.separator_color);

      setCheckbox('sep_h_enabled', p.sep_h_enabled);
      setNumber('sep_h_width', p.sep_h_width);

      setCheckbox('sep_v_enabled', p.sep_v_enabled);
      setNumber('sep_v_width', p.sep_v_width);

      setCheckbox('border_enabled', p.border_enabled);
      setColor('border_color', p.border_color);
      setNumber('border_width', p.border_width);
      setNumber('border_radius', p.border_radius);

      setSelect('hover_effect', p.hover_effect);
      setCheckbox('show_location', p.show_location);

      refreshLivePreview();
    });

    // Initial
    refreshLivePreview();
  });
  JS);
});

/** Settings-Registrierung */
add_action('admin_init', function() {
  register_setting('kme_settings_group', KME_OPTION_KEY, [
    'type'=>'array',
    'sanitize_callback'=>'kme_sanitize_options',
    'default'=>kme_default_options(),
  ]);

  /* Presets */
  add_settings_section('kme_presets', 'Presets', '__return_false', 'kme-settings');
  add_settings_field('kme_preset_picker', 'Stilvorlage', 'kme_field_presets', 'kme-settings', 'kme_presets', []);

  /* Allgemeine Farben */
  add_settings_section('kme_colors', 'Allgemeine Farben', '__return_false', 'kme-settings');
  add_settings_field('text_color', 'Textfarbe', 'kme_field_color', 'kme-settings', 'kme_colors', [
    'key'=>'text_color'
  ]);
  add_settings_field('background_color', 'Hintergrund', 'kme_field_color', 'kme-settings', 'kme_colors', [
    'key'=>'background_color'
  ]);
  add_settings_field('enable_background', 'Hintergrund aktivieren', 'kme_field_toggle', 'kme-settings', 'kme_colors', [
    'key'=>'enable_background'
  ]);
  add_settings_field('weekday_badge_bg', 'Badge (Jahr/Wochentag)', 'kme_field_color', 'kme-settings', 'kme_colors', [
    'key'=>'weekday_badge_bg'
  ]);

  /* Trenner */
  add_settings_section('kme_seps', 'Trenner', '__return_false', 'kme-settings');
  add_settings_field('separator_color', 'Trennerfarbe', 'kme_field_color', 'kme-settings', 'kme_seps', [
    'key'=>'separator_color'
  ]);
  add_settings_field('sep_h_enabled', 'Horizontale Linie', 'kme_field_toggle', 'kme-settings', 'kme_seps', [
    'key'=>'sep_h_enabled','desc'=>'Linien zwischen Einträgen'
  ]);
  add_settings_field('sep_h_width', 'Linienstärke (px)', 'kme_field_number', 'kme-settings', 'kme_seps', [
    'key'=>'sep_h_width'
  ]);
  add_settings_field('sep_v_enabled', 'Vertikale Linie', 'kme_field_toggle', 'kme-settings', 'kme_seps', [
    'key'=>'sep_v_enabled','desc'=>'Linie zwischen Datum und Inhalt'
  ]);
  add_settings_field('sep_v_width', 'Linienstärke (px)', 'kme_field_number', 'kme-settings', 'kme_seps', [
    'key'=>'sep_v_width'
  ]);

  /* Rahmen */
  add_settings_section('kme_box', 'Rahmen', '__return_false', 'kme-settings');
  add_settings_field('border_enabled', 'Rahmen aktivieren', 'kme_field_toggle', 'kme-settings', 'kme_box', [
    'key'=>'border_enabled'
  ]);
  add_settings_field('border_color', 'Rahmenfarbe', 'kme_field_color', 'kme-settings', 'kme_box', [
    'key'=>'border_color'
  ]);
  add_settings_field('border_width', 'Breite (px)', 'kme_field_number', 'kme-settings', 'kme_box', [
    'key'=>'border_width'
  ]);
  add_settings_field('border_radius', 'Radius (px)', 'kme_field_number', 'kme-settings', 'kme_box', [
    'key'=>'border_radius'
  ]);

  /* Hover-Effekt */
  add_settings_section('kme_hover', 'Hover-Effekt', '__return_false', 'kme-settings');
  add_settings_field('hover_effect', 'Effekt wählen', 'kme_field_select', 'kme-settings', 'kme_hover', [
    'key'=>'hover_effect',
    'options'=>[
      'none'      => 'Kein',
      'glow'      => 'Glow',
      'lift'      => 'Lift',
      'shade'     => 'Shade',
      'underline' => 'Underline',
    ],
  ]);

  /* Weitere Optionen */
  add_settings_section('kme_more', 'Weitere Optionen', '__return_false', 'kme-settings');
  add_settings_field('show_location', 'Eventstandort anzeigen', 'kme_field_toggle', 'kme-settings', 'kme_more', [
    'key'=>'show_location','desc'=>'Zeigt den Standort vom Anlass an.'
  ]);

  /* Quelle */
  add_settings_section('kme_source', 'Quelle', '__return_false', 'kme-settings');
  add_settings_field('km_url', 'Konzertmeister-URL', 'kme_field_url', 'kme-settings', 'kme_source', [
    'key'=>'km_url','desc'=>'Vollständige Embed-URL inkl. Hash'
  ]);
});

/** Sanitizer – erlaubt sichere CSS-Farben inkl. Alpha */
function kme_sanitize_color($v) {
  $v = trim((string)$v);
  if ($v === '') return '';
  if (preg_match('/^[0-9A-Fa-f]{3}([0-9A-Fa-f]{3})?$/', $v)) $v = '#'.$v; // Hex ohne # → ergänzen
  if (!preg_match('/^[#a-zA-Z0-9\(\)\.,%\s\/\+\-]+$/', $v)) return '';
  return $v;
}
function kme_sanitize_options($input) {
  $out = kme_default_options();

  // URL
  if (!empty($input['km_url'])) {
    $url = esc_url_raw(trim($input['km_url']));
    $p = wp_parse_url($url);
    if (!empty($p['scheme']) && strtolower($p['scheme'])==='https' && !empty($p['host']) && str_contains($p['host'],'konzertmeister.app')) {
      $out['km_url'] = $url;
    }
  }

  // Farben
  foreach (['text_color','background_color','weekday_badge_bg','separator_color','border_color'] as $k) {
    if (isset($input[$k])) { $c=kme_sanitize_color($input[$k]); if($c!=='') $out[$k]=$c; }
  }

  // Toggles
  foreach (['enable_background','show_location','border_enabled','sep_h_enabled','sep_v_enabled'] as $k) {
    $out[$k] = !empty($input[$k]) ? 1 : 0;
  }

  // Zahlen
  foreach (['border_width','border_radius','sep_h_width','sep_v_width'] as $k) {
    if (isset($input[$k])) $out[$k] = max(0, (int)$input[$k]);
  }

  // Hover
  $allowed = ['none','glow','lift','shade','underline'];
  if (!empty($input['hover_effect']) && in_array($input['hover_effect'], $allowed, true)) {
    $out['hover_effect'] = $input['hover_effect'];
  }

  return $out;
}

function kme_field_presets() {
  echo '<div class="kme-field">';
  echo '<select id="kme-preset" style="min-width:220px">';
  echo '<option value="KMlight">Konzertmeister hell</option>';
  echo '<option value="KMdark">Konzertmeister dunkel</option>';
  echo '<option value="violettLight">Violett Light</option>';
  echo '<option value="violettDark">Violett Dark</option>';
  echo '<option value="creamBrass">Cream Brass</option>';
  echo '<option value="darkStage">Dark Stage</option>';
  echo '</select> ';
  echo '<button class="button" id="kme-preset-apply">Anwenden</button>';
  echo '<p class="description" style="width:100%;">Design Preset – wird erst nach „Speichern“ wirksam.</p>';
  echo '</div>';
}
function kme_field_url($args){
  $o=kme_get_options(); $k=$args['key']; $v=$o[$k]??''; $d=$args['desc']??'';
  echo '<div class="kme-field">';
  printf('<input type="url" class="regular-text code" name="%s[%s]" value="%s" placeholder="https://rest.konzertmeister.app/api/v3/org/.../upcomingappointments?...&hash=..." style="width:100%%;max-width:800px;" />',
    esc_attr(KME_OPTION_KEY), esc_attr($k), esc_attr($v));
  if($d) echo '<p class="description">'.esc_html($d).'</p>';
  echo '</div>';
}
function kme_field_color($args){
  $o=kme_get_options(); $k=$args['key']; $v=$o[$k]??''; $d=$args['desc']??'';
  echo '<div class="kme-field kme-colorpair">';
  $picker = (preg_match('/^#([0-9A-Fa-f]{6}|[0-9A-Fa-f]{3})$/',$v)) ? $v : '#000000';
  printf('<input type="color" value="%s" /> ', esc_attr($picker));
  printf('<input type="text" name="%s[%s]" value="%s" class="regular-text code" placeholder="#0E1111 oder rgba(14,17,17,0.85)" style="width:260px;" />',
    esc_attr(KME_OPTION_KEY), esc_attr($k), esc_attr($v));
  if($d) echo '<p class="description">'.esc_html($d).'</p>';
  echo '</div>';
}
function kme_field_toggle($args){
  $o=kme_get_options(); $k=$args['key']; $v=!empty($o[$k])?1:0; $d=$args['desc']??'';
  $label = match($k){
    'enable_background' => 'Hintergrund anzeigen',
    'border_enabled'    => 'Rahmen anzeigen',
    'sep_h_enabled'     => 'Horizontale Trenner anzeigen',
    'sep_v_enabled'     => 'Vertikalen Trenner anzeigen',
    default             => 'Aktivieren'
  };
  echo '<div class="kme-field">';
  printf('<label><input type="checkbox" name="%s[%s]" value="1" %s/> %s</label>',
    esc_attr(KME_OPTION_KEY), esc_attr($k), checked(1,$v,false), esc_html($label));
  if($d) echo '<p class="description">'.esc_html($d).'</p>';
  echo '</div>';
}
function kme_field_number($args){
  $o=kme_get_options(); $k=$args['key']; $v=$o[$k]??0; $d=$args['desc']??'';
  echo '<div class="kme-field">';
  printf('<input type="number" name="%s[%s]" value="%s" min="0" step="1" class="small-text" />',
    esc_attr(KME_OPTION_KEY), esc_attr($k), esc_attr($v));
  if($d) echo '<p class="description">'.esc_html($d).'</p>';
  echo '</div>';
}
function kme_field_select($args){
  $o=kme_get_options(); $k=$args['key']; $val=$o[$k]??''; $opts=$args['options']??[]; $d=$args['desc']??'';
  echo '<div class="kme-field">';
  printf('<select name="%s[%s]">', esc_attr(KME_OPTION_KEY), esc_attr($k));
  foreach($opts as $key=>$label){
    printf('<option value="%s"%s>%s</option>', esc_attr($key), selected($val,$key,false), esc_html($label));
  }
  echo '</select>';
  if($d) echo '<p class="description">'.esc_html($d).'</p>';
  echo '</div>';
}

/** Settings-Seite */
function kme_render_settings_page() {
  if (!current_user_can('manage_options')) return;

  // Datum/Zeit für Vorschau
  $ts=current_time('timestamp');
  $year=date_i18n('Y',$ts); $wday=date_i18n('D',$ts); $day=date_i18n('j',$ts); $monthS=date_i18n('M',$ts); $time=date_i18n('H:i',$ts);
  $ts2=$ts+DAY_IN_SECONDS*7; $year2=date_i18n('Y',$ts2); $wday2=date_i18n('D',$ts2); $day2=date_i18n('j',$ts2); $monthS2=date_i18n('M',$ts2); $time2=date_i18n('H:i',$ts2);

  $o = kme_get_options();
  $cls = trim(
    (!empty($o['show_location']) ? 'kme-show-location' : 'kme-hide-location').' '.
    (!empty($o['border_enabled'])? 'kme-border-on' : 'kme-border-off').' '.
    (!empty($o['sep_h_enabled']) ? 'kme-sep-h-on'  : 'kme-sep-h-off').' '.
    (!empty($o['sep_v_enabled']) ? 'kme-sep-v-on'  : 'kme-sep-v-off').' '.
    ('kme-hover-'.preg_replace('/[^a-z]/','', strtolower($o['hover_effect'])))
  );
  ?>
  <div class="wrap kme-wrap">
    <h1>Konzertmeister Events – Einstellungen</h1>

    <div class="kme-grid">
      <div class="kme-col kme-left">
        <form method="post" action="options.php" class="kme-form">
          <?php
            settings_fields('kme_settings_group');
            do_settings_sections('kme-settings');
            submit_button('Speichern');
          ?>
        </form>
      </div>

      <div class="kme-col kme-right">
        <style id="kme-live-vars"></style>
        <div class="kme-preview">
          <h2>Vorschau</h2>
          <div class="km-appointment-list proxied <?php echo esc_attr($cls); ?>">
            <!-- Eintrag 1 -->
            <div class="km-list-item">
              <a class="km-app-date list-item-link">
                <div class="km-week-day-date">
                  <div class="km-week-day">
                    <div class="km-year"><?php echo esc_html($year); ?></div>
                    <div><?php echo esc_html($wday); ?></div>
                  </div>
                  <div class="km-date-month">
                    <div class="km-date"><?php echo esc_html($day); ?></div>
                    <div class="km-month"><?php echo esc_html($monthS); ?></div>
                  </div>
                </div>
                <div class="km-time"><?php echo esc_html($time); ?> Uhr</div>
              </a>
              <div class="km-app-main km-attendance-indicator">
                <div class="km-appointment-header" style="width:100%;">
                  <div class="km-appointment-name"><?php echo esc_html($day.'.'.$monthS.'.'); ?></div>
                  <div class="km-appointment-type">Auftritt</div>
                  <div class="km-location"><a href="#" target="_blank" rel="noopener">Standort</a></div>
                </div>
              </div>
            </div>
            <!-- Eintrag 2 -->
            <div class="km-list-item">
              <a class="km-app-date list-item-link">
                <div class="km-week-day-date">
                  <div class="km-week-day">
                    <div class="km-year"><?php echo esc_html($year2); ?></div>
                    <div><?php echo esc_html($wday2); ?></div>
                  </div>
                  <div class="km-date-month">
                    <div class="km-date"><?php echo esc_html($day2); ?></div>
                    <div class="km-month"><?php echo esc_html($monthS2); ?></div>
                  </div>
                </div>
                <div class="km-time"><?php echo esc_html($time2); ?> Uhr</div>
              </a>
              <div class="km-app-main km-attendance-indicator">
                <div class="km-appointment-header" style="width:100%;">
                  <div class="km-appointment-name"><?php echo esc_html($day2.'.'.$monthS2.'.'); ?></div>
                  <div class="km-appointment-type">Probe</div>
                  <div class="km-location"><a href="#" target="_blank" rel="noopener">Standort</a></div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <?php
}
