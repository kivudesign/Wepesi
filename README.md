# Wepesi Quick
* Wepesi library
`wepesi quick` is the quick version of wepesi that doesn not required composer.
all fonctionnality available on `Wepesi library` are available. there is nothing different apart from that, with `wepsi Quick` you are not able to benefit all advantage of the namespace available on `Wepesi library`.
*whith the simple donwn on you can start using it without wasting your time.* 
This a simple php library that will help design an mvc application and work in OOP.
you can find project directly on : https://packagist.org/packages/wepesi/

# Intoduction
Wepesi is a simple Web libray that help you devellop simple web application, and benefit advantage of php framework like
- routing
- controller
- simple ORM *without migration
- MVC design patern
- OOP

all required is just the basic on php, and php class and you are done.

# Integration
no need to know about composer, the simple way is to download the all the project and place on the server side devellopenent.
decompress the file, and folder the instruction.
- if you are using `wamp`, place the decopress folder on the `www` folder of `wamp` folder with is on the root of the dick c: on windows
- if you are using `xamp`, you place the folder on the `htdocs`
you can rename the folder as you want according to you need

# Structure
the library is subdivised in multiple part with folder, we have:
- `class` : where all model and where we can find the core logic to run the libray. 
          there is a folder call `app`, in with you are not allowed to modified any class if you dont know what you are doing.
          in some case, it will have an impact on the way your application is working.
- `controller`: where you can creat all the controller. the system has been designed like that to make a difference between controller and model.
- `config`: where you can config the database configuration, and the autoloading.
- `layout`: the layout help manage all `css style` or `javascript script`, or not. the idea, is to have a better logical. in case you use the layout, 
          there is a way yu can accee those file by using wepesi class `Bundle`.
- `route`: is where you can define all your route
- `views`: here is where you will create all the pages that will display by the user.
- `index.php`: this is the main file. to start the app,
- `.htaccess`: this help to manage the routing.

# *Hope you enjoy.
