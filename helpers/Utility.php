<?php

namespace app\helpers;

use app\models\Archivos;
use Yii;
use yii\helpers\Url;

/**
 * Clase Utility.
 */
class Utility
{
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

    public static function tabXOption($label, $contenido)
    {
        return [
            'label' => $label,
            'content' => $contenido,
        ];
    }

    public static function tabXArchivos($archivos)
    {
        $items = [];
        foreach ($archivos as $archivo) {
            $items[] = self::tabXOption($archivo->gestor->nombre, Url::to($archivo->link));
        }
        return $items;
    }

    public static function fixParticipantes($participantes)
    {
        $items = [];
        foreach ($participantes as $participante) {

        }
        return $items;
    }
}
