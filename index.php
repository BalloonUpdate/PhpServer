<?php

function dir_hash($path)
{
    $tree = [];
    $handle = dir($path);

    if($handle) {
        while(($file = $handle->read()) !== false) {
            $p = $path . DIRECTORY_SEPARATOR . $file;

            if($file=='.' || $file=='..')
                continue;

            if(is_dir($p)) {
                array_push($tree, [
                    'name' => $file,
                    'tree' => dir_hash($p)
                ]);
            }

            if(is_file($p)) {
                array_push($tree, [
                    'name' => $file,
                    'length' => filesize($p),
                    'hash' => sha1_file($p)
                ]);
            }
        }
    }

    $handle->close();
    return $tree;
}

header('Content-Type:application/json;charset=utf-8');

$purpose = isset($_GET["purpose"])? htmlspecialchars($_GET["purpose"]):'';

if($purpose=='')
{
    $server_json = json_decode(file_get_contents('server.json'), true);

    $compatibility_v25 = [
        'upgrade_info' => 'index.php?purpose=upgrade',
        'upgrade_dir' => 'self',
        'update_info' => 'index.php?purpose=update',
        'update_dir' => 'res',
        'server' => [
            'mode_a' => true,
            'command_before_exit' => '',
            'match_all_regexes' => false,
            'regexes' => []
        ],
    ];

    $compatibility_v23 = [
        'client' => [
            "visible_time"=> 100,
            "width"=> 400,
            "height"=> 300
        ]
    ];

    echo json_encode(array_merge([
        'version' => '2.8p',
        'upgrade' => 'index.php?purpose=upgrade&source=self',
        'update' => 'index.php?purpose=update&source=res'
    ], $server_json, $compatibility_v25, $compatibility_v23));
} else if($purpose=='upgrade') {
    echo json_encode(dir_hash('self'));
} else if($purpose=='update') {
    echo json_encode(dir_hash('res'));
} else {
    echo '{"error":"Something went wrong"}';
}

?>