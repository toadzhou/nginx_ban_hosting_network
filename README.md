nginx.conf like below:



    ##user nginx;
    user www-data;
    worker_processes auto;
    pid /run/nginx.pid;

    events {
      worker_connections 20999;
      # multi_accept on;
    }

    http {
      limit_conn_zone $binary_remote_addr zone=addr:15m;


		## https://www.digitalxxxxxx.com/community/tutorials/how-to-increase-pagespeed-score-by-changing-your-nginx-configuration-on-ubuntu-16-04
	  gzip on; ## use cpu
    gzip_comp_level    4; ## https://serverfault.com/questions/253074/what-is-the-best-nginx-compression-gzip-level
    gzip_min_length    256;
    gzip_proxied       any;
    gzip_vary          on; ## use cpu 

    gzip_types
    application/atom+xml
    application/javascript
    application/json
    application/ld+json
    application/manifest+json
    application/rss+xml
    application/vnd.geo+json
    application/vnd.ms-fontobject
    application/x-font-ttf
    application/x-web-app-manifest+json
    application/xhtml+xml
    application/octet-stream
    application/xml
    font/opentype
    image/bmp
	image/png
	image/jpg
    image/svg+xml
    image/x-icon
    text/cache-manifest
    text/css
    text/plain
    text/vcard
    text/vnd.rim.location.xloc
    text/vtt
    text/x-component
	video/MP2T
    text/x-cross-domain-policy;
	## sendfile on;
	## tcp_nopush on;
	## tcp_nodelay on;
	keepalive_timeout 2;
	types_hash_max_size 20480;
	access_log off;
	## access_log /tmp/nginx_access.log;

	include /etc/nginx/mime.types;
	default_type application/octet-stream;

	include /etc/nginx/banned.d/*.conf;
    include /etc/nginx/banned.d/*.ini;
	include "/etc/nginx/my_config";
}


