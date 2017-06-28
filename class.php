<?php

require_once('config.php');

abstract class database
{
    private $dbFile;

    public function setDbFile($dbFile)
    {
        if (file_exists($dbFile) && is_writable($dbFile))
        {
            $this->dbFile = $dbFile;
        }
        else
        {
            $msg = 'Arquivo de banco de dados inv&aacute;lido ou sem permiss&atilde;o de escrita.';
            notify::showMsg($msg,'danger','home.php');
            die();
        }
    }

    public function getDbFile()
    {
        return $this->dbFile;
    }
}

class environ extends database
{
    public function environ($dbFile)
    {
        parent::setDbFile($dbFile);
    }

    public function setConf()
    {
        $db = new SQLite3(parent::getDbFile());

        $stmt = $db->prepare("select value from settings where setting = 'conf_value'");
        $result = $stmt->execute();
        $row = $result->fetchArray(SQLITE3_NUM);
        $conf_value = str_ireplace("\x0D", "", $row[0]);

        $stmt = $db->prepare("select value from settings where setting = 'conf_path'");
        $result = $stmt->execute();
        $row = $result->fetchArray(SQLITE3_NUM);

        if (!empty($row[0]))
        {
            $conf_path = $row[0];
        }
        else
        {
            $msg = 'Registro "conf_path" nulo ou n&atilde;o encontrado na tabela "settings".';
            throw new Exception("$msg");
        }

        if(is_dir($conf_path) && is_writable($conf_path))
        {
            if (file_exists($conf_path . '/motion.conf') && !is_writable($conf_path . '/motion.conf'))
            {
                $msg = 'Arquivo ' . $conf_path . ' sem permiss&atilde;o de escrita.';
                throw new Exception("$msg");
            }
            else
            {
                $file = $conf_path . '/motion.conf';
                file_put_contents($file, $conf_value);
                chmod($file, 0664);
            }
        }
        else
        {
            $msg = 'Pasta ' . $conf_path . ' sem permiss&atilde;o de escrita.';
            throw new Exception("$msg");
        }
    }

    public function setThreads()
    {
        $db = new SQLite3(parent::getDbFile());

        $stmt = $db->prepare("select value from settings where setting = 'conf_path'");
        $result = $stmt->execute();
        $row = $result->fetchArray(SQLITE3_NUM);

        if (!empty($row[0]))
        {
            $conf_path = $row[0];
        }
        else
        {
            $msg = 'Registro "conf_path" nulo ou n&atilde;o encontrado na tabela "settings".';
            throw new Exception("$msg");
        }

        if(file_exists($conf_path) && is_writable($conf_path))
        {
            $stmt = $db->prepare("select * from cameras");
            $result = $stmt->execute();

            $i = 1;
            while($row = $result->fetchArray(SQLITE3_ASSOC))
            {
                file_put_contents($conf_path . '/motion.conf', PHP_EOL, FILE_APPEND);
                file_put_contents($conf_path . '/motion.conf', "thread $conf_path/thread$i.conf" . PHP_EOL, FILE_APPEND);

                $content = null;
                $content .= '# door_id: ' . $row['door_id'] . ' | camera_id: ' . $row['id'] . PHP_EOL;
                $content .= 'v4l2_palette ' . $row['v4l2_palette'] . PHP_EOL;
                $content .= 'norm ' . $row['norm'] . PHP_EOL;
                $content .= 'width ' . $row['width'] . PHP_EOL;
                $content .= 'height ' . $row['height'] . PHP_EOL;
                $content .= 'framerate ' . $row['framerate'] . PHP_EOL;
                $content .= 'minimum_frame_time ' . $row['minimum_frame_time'] . PHP_EOL;
                $content .= 'netcam_url ' . $row['netcam_url'] . PHP_EOL;
                $content .= 'netcam_userpass ' . $row['netcam_userpass'] . PHP_EOL;
                $content .= 'netcam_keepalive ' . $row['netcam_keepalive'] . PHP_EOL;
                $content .= 'auto_brightness ' . $row['auto_brightness'] . PHP_EOL;
                $content .= 'brightness ' . $row['brightness'] . PHP_EOL;
                $content .= 'contrast ' . $row['contrast'] . PHP_EOL;
                $content .= 'saturation ' . $row['saturation'] . PHP_EOL;
                $content .= 'hue ' . $row['hue'] . PHP_EOL;

                $file = "$conf_path/thread$i.conf";
                file_put_contents($file, $content);
                chmod($file, 0664);

                $i++;   
            }
        }
        else
        {
            $msg = 'Arquivo ' . $conf_path . '/motion.conf inexistente ou sem permiss&atilde;o de escrita.';
            throw new Exception("$msg");
        }
    }

    public function setGallery()
    {
        $db = new SQLite3(parent::getDbFile());

        $stmt = $db->prepare("select value from settings where setting = 'gallery_path'");
        $result = $stmt->execute();
        $row = $result->fetchArray(SQLITE3_NUM);

        if (!empty($row[0]))
        {
            $gallery_path = $row[0];
        }
        else
        {
            $msg = 'Registro "gallery_path" nulo ou n&atilde;o encontrado na tabela "settings".';
            throw new Exception("$msg");
        }

        if(file_exists($gallery_path) && is_writable($gallery_path))
        {
            $result = $db->query('SELECT id from doors');

            while ($row2 = $result->fetchArray(SQLITE3_NUM))
            {
                $door_id = $row2[0];

                $f = array();
                $result2 = $db->query("select face_id from doors_faces where door_id = $door_id order by face_id");

                $c = 0;
                while($face_id = $result2->fetchArray(SQLITE3_NUM))
                {
                    $imgFile = $gallery_path . '/face' . $face_id[0] . '.jpg';
                    $f[] = $imgFile;

                    $stmt = $db->prepare("select img from faces where id = ?");
                    $stmt->bindValue(1, $face_id[0], SQLITE3_INTEGER);
                    $result3 = $stmt->execute();
                    $row3 = $result3->fetchArray(SQLITE3_NUM);

                    if (file_exists($imgFile))
                    {
                        if(!unlink($imgFile))
                        {
                            $msg = 'Erro ao remover a face antiga (' . $imgFile . ').';
                            throw new Exception("$msg");            
                        }
                    }

                    if (!file_put_contents($imgFile ,$row3[0]))
                    {
                        $msg = 'Erro ao gerar a imagem da face (' . $imgFile . ').';
                        throw new Exception("$msg");            
                    }

                    $c++;
                }

                if ($c > 0)
                {
                    $f = implode(' ', $f);
                    $galFile = $gallery_path . '/' . $door_id . '.gal';

                    if (file_exists($galFile))
                    {
                        if(!unlink($galFile))
                        {
                            $msg = 'Erro ao remover a galeria antiga (' . $galFile . ').';
                            throw new Exception("$msg");            
                        }
                    }

                    $br_bin = $this->getBr_bin();
                    system("$br_bin -algorithm FaceRecognition -enrollAll -enroll $f $galFile > $galFile-enroll.log 2>&1");
                    chmod($galFile, 0664);
                    chmod($galFile . '-enroll.log', 0664);

                    $log = file($galFile . '-enroll.log');
                    foreach($log as $line)
                    {
                        $r = explode(' ', $line);
                        $r = $r[0];
                        if ($r == 'Set' || $r == 'Loading' || $r == 'Enrolling')
                        {
                            continue;
                        }
                        else
                        {
                            if (trim($r) != '100.00%')
                            {
                                $msg = 'Erro ao gerando a nova galeria, consulte ' . $r . $galFile . '-enroll.log';
                                throw new Exception("$msg");            
                            }
                        }
                    }
                }
            }
        }
        else
        {
            $msg = 'Pasta ' . $gallery_path . ' inexistente ou sem permiss&atilde;o de escrita.';
            throw new Exception("$msg");
        }
    }

    public function assessment($image)
    {
        $assessment_tmp = $this->getAssessment_tmp();
        if (empty($assessment_tmp))
        {
            $msg = 'Arquivo tempor&aacute;rio para assessment "settings.assessment_tmp" n&atilde;o definido na tabela settings.';
            throw new Exception("$msg");
        }

        $br_bin = $this->getBr_bin();
        if (empty($br_bin))
        {
            $msg = 'Arquivo bin&aacute;rio do Briometrics "settings.br_bin" n&atilde;o definido na tabela settings.';
            throw new Exception("$msg");
        }

        //return $image;

        $image = explode(',', $image);
        $image = base64_decode($image[1]);
        
        if (!file_put_contents($assessment_tmp ,$image))
        {
            $msg = 'Erro ao gerar a imagem tempor&aacute; para o assessment.';
            throw new Exception("$msg");            
        }
       
        $result = `$br_bin -algorithm FaceRecognition -compare $assessment_tmp $assessment_tmp`;

        return $result;
    }

    public function getBr_bin()
    {
        $db = new SQLite3(parent::getDbFile());

        $stmt = $db->prepare("SELECT value from settings where setting = 'br_bin'");
        $result = $stmt->execute();
        $row = $result->fetchArray(SQLITE3_NUM);

        $db->close();

        if (empty($row[0]))
        {
            $msg = 'Par&acirc;meto "br_bin" n&atilde;o encontrado na tabela settings.';
            throw new Exception("$msg");
        }
        else
        {
            return $row[0];
        }
    }

    public function getAssessment_tmp()
    {
        $db = new SQLite3(parent::getDbFile());

        $stmt = $db->prepare("SELECT value from settings where setting = 'assessment_tmp'");
        $result = $stmt->execute();
        $row = $result->fetchArray(SQLITE3_NUM);

        $db->close();

        if (empty($row[0]))
        {
            $msg = 'Par&acirc;meto "br_bin" n&atilde;o encontrado na tabela settings.';
            throw new Exception("$msg");
        }
        else
        {
            return $row[0];
        }
    }

    public function checkStatus()
    {
        $db = new SQLite3(parent::getDbFile());

        $stmt = $db->prepare("SELECT value from settings where setting = 'check_cmd'");
        $result = $stmt->execute();
        $row = $result->fetchArray(SQLITE3_NUM);

        $db->close();

        if (empty($row[0]))
        {
            $msg = 'Par&acirc;meto "restart_cmd" n&atilde;o encontrado na tabela settings.';
            throw new Exception("$msg");
        }
        else
        {
            $cmd = $row[0];
            $pid = `$cmd`;

            if (empty($pid))
            {
                return false;
            }
            else
            {
                return true;
            }
        }
    }

    public function restart()
    {
        $db = new SQLite3(parent::getDbFile());

        $stmt = $db->prepare("SELECT value from settings where setting = 'restart_cmd'");
        $result = $stmt->execute();
        $row = $result->fetchArray(SQLITE3_NUM);

        $db->close();

        if (empty($row[0]))
        {
            $msg = 'Par&acirc;meto "restart_cmd" n&atilde;o encontrado na tabela settings.';
            throw new Exception("$msg");
        }
        else
        {
            $cmd = $row[0];
            system("$cmd > /dev/null");
        }
    }

    public function start()
    {
        $db = new SQLite3(parent::getDbFile());

        $stmt = $db->prepare("SELECT value from settings where setting = 'start_cmd'");
        $result = $stmt->execute();
        $row = $result->fetchArray(SQLITE3_NUM);

        $db->close();

        if (empty($row[0]))
        {
            $msg = 'Par&acirc;meto "start_cmd" n&atilde;o encontrado na tabela settings.';
            throw new Exception("$msg");
        }
        else
        {
            $cmd = $row[0];
            system("$cmd > /dev/null");
        }
    }

    public function stop()
    {
        $db = new SQLite3(parent::getDbFile());

        $stmt = $db->prepare("SELECT value from settings where setting = 'stop_cmd'");
        $result = $stmt->execute();
        $row = $result->fetchArray(SQLITE3_NUM);

        $db->close();

        if (empty($row[0]))
        {
            $msg = 'Par&acirc;meto "stop_cmd" n&atilde;o encontrado na tabela settings.';
            throw new Exception("$msg");
        }
        else
        {
            $cmd = $row[0];
            system("$cmd > /dev/null");
        }
    }
}

class notify
{
    public static function showMsg($msg,$alert,$page)
    {
        if ($alert == 'success')
        {
            $msg = 'OK! ' . $msg . PHP_EOL;
        }
        elseif ($alert == 'danger')
        {
            $msg = 'Erro! ' . $msg . PHP_EOL;
        }
            
        echo '<div class="row">';
        echo '  <div class="alert alert-' . $alert . '" role="alert">';
        echo $msg;
        echo '  </div>';
        echo '</div>';

        echo '<div class="row">';
        echo '  <button type="button" class="btn btn-default" onclick="populate(\'' . $page . '\');">Voltar</button>';
        echo '</div>';
    }
}

class logs extends database
{
    private $id;
    private $timestamp;
    private $camera_id;
    private $door_id;
    private $face_id;
    private $img;

    public function logs($dbFile)
    {
        parent::setDbFile($dbFile);
    }

    public function del()
    {
        if (empty($this->id))
        {
            $msg = 'Antes de remover, sete o "id" com o m&eacute;todo setId.';
            throw new Exception("$msg");
        }

        $db = new SQLite3(parent::getDbFile());

        $stmt = $db->prepare("delete from logs where id = ?");
        $stmt->bindValue(1, $this->id, SQLITE3_INTEGER);
        if (! $stmt->execute())
        {
            $msg = $db->lastErrorMsg();
            $db->close();

            throw new Exception("$msg");
        }
        $db->close();
    }

    public function load()
    {
        if (empty($this->id))
        {
            $msg = 'Antes de carregar, sete o "id" com o m&eacute;todo setId.';
            throw new Exception("$msg");
        }

        $db = new SQLite3(parent::getDbFile());

        $stmt = $db->prepare("select * from logs where id = ?");
        $stmt->bindValue(1, $this->id, SQLITE3_INTEGER);
        $result = $stmt->execute();
        $row = $result->fetchArray(SQLITE3_ASSOC);

        foreach ($row as $key => $val)
        {
            $this->$key = $val;
        }

        $db->close();
    }

    public function getAll()
    {
        $db = new SQLite3(parent::getDbFile());
        $result = $db->query('SELECT l.id as id,
                                        l.timestamp as date,
                                        l.match as mach,
                                        d.name as door,
                                        c.name as camera,
                                        f.name as name from logs l
                                            inner join faces f on f.id = l.face_id
                                            inner join doors d on d.id = l.door_id
                                            inner join cameras c on c.id = l.camera_id order by l.id desc limit 12');

        while ($row = $result->fetchArray(SQLITE3_ASSOC))
        {
            $logs[] = $row;
        }

        $db->close();
        return $logs;
    }

    public function setId($id)
    {
        if (is_numeric($id))
        {
            $db = new SQLite3(parent::getDbFile());

            $stmt = $db->prepare('SELECT count(1) from logs where id = ?');
            $stmt->bindValue(1, $id, SQLITE3_INTEGER);
            $result = $stmt->execute();
            $row = $result->fetchArray(SQLITE3_NUM);
            $db->close();

            if ($row[0] == 1)
            {
                $this->id = $id;
            }
            else
            {
                $msg = 'O "id" informado n&atilde;o foi localizado.';
                throw new Exception("$msg");
            }
        }
        else
        {
            $msg = 'O "id" deve ser um valor num&eacute;rico.';
            throw new Exception("$msg");
        }
    }

    public function setTimestamp($timestamp)
    {
        if (is_a($timestamp, 'DateTime'))
        {
            //$this->timestamp = $timestamp->format('Y-m-d H:i:s');
            $this->timestamp = $timestamp;
        }
        else
        {
            $msg = 'Timestamp deve ser um objeto do tipo DateTime.';
            throw new Exception("$msg");
        }
    }

    public function setCamera_id($camera_id)
    {
        if (is_numeric($camera_id))
        {
            $db = new SQLite3(parent::getDbFile());

            $stmt = $db->prepare('SELECT count(1) from cameras where id = ?');
            $stmt->bindValue(1, $camera_id, SQLITE3_INTEGER);
            $result = $stmt->execute();
            $row = $result->fetchArray(SQLITE3_NUM);
            $db->close();

            if ($row[0] == 1)
            {
                $this->camera_id = $camera_id;
            }
            else
            {
                $msg = 'O "id" informado n&atilde;o foi localizado.';
                throw new Exception("$msg");
            }
        }
        else
        {
            $msg = 'O "id" deve ser um valor num&eacute;rico.';
            throw new Exception("$msg");
        }
    }

    public function setDoor_id($door_id)
    {
        if (is_numeric($door_id))
        {
            $db = new SQLite3(parent::getDbFile());

            $stmt = $db->prepare('SELECT count(1) from doors where id = ?');
            $stmt->bindValue(1, $door_id, SQLITE3_INTEGER);
            $result = $stmt->execute();
            $row = $result->fetchArray(SQLITE3_NUM);
            $db->close();

            if ($row[0] == 1)
            {
                $this->door_id = $door_id;
            }
            else
            {
                $msg = 'O "id" informado n&atilde;o foi localizado.';
                throw new Exception("$msg");
            }
        }
        else
        {
            $msg = 'O "id" deve ser um valor num&eacute;rico.';
            throw new Exception("$msg");
        }
    }

    public function setFace_id($face_id)
    {
        if (is_numeric($face_id))
        {
            $db = new SQLite3(parent::getDbFile());

            $stmt = $db->prepare('SELECT count(1) from faces where id = ?');
            $stmt->bindValue(1, $face_id, SQLITE3_INTEGER);
            $result = $stmt->execute();
            $row = $result->fetchArray(SQLITE3_NUM);
            $db->close();

            if ($row[0] == 1)
            {
                $this->face_id = $face_id;
            }
            else
            {
                $msg = 'O "id" informado n&atilde;o foi localizado.';
                throw new Exception("$msg");
            }
        }
        else
        {
            $msg = 'O "id" deve ser um valor num&eacute;rico.';
            throw new Exception("$msg");
        }
    }

    public function setImg($img)
    {
        if (!empty($img))
        {
            $this->img = $img;
        }
        else
        {
            $msg = 'A imagem n&atilde;o pode ser nula.';
            throw new Exception("$msg");
        }
    }

    public function getImg()
    {
        //header('Content-Type: image/jpeg');
        echo $this->img;
    }
}

class cameras extends database
{
    private $id;
    private $door_id;
    private $name;
    private $netcam_url;
    private $netcam_userpass;
    private $v4l2_palette;
    private $norm;
    private $width;
    private $height;
    private $framerate;
    private $minimum_frame_time;
    private $netcam_keepalive;
    private $auto_brightness;
    private $brightness;
    private $contrast;
    private $saturation;
    private $hue;

    public function cameras($dbFile)
    {
        parent::setDbFile($dbFile);
    }

    public function del()
    {
        if (empty($this->id))
        {
            $msg = 'Antes de remover, sete o "id" com o m&eacute;todo setId.';
            throw new Exception("$msg");
        }

        $db = new SQLite3(parent::getDbFile());
        $db->exec('PRAGMA foreign_keys = ON;');

        $stmt = $db->prepare("delete from cameras where id = ?");
        $stmt->bindValue(1, $this->id, SQLITE3_INTEGER);
        if (! $stmt->execute())
        {
            $msg = $db->lastErrorMsg();
            $db->close();

            throw new Exception("$msg");
        }

        $db->close();
    }

    public function load()
    {
        if (empty($this->id))
        {
            $msg = 'Antes de carregar, sete o "id" com o m&eacute;todo setId.';
            throw new Exception("$msg");
        }

        $db = new SQLite3(parent::getDbFile());

        $stmt = $db->prepare("select * from cameras where id = ?");
        $stmt->bindValue(1, $this->id, SQLITE3_INTEGER);
        $result = $stmt->execute();
        $row = $result->fetchArray(SQLITE3_ASSOC);

        foreach ($row as $key => $val)
        {
            $this->$key = $val;
        }

        $db->close();
    }

    public function add()
    {
        if (empty($this->name) || empty($this->netcam_url))
        {
            $msg = 'Antes de adicionar, adicione um nome e o netcam_url.';
            throw new Exception("$msg");
        }

        $db = new SQLite3(parent::getDbFile());

        $stmt = $db->prepare("insert into cameras ('name','netcam_url','netcam_userpass','v4l2_palette','norm','width','height','framerate','minimum_frame_time','netcam_keepalive','auto_brightness','brightness','contrast','saturation','hue') values (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)");
        $stmt->bindValue(1, $this->name, SQLITE3_TEXT);
        $stmt->bindValue(2, $this->netcam_url, SQLITE3_TEXT);
        $stmt->bindValue(3, $this->netcam_userpass, SQLITE3_TEXT);
        $stmt->bindValue(4, $this->v4l2_palette, SQLITE3_INTEGER);
        $stmt->bindValue(5, $this->norm, SQLITE3_INTEGER);
        $stmt->bindValue(6, $this->width, SQLITE3_INTEGER);
        $stmt->bindValue(7, $this->height, SQLITE3_INTEGER);
        $stmt->bindValue(8, $this->framerate, SQLITE3_INTEGER);
        $stmt->bindValue(9, $this->minimum_frame_time, SQLITE3_INTEGER);
        $stmt->bindValue(10, $this->netcam_keepalive, SQLITE3_INTEGER);
        $stmt->bindValue(11, $this->auto_brightness, SQLITE3_INTEGER);
        $stmt->bindValue(12, $this->brightness, SQLITE3_INTEGER);
        $stmt->bindValue(13, $this->contrast, SQLITE3_INTEGER);
        $stmt->bindValue(14, $this->saturation, SQLITE3_INTEGER);
        $stmt->bindValue(15, $this->hue, SQLITE3_INTEGER);

        if (! $stmt->execute())
        {
            $msg = $db->lastErrorMsg();
            $db->close();

            throw new Exception("$msg");
        }

        $db->close();
    }

    public function save()
    {
        if (empty($this->id))
        {
            $msg = 'Antes de salvar, carregue alguma c&acirc;mera com o m&eacute;todo load.';
            throw new Exception("$msg");
        }

        $db = new SQLite3(parent::getDbFile());

        $stmt = $db->prepare("update cameras set 'name' = ?, 'netcam_url' = ?, 'netcam_userpass' = ?, 'v4l2_palette' = ?, 'norm' = ?, 'width' = ?, 'height' = ?, 'framerate' = ?, 'minimum_frame_time' = ?, 'netcam_keepalive' = ?, 'auto_brightness' = ?, 'brightness' = ?, 'contrast' = ?, 'saturation' = ?, 'hue' = ? where id = ?");
        $stmt->bindValue(1, $this->name, SQLITE3_TEXT);
        $stmt->bindValue(2, $this->netcam_url, SQLITE3_TEXT);
        $stmt->bindValue(3, $this->netcam_userpass, SQLITE3_TEXT);
        $stmt->bindValue(4, $this->v4l2_palette, SQLITE3_INTEGER);
        $stmt->bindValue(5, $this->norm, SQLITE3_INTEGER);
        $stmt->bindValue(6, $this->width, SQLITE3_INTEGER);
        $stmt->bindValue(7, $this->height, SQLITE3_INTEGER);
        $stmt->bindValue(8, $this->framerate, SQLITE3_INTEGER);
        $stmt->bindValue(9, $this->minimum_frame_time, SQLITE3_INTEGER);
        $stmt->bindValue(10, $this->netcam_keepalive, SQLITE3_INTEGER);
        $stmt->bindValue(11, $this->auto_brightness, SQLITE3_INTEGER);
        $stmt->bindValue(12, $this->brightness, SQLITE3_INTEGER);
        $stmt->bindValue(13, $this->contrast, SQLITE3_INTEGER);
        $stmt->bindValue(14, $this->saturation, SQLITE3_INTEGER);
        $stmt->bindValue(15, $this->hue, SQLITE3_INTEGER);
        $stmt->bindValue(16, $this->id, SQLITE3_INTEGER);

        if (! $stmt->execute())
        {
            $msg = $db->lastErrorMsg();
            $db->close();

            throw new Exception("$msg");
        }

        $db->close();
    }

    public function getAll()
    {
        $db = new SQLite3(parent::getDbFile());
        $result = $db->query('SELECT * from cameras');

        while ($row = $result->fetchArray(SQLITE3_ASSOC))
        {
            $cameras[] = $row;
        }

        $db->close();
        return $cameras;
    }

    public function setId($id)
    {
        if (is_numeric($id))
        {
            $db = new SQLite3(parent::getDbFile());

            $stmt = $db->prepare('SELECT count(1) from cameras where id = ?');
            $stmt->bindValue(1, $id, SQLITE3_INTEGER);
            $result = $stmt->execute();
            $row = $result->fetchArray(SQLITE3_NUM);
            $db->close();

            if ($row[0] == 1)
            {
                $this->id = $id;
            }
            else
            {
                $msg = 'O "id" informado n&atilde;o foi localizado.';
                throw new Exception("$msg");
            }
        }
        else
        {
            $msg = 'O "id" deve ser um valor num&eacute;rico.';
            throw new Exception("$msg");
        }
    }

    public function setDoor_id($door_id)
    {
        if (is_numeric($door_id))
        {
            $db = new SQLite3(parent::getDbFile());

            $stmt = $db->prepare('SELECT count(1) from doors where id = ?');
            $stmt->bindValue(1, $door_id, SQLITE3_INTEGER);
            $result = $stmt->execute();
            $row = $result->fetchArray(SQLITE3_NUM);
            $db->close();

            if ($row[0] == 1)
            {
                $this->door_id = $door_id;
            }
            else
            {
                $msg = 'O "id" informado n&atilde;o foi localizado.';
                throw new Exception("$msg");
            }
        }
        else
        {
            $msg = 'O "id" deve ser um valor num&eacute;rico.';
            throw new Exception("$msg");
        }
    }

    public function setName($name)
    {
        if (!empty($name))
        {
            $this->name = $name;
        }
        else
        {
            $msg = 'O valor "name" n&tilde;o pode ser vazio.';
            throw new Exception("$msg");
        }
    }

    public function setNetcam_url($netcam_url)
    {
        if (!empty($netcam_url))
        {
            $this->netcam_url = $netcam_url;
        }
        else
        {
            $msg = 'O valor "netcam_url" n&tilde;o pode ser vazio.';
            throw new Exception("$msg");
        }
    }

    public function setNetcam_userpass($netcam_userpass)
    {
        if (!empty($netcam_userpass))
        {
            $check = explode(":", $netcam_userpass);
            if (empty($check[0]) || empty($check[1]))
            {
                $msg = 'O formato para o campo "netcam_userpass" deve ser "usuario:senha".';
                throw new Exception("$msg");
            }
            else
            {
                $this->netcam_userpass = $netcam_userpass;
            }
        }
    }

    public function setV4l2_palette($v4l2_palette)
    {
        if (is_numeric($v4l2_palette))
        {
            $this->v4l2_palette = $v4l2_palette;
        }
        else
        {
            $msg = 'O valor "v4l2_palette" n&tilde;o pode ser vazio e deve ser num&eacute;rico.';
            throw new Exception("$msg");
        }
    }

    public function setNorm($norm)
    {
        if (is_numeric($norm))
        {
            $this->norm = $norm;
        }
        else
        {
            $msg = 'O valor "norm" n&tilde;o pode ser vazio e deve ser num&eacute;rico.';
            throw new Exception("$msg");
        }
    }

    public function setWidth($width)
    {
        if (is_numeric($width))
        {
            $this->width = $width;
        }
        else
        {
            $msg = 'O valor "width" n&tilde;o pode ser vazio e deve ser num&eacute;rico.';
            throw new Exception("$msg");
        }
    }

    public function setHeight($height)
    {
        if (is_numeric($height))
        {
            $this->height = $height;
        }
        else
        {
            $msg = 'O valor "height" n&tilde;o pode ser vazio e deve ser num&eacute;rico.';
            throw new Exception("$msg");
        }
    }

    public function setFramerate($framerate)
    {
        if (is_numeric($framerate))
        {
            $this->framerate = $framerate;
        }
        else
        {
            $msg = 'O valor "framerate" n&tilde;o pode ser vazio e deve ser num&eacute;rico.';
            throw new Exception("$msg");
        }
    }

    public function setMinimum_frame_time($minimum_frame_time)
    {
        if (is_numeric($minimum_frame_time))
        {
            $this->minimum_frame_time = $minimum_frame_time;
        }
        else
        {
            $msg = 'O valor "minimum_frame_time" n&tilde;o pode ser vazio e deve ser num&eacute;rico.';
            throw new Exception("$msg");
        }
    }

    public function setNetcam_keepalive($netcam_keepalive)
    {
        if (is_numeric($netcam_keepalive))
        {
            $this->netcam_keepalive = $netcam_keepalive;
        }
        else
        {
            $msg = 'O valor "netcam_keepalive" n&tilde;o pode ser vazio e deve ser num&eacute;rico.';
            throw new Exception("$msg");
        }
    }

    public function setAuto_brightness($auto_brightness)
    {
        if ($auto_brightness == 0 || $auto_brightness == 1)
        {
            $this->auto_brightness = $auto_brightness;
        }
        else
        {
            $msg = 'O valor "auto_brightness" n&tilde;o pode ser vazio e deve ser 0 ou 1.';
            throw new Exception("$msg");
        }
    }

    public function setBrightness($brightness)
    {
        if (is_numeric($brightness))
        {
            $this->brightness = $brightness;
        }
        else
        {
            $msg = 'O valor "brightness" n&tilde;o pode ser vazio e deve ser num&eacute;rico.';
            throw new Exception("$msg");
        }
    }

    public function setContrast($contrast)
    {
        if (is_numeric($contrast))
        {
            $this->contrast = $contrast;
        }
        else
        {
            $msg = 'O valor "contrast" n&tilde;o pode ser vazio e deve ser num&eacute;rico.';
            throw new Exception("$msg");
        }
    }

    public function setSaturation($saturation)
    {
        if (is_numeric($saturation))
        {
            $this->saturation = $saturation;
        }
        else
        {
            $msg = 'O valor "saturation" n&tilde;o pode ser vazio e deve ser num&eacute;rico.';
            throw new Exception("$msg");
        }
    }

    public function setHue($hue)
    {
        if (is_numeric($hue))
        {
            $this->hue = $hue;
        }
        else
        {
            $msg = 'O valor "hue" n&tilde;o pode ser vazio e deve ser num&eacute;rico.';
            throw new Exception("$msg");
        }
    }

    public function getName()
    {
        return $this->name;
    }

    public function getNetcam_url()
    {
        return $this->netcam_url;
    }

    public function getNetcam_userpass()
    {
        return $this->netcam_userpass;
    }

    public function getV4l2_palette()
    {
        return $this->v4l2_palette;
    }

    public function getNorm()
    {
        return $this->norm;
    }

    public function getWidth()
    {
        return $this->width;
    }

    public function getHeight()
    {
        return $this->height;
    }

    public function getFramerate()
    {
        return $this->framerate;
    }

    public function getMinimum_frame_time()
    {
        return $this->minimum_frame_time;
    }

    public function getNetcam_keepalive()
    {
        return $this->netcam_keepalive;
    }

    public function getAuto_brightness()
    {
        return $this->auto_brightness;
    }

    public function getBrightness()
    {
        return $this->brightness;
    }

    public function getContrast()
    {
        return $this->contrast;
    }

    public function getSaturation()
    {
        return $this->saturation;
    }

    public function getHue()
    {
        return $this->hue;
    }
}

class doors extends database
{
    private $id;
    private $name;

    public function doors($dbFile)
    {
        parent::setDbFile($dbFile);
    }

    public function setCameras($cameras)
    {
        if (empty($this->id))
        {
            $msg = 'Antes de setar as c&acirc;meras, sete o "id" com o m&eacute;todo setId.';
            throw new Exception("$msg");
        }

        $db = new SQLite3(parent::getDbFile());

        $stmt = $db->prepare("update cameras set door_id = null where door_id = ?");
        $stmt->bindValue(1, $this->id, SQLITE3_INTEGER);
        if (! $stmt->execute())
        {
            $msg = $db->lastErrorMsg();
            $db->close();

            throw new Exception("$msg");
        }

        $cameras = explode(':', $cameras);
        foreach ($cameras as $camera)
        {
            $camera_id = str_replace('camera','',$camera);

            $stmt = $db->prepare("update cameras set door_id = ? where id = ?");
            $stmt->bindValue(1, $this->id, SQLITE3_INTEGER);
            $stmt->bindValue(2, $camera_id, SQLITE3_INTEGER);
            if (! $stmt->execute())
            {
                $msg = $db->lastErrorMsg();
                $db->close();

                throw new Exception("$msg");
            }
        }

        $db->close();
    }

    public function del()
    {
        if (empty($this->id))
        {
            $msg = 'Antes de remover, sete o "id" com o m&eacute;todo setId.';
            throw new Exception("$msg");
        }

        $db = new SQLite3(parent::getDbFile());
        $db->exec('PRAGMA foreign_keys = ON;');

        $stmt = $db->prepare("update cameras set door_id = null where door_id = ?");
        $stmt->bindValue(1, $this->id, SQLITE3_INTEGER);

        if (! $stmt->execute())
        {
            $msg = $db->lastErrorMsg();
            $db->close();

            throw new Exception("$msg");
        }

        $stmt = $db->prepare("delete from doors where id = ?");
        $stmt->bindValue(1, $this->id, SQLITE3_INTEGER);

        if (! $stmt->execute())
        {
            $msg = $db->lastErrorMsg();
            $db->close();

            throw new Exception("$msg");
        }

        $db->close();
    }

    public function load()
    {
        if (empty($this->id))
        {
            $msg = 'Antes de carregar, sete o "id" com o m&eacute;todo setId.';
            throw new Exception("$msg");
        }

        $db = new SQLite3(parent::getDbFile());

        $stmt = $db->prepare("select name from doors where id = ?");
        $stmt->bindValue(1, $this->id, SQLITE3_INTEGER);
        $result = $stmt->execute();
        $row = $result->fetchArray(SQLITE3_ASSOC);

        $this->name = $row['name'];

        $db->close();
    }

    public function save()
    {
        if (empty($this->id))
        {
            $msg = 'Antes de salvar, carregue alguma porta com o m&eacute;todo load.';
            throw new Exception("$msg");
        }

        $db = new SQLite3(parent::getDbFile());

        $stmt = $db->prepare("update doors set name = ? where id = ?");
        $stmt->bindValue(1, $this->name, SQLITE3_TEXT);
        $stmt->bindValue(2, $this->id, SQLITE3_INTEGER);

        if (! $stmt->execute())
        {
            $msg = $db->lastErrorMsg();
            $db->close();

            throw new Exception("$msg");
        }

        $db->close();
    }

    public function add()
    {
        if (empty($this->name))
        {
            $msg = 'Antes de adicionar, adicione um nome com o m&eacute;todo setName.';
            throw new Exception("$msg");
        }

        $db = new SQLite3(parent::getDbFile());

        $stmt = $db->prepare("insert into doors ('name') values (?)");
        $stmt->bindValue(1, $this->name, SQLITE3_TEXT);

        if (! $stmt->execute())
        {
            $msg = $db->lastErrorMsg();
            $db->close();

            throw new Exception("$msg");
        }

        $db->close();
    }

    public function getCameras()
    {
        if (empty($this->id))
        {
            $msg = 'Antes de checar as c&acirc;meras, sete o "id" com o m&eacute;todo setId.';
            throw new Exception("$msg");
        }

        $db = new SQLite3(parent::getDbFile());
        $stmt = $db->prepare('select id from cameras where door_id = ?');
        $stmt->bindValue(1, $this->id, SQLITE3_INTEGER);
        $result = $stmt->execute();

        while ($row = $result->fetchArray(SQLITE3_ASSOC))
        {
            $cameras[] = $row['id'];
        }
        $db->close();

        if (!empty($cameras))
        {
            $cameras = implode(':',$cameras);
            return $cameras;
        }
        else
        {
            return null;
        }
    }

    public function getAll()
    {
        $db = new SQLite3(parent::getDbFile());
        $result = $db->query('SELECT id,name from doors');

        while ($row = $result->fetchArray(SQLITE3_ASSOC))
        {
            $doors[] = $row;
        }

        $db->close();
        return $doors;
    }

    public function setId($id)
    {
        if (is_numeric($id))
        {
            $db = new SQLite3(parent::getDbFile());

            $stmt = $db->prepare('SELECT count(1) from doors where id = ?');
            $stmt->bindValue(1, $id, SQLITE3_INTEGER);
            $result = $stmt->execute();
            $row = $result->fetchArray(SQLITE3_NUM);
            $db->close();

            if ($row[0] == 1)
            {
                $this->id = $id;
            }
            else
            {
                $msg = 'O "id" informado n&atilde;o foi localizado.';
                throw new Exception("$msg");
            }
        }
        else
        {
            $msg = 'O "id" deve ser um valor num&eacute;rico.';
            throw new Exception("$msg");
        }
    }

    public function findId()
    {
        if (!empty($this->name))
        {
            $db = new SQLite3(parent::getDbFile());

            $stmt = $db->prepare('SELECT id from doors where name = ?');
            $stmt->bindValue(1, $this->name, SQLITE3_TEXT);
            $result = $stmt->execute();
            $row = $result->fetchArray(SQLITE3_NUM);
            $db->close();

            return $row[0];
        }
        else
        {
            $msg = 'Antes de procurar o "id" de uma porta, &eacute; necess&aacute;rio carregar ou adicionar algum registro.';
            throw new Exception("$msg");
        }
    }

    public function setName($name)
    {
        if (!empty($name))
        {
            $this->name = $name;
        }
        else
        {
            $msg = 'O nome n&atilde;o pode ser nulo.';
            throw new Exception("$msg");
        }
    }

    public function getName()
    {
        return $this->name;
    }
}

class faces extends database
{
    private $id;
    private $name;
    private $img;

    public function faces($dbFile)
    {
        parent::setDbFile($dbFile);
    }

    public function setPermissions($permissions)
    {
        if (empty($this->id))
        {
            $msg = 'Antes de setar as permiss&otilde;es, sete o "id" com o m&eacute;todo setId.';
            throw new Exception("$msg");
        }

        $db = new SQLite3(parent::getDbFile());
        $stmt = $db->prepare('delete from doors_faces where face_id = ?');
        $stmt->bindValue(1, $this->id, SQLITE3_INTEGER);
        if (! $stmt->execute())
        {
            $msg = $db->lastErrorMsg();
            $db->close();

            throw new Exception("$msg");
        }

        $permissions = explode(':', $permissions);
        foreach ($permissions as $permission)
        {
            $door_id = str_replace('door','',$permission);

            $stmt = $db->prepare("insert into doors_faces ('door_id','face_id') values (?,?)");
            $stmt->bindValue(1, $door_id, SQLITE3_INTEGER);
            $stmt->bindValue(2, $this->id, SQLITE3_INTEGER);
            if (! $stmt->execute())
            {
                $msg = $db->lastErrorMsg();
                $db->close();

                throw new Exception("$msg");
            }
        }

        $db->close();
    }

    public function getPermissions()
    {
        if (empty($this->id))
        {
            $msg = 'Antes de checar as permiss&otilde;es, sete o "id" com o m&eacute;todo setId.';
            throw new Exception("$msg");
        }

        $db = new SQLite3(parent::getDbFile());
        $stmt = $db->prepare('select door_id from doors_faces where face_id = ?');
        $stmt->bindValue(1, $this->id, SQLITE3_INTEGER);
        $result = $stmt->execute();

        while ($row = $result->fetchArray(SQLITE3_ASSOC))
        {
            $permissions[] = $row['door_id'];
        }
        $db->close();

        if (!empty($permissions))
        {
            $permissions = implode(':',$permissions);
            return $permissions;
        }
        else
        {
            return null;
        }
    }

    public function del()
    {
        if (empty($this->id))
        {
            $msg = 'Antes de remover, sete o "id" com o m&eacute;todo setId.';
            throw new Exception("$msg");
        }

        $db = new SQLite3(parent::getDbFile());
        $db->exec('PRAGMA foreign_keys = ON;');

        $stmt = $db->prepare("delete from faces where id = ?");
        $stmt->bindValue(1, $this->id, SQLITE3_INTEGER);

        if (! $stmt->execute())
        {
            $msg = $db->lastErrorMsg();
            $db->close();

            throw new Exception("$msg");
        }

        $db->close();
    }

    public function load()
    {
        if (empty($this->id))
        {
            $msg = 'Antes de carregar, sete o "id" com o m&eacute;todo setId.';
            throw new Exception("$msg");
        }

        $db = new SQLite3(parent::getDbFile());

        $stmt = $db->prepare("select name,img from faces where id = ?");
        $stmt->bindValue(1, $this->id, SQLITE3_INTEGER);
        $result = $stmt->execute();
        $row = $result->fetchArray(SQLITE3_ASSOC);

        $this->name = $row['name'];
        $this->img = $row['img'];

        $db->close();
    }

    public function save()
    {
        if (empty($this->id))
        {
            $msg = 'Antes de salvar, carregue alguma face com o m&eacute;todo load.';
            throw new Exception("$msg");
        }

        $db = new SQLite3(parent::getDbFile());

        $stmt = $db->prepare("update faces set name = ? where id = ?");
        $stmt->bindValue(1, $this->name, SQLITE3_TEXT);
        $stmt->bindValue(2, $this->id, SQLITE3_INTEGER);

        if (! $stmt->execute())
        {
            $msg = $db->lastErrorMsg();
            $db->close();

            throw new Exception("$msg");
        }

        $db->close();
    }

    public function add()
    {
        if (empty($this->name))
        {
            $msg = 'Antes de adicionar, adicione um nome com o m&eacute;todo setName.';
            throw new Exception("$msg");
        }

        if (empty($this->img))
        {
            $msg = 'Antes de adicionar, adicione uma imagem com o m&eacute;todo setImg.';
            throw new Exception("$msg");
        }

        $db = new SQLite3(parent::getDbFile());

        $stmt = $db->prepare("insert into faces ('id','name','img') values (?,?,?)");
        $stmt->bindValue(1, $this->id, SQLITE3_INTEGER);
        $stmt->bindValue(2, $this->name, SQLITE3_TEXT);
        $stmt->bindValue(3, $this->img, SQLITE3_BLOB);

        if (! $stmt->execute())
        {
            $msg = $db->lastErrorMsg();
            $db->close();

            throw new Exception("$msg");
        }

        $db->close();
    }

    public function getAll()
    {
        $db = new SQLite3(parent::getDbFile());
        $result = $db->query('SELECT id,name from faces');

        while ($row = $result->fetchArray(SQLITE3_ASSOC))
        {
            $faces[] = $row;
        }

        $db->close();
        return $faces;
    }

    public function setId($id)
    {
        if (is_numeric($id))
        {
            $this->id = $id;
        }
        else
        {
            $msg = 'O "id" deve ser um valor num&eacute;rico.';
            throw new Exception("$msg");
        }
    }

    public function getNextId()
    {
        $db = new SQLite3(parent::getDbFile());

        $stmt = $db->prepare("SELECT seq from SQLITE_SEQUENCE where name = 'faces'; update SQLITE_SEQUENCE set seq = seq + 1 where name = 'faces';");
        $result = $stmt->execute();
        $row = $result->fetchArray(SQLITE3_NUM);

        $db->close();

        return $row[0] + 1;
    }

    public function setImg($img)
    {
        if (!empty($img))
        {
            $data = explode(',', $img);
            $data = base64_decode($data[1]);
            $this->img = $data;
        }
        else
        {
            $msg = 'A imagem n&atilde;o pode ser nula.';
            throw new Exception("$msg");
        }
    }

    public function setName($name)
    {
        if (!empty($name))
        {
            $this->name = $name;
        }
        else
        {
            $msg = 'O nome n&atilde;o pode ser nulo.';
            throw new Exception("$msg");
        }
    }

    public function getName()
    {
        return $this->name;
    }

    public function getImg()
    {
        //header('Content-Type: image/jpeg');
        echo $this->img;
    }
}

class settings extends database
{
    public function settings($dbFile)
    {
        parent::setDbFile($dbFile);
    }

    public function setValue($setting,$value)
    {
        if (empty($setting) || empty($value))
        {
            $msg = 'Os par&acirc;metros "setting" e "value", n&atilde;o podem ser nulos.';
            throw new Exception("$msg");
        }
        else
        {
            $db = new SQLite3(parent::getDbFile());
            $stmt = $db->prepare("insert or replace into settings ('setting','value') values (?,?);");
            $stmt->bindValue(1, $setting, SQLITE3_TEXT);
            $stmt->bindValue(2, $value, SQLITE3_TEXT);

            if (! $stmt->execute())
            {
                $msg = $db->lastErrorMsg();
                $db->close();

                throw new Exception("$msg");
            }
            $db->close();
        }
    }

    public function getValue($setting)
    {
        if (empty($setting))
        {
            $msg = 'O par&acirc;metro "name" n&atilde;o pode ser nulo.';
            throw new Exception("$msg");
        }
        else
        {
            $db = new SQLite3(parent::getDbFile());

            $stmt = $db->prepare("select value from settings where setting = ?;");
            $stmt->bindValue(1, $setting, SQLITE3_TEXT);
            $result = $stmt->execute();
            $result = $result->fetchArray(SQLITE3_ASSOC);
            $db->close();
        }

        if(empty($result))
        {
            return null;
        }
        else
        {
            return $result['value'];
        }
    }

    public function getAll()
    {
        $db = new SQLite3(parent::getDbFile());
        $result = $db->query('SELECT setting,value from settings');

        while ($row = $result->fetchArray(SQLITE3_ASSOC))
        {
            if ($row['setting'] == 'conf_value')
            {
                continue;
            }
            else
            {
                $settings[] = $row;
            }
        }

        $db->close();
        return $settings;
    }
}

class users extends database
{
    private $id;
    private $name;
    private $login;
    private $passwd;

    public function users($dbFile)
    {
        parent::setDbFile($dbFile);
    }

    public function add()
    {
        if (empty($this->name))
        {
            $msg = 'Antes de adicionar, adicione um nome com o m&eacute;todo setName.';
            throw new Exception("$msg");
        }

        if (empty($this->login))
        {
            $msg = 'Antes de adicionar, adicione um login com o m&eacute;todo setLogin.';
            throw new Exception("$msg");
        }

        if (empty($this->passwd))
        {
            $msg = 'Antes de adicionar, adicione uma senha com o m&eacute;todo setPasswd.';
            throw new Exception("$msg");
        }

        $db = new SQLite3(parent::getDbFile());

        $stmt = $db->prepare("select count(1) from users where login = ?");
        $stmt->bindValue(1, $this->login, SQLITE3_TEXT);
        $result = $stmt->execute();
        $result = $result->fetchArray(SQLITE3_NUM);

        if ($result[0] > 0)
        {
            $msg = 'O login de usu&aacute;rio ' . $this->login . ' j&aacute; existe.';
            $db->close();

            throw new Exception("$msg");
        }
        else
        {
            $stmt = $db->prepare("insert into users ('name','login','passwd') values (?,?,?)");
            $stmt->bindValue(1, $this->name, SQLITE3_TEXT);
            $stmt->bindValue(2, $this->login, SQLITE3_TEXT);
            $stmt->bindValue(3, $this->passwd, SQLITE3_TEXT);

            if (! $stmt->execute())
            {
                $msg = $db->lastErrorMsg();
                $db->close();

                throw new Exception("$msg");
            }

            $db->close();
        }
    }

    public function del()
    {
        if (empty($this->id))
        {
            $msg = 'Antes de remover, sete o "id" com o m&eacute;todo setId.';
            throw new Exception("$msg");
        }

        $db = new SQLite3(parent::getDbFile());
        $db->exec('PRAGMA foreign_keys = ON;');

        $stmt = $db->prepare("delete from users where id = ?");
        $stmt->bindValue(1, $this->id, SQLITE3_INTEGER);

        if (! $stmt->execute())
        {
            $msg = $db->lastErrorMsg();
            $db->close();

            throw new Exception("$msg");
        }

        $db->close();
    }

    public function load()
    {
        if (empty($this->id))
        {
            $msg = 'Antes de carregar, sete o "id" com o m&eacute;todo setId.';
            throw new Exception("$msg");
        }

        $db = new SQLite3(parent::getDbFile());

        $stmt = $db->prepare("select name,login,passwd from users where id = ?");
        $stmt->bindValue(1, $this->id, SQLITE3_INTEGER);
        $result = $stmt->execute();
        $row = $result->fetchArray(SQLITE3_ASSOC);

        $this->name = $row['name'];
        $this->login = $row['login'];
        $this->passwd = $row['passwd'];

        $db->close();
    }

    public function save()
    {
        if (empty($this->id))
        {
            $msg = 'Antes de salvar, carregue algum usu&aacute;rio com o m&eacute;todo load.';
            throw new Exception("$msg");
        }

        $db = new SQLite3(parent::getDbFile());

        $stmt = $db->prepare("update users set name = ?, login = ?, passwd = ? where id = ?");
        $stmt->bindValue(1, $this->name, SQLITE3_TEXT);
        $stmt->bindValue(2, $this->login, SQLITE3_TEXT);
        $stmt->bindValue(3, $this->passwd, SQLITE3_TEXT);
        $stmt->bindValue(4, $this->id, SQLITE3_INTEGER);

        if (! $stmt->execute())
        {
            $msg = $db->lastErrorMsg();
            $db->close();

            throw new Exception("$msg");
        }

        $db->close();
    }

    public function getAll()
    {
        $db = new SQLite3(parent::getDbFile());
        $result = $db->query('SELECT id,name,login from users');

        while ($row = $result->fetchArray(SQLITE3_ASSOC))
        {
            $users[] = $row;
        }

        $db->close();
        return $users;
    }

    public function setPasswd($passwd)
    {
        if (!empty($passwd))
        {
            $this->passwd = password_hash($passwd, PASSWORD_DEFAULT);
        }
        else
        {
            $msg = 'a senha n&atilde;o pode ser nula.';
            throw new Exception("$msg");
        }
    }

    public function setName($name)
    {
        if (!empty($name))
        {
            $this->name = $name;
        }
        else
        {
            $msg = 'O nome n&atilde;o pode ser nulo.';
            throw new Exception("$msg");
        }
    }

    public function setLogin($login)
    {
        if (!empty($login))
        {
            if (empty($this->login) || $this->login != $login)
            {
                $db = new SQLite3(parent::getDbFile());

                $stmt = $db->prepare('SELECT count(1) from users where login = ?');
                $stmt->bindValue(1, $login, SQLITE3_TEXT);
                $result = $stmt->execute();
                $row = $result->fetchArray(SQLITE3_NUM);
                $db->close();

                if ($row[0] == 0)
                {   
                    $this->login = $login;
                }
                else
                {
                    $msg = 'O login ' . $login . ' j&aacute; existe.';
                    throw new Exception("$msg");
                }
            }
        }
        else
        {
            $msg = 'O login n&atilde;o pode ser nulo.';
            throw new Exception("$msg");
        }
    }

    public function setId($id)
    {
        if (is_numeric($id))
        {
            $db = new SQLite3(parent::getDbFile());

            $stmt = $db->prepare('SELECT count(1) from users where id = ?');
            $stmt->bindValue(1, $id, SQLITE3_INTEGER);
            $result = $stmt->execute();
            $row = $result->fetchArray(SQLITE3_NUM);
            $db->close();

            if ($row[0] == 1)
            {   
                $this->id = $id;
            }
            else
            {
                $msg = 'O "id" informado n&atilde;o foi localizado.';
                throw new Exception("$msg");
            }
        }
        else
        {
            $msg = 'O "id" deve ser um valor num&eacute;rico.';
            throw new Exception("$msg");
        }
    }

    public function getName()
    {
        return $this->name;
    }

    public function getLogin()
    {
        return $this->login;
    }

    public function getPasswd()
    {
        return $this->passwd;
    }

    public function getId()
    {
        return $this->id;
    }
}

?>
