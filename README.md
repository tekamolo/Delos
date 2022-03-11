# Delos Introduction
**What is Delos?**

Delos is a framework aiming to answer a clients need. Since it is not possible to use a wellknown frameworks such
as `Symfony` or `Laravel` in old systems (php 5.3 and above in few cases), I had to create one in order to accelerate
the transformation from a legacy code into a more modern design. However I am working to provide a structure supporting
php 7.1 and above and php 8.1 and above.

The implementation of `Delos` had helped us to implement common programming structure and concepts such as:

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

**Installation**

`composer create-project delos/framework`

This demos is set to work with a user table.
Run the following query in your sql:

```
CREATE TABLE `users` (
 `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
 `username` varchar(100) DEFAULT NULL,
 `email` varchar(200) DEFAULT NULL,
 `password` varchar(200) DEFAULT NULL,
 `created_at` timestamp NULL DEFAULT NULL,
 `updated_at` timestamp NULL DEFAULT NULL,
 `deleted_at` timestamp NULL DEFAULT NULL,
 PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
```

After creating the table do not forget to set the `.env`. In this latest you will have to set the database
credentials. You have an example in the `.env.dist` file.

```
DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=databasename
DB_USERNAME=root
DB_PASSWORD=
```

Run your local server and open the project into the `http://localhost/framework/public` folder. This one will list all the users you have in that table.
You can create a new user by accessing the url `http://localhost/framework/public/user-creation` this link will create a new user.
you can back to the previous page (`http://localhost/framework/public`) to see it listed.

If you come to set a virtual host point the server to `path/framework/public/`. and you will be able to access to prior pages as following:
`http://localhost` and `http://localhost/user-creation`.


**Quick Guide:**
* [How to create a page](documentation/quick_start.md)

**In detail:**
* [Routing](documentation/routing.md)
* [Controller and dependency injection](documentation/controller_injection.md)
* [Database](documentation/database.md)
* [Abstractions and concretions](documentation/controller_injection.md#abstractions)
* [Services](documentation/services.md)
* [Rendering](documentation/render.md)
* [Components](documentation/components.md)

