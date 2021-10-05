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
                array_push($tree, [ 'name' => $file, 'length' => filesize($p), 'hash' => sha1_file($p) ]);
        }
    }
    $handle->close();
    return $tree;
}

header('Content-Type:application/yaml;charset=utf-8');

$purpose = isset($_GET["purpose"])? htmlspecialchars($_GET["purpose"]):'';
if($purpose=='')
{
    $data = Spyc::YAMLLoad(file_get_contents('index.yml'));
    echo Spyc::YAMLDump(array_merge([
        'update' => 'index.php?purpose=update&source=res'
    ], $data), false, false, true);
} else if($purpose=='update') {
    echo Spyc::YAMLDump(dir_hash('res'), false, false, true);
} else {
    echo Spyc::YAMLDump([ 'error' => 'something went wrong' ], false, false, true);
}

?>