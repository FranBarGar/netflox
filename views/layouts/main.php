<?php

/* @var $this \yii\web\View */

/* @var $content string */

use app\models\Usuarios;
use app\widgets\Alert;
use yii\helpers\Html;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\helpers\Url;
use yii\widgets\Breadcrumbs;
use app\assets\AppAsset;

AppAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php $this->registerCsrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
</head>
<body>
<?php $this->beginBody() ?>

<div class="wrap">
    <?php
    NavBar::begin([
        'brandLabel' => Yii::$app->name,
        'brandUrl' => Yii::$app->homeUrl,
        'options' => [
            'class' => 'navbar-inverse navbar-fixed-top',
        ],
    ]);


    $menu[] = [
        'label' => 'Admin',
        'items' => [
            '<li class="dropdown-header">Personas</li>',
            ['label' => 'Index', 'url' => ['/personas/index']],
            ['label' => 'Crear', 'url' => ['/personas/create']],
            '<li class="divider"></li>',
            '<li class="dropdown-header">Roles</li>',
            ['label' => 'Index', 'url' => ['/roles/index']],
            ['label' => 'Crear', 'url' => ['/roles/create']],
            '<li class="divider"></li>',
            '<li class="dropdown-header">Generos</li>',
            ['label' => 'Index', 'url' => ['/generos/index']],
            ['label' => 'Crear', 'url' => ['/generos/create']],
            '<li class="divider"></li>',
            '<li class="dropdown-header">Tipos de shows</li>',
            ['label' => 'Index', 'url' => ['/tipos/index']],
            ['label' => 'Crear', 'url' => ['/tipos/create']],
            '<li class="divider"></li>',
            '<li class="dropdown-header">Usuarios</li>',
            ['label' => 'Index', 'url' => ['/usuarios/index']],
            '<li class="divider"></li>',
            '<li class="dropdown-header">Shows</li>',
            ['label' => 'Crear', 'url' => ['/shows/create']],
        ],
    ];

    $menu = [
        ['label' => 'Shows',
            'items' => [
                [
                    'label' => 'Lo mas valorado',
                    'url' => [
                        '/shows/index',
                        'ShowsSearch[orderBy]' => 'valoracionMedia',
                        'ShowsSearch[orderType]' => 'DESC'
                    ]
                ],
                [
                    'label' => 'Lo mas comentado',
                    'url' => [
                        '/shows/index',
                        'ShowsSearch[orderBy]' => 'numComentarios',
                        'ShowsSearch[orderType]' => 'DESC',
                    ]
                ],
                [
                    'label' => 'Lo mas nuevo',
                    'url' => [
                        '/shows/index',
                        'ShowsSearch[orderBy]' => 'shows.lanzamiento',
                        'ShowsSearch[orderType]' => 'DESC'
                    ]
                ],
            ]
        ],
        ['label' => 'Peliculas',
            'items' => [
                [
                    'label' => 'Lo mas valorado',
                    'url' => [
                        '/shows/index',
                        'ShowsSearch[orderBy]' => 'valoracionMedia',
                        'ShowsSearch[orderType]' => 'DESC',
                        'ShowsSearch[tipo_id]' => '1'
                    ]
                ],
                [
                    'label' => 'Lo mas comentado',
                    'url' => [
                        '/shows/index',
                        'ShowsSearch[orderBy]' => 'numComentarios',
                        'ShowsSearch[orderType]' => 'DESC',
                        'ShowsSearch[tipo_id]' => '1'
                    ]
                ],
                [
                    'label' => 'Lo mas nuevo',
                    'url' => [
                        '/shows/index',
                        'ShowsSearch[orderBy]' => 'shows.lanzamiento',
                        'ShowsSearch[orderType]' => 'DESC',
                        'ShowsSearch[tipo_id]' => '1'
                    ]
                ],
            ]
        ],
        ['label' => 'Series',
            'items' => [
                [
                    'label' => 'Lo mas valorado',
                    'url' => [
                        '/shows/index',
                        'ShowsSearch[orderBy]' => 'valoracionMedia',
                        'ShowsSearch[orderType]' => 'DESC',
                        'ShowsSearch[tipo_id]' => '2'
                    ]
                ],
                [
                    'label' => 'Lo mas comentado',
                    'url' => [
                        '/shows/index',
                        'ShowsSearch[orderBy]' => 'numComentarios',
                        'ShowsSearch[orderType]' => 'DESC',
                        'ShowsSearch[tipo_id]' => '2'
                    ]
                ],
                [
                    'label' => 'Lo mas nuevo',
                    'url' => [
                        '/shows/index',
                        'ShowsSearch[orderBy]' => 'shows.lanzamiento',
                        'ShowsSearch[orderType]' => 'DESC',
                        'ShowsSearch[tipo_id]' => '2'
                    ]
                ],
            ]
        ],
        ['label' => 'Social',
            'items' => [
                ['label' => 'Usuarios', 'url' => ['/usuarios/index']],
                ['label' => 'Valoraciones', 'url' => ['/comentarios/index']],
            ]
        ]
    ];

    if (Yii::$app->user->isGuest) {
        $menu[] = ['label' => 'Registrarse', 'url' => ['usuarios/create']];
        $menu[] = ['label' => 'Login', 'url' => ['/site/login']];
    } else {
        if (Yii::$app->user->identity->rol == 'admin') {
            $menu[] = [
                'label' => 'Admin',
                'items' => [
                    '<li class="dropdown-header">Personas</li>',
                    ['label' => 'Index', 'url' => ['/personas/index']],
                    ['label' => 'Crear', 'url' => ['/personas/create']],
                    '<li class="divider"></li>',
                    '<li class="dropdown-header">Roles</li>',
                    ['label' => 'Index', 'url' => ['/roles/index']],
                    ['label' => 'Crear', 'url' => ['/roles/create']],
                    '<li class="divider"></li>',
                    '<li class="dropdown-header">Generos</li>',
                    ['label' => 'Index', 'url' => ['/generos/index']],
                    ['label' => 'Crear', 'url' => ['/generos/create']],
                    '<li class="divider"></li>',
                    '<li class="dropdown-header">Tipos de shows</li>',
                    ['label' => 'Index', 'url' => ['/tipos/index']],
                    ['label' => 'Crear', 'url' => ['/tipos/create']],
                    '<li class="divider"></li>',
                    '<li class="dropdown-header">Usuarios</li>',
                    ['label' => 'Index', 'url' => ['/usuarios/index-admin']],
                    '<li class="divider"></li>',
                    '<li class="dropdown-header">Shows</li>',
                    ['label' => 'Crear', 'url' => ['/shows/create']],
                ],
            ];
        }


        $menu[] = [
            'label' =>
                '<img 
                src="' . Yii::$app->user->identity->getImagenLink() . '" 
                alt="Image not found" 
                onerror="
                    this.onerror=null;
                    this.src=\'' . Usuarios::IMAGEN . '\';"
                onload="
                    this.parentNode.style=\'padding: 0\';"
                class="img-circle"
                width="40px">  
                <div style="display: inline-block; margin-top: 13px">' .
                Yii::$app->user->identity->nick .
                '</div>',
            'items' => [
                ['label' => 'Mi perfil', 'url' => ['/usuarios/my-profile', 'id' => Yii::$app->user->id]],
                '<li>'
                . Html::a('Logout', Url::to(['/site/logout']), ['data-method' => 'POST'])
                . '</li>',
            ],
        ];
    }

    echo Nav::widget([
        'options' => ['class' => 'navbar-nav navbar-right'],
        'encodeLabels' => false,
        'items' => array_merge([
            ['label' => 'Home', 'url' => ['/site/index']],
        ], $menu)
    ]);
    NavBar::end();
    ?>

    <div class="container">
        <?= Breadcrumbs::widget([
            'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
        ]) ?>
        <?= Alert::widget() ?>
        <?= $content ?>
    </div>
</div>

<footer class="footer">
    <div class="container">
        <p class="pull-left">&copy; My Company <?= date('Y') ?></p>

        <p class="pull-right"><?= Yii::powered() ?></p>
    </div>
</footer>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
