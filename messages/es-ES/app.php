<?php

use app\models\Accion;

return [
    Accion::DROPPED => '{user} abandonó "{name}" el {date}',
    Accion::WATCHED => '{user} terminó "{name}" el {date}',
    Accion::WATCHING => '{user} empezó a ver "{name}" el {date}',
];
