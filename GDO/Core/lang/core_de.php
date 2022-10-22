<?php
namespace GDO\Core\lang;
return [
	
# Site
	'sitename' => def('GDO_SITENAME', 'GDOv7'),
# Errors
	'error' => 'Fehler: %s',
	'err_gdo_not_found' => 'Die Daten für %s mit ID: %s konnten nicht gefunden werden.',
	'err_input_not_numeric' => 'Dieses Feld erwartet eine Zahl.',
	'err_unknown_module' => 'Das Modul `%s` ist unbekannt',
	'err_select_candidates' => 'Es gibt mehrere Übereinstimmungen: `%s`.',
// 	'err_duplicate_field_name' => 'A field has been added twice: `%s`.',
	'err_url_not_reachable' => 'Diese URL ist nicht erreichbar: `%s`.',
# err_path
	'is_dir' => 'Verzeichnis',
# Messages
	'msg_form_validated' => 'Ihr Formular wurde erfolgreich gesendet.',
	'msg_crud_created' => 'Das Objekt des Typ\'s %s wurde erfolgreich erstellt.',
	'msg_crud_updated' => 'Das Objekt des Typ\'s %s wurde erfolgreich aktualisiert.',
	'msg_binary_detected' => 'Die Binärdatei %s wurde erkannt.',
# Checkbox
	'enum_yes' => 'Ja',
# Enum
	'enum_none' => '-Keine-',
# E-Mail
	'enum_html' => 'HTML',
# Navpos
	'enum_left' => 'Links',
# Permissions
	'sel_no_permissions' => 'Nicht erforderlich',
# Buttons
	'btn_ok' => 'O.K.',
# Float
	'decimal_point' => '.',
# UserType
	'enum_system' => 'System',
# GDTs
	'website_content' => 'Webseiteninhalt',
	'exception' => 'Ausnahme',
	'logo' => 'Logo',
# CBX
	'sel_all' => 'Alle auswählen',
# Fineprint
	'privacy' => 'Datenschutz',
# Util
	'or' => 'oder',
# Welcome
	'welcome' => 'willkommen',
# Version
	'info_version' => 'GDOv7- und PHP-Version anzeigen.',
# Directory Index
	'mt_dir_index' => '%s (%s Dateien und Ordner)',
# Table
	'cfg_spr' => 'Vorschläge pro Anfrage',
# List
	'lbl_search_criteria' => 'Suche: %s',
# User
	'users' => 'Benutzer',
### Config ###
	'cfg_asset_revision' => 'Asset-Revision / Client-Cache-Poisoning',
### 403 ###
	'mail_title_403' => '%s: 403 (%s)',

Auf %s wurde eine verbotene URL besucht.
URL: %s
Benutzer: %s

Mit freundlichen Grüße,
Das %2$s-System',

	
### 404 ###
	'mail_title_404' => '%s: 404 (%s)',

Es wurde eine unbekannte URL auf %s besucht.
URL: %s
Benutzer: %s
Referrer: %s

Mit freundlichen Grüße,
Das %2$s-System',

	'confirm_delete' => 'Wollen Sie das wirklich löschen?',
# Privacy
	'info_privacy_related_module' => '%s ist ein datenschutzbezogenes Modul, das derzeit aktiviert ist.',