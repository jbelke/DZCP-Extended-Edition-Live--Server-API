<?php
/**
 * <DZCP-Extended Edition - Live! Server>
 * @package: DZCP-Extended Edition
 * @author: Hammermaps.de Developer Team
 * @link: http://www.hammermaps.de
 */

function updater_run($input)
{
    switch ($input['mode'])
    {
        case 'get_update_core':
            $sql = db("SELECT extract_to,file,update_type,edition FROM `dzcp_server_updates` WHERE `id` = ".$input['update_id']);
            if(sql_rows($sql))
            {
                $get = sql_fetch($sql);
                switch($get['update_type'])
                {
                    case 1: $update_dir = 'hotfix'; break;
                    case 2: $update_dir = 'bugfix'; break;
                    case 3: $update_dir = 'security'; break;
                    case 4: $update_dir = 'enhancement'; break;
                    case 5: $update_dir = 'api'; break;
                    default: $update_dir = 'updates'; break;
                }

                $get['edition'] = str_replace(' ', '_', $get['edition']);
                $dir = ROOT_PATH . 'download/core/'.$get['edition'].'/'.$update_dir;
                return array('status' => true,
                             'download_url' => 'http://127.0.0.1/DZCP-EE-API/download/core/'.$get['edition'].'/'.$update_dir.'/'.$get['file'],
                             'hash' => md5_file($dir.'/'.$get['file']),
                             'extract_to' => (empty($get['extract_to']) ? '/' : $get['extract_to']),
                             'temp_file' => md5(kernel::generatePW(8)));
            }

            return array('status' => false, 'msg' => 'not_found_update_id');
        break;

        case 'get_update_addons':
            $sql = db("SELECT extract_to,file,update_type,addon_id FROM `dzcp_server_addons_updates` WHERE `id` = ".$input['update_id']);
            if(sql_rows($sql))
            {
                $get = sql_fetch($sql);
                switch($get['update_type'])
                {
                    case 1: $update_dir = 'hotfix'; break;
                    case 2: $update_dir = 'bugfix'; break;
                    case 3: $update_dir = 'security'; break;
                    case 4: $update_dir = 'enhancement'; break;
                    case 5: $update_dir = 'api'; break;
                    default: $update_dir = 'updates'; break;
                }

                $sql = db("SELECT dir FROM `dzcp_server_addons` WHERE `id` = ".$get['addon_id']." LIMIT 1");
                if(!sql_rows($sql)) return array('status' => false, 'msg' => 'not_found_addon_id');
                $get_addon_dir = sql_fetch($sql);
                $get_addon_dir['dir'] = str_replace(' ', '_', $get_addon_dir['dir']);

                $dir = ROOT_PATH . 'download/addons/'.$get_addon_dir['dir'].'/'.$update_dir;
                return array('status' => true,
                             'download_url' => 'http://127.0.0.1/DZCP-EE-API/download/addons/'.$get_addon_dir['dir'].'/'.$update_dir.'/'.$get['file'],
                             'hash' => md5_file($dir.'/'.$get['file']),
                             'extract_to' => (empty($get['extract_to']) ? '/' : $get['extract_to']),
                             'temp_file' => md5(kernel::generatePW(8)));
            }

            return array('status' => false, 'msg' => 'not_found_update_id');
        break;

        case 'list_updates':
            $input['addons'] = kernel::string_to_array($input['addons'],true); //Uncompress
            $updates = array(); $update_available = false;

            ####### CMS Core Updates #######
            $sql = db("SELECT * FROM `dzcp_server_updates` WHERE `for_edition` = '".$input['edition']."' AND `for_version` = '".$input['version']."' AND `for_build` = '".$input['build']."' AND `for_dbv` = ".$input['dbv']);
            if(sql_rows($sql) >= 1)
            {
                $update_available = true;
                while($get = sql_fetch($sql))
                {
                    $updates[] = kernel::array_to_string(array('id' => $get['id'],
                                                               'type' => convert::ToString($get['update_type']),
                                                               'rev' => $get['rev'],
                                                               'version' => $get['version'],
                                                               'date' => date("d.m.Y",$get['time']),
                                                               'text' => $get['changelog'],
                                                               'tile' => $get['title'],
                                                               'core' => '1'),true);
                }

                unset($get);
            }

            unset($sql);

            ####### CMS Addons Updates #######
            if(count($input['addons']) >= 1)
            {
                foreach ($input['addons'] as $dir => $data)
                {
                    $sql = db("SELECT id FROM `dzcp_server_addons` WHERE `name` = '".$data['addon_name']."' AND `autor` = '".$data['addon_autor']."' AND `dir` = '".$dir."'");
                    if(sql_rows($sql) >= 1)
                    {
                        $id = sql_fetch($sql);
                        $sql = db("SELECT * FROM `dzcp_server_addons_updates` WHERE `addon_id` = ".$id['id']." AND `for_version` = '".$data['addon_version']."' AND `for_build` = '".$data['addon_build_rev']."'");
                        if(sql_rows($sql) >= 1)
                        {
                            $update_available = true;
                            while($get = sql_fetch($sql))
                            {
                                $updates[] = kernel::array_to_string(array('id' => $get['id'],
                                                                           'type' => convert::ToString($get['update_type']),
                                                                           'rev' => $get['rev'],
                                                                           'version' => $get['version'],
                                                                           'date' => date("d.m.Y",$get['time']),
                                                                           'text' => $get['changelog'],
                                                                           'tile' => $get['title'],
                                                                           'core' => '0'),true);
                            }
                        }
                    }
                }
            }

            return array('update_available' => convert::BoolToInt($update_available), 'updates' => $updates);
        break;
    }
}