# Delos Introduction
**What is Delos?**

Delos is a framework aiming to answer a clients need.
Since it is not possible to use a wellknown frameworks such as `Symfony` or `Laravel`, and because it had to coexist with the old system,
 we had to create one in order to accelerate the renewal of the coding structure.
 
The implementation of `Delos` >had helped us to implement common programming structure and concepts such as:

* **MVC** - The separation between backend logic and templates was long ago due. 
We decided to use twig as our rendering template engine. 
Be aware that most templates are still in php if not integrated to Delos.
* **Containers and automatic instantiations** - Having an instantiation manager will allow us to avoid to reinstantiate already alive objects. 
It is not uncommon in old templates to have several database connections open...
* **Services** - Breaking down services into code blocks will help us to test them via integration tests.
This is mainly useful for the business logic.
* **Reusability** - an important one. The actual system not only does not support reusability but runs away from it. 
For example we have a page with raw methods and queries. A copy is made in another folder to act as a separate copy.. 
using a framework will help to unify all the pages.
* **Maintenance** - having a centered framework is suited for maintenance and debugging.
* **Url rewriting** - instead of having `affiliates.php` we will be able to read in the url bar: `/affiliates/`
 This is suited for **SEO optimization**.

**Quick Guide:**
* [How to create a page](quick_start.md)

**In detail:**
* [Routing](routing.md)
* [Controller and injection](controller_injection.md)
* [Abstractions and concretions](controller_injection.md#abstractions)
* [Services](services.md)
* [Rendering](render.md)
* [Components](components.md)

