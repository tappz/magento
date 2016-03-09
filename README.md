# T-appz  Cache optimization

As your bussiness  grow and see an increase in traffic, one of the components that shows stress the fastest is the backend database. Dealing with this is leveraging a memory object caching system, like memcached or redis.

## Memcache Optimization

İf you already use memcache caching system its super easy to optimizate for t-appz module.

Before start try to connect to the memcache server  and be sure that your    memcache server is  running.

** Memcache Configuration **

Go to tappz module file 

```sh
app/code/community/TmobLabs/Tappz
```
Go to helper file and copy & paste this helper class 

https://github.com/tappz/magento/blob/cache/app/code/community/TmobLabs/Tappz/Helper/Memcache.php

Check & change your memcache server  details
```sh
 private $_host = "127.0.0.1";
 private $_port = "11211";
```
After that go to t-appz module  catalog model  
```sh
app/code/community/TmobLabs/Tappz/Model/Catalog
```
and open  your Api.php .After go to  

https://github.com/tappz/magento/blob/cache/app/code/community/TmobLabs/Tappz/Model/Catalog/Api.php

And go to getFrontPage function 
As you see we first register tappz/memcache 
```sh
 $cache = Mage::helper('tappz/memcache');
 ```
 After that we have to check specific key for memcache.
 
 ```sh
   $sampleEx = $cache->get("getFrontPage");
 ```
if  this key doesnt have data ; we have to set our data to this key 
 ```sh
 if($sampleEx == false){
   $cache->store("getFrontPage",$sampleEx);
 }
  return $sampleEx;
  ```
From now on we set our data to  getFrontPage(key).Until nemcache expire each request going to memcache not your database.
You can simple go to

https://github.com/tappz/magento/blob/cache/app/code/community/TmobLabs/Tappz/Model/Catalog/Api.php
 replace with your  Catalog api.
 Memcache benchmarks 
 Machine : 64-bit 1 cpu 	1.7 Memory 
 
   Method|Without Cache|Memcache
   
   getFrontPage | 4290 ms | 1328 ms
   
   getFrontPage | 4290 ms | 1328 ms
   
   getCategories || 3423 ms || 1297 ms 
   
   getCategory/id || 1515 ms || 1272 ms
   
   getProduct/id || ​1823 ms || ​1241 ms
   
   getRelatedProducts/id|​1609 ms || ​1346 ms





 
## Redis  


Go to tappz module file 

```sh
app/code/community/TmobLabs/Tappz
```
Go to helper file and copy & paste this helper class 

https://github.com/tappz/magento/blob/cache/app/code/community/TmobLabs/Tappz/Helper/Redis.php

Check & change your memcache server  details
```sh
 private $_host = "127.0.0.1";
 private $_port = "6379";
```
After that go to t-appz module  catalog model  
```sh
app/code/community/TmobLabs/Tappz/Model/Catalog
```
and open  your Api.php .After go to  

https://github.com/tappz/magento/blob/cache/app/code/community/TmobLabs/Tappz/Model/Catalog/Api.php

And go to getFrontPage function 
As you see we first register tappz/redis 
```sh
 $cache = Mage::helper('tappz/redis');
 ```
 After that we have to check specific key for redis.
 
 ```sh
   $sampleEx = $cache->get("getFrontPage");
 ```
if  this key doesnt have data ; we have to set our data to this key 
 ```sh
 if($sampleEx == false){
   $cache->store("getFrontPage",$sampleEx);
 }
  return $sampleEx;
  ```
From now on we set our data to  getFrontPage(key).Until nemcache expire each request going to memcache not your database.
You can simple go to

https://github.com/tappz/magento/blob/cache/app/code/community/TmobLabs/Tappz/Model/Catalog/Api.php
 replace with your  Catalog api.
After that change 
 ```sh
  $cache = Mage::helper('tappz/memcache');
  ```
  to 
 ```sh
  $cache = Mage::helper('tappz/redis');
  ```
If you ever need further assistance please contact us at support@t-appz.com. We’ll be happy to help!

T-appz team 
