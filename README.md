I was asked to develop a API server with these 
<a href="https://github.com/noud/bumbal/doc/">Specifications</a>.

To get me a Laravel Docker project environment i executed 
```
curl -s "https://laravel.build/bumbal?with=mariadb,redis" | bash
```

### Start containers

Be sure to have no local Apache running:
```
sudo /etc/init.d/apache2 stop
sudo systemctl stop apache2.service
```

To start the dockered application type
```
cd bumbal && ./vendor/bin/sail up
```

Finally, you can access the application in your web browser at: <a href="http://localhost">http://localhost</a>.

I get an error,
see this browser screenshot:
<img src="doc/Screenshot from localhost error 2024-04-10 16-58-13.png">Error</img>

after a while i did open another browser tab to  <a href="http://localhost">http://localhost</a>.

I get
<img src="doc/Screenshot from localhost 2024-04-10 17-03-53.png">Laravel up and running in a docker container with a mariadb container and redis container .</img>

### Stop containers

```
cd bumbal && ./vendor/bin/sail stop
```
to stop the docker container services.
