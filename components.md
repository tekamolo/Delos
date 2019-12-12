# Render a component

Now we have the controllers and templates put together we can reuse them to make things more compact and reusable via components.

The problem encountered previously were that this platform did not follow a framework architecture. We had issues dealing with independent components their resources and their access to the database.
We had to instantiate several times the very same objects and database connections making the plateform lose efficiency.

Now the component is using the same collection of classes where the container checks the needed classes exist or not and will instantiate them. Yes that was the idea of the dependency injection explained previously in service section.

Components check those very same classes and does not need to reinstantiate something that already exist.

You can insert a component into your template via a special twig service injected into the template under the alias component. Let's be concrete:

``{{ component.render("Component\\Component","merchantSearchFilter")|raw }}``

Here the render method will call the the Controller ``Component`` and the the method ``merchantSearchFilter`` inside that controller. In theory you won't need to do any thing else.
Also do not neglect the filter ``raw`` that will allow twig to interpret the results as html otherwise the browser will render the html tags too (escaping).

If we look into that controller:

```
    /**
     * This is the controller that handles the merchant Search Filter component in the admin section
     * @param Request $request
     * @param merchantRepository $merchantRepo
     * @return mixed
     */
    public function merchantSearchFilter(Request $request,MerchantRepository $merchantRepo)
    {

        $merchantId = $request->get->get("merchantId", \VarFilter::INT, 0);

        $merchantInfo = false;
        if(!empty($merchantId)){
            $merchantInfo = $merchantRepo->getMerchantShortHandById($merchantId);
        }

        return $this->utils->renderComponent('admin/component/adminSearchFilter.html.twig',array(
            "merchantInfo" => $merchantInfo
        ));
    }
```

As you can see the component call for its own backend resources. What about the front end ressources?
Let's see the template that is being rendered.

```
<link rel="stylesheet" href="/css/component/merchantSearchFilter.css">
<div id="searcher">
    <label for="merchants">Merchants: </label>
    <div>
        <input type="text" name="searchInput" id="searchInput" placeholder="type merchant id or name" />
        <div id="spinner" class="center hidden">
            <div class="fa-2x">
                <i class="fas fa-spinner fa-spin"></i>
            </div>
        </div>
        <ul id="merchantList">
            <li><a href="#">Merchants results</a></li>
        </ul>
    </div>
    <div class="searchButtonContainer">
        <button id="search">Search</button>
    </div>
</div>
<div id="selectedMerchantInfo">
    Transfers for merchant:
    <b>
        {% if merchantInfo is not empty %}
            {{ merchantInfo.id }}. {{ merchantInfo['username'] }}
            <br /><a href="{{ router.getUrl('bank-transfers') }}"><button id="reset">Reset to all</button></a>
        {% else %}
            All merchants
        {% endif %}
    </b>
</div>
<script src="/jscripts/component/merchantSearchFilter.js"></script>
<script>
    $(document).ready(function(){
        let urlService = "{{ router.getUrl('bankTransferService') }}";
        let searchInputField = "#searchInput";
        $("#searchInput").keyup(function(){
            let input = $(this).val().trim();
            if(input.length > 3){
                backendSearcherService(urlService,input);
            }
        });
        $("#search").click(function(){
            let input = $(searchInputField).val().trim();
            backendSearcherService(urlService,input);
        });

        /**
         * Call back from the backendSevice, we should decide what to do with the results eventhough they may look similar
         * @param data
         * @param input
         */
        window.handleSearcherServiceResponse = function(data,input){
            $("#merchantList").html("");
            data.forEach(function(element){
                $("#merchantList").append("<li><a href='{{ router.getUrl('bank-transfers') }}?merchantId="+element.id+"'>"+element.id +". "+element.username+"</a></li>");
            });
            if(data.length == 0){
                $("#merchantList").append("<li><a href='#'>No results found for: "+input+"</a></li>");
            }
        }
    });
</script>
```

You can see that the component should bring with itself the css resources with the js resources as well.