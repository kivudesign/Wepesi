# Wepesi

* Wepesi library
  `wepesi` is the quick ans simple framework the help you devellop simple web application with php and design OOP
  concept,
  it has been design by following most off principle for big framework but make it simple for develloper.

## Installation

The installation of the application does not require somuch thing;

- In you are familliar with `composer` to install :
  You can find project directly on packagest on : https://packagist.org/packages/wepesi/wepesi

```shell
 composer create-project wepesi/wepesi
```

and it will create a project for You.

- In case you are not familliar with `composer` you can donwload it directly the source code on github
  on :https://github.com/kivudesign/Wepesi, no nee of extra module to start workin on the project.

# Intoduction

Wepesi is a simple Web framework that help you devellop simple web application, and benefit advantage of large php
framework like

- routing
- controller
- simple ORM *without migration
- MVC design patern
- OOP
- Middleware
- Validation
- View

All module are built-id, its has been design to give to make the framework flexible, you can restructure everything as
you want and be able to add more module.

# Integration

no need to know about composer, the simple way is to download the all the project and place on the server side
devellopenent.
decompress the file, and folder the instruction.

- if you are using `wamp`, place the decopress folder on the `www` folder of `wamp` folder with is on the root of the
  dick c: on windows
- if you are using `xamp`, you place the folder on the `htdocs`
  you can rename the folder as you want according to you need

# Structure

the library is subdivised in multiple part with folder, we have:

- `class` : where all model and where we can find the core logic to run the libray.
  there is a folder call `app`, in with you are not allowed to modified any class if you dont know what you are doing.
  in some case, it will have an impact on the way your application is working.
- `controller`: where you can creat all the controller. the system has been designed like that to make a difference
  between controller and model.
- `config`: where you can config the database configuration, and the autoloading.
- `layout`: the layout help manage all `css style` or `javascript script`, or not. the idea, is to have a better
  logical. in case you use the layout,
  there is a way yu can accee those file by using wepesi class `Bundle`.
- `route`: is where you can define all your route
- `views`: here is where you will create all the pages that will display by the user.
- `index.php`: this is the main file. to start the app,
- `.htaccess`: this help to manage the routing.

# *Hope you enjoy.
