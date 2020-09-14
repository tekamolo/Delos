# Routing
The routing functionality adds several advantages in our system:
* **Routing resources** - It help us to determine which resource will be called and therefore which response is going
to be served. In addition, we will be able to correct any name mistake rather quickly.
* **Url rewriting** - Having our url rewrite can make our system more suitable for SEO.
For example robots read `/affiliate/fr/` but do not acknowledge `affiliate_fr.php`.
* **Access, Firewalls and filters** - The router will allow us to use an `Access` checker object in charge to check if a user in certain
section/department has access to the resource demanded.
* **Dynamic route building** - with the router we will be able to generate url dynamically. 
If a url is not correct, or the destination changes it won't matter since our route alias remain the same.
We won't have to wonder in every template if our redirection and links still work.

# The routing.xml file
* The routing file. As the time I write this, we are only using one routing file: 
You will be able to spot it in `Delos/framework` folder with the name `routing.xml`. 
Here we will indicate to Delos of the route we need. The route/alias must be unique as well as the url:

```
<?xml version="1.0" encoding="UTF-8"?>
<routes namespaceBaseController="Namespace\Project\">
    <route alias="main-template">
        <url lang="en">/</url>
        <url lang="es">/es/principal/</url>
        <url lang="fr">/fr/principal/</url>
        <controller>Main\MainController:mainMethod</controller>
        <access>USER</access>
    </route>
</routes>
```

* The `alias` attribute and `url` tag act as identifiers of the page, in here they are equal but keep in mind they can be different.
The alias is generally used for redirects and for generating urls in the templates. The advantage is that if we change
the url, we are still using the alias to generate the urls and nothing will change from a functioning point of view.

* The `controller` tag is meant to indicate which controller and method the route should be triggered. 
In here we have `Main\MainController:mainMethod`. This will launch `Main\MainController` and the method `mainMethod()`.

* The `access` tag indicates which user level has access to the resource. In here we are granting the access to the common user.
This means common users will have access to the resource we are requesting.

Here are the list of Accesses `USER` `ADMIN`.
Check the object `Delos\Security\Access` if you want to add or modify user profiles.

To combine them in one resource separate every user profile with a pipe sign `|`. For example: `<access>USER|ADMIN</access>`.

You can go ahead and create the previous Controller `Main\MainController`

````
namespace Delos\Controller;

class MySection
{
    public function mainMethod()
    {
        die("This is my hello world page!");
    }
}
````

And if you visit the url in question `localhost/Delos/public/` , (or `localhost/` if you set a virtual host)  you should be able to see in the browser `This is my hello world page!`.

# Routing in the twig templates
Within the twig templates, the router is already injected, therefore in templates you can use:
`{{ router.getUrl('my-page') }}` targeting the `alias` not the url !

# Routing in the code
Generally speaking the in the backend the only case we need an url is when we are redirecting. 
You can use the Router object as follows:
```
$router = $this->utils->getRouter(); //This will return the RouterXml object
$router->redirect("my-arriving-point");
```

Note: no need to kill the script the redirect method already has a `die()`;

IMPORTANT: here you will need the Controller Utils object. Read the injection section to know how to get it. [[Controller and dependency injection]](../documentation/controller_injection.md)

# Parameters in routes
* **Via Get params**

Please note that we are able to pass parameters in the old fashioned way via `$_GET` parameters and therefore
urls such as `\my-page\?elementid=1&country=23` will work.

To catch those params in the controller you will need to get access to the `Request` object that manages the request.
To get that object look into the controller and injection section:
 [[Controller and injection]](../documentation/controller_injection.md)

```
$element = $request->get->get("elementid",\VarFilter::INT);
var_dump($element);
die();
```

This will display 
```
int 1
```

Also post method is also handle by such object in similar manner:
```
$element = $request->post->get("elementid",\VarFilter::INT);
var_dump($element);
die();
```

Note that the `\VarFilter::INT` will convert the parameter into a int no matter what. There is also a filter for strings.

* **Via params in the url**

The router also can handle parameters in the url but at this stage this part of the Router object is a bit limited:
urls such as /my-page/1/23/ can be handled. in the controller:

```
var_dump($routerXml->getParams());
die();
```

will output

```
array (size=2)
  0 => string '1' (length=1)
  1 => string '23' (length=2)
```