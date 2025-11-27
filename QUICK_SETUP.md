# ⚡ Configuração Rápida - ProspectaFreela

## 🚨 Problema: MySQL Connection Refused

O erro indica que o MySQL não está rodando. **Solução mais rápida: usar SQLite!**

## ✅ Solução Rápida (SQLite - 2 minutos)

### Passo 1: Edite o arquivo `.env`

Abra o arquivo `.env` e altere estas linhas:

```env
# ANTES (MySQL):
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=prospectafreela
DB_USERNAME=root
DB_PASSWORD=

# DEPOIS (SQLite):
DB_CONNECTION=sqlite
# DB_HOST=127.0.0.1
# DB_PORT=3306
# DB_DATABASE=prospectafreela
# DB_USERNAME=root
# DB_PASSWORD=
```

### Passo 2: Limpe o cache

```bash
php artisan config:clear
```

### Passo 3: Execute as migrations

```bash
php artisan migrate
```

✅ **Pronto!** O banco está configurado!

---

## 🔍 Verificar se funcionou

```bash
php artisan migrate:status
```

Se mostrar as tabelas, está tudo certo! 🎉

---

## 📝 Próximos Passos

1. **Iniciar o servidor:**
   ```bash
   php artisan serve
   ```

2. **Iniciar o queue worker** (em outro terminal):
   ```bash
   php artisan queue:work --queue=prospecting
   ```

3. **Acessar a aplicação:**
   - Abra: http://127.0.0.1:8000
   - Registre um usuário
   - Comece a usar!

---

## 🐛 Se ainda der erro

1. **Verifique se o arquivo SQLite existe:**
   ```bash
   ls -la database/database.sqlite
   ```
   
   Se não existir, crie:
   ```bash
   touch database/database.sqlite
   ```

2. **Dê permissões:**
   ```bash
   chmod 664 database/database.sqlite
   ```

3. **Limpe tudo e tente novamente:**
   ```bash
   php artisan config:clear
   php artisan cache:clear
   php artisan migrate:fresh
   ```

---

## 💡 Por que SQLite?

- ✅ Não precisa de servidor MySQL rodando
- ✅ Funciona perfeitamente para desenvolvimento
- ✅ Mais rápido de configurar
- ✅ Ideal para MVP e testes

Para produção, você pode migrar para MySQL depois sem problemas!

