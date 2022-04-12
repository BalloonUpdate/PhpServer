<?php
include('./Spyc.php');

function dir_hash($path)
{
    $tree = [];
    $handle = dir($path);
    if($handle) {
        while(($file = $handle->read()) !== false) {
            $p = $path . DIRECTORY_SEPARATOR . $file;
            if($file=='.' || $file=='..')
                continue;
            if(is_dir($p))
                array_push($tree, [ 'name' => $file, 'children' => dir_hash($p) ]);
            if(is_file($p))
                array_push($tree, [ 'name' => $file, 'length' => filesize($p), 'hash' => sha1_file($p), 'modified' => filemtime($p) ]);
        }
    }
    $handle->close();
    return $tree;
}

function jsonify($obj, $pretty = false)
{
    return json_encode($obj, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
}

header('Content-Type:text/plain;charset=utf-8');

$purpose = isset($_GET["purpose"])? htmlspecialchars($_GET["purpose"]):'';
if($purpose=='')
{
    $data = Spyc::YAMLLoad(file_get_contents('config.yml'));
    echo jsonify(array_merge([
        'update' => 'index.php?purpose=update&source=res'
    ] , $data));
} else if($purpose=='update') {
    echo jsonify(dir_hash('res'));
} else {
    echo jsonify([ 'error' => 'something went wrong' ]);
}

?>