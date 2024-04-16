I was asked to develop a API server with these 
<a href="https://github.com/noud/bumbal/blob/main/doc/Test%20assignment%20for%20back-end%20developer.pdf">Specifications</a>.

To get me a Laravel Docker project environment i executed 
```
curl -s "https://laravel.build/bumbal?with=mariadb,redis" | bash
```
### Git clone

After you have git cloned this repository you have to install the php packages.
For this you have to have php >= 8.2.
```
cd bumbal && composer install
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
If it's the first time you did build the containers you have to add the JWT secret to your .env file.
```
cd bumbal && ./vendor/bin/sail artisan jwt:secret
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

### Database

There are 2 database migrations for the Employees and the Devices tables.
- <a href="https://github.com/noud/bumbal/blob/main/database/migrations/2024_04_11_142451_create_employees_table.php">create_employees_table</a>.
- <a href="https://github.com/noud/bumbal/blob/main/database/migrations/2024_04_11_142556_create_devices_table.php">create_devices_table</a>.

Database migrations can ben run by:
```
cd bumbal && .vendor/bin/sail artisan migrate
```

### Models

There is the given User model and 2 models for our Employee and Device.
- <a href="https://github.com/noud/bumbal/blob/main/app/Models/Employee.php">Employee</a>.
- <a href="https://github.com/noud/bumbal/blob/main/app/Models/Device.php">Device</a>.
- <a href="https://github.com/noud/bumbal/blob/main/app/Models/User.php">User</a>.

### JWT authentication

For JWT authentication i add package ```php-open-source-saver/jwt-auth```.
```
cd bumbal && .vendor/bin/sail composer require php-open-source-saver/jwt-auth
```
I added <a href="https://github.com/noud/bumbal/blob/main/app/Http/Controllers/api/AuthController.php">AuthController.php</a>

### API

There are controllers for the API endpoints.

-<a href="https://github.com/noud/bumbal/blob/main/app/Http/Controllers/api/DeviceController.php">DeviceController.php</a>

-<a href="https://github.com/noud/bumbal/blob/main/app/Http/Controllers/api/EmployeeController.php">EmployeeController.php</a>

so i have the following API endpoints:
```
$ ./vendor/bin/sail  artisan route:list --path=api

  POST      api/v1/device generated::rfbPTr21n5k9VdbM › api\DeviceController@…
  GET|HEAD  api/v1/device/{id} generated::xrgLU0DiQJGA912j › api\DeviceContro…
  PUT       api/v1/device/{id} generated::3jyx9E0WN0Ft5TRO › api\DeviceContro…
  DELETE    api/v1/device/{id} generated::Y66VGT20it2lEz0p › api\DeviceContro…
  GET|HEAD  api/v1/devices generated::2SKAf0q5pFkzDl7Z › api\DeviceController…
  POST      api/v1/employee generated::Oyto2t82NRC4Hkn5 › api\EmployeeControl…
  GET|HEAD  api/v1/employee/{id} generated::kg7mdAHwcOC2RAtI › api\EmployeeCo…
  PUT       api/v1/employee/{id} generated::VeGPSn1YQC3vXzei › api\EmployeeCo…
  DELETE    api/v1/employee/{id} generated::DftlC7SYvoDkyU3g › api\EmployeeCo…
  GET|HEAD  api/v1/employees generated::Mjh2kBh4LxgQgr7a › api\EmployeeContro…
  POST      api/v1/login .................... login › api\AuthController@login
  POST      api/v1/logout generated::saN84nweRG7Jj3tN › api\AuthController@lo…
  POST      api/v1/refresh generated::ZICMVOtuPDzeAIQs › api\AuthController@r…
  POST      api/v1/register generated::2eiXG4nVh19v18qL › api\AuthController@…

                                                           Showing [14] routes

```
