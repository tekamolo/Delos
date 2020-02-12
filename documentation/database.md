# Database Interactions
**Eloquent**

To simplify the database relations I have decided to use an external ORM, Eloquent. To summarize, this is a ORM created by Laravel creators.
To avoid having to create from scratch Database managers I have integrated the library.

You can read more about it in here:

```
https://laravel.com/docs/5.8/eloquent
```

You won't have access to the command line but you should know that all the other relationships between objects .

To summarize:
* Create a Model, Repository and the database table:

You can create a class under the Model folder like I did for the users `User.php` by extending the object Model.

Let's call it Advert since the entity User is already created:

```
namespace Delos\Model;

use Illuminate\Database\Eloquent\Model;

class Advert extends Model
{
    protected $table = "advert";
    protected $fillable = ["advert","title","description"];
}
```

Now let's create the corresponding database:

```
CREATE TABLE `advert` (
 `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
 `title` varchar(100) DEFAULT NULL,
 `email` varchar(200) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
 PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
```

To preserve the Repository pattern you can create a Repository under repositories 

```
<?php

namespace Delos\Repository;

use Delos\Model\Advert;

class AdvertRepository
{
    /**
     * @return Advert[]|\Illuminate\Database\Eloquent\Collection
     */
    public function getAll(){
        return Advert::all();
    }

    public function createAdvert(Advert $advert){
        Advert::create(['title'=>$advert->title,'description'=>$advert->description]);
    }
}
```

* Interacting with the database:

To Insert a new entry in the database. 
You will have to instantiate the entity and use the create method from the repo:

```
$advert = new Advert();
$advert->title = "This is my title";
$advert->description = "Description of the adv";
$repository->createAdvert($advert);
```

Obviously you could access the `Advert::create` method directly but the repository pattern is made to abstract that layer making things easier if the ORM or database structure changes.


* Select: You retrieve the entity by using in the repo:

```
$advert = Model\Advert::find(1); //this is the id
```

* You can update the data by just getting the previous objects and saving it:

```
$advert = Model\Advert::find(1); //this is the id
$advert->title = 'Tesla for Sale';

$advert->save();
```

* And you can delete them:

```
$advert = Model\Advert::find(1); //this is the id
$advert->delete();
```

But again I always advice you to use the repository in the middle: So if you have to implement the delete method in the repo use:

```
class AdvertRepository
{
    public function deleteAdvert(Advert $advert){
        Advert::deleted();
    }
}
```

Do the same for updating and retrieve. 
For relationships between models and more complex jointures please refer to the documentation from the Eloquent link I gave above.

