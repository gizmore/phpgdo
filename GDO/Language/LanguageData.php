<?php
namespace GDO\Language;

use GDO\Util\FileUtil;

/**
 * This class contains Language data.
 * English Name | Native Name | iso-639-3 | iso-639-1
 *
 * @author gizmore
 */
final class LanguageData
{

	public static function onInstall()
	{
		foreach (self::getLanguages() as $data)
		{
// 			list($en, $native, $iso3, $iso2) = $data;
			$iso2 = $data[3];
			if (FileUtil::isFile(GDO_PATH . 'GDO/Language/img/' . strtolower($iso2) . '.png'))
			{
				if (!GDO_Language::getById($iso2))
				{
					GDO_Language::blank([
						'lang_iso' => $iso2,
					])->insert();
				}
			}
		}
	}

	public static function getLanguages()
	{
		# English Name | Native Name | iso-639-3 | iso-639-1
		static $languages = [
			['English', 'English', 'eng', 'en'],
			['German', 'Deutsch', 'ger', 'de'],
			['French', 'Française', 'fre', 'fr'],
			['Bulgarian', 'български език', 'bul', 'bg'],
			['Brazil', 'Brazil', 'bra', 'br'],
			['Spanish', 'español', 'spa', 'es'],
			['Chinese', '汉语 / 漢語', 'chi', 'zh'],
			['Croatian', 'hrvatski', 'cro', 'hr'],
			['Albanian', 'Shqip', 'alb', 'sq'],
			['Arabic', 'العربية', 'ara', 'ar'],
// 			array('Amazigh', '', 'ama', ''),
			['Catalan', 'català', 'cat', 'ca'],
			['Armenian', 'Հայերեն', 'arm', 'hy'],
			['Azerbaijani', 'Azərbaycan / Азәрбајҹан / آذربایجان دیلی', 'aze', 'az'],
			['Bengali', 'বাংলা', 'ben', 'bn'],
			['Dutch', 'Nederlands', 'dut', 'nl'],
			['Bosnian', 'bosanski/босански', 'bos', 'bs'],
			['Serbian', 'Српски / Srpski ', 'ser', 'sr'],
			['Portuguese', 'português', 'por', 'pt'],
			['Greek', 'Ελληνικά / Ellīniká', 'gre', 'el'],
			['Turkish', 'Türkçe', 'tur', 'tr'],
			['Czech', 'Čeština', 'cze', 'cs'],
			['Danish', 'dansk', 'dan', 'da'],
			['Finnish', 'suomi', 'fin', 'fi'],
			['Swedish', 'svenska', 'swe', 'sv'],
			['Hungarian', 'magyar', 'hun', 'hu'],
			['Icelandic', 'Íslenska', 'ice', 'is'],
			['Hindi', 'हिन्दी / हिंदी', 'hin', 'hi'],
			['Persian', 'فارسی', 'per', 'fa'],
			['Kurdish', 'Kurdî / کوردی', 'kur', 'ku'],
			['Irish', 'Gaeilge', 'iri', 'ga'],
			['Hebrew', 'עִבְרִית / \'Ivrit', 'heb', 'he'],
			['Italian', 'Italiano', 'ita', 'it'],
			['Japanese', '日本語 / Nihongo', 'jap', 'ja'],
			['Korean', '한국어 / 조선말', 'kor', 'ko'],
			['Latvian', 'latviešu valoda', 'lat', 'lv'],
			['Lithuanian', 'Lietuvių kalba', 'lit', 'lt'],
			['Luxembourgish', 'Lëtzebuergesch', 'lux', 'lb'],
			['Macedonian', 'Македонски јазик / Makedonski jazik', 'mac', 'mk'],
			['Malay', 'Bahasa Melayu / بهاس ملايو', 'mal', 'ms'],
			['Dhivehi', 'Dhivehi / Mahl', 'dhi', 'dv'],
// 			array("Montenegrin", "Црногорски / Crnogorski", "mon", ''),
			['Maori', 'Māori', 'mao', 'mi'],
			['Norwegian', 'norsk', 'nor', 'no'],
			['Filipino', 'Filipino', 'fil', 'tl'],
			['Polish', 'język polski', 'pol', 'pl'],
			['Romanian', 'română / limba română', 'rom', 'ro'],
			['Russian', 'Русский язык', 'rus', 'ru'],
			['Slovak', 'slovenčina', 'slo', 'sk'],
			['Mandarin', '官話 / Guānhuà', 'man', 'zh'],
			['Tamil', 'தமிழ', 'tam', 'ta'],
			['Slovene', 'slovenščina', 'slv', 'sl'],
			['Zulu', 'isiZulu', 'zul', 'zu'],
			['Xhosa', 'isiXhosa', 'xho', 'xh'],
			['Afrikaans', 'Afrikaans', 'afr', 'af'],
// 			array('Northern Sotho', 'Sesotho sa Leboa', 'nso', '--'),
			['Tswana', 'Setswana / Sitswana', 'tsw', 'tn'],
			['Sotho', 'Sesotho', 'sot', 'st'],
			['Tsonga', 'Tsonga', 'tso', 'ts'],
			['Thai', 'ภาษาไทย / phasa thai', 'tha', 'th'],
			['Ukrainian', 'українська мова', 'ukr', 'uk'],
			['Vietnamese', 'Tiếng Việt', 'vie', 'vi'],
			['Pashto', 'پښت', 'pas', 'ps'],
			['Samoan', 'gagana Sāmoa', 'sam', 'sm'],
// 			array('Bajan', 'Barbadian Creole', 'baj', '--'),
			['Belarusian', 'беларуская мова', 'bel', 'be'],
			['Dzongkha', '', 'dzo', 'dz'],
// 			array('Quechua', '', 'que', ''),
// 			array('Aymara', '', 'aym', ''),
// 			array('Setswana', '', 'set', ''),
// 			array('Bruneian', '', 'bru', ''),
// 			array('Indigenous', '', 'ind', ''),
// 			array('Kirundi', '', 'kir', ''),
// 			array('Swahili', '', 'swa', ''),
// 			array('Khmer', '', 'khm', ''),
// 			array('Sango', '', 'san', ''),
// 			array('Lingala', '', 'lin', ''),
// 			array('Kongo/Kituba', '', 'kon', ''),
// 			array('Tshiluba', '', 'tsh', ''),
// 			array('Afar', '', 'afa', ''),
// 			array('Somali', '', 'som', ''),
// 			array('Fang', '', 'fan', ''),
// 			array('Bube', '', 'bub', ''),
// 			array('Annobonese', '', 'ann', ''),
// 			array('Tigrinya', '', 'tig', ''),
// 			array('Estonian', 'Eesti', 'est', 'et'),
// 			array('Amharic', '', 'amh', ''),
// 			array('Faroese', '', 'far', ''),
// 			array('Bau Fijian', '', 'bau', ''),
// 			array('Hindustani', '', 'hit', ''),
// 			array('Tahitian', '', 'tah', ''),
// 			array('Georgian', '', 'geo', ''),
// 			array('Greenlandic', '', 'grl', ''),
// 			array('Chamorro', '', 'cha', ''),
// 			array('Crioulo', '', 'cri', ''),
// 			array('Haitian Creole', '', 'hai', ''),
// 			array('Indonesian', '', 'inn', ''),
// 			array('Kazakh', '', 'kaz', ''),
// 			array('Gilbertese', '', 'gil', ''),
// 			array('Kyrgyz', '', 'kyr', ''),
// 			array('Lao', '', 'lao', ''),
// 			array('Southern Sotho', '', 'sso', ''),
// 			array('Malagasy', '', 'mag', ''),
// 			array('Chichewa', '', 'chw', ''),
// 			array('Maltese', '', 'mat', ''),
// 			array('Marshallese', '', 'mar', ''),
// 			array('Moldovan', '', 'mol', ''),
// 			array('Gagauz', '', 'gag', ''),
// 			array('Monegasque', '', 'moq', ''),
// 			array('Mongolian', '', 'mgl', ''),
// 			array('Burmese', '', 'bur', ''),
// 			array('Oshiwambo', '', 'osh', ''),
// 			array('Nauruan', '', 'nau', ''),
// 			array('Nepal', '', 'nep', ''),
// 			array('Papiamento', '', 'pap', ''),
// 			array('Niuean', '', 'niu', ''),
// 			array('Norfuk', '', 'nfk', ''),
// 			array('Carolinian', '', 'car', ''),
// 			array('Urdu', 'اردو', 'urd', 'ur'),
// 			array('Palauan', '', 'pal', ''),
// 			array('Tok Pisin', '', 'tok', ''),
// 			array('Hiri Motu', '', 'hir', ''),
// 			array('Guarani', '', 'gua', ''),
// 			array('Pitkern', '', 'pit', ''),
// 			array('Kinyarwanda', '', 'kin', ''),
// 			array('Antillean Creole', '', 'ant', ''),
// 			array('Wolof', '', 'wol', ''),
// 			array('Sinhala', '', 'sin', ''),
// 			array('Sranan Tongo', '', 'sra', ''),
// 			array('Swati', '', 'swt', ''),
// 			array('Syrian', '', 'syr', ''),
// 			array('Tajik', '', 'taj', ''),
// 			array('Tetum', '', 'tet', ''),
// 			array('Tokelauan', '', 'tol', ''),
// 			array('Tongan', '', 'ton', ''),
// 			array('Turkmen', '', 'tkm', ''),
// 			array('Uzbek', '', 'uzb', ''),
// 			array('Dari', '', 'dar', ''),
// 			array('Tuvaluan', '', 'tuv', ''),
// 			array('Bislama', '', 'bis', ''),
// 			array('Uvean', '', 'uve', ''),
// 			array('Futunan', '', 'fut', ''),
// 			array('Shona', '', 'sho', ''),
// 			array('Sindebele', '', 'sid', ''),
// 			array('Taiwanese', '', 'tai', ''),
// 			array('Manx', '', 'max', ''),
			['Fanmglish', 'Famster', 'fam', 'xf'],
			['Bot', 'BotJSON', 'bot', 'xb'],
			['Ibdes', 'RFCBotJSON', 'ibd', 'xi'],
			['Test Japanese', 'Test Japanese', 'ori', 'xo'],
		];
		return $languages;
	}

}
