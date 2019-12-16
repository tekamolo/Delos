# Quick start

How do you create a page under Delos?

* **The routing file**. You will be able to spot a `routing.xml` in the `lib/framework` folder. 
Here we will indicate to Delos of the route we need. The route must be unique:

```
<?xml version="1.0" encoding="UTF-8"?>
<routes>
    <route alias="my-page">
        <url>my-page</url>
        <controller>Admin\mySection:myMethod</controller>
        <access>MANAGEMENT</access>
    </route>
</routes>
```
This will allow the system to recognize the url `!_admin/my-page` and the tag controller will search for the class 
controller: `Delos\Controller\Admin\mySection` and within that class the following method will be trigerred:
 `myMethodAction()` (yeah note we appended the Action word in the code, and `Delos\Controller\` for the class)

Therefore we need to create that controller inside the folder: `lib/framework/Controller/Admin`
Important: make sure that your file has the following suffix `.class.php`


````
namespace Delos\Controller\Admin;

class MySection
{
    public function myMethodAction()
    {
        die("This is my hello world page!");
    }
}
````

And if you visit the url in question `!_admin/my-page`  you should be able to see in the browser `This is my hello world page!`.

But Delos will need to render a php template or rather a `twig` template OR a json response format in the case of an `API` response.

The templates for now are stored in `lib/views`. So here you will be able to create your template. For example go ahead a create the template: `newTemplate.html.twig`
and complete that document by writing the following

````
This is the template of {{ page }}
````

Since we need to render the page as a twig file, we are forcing the controller to return an object that implements the `Delos\Response\ResponseInterface`.
To do that we created a class `Delos\ControllerControllerUtils` that has that utility. To use it complete the controller as following:

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
         * @return \Delos\Response\Response
         * @throws \Twig_Error_Loader
         * @throws \Twig_Error_Runtime
         * @throws \Twig_Error_Syntax
         */
        public function myMethodAction()
        {
            return $this->utils->render("newTemplate.html.twig",array());
        }
    }

Fantastic if you look now at the page you will see `This is the template of`. 
Note that the variable page in twig is not rendered you need to pass the variable to the response object, 
at the bottom in our `myMethodAction()` method:

````
    public function myMethodAction()
    {
        return $this->utils->render("newTemplate.html.twig",
            array("page"=>"an awesome page")
        );
    }
````

And if you rerender the page you will be able to see:

````
This is the template of an awesome page
```` 
****
For more documentation about the twig engine please here [twig documentation](https://twig.symfony.com/doc/2.x/)