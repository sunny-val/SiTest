<?php
namespace SearchInform\DataBase;

use mysqli;

/**
 *
 * @author Aleksandr
 *        
 */
class DbMySql
{

    // объект, представляющий подключение к открытой базе
    private $db;

    // текст с последней ошибкой
    private $err_text = null;

    // имя сервера, на котором расположена база данных
    protected $host = 'localhost';

    // название базы данных, которая уже должна быть создана
    protected $database = 'test';

    // номер порта для подключения, пусто если порт по умолчанию (MySQL - 3306, PGSQL - 5432)
    protected $port = '';

    // имя пользователя для доступа к базе данных
    protected $username = 'root';

    // пароль для доступа к базе данных
    protected $password = '123';

    // название таблицы с задачами
    public $task_table = "tasks";

    /**
     */
    public function __construct()
    {
        // создать соединение с БД
        if (! $this->connect())
            return;
        /*
         * создать таблицу, если её ещё нет
         * CREATE TABLE `tasks` (
         * `uuid` VARCHAR(40) NOT NULL COMMENT 'UUID4 идентификатор задачи',
         * `name` VARCHAR(200) NULL DEFAULT NULL COMMENT 'Название задачи',
         * `tags` VARCHAR(600) NULL DEFAULT NULL COMMENT 'Список тегов через пробел',
         * `priority` TINYINT(4) NOT NULL COMMENT 'приоритет',
         * `status` TINYINT(3) NOT NULL COMMENT 'статус',
         * PRIMARY KEY (`uuid`),
         * UNIQUE INDEX `uuid` (`uuid`),
         * INDEX `priority` (`priority`)
         * )
         */
        $columns = array(
            // 'id' => array(
            // 'type' => 'int',
            // 'allow_null' => 0,
            // 'auto_increment' => 1,
            // 'permanent' => 1,
            // 'primary' => 1,
            // 'comment' => 'порядковый номер задачи'
            // ),
            'uuid' => array(
                'type' => 'char',
                'length' => 40,
                'allow_null' => 0,
                'unique' => 1,
                'index' => 1,
                'primary' => 1,
                'comment' => 'UUID4 идентификатор задачи'
            ),
            'name' => array(
                'type' => 'char',
                'length' => 200,
                'allow_null' => 0,
                'index' => 1,
                'unique' => 1,
                'comment' => 'Название задачи'
            ),
            'tags' => array(
                'type' => 'char',
                'length' => 600,
                'allow_null' => 1,
                'default' => NULL,
                'unique' => 0,
                'comment' => 'Список тегов через пробел'
            ),
            'priority' => array(
                'type' => 'int',
                'length' => 4,
                'allow_null' => 0,
                'default' => 0,
                'index' => 1,
                'comment' => 'приоритет'
            ),
            'status' => array(
                'type' => 'int',
                'length' => 3,
                'allow_null' => 0,
                'default' => 0,
                'comment' => 'статус'
            )
        );
        
        $this->createTable($this->task_table, $columns);
    }

    /**
     */
    function __destruct()
    {
        // закрыть соединение с БД
        $this->close();
    }

    /*
     * Движок базы данных по умолчанию
     * @return string
     */
    private function getEngine()
    {
        // движок БД. MyISAM - быстро ; InnoDB - надежно, при большом числе запросов быстрее.
        return 'InnoDB';
    }

    /*
     * Проверка на допустимость указателя к базе
     * @return bool
     */
    protected function isValidDb()
    {
        // сбрасываем текста с ошибкой
        $this->err_text = null;
        if (empty($this->db) || gettype($this->db) != 'object') {
            $this->err_text = _('Error connecting to MySQL database'); // 'Неверный указатель на базу данных db';
            return false;
        }
        // код ошибки, 0 - без ошибок
        $err_code = $this->db->connect_errno;
        // если есть код ошибки
        if ($err_code != 0) {
            // Ошибка подключения - Error Establishing a Database Connection
            $this->err_text = _('Error connecting to MySQL database') . ' (' . $this->db->connect_errno . ') ' . $this->db->connect_error;
            return false;
        }
        return true;
    }

    /*
     * Соедининие с базой данных, устанавливает $db в случае успеха, $err_text в случае ошибки
     * @return bool
     */
    protected function connect()
    {
        // пока нет ошибок
        $err_code = 0;
        // подключение к базе данных + выбираем текущую базу данных
        if (is_numeric($this->port)) {
            $this->db = new mysqli($this->host, $this->username, $this->password, $this->database, $this->port);
        } else {
            $this->db = new mysqli($this->host, $this->username, $this->password, $this->database);
        }
        if ($this->isValidDb()) {
            // выставляем кодировку обмена с сервером (для нормальной работы mysqli_real_escape_string() в том числе)
            mysqli_set_charset($this->db, 'utf8');
            return true;
        }
        $this->close();
        unset($this->db);
        return false;
    }

    /*
     * Отключение от базы данных, сбрасывает $db и $err_text в случае успеха
     * @return bool
     */
    protected function close()
    {
        if (! $this->db->close())
            return false;
        unset($this->db);
        $this->err_text = null; // сброс текста с ошибкой
        return true;
    }

    /*
     * Возвращает ошибку в виде строки
     * @return string
     */
    public function getErrorStr()
    {
        return $this->err_text;
    }

    /*
     * Экранирует специальные символы в строке для использования в выражениях SQL
     * @param string
     * @return string
     * новая строка
     */
    public function escapeString($string)
    {
        $new_string = mysqli_real_escape_string($this->db, $string);
        return $new_string;
    }

    /*
     * Запрос к базе данных
     * @param req - строка с SQL запросом
     * @return bool - возвращает null в случае неудачи. В случае успешного выполнения запросов SELECT, SHOW, DESCRIBE или EXPLAIN
     * вернет объект mysqli_result. Для остальных успешных запросов вернет true.
     */
    private function requestQuery($req)
    {
        if (! $this->isValidDb() || empty($req))
            return null;
        $result = $this->db->query($req);
        if (! $result) // запрос не был выполнен
        {
            // printf("[%s] Error message(%d): %s<br>\n", __METHOD__, mysqli_errno($this->db), mysqli_error($this->db)); // __FUNCTION__ __METHOD__
            return null;
        }
        return $result;
    }

    /*
     * Создание таблицы в базе данных
     * @param table_name - название таблицы
     * @param columns_param - массив столбцов - array([$columnName => array([$coltype => $value]...)]... )
     * @param comment - комментарий к таблице
     * @return bool - true (при успешном выполнении операции) или false
     *
     * параметры столбцов:
     * type - тип (char|int|bool|date|enum)
     * length - (число) макс. длина записи
     * allow_null - (0|1) позволить быть NULL в ячейке (не для txtsql)
     * default - (число/текст) значение по умолчанию
     * auto_increment - (0|1) автоматически увеличивающиеся значение
     * permanent - (0|1) столбец только для чтения
     * comment - (текст) комментарий
     * primary - (0|1) столбец является первичным ключём таблицы
     * index - (0|1) индексируемый столбец
     * unique - (0|1) столбец с уникальным значением в каждом поле
     */
    public function createTable($table_name, $columns_param, $comment = '')
    {
        if (! $this->isValidDb() || empty($columns_param))
            return FALSE;
        // разбор параметров таблицы и подготовка к составлению запроса к БД в требуемом формате
        $columns = '';
        $columns_primary = '';
        $columns_indexs = '';
        foreach ($columns_param as $key => $value) {
            // длина записи
            $column_length = isset($value['length']) ? $value['length'] : 0;
            // тип записи - по умолчанию строка
            if (isset($value['type']))
                $type = $value['type'];
            else
                $type = null;
            switch ($type) {
                case 'int':
                    if ($column_length > 20)
                        $column_length = 20;
                    if (! $column_length)
                        $column_type = " INT"; // по умолчанию
                    else if ($column_length < 5)
                        $column_type = " TINYINT($column_length)"; // 1 байт: -127...127 или max 255
                    else if ($column_length < 7)
                        $column_type = " SMALLINT($column_length)"; // 2 байта: –32768...32767 или от 0.65535
                    else if ($column_length < 9)
                        $column_type = " MEDIUMINT($column_length)"; // 3 байта: –8388608...8388607 или 0...16777215
                    else if ($column_length < 12)
                        $column_type = " INT($column_length)"; // 4 байта: max 4 294 967 295
                    else
                        $column_type = " BIGINT($column_length)"; // 8 байт: max 18 446 744 073 709 551 615
                    break;
                case 'bool':
                    $column_type = ' BOOL';
                    break;
                case 'date':
                    $column_type = ' DATETIME';
                    break;
                case 'enum':
                    $column_type = " ENUM(isset({$value['enum_val']}) ? {$value['enum_val']} : '')";
                    break;
                case 'char':
                default:
                    {
                        if ($column_length < 21840)
                            $column_type = " VARCHAR($column_length)";
                        else
                            $column_type = " TEXT($column_length)";
                        break;
                    }
            }
            // разрешать ли в записи хранить пустое значение
            if (! empty($value['allow_null']))
                $column_null = ' NULL';
            else
                $column_null = ' NOT NULL';
            // является ли поле счётчиком
            if (! empty($value['auto_increment']))
                $column_ai = ' AUTO_INCREMENT';
            else
                $column_ai = '';
            // значение по умолчанию
            if (! empty($value['default']))
                $column_default = " DEFAULT {$value['default']}";
            else
                $column_default = '';
            // комментарий
            if (! empty($value['comment']))
                $column_comment = " COMMENT '{$value['comment']}'";
            else
                $column_comment = '';
            // полная строка
            $columns = $columns . "`$key`" . $column_type . $column_null . $column_ai . $column_default . $column_comment . ",\n";
            
            // первичный индекс - обязательное поле в единственном экземпляре на таблицу
            if (! empty($value['primary']))
                $columns_primary = " PRIMARY KEY (`$key`)";
            // индексы
            if (! empty($value['index'])) {
                if (! empty($value['unique']))
                    $columns_indexs = $columns_indexs . ",\n" . " UNIQUE INDEX `$key` (`$key`)";
                else
                    $columns_indexs = $columns_indexs . ",\n" . " INDEX `$key` (`$key`)";
            }
        }
        // формирование запроса к БД на создание таблицы
        $req = sprintf("CREATE TABLE IF NOT EXISTS `%s` (\n%s%s%s\n)
						COMMENT='%s'
						COLLATE='utf8_general_ci'
						ENGINE=%s", $table_name, $columns, $columns_primary, $columns_indexs, $comment, $this->getEngine());
        if (! $this->requestQuery($req))
            return false; // запрос не был выполнен
        return true;
    }

    // переименование таблицы в базе данных
    public function rename_table($table_name_prev, $table_name_new)
    {
        // $req = sprintf("RENAME TABLE `%s` TO `%s`", $table_name_prev,$table_name_new);
        $req = sprintf("ALTER TABLE `%s` RENAME TO `%s`", $table_name_prev, $table_name_new);
        
        if (! $this->requestQuery($req))
            return false; // запрос не был выполнен
        return true;
    }

    // удаление таблицы в базе данных
    public function drop_table($table_name)
    {
        $req = sprintf("DROP TABLE IF EXISTS `%s`", $table_name);
        if (! $this->requestQuery($req))
            return false; // запрос не был выполнен
        return true;
    }

    // вставить строку в базу данных
    // $data_array - массив данных для новой строки
    // return: true (при успешном выполнении операции) или false
    public function insert_data($table_name, $data_array)
    {
        $values_str = '';
        foreach ($data_array as $key => $value) {
            if (! isset($value) || is_null($value)) // if(empty($value))
                $values_str = $values_str . 'null, ';
            else
                $values_str = $values_str . "'$value', ";
        }
        // удаляем последнюю запятую
        $values_str = rtrim($values_str, ', ');
        $result = $this->requestQuery("insert into `$table_name` values($values_str)");
        if ($result)
            return true;
        else {
            // в случае ошибочного запроса выставляем текстовое содержимое ошибки
            $errno = $this->db->errno;
            if ($errno == 1062)
                $this->err_text = _("Duplicate task name");
            else
                $this->err_text = _("Can't save task in database");
            return false;
        }
    }
}
