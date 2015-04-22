Benchmarks
=========

Benchmarks are hard and often unhelpful. Here are some of those. Benchmarks performed locally on Mellivora's busiest page (scores/), with 85 registered teams and 45 challenges. A WordPress instance and a static HTML file are used for comparison. The Wordpress instance is a fresh out-of-box install, and the page loaded is the default post-install home page. Tools: Xdebug 2.2.3, SIEGE 3.0.5 and ApacheBench 2.3.

**Benchmark overview**

![Mellivora benchmark chart](http://i.imgur.com/Vn33abl.png "Mellivora benchmark chart")
![Mellivora benchmark chart](http://i.imgur.com/De4HH10.png "Mellivora benchmark chart")

**Benchmark system**
```
$ uname -a
Linux hostname 3.13.0-24-generic #46-Ubuntu (Ubuntu 14.04 LTS)

$ apache2 -v
Server version: Apache/2.4.7 (Ubuntu)

$ php -v
PHP 5.5.9-1ubuntu4 (cli) (built: Apr  9 2014 17:08:27) 
Copyright (c) 1997-2014 The PHP Group
Zend Engine v2.5.0, Copyright (c) 1998-2014 Zend Technologies
    with Zend OPcache v7.0.3, Copyright (c) 1999-2014, by Zend Technologies
    with Xdebug v2.2.3, Copyright (c) 2002-2013, by Derick Rethans
    
$ mysql --version
mysql Ver 14.14 Distrib 5.5.35, for debian-linux-gnu (i686) using readline 6.3
```

**Benchmark: Mellivora, with built-in caching disabled (dev environment settings)**
```
$ siege -b -t60S https://mellivora/scores
Transactions: 5958 hits
Availability: 100.00 %
Elapsed time:	59.99 secs
Data transferred: 22.02 MB
Response time: 0.15 secs
Transaction rate: 99.32 trans/sec
Throughput: 0.37 MB/sec
Concurrency: 14.93
Successful transactions: 5958
Failed transactions: 0
Longest transaction: 0.30
Shortest transaction: 0.04

$ ab -n 1000 -c 10 https://mellivora/scores
Time taken for tests:   11.236 seconds
Complete requests:      1000
Failed requests:        0
Total transferred:      48752000 bytes
HTML transferred:       48301000 bytes
Requests per second:    89.00 [#/sec] (mean)
Time per request:       112.362 [ms] (mean)
Time per request:       11.236 [ms] (mean, across all concurrent requests)
Transfer rate:          4237.14 [Kbytes/sec] received

Connection Times (ms)
              min  mean[+/-sd] median   max
Connect:        8   21  13.8     16      92
Processing:    28   91  36.3     90     215
Waiting:        4   17  11.6     14      73
Total:         38  112  41.0    111     258

Percentage of the requests served within a certain time (ms)
  50%    111
  66%    127
  75%    137
  80%    145
  90%    167
  95%    183
  98%    207
  99%    227
 100%    258 (longest request)
```

**Benchmark: Mellivora, built-in caching enabled (prod environment settings)**
```
$ siege -b -t60S https://mellivora/scores
Transactions: 17041 hits
Availability: 100.00 %
Elapsed time: 59.17 secs
Data transferred: 62.97 MB
Response time: 0.05 secs
Transaction rate: 288.00 trans/sec
Throughput: 1.06 MB/sec
Concurrency: 14.72
Successful transactions: 17041
Failed transactions: 0
Longest transaction: 0.17
Shortest transaction: 0.01

$ ab -n 1000 -c 10 https://mellivora/scores
Time taken for tests:   3.902 seconds
Complete requests:      1000
Failed requests:        0
Total transferred:      48752000 bytes
HTML transferred:       48301000 bytes
Requests per second:    256.27 [#/sec] (mean)
Time per request:       39.022 [ms] (mean)
Time per request:       3.902 [ms] (mean, across all concurrent requests)
Transfer rate:          12200.67 [Kbytes/sec] received

Connection Times (ms)
              min  mean[+/-sd] median   max
Connect:        9   26   9.2     26      64
Processing:     3   12   8.2     10      49
Waiting:        1    7   5.7      6      36
Total:         15   39  11.9     37      87

Percentage of the requests served within a certain time (ms)
  50%     37
  66%     41
  75%     44
  80%     47
  90%     55
  95%     63
  98%     71
  99%     74
 100%     87 (longest request)
```

**Benchmark: WordPress 3.9, fresh out-of-box install**
```
$ siege -b -t60S https://wordpress
Transactions: 345 hits
Availability: 100.00 %
Elapsed time: 59.29 secs
Data transferred: 0.77 MB
Response time: 2.52 secs
Transaction rate: 5.82 trans/sec
Throughput: 0.01 MB/sec
Concurrency: 14.68
Successful transactions: 346
Failed transactions: 0
Longest transaction: 3.43
Shortest transaction:1.04

$ ab -n 1000 -c 10 https://wordpress/
Time taken for tests:   162.590 seconds
Complete requests:      1000
Failed requests:        0
Total transferred:      7679000 bytes
HTML transferred:       7412000 bytes
Requests per second:    6.15 [#/sec] (mean)
Time per request:       1625.902 [ms] (mean)
Time per request:       162.590 [ms] (mean, across all concurrent requests)
Transfer rate:          46.12 [Kbytes/sec] received

Connection Times (ms)
              min  mean[+/-sd] median   max
Connect:        8   12   5.2     11      67
Processing:   712 1610 215.7   1630    2139
Waiting:      712 1608 215.2   1629    2127
Total:        726 1622 215.9   1643    2148

Percentage of the requests served within a certain time (ms)
  50%   1643
  66%   1722
  75%   1766
  80%   1789
  90%   1870
  95%   1936
  98%   2014
  99%   2066
 100%   2148 (longest request)
```
**Benchmark: single static file**
```
$ siege -b -t60S https://localhost/test.html
Transactions: 21760 hits
Availability: 100.00 %
Elapsed time: 59.12 secs
Data transferred: 0.10 MB
Response time: 0.04 secs
Transaction rate: 368.06 trans/sec
Throughput: 0.00 MB/sec
Concurrency: 14.51
Successful transactions: 21761
Failed transactions: 0
Longest transaction: 0.18
Shortest transaction: 0.00

$ ab -n 1000 -c 10 https://localhost/test.html
Time taken for tests:   3.284 seconds
Complete requests:      1000
Failed requests:        0
Total transferred:      248000 bytes
HTML transferred:       5000 bytes
Requests per second:    304.49 [#/sec] (mean)
Time per request:       32.842 [ms] (mean)
Time per request:       3.284 [ms] (mean, across all concurrent requests)
Transfer rate:          73.74 [Kbytes/sec] received

Connection Times (ms)
              min  mean[+/-sd] median   max
Connect:        9   27   8.7     26      65
Processing:     0    6   5.7      5      35
Waiting:        0    4   4.0      3      35
Total:          9   33   9.9     33      78

Percentage of the requests served within a certain time (ms)
  50%     33
  66%     36
  75%     39
  80%     40
  90%     44
  95%     50
  98%     54
  99%     60
 100%     78 (longest request)
```

#### Memory consumption
of a single page load. Measured with Xdebug.
- Mellivora, caching disabled: 0.115784MB
- Mellivora, caching enabled: 0.068176MB
- WordPress 3.9: 1.09819MB

as you can see, Mellivora is pretty damn fast.