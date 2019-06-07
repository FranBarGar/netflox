<?php

namespace app\models;

use app\helpers\Utility;
use Imagine\Gd\Imagine;
use Imagine\Image\Box;
use kartik\file\FileInput;
use Yii;
use yii\web\UploadedFile;

/**
 * This is the model class for table "shows".
 *
 * @property int $id
 * @property string $titulo
 * @property string $sinopsis
 * @property string $lanzamiento
 * @property int $duracion
 * @property string $trailer
 * @property string $imagen
 * @property int $tipo_id
 * @property int $show_id
 *
 * @property Archivos[] $archivos
 * @property Comentarios[] $comentarios
 * @property Participantes[] $participantes
 * @property Shows $show
 * @property Shows[] $shows
 * @property Tipos $tipo
 * @property ShowsGeneros[] $showsGeneros
 * @property Generos[] $generos
 * @property UsuariosShows[] $usuariosShows
 */
class Shows extends \yii\db\ActiveRecord
{
    /**
     * @var string Imagen por defecto.
     */
    const IMAGEN = '@images/default.png';

    /**
     * @var array Opciones de ordenacion disponibles.
     */
    const ORDER_BY = [
        'shows.titulo' => 'Titulo',
        'shows.lanzamiento' => 'Fecha de estreno',
        'valoracionMedia' => 'Valoración',
        'numComentarios' => 'Numero de valoraciones',
    ];

    /**
     * Caso de creacion de show.
     * @var string
     */
    const SCENARIO_CREATE = 'create';

    // Creacion de shows

    /**
     * Lista de generos a añadir al show tras ser creado.
     * @var array
     */
    public $listaGeneros;

    /**
     * Lista de participantes a añadir al show tras ser creado.
     * @var array
     */
    public $listaParticipantes;

    /**
     * Fichero a subir a la nube como portada.
     * @var FileInput
     */
    public $imgUpload;

    /**
     * Fichero a subir a la nube como contenido descargable.
     * @var FileInput
     */
    public $showUpload;

    /**
     * Descripcion del show a subir.
     * @var string
     */
    public $descripcion;

    // Atributos generados por la query de ShowsSearch.

    /**
     * @var float Valoracion media de el show actual.
     */
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
            [['titulo', 'sinopsis', 'trailer', 'descripcion'], 'trim'],
            [['titulo', 'descripcion'], 'string', 'max' => 255],
            [['lanzamiento'], 'date', 'format' => 'php:Y-m-d'],
            [['trailer'], 'url'],
            [['duracion'], 'integer', 'min' => -32767, 'max' => 32767],
            [['duracion', 'tipo_id', 'show_id'], 'integer'],
            [['trailer', 'show_id'], 'default', 'value' => null],
            [['listaGeneros'], 'each', 'rule' => ['integer']],
            [['listaParticipantes'], 'safe', 'on' => self::SCENARIO_CREATE],
            [['imgUpload'], 'image', 'extensions' => 'jpg, gif, png, jpeg'],
            [['showUpload'], 'file', 'extensions' => 'owm, mp4, flv, avi'],
            [['show_id'], 'required', 'isEmpty' => function ($value) {
                $tipo = Tipos::find()
                    ->andFilterWhere(['id' => $this->tipo_id])
                    ->andWhere(['not', ['padre_id' => null]])
                    ->one();
                if ($tipo !== null) {
                    $padres = Shows::find()
                        ->select('id')
                        ->andFilterWhere([
                            'tipo_id' => $tipo->padre_id
                        ])
                        ->column();
                    if (!empty($padres) && !in_array($this->show_id, $padres)) {
                        return true;
                    }
                }
                return false;
            }],
            [['tipo_id'], 'exist', 'skipOnError' => true, 'targetClass' => Tipos::class, 'targetAttribute' => ['tipo_id' => 'id']],
            [['show_id'], 'exist', 'skipOnError' => true, 'targetClass' => self::class, 'targetAttribute' => ['show_id' => 'id']],
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
            'Imagen' => 'Imagen',
            'tipo_id' => 'Tipo de show',
            'show_id' => 'Show al que pertenece',
            'listaGeneros' => 'Generos',
            'imgUpload' => 'Imagen de portada',
            'showUpload' => 'Show a subir',
            'trailer' => 'Enlace del trailer (Youtube, Vimeo...)',
            'orderBy' => 'Ordenar por',
            'orderType' => 'Tipo de ordenacion',
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function beforeSave($insert)
    {
        if (!parent::beforeSave($insert)) {
            return false;
        }

        $this->uploadImg();

        return true;
    }

    /**
     * Sube una imagen a AWS S3.
     */
    public function uploadImg()
    {
        $this->imgUpload = UploadedFile::getInstance($this, 'imgUpload');
        if ($this->imgUpload !== null) {
            $this->imagen = Utility::upload($this->imgUpload, uniqid($this->titulo), 'netflox-shows-images', $this->imagen);
            $this->imgUpload = null;
        }
    }

    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);

        /**
         * Añadimos generos al show actual.
         */
        Utility::massiveSaveGeneros($this->listaGeneros, $this->id);

        if ($this->scenario == self::SCENARIO_CREATE) {
            /**
             * Añadimos los participantes
             */
            $this->listaParticipantes = json_decode($this->listaParticipantes);
            Utility::massiveSaveParticipantes($this->listaParticipantes, $this->id);
        }

        /**
         * Añadimos los links de descarga al show.
         */
        $this->uploadShow();
    }

    /**
     * Sube un show.
     */
    public function uploadShow()
    {
        $this->showUpload = UploadedFile::getInstance($this, 'showUpload');
        if ($this->showUpload !== null) {
            $archivo = new Archivos();
            $archivo->link = Utility::upload(
                $this->showUpload,
                uniqid($this->id),
                'netflox-shows-content'
            );
            $archivo->descripcion = $this->descripcion ?: $this->id . '-' . $this->getFullTittle();
            $archivo->show_id = $this->id;

            $archivo->save();

            $this->showUpload = null;
        }
    }

    /**
     * Devuelve el nombre completo del show.
     * @param string $str
     * @return string
     */
    public function getFullTittle($str = '')
    {
        $str = $this->titulo . (empty($str) ? '' : (', ' . $str));

        if ($this->show != null) {
            return $this->show->getFullTittle($str);
        }

        return $str;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getArchivos()
    {
        return $this->hasMany(Archivos::className(), ['show_id' => 'id'])->inverseOf('show');
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
    public function getShow()
    {
        return $this->hasOne(Shows::className(), ['id' => 'show_id'])->inverseOf('shows');
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getShows()
    {
        return $this->hasMany(Shows::className(), ['show_id' => 'id'])->inverseOf('show');
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
     * @return \yii\db\ActiveQuery
     */
    public function getUsuariosShows()
    {
        return $this->hasMany(UsuariosShows::className(), ['show_id' => 'id'])->inverseOf('show');
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
        if ($this->imagen !== null) {
            try {
                $img = Utility::s3Download($this->imagen, 'netflox-shows-images');
                $path = Yii::getAlias('@cover/' . $this->imagen);
                file_put_contents($path, $img['Body']);
                return $path;
            } catch (\Exception $exception) {
            }
        }

        return Yii::getAlias(self::IMAGEN);
    }

    /**
     * Metodo para buscar los shows que tienen como padre id el del modelo actual.
     * @return \yii\db\ActiveQuery
     */
    public function findChildrens()
    {
        return self::find()
            ->andWhere(['shows.show_id' => $this->id])
            ->orderBy('lanzamiento');
    }

    /**
     * @return Generos[]|bool
     */
    public function obtenerGeneros()
    {
        $generos = $this->generos;
        if (!empty($generos)) {
            return $generos;
        } elseif ($this->show_id !== null) {
            return $this->show->obtenerGeneros();
        }
        return false;
    }
}
