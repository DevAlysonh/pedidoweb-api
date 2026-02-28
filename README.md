# 🚀 Customer Manager API

API para gerenciamento de cadastro de clientes com autenticação JWT, construída com **Domain-Driven Design (DDD)** e **Clean Architecture**.

---

## ⚡ Quick Start (Docker)

### Pré-requisitos
- Docker e Docker Compose instalados
- Git

### 3 Passos para Começar

#### 1️⃣ Clonar e configurar

```bash
git clone git@github.com:DevAlysonh/customer-manager-api.git
cd customer-manager-api
cp .env.example .env
```

#### 2️⃣ Iniciar containers

```bash
docker-compose up -d
```

Aguarde os containers ficarem prontos (cerca de 10 segundos).

#### 3️⃣ Configurar a aplicação

```bash
# Instalar dependências
docker compose exec app composer install

# Gerar chave da aplicação
docker compose exec app php artisan key:generate

# Gerar chave JWT (IMPORTANTE para autenticação)
docker compose exec app php artisan jwt:secret

# Executar migrações do banco de dados
docker compose exec app php artisan migrate
```

**Pronto!** Acesse: **http://localhost**

---

## 📋 Índice

1. [Sobre o Projeto](#sobre-o-projeto)
2. [Arquitetura](#arquitetura)
3. [Domain-Driven Design (DDD)](#domain-driven-design-ddd)
4. [Clean Architecture](#clean-architecture)
5. [Testes](#testes)
6. [Documentação da API](#documentação-da-api)
7. [Comandos Úteis](#comandos-úteis)

---

## Sobre o Projeto

A CustomerManager API é uma aplicação que gerencia o cadastro de clientes, permitindo que usuários autenticados criem, leiam, atualizem e deletem seus próprios registros de clientes com endereços associados.

---

## 📊 Arquitetura

O projeto segue os princípios de **Domain-Driven Design (DDD)** combinados com **Clean Architecture**, garantindo separação de responsabilidades, testabilidade e manutenibilidade.

### Camadas da Aplicação

```
app/
├── Domain/                  # Camada de Domínio (lógica de negócio)
├── Application/             # Camada de Aplicação (casos de uso)
├── Http/                    # Camada de Apresentação (controllers e requests)
└── Infrastructure/          # Camada de Infraestrutura (persistência e serviços)
```

---

## 🎯 Domain-Driven Design (DDD)

O projeto aplica DDD através da estrutura de domínios específicos e artefatos de domínio:

### Domínios

#### 1. **Customer (Domínio Principal)**
Responsável pela gestão de clientes e seus endereços.

**Entidades:**
- `Customer` - Agregado raiz (aggregate root) contendo os dados do cliente (id, nome, email, usuário responsável) e seu endereço

**Value Objects:**
- `CustomerId` - Identifica unicamente um cliente (válida com prefixo `cus_`)
- `Address` - Representa o endereço do cliente (rua, número, cidade, estado, CEP, id)

**Repositório:**
- `CustomerRepositoryInterface` - Abstração para persistência de clientes

**Exceções de Domínio:**
- `CustomerNotFoundException` - Cliente não encontrado (404)
- `InvalidZipcodeException` - CEP inválido

#### 2. **User (Domínio de Autenticação)**
Responsável pelos usuários e autenticação do sistema.

**Entidades:**
- `User` - Agregado raiz (aggregate root) contendo dados do usuário (id, nome, email, senha)

**Value Objects:**
- `UserId` - Identifica unicamente um usuário (válida com prefixo `usr_`)

**Repositório:**
- `UserRepositoryInterface` - Abstração para persistência de usuários

**Exceções de Domínio:**
- `UserNotFoundException` - Usuário não encontrado
- `UnauthorizedException` - Usuário não autorizado
- `UserAlreadyExistsException` - Email já registrado
- `InvalidCredentialsException` - Credenciais inválidas

#### 3. **Shared (Domínio Compartilhado)**
Contém abstrações e interfaces reutilizáveis:

**Interfaces:**
- `IdGeneratorInterface` - Contrato para geração de IDs/Uuids
- `PasswordHasherInterface` - Contrato para hash de senhas
- `TokenGeneratorInterface` - Contrato para geração de tokens
- `LoggerInterface` - Contrato para logging

---

## 🏗️ Clean Architecture

A aplicação está organizada em camadas conforme princípios de Clean Architecture:

### 1. **Domain Layer** (`app/Domain/`)
Núcleo da aplicação contendo lógica de negócio pura, independente de frameworks. Define:
- Entidades (aggregate root)
- Value Objects
- Interfaces de repositórios
- Exceções de domínio
- Interfaces de serviços

**Característica principal:** Não possui dependências externas (sem Laravel, sem banco de dados).

### 2. **Application Layer** (`app/Application/`)
Contém os **casos de uso** que orquestram a lógica de domínio:

**UseCases do Cliente:**
- `CreateCustomerUseCase` - Criar novo cliente (valida CEP via ViaCEP)
- `ListCustomersUseCase` - Listar clientes do usuário autenticado
- `ShowCustomerUseCase` - Exibir cliente específico (com validação de autorização)
- `UpdateCustomerUseCase` - Atualizar dados do cliente
- `UpdateCustomerAddressUseCase` - Atualizar endereço do cliente
- `DeleteCustomerUseCase` - Deletar cliente

**UseCases do Usuário:**
- `RegisterUseCase` - Registrar novo usuário
- `LoginUseCase` - Autenticar usuário
- `MeUseCase` - Obter dados do usuário autenticado

**DTOs (Data Transfer Objects):**
- Responsáveis por transportar dados entre camadas
- Exemplos: `CreateCustomerDTO`, `UpdateCustomerDto`, `LoginUserDTO`

### 3. **Infrastructure Layer** (`app/Infrastructure/`)
Implementações concretas de interfaces e serviços externos:

**Persistência (Eloquent):**
- `CustomerRepository` - Implementação de persistência de clientes
- `UserRepository` - Implementação de persistência de usuários
- Models: `Customer`, `Address`, `User` (Eloquent Models)

**Serviços:**
- `CepService` - Interface para consulta de CEP
- `ViaCepService` - Implementação via API ViaCEP
- `JwtTokenGenerator` - Geração de tokens JWT
- `LaravelPasswordHasher` - Hash de senhas com bcrypt
- `UuidGenerator` - Geração de IDs UUID
- `LaravelLogger` - Logging via Laravel

**Service Providers:**
- `RepositoryServiceProvider` - Binding de repositórios
- `InfrastructureServiceProvider` - Binding de serviços de infraestrutura

### 4. **Http Layer** (`app/Http/`)
Interface HTTP da aplicação:

**Controllers:**
- `AuthController` - Registro, login, logout e obter usuário autenticado
- `CustomerController` - CRUD completo de clientes

**Requests (Validação):**
- `RegisterRequest` - Validação de registro
- `LoginRequest` - Validação de login
- `CreateCustomerRequest` - Validação de criação de cliente
- `UpdateCustomerRequest` - Validação de atualização de cliente
- `UpdateCustomerAddressRequest` - Validação de atualização de endereço

**Resources (Serialização):**
- `CustomerResource` - Serialização de clientes
- `UserResource` - Serialização de usuários
- `AuthTokenResource` - Serialização de tokens

### Fluxo de Autorização

O projeto implementa segurança através de:
- **JWT (JSON Web Tokens)** para autenticação
- **Verificação de propriedade** - Usuários só podem acessar/modificar seus próprios clientes
- **Logging de tentativas não autorizadas**

Exemplo de fluxo:
```
Cliente faz requisição com token JWT
    ↓
Controller extrai user_id do token autenticado
    ↓
UseCase valida se o cliente pertence ao usuário
    ↓
Se não pertencer, lança UnauthorizedException
    ↓
Resposta 403 ao cliente
```

### Padrões Utilizados

- **Repository Pattern** - Abstração de persistência via interfaces
- **Dependency Injection** - Injeção de dependências via Service Providers
- **DTO Pattern** - Transferência de dados entre camadas
- **Value Objects** - Encapsulamento de valores com comportamento
- **Use Case Pattern** - Orquestração de lógica de domínio
- **Exception Handling** - Exceções de domínio para diferentes cenários

---

## 🧪 Testes

```bash
# Rodar testes
docker-compose exec app composer test

# Testes com cobertura de código
docker-compose exec app composer test:coverage
```

---

## 📚 Documentação da API

A documentação completa está em `docs/openapi.yaml` (OpenAPI 3.0).

Para executar a documentação interativa execute:
```
npx swagger-ui-watcher docs/openapi.yaml --port=8001
```

**Endpoints principais:**

- `POST /api/v1/auth/register` - Registrar novo usuário
- `POST /api/v1/auth/login` - Fazer login
- `GET /api/v1/auth/me` - Dados do usuário autenticado
- `GET /api/v1/customers` - Listar clientes
- `POST /api/v1/customers` - Criar cliente
- `GET /api/v1/customers/{id}` - Obter cliente
- `PATCH /api/v1/customers/{id}` - Atualizar cliente
- `DELETE /api/v1/customers/{id}` - Deletar cliente

---

**Postman collection**

Baixe a coleção de endpoints do postman para testar: [CustomerManager-Collection](https://drive.google.com/file/d/12Fy6cmDqkAsXy6v6kwGME89KTHIkhIPa/view?usp=drive_link)

## 🎮 Comandos Úteis

```bash
# Ver status dos containers
docker-compose ps

# Ver logs
docker-compose logs -f app

# Acessar bash do container
docker-compose exec app bash

# Executar artisan commands
docker-compose exec app php artisan migrate:fresh

# Parar containers
docker-compose down
```

---