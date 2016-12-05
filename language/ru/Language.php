<?php

$language = [
	'error' => 'Ошибка',
	'authorization' => [
		1 => 'В поле Login/Email не допустимые символы',
		2 => 'Авторизация не была произведена. Возможно, Вы ввели неверный Login/Email или пароль.',

	],

	'registration' => [
		1 => 'Администратором сайта была отключена поддержка регистрации на сайте.',
		2 => 'На сайте уже зарегистрировано максимально допустимое количество пользователей. Попробуйте зарегистрироваться позже.',
		3 => 'Вы уже авторизованы на сайте под зарегистрированным аккаунтом.',
		4 => 'Недопустимая длина Login. Login не может быть менее 3 символов и более 40 символов!',
		5 => 'Вы используете недопустимый Login!',
		6 => 'Введён неверный Email адрес!',
		7 => 'Длина пароля должна быть не менее 8!',
		8 => 'Пользователь с таким Login или Email адресом уже зарегистрирован!',
		9 => 'Активация аккаунта',
		10 => 'Запрос на регистрацию принят. Администрация сайта требует реальности всех вводимых Email-адресов. Через 10 минут (возможно и раньше) Вы получите письмо с инструкциями для следующего шага. Ещё немного, и Вы будете зарегистрированы на сайте. Если в течении этого времени Вы не получили письма с подтверждением, то возможно, оно попало в папку со спамом. Пожалуйста, проверьте содержимое этой папки. В противном случае повторите попытку, используя другой Email адрес или обратитесь к администратору сайта.',
		11 => 'Ошибка активации аккаунта. Попробуйте заново.',
		12 => 'Благодарим Вас за регистрацию! Теперь Вы можете авторизоваться на сайте, используя Ваш Login/Email и пароль.',

	],

	'vk_login' => [
		1 => '',
		2 => 'Ошибка авторизации. Попробуйте заново.',
		3 => 'Ошибка активации аккаунта. Попробуйте заново.',
	],

	'userPanel' => [
		1 => 'Гость',
	],

	'langTranslate' => [
		'а' => 'a', 'б' => 'b', 'в' => 'v',
		'г' => 'g', 'д' => 'd', 'е' => 'e',
		'ё' => 'e', 'ж' => 'zh', 'з' => 'z',
		'и' => 'i', 'й' => 'y', 'к' => 'k',
		'л' => 'l', 'м' => 'm', 'н' => 'n',
		'о' => 'o', 'п' => 'p', 'р' => 'r',
		'с' => 's', 'т' => 't', 'у' => 'u',
		'ф' => 'f', 'х' => 'h', 'ц' => 'c',
		'ч' => 'ch', 'ш' => 'sh', 'щ' => 'sch',
		'ь' => '', 'ы' => 'y', 'ъ' => '',
		'э' => 'e', 'ю' => 'yu', 'я' => 'ya',
		"ї" => "yi", "є" => "ye",

		'А' => 'A', 'Б' => 'B', 'В' => 'V',
		'Г' => 'G', 'Д' => 'D', 'Е' => 'E',
		'Ё' => 'E', 'Ж' => 'Zh', 'З' => 'Z',
		'И' => 'I', 'Й' => 'Y', 'К' => 'K',
		'Л' => 'L', 'М' => 'M', 'Н' => 'N',
		'О' => 'O', 'П' => 'P', 'Р' => 'R',
		'С' => 'S', 'Т' => 'T', 'У' => 'U',
		'Ф' => 'F', 'Х' => 'H', 'Ц' => 'C',
		'Ч' => 'Ch', 'Ш' => 'Sh', 'Щ' => 'Sch',
		'Ь' => '', 'Ы' => 'Y', 'Ъ' => '',
		'Э' => 'E', 'Ю' => 'Yu', 'Я' => 'Ya',
		"Ї" => "yi", "Є" => "ye",
		"À"=>"A", "à"=>"a", "Á"=>"A", "á"=>"a",
		"Â"=>"A", "â"=>"a", "Ä"=>"A", "ä"=>"a",
		"Ã"=>"A", "ã"=>"a", "Å"=>"A", "å"=>"a",
		"Æ"=>"AE", "æ"=>"ae", "Ç"=>"C", "ç"=>"c",
		"Ð"=>"D", "È"=>"E", "è"=>"e", "É"=>"E",
		"é"=>"e", "Ê"=>"E", "ê"=>"e", "Ì"=>"I",
		"ì"=>"i", "Í"=>"I", "í"=>"i", "Î"=>"I",
		"î"=>"i", "Ï"=>"I", "ï"=>"i", "Ñ"=>"N",
		"ñ"=>"n", "Ò"=>"O", "ò"=>"o", "Ó"=>"O",
		"ó"=>"o", "Ô"=>"O", "ô"=>"o", "Ö"=>"O",
		"ö"=>"o", "Õ"=>"O", "õ"=>"o", "Ø"=>"O",
		"ø"=>"o", "Œ"=>"OE", "œ"=>"oe", "Š"=>"S",
		"š"=>"s", "Ù"=>"U", "ù"=>"u", "Û"=>"U",
		"û"=>"u", "Ú"=>"U", "ú"=>"u", "Ü"=>"U",
		"ü"=>"u", "Ý"=>"Y", "ý"=>"y", "Ÿ"=>"Y",
		"ÿ"=>"y", "Ž"=>"Z", "ž"=>"z", "Þ"=>"B",
		"þ"=>"b", "ß"=>"ss", "£"=>"pf", "¥"=>"ien",
		"ð"=>"eth", "ѓ"=>"r"

	],

	'relatesWord' => [
		'e' => '[eеё]', 'r' => '[rг]', 't' => '[tт]',
		'y' => '[yу]', 'u' => '[uи]', 'i' => '[i1l!]',
		'o' => '[oо0]', 'p' => '[pр]', 'a' => '[aа]',
		's' => '[s5]', 'w' => 'w', 'q' => 'q',
		'd' => 'd', 'f' => 'f', 'g' => '[gд]',
		'h' => '[hн]', 'j' => 'j', 'k' => '[kк]',
		'l' => '[l1i!]', 'z' => 'z', 'x' => '[xх%]',
		'c' => '[cс]', 'v' => '[vuи]', 'b' => '[bвь]',
		'n' => '[nпл]', 'm' => '[mм]', 'й' => '[йиu]',
		'ц' => 'ц', 'у' => '[уy]', 'е' => '[еeё]',
		'н' => '[нh]', 'г' => '[гr]', 'ш' => '[шwщ]',
		'щ' => '[щwш]', 'з' => '[з3э]', 'х' => '[хx%]',
		'ъ' => '[ъь]', 'ф' => 'ф', 'ы' => '(ы|ь[i1l!]?)',
		'в' => '[вb]', 'а' => '[аa]', 'п' => '[пn]',
		'р' => '[рp]', 'о' => '[оo0]', 'л' => '[лn]',
		'д' => 'д', 'ж' => 'ж', 'э' => '[э3з]',
		'я' => '[я]', 'ч' => '[ч4]', 'с' => '[сc]',
		'м' => '[мm]', 'и' => '[иuй]', 'т' => '[тt]',
		'ь' => '[ьb]', 'б' => '[б6]', 'ю' => '(ю|[!1il][oо0])',
		'ё' => '[ёеe]', '1' => '[1il!]', '2' => '2',
		'3' => '[3зэ]', '4' => '[4ч]', '5' => '[5s]',
		'6' => '[6б]', '7' => '7', '8' => '8', '9' => '9',
		'0' => '[0оo]', '_' => '_', '#' => '#', '%' => '[%x]',
		'^' => '[^~]', '(' => '[(]', ')' => '[)]', '=' => '=',
		'.' => '[.]', '-' => '-', '[' => '[\[]'

	],

];