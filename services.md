# Services

Services are intended to divide the business logic into blocks. 

This will allow us to test the business logic, to make sure all the requirements for an action
are fulfilled. Example: We need to register a user which involves inserting data into the database
and sending an email. We may create another way for the user to register, for instance via API, which will need anyways
the same service.

Another example: one service block could be the cancellation of a subscription involving several operations.
But that service can be involved in another business block. 
Let's suppose we decide to ban a merchant We will trigger the block banned member 
but we will also need to cancel the subscriptions he has. That is why it is important to have every block delimited and tested.

**Therefore it is possible to inject a service inside a service**. and consequently in them we can also inject models.

As outlined before we inject those object via the constructor. For example:

    use Model_Banned;

    class BanningService
    {
        /**
         * @var Model_Banned
         */
        public $repoBanned;
        
        /**
         * @var SubscriptionService
         */
        public $subscriptionService;
    
        /**
         * @param Model_Banned $repoBanned
         */
        public function __construct(Model_Banned $repoBanned,SubscriptionService $subscriptionService){
                $this->repoBanned = $repoBanned;
                $this->subscriptionService = $subscriptionService;
        }
        
Note: In here we pass the Model_Banned to interact with the database banned table along with the subscriptionService
in order to allow the BanningService to also cancel the subscription via a potential `$this->subscriptionService->cancel()` method.
These objects are not actual existing objects but outlined like that to explain the functioning of the service logic.

**Important:** the injection of classes in a service can only be done via its constructor.
