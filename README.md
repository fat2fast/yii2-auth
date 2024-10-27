<p align="center">
    <h1 align="center">Fat Too Fast Yii2 Auth</h1>
    <br>
</p>

### Fork from <a href="https://git.lcsa.vn/packages/yii2-auth">Lsat Yii2 Auth</a>

### How to install

```
docker-compose exec api composer require fat2fast/yii2-auth
```
```
docker-compose exec api yii migrate-module
```

### Config composer.json

#### Config local to development , not push change composer.json to git 

```
 "require" : {
    "fat2fast/yii2-auth": "dev-master"
  }
```
```
{
    "type": "path",
    "url": "modules/yii2-auth",
    "options": {}
},
```



### Add configuration console.php trong main project
```
    'controllerMap' => [
        
        // other controllers
    
        'migrate-module' => [
            'class' => 'yii\console\controllers\MigrateController',
            'migrationPath' => [
                '@fat2fast/auth/migrations'
            ],
            'migrationTable' => 'migration_module'
        ],
    ]
```

### Add to web.php của main project

```
    'modules' => [
        // other modules
          'auth' => [
            'class' => 'fat2fast\auth\Module',
            'userIdentityClass' => 'fat2fast\auth\models\user\User',
        ]
    ],
```

### How to migrate
```
docker-compose exec api yii migrate-module
```
### How to create migrate
```
php yii migrate/create ModuleMigrationName --migrationPath=@fat2fast/auth/migrations
```
config `params.php`
```
authz.fullNameOrganization
authz.acronymOrganization
authz.TitleLogin
```
