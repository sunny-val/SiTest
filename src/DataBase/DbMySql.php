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
    private $errno_text = null;

    // имя сервера, на котором расположена база данных
    protected $host = 'xxxxxxxxxx';

    // название базы данных, которая уже должна быть создана
    protected $database = 'xxxxxx';

    // номер порта для подключения, пусто если порт по умолчанию (MySQL - 3306, PGSQL - 5432)
    protected $port = '';

    // имя пользователя для доступа к базе данных
    protected $username = 'xxxxxxx';

    // пароль для доступа к базе данных
    protected $password = "xxxxxxxxx";

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

    /**
     * Движок базы данных по умолчанию
     *
     * @return string
     */
    private function getEngine()
    {
        // движок БД. MyISAM - быстро ; InnoDB - надежно, при большом числе запросов быстрее.
        return 'InnoDB';
    }

    /**
     * Проверка на допустимость указателя к базе
     *
     * @return bool
     */
    protected function isValidDb()
    {
        // сбрасываем текста с ошибкой
        $this->errno_text = null;
        if (empty($this->db) || gettype($this->db) != 'object') {
            $this->errno_text = _('Error connecting to MySQL database'); // 'Неверный указатель на базу данных db';
            return false;
        }
        // код ошибки, 0 - без ошибок
        $this->errno = $this->db->connect_errno;
        // если есть код ошибки
        if ($this->errno != 0) {
            // Ошибка подключения - Error Establishing a Database Connection
            $this->errno_text = $this->db->connect_error;
            return false;
        }
        return true;
    }

    /**
     * Соедининие с базой данных, устанавливает $db в случае успеха, $err_text в случае ошибки
     *
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
            $this->database_is_open = true;
            return true;
        }
        $this->close();
        unset($this->db);
        return false;
    }

    /**
     * Отключение от базы данных, сбрасывает $db и $err_text в случае успеха
     *
     * @return bool
     */
    protected function close()
    {
        if (isset($this->database_is_open) && $this->db->close()) {
            unset($this->db);
            $this->errno_text = null; // сброс текста с ошибкой
            return true;
        }
        return false;
    }

    /**
     * Возвращает ошибку в виде строки
     *
     * @return string
     */
    public function getErrorStr()
    {
        return $this->errno_text;
    }

    /**
     * Экранирует специальные символы в строке для использования в выражениях SQL
     *
     * @param
     *            string
     * @return string новая строка
     */
    public function escapeString($string)
    {
        $new_string = mysqli_real_escape_string($this->db, $string);
        return $new_string;
    }

    /**
     * Запрос к базе данных
     *
     * @param
     *            req - строка с SQL запросом
     * @return bool - возвращает null в случае неудачи. В случае успешного выполнения запросов SELECT, SHOW, DESCRIBE или EXPLAIN
     *         вернет объект mysqli_result. Для остальных успешных запросов вернет true.
     */
    private function requestQuery($req)
    {
        if (! $this->isValidDb() || empty($req))
            return null;
        $result = $this->db->query($req);
        if (! $result) // запрос не был выполнен
        {
            $this->errno = mysqli_errno($this->db);
            $this->errno_text = mysqli_error($this->db);
            // printf("[%s] Error message(%d): %s<br>\n", __METHOD__, mysqli_errno($this->db), mysqli_error($this->db)); // __FUNCTION__ __METHOD__
            return null;
        }
        return $result;
    }

    /**
     * Создание таблицы в базе данных
     *
     * @param
     *            table_name - название таблицы
     * @param
     *            columns_param - массив столбцов - array([$columnName => array([$coltype => $value]...)]... )
     * @param
     *            comment - комментарий к таблице
     * @return bool - true (при успешном выполнении операции) или false
     *        
     *         параметры столбцов:
     *         type - тип (char|int|bool|date|enum)
     *         length - (число) макс. длина записи
     *         allow_null - (0|1) позволить быть NULL в ячейке (не для txtsql)
     *         default - (число/текст) значение по умолчанию
     *         auto_increment - (0|1) автоматически увеличивающиеся значение
     *         permanent - (0|1) столбец только для чтения
     *         comment - (текст) комментарий
     *         primary - (0|1) столбец является первичным ключём таблицы
     *         index - (0|1) индексируемый столбец
     *         unique - (0|1) столбец с уникальным значением в каждом поле
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

    /**
     * * Переименование таблицы в базе данных
     *
     * @param string $table_name_prev
     *            предыдущее название таблицы в БД
     * @param string $table_name_new
     *            новое название таблицы в БД
     * @return boolean
     */
    public function renameTable($table_name_prev, $table_name_new)
    {
        // $req = sprintf("RENAME TABLE `%s` TO `%s`", $table_name_prev,$table_name_new);
        $req = sprintf("ALTER TABLE `%s` RENAME TO `%s`", $table_name_prev, $table_name_new);
        
        if (! $this->requestQuery($req))
            return false; // запрос не был выполнен
        return true;
    }

    /**
     * Удаление таблицы в базе данных
     *
     * @param string $table_name
     *            название таблицы в БД
     * @return boolean
     */
    public function dropTable($table_name)
    {
        $req = sprintf("DROP TABLE IF EXISTS `%s`", $table_name);
        if (! $this->requestQuery($req))
            return false; // запрос не был выполнен
        return true;
    }

    /**
     * Чистка таблицы в базе данных от всех записей
     *
     * @param string $table_name
     *            название таблицы в БД
     * @return boolean
     */
    protected function truncateTable($table_name)
    {
        return $this->requestQuery("truncate `{$table_name}`");
    }

    /**
     * *** Data Manipulation Functions ****
     */
    
    /**
     * вставить строку в базу данных
     *
     * @param string $table_name
     * @param array $data_array
     *            массив данных для новой строки
     * @return boolean true (при успешном выполнении операции) или false
     */
    public function insertData($table_name, $data_array)
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
            // $errno = $this->errno;
            // if ($errno == 1062)
            // $this->err_text = _("Duplicate task name");
            // else
            // if ($errno == 1062)
            // $this->err_text = _("Can't save task in database");
            return false;
        }
    }

    /**
     * Конвертировать массив в строку для запроса MySQL
     *
     * @param array $where
     *            массив условий для выбора строк, пример: array("`login` = $login",'and',"`password` = $pass")
     *            
     *            Логические операции:
     *            array('$a', 'and', '$b'), array('$a', 'or', '$b'), array('$a', 'xor', '$b')
     *            
     *            Функции:
     *            array('md5($a) = $b'), array('strUpper($a) = $b')
     *            
     *            Relational Operators:
     *            array('$a = $b') equal to TRUE if $a is equal to $b
     *            array('$a != $b') not equal to TRUE if $a is not equal to $b
     *            array('$a <> $b') not equal to TRUE if $a is not equal to $b
     *            array('$a < $b') less than TRUE if $a is less than $b
     *            array('$a <= '$b') less than or equal to TRUE if $a is less than or equal to $b
     *            array('$a > $b') greater than TRUE if $a is greater than $b
     *            array('$a >= $b') greater than or equal to TRUE if $a is greater than or equal to $b
     *            array('$a =~ $b') like TRUE if $a matches the pattern $b. Also see: LIKE clauses
     *            array('$a !~ $b') not like TRUE if $a does _NOT_ match the pattern $b.
     *            array('$a ? $b') instring (txtSQL >= 2.2.2 RC2) TRUE if $b is in $a
     *            array('$a !? $b') not instring (txtSQL >= 2.2.2 RC2) TRUE if $b is NOT in $a
     * @return string строка для запроса к БД, '' - при ошибке
     */
    private function getWhere($where)
    {
        if (empty($where) || ! is_array($where))
            return '';
        $req = 'where';
        foreach ($where as $key => $value) {
            $req .= " $value";
        }
        return $req;
    }

    /**
     * Конвертировать массив в строку для запроса MySQL
     *
     * @param array $orderby
     *            порядок сортировки строк, пример: array($column1, [ASC|DESC],$column2, [ASC|DESC])
     * @return string строка для запроса к БД, '' - при ошибке
     */
    private function getOrderBy($orderby)
    {
        if (empty($orderby) || ! is_array($orderby))
            return '';
        $req = 'order by ';
        $length = count($orderby);
        for ($i = 0; $i < $length; $i ++) {
            $value = "`{$orderby[$i]}`";
            // если следующая значение - новый столбец или конец массива
            if (empty($orderby[$i + 1]) || (strtolower($orderby[$i + 1]) != 'asc' && strtolower($orderby[$i + 1]) != 'desc'))
                $order = 'ASC';
            // если следующее значение - направление сортировки
            else {
                $order = $orderby[$i + 1];
                $i ++;
            }
            if (! empty($value)) {
                $req .= "$value $order, ";
            }
        }
        // удаляем последнюю запятую
        $req = rtrim($req, ', ');
        return $req;
    }

    /**
     * Конвертировать массив в строку для запроса MySQL
     *
     * @param array $limit
     *            ограничение на число строк, пример: 'limit' => array(10, 19) - 2 числа, начало и длина, если одно число, то количество
     * @return string строка для запроса к БД, '' - при ошибке
     */
    private function getLimit($limit)
    {
        if (empty($limit) || ! is_array($limit))
            return '';
        if (empty($limit[0]))
            return '';
        if (empty($limit[1]))
            $req = 'LIMIT ' . $limit[0];
        else
            $req = 'LIMIT ' . "$limit[0], $limit[1]";
        return $req;
    }

    /**
     * выбрать строки из базы данных
     *
     * @param string $table_name
     *            название таблицы в БД
     * @param array|string $columns
     *            массив столбцов для выбора, если все стоблцы, тогда array('*') или '*', пример: array('id', 'name')
     * @param array|null $where
     *            массив условий для выбора строк, пример: array("`somecolumn` = 'value'"),
     * @param array|null $orderby
     *            порядок сортировки строк, пример: array('id', 'ASC')
     * @param array|null $limit
     *            ограничение на число строк, пример: null или array(10) или array(10, 19)
     *            null - без ограничениий, 2 числа - начало и длина, если одно число, то количество
     * @return boolean|array массив с данными или пустой массив при успешном выполнении, false - при ошибке
     */
    public function selectData($table_name, $columns, $where, $orderby, $limit = null)
    {
        $columns_list = ' ';
        if (! is_array($columns))
            $columns = array(
                $columns
            );
        foreach ($columns as $key => $value) {
            if ($value == '*') // выбрать все стоблцы
            {
                $columns_list = $value;
                break;
            } else
                $columns_list .= "`$value`, ";
        }
        $columns_list = rtrim($columns_list, ', ');
        // select * from `{$this->dbase->table_name_vsite_struct}` where (`theme`='' or `theme`='$theme_name') and `position`='$position' and `use`='1' order by `order`
        $req = "select  $columns_list from `$table_name` {$this->getWhere($where)} {$this->getOrderBy($orderby)} {$this->getLimit($limit)}";
        $result = $this->requestQuery($req);
        if (! $result)
            return false;
        // массив с массивами данных
        $mass = array();
        $i = 1;
        // получаем массив c символьными индексами (MYSQLI_ASSOC MYSQLI_NUM MYSQLI_BOTH)
        while ($row = $result->fetch_array(MYSQLI_ASSOC)) // mysqli_fetch_array($result,MYSQL_BOTH);// массив с числовыми индексами
        {
            $mass[$i] = $row;
            $i ++;
        }
        return $mass;
    }

    /**
     * Удаление строк из базы данных по фильтру
     *
     * @param string $table_name
     *            название таблицы в БД
     * @param array|null $where
     *            массив условий для выбора строк
     * @param array|null $limit
     *            ограничение на число строк, пример: 'limit' => array(10)
     * @return boolean
     */
    public function deleteData($table_name, $where, $limit = null)
    {
        if (empty($where) && empty($limit)) // all rows will be deleted - безусловное удаление
            return $this->truncateTable($table_name);
        $req_where = $this->getWhere($where);
        $req_limit = $this->getLimit($limit);
        $req = "delete from `$table_name` ";
        if (! empty($req_where))
            $req = $req . " $req_where";
        if (! empty($req_limit))
            $req = $req . " $req_limit";
        // $req = "delete from `$table_name` " . $this->get_where($where) . ' ' . $this->get_limit($limit);
        $result = $this->requestQuery($req);
        if ($result)
            return true;
        else
            return false;
    }

    /**
     * Обновить строки в базе данных
     *
     * @param string $table_name
     *            название таблицы в БД
     * @param array|null $where
     *            массив условий для выбора строк, пример: array('strtolower(somecolumn) = value'),
     * @param array $new_val
     *            массив с новыми значениями, пример: array('name' => 'Vasya', 'sex' => 'male')
     * @param array $limit
     *            ограничение на число строк, пример: 'limit' => array(10)
     * @return boolean
     */
    public function updateData($table_name, $where, $new_val, $limit = null)
    {
        $set_sql = 'set '; // set `sid`='$sid', `sid_expire`='$sid_expire', `sid_cond`='$sid_cond'
        foreach ($new_val as $key => $value) {
            $set_sql .= "`$key`='$value', ";
        }
        // удаляем последнюю запятую
        $set_sql = rtrim($set_sql, ', ');
        $result = $this->requestQuery("update `$table_name` $set_sql {$this->getWhere($where)}");
        if ($result)
            return true;
        else
            return false;
    }
}
