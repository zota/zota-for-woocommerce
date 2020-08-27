<?php

namespace Zotapay;

/**
 * Class Data.
 */
abstract class AbstractData
{
    public function __construct($data = null)
    {
        if (!empty($data)) {
            foreach ($data as $key => $value) {
                $setter = 'set' . \ucwords($key);

                if (\method_exists($this, $setter)) {
                    $this->$setter($value);
                }
            }
        }
    }
}
