# Delos Introduction
If you are reading this you may have worked a little bit with the pbiz project by now. This means you know the weaknesses of the system when it comes to reusability and programming standards implementation.

Despite countless efforts to improve the code we still face the following difficulties to overcome:
* **Poor reusability**. Raw methods and non programming-oriented code is used in old templates, and therefore there is a high probability of inconsistency.
* **Database inconsistencies** and **lack of standards**, the `sites` table is a good example of poor choice of database structure. As I write this we count about 321 columns in this table.
* All the **logic is mixed** in those admin php files which makes it impossible to suppress or move the data structure right now. 
We can only add columns and tables but it is out of question to modify the database until the framework is widely implemented in most critical sections.

Hopefully prior teams were working on building models which are in charge of making the queries, and DAOs which represent the database structure. 
Despite having their names switched (yeah dao should be models and the models should be DAOs). 
They are a huge help in implementing common programming standard such as filter, abstraction and the implementation of `Delos`.


**What is Delos?**

Delos is a framework aiming to answer the needs of the pbiz project. 
Since it is not possible to use wellknown frameworks such as `Symfony` or `Laravel`, and because it has to coexist with the old system,
 we had to create one in order to accelerate the renewal of the coding structure.
The implementation of `Delos`will help us to implement common programming structure and concepts such as:

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
* The implementation of this system will eventually help in the future **to migrate** to a more established framework or improving this one. Also debugging will be easier. An important aspects also is that the system being more compact and centralized will allow to update the code to a superior version of PHP like PHP 7.0 and above.
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

