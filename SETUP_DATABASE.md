 🔧 Guia de Configuração do Banco de Dados

## Problema: Connection Refused

O erro `SQLSTATE[HY000] [2002] Connection refused` indica que o MySQL não está rodando ou não está acessível.

## Soluções

### Opção 1: Usar SQLite (Mais Rápido para Desenvolvimento)

SQLite é mais simples e não requer servidor separado. Ideal para desenvolvimento local.

1. **Edite o arquivo `.env`** e configure:

```env
DB_CONNECTION=sqlite
# Comente ou remova as linhas do MySQL:
# DB_HOST=127.0.0.1
# DB_PORT=3306
# DB_DATABASE=prospectafreela
# DB_USERNAME=root
# DB_PASSWORD=
```

2. **Crie o arquivo SQLite** (se não existir):

```bash
touch database/database.sqlite
```

3. **Execute as migrations**:

```bash
php artisan migrate
```

✅ **Pronto!** O SQLite está configurado e funcionando.

---

### Opção 2: Configurar MySQL no WSL

Se preferir usar MySQL, siga estes passos:

#### 1. Instalar MySQL no WSL

```bash
sudo apt update
sudo apt install mysql-server
```

#### 2. Iniciar o serviço MySQL

```bash
sudo service mysql start
# ou
sudo systemctl start mysql
```

#### 3. Configurar MySQL (primeira vez)

```bash
sudo mysql_secure_installation
```

#### 4. Criar banco de dados e usuário

```bash
sudo mysql -u root
```

No prompt do MySQL, execute:

```sql
CREATE DATABASE prospectafreela CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'prospectafreela'@'localhost' IDENTIFIED BY 'sua_senha_aqui';
GRANT ALL PRIVILEGES ON prospectafreela.* TO 'prospectafreela'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

#### 5. Configurar o `.env`

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=prospectafreela
DB_USERNAME=prospectafreela
DB_PASSWORD=sua_senha_aqui
```

#### 6. Testar a conexão

```bash
php artisan migrate
```

#### 7. Verificar se MySQL está rodando

```bash
sudo service mysql status
# ou
sudo systemctl status mysql
```

Se não estiver rodando:

```bash
sudo service mysql start
# ou
sudo systemctl start mysql
```

---

### Opção 3: Usar Laravel Sail (Docker)

Laravel Sail já vem com MySQL configurado via Docker.

1. **Instale o Docker Desktop** (se ainda não tiver)

2. **Inicie o Sail**:

```bash
./vendor/bin/sail up -d
```

3. **Execute as migrations**:

```bash
./vendor/bin/sail artisan migrate
```

O Sail já configura tudo automaticamente!

---

## Verificar Configuração Atual

Para ver qual banco está configurado:

```bash
php artisan tinker
```

No tinker:

```php
config('database.default')
config('database.connections.mysql')
```

---

## Recomendação

Para desenvolvimento local, **recomendo usar SQLite** (Opção 1) pois é:
- ✅ Mais rápido de configurar
- ✅ Não requer servidor separado
- ✅ Perfeito para desenvolvimento
- ✅ Funciona perfeitamente com o Laravel

Para produção, use MySQL ou PostgreSQL.

---

## Troubleshooting

### MySQL não inicia

```bash
# Verificar logs
sudo tail -f /var/log/mysql/error.log

# Tentar reiniciar
sudo service mysql restart
```

### Erro de permissão

```bash
# Dar permissões ao usuário
sudo chown -R $USER:$USER database/
```

### Limpar cache de configuração

```bash
php artisan config:clear
php artisan cache:clear
```

---

## Próximos Passos

Após configurar o banco:

1. ✅ Execute as migrations: `php artisan migrate`
2. ✅ Inicie o servidor: `php artisan serve`
3. ✅ Inicie o queue worker: `php artisan queue:work --queue=prospecting`

