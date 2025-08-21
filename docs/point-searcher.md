# PointSearcher

If you want to display points of delivery you can use `Xgrz\InPost\Facades\InPost::pointSearch($query)`. As a `$query` you can provide city, street and name of the point. However if you provide a `post_code` of your client your will receive parcel locker/points near this code with distance.  
Distance is filled only for `post_code` search. 
