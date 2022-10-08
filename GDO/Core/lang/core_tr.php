<?php
namespace GDO\Core\lang;
return [
# Site
'sitename' => def('GDO_SITENAME', 'GDOv7'),'%s' => '%s','---n/a---' => '---n/a---',# Errors
'error' => 'Hata: %s','err_php_extension_missing' => 'Bu modül PHP uzantısı `%s` gerektiriyor.','err_system_dependency' => 'Bir sistem gereksinimi karşılanmadı: %s','err_php_major_version' => 'GDOv7, PHP %s sürümünü gerektirir.','err_db' => "SQL hatası %s: %s\nSorgu: %s",'err_db_connect' => 'Veritabanı bağlantısı kurulamadı: %s.','err_db_no_link' => 'Veritabanı bağlantısı kurulamadı.','err_csrf' => 'Form belirteciniz geçersiz. Muhtemelen çerez ve/veya oturum sorunları yaşıyorsunuz. Bu site için çerezlerinizi temizlemeyi deneyin.',
'err_gdo_not_found' => '%s kimlikli veri bulunamadı: %s.','err_module' => '%s modülü şu anda kurulu değil.','err_img_not_found' => 'Resim bulunamadı.','err_unknown_gdo_column' => '\'%s\', `%s` adında bir sütuna sahip değil.','err_save_unpersisted_entity' => '%s türünde bir nesne kaydedilmek/güncellenmek üzere, ancak daha önce kalıcı değildi.','err_create_dir' => '\'%s\' dizini %s satırında %s oluşturulamıyor.','err_no_permission' => '%s\'yi kullanmak için %s iznine ihtiyacınız var.','err_permission_required' => 'Bu işlevi gerçekleştirme izniniz yok.','err_null_not_allowed' => 'Bu alan boş bırakılamaz.','err_pattern_mismatch' => 'Girişiniz %s kalıbıyla eşleşmiyor.','err_parameter' => 'Bir yöntem parametresi yanlış: `%s`` - %s','err_unknown_config' => '%s modülü, %s adında bir yapılandırma değişkenine sahip değil.','err_unknown_user_setting' => '%s modülü, %s adında bir kullanıcı ayar değişkenine sahip değil.','err_text_only_numeric' => 'Girişiniz yalnızca sayısal. Bu beklenmedik bir durum.',
'err_input_not_numeric' => 'Bu alan bir sayı bekliyor.','err_int_bytes_length' => 'Bu tamsayının bayt boyutu geçersiz: `%s´.','err_gdo_no_primary_key' => 'Bu GDT_Object, birincil anahtarlara sahip bir GDO tablosu gerektirir.','err_missing_template' => 'Bir şablon dosyası tamamen eksik: %s.','err_int_not_between' => 'Bu sayı %s ile %s arasında olmalıdır.','err_int_too_small' => 'Bu sayı %s\'den büyük veya ona eşit olmalıdır.','err_int_too_large' => 'Bu sayı %s\'den küçük veya ona eşit olmalıdır.','err_invalid_string_encoding' => 'Bu metin geçersiz bir dize kodlaması belirtti.','err_properitary_asset_code' => 'GDO klasöründeki varlık dosyalarına erişmenize izin verilmiyor.','err_invalid_choice' => 'Seçiminiz geçersiz.','err_gdt_should_have_a_name' => '%s\'nin bir adı olmalı!','err_gdo_no_gdt' => 'GDO `%2$s`, `%1$s` GDT yöntemine izin vermiyor.','err_table_gdo' => 'Bu GDO bir tablo nesnesi değil: `%s`.','err_method_disabled' => '%1$s modülündeki %2$s yöntemi şu anda devre dışı.','err_method_is_stub' => 'Bu yöntem henüz uygulanmadı: `%s`','err_username_taken' => 'Bu kullanıcı adı zaten alınmış.','err_form_invalid' => 'Formunuz geçersiz veya eksik. %s hata bulundu.',
'err_unknown_module' => '%s modülü bilinmiyor','err_unknown_method' => '`%2$s` yöntemi `%1$s` modülü tarafından bilinmiyor.','err_unknown_parameter' => '%s yönteminde `%s` bilinmeyen parametresi.','err_user_type' => 'Kullanıcınız `%s` türünde değil.','err_local_url_not_allowed' => 'Bu URL yerel dosyalara işaret etmeyebilir.','err_external_url_not_allowed' => 'Bu URL harici bir kaynağa işaret etmeyebilir: %s','file_not_found' => 'Dosya bulunamadı','err_file_not_found' => '%s dosyası bu sunucuda bulunamadı.','err_invalid_gdt_var' => 'Girişiniz geçersiz!','forbidden' => 'Yasak!','err_forbidden' => 'Buna erişme izniniz yok.','err_unknown_field' => 'Bu formdaki `%s` alanı ya bilinmiyor ya da yazılabilir değil.','err_user_required' => 'Devam etmek için giriş yapmalısınız. Ayrıca <a href="%s">misafir olarak devam edebilirsiniz</a>',
'err_select_candidates' => 'Birden çok eşleşme var: `%s`.','err_string_length' => 'Bu metin %s ile %s karakter uzunluğunda olmalıdır.','err_unknown_order_column' => 'Bu sütun bilinmiyor ve şu şekilde sıralanamaz: `%s`.','err_no_data_yet' => 'Bu öğe için henüz veri yok.','err_pass_too_short' => 'Şifreniz en az %s karakter uzunluğunda olmalıdır.','err_members_only' => '<a href="%s">kimlik doğrulamanız</a> gerekiyor.','err_only_member_access' => 'Buna sadece üyeler erişebilir.','err_only_friend_access' => 'Sadece arkadaşlarınız erişebilir.','err_only_private_access' => 'Sadece kullanıcı erişebilir.','err_unknown_acl_setting' => 'Bilinmeyen ACL ayarı: `%s`.','err_submit_without_click_handler' => '%s yönteminin `%s` inin gönder düğmesinde bir tıklama işleyicisi eksik.',	'err_duplicate_field_name' => 'A field has been added twice: `%s`.',
'err_url_not_reachable' => 'Bu URL ye ulaşılamıyor: `%s`.','err_cli_form_gdt' => '%s de bir hata var: %s','err_path_not_exists' => '%2$s `%1$s` mevcut değil veya okunamıyor.','err_token' => 'Özgünlük belirteciniz geçersiz veya zaten kullanılmış.','err_exception' => '%s: `%s`.','err_is_deleted' => 'Bu giriş silindi ve artık düzenlenemez.','err_session_required' => 'Bu yöntemi kullanmak için bir oturum tanımlama bilgisine ihtiyacınız var.','err_invalid_ipc' => 'config.php deki IPC Bridge ayarları geçersiz.','err_positional_after_named_parameter' => 'Gerekli konum bilgisinden sonra adlandırılmış parametreleri belirtemezsiniz.','err_gdo_is_not_table' => 'Bir %s tablosu GDO bir tablo değil, bir örnektir.',# err_path
'is_dir' => 'Dizin','is_file' => 'Dosya',# Messages
'msg_form_validated' => 'Formunuz başarıyla gönderildi.','msg_cache_flushed' => 'Tüm önbellekler temizlendi. Örneğin.; rm -rf temp/, Cache::flush(), Kancalar aracılığıyla dahili önbellekler.',
'msg_crud_created' => '%s başarıyla oluşturuldu.','msg_binary_detected' => 'İkili dosya %s algılandı.','msg_module_methods' => '%s yöntemleri: %s.',# Checkbox
'enum_yes' => 'Evet','enum_no' => 'Hayır','enum_undetermined_yes_no' => 'kararsız',# Enum
'enum_none' => '-Yok-','enum_all' => 'Tümü','enum_staff' => 'Personel','enum_unknown' => 'bilinmiyor',# E-Mail
'enum_html' => 'HTML','enum_text' => 'Metin','email_fmt' => 'Biçim',# Navpos
'enum_left' => 'Sol','enum_right' => 'Doğru','enum_bottom' => 'Alt',# Permissions
'sel_no_permissions' => 'Gerekli değil','perm_admin' => 'Yönetici','perm_staff' => 'Personel','perm_cronjob' => 'cronjob',# Buttons
'btn_ok' => 'Tamam','btn_abort' => 'İptal',
'btn_add' => 'Ekle','btn_back' => 'Geri','btn_clearcache' => 'Önbelleği temizle','btn_create' => 'Oluştur','btn_delete' => 'Sil','btn_edit' => 'Düzenle','btn_modules' => 'Modüller','btn_overview' => 'Genel Bakış','btn_preview' => 'Önizleme','btn_print' => 'Yazdır','btn_save' => 'Kaydet','btn_send' => 'Gönder','btn_invisible' => 'Görünmez olarak ayarla','btn_send_mail' => 'Posta gönder','btn_set' => 'Ayarla','btn_upload' => 'Yükle','btn_view' => 'Görünüm','btn_visible' => 'Görünür olarak ayarla','submit' => 'gönder',# Float
'decimal_point' => '.','thousands_seperator' => ',',# UserType
'enum_system' => 'Sistem','enum_ghost' => 'Hayalet','enum_guest' => 'Misafir','enum_member' => 'Üye','enum_link' => 'Bağlantı','enum_bot' => 'Bot','unknown_user' => 'Bilinmeyen kullanıcı',# GDTs
'reason' => 'sebep','code' => 'kod','front' => 'ön','back' => 'geri','redirect' => 'yönlendirme','attachment' => 'ek','edited_by' => 'Düzenleyen','html' => 'HTML','format' => 'biçim','ghost' => 'hayalet','guest' => 'misafir','last_url' => 'Son URL','age' => 'yaş','file_size' => 'Dosya boyutu','folder' => 'klasör','message' => 'mesaj','url' => 'url','filesize' => 'boyut','file_type' => 'tür','module_path' => 'yol','sorting' => 'sıralama','enabled' => 'Etkin','name' => 'isim','user_type' => 'Kullanıcı türü','user_guest_name' => 'misafir adı','user_level' => 'seviye','copyright' => 'telif hakkı','password' => 'şifre','ipp' => 'IPP','keywords' => 'anahtar kelimeler','description' => 'açıklama','title' => 'başlık','cfg_hook_sidebar' => 'Kenar çubuğuna bağlanılsın mı?','text' => 'metin','string' => 'dize','xsrf' => 'XSRF Koruması','permission' => 'izin','user' => 'kullanıcı','username' => 'kullanıcı adı','edited_at' => 'Düzenleme tarihi','deleted_at' => 'Silindi','deleted_by' => 'Silinen','unknown' => 'Bilinmeyen','id' => 'kimlik','testfield' => 'test alanı','created_at' => 'Oluşturulma tarihi','created_by' => 'Oluşturan','page' => 'sayfa','search' => 'Ara','path' => 'yol','font' => 'Yazı tipi','color' => 'renk','priority' => 'öncelik','from' => 'Kimden','to' => 'açık','version' => 'sürüm','count' => 'say','backup_file' => 'Yedekleme dosyası','license' => 'lisans','step' => 'adım','ip' => 'IP','token' => 'belirteç','editor' => 'editör','quote_by' => '%s tarafından alıntı','quote_at' => '%s\'de','not_specified' => 'Belirtilmemiş','email' => 'e-posta','size' => 'boyut','object_filter' => 'Filtre','directory' => 'dizin','type' => 'tür','print' => 'Yazdır','favorite_color' => 'Favori renk','website' => 'web sitesi','information' => 'bilgi',# CBX
'sel_all' => 'Tümünü seç','sel_checked' => 'Evet','sel_unchecked' => 'Hayır',# Fineprint
'privacy' => 'Gizlilik','impressum' => 'damga','md_core_privacy' => '%s için gizlilik ve veri akışı bilgisi.','md_core_impressum' => '%s hizmet web sitesi için damga.',# Util
'or' => 'veya','and' => 've','none' => 'yok',# Welcome
'welcome' => 'hoş geldiniz','md_welcome' => '%s hizmeti için hoş geldiniz sayfası.',# Version
'info_version' => 'GDOv7 ve PHP sürümünü göster.','php_version' => 'PHP sürümü','gdo_version' => 'GDO versiyonu',# Directory Index
'mt_dir_index' => '%s (%s dosya ve klasör)','mt_filenotfound' => 'Bulunamadı!','mt_notallowed' => 'Yasak!',# Table
'cfg_spr' => 'Talep başına öneri','cfg_ipp_cli' => 'Sayfa başına öğe sayısı (CLI)','cfg_ipp_http' => 'Sayfa başına öğe sayısı (HTML)',# List
'lbl_search_criteria' => 'Ara: %s','order_by' => 'Sıralama ölçütü','order_dir' => 'Yön','asc' => 'Artan','desc' => 'Azalan',# User
'users' => 'Kullanıcılar','permissions' => 'izinler','msg_sort_success' => 'Başarıyla mı sıralandı?!',### Config ###
'cfg_asset_revision' => 'Varlık revizyonu / istemci önbelleği zehirlenmesi','cfg_system_user' => 'Sistem kullanıcısı','cfg_show_impressum' => 'Alt bilgide baskı gösterilsin mi?','cfg_show_privacy' => 'Gizlilik bilgileri alt bilgide gösterilsin mi?','cfg_allow_guests' => 'GDOv7 konuk kullanıcı sistemi etkinleştirilsin mi?','cfg_siteshort_title_append' => 'Site kısa adı sayfa başlığına eklensin mi?','cfg_mail_403' => '403 hatalarında e-posta gönderilsin mi?','cfg_mail_404' => '404 hatalarında e-posta gönderilsin mi?','cfg_directory_indexing' => 'Dizin indeksleme etkinleştirilsin mi?','cfg_module_assets' => 'GDO kaynak dizininden varlıkları yükleyebilir misiniz?','cfg_dotfiles' => 'Gizli nokta dosyalarının okunmasına ve indekslenmesine izin verilsin mi?',### 403 ###
'mail_title_403' => '%s: 403 (%s)','mail_body_403' => 'Sevgili S,

%s tarihinde yasak bir URL ziyaret edildi.
URL: %s
Kullanıcılar

En iyi dileklerimle,
%2$s sistemi',

### 404 ###
'mail_title_404' => '%s: 404 (%s)','mail_body_404' => 'Sevgili S,

%s üzerinde bilinmeyen bir URL ziyaret edildi.
URL: %s
Kullanıcılar

En iyi dileklerimle,
%2$s sistemi',

'confirm_delete' => 'Bunu silmek istediğinizden emin misiniz?','md_switch_language' => 'Dili değiştir','gdt_redirect_to' => '%s adresine yönlendir...','unknown_permission' => 'Bu izin bilinmiyor','add_permissions' => 'İzin ekle','mt_sort' => '%s veritabanını sırala','mt_crud_create' => 'Yeni %s','mt_crud_update' => '%s yi düzenle','cronjob_method' => 'cronjob yöntemi','method' => 'yöntem','msg_installed_modules' => 'Yüklü modüller: %s.','mt_core_security' => 'GÜVENLİK.md','mt_core_robots' => 'robots.txt','mt_core_gettypes' => 'GDT meta verilerini al','mt_core_pathcompletion' => 'Yol Tamamlama','mt_ajax' => '%s veri getirme','creator_header' => '%s bunu %s\'e ekledi.','please_choose' => 'Lütfen seçin...','info_privacy_related_module' => '%s kurulu, verilerinizle ilgili bir modül.',
	't_privacy_core_toggles' => 'Çekirdek Yapılandırma',
	'privacy_settings' => 'Gizlilik ayarları',
	'health' => 'Şart',
	'mt_core_forcessl' => 'Şifrelemeye zorla',
	'err_nothing_happened' => 'Hiçbir hata oluşmadı, ancak garip bir şekilde değişiklik olmadı.',
];
