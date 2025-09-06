# Keys PHP
## Rotas do Back-End

Pasta raiz: http://localhost/backend

### Usuários

*Criar Usuário:* POST /users

Entrada (BODY):
```json
{
    "name": "bruna",
    "email": "bruna@gmail.com",
    "main_pass": "123456789"
}
```


Retorno:
```json
{
    "status": "201",
    "message": "user created successfully!",
    "success": true,
    "data": {
        "insertId": "11"
    }
}
```

*Login:* POST /users/login

Entrada (BODY):
json

# Keys PHP
## Rotas do Back-End

Pasta raiz: http://localhost/backend

### Usuários

*Criar Usuário:* POST /users

Entrada (BODY):
json
{
    "name": "bruna",
    "email": "bruna@gmail.com",
    "main_pass": "123456789"
}


Retorno:
json
{
    "status": "201",
    "message": "user created successfully!",
    "success": true,
    "data": {
        "insertId": "11"
    }
}


*Login:* POST /users/login

Entrada (BODY):
json
{
    "email": "bernardo@gmail.com",
    "main_pass": "123456789"
}


Retorno:
json
{
    "success": true,
    "token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzUxMiJ9.eyJpYXQiOjE3NTMzODEzMDQsImp0aSI6IjZOajFxOHhyNXBHVnNmL0VnVmZ4eEE9PSIsImlzcyI6Imh0dHA6Ly9sb2NhbGhvc3Q6ODA4MCIsIm5iZiI6MTc1MzM4MTMwNCwiZXhwIjoxNzUzMzg0OTA0LCJkYXRhIjp7ImlkIjoxMCwiZW1haWwiOiJiZXJuYXJkb0BnbWFpbC5jb20ifX0.TbgnM8rXfz0ZLauGISQDCC3pXrQogsAr2UIWUYUk6JwJJ2HbE-CPr59vcSym4xyoipt0S1myxyz7h_IGCINKaw"
}


*Buscar Usuário por ID:* GET /users/id/{id}

Entrada: {id} do parâmetro

Retorno:

json
{
    "id": 5,
    "name": "Eduarda Silva",
    "email": "eduarda@example.com",
    "picture": null
}



*Buscar Usuário Autenticado:* GET /users/user

Entrada: Apenas o token inserido nos Headers.

Retorno:
json
{
    "id": 10,
    "name": "bernardo",
    "email": "bernardo@gmail.com",
    "picture": null
}


*Atualizar Dados do Usuário:* PUT /users/user

Entrada (Headers): token.
Entrada (BODY):

json
{
    "name": "tavo",
    "picture": "minha-nova-foto.jpeg"
}


Retorno: 204 sem conteúdo


# Keys PHP
## Rotas do Back-End

Pasta raiz: http://localhost/backend

### Usuários

*Criar Usuário:* POST /users

Entrada (BODY):
json
{
    "name": "bruna",
    "email": "bruna@gmail.com",
    "main_pass": "123456789"
}


Retorno:
json
{
    "status": "201",
    "message": "user created successfully!",
    "success": true,
    "data": {
        "insertId": "11"
    }
}


*Login:* POST /users/login

Entrada (BODY):
json
{
    "email": "bernardo@gmail.com",
    "main_pass": "123456789"
}


Retorno:
json
{
    "success": true,
    "token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzUxMiJ9.eyJpYXQiOjE3NTMzODEzMDQsImp0aSI6IjZOajFxOHhyNXBHVnNmL0VnVmZ4eEE9PSIsImlzcyI6Imh0dHA6Ly9sb2NhbGhvc3Q6ODA4MCIsIm5iZiI6MTc1MzM4MTMwNCwiZXhwIjoxNzUzMzg0OTA0LCJkYXRhIjp7ImlkIjoxMCwiZW1haWwiOiJiZXJuYXJkb0BnbWFpbC5jb20ifX0.TbgnM8rXfz0ZLauGISQDCC3pXrQogsAr2UIWUYUk6JwJJ2HbE-CPr59vcSym4xyoipt0S1myxyz7h_IGCINKaw"
}


*Buscar Usuário por ID:* GET /users/id/{id}

Entrada: {id} do parâmetro

Retorno:

json
{
    "id": 5,
    "name": "Eduarda Silva",
    "email": "eduarda@example.com",
    "picture": null
}



*Buscar Usuário Autenticado:* GET /users/user

Entrada: Apenas o token inserido nos Headers.

Retorno:
json
{
    "id": 10,
    "name": "bernardo",
    "email": "bernardo@gmail.com",
    "picture": null
}


*Atualizar Dados do Usuário:* PUT /users/user

Entrada (Headers): token
Entrada (BODY):

json
{
    "name": "tavo",
    "picture": "minha-nova-foto.jpeg"
}


Retorno: 204 sem conteúdo


### Senhas

*Criar uma Senha:* POST /passwords/create

Entrada (Headers): token
Entrada (Body):
json
{
    "value": "teste5"
}


Saida:
json
{
    "status": "201",
    "message": "password created sucessfully",
    "success": true,
    "data": {
        "success": true,
        "insertId": "11"
    }
}


*Pegar todas as Senhas:* GET /passwords/all

Entrada (Header): token

Saída:
json  
{ 
    "status": "200",
    "message": "passowords found",
    "success": true,
    "data": [
        {
            "id": 11,
            "value": "teste5",
            "software_id": null
        }
    ]
}


*Deletar uma Senha:* DELETE /passwords/delete/{id}

Entrada (Headers): token

Saída:
json
{
    "status": "404",
    "message": "password not deleted",
    "success": false,
    "data": {
        "success": false,
        "message": "password not found or not owned by user"
    }
}


*Pegar uma senha por ID:* GET /passwords/id/{id}

Entrada (Headers): token

Saida:
json
{
    "status": "404",
    "message": "password not found",
    "success": false,
    "data": {
        "success": false,
        "message": "password not found"
    }
}


*Atualizar uma Senha:* PUT /passwords/update/{id}

Entrada (Headers): token
Entrada (Body): Senha nova.

Saida: 204 No Content

### Softwares

*Pegar todos os softwares:* GET /softwares/all

Retorno:
```json
{
    "status": "200",
    "message": "softwares found!",
    "success": true,
    "data": [
        {
            "id": 1,
            "name": "Facebook",
            "icon": null
        },
        {
            "id": 2,
            "name": "Gmail",
            "icon": null
        },
        {
            "id": 4,
            "name": "Netflix",
            "icon": null
        },
        {
            "id": 5,
            "name": "Twitter",
            "icon": null
        },
        {
            "id": 6,
            "name": "antediguemon",
            "icon": null
        },
        {
            "id": 7,
            "name": "zé pilintra aluguéis",
            "icon": null
        },
        {
            "id": 9,
            "name": "softwareDeTeste",
            "icon": null
        }
    ]
}
////////////////////////////
