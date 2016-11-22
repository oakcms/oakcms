<?php

namespace YOOtheme\Framework\Joomla;

use YOOtheme\Framework\Config\Config;

class Option extends Config
{
    /**
     * Constructor.
     *
     * @param $db
     * @param $element
     */
    public function __construct($db, $element)
    {
        $self = $this;
        $row  = $db->fetchAssoc("SELECT params FROM @extensions WHERE element = :element LIMIT 1", compact('element'));

        parent::__construct(json_decode($row['params'], true) ?: array());

        register_shutdown_function(function () use ($self, $db, $row, $element) {
            if (($params = (string) $self) != $row['params']) {
                $db->update('@extensions', compact('params'), compact('element'));
            }
        });
    }
}
