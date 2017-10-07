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

    public function deserialize($type, $payload)
    {
        if (!class_exists($type)) throw new \RuntimeException("class not exist");
        try {
            $reflector = new \ReflectionClass($type);
            $object = $reflector->newInstanceWithoutConstructor();
            $properties = $reflector->getProperties();
            foreach ($properties as $property) {
                $property->setAccessible(true);
                $property->setValue($object, $payload->{$property->getName()});
            }
            return $object;
        } catch (\Exception $e) {
            throw new \RuntimeException("error deserialization");
        }
    }
}