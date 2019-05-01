<?php

namespace app\models;

use Imagine\Gd\Imagine;
use Imagine\Image\Box;
use kartik\file\FileInput;
use Yii;

/**
 * This is the model class for table "shows".
 *
 * @property int $id
 * @property string $titulo
 * @property string $sinopsis
 * @property string $lanzamiento
 * @property int $duracion
 * @property int $imagen_id
 * @property string $trailer
 * @property int $tipo_id
 * @property int $show_id
 *
 * @property Comentarios[] $comentarios
 * @property Participantes[] $participantes
 * @property Archivos $imagen
 * @property Shows $show
 * @property Shows[] $shows
 * @property Tipos $tipo
 * @property ShowsDescargas[] $showsDescargas
 * @property Archivos[] $archivos
 * @property ShowsGeneros[] $showsGeneros
 * @property Generos[] $generos
 */
class Shows extends \yii\db\ActiveRecord
{
    /**
     * @var string Imagen por defecto.
     */
    const IMAGEN = 'images/default.png';

    /**
     * @var array Opciones de ordenacion disponibles.
     */
    const ORDER_BY = [
        'shows.titulo' => 'Titulo',
        'shows.lanzamiento' => 'Fecha de estreno',
        'valoracionMedia' => 'Valoración',
        'numComentarios' => 'Numero de comentarios',
    ];

    // Creacion de shows

    /** @var array */
    public $listaGeneros;

    /** @var array */
    public $listaParticipantes;

    /** @var FileInput */
    public $imgUpload;

    /** @var FileInput */
    public $showUpload;

    /** @var int */
    public $gestorId;

    //Ordenacion

    /** @var string */
    public $orderBy;

    /** @var string */
    public $orderType;

    // Atributos generados por la query de ShowsSearch.

    /** @var float */
    public $valoracionMedia;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'shows';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['titulo', 'lanzamiento', 'tipo_id'], 'required'],
            [['titulo'], 'string', 'max' => 255],
            [['lanzamiento'], 'date', 'format' => 'php:Y-m-d'],
            [['sinopsis'], 'string'],
            [['trailer'], 'url'],
            [['duracion', 'imagen_id', 'tipo_id', 'show_id'], 'integer'],
            [['duracion', 'imagen_id', 'trailer', 'tipo_id', 'show_id'], 'default', 'value' => null],
            [['listaGeneros'], 'each', 'rule' => ['integer']],
            [['listaParticipantes'], 'safe'],
            [['imgUpload'], 'image', 'extensions' => 'jpg, gif, png, jpeg'],
            [['showUpload'], 'file', 'extensions' => 'owm, mp4, flv, avi'],
            [['gestorId'], 'integer'],
            [['imagen_id'], 'exist', 'skipOnError' => true, 'targetClass' => Archivos::class, 'targetAttribute' => ['imagen_id' => 'id']],
            [['show_id'], 'exist', 'skipOnError' => true, 'targetClass' => self::class, 'targetAttribute' => ['show_id' => 'id']],
            [['tipo_id'], 'exist', 'skipOnError' => true, 'targetClass' => Tipos::class, 'targetAttribute' => ['tipo_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributes()
    {
        return parent::attributes();
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'titulo' => 'Titulo',
            'sinopsis' => 'Sinopsis',
            'lanzamiento' => 'Fecha de lanzamiento',
            'duracion' => 'Duracion',
            'imagen_id' => 'Imagen ID',
            'tipo_id' => 'Tipo de show',
            'show_id' => 'Show al que pertenece',
            'listaGeneros' => 'Generos',
            'imgUpload' => 'Imagen de portada',
            'showUpload' => 'Show a subir',
            'gestorId' => 'Gestor de archivos (AWS por defecto)',
            'trailer' => 'Enlace del trailer (Youtube, Vimeo...)',
            'orderBy' => 'Ordenar por',
            'orderType' => 'Tipo de ordenacion',
        ];
    }

    /**
     * Sube una imagen en principio a local.
     * @return bool
     */
    public function uploadImg()
    {
        if ($this->imgUpload !== null) {
            $fileName = Yii::getAlias('@uploads/' . $this->imgUpload->baseName . '.' . $this->imgUpload->extension);
            $this->imgUpload->saveAs($fileName);

            $imagine = new Imagine();
            $image = $imagine->open($fileName);
            $image->resize(new Box(200, 200))->save($fileName);

            /**
             * Guardamos la ruta del archivo en la base de datos y ponemos su id en el show a crear
             */
            $archivo = new Archivos();
            $archivo->gestor_id = 3; //TODO: cambiar el nombre del fichero
            $archivo->link = $fileName;

            if ($archivo->save()) {
                $this->imagen_id = $archivo->id;
                return true;
            }
        }

        return false;
    }

    /**
     * Sube una imagen en principio a local
     * @return bool
     */
    public function uploadShow()
    {
        if ($this->showUpload !== null) {
            $fileName = Yii::getAlias('@uploads/' . $this->showUpload->baseName . '.' . $this->showUpload->extension);
            $this->showUpload->saveAs($fileName);

            /**
             * Guardamos la ruta del archivo en la base de datos y ponemos su id en el show a crear
             */
            $archivo = new Archivos();
            $archivo->gestor_id = $this->gestorId ?: 3; //TODO: gestor a elegir y cambiar el nombre del fichero
            $archivo->link = $fileName;

            if ($archivo->save()) {
                $showsDescargas = new ShowsDescargas();
                $showsDescargas->show_id = $this->id;
                $showsDescargas->archivo_id = $archivo->id;
                return $showsDescargas->save();
            }
        }

        return false;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getComentarios()
    {
        return $this->hasMany(Comentarios::className(), ['show_id' => 'id'])->inverseOf('show');
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getParticipantes()
    {
        return $this->hasMany(Participantes::className(), ['show_id' => 'id'])->inverseOf('show');
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getImagen()
    {
        return $this->hasOne(Archivos::className(), ['id' => 'imagen_id'])->inverseOf('shows');
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getShow()
    {
        return $this->hasOne(self::className(), ['id' => 'show_id'])->inverseOf('shows');
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getShows()
    {
        return $this->hasMany(self::className(), ['show_id' => 'id'])->inverseOf('show');
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTipo()
    {
        return $this->hasOne(Tipos::className(), ['id' => 'tipo_id'])->inverseOf('shows');
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getShowsDescargas()
    {
        return $this->hasMany(ShowsDescargas::className(), ['show_id' => 'id'])->inverseOf('show');
    }

    /**
     * @return \yii\db\ActiveQuery
     * @throws \yii\base\InvalidConfigException
     */
    public function getArchivos()
    {
        return $this->hasMany(Archivos::className(), ['id' => 'archivo_id'])->viaTable('shows_descargas', ['show_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getShowsGeneros()
    {
        return $this->hasMany(ShowsGeneros::className(), ['show_id' => 'id'])->inverseOf('show');
    }

    /**
     * @return \yii\db\ActiveQuery
     * @throws \yii\base\InvalidConfigException
     */
    public function getGeneros()
    {
        return $this->hasMany(Generos::className(), ['id' => 'genero_id'])->viaTable('shows_generos', ['show_id' => 'id']);
    }

    /**
     * Busca las valoraciones del show actual.
     * @return array|\yii\db\ActiveRecord[]
     */
    public function getValoraciones()
    {
        return Comentarios::find()
            ->andWhere(['not', ['valoracion' => null]])
            ->andWhere(['show_id' => $this->id])
            ->orderBy('created_at')
            ->all();
    }

    /**
     * Devuelve el enlace a la imagen de portada, en caso de no tener devuelve la imagen por defecto.
     * @return string
     */
    public function getImagenLink()
    {
        if ($this->imagen_id !== null) {
            return $this->imagen->link;
        }

        return self::IMAGEN;
    }

    /**
     * @return Generos[]|bool
     */
    public function tieneGeneros()
    {
        $generos = $this->generos;
        if (!empty($generos)) {
            return $generos;
        } elseif ($this->show_id !== null) {
            return $this->show->tieneGeneros();
        }
        return false;
    }

    /**
     * @return Generos[]|bool
     */
    public function getPadreGeneros()
    {
        return $this->show->tieneGeneros();
    }

    /**
     * @param int $id
     * @return \yii\db\ActiveQuery
     */
    public static function findChildrens($id)
    {
        return self::find()
            ->joinWith(['comentarios'])
            ->andWhere(['shows.show_id' => $id])
            ->orderBy('lanzamiento');
    }
}
