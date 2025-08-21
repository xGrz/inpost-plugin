# Jobs for queue

1. `Xgrz\InPost\Jobs\SynchronizePointsJob` - run this job at least once a day. It will update points of delivery details.
2. `Xgrz\InPost\Jobs\UpdateInPostServices` - run this job at least once a month. It will update InPost services and additional services.

Queue is required for Jobs to work. It can download a large number of points. In case of running without queue mechanizm it can time out script.

You should manaually **add a job to laravel scheduler** for regular execution as mentioned above.