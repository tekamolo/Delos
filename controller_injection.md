# Controllers
**Nomenclature**: The nomenclature of the **Controller name** will vary depending upon your needs.
 A very complicated page can have one controller and several simplistic pages can be handled in only one,
 use your own assessment to determine which nomenclature suits you best.
 
Controllers are a very important part of the framework since they also include another concept we review in this section:
 the injections of objects and container.
 
The injection concept refers to the strategy of injecting in a object (A) other objects it needs (B and C).
All of this without having to instantiate ourselves the classes in question. 
This allows us to have control of what is already instantiated avoiding to instantiate again the same object 
and allowing the developer to write way less code in the process.

For managing objects that are already instantiated we will use a `Container` that manages all the instances
that already exist. Concretely in the code the container acts more like manager and all the instances are stored in
a `Collection` class that acts as an array();

If for example we need something that has to be instantiated 
the `Injector` class will be in charge of instantiating it.
once instantiated it will store it in the previous `Collection`.

Injections are mostly done via constructors except in controllers where we can inject via the constructor as well as the method themselves.


#Injecting ControllerUtils

If you are coming from the [Routing](routing.md) section You will need to complete the following in the controller
to get the page redirected:

´´´´

    namespace Delos\Controller\Admin;

    use Delos\Controller\ControllerUtils;

    class MySection
    {
        /**
         * @var ControllerUtils
         */
        public $utils;
    
        /**
         * Controller_Admin_Sites_Sites constructor.
         * @param ControllerUtils $utils
         */
        public function __construct(ControllerUtils $utils)
        {
            $this->utils = $utils;
        }
    
        /**
         * @return void
         */
        public function myMethodAction()
        {
            $router = $this->utils->getRouter(); //This will return the RouterXml object
            $router->redirect("my-arriving-point");
        }
    }

What the ControllerUtils is? is the glue between the Container and the Controllers. 
We need it to access render functionalities such as twig, router, request, redirections and get services.

* What happened previously? How the container and the injector did manage to inject the object `ControllerUtils` into the controller?

1) What controller needs is red in the container and via a reflection class.

2) Once we know what the needs are we use the injector to instantiate those objects.
 
3) And finally we can instantiate the controller with all the prior objects that will be injected.


 
#### Methods available in utils
For the time being the available methods in `ControllerUtils` are:

* `getRouter()` returns the \RouterXml object

* `getRequest()` returns the \Http_Request object

* `getTwig()` returns the \Twig_Environment in the case you want to inject additional objects most of the time you won't

* `getService()` returns any object the main injector is able to instantiate

* `render()` returns the ReponseInterface that force the process() implementation that will trigger a display for twig templates

* `renderJson()` returns the ReponseInterface as previously, but handling arrays to be displayed as json



#### What objects can be injected
The injector can handle only simple objects that needs no additional handling, in other words that do not need any specific parameter in its constructor.

Otherwise the injector for now handles:

* **Repositories**

The repositories are used in our system to interact with the database.
To easier things I have decided to this version displayed on Github to use Eloquent, the library Laravel is using 
to interact to the database. I created another layer logic `repositories` which we will help us to inject that layer into services and controllers.

Now that we indicated that we will be able to use that model directly a Controller:

```    
    public function mainMethod(UserRepository $repository){

        $users = $repository->getAll();

        return $this->utils->render("/main/index.html.twig",
            array("users"=> $users)
        );
    }
```
And that's it.
Most of the time repos will be injected into services if you want to test the business logic of a page (keep reading the following section)

* **Services**

The objective of dividing business logic into services is reusability as well as consistency of the logic 
adopted in one place in order to reuse it in another. Also those blocks will be able to be tested
via integration tests. please read [services](services.md) for more information.

* **ControllerUtils**

This object is kind of special because it represents the glue between the Container and the Controllers. 
To work it needs the container to be injected within itself. We already explain above the method it contains.
We showed it could be injected into the constructor but also we can inject it in the method avoiding all the routes to 
have that object in the case it is not needed.

* **Delos\Request\Request** 

This object handles the `$_REQUEST` variables. This is instantiated inside itself.
You can ask for it from the constructor or controller method, or service constructor.

* **Delos\Security\Access** 

This object can be used to filter templates as well as logic in the Services.

* **Delos\Routing\RouterXml** 

As seen in the router section the router allows us to make redirections or building
routes inside templates. see [Routing](routing.md)

* **\Validators**

Self explanatory Validators are to be injected into Services for verification purposes.


#### <h3 id="abstractions">Implementing abstractions: Interfaces and abstract classes.<h3>

To complete all the above we can pass to a property method signature an interface or an abstract method. Nonetheless we still have to 
elaborate what is the concretion (The actual method needed to be injected). This is important to implement solid principles across 
all our code. Liskov principle and Dependency inversion principle.

Example:

````
    /**
     * @param TfaServiceBase $service
     * @return \Delos\Response\ResponseInteface
     */
    public function merchantTfaAction(TfaServiceBase $service)
    {
    }
````

Here we type in the method signature an abstract base class. This in theory will work if we were to pass the
the concretion (the instance of a class implementing the abstract base class TfaServiceBase).
But because we are using dependency injection our system will not be able to tell which concrete class to use unless we told
him which one to use. Therefore we will write the following hint for the system to find out:

````
    /**
     * @param TfaServiceBase $service @concretion Delos\Service\Tfa\TfaMerchant
     * @return \Delos\Response\ResponseInteface
     */
    public function merchantTfaAction(TfaServiceBase $service)
    {
    }
````

Note we included in the first line annotation the following **@concretion Delos\Service\Tfa\TfaMerchant**
Like that the code will validate a class implementing the base TfaServiceBase and we are telling concretely
try to use TfaMerchant.

For interfaces the same logic applies. the concretion hint must be in the same line as the parameter as well
as the fully qualified name of the class.
