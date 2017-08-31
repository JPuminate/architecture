<?php
/**
 * Created by PhpStorm.
 * User: Ouachhal
 * Date: 31/08/2017
 * Time: 12:20
 */

namespace JPuminate\Architecture\EventBus\Serialization;

class JSONDeserializer
{

    public function deserialize($type, $json)
    {
        if (!class_exists($type)) throw new \RuntimeException("class not existe");
        try {
            $object = new $type();
            $object->promote($json);
            return $object;
        } catch (\Exception $e) {
            throw new \RuntimeException("error deserialization");
        }
    }
}