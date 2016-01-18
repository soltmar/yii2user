##NOTE: This is Yii2-User module, which is rewritten version of Yii-User which can be found on [http://www.yiiframework.com/extension/yii-user](http://www.yiiframework.com/extension/yii-user)


This module allows you to maintain users.

Yii-User Installation
=====================

Download
--------

Download or checkout (SVN/Git) from https://github.com/marsoltys/yii2user and unpack files in your protected/modules/user

Composer
---------

    composer require "marsoltys/yii2user:*",

Configure
---------
```
Change your config web.php:
        #...
        'modules'=>[
            #...
            'user' => [
                'class' => 'marsoltys\yii2user\Module',
                
                # OPTIONALLY YOU CAN CONFIGURE THESE PROPERTIES
                #'userClass' => 'marsoltys\yii2user\components\User', # User component class, you can change this to be your own class but it must be extended from class provided in this comment
                #'identityClass' => 'marsoltys\yii2user\models\User', # User Identity class, you can change this to be your own class but it must be extended from class provided in this comment
                #'userModel' => 'marsoltys\yii2user\models\User',     # User model class, you can change this to be your own class but it must be extended from class provided in this comment
            ],
            #...
        ],
```    
Change your config console:
```
    return array(
        #...
        'modules'=>[
            #...
            
            'user' => [
                'class' => 'mariusz_soltys\yii2user\Module'
            ],
            
            #...
        ]
        #...
    ); 
```

Install
------- 

1. Run console command:

        php yii migrate --migrationPath=@marsoltys/yii2user/migrations

2. Provide admin login, email and password in console when prompted.

##NOTE##

Please note that "user" component will be configured through this User module.
To configure user composer classes please comments in web.php config file of "Configure" section above

##TODO##

- Fix "Autocomplete" and "Belongsto" components
- Refresh user session if anything changes within user details (logout user after pass change?)
- add activation email option when user is created by admin and status is not active

##CHANGELOG##

###1.0.0.2###
- Added bootstrapping through composer