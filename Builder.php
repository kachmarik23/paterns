<?php

/**
 * Class Builder
 * Шаблон строитель, его етобы возвращают сами себя
 *при создании обЪекта  строителя $b=new Builder()  мы можем достраивать его параметры
 * $b=$b->test('Значение 1')->set('Значение 2')->test('Значение 3')->set('Значение 4');
 * это помогает избежать громостких констукторов
 */
class Builder
{
    private $test;
    private $set;

    public function test($test)
    {
        $this->test = $test;
        return $this;
    }

    public function set($set)
    {
      $this->set=$set;
      return $this;
    }

}

$b=new Builder();
$b=$b->test('Значение 1')->set('Значение 2')->test('Значение 3')->set('Значение 4');

print_r($b);