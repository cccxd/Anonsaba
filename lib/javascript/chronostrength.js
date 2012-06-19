/*********************************************************
**
**   Plugin by Small Hadron Collider
**   http://www.smallhadroncollider.com
**
**   Distributed under a Creative Commons by-sa License
**   http://creativecommons.org/licenses/by-sa/3.0/
**
**   For usage see:
**   http://plugins.jquery.com/project/chronoStrength
**
*********************************************************/

jQuery.fn.chronoStrength = function(options)
{
	var defaults =
	{  
		calculationsPerSecond: 10000000,
		placeInto: 'none'
	};  
	
	var options = jQuery.extend(defaults, options); 
	
	if (options.placeInto == 'none')
	{
		jQuery(this).after('<div class="chronostrength-box"></div>');
		var placeInto = 'div.chronostrength-box';
	}
	else
	{
		var placeInto = options.placeInto;
	}
	
	jQuery(this).keyup(function(event)
	{
		if (event.keyCode == 27 || event.charCode == 27)
		{
			$(this).val("");
		}
		
		var password = $(this).val();
		
		if (password != "")
		{			
			var calculationsPerSecond = options.calculationsPerSecond;
			strength = chronoStrengthPasswordStrength(password, calculationsPerSecond);
			
			jQuery(placeInto).html(strength);
		}
		else
		{
			jQuery(placeInto).html('');
		}
	});
};

jQuery.chronoStrength = function(password)
{
	return chronoStrengthPasswordStrength(password, 10000000);
}

jQuery.chronoStrengthWithCustomSpeed = function(password, calculationsPerSecond)
{
	return chronoStrengthPasswordStrength(password, calculationsPerSecond);
}

function chronoStrengthPasswordStrength(password, calculationsPerSecond)
{
	var length = password.length;
	
	if (length > 2 && length < 9)
	{
		for (var i=0; i<chronoStrengthArrayOfPasswords[length].length; i++)
		{
			if (password.toLowerCase() == chronoStrengthArrayOfPasswords[length][i])
			{
				return 'One of the 500 most common passwords';
			}
		}
	}
	
	var possibleCharacters = 0;
	if (password.match(/[a-z]/)) { possibleCharacters += 26; }
	if (password.match(/[A-Z]/)) { possibleCharacters += 26; }
	if (password.match(/\d/)) { possibleCharacters += 10; }
	if (password.match(/[!,@,#,$,%,^,&,*,?,_,~,-,(,)]/) ) {possibleCharacters += 13};

	// Unicode Latin
	if (password.match(/[\u00A1-\u00FF]+/)) {possibleCharacters += 94};
	if (password.match(/[\u0100-\u017F]+/)) {possibleCharacters += 128};
	if (password.match(/[\u0180-\u024F]+/)) {possibleCharacters += 208};
	if (password.match(/[\u2C60-\u2C7F]+/)) {possibleCharacters += 32};
	if (password.match(/[\uA720-\uA7FF]+/)) {possibleCharacters += 29};
	
	// Unicode Cyrillic
	if (password.match(/[\u0500-\u052F]+/)) {possibleCharacters += 40};
	if (password.match(/[\uA640-\uA69F]+/)) {possibleCharacters += 74};
	
	
	var possibleCombinations = Math.pow(possibleCharacters, password.length);
	var computerTimeInSecs = possibleCombinations / calculationsPerSecond;

	var arrayOfPeriods = new Array();
	arrayOfPeriods['minute'] = 60; // Minute in seconds
	arrayOfPeriods['hour'] = 3600; // Hour in seconds
	arrayOfPeriods['day'] = 86400; // Day in seconds
	arrayOfPeriods['year'] = 31556926; // Year in seconds
	arrayOfPeriods['thousand years'] = 31556926 * Math.pow(10,3);
	arrayOfPeriods['million years'] = 31556926 * Math.pow(10,6);
	arrayOfPeriods['billion years'] = 31556926 * Math.pow(10,9);
	arrayOfPeriods['trillion years'] = 31556926 * Math.pow(10,12);
	arrayOfPeriods['quadrillion years'] = 31556926 * Math.pow(10,15);
	arrayOfPeriods['quintillion years'] = 31556926 * Math.pow(10,18);
	arrayOfPeriods['sextillion years'] = 31556926 * Math.pow(10,21);
	arrayOfPeriods['septillion years'] = 31556926 * Math.pow(10,24);
	arrayOfPeriods['octillion years'] = 31556926 * Math.pow(10,27);
	arrayOfPeriods['nonillion years'] = 31556926 * Math.pow(10,30);
	arrayOfPeriods['decillion years'] = 31556926 * Math.pow(10,33);
	arrayOfPeriods['undecillion years'] = 31556926 * Math.pow(10,36);
	arrayOfPeriods['duodecillion years'] = 31556926 * Math.pow(10,39);
	arrayOfPeriods['tredecillion years'] = 31556926 * Math.pow(10,42);
	arrayOfPeriods['quattuordecillion years'] = 31556926 * Math.pow(10,45);
	arrayOfPeriods['quindecillion years'] = 31556926 * Math.pow(10,48);
	arrayOfPeriods['sexdecillion years'] = 31556926 * Math.pow(10,51);
	arrayOfPeriods['septendecillion years'] = 31556926 * Math.pow(10,54);
	arrayOfPeriods['octodecillion years'] = 31556926 * Math.pow(10,57);
	arrayOfPeriods['novemdecillion years'] = 31556926 * Math.pow(10,60);
	arrayOfPeriods['vigintillion years'] = 31556926 * Math.pow(10,63);
	arrayOfPeriods['unvigintillion years'] = 31556926 * Math.pow(10,66);
	arrayOfPeriods['duovigintillion years'] = 31556926 * Math.pow(10,69);
	arrayOfPeriods['tresvigintillion years'] = 31556926 * Math.pow(10,72);
	arrayOfPeriods['quattuorvigintillion years'] = 31556926 * Math.pow(10,75);
	arrayOfPeriods['quinquavigintillion years'] = 31556926 * Math.pow(10,78);
	arrayOfPeriods['sesvigintillion years'] = 31556926 * Math.pow(10,81);
	arrayOfPeriods['septemvigintillion years'] = 31556926 * Math.pow(10,84);
	arrayOfPeriods['octovigintillion years'] = 31556926 * Math.pow(10,87);
	arrayOfPeriods['novemvigintillion years'] = 31556926 * Math.pow(10,90);
	arrayOfPeriods['trigintillion years'] = 31556926 * Math.pow(10,93);
	arrayOfPeriods['untrigintillion years'] = 31556926 * Math.pow(10,96);
	arrayOfPeriods['duotrigintillion years'] = 31556926 * Math.pow(10,99);
	arrayOfPeriods['googol years'] = 31556926 * Math.pow(10,100);
	arrayOfPeriods['trestrigintillion years'] = 31556926 * Math.pow(10,102);
	arrayOfPeriods['quattuortrigintillion years'] = 31556926 * Math.pow(10,105);
	arrayOfPeriods['quinquatrigintillion years'] = 31556926 * Math.pow(10,108);
	arrayOfPeriods['sestrigintillion years'] = 31556926 * Math.pow(10,111);
	arrayOfPeriods['septentrigintillion years'] = 31556926 * Math.pow(10,114);
	arrayOfPeriods['octotrigintillion years'] = 31556926 * Math.pow(10,117);
	arrayOfPeriods['noventrigintillion years'] = 31556926 * Math.pow(10,120);
	arrayOfPeriods['quadragintillion years'] = 31556926 * Math.pow(10,123);
	arrayOfPeriods['quinquagintillion years'] = 31556926 * Math.pow(10,153);
	arrayOfPeriods['sexagintillion years'] = 31556926 * Math.pow(10,183);
	arrayOfPeriods['septuagintillion years'] = 31556926 * Math.pow(10,213);
	arrayOfPeriods['octogintillion years'] = 31556926 * Math.pow(10,243);
	arrayOfPeriods['nonagintillion years'] = 31556926 * Math.pow(10,273);
	arrayOfPeriods['centillion years'] = 31556926 * Math.pow(10,303);
	arrayOfPeriods['uncentillion years'] = 31556926 * Math.pow(10,306);
	arrayOfPeriods['duocentillion years'] = 31556926 * Math.pow(10,309);
	arrayOfPeriods['trescentillion years'] = 31556926 * Math.pow(10,312);
	arrayOfPeriods['decicentillion years'] = 31556926 * Math.pow(10,333);
	arrayOfPeriods['undecicentillion years'] = 31556926 * Math.pow(10,336);
	arrayOfPeriods['viginticentillion years'] = 31556926 * Math.pow(10,363);
	arrayOfPeriods['unviginticentillion years'] = 31556926 * Math.pow(10,366);
	arrayOfPeriods['trigintacentillion years'] = 31556926 * Math.pow(10,393);
	arrayOfPeriods['quadragintacentillion years'] = 31556926 * Math.pow(10,423);
	arrayOfPeriods['quinquagintacentillion years'] = 31556926 * Math.pow(10,453);
	arrayOfPeriods['sexagintacentillion years'] = 31556926 * Math.pow(10,483);
	arrayOfPeriods['septuagintacentillion years'] = 31556926 * Math.pow(10,513);
	arrayOfPeriods['octogintacentillion years'] = 31556926 * Math.pow(10,543);
	arrayOfPeriods['nonagintacentillion years'] = 31556926 * Math.pow(10,573);
	arrayOfPeriods['ducentillion years'] = 31556926 * Math.pow(10,603);
	arrayOfPeriods['trecentillion years'] = 31556926 * Math.pow(10,903);
	arrayOfPeriods['quadringentillion years'] = 31556926 * Math.pow(10,1203);
	arrayOfPeriods['quingentillion years'] = 31556926 * Math.pow(10,1503);
	arrayOfPeriods['sescentillion years'] = 31556926 * Math.pow(10,1803);
	arrayOfPeriods['septingentillion years'] = 31556926 * Math.pow(10,2103);
	arrayOfPeriods['octingentillion years'] = 31556926 * Math.pow(10,2403);
	arrayOfPeriods['nongentillion years'] = 31556926 * Math.pow(10,2703);
	arrayOfPeriods['millinillion years'] = 31556926 * Math.pow(10,3003);
	
	var periodType = 'second';
	var strength = '';
	
	if (computerTimeInSecs < 1) { strength = computerTimeInSecs+" seconds";}
	else
	{
		var intoThousands = 's';
		var intoMinutes = '';
		var newTime = Math.floor(computerTimeInSecs);
		
		for (var i in arrayOfPeriods)
		{
			if (computerTimeInSecs < arrayOfPeriods[i]) { break; }
			else
			{
				if (i == "thousand years") { intoThousands = ''; }
				if (i == "minute") { intoMinutes = 'Crackable in about '; }
				newTime = Math.floor(computerTimeInSecs / arrayOfPeriods[i]);
				periodType = i;
			}
		}
		
		if (newTime == 1)
		{
			var aType = 'a ';
			if (periodType == 'hour' || periodType == 'octillion years')
			{
				aType = 'an ';
			}
			
			strength = "Crackable in about "+aType+periodType;
		}
		else
		{
			newTime += '';
			
			var regex = /(\d+)(\d{3})/;
			
			while (regex.test(newTime))
			{
				newTime = newTime.replace(regex, '$1' + ',' + '$2');
			}
			
			strength = intoMinutes+newTime+" "+periodType+intoThousands;
		}
	}
	
	return strength;
};

var chronoStrengthArrayOfPasswords = new Array();

chronoStrengthArrayOfPasswords[3] = ['god','sex'];

chronoStrengthArrayOfPasswords[4] = ['1234','cool','1313','star','golf','bear','dave','pass','aaaa','6969','jake','matt','1212','fish','fuck','porn','4321','2000','4128','test','shit','love','baby','cunt','mark','3333','john','sexy','5150','4444','2112','fred','mike','1111','tits','paul','mine','king','fire','5555','slut','girl','2222','asdf','time','7777','rock','xxxx','ford','dick','bill','wolf','blue','alex','cock','beer','eric','6666','jack'];

chronoStrengthArrayOfPasswords[5] = ['beach','great','black','pussy','12345','frank','tiger','japan','money','naked','11111','angel','stars','apple','porno','steve','viper','horny','ou812','kevin','buddy','teens','young','jason','lucky','girls','lover','brian','kitty','bubba','happy','cream','james','xxxxx','booty','kelly','boobs','penis','eagle','white','enter','chevy','smith','chris','green','sammy','super','magic','power','enjoy','scott','david','video','qwert','paris','women','juice','dirty','music','peter','bitch','house','hello','billy','movie'];

chronoStrengthArrayOfPasswords[6] = ['123456','prince','guitar','butter','jaguar','united','turtle','muffin','cooper','nascar','redsox','dragon','zxcvbn','qwerty','tomcat','696969','654321','murphy','987654','amanda','brazil','wizard','hannah','lauren','master','doctor','eagle1','gators','squirt','shadow','mickey','mother','monkey','bailey','junior','nathan','abc123','knight','alexis','iceman','fuckme','tigers','badboy','bonnie','purple','debbie','angela','jordan','andrea','spider','harley','ranger','dakota','booger','iwantu','aaaaaa','lovers','player','flyers','suckit','hunter','beaver','morgan','matrix','boomer','runner','batman','scooby','edward','thomas','walter','helpme','gordon','tigger','jackie','casper','robert','booboo','boston','monica','stupid','access','coffee','braves','xxxxxx','yankee','saturn','buster','gemini','barney','apples','soccer','rabbit','victor','august','hockey','peanut','tucker','killer','canada','george','johnny','sierra','blazer','andrew','spanky','doggie','232323','winter','zzzzzz','brandy','gunner','beavis','compaq','horney','112233','carlos','arthur','dallas','tennis','sophie','ladies','calvin','shaved','pepper','giants','surfer','fender','samson','austin','member','blonde','blowme','fucked','daniel','donald','golden','golfer','cookie','summer','bronco','racing','sandra','hammer','pookie','joseph','hentai','joshua','diablo','birdie','maggie','sexsex','little','biteme','666666','topgun','ashley','willie','sticky','cowboy','animal','silver','yamaha','qazwsx','fucker','justin','skippy','orange','banana','lakers','marvin','merlin','driver','rachel','marine','slayer','angels','asdfgh','bigdog','vagina','apollo','cheese','toyota','parker','maddog','travis','121212','london','hotdog','wilson','sydney','martin','dennis','voodoo','ginger','magnum','action','nicole','carter','erotic','sparky','jasper','777777','yellow','smokey','dreams','camaro','xavier','teresa','freddy','secret','steven','jeremy','viking','falcon','snoopy','russia','taylor','nipple','111111','eagles','131313','winner','tester','123123','miller','rocket','legend','flower','theman','please','oliver','albert'];

chronoStrengthArrayOfPasswords[7] = ['porsche','rosebud','chelsea','amateur','7777777','diamond','tiffany','jackson','scorpio','cameron','testing','shannon','madison','mustang','bond007','letmein','michael','gateway','phoenix','thx1138','raiders','forever','peaches','jasmine','melissa','gregory','cowboys','dolphin','charles','cumshot','college','bulldog','1234567','ncc1701','gandalf','leather','cumming','hunting','charlie','rainbow','asshole','bigcock','fuckyou','jessica','panties','johnson','naughty','brandon','anthony','william','ferrari','chicken','heather','chicago','voyager','yankees','rangers','packers','newyork','trouble','bigtits','winston','thunder','welcome','bitches','warrior','panther','broncos','richard','8675309','private','zxcvbnm','nipples','blondes','fishing','matthew','hooters','patrick','freedom','fucking','extreme','blowjob','captain','bigdick','abgrtyu','chester','monster','maxwell','arsenal','crystal','rebecca','pussies','florida','phantom','scooter','success'];

chronoStrengthArrayOfPasswords[8] = ['firebird','password','12345678','steelers','mountain','computer','baseball','xxxxxxxx','football','qwertyui','jennifer','danielle','sunshine','starwars','whatever','nicholas','swimming','trustno1','midnight','princess','startrek','mercedes','superman','bigdaddy','maverick','einstein','dolphins','hardcore','redwings','cocacola','michelle','victoria','corvette','butthead','marlboro','srinivas','internet','redskins','11111111','access14','rush2112','scorpion','iloveyou','samantha','mistress'];
