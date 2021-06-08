<?php
/**
 * Created by PhpStorm.
 * User: Aios
 * https://t.me/aiosslike.
 */

namespace App\Services\Product;

/**
 * Interface DtoMagic.
 */
interface DtoMagic
{
    /**
     * @param $name
     *
     * @return mixed
     */
    public function __get($name);

    /**
     * @param $name
     * @param $value
     *
     * @return DtoMagic
     * @return DtoMagic
     */
    public function __set($name, $value): DtoMagic;

    /**
     * @param $name
     *
     * @return bool
     */
    public function __isset($name): bool;
}
