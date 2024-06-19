## Laravel API REST
API REST - Sistema de Agendamento para Barbearias.

Esta API permite que usuários criem contas como BarberShop (ADM) ou cliente. Usuários BarberShop podem personalizar sua Barbearia, criando Barbers (Barbeiros), Services (Serviços) e Schedules (Horários disponíveis). Usuários clientes tem permissão de criar um Appointment (Agendar horário) e Evaluate (Avaliar o serviço).

Para a criação desta API foi usado Laravel, MySQL como Banco de Dados e Docker para rodar o projeto.

## Endpoints

##### User Endpoints (Autenticação)

Registro de User ```POST api/user/register```

Login de User ```POST api/user/login```

Atualizar token de User ```POST /user/refresh``` - Requer Autenticação

Logout de User ```POST /user/logout``` - Requer Autenticação

Obter dados do User logado ```GET api/user/profile``` - Requer Autenticação

Atualizar dados do User logado ```PUT api/user/update-profile``` - Requer Autenticação

Obter os Appointments do User ```GET /user/appointments``` - Requer Autenticação

<hr>

##### Barber Shop Endpoints (Autenticação)

Registro de BarberShop ```POST /barber-shop/register```

Login de BarberShop ```POST /barber-shop/login```

Atualizar token de BarberShop ```POST /barber-shop/refresh``` - Requer Autenticação

Logout de BarberShop ```POST /barber-shop/logout``` - Requer Autenticação

Obter dados de BarberShop logado ```GET /barber-shop/profile``` - Requer Autenticação

Atualizar dados de BarberShop logado ```PUT api/barber-shop/update-profile``` - Requer Autenticação

Painel de controle do BarberShop ```GET /barber-shop/dashboard``` - Requer Autenticação

<hr>

##### Barber Endpoints

Listar todos os Barbers ```GET /barber```

Registra novo Barbers ```POST /barber``` - Requer Autenticação de BarberShop

Obter informações de um Barber específico ```GET /barber/{barber}```

Atualiza um Barber ```PUT/PATCH /barber/{barber}``` - Requer Autenticação de BarberShop

Exclui um Barber ```DELETE /barber/{barber}``` - Requer Autenticação de BarberShop

<hr>

##### Service Endpoints

Listar todos os Services ```GET /service```

Registra novo Services ```POST /service``` - Requer Autenticação de BarberShop

Obter informações de um Service específico ```GET /service/{service}```

Atualiza um Service ```PUT/PATCH /service/{service}``` - Requer Autenticação de BarberShop

Exclui um Service ```DELETE /service/{service}``` - Requer Autenticação de BarberShop

<hr>

##### Schedule Endpoints

Listar todos os Schedules ```GET /schedule```

Registra novo Schedule ```POST /schedule``` - Requer Autenticação de BarberShop

Obter informações de um Schedule específico ```GET /schedule/{schedule}```

Atualiza um Schedule ```PUT/PATCH /schedule/{schedule}``` - Requer Autenticação de BarberShop

Exclui um Schedule ```DELETE /schedule/{schedule}``` - Requer Autenticação de BarberShop

<hr>

##### Appointment Endpoints

Criar um novo Appointment ```POST /create-appointment``` - Requer Autenticação de User

Obter informações de um compromisso específico ```GET /show-appointment/{appointment}``` - Requer Autenticação de User

<hr>

##### Evaluate
Listar um Evaluate específico ```GET api/evaluation/{evaluation}```

Criar um novo Evaluate ```POST api/appointment/{appointment}/evaluate``` - Requer Autenticação de User

<hr>

##### Documentação Swagger (OpenAPI)
```GET api/documentation```

<hr>

### Pré-requisitos
* PHP 8.3
* Laravel 10+
* Composer
* Docker

<hr>

### Instalação
1. Clone o repositório:
```
git clone https://github.com/matheusmrqs4/barbershop-api
```

2. Entre no diretório:
 
```
cd your-repo
```

3. Instale as dependências:
```
composer install
```

4. Crie um arquivo .env e preencha os dados:
```
cp .env.example .env
```

5. Gere uma nova chave da aplicação:
```
php artisan key:generate
```

6. Gere uma nova chave JWT:
```
php artisan jwt:secret  
```

7. Rode os Containers Docker:
```
docker-compose up -d --build
```

8. Acesse em:
```
http://127.0.0.1:8980/
```
