<?php
namespace GDO\Date\lang;

return [
	'module_date' => 'Datum und Uhrzeit',
	'gdo_timezone' => 'Zeitzone',
	'privacy_info_date_module' => 'Zeitzone und letzte Aktivität können einiges verraten. Überprüfen Sie Ihre Einstellungen.',
	'ago' => 'vor %s',

	'err_min_date' => 'Dieses Datum muss nach %s sein.',
	'err_max_date' => 'Dieses Datum muss vor %s sein.',
	'err_invalid_date' => 'Ungültige Zeitangabe: %s ist nicht im Format %s. Bitte stellen Sie sicher, dass Ihre Sprache und Zeitzone korrekt eingestellt sind.',

	# Dateformats
	'df_db' => 'Y-m-d H:i:s.v', # do not change
	'df_local' => 'Y-m-d\TH:i', # do not change
	'df_parse' => 'd.m.Y H:i:s.u',
	'df_ms' => 'd.m.Y H:i:s.v',
	'df_long' => 'd.m.Y H:i:s',
	'df_short' => 'd.m.Y H:i',
	'df_minute' => 'd.m.Y H:i',
	'df_day' => 'd.m.Y',
	'df_sec' => 'd.m.Y H:i:s',
	'tu_s' => 's',
	'tu_m' => 'm',
	'tu_h' => 'h',
	'tu_d' => 'd',
	'tu_w' => 'w',
	'tu_y' => 'y',

	# Timezone
	'mt_date_timezone' => 'Setzen Ihrer Zeitzone',
	'md_date_timezone' => 'Setzen Sie Ihre Zeitzone auf %s.',
	'msg_timezone_changed' => 'Ihre Zeitzone ist nun %s.',
	'cfg_tz_probe_js' => 'Zeitzone mit Javascript ermitteln?',
	'cfg_tz_sidebar_select' => 'Zeitzohnenwahl in der Sidebar anzeigen?',

	# Timezones
	'mt_timezones' => 'Alle Zeitzonen',
	'md_timezones' => 'Zeigt alle Zeitzonen und Offsets via Ajax.',

	# Epoch
	'mt_date_epoch' => 'Zeitstempel ausgeben',
	'msg_time_unix' => 'Unix timestamp: %s',
	'msg_time_java' => 'Java timestamp: %s',
	'msg_time_micro' => 'Microtimestamp: %s',

	# Duration
	'duration' => 'Dauer',
	'err_min_duration' => 'Die Dauer muss mindestens %s Sekunden betragen.',

	# Clock
	'cfg_clock_sidebar' => 'Uhr in der Navigation anzeigen?',
	'cfg_tz_default' => 'Standard-Zeitzone',

	# Activity Accuracy
	'activity_accuracy' => 'Activity Accuracy',
	'tt_activity_accuracy' => 'Control how exact your online activity is shown / last seen on...',

    'timezone' => 'Zeitzone',
];
