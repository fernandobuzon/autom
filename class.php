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
        $result = $db->query('SELECT * from logs');

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

        $stmt = $db->prepare("delete from logs where camera_id = ?");
        $stmt->bindValue(1, $this->id, SQLITE3_INTEGER);
        if (! $stmt->execute())
        {
            $msg = $db->lastErrorMsg();
            $db->close();

            throw new Exception("$msg");
        }

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
            $db = new SQLite3(parent::getDbFile());

            $stmt = $db->prepare('SELECT count(1) from cameras where name = ?');
            $stmt->bindValue(1, $name, SQLITE3_TEXT);
            $result = $stmt->execute();
            $row = $result->fetchArray(SQLITE3_NUM);
            $db->close();

            if ($row[0] == 0)
            {
                $this->name = $name;
            }
            else
            {
                $msg = 'O "nome" informado j&aacute; existe.';
                throw new Exception("$msg");
            }
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
            $db = new SQLite3(parent::getDbFile());

            $stmt = $db->prepare('SELECT count(1) from cameras where netcam_url = ?');
            $stmt->bindValue(1, $netcam_url, SQLITE3_TEXT);
            $result = $stmt->execute();
            $row = $result->fetchArray(SQLITE3_NUM);
            $db->close();

            if ($row[0] == 0)
            {
                $this->netcam_url = $netcam_url;
            }
            else
            {
                $msg = 'O "netcam_url" informado j&aacute; existe.';
                throw new Exception("$msg");
            }
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
        else
        {
            $msg = 'O valor "netcam_userpass" n&tilde;o pode ser vazio.';
            throw new Exception("$msg");
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

        $stmt = $db->prepare("insert into faces ('name','img') values (?,?)");
        $stmt->bindValue(1, $this->name, SQLITE3_TEXT);
        $stmt->bindValue(2, $this->img, SQLITE3_BLOB);

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
            $db = new SQLite3(parent::getDbFile());

            $stmt = $db->prepare('SELECT count(1) from faces where id = ?');
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

            $stmt = $db->prepare('SELECT id from faces where name = ?');
            $stmt->bindValue(1, $this->name, SQLITE3_TEXT);
            $result = $stmt->execute();
            $row = $result->fetchArray(SQLITE3_NUM);
            $db->close();

            return $row[0];
        }
        else
        {
            $msg = 'Antes de procurar o "id" de uma face, &eacute; necess&aacute;rio carregar ou adicionar algum registro.';
            throw new Exception("$msg");
        }
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
