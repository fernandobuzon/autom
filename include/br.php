#!/usr/bin/php

<?php

try
{
    $dbFile = '/etc/motion/br.sqlite';
    $db = new SQLite3($dbFile);
    $result = $db->query("select setting,value from settings where setting in ('conf_path','gallery_path','br_bin','match','interval','log','csv')");
}
catch (Exception $e)
{
    $msg = "Erro ao executar a query inicial para obter os dados da tabela settings" . PHP_EOL;
    $msg .= $e->getMessage();
}
finally
{
    while ($row = $result->fetchArray(SQLITE3_NUM))
    {
        $$row[0] = $row[1];
    }
    $db->close();
}

if (empty($argv[1]) || !is_numeric($argv[1]))
{
    $msg = date("Y-m-d H:i:s") . ' - Erro! Necessario informar o numero da thread como primeiro argumento.' . PHP_EOL;
    echo $msg;
    file_put_contents($log, $msg, FILE_APPEND);
    die();
}

if (empty($argv[2]))
{
    $msg = date("Y-m-d H:i:s") . ' - Erro! Necessario informar o caminho da foto a ser comparada.' . PHP_EOL;
    echo $msg;
    file_put_contents($log, $msg, FILE_APPEND);
    die();
}

$other=`ps ax | grep br.php | sed '/grep/d' | wc -l`;
if ($other > 2)
{
    $msg = date("Y-m-d H:i:s") . ' - Outro processo em execucao.' . PHP_EOL;
    echo $msg;
    file_put_contents($log, $msg, FILE_APPEND);
    unlink($argv[2]);
    die();
}

$thread = $argv[1];
$imageFile = $argv[2];

$ids = file($conf_path . '/thread' . $thread . '.conf')[0];
$ids = explode('|', $ids);
$gallery_id = $ids[0];
$gallery_id = explode(':', $gallery_id);
$gallery_id = trim($gallery_id[1]);
$gallery = $gallery_path . '/' . $gallery_id . '.gal';

$camera_id = $ids[1];
$camera_id = explode(':', $camera_id);
$camera_id = trim($camera_id[1]);

function checkLog($dbFile, $interval, $gallery_id)
{
    $db = new SQLite3($dbFile);

    $stmt = $db->prepare("select count(1) from logs where door_id = ? and timestamp > datetime('now', '-$interval Seconds')");
    $stmt->bindValue(1, $gallery_id, SQLITE3_INTEGER);
    $result = $stmt->execute();
    $row = $result->fetchArray(SQLITE3_NUM);

    $db->close();

    if ($row[0] > 0)
    {
        return true;
    }
    else
    {
        return false;
    }
}

function writeLog($dbFile, $camera_id, $gallery_id, $face_id, $image, $match)
{
    $db = new SQLite3($dbFile);

    $stmt = $db->prepare("insert into logs ('timestamp','camera_id','door_id','face_id','img','match') values (CURRENT_TIMESTAMP, ?, ?, ?, ?, ?)");
    $stmt->bindValue(1, $camera_id, SQLITE3_INTEGER);
    $stmt->bindValue(2, $gallery_id, SQLITE3_INTEGER);
    $stmt->bindValue(3, $face_id, SQLITE3_INTEGER);
    $stmt->bindValue(4, $image, SQLITE3_BLOB);
    $stmt->bindValue(5, $match, SQLITE3_FLOAT);

    if ($stmt->execute())
    {
        $msg = date("Y-m-d H:i:s") . ' - Registrado acao camera_id=' . $camera_id . ' door_id=' . $gallery_id . ' face_id=' . $face_id . ' match=' . $match . '.' . PHP_EOL;
        $db->close();

        return $msg;
    }
    else
    {
        $msg = date("Y-m-d H:i:s") . ' - Erro ao tentar registrar a acao camera_id=' . $camera_id . ' door_id=' . $gallery_id . ' face_id=' . $face_id . ' match=' . $match . '.' . PHP_EOL;
        $db->close();

        return $msg;
    }
}

function findFaceId($dbFile, $gallery_id, $pos)
{
    $db = new SQLite3($dbFile);

    $stmt = $db->prepare("select face_id from doors_faces where door_id = ? order by id");
    $stmt->bindValue(1, $gallery_id, SQLITE3_INTEGER);
    $result = $stmt->execute();

    $i = 0;
    while($row = $result->fetchArray(SQLITE3_NUM))
    {
        if ($i == $pos)
        {
            break;
        }
        $i++; 
    }

    $db->close();
    return $row[0];
}

system("$br_bin -algorithm FaceRecognition -compare $gallery $imageFile $csv");

$result = file($csv)[1];
$result = explode(',', $result);
array_shift($result);

$i = 0;
foreach($result as $res)
{
    $val = floatval($res);
    if ($val >= $match)
    {
        if(checkLog($dbFile, $interval, $gallery_id) == false)
        {
            $image = file_get_contents($imageFile);
            $face_id = findFaceId($dbFile, $gallery_id, $i);
            $msg = writeLog($dbFile, $camera_id, $gallery_id, $face_id, $image, $val);
            file_put_contents($log, $msg, FILE_APPEND);
        }
        else
        {
            $msg = 'evento dentro do interval!' . PHP_EOL;
            file_put_contents($log, $msg, FILE_APPEND);
        }
    }
    $i++;
}

unlink($imageFile);
?>
