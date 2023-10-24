<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

<hr>

## Laravel API REST
API REST - Sistema de Agendamento de Horários para Barbearias.
Laravel
MySQL
PHPUnit

### Endpoints
##### User Endpoints:
```POST /user/register - Registro de usuário```

```POST /user/login - Login de usuário```

```GET /user/me - Obter dados do usuário logado```

```POST /user/refresh - Atualizar token de usuário```

```POST /user/logout - Logout de usuário```

```GET /user/appointments - Obter os compromissos do usuário```


##### Barber Shop Endpoints:

```POST /barber-shop/register - Registro de estabelecimento de barbearia```

```POST /barber-shop/login - Login de estabelecimento de barbearia```

```GET /barber-shop/me - Obter dados do estabelecimento de barbearia logado```

```POST /barber-shop/refresh - Atualizar token do estabelecimento de barbearia```

```POST /barber-shop/logout - Logout do estabelecimento de barbearia```

```GET /barber-shop/dashboard - Painel de controle do estabelecimento de barbearia```


##### Barber Endpoints:

```POST /barber - Registra novo barbeiro```

```PUT/PATCH /barber/{barber} - Atualiza um barbeiro (requer autenticação de estabelicimento de barbearia)```

```DELETE /barber/{barber} - Exclui um barbeiro```

```GET /barber - Listar todos os barbeiros```

```GET /barber/{barber} - Obter informações de um barbeiro específico```


##### Service Endpoints:

```POST /service - Registra novo serviço```

```PUT/PATCH /service/{service} - Atualiza um serviço (requer autenticação de estabelicimento de barbearia)```

```DELETE /service/{service} - Exclui um serviço```

```GET /service - Listar todos os serviços```

```GET /service/{service} - Obter informações de um serviço específico```


##### Schedule Endpoints:

```POST /schedule - Registra novo Schedule```

```PUT/PATCH /schedule/{schedule} - Atualiza um Schedule (requer autenticação de estabelicimento de barbearia)```

```DELETE /schedule/{schedule} - Exclui um schedule```

```GET /schedule - Listar todos os horários```

```GET /schedule/{schedule} - Obter informações de um horário específico```


##### Appointment Endpoints:

```POST /create-appointment - Criar um novo compromisso```

```GET /show-appointment/{appointment} - Obter informações de um compromisso específico (requer autenticação de usuário)```


## Routes

```
Route::prefix('user')->group(function () {
    Route::middleware('user.validate.register')->post('register', [UserRegisterController::class, 'register']);
    Route::middleware('user.validate.login')->post('login', [UserController::class, 'login']);
    Route::middleware('auth:api')->get('me', [UserController::class, 'me']);
    Route::middleware('auth:api')->post('refresh', [UserController::class, 'refresh']);
    Route::middleware('auth:api')->post('logout', [UserController::class, 'logout']);
    Route::middleware('auth:api')->get('appointments', [UserController::class, 'userAppointments']);
});
```

```
Route::prefix('barber-shop')->group(function () {
    Route::middleware('barbershop.validate.register')->post('register', [BarberShopRegisterController::class, 'register']);
    Route::middleware('barbershop.validate.login')->post('login', [BarberShopController::class, 'login']);
    Route::middleware('auth:barber_shop')->get('me', [BarberShopController::class, 'me']);
    Route::middleware('auth:barber_shop')->post('refresh', [BarberShopController::class, 'refresh']);
    Route::middleware('auth:barber_shop')->post('logout', [BarberShopController::class, 'logout']);
    Route::middleware('auth:barber_shop')->get('dashboard', [DashboardController::class, 'index']);
});
```

```
Route::middleware('auth:barber_shop')->apiResource('barber', BarberController::class)->except(['index', 'show']);
Route::get('barber', [BarberController::class, 'index']);
Route::get('barber/{barber}', [BarberController::class, 'show']);
```

```
Route::middleware('auth:barber_shop')->apiResource('service', ServiceController::class)->except(['index', 'show']);
Route::get('service', [ServiceController::class, 'index']);
Route::get('service/{service}', [ServiceController::class, 'show']);
```

```
Route::middleware('auth:barber_shop')->apiResource('schedule', ScheduleController::class)->except(['index', 'show']);
Route::get('schedule', [ScheduleController::class, 'index']);
Route::get('schedule/{schedule}', [ScheduleController::class, 'show']);
```

```
Route::middleware('auth:api')->post('create-appointment', [AppointmentController::class, 'createAppointment']);
Route::middleware('auth:api')->get('show-appointment/{appointment}', [AppointmentController::class, 'showAppointment']);
```
