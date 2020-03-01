<?php
/**
 * Class BuilderMySQl
 * Строитель для sql запросов по типу eloquent
 */

class BuilderMySQl
{
    private $db;
    private $table;
    private $whereQuery;
    private $limit;
    private $orderByParam;
    private $order;

// подключимся к БД
    public function __construct()
    {
        $this->db = new mysqli('localhost', 'root', '', 'test');
        if ($this->db->connect_errno) {
            die("Не удалось подключиться к MySQL: " . $this->db->connect_error);
        }
    }

// создадим статический метод с которого начинается любой запрос
    public static function table($table)
    {
        $instance = new self();
        $instance->table = $table;
        return $instance;
    }

//условия выборки
    public function where($column, $op, $value = null)
    {
        //что бы каждый раз не указывать все знаки между $ор и $value, присвоем по умолчанию ор '=', теперь
        // если нет сравнения можно указывать два параметра Where('id', 1) и это удет соответствовать
        //Where(column:'id', op:'=', value:1) если $value отсутствует то мы
        if (!$value) {
            $value = $op;// сдвигаем $op в сторону $ор2
            $op = '=';// и ор стает равным '='
        }
        $this->whereQuery[] = [
            'column' => $column,
            'op' => $op,
            'value' => $value
        ];
        return $this;

    }

    // лимит
    public function first()
    {
        $this->limit = 1;
        return $this;
    }

    public function take($count = 1)
    {
        $this->limit = $count;
        return $this;
    }

    // DELETE
    public function delete()
    {
        $q = "DELETE FROM `" . $this->table . "`";
        $this->mysqlWhereSet($q);
        return $this->db->query($q);
    }

    private function mysqlWhereSet(&$q)
    {
        //проверим если условие
        if ($this->whereQuery) {
            $q .= " WHERE ";
            foreach ($this->whereQuery as $index => $where) {
                if ($index !== 0) {
                    $q .= " AND ";
                }
                $q .= "`{$where['column']}` {$where['op']} '{$where['value']}' ";
            }

        }

    }

    //INSERT INTO
    public function insert($data)
    {
        if (sizeof($data) === 0) {// Псевдоним count()
            return false;
        }
        // формируем массив с названиями столбцов
        $keys = [];
        foreach ($data[0] as $column => $value) {
            //в массив записываем названеи столцов
            $keys[] = $column;
        }
        //формируем запрос, оформим столбцы (`user_id`, `items`), для этого используем
        // (`".implode('`,`',$keys)."`) склеиваем данные  массива $keys в строку с разделителем `,`
        $q = "INSERT INTO `" . $this->table . "`(`" . implode('`,`', $keys) . "`) VALUES ";
        $d = [];
        //фармируем массив VALUES
        foreach ($data as $row) {
            $data = [];
            foreach ($keys as $key) {
                // в массив $data записываем данные соответствующие своему столбцу $row[$key]
                // мы проходим циклом по массиву row с индексом key (т.е с названиями стобцов)
                $data[] = $row[$key];
            }
            // формируем данные для вставки, склееваем из массива  $data значения для столбцов  ('1','1')
            $d[] = "('" . implode("','", $data) . "')";
        }
        // склееваем множество значений ('1','1') ('2','2')
        $q .= implode(",", $d);
        // Запрос
        return $this->db->query($q);
    }

    public function orderBy($key, $order="ASC")
    {
        $this->orderByParam = $key;
        $this->order = $order;
        return $this;

    }

//сам запрос
    public function get()
    {
        //укажем таблицу
        // $query = 'SELECT*FROM`user`WHERE `id`=1 LIMIT 1';
        $q = "SELECT * FROM `" . $this->table . "`";
        $this->mysqlWhereSet($q);//проверим на наличие where b and
        //добавим сортировку
        if ($this->orderByParam){
            $q.=" ORDER BY ".$this->orderByParam." ".(empty($this->order)? "ASC":$this->order);
        }
        //добавим лимит
        if ($this->limit) {
            $q .= " LIMIT " . $this->limit;
        }
        return $this->db->query($q)->fetch_all(MYSQLI_ASSOC);
    }
}
//Удалить
//BuilderMySQl::table('orders')->where('user_id',2)->delete();
//SELECT

$item = BuilderMySQl::table('items')
    ->where('id', '<', 6)
    ->where('category_id', 2)
    ->take(2)
    ->orderBy('id','DESC')
    ->get();
print_r($item);
/*
//INSERT если необходимо вставить много значений, лучше всего сначала сформировать циклом запрос,
// нельзя вставлять запрос в цикл и передавать значения по одному, это увеличивает нагрузку на пустом месте
// для передачи данных в запрос мы организуем массив массивов
$res = BuilderMySQl::table('orders')->insert([
    [
        'user_id' => 1,
        'items' => 1
    ],
    [
        'user_id' => 2,
        'items' => 2
    ]
]);
var_dump($res);
*/