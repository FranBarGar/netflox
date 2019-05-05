<?php

namespace app\helpers;

use app\models\Archivos;
use app\models\Comentarios;
use app\models\Generos;
use app\models\GestoresArchivos;
use app\models\Participantes;
use app\models\Personas;
use app\models\Roles;
use app\models\Shows;
use app\models\Tipos;
use Yii;
use yii\helpers\Url;

/**
 * Clase Utility.
 */
class Utility
{
    /**
     * @var array Tipos de ordenacion disponibles.
     */
    const ORDER_TYPE = [
        'ASC' => 'Ascendente',
        'DESC' => 'Descendente',
    ];

    /**
     * Devuelve un template de ActiveForm con un icono de Bootstrap en su campo.
     * @param  string $icon Nombre del icono de Bootstrap
     * @return string       La cadena del template
     */
    public static function inputWithIcon($icon)
    {
        return '<div class="input-group">
            <span class="input-group-addon">
                <span class="glyphicon glyphicon-' . $icon . '"></span>
            </span>
            {input}
       </div>';
    }

    /**
     * Codigo JQuery que hace visible/invisible los input password cuando se
     * hace click en el icono de ojo que tienen en su mismo input.
     * @return string Codigo JQuery
     */
    public static function togglePassword()
    {
        return "$('.glyphicon-eye-close').on('click', (e)=>{
            var target = $(e.target);
            target.toggleClass('glyphicon-eye-close');
            target.toggleClass('glyphicon-eye-open');
            if (target.hasClass('glyphicon-eye-open')) {
                target.parents('.input-group').find('input').attr('type', 'text');
            } else {
                target.parents('.input-group').find('input').attr('type', 'password');
            }
        })";
    }

    /**
     * Envia un email.
     * @param  string $cuerpo Archivo con el cuerpo del email
     * @param  array $params Array de parámetros pasados al archivo
     * @param  string $dest Email de destino
     * @param  string $asunto Asunto del email
     * @return bool            True si el email se ha enviado con éxito
     */
    public static function enviarMail($cuerpo, $dest, $asunto, $params = [])
    {
        return Yii::$app->mailer->compose($params)
            ->setFrom([Yii::$app->params['adminEmail'] => Yii::$app->name])
            ->setTo($dest)
            ->setSubject($asunto)
            ->setHtmlBody($cuerpo)
            ->send();
    }

    /**
     * Crea las pestañas para el widget TabX de un array de Archivos.
     * @param $archivos array Array de Archivos.
     * @return          array Devuelve las pestañas para el widget TabX.
     */
    public static function tabXArchivos($archivos)
    {
        $items = [];
        foreach ($archivos as $archivo) {
            $items[] = self::tabXOption($archivo->gestor->nombre, Url::to($archivo->link));
        }
        return $items;
    }

    /**
     * Crea un array con el contenido necesario para añadirselo a el widget TabX.
     * @param $label     string Titulo de la pestaña.
     * @param $contenido string Contenido de la pestaña.
     * @return           array  Pestaña del widget TabX.
     */
    public static function tabXOption($label, $contenido)
    {
        return [
            'label' => $label,
            'content' => $contenido,
        ];
    }

    /**
     * Organiza los participantes en un array de forma que la key es el nombre del rol y el valor es un array con los
     * nombres de las personas con ese rol.
     * @param $participantes    array Array de Participantes.
     * @return                  array Participantes listos para mostrarlos en forma de lista.
     */
    public static function fixParticipantes($participantes)
    {
        $items = [];
        foreach ($participantes as $participante) {
            $items[$participante->rol->rol][] = $participante->persona->nombre;
        }
        return $items;
    }


    /**
     * Lista de tipos con los valores que no tienen ningun padre del que heredar.
     * @return array
     */
    public static function listaTiposSearch()
    {
        return Tipos::find()
            ->select('tipo')
            ->where(['padre_id' => null])
            ->indexBy('id')
            ->column();
    }

    /**
     * Lista de tipos completa.
     * @return array
     */
    public static function listaTipos()
    {
        return Tipos::find()
            ->select('tipo')
            ->indexBy('id')
            ->column();
    }

    /**
     * Lista de posibles participantes.
     * @return array
     */
    public static function listaPersonas()
    {
        return Personas::find()
            ->select('nombre')
            ->indexBy('id')
            ->column();
    }

    /**
     * Lista de los roles para los posibles participantes.
     * @return array
     */
    public static function listaRoles()
    {
        return Roles::find()
            ->select('rol')
            ->indexBy('id')
            ->column();
    }

    /**
     * Lista de padres directos a un tipo de show.
     * @return array
     */
    public static function listaPadres($id)
    {
        return Shows::find()
            ->select('titulo')
            ->where(['tipo_id' => $id])
            ->indexBy('id')
            ->column();
    }

    /**
     * Lista completa de generos.
     * @return array
     */
    public static function listaGeneros()
    {
        return Generos::find()
            ->select('genero')
            ->indexBy('id')
            ->column();
    }

    /**
     * Lista de gestores de subida.
     * @return array
     */
    public static function listaGestores()
    {
        return GestoresArchivos::find()
            ->select('nombre')
            ->indexBy('id')
            ->column();
    }

    /**
     * Pinta los comentarios anidados.
     * @param $comentarios
     * @param $vista
     * @param $comentarioVacio
     * @param int $level
     * @return string
     */
    public static function formatComentarios($comentarios, $vista, $comentarioVacio, $level = 0)
    {
        $str = '';
        if ($comentarios) {
            $str .= '
                <div class="col-md-offset-' . ($level == 0 ? 0 : 1) . ' col-md-' . ($level == 0 ? 12 : 11) . ' comentario-margin">';
            foreach ($comentarios as $comentario) {
                $comentarioVacio->padre_id = $comentario->id;
                $str .= $vista->render('../comentarios/view', [
                    'model' => $comentario,
                    'comentarioHijo' => $comentarioVacio,
                ]);

                $str .= self::formatComentarios($comentario->comentarios, $vista, $comentarioVacio, ++$level);
            }
            $str .= '</div>';
        }

        return $str;
    }
}
