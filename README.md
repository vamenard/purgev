## PurgeV

Simple php tool to purge vanish cache of all internal links present on a web page. Very handy when you don't have access to varnish deamon, you only need to test a specific part or in situations when flushing the cache entirely is too resource demanding. 
***

### Usage

`php purgev.php -h http://www.example.com`
***

### Future options

- Allow multiple path requests for balanced servers

- Allow verbose mode and log file

- Add fail safe features

- Limit the number of curl requests

- Allow recursive / page vanish purge
 
- Code a new version in C

