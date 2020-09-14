# Render a template

Once all our backend operations are executed. We will need to return a response from server.

We are assuming that you already set the following route inside the `routing.xml` file:

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

As we said before to render the results we need to return a response. 
That response can take several forms: Json, Xml, html, pdf, csv etc..
For that Delos will need to have an object that implements the **Delos\Response\ResponseInterface** in order to be able to accomplish that task.


#Twig

`ControlUtils` can handle for us the burden of preparing the twig object to render our template:

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

if you look inside the `$this->utils->render` you will notice that we are casting a twig object that will allow
us to use the twig library. What you can do inside that twig template is another story you can find documentation in here: 
[twig documentation](https://twig.symfony.com/doc/1.x/) in our demonstration we will stick to a simple example

We should have to create a template inside `lib/views` with the name of `newTemplate.html.twig`
and fill it with the following content:

```
This is the template of {{ page }}
```

Fantastic if you look now at the page at `!_admin/my-page` you will see `This is the template of`. 
Note that the variable `page` in twig is not rendered you need to pass the variable to the response object, 
at the bottom in our `myMethod()` method:

````
    public function myMethod()
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

Classics aside from rendering a variable, `{{ variable }}` instead of `$variable` are 
foreach loops: 

````
{% for e in element %}

  {{ renderThisVar }}
 
{% endfor %}
````
 
 and for loops
 
````
{% for i in 0..10 %}

   {{ renderThisVar }}
   
{% endfor %}
````


