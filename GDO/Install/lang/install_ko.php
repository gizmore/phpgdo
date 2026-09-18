<?php
namespace GDO\Install\lang;

return [
	'install_title_1' => '환영합니다',
	'install_text_1' => 'GDOv7에 오신 것을 환영합니다. 여기에서 계속하십시오: %s',
	'install_text_2' => '데이터베이스를 사용하려는 경우 다음 mysql 명령을 실행하십시오.',
	'install_title_2' => '시스템-테스트',
	'install_title_2_tests' => '필수 요구 사항',
	'install_test_0' => 'PHP 버전 8.5.9가 지원됩니까?',
	'install_test_1' => '보호된 폴더에 쓰기가 가능합니까?',
	'install_test_2' => '파일 폴더에 쓰기가 가능한가요?',
	'install_test_3' => '임시 폴더에 쓰기가 가능한가요?',
	'install_test_4' => '자산 폴더에 쓰기가 가능합니까?',
	'install_test_5' => 'PHP mbstring이 설치되어 있습니까?',
	'install_test_6' => 'fileinfo 확장자를 사용할 수 있나요?',
	'install_test_7' => 'bcmath 확장이 설치되어 있습니까?',
	'install_test_8' => 'iconv 확장이 설치되어 있습니까?',
	'install_title_2_optionals' => '선택적 기능',
	'install_optional_0' => 'PHP 컬이 설치되어 있습니까?',
	'install_optional_1' => 'PHP gd가 설치되어 있습니까?',
	'install_optional_2' => 'PHP memcached가 설치되어 있나요?',
	'install_optional_3' => 'openssl 확장을 사용할 수 있나요?',
	'install_optional_4' => 'nodejs, npm 및 Yarn을 사용할 수 있나요?',
	'install_system_ok' => '귀하의 시스템은 GDOv7을 실행할 수 있습니다. %s을(를) 계속할 수 있습니다.',
	'install_system_not_ok' => '귀하의 시스템은 현재 GDOv7을 실행할 수 없습니다. %s 실행을 다시 시도할 수 있습니다.',
	'install_title_3' => 'GDO 구성',
	'mt_install_configure' => '구성 파일 쓰기',
	'install_config_section_site' => '사이트',
	'cfg_sitename' => '짧은 사이트 이름',
	'language' => '주요 언어',
	'timezone' => '시간대',
	'themes' => '테마',
	'force_ssl' => 'SSL을 강제하시겠습니까?',
	'log_request' => '모든 요청을 기록하시겠습니까?',
	'sess_samesite' => 'SESS-Samesite X-쿠키',
	'install_config_section_http' => 'HTTP',
	'sess_https' => 'TLS 쿠키만 보호하시겠습니까?',
	'install_config_section_files' => '파일',
	'enum_448' => '700',
	'enum_504' => '770',
	'enum_511' => '777',
	'enum_en' => '영어',
	'enum_de' => '독일어',
	'install_config_section_logging' => '로깅',
	'install_config_section_database' => '데이터베이스',
	'install_config_section_cache' => '캐시',
	'install_config_section_cookies' => '쿠키',
	'install_config_section_email' => '메일',
	'install_config_section_smtp' => 'SMTP 메일',
	'install_config_boxinfo_success' => '시스템이 견고해 보입니다. %s을(를) 계속할 수 있습니다',
	'save_config' => '저장',
	'test_config' => '테스트',
	'install_title_4' => 'GDO 모듈',
	'install_modules_info_text' => '여기에서 설치할 모듈을 선택할 수 있습니다. 종속성은 아직 100% 해결되지 않았습니다.',
	'install_modules_completed' => '모듈이 설치되었습니다. %s을(를) 계속할 수 있습니다',
	'err_disable_core_module' => '핵심 모듈은 비활성화할 수 없습니다.',
	'err_multiple_site_modules' => '사이트 모듈이 여러 개 있어서는 안 됩니다.',
	'err_missing_dependency' => '종속성이 누락되었습니다.',
	'module_priority' => '우선순위',
	'module_description' => '설명',
	'install_title_5' => '크론작업 구성',
	'install_cronjob_info' => '서버에 cronjob을 생성해야 합니다.
이것을 crontab 파일에 붙여넣을 수 있습니다:

%s

그런 다음 %s을(를) 계속할 수 있습니다.',
	'install_title_6' => '관리자 만들기',
	'info_install_admins' => '여기에서 GDOv7 설치를 위한 관리자 계정을 생성할 수 있습니다.',
	'mt_install_installadmins' => '관리자 만들기',
	'msg_admin_created' => '%s이라는 관리자가 생성되었거나 해당 비밀번호가 재설정되었습니다.',
	'install_title_7' => '자바스크립트 설치',
	'install_content_7' => '<p>이제 노드, npm, 원사 및 기타 자바스크립트 구성요소를 설치해야 합니다.</p>
<p>또는 이러한 종속성을 개별적으로 업로드해야 합니다.</p>
<p>Debian 컴퓨터에서 다음 명령을 실행하세요:<p>
<코드>
루트로:<br/>
<br/>
aptitude install nodejs nodejs-dev npm # 자바스크립트 설치<br/>
npm install -g Yarn # Yarn 설치<br/>
<br/>
phpgdo 사용자로서:<br/>
<br/>
CD www/phpgdo<br/>
./gdo_yarn.sh # 모듈 js 종속성 설치<br/>
</code>',
	'install_title_8' => '백업 가져오기',
	'mt_install_importbackup' => '백업 가져오기',
	'install_title_9' => 'htaccess 복사(선택사항)',
	'mt_install_copyhtaccess' => '기본 htaccess를 gdo6 루트에 복사',
	'copy_htaccess_info' => '<b>현재 존재하는 <i>.htaccess</i> 파일을 덮어씁니다!</b><br>그런 다음 %s을(를) 계속할 수 있습니다.',
	'copy_htaccess' => '기본 htaccess 복사',
	'install_title_10' => '보안',
	'mt_install_security' => '설치 마법사 및 보호된 폴더에 대한 액세스를 제거하여 설치를 완료합니다.',
	'protect_folders' => '폴더 보호',
	'install_title_11' => '웹서버',
	'mt_install_webserver' => 'WebServer를 추가로 구성하십시오. 다음은 구성에 대한 제안 사항입니다.',
	'msg_config_written' => '기본 구성 파일이 protected/%s에 기록되었습니다.',
	'msg_available_config' => '%s 모듈에 사용 가능한 구성 변수: %s.',
	'msg_set_config' => '%s 모듈의 %s에 대한 구성 변수가 현재 %s(으)로 설정되어 있습니다. 다른 예: %s',
	'msg_changed_config' => '%s 모듈의 %s에 대한 구성 변수가 %s에서 %s(으)로 설정되었습니다.',
	'msg_installing_modules' => '다음 종속 모듈을 설치하는 중: %s.',
	'bot_mail' => '봇 이메일',
	'bot_name' => '봇 이름',
	'admin_mail' => '관리자 이메일',
	'error_mail' => '오류 이메일',
	'msg_install_security' => '이제 gdo6 설치가 월드 와이드 웹에 대해 보호됩니다.',
	'msg_gdoadm_migrated_all' => '모든 GDO 데이터베이스 테이블이 자동으로 마이그레이션되었습니다.',
	'repl_adm_interactive' => '대화형 구성 도우미를 원하시나요?',
	'err_adm_iconfig' => '%s에 오류가 있습니다: "%s".',
];
