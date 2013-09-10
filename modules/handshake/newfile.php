<?php
function send_handshake($input)
{
	$input['cryptkey_use'];

	return array('maintenance' => false, 'online' => true, 'name' => 'Hammermaps.de Primary');
}

