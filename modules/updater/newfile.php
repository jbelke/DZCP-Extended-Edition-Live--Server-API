<?php
function test2($input)
{
	$input['addons'] = kernel::string_to_array($input['addons'],true); //Uncompress

	#echo '<pre>';
	#print_r($input);
	#die();

	/*
	 * Array
(
    [call] => updater
    [mode] => check_version
    [version] => 1.0
    [build] => repo:dev:git:0176
    [edition] => Extended Edition
    [dbv] => 1600
    [addons] => Array
        (
            [HM-DZCP-Live] => Array
                (
                    [addon_name] => DZCP - Live!
                    [addon_autor] => Hammermaps.de
                    [addon_version] => 1.0
                    [addon_build_rev] => 0001
                )

            [HM-ProFTPD] => Array
                (
                    [addon_name] => ProFTPD Administrator
                    [addon_autor] => Hammermaps.de
                    [addon_version] => 1.0
                    [addon_build_rev] => 0001
                )

        )

)
	 */

	function RandNumber($e){


		for($i=0;$i<$e;$i++){
			$rand =  $rand .  rand(0, 9);
		}
		return $rand;

	}

	//Test
	$updates = array(); $b=1; $type=0;
	for($i = 1; $i <= 8; $i++)
	{
		$title1 = ' for CMS Kernel, Github: <a class="live" href="https://github.com/Hammermaps-DEV/DZCP-Extended-Edition/commit/c78f77cfedefc63f53def365eb8b5aca0c90fca1" target="_blank">#'.kernel::generatePW(6).'</a>';
		$title2 = ' for CMS Kernel, Github: <a class="live" href="https://github.com/Hammermaps-DEV/DZCP-Extended-Edition/commit/c78f77cfedefc63f53def365eb8b5aca0c90fca1" target="_blank">#'.kernel::generatePW(6).'</a>';
		$date = '03.03.2012';
		$version = '1.1.1';

		if($type > 5)
			$type = 0;
		/*
		 * update_available => 1/0
		* updates => array()
		* 			[1] => id => int(11)
		* 			[1] => type => 0-5
		* 			[1] => version => double
		* 			[1] => date => string
		* 			[1] => text => html
		* 			[1] => tile => sting
		* 			[1] => core => 1/0
		*/

		$text = 'TEST TEXT | TEST TEXT | TEST TEXT | TEST TEXT | TEST TEXT | TEST TEXT | TEST TEXT | TEST TEXT | TEST TEXT | TEST TEXT | TEST TEXT | <p>
            TEST TEXT | TEST TEXT | TEST TEXT | TEST TEXT | TEST TEXT | TEST TEXT | TEST TEXT | TEST TEXT | TEST TEXT | TEST TEXT | TEST TEXT | <p> TEST TEXT |
            TEST TEXT | TEST TEXT | TEST TEXT | TEST TEXT | TEST TEXT | TEST TEXT | TEST TEXT | TEST TEXT | TEST TEXT | TEST TEXT | TEST TEXT | <p>
            TEST TEXT | TEST TEXT | TEST TEXT | TEST TEXT | TEST TEXT | TEST TEXT | TEST TEXT | TEST TEXT | TEST TEXT | TEST TEXT | TEST TEXT | <p>
            TEST TEXT | TEST TEXT | TEST TEXT | TEST TEXT | TEST TEXT | TEST TEXT | TEST TEXT | TEST TEXT | TEST TEXT | TEST TEXT | TEST TEXT | <p>';

		$updates[] = kernel::array_to_string(array('id' => $b, 'type' => convert::ToString($type), 'rev' => '#'.RandNumber(5), 'version' => $version, 'date' => $date, 'text' => $text, 'tile' => $title1, 'core' => '1'),true);
		$b++;

		$updates[] = kernel::array_to_string(array('id' => $b, 'type' => convert::ToString($type), 'rev' => '#'.RandNumber(5), 'version' => $version, 'date' => $date, 'text' => $text, 'tile' => $title2, 'core' => '0'),true);
		$b++; $type++;
	}

	return array('update_available' => '1', 'updates' => $updates);
}

