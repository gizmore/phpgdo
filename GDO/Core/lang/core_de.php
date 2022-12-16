<?php
namespace GDO\Core\lang;
return [
	
# Site
	'sitename' => def('GDO_SITENAME', 'GDOv7'),	'%s' => '%s',	'---n/a---' => '---n/v---',	
# Errors
	'error' => 'Fehler: %s',	'err_php_extension_missing' => 'Dieses Modul erfordert die PHP-Erweiterung `%s`.',	'err_system_dependency' => 'Eine Systemvoraussetzung ist nicht erfüllt: %s',	'err_php_major_version' => 'GDOv7 erfordert PHP-Version %s.',	'err_db' => "SQL-Fehler %s: %s\nAbfrage: %s",	'err_db_connect' => 'Die Datenbankverbindung konnte nicht hergestellt werden: %s.',	'err_db_no_link' => 'Die Datenbankverbindung konnte nicht hergestellt werden.',	'err_csrf' => 'Ihr Formular-Token ist ungültig. Sie haben wahrscheinlich Cookie- und/oder Sitzungsprobleme. Versuchen Sie, Ihre Cookies für diese Seite zu löschen.',
	'err_gdo_not_found' => 'Die Daten für %s mit ID: %s konnten nicht gefunden werden.',	'err_module' => 'Modul `%s` ist derzeit nicht installiert.',	'err_img_not_found' => 'Bild nicht gefunden.',	'err_unknown_gdo_column' => '´%s´ hat keine Spalte namens `%s`.',	'err_save_unpersisted_entity' => 'Ein Objekt vom Typ %s soll gespeichert / aktualisiert werden, aber es wurde vorher nicht persistiert.',	'err_create_dir' => 'Verzeichnis ´%s´ in %s Zeile %s kann nicht erstellt werden.',	'err_no_permission' => 'Um %s zu verwenden, benötigen Sie die %s-Berechtigung.',	'err_permission_required' => 'Sie haben nicht die Berechtigung diese Funktion auszuführen.',	'err_null_not_allowed' => 'Dieses Feld darf nicht leer bleiben.',	'err_pattern_mismatch' => 'Ihre Eingabe stimmt nicht mit dem Muster %s überein.',	'err_parameter' => 'Ein Methodenparameter ist fehlerhaft: `%s` - %s',	'err_unknown_config' => 'Modul %s hat keine Konfigurationsvariable namens %s.',	'err_unknown_user_setting' => 'Modul %s hat keine Benutzereinstellungsvariable namens %s.',	'err_text_only_numeric' => 'Ihre Eingabe ist nur numerisch. Das ist unerwartet.',
	'err_input_not_numeric' => 'Dieses Feld erwartet eine Zahl.',	'err_int_bytes_length' => 'Die Bytegröße dieser Ganzzahl ist ungültig: `%s`.',	'err_gdo_no_primary_key' => 'Dieses GDT_Objekt benötigt eine GDO-Tabelle mit Primärschlüsseln.',	'err_missing_template' => 'Eine Vorlagendatei fehlt vollständig: %s.',	'err_int_not_between' => 'Diese Zahl muss zwischen %s und %s liegen.',	'err_int_too_small' => 'Diese Zahl muss größer oder gleich %s sein.',	'err_int_too_large' => 'Diese Zahl muss kleiner oder gleich %s sein.',	'err_invalid_string_encoding' => 'Dieser Text hat eine ungültige Zeichenfolgencodierung festgelegt.',	'err_properitary_asset_code' => 'Sie dürfen nicht auf Asset-Dateien im GDO-Ordner zugreifen.',	'err_invalid_choice' => 'Ihre Auswahl ist ungültig.',	'err_gdt_should_have_a_name' => '%s sollte einen Namen haben!',	'err_gdo_no_gdt' => 'Das GDO `%2$s` erlaubt die GDT-Methode `%1$s` nicht.',	'err_table_gdo' => 'Dieses GDO ist kein Tabellenobjekt: `%s`.',	'err_method_disabled' => 'Die Methode %2$s im Modul %1$s ist derzeit deaktiviert.',	'err_method_is_stub' => 'Diese Funktion ist noch nicht implementiert: `%s`',	'err_username_taken' => 'Dieser Benutzername ist bereits vergeben.',	'err_form_invalid' => 'Ihr Formular ist ungültig oder unvollständig. %s Fehler wurden gefunden.',
	'err_unknown_module' => 'Das Modul `%s` ist unbekannt',	'err_unknown_method' => 'Die Methode `%2$s` ist dem Modul `%1$s` unbekannt.',	'err_unknown_parameter' => 'Unbekannter Parameter `%s` in Methode `%s`.',	'err_user_type' => 'Ihr Benutzer ist nicht vom Typ `%s`.',	'err_local_url_not_allowed' => 'Diese URL zeigt auf lokale Dateien.',	'err_external_url_not_allowed' => 'Diese URL zeigt auf eine externe Ressource: %s',	'file_not_found' => 'Datei nicht gefunden',	'err_file_not_found' => 'Die Datei %s konnte auf diesem Server nicht gefunden werden.',	'err_invalid_gdt_var' => 'Ihre Eingabe ist ungültig!',	'forbidden' => 'Verboten!',	'err_forbidden' => 'Sie dürfen nicht darauf zugreifen.',	'err_unknown_field' => 'Das Feld `%s` in diesem Formular ist entweder unbekannt oder nicht beschreibbar.',	'err_user_required' => 'Sie müssen sich anmelden, um fortzufahren. Sie können auch <a href="%s">als Gast fortfahren</a>',
	'err_select_candidates' => 'Es gibt mehrere Übereinstimmungen: `%s`.',	'err_string_length' => 'Dieser Text muss zwischen %s und %s Zeichen lang sein.',	'err_unknown_order_column' => 'Diese Spalte ist unbekannt und kann nicht sortiert werden nach: `%s`.',	'err_no_data_yet' => 'Es gibt noch keine Daten für diesen Artikel.',	'err_pass_too_short' => 'Ihr Passwort muss mindestens %s Zeichen lang sein.',	'err_members_only' => 'Dazu müssen Sie sich <a href="%s">authentifizieren</a>.',	'err_only_member_access' => 'Nur Mitglieder dürfen darauf zugreifen.',	'err_only_friend_access' => 'Nur ihre Freunde dürfen darauf zugreifen.',	'err_only_private_access' => 'Nur der Benutzer selbst darf darauf zugreifen.',	'err_unknown_acl_setting' => 'Unbekannte ACL-Einstellung: `%s`.',	'err_submit_without_click_handler' => 'Der Submit-Schaltfläche von `%s` der Methode `%s` fehlt ein Klick-Handler.',	'err_url_not_reachable' => 'Diese URL ist nicht erreichbar: `%s`.',	'err_cli_exception' => 'Der Befehl hat einen Fehler verursacht: %s in %s Zeile %s.',
	'err_cli_form_gdt' => '%s hat einen Fehler: %s',	'err_path_not_exists' => 'Das %2$s `%1$s` existiert nicht oder ist nicht lesbar.',	'err_token' => 'Ihr Authentizitätstoken ist ungültig oder wurde bereits verwendet.',	'err_exception' => '%s: `%s`.',	'err_is_deleted' => 'Dieser Eintrag wurde gelöscht und kann nicht mehr geändert werden.',	'err_session_required' => 'Sie benötigen ein Sitzungscookie, um diese Methode zu verwenden.',	'err_invalid_ipc' => 'Die IPC Bridge-Einstellungen in config.php sind ungültig.',	'err_positional_after_named_parameter' => 'Sie können keine benannten Parameter nach erforderlichen Positionsangaben angeben.',	'err_gdo_is_not_table' => 'Ein %s-Tabellen-GDO ist keine Tabelle, sondern eine Instanz.',	'err_db_unique' => 'Dieser Eintrag existiert bereits.',
	'err_min_max_confusion' => 'Das Maximum ist kleiner als das Minimum.',
	'err_invalid_user_setting' => 'Modul %s hat eine ungültige Nutzereinstellung namens %s: %s (%s)',
	
# err_path
	'is_dir' => 'Verzeichnis',	'is_file' => 'Datei',	
# Messages
	'msg_form_validated' => 'Ihr Formular wurde erfolgreich gesendet.',	'msg_cache_flushed' => 'Alle Caches wurden geleert. Z.B; rm -rf temp/, Cache::flush(), Interne Caches über Hooks.',
	'msg_crud_created' => 'Das Objekt des Typ\'s %s wurde erfolgreich erstellt.',
	'msg_crud_updated' => 'Das Objekt des Typ\'s %s wurde erfolgreich aktualisiert.',
	'msg_binary_detected' => 'Die Binärdatei %s wurde erkannt.',	'msg_module_methods' => '%s Methoden: %s.',	
# Checkbox
	'enum_yes' => 'Ja',	'enum_no' => 'Nein',	'enum_undetermined_yes_no' => 'unentschieden',	
# Enum
	'enum_none' => '-Keine-',	'enum_all' => 'Alle',	'enum_staff' => 'Personal',	'enum_unknown' => 'unbekannt',	
# E-Mail
	'enum_html' => 'HTML',	'enum_text' => 'Text',	'email_fmt' => 'Format',	
# Navpos
	'enum_left' => 'Links',	'enum_right' => 'Richtig',	'enum_bottom' => 'Unten',	
# Permissions
	'sel_no_permissions' => 'Nicht erforderlich',	'perm_admin' => 'Admin',	'perm_staff' => 'Personal',	'perm_cronjob' => 'Cronjob',	
# Buttons
	'btn_ok' => 'O.K.',	'btn_abort' => 'Abbruch',	'btn_add' => 'Hinzufügen',	'btn_back' => 'Zurück',	'btn_clearcache' => 'Cache löschen',	'btn_create' => 'Erstellen',	'btn_delete' => 'Löschen',	'btn_edit' => 'Bearbeiten',	'btn_modules' => 'Module',	'btn_overview' => 'Übersicht',	'btn_participate' => 'Teilnehmen',
	'btn_preview' => 'Vorschau',	'btn_print' => 'Drucken',	'btn_save' => 'Speichern',	'btn_send' => 'Senden',	'btn_invisible' => 'Unsichtbar setzen',	'btn_send_mail' => 'Mail senden',	'btn_set' => 'Einstellen',	'btn_upload' => 'Hochladen',	'btn_view' => 'Ansicht',	'btn_visible' => 'Sichtbar setzen',	'submit' => 'Senden',	
# Float
	'decimal_point' => '.',	'thousands_seperator' => ',',	
# UserType
	'enum_system' => 'System',	'enum_ghost' => 'Geist',	'enum_guest' => 'Gast',	'enum_member' => 'Mitglied',	'enum_link' => 'Link',	'enum_bot' => 'Bot',	'unknown_user' => 'Unbekannter Benutzer',	
# GDTs
	'expires' => 'Endet am',
	'website_content' => 'Webseiteninhalt',
	'exception' => 'Ausnahme',	'reason' => 'Grund',	'code' => 'code',	'front' => 'vorne',	'back' => 'Zurück',	'redirect' => 'umleiten',	'attachment' => 'Anhang',	'edited_by' => 'Bearbeitet von',	'html' => 'HTML',	'format' => 'formatieren',	'ghost' => 'Geist',	'guest' => 'Gast',	'last_url' => 'Letzte URL',	'age' => 'Alter',	'file_size' => 'Dateigröße',	'folder' => 'Ordner',	'message' => 'Nachricht',	'url' => 'URL',	'filesize' => 'Größe',	'file_type' => 'Typ',	'module_path' => 'Pfad',	'sorting' => 'sortieren',	'enabled' => 'Aktiviert',	'name' => 'Name',	'user_type' => 'Benutzertyp',	'user_guest_name' => 'Gastname',	'user_level' => 'Level',	'copyright' => 'Urheberrecht',	'password' => 'Passwort',	'ipp' => 'IPP',	'keywords' => 'Schlüsselwörter',	'description' => 'Beschreibung',	'title' => 'Titel',	'cfg_hook_sidebar' => 'Einhaken in Seitenleiste?',	'text' => 'Text',	'string' => 'Zeichenfolge',	'xsrf' => 'XSRF-Schutz',	'permission' => 'Erlaubnis',	'user' => 'Benutzer',	'username' => 'Benutzername',	'edited_at' => 'Bearbeitet am',	'deleted_at' => 'Gelöscht am',	'deleted_by' => 'Gelöscht von',	'unknown' => 'Unbekannt',	'id' => 'ID',	'testfield' => 'Testfeld',	'created_at' => 'Erstellt am',	'created_by' => 'Erstellt von',	'page' => 'Seite',	'search' => 'Suche',	'path' => 'Pfad',	'font' => 'Schriftart',	'color' => 'Farbe',	'priority' => 'Priorität',	'from' => 'Von',	'to' => 'an',	'version' => 'Version',	'count' => 'Anzahl',	'backup_file' => 'Sicherungsdatei',	'license' => 'Lizenz',	'step' => 'Schritt',	'ip' => 'IP',	'token' => 'Token',	'editor' => 'Editor',	'quote_by' => 'Zitat von %s',	'quote_at' => 'Bei %s',	'not_specified' => 'Nicht angegeben',	'email' => 'E-Mail',	'size' => 'Größe',	'object_filter' => 'Filter',	'directory' => 'Verzeichnis',	'type' => 'Typ',	'print' => 'Drucken',	'favorite_color' => 'Lieblingsfarbe',	'website' => 'Website',	'information' => 'Informationen',	'health' => 'Gesundheit',
	'logo' => 'Logo',	
# CBX
	'sel_all' => 'Alle auswählen',	'sel_checked' => 'Ja',	'sel_unchecked' => 'Nein',	
# Fineprint
	'privacy' => 'Datenschutz',	'impressum' => 'impressum',	'md_core_privacy' => 'Datenschutz- und Datenflussinformationen für %s.',	'md_core_impressum' => 'Das Impressum für die %s-Service-Website.',	
# Util
	'or' => 'oder',	'and' => 'und',	'none' => 'keine',	
# Welcome
	'welcome' => 'willkommen',	'md_welcome' => 'Die Willkommensseite für den %s-Dienst.',	
# Version
	'info_version' => 'GDOv7- und PHP-Version anzeigen.',	'php_version' => 'PHP-Version',	'gdo_version' => 'GDO-Version',	
# Directory Index
	'mt_dir_index' => '%s (%s Dateien und Ordner)',	'mt_filenotfound' => 'Nicht gefunden!',	'mt_notallowed' => 'Verboten!',	
# Table
	'cfg_spr' => 'Vorschläge pro Anfrage',	'cfg_ipp_cli' => 'Elemente pro Seite (CLI)',	'cfg_ipp_http' => 'Elemente pro Seite (HTML)',	
# List
	'lbl_search_criteria' => 'Suche: %s',	'order_by' => 'Sortieren nach',	'order_dir' => 'Richtung',	'asc' => 'Aufsteigend',	'desc' => 'Absteigend',	
# User
	'users' => 'Benutzer',	'permissions' => 'Berechtigungen',	'msg_sort_success' => 'Erfolgreich sortiert?!',	
### Config ###
	'cfg_asset_revision' => 'Asset-Revision / Client-Cache-Poisoning',	'cfg_system_user' => 'Systembenutzer',	'cfg_show_impressum' => 'Impressum in der Fußzeile anzeigen?',	'cfg_show_privacy' => 'Datenschutzinformationen in der Fußzeile anzeigen?',	'cfg_allow_guests' => 'GDOv7-Gastbenutzersystem aktivieren?',	'cfg_siteshort_title_append' => 'Kurznamen der Website in Seitentitel einfügen?',	'cfg_mail_403' => 'E-Mail bei 403-Fehlern senden?',	'cfg_mail_404' => 'E-Mail bei 404-Fehlern senden?',	'cfg_directory_indexing' => 'Verzeichnisindizierung aktivieren?',	'cfg_module_assets' => 'Darf Assets aus dem GDO-Quellverzeichnis geladen werden?',	'cfg_dotfiles' => 'Erlauben, versteckte Punktdateien zu lesen und zu indizieren?',	
### 403 ###
	'mail_title_403' => '%s: 403 (%s)',	'mail_body_403' => 'Lieber %s,

Auf %s wurde eine verbotene URL besucht.
URL: %s
Benutzer: %s

Mit freundlichen Grüße,
Das %2$s-System',

	
### 404 ###
	'mail_title_404' => '%s: 404 (%s)',	'mail_body_404' => 'Lieber %s,

Es wurde eine unbekannte URL auf %s besucht.
URL: %s
Benutzer: %s
Referrer: %s

Mit freundlichen Grüße,
Das %2$s-System',

	'confirm_delete' => 'Wollen Sie das wirklich löschen?',	'md_switch_language' => 'Sprache wechseln',	'gdt_redirect_to' => 'Umleitung zu %s...',	'unknown_permission' => 'Diese Berechtigung ist unbekannt',	'add_permissions' => 'Eine Berechtigung hinzufügen',	'mt_sort' => '%s-Datenbank sortieren',	'mt_crud_create' => 'Neues %s',	'mt_crud_update' => '%s bearbeiten',	'cronjob_method' => 'Cronjob-Methode',	'method' => 'Methode',	'msg_installed_modules' => 'Installierte Module: %s.',	'mt_core_security' => 'SICHERHEIT.md',	'mt_core_robots' => 'robots.txt',	'mt_core_gettypes' => 'GDT-Metadaten abrufen',	'mt_core_pathcompletion' => 'Pfadvervollständigung',	'mt_ajax' => '%s Datenabruf',	'creator_header' => '%s hat dies auf %s hinzugefügt.',	'please_choose' => 'Bitte wählen Sie...',	'required' => 'Dieses Feld muss zwingend ausgefüllt werden.',
	
# Privacy
	'info_privacy_related_module' => '%s ist ein datenschutzbezogenes Modul, das derzeit aktiviert ist.',	't_privacy_core_toggles' => 'Core-Konfiguration',	'_acl_last_activity_relation' => '`Letzte Aktivität` sichtbar für',	'privacy_settings' => 'Datenschutzeinstellungen',	'health' => 'Gesundheit',	'mt_core_forcessl' => 'Verschlüsselung erzwingen',	'err_nothing_happened' => 'Es ist kein Fehler aufgetreten, aber unerwarteterweise ist nichts passiert.',
	# Health
	'gdo_revision' => 'GDO-Revision',
	'health_cpus' => 'Kerne',
	'health_load' => 'Load',
	'health_clock' => 'GHz',
	'health_mem' => 'RAM',
	'health_free' => 'Free',
	'health_used' => 'Used',
	'health_hdd_avail' => 'HDD',
	'health_hdd_free' => 'Free',
	'health_hdd_used' => 'Used',
	
];
