<?php
namespace app\modules\order\interfaces;

interface User
{
    function getUserProfile();
    function getEmail();
    function getPhone();
    function getName();
    function getFullName();
}
