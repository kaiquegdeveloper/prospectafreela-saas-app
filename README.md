# ProspectaFreela - MVP SaaS de Prospecção

Plataforma SaaS para prospecção automática de empresas através do Google Maps. O sistema busca informações de contato (telefone, e-mail, WhatsApp) e organiza os dados para facilitar a prospecção de clientes.

## 🚀 Tecnologias

- **Laravel 12** (PHP 8.3+)
- **TailwindCSS** + **Blade**
- **MySQL**
- **Queue Jobs** (Database Queue)
- **Laravel Breeze** (Autenticação)

## 📋 Funcionalidades

### Autenticação
- Login e registro de usuários
- Gerenciamento de perfil
- Sistema de autenticação completo

### Prospecção
- Busca automática no Google Maps por cidade e nicho
- Coleta de dados:
  - Nome da empresa
  - Telefone
  - WhatsApp (quando disponível)
  - E-mail (quando disponível no site)
  - Site
  - Endereço
  - URL do Google Maps
- Processamento em background via Queue Jobs
- Prevenção de duplicatas
- Status de processamento (pending, done, error)

### Dashboard
- Estatísticas de prospects
- Lista de prospects recentes
- Ações rápidas

### Gerenciamento de Prospects
- Listagem paginada
- Busca e filtros
- Visualização de detalhes
- Exportação em CSV
- Exclusão de prospects

### Plano
- Tela de gerenciamento de plano (placeholder para futuro billing)

## 🏗️ Arquitetura

### Estrutura de Pastas

```
app/
├── Http/
│   └── Controllers/
│       └── ProspectController.php
├── Jobs/
│   └── ProcessProspectingJob.php
├── Models/
│   ├── Prospect.php
│   └── User.php
└── Services/
    └── GoogleMapsScraperService.php
```

### Modelos

#### Prospect
- `user_id` - Relacionamento com usuário
- `nome` - Nome da empresa
- `telefone` - Telefone
- `whatsapp` - WhatsApp
- `email` - E-mail
- `site` - Site da empresa
- `endereco` - Endereço
- `cidade` - Cidade
- `nicho` - Nicho do negócio
- `google_maps_url` - URL do Google Maps
- `status` - Status (pending, done, error)

### Services

#### GoogleMapsScraperService
Responsável por:
- Buscar empresas no Google Maps
- Fazer scraping de sites para extrair e-mails e telefones
- Parsing de dados HTML
- Rate limiting e user agents rotativos

### Jobs

#### ProcessProspectingJob
Job que processa a prospecção em background:
- Busca empresas no Google Maps
- Para cada empresa encontrada:
  - Verifica duplicatas
  - Cria registro inicial
  - Busca informações adicionais no site (se disponível)
  - Atualiza status
- Rate limiting entre requisições (2 segundos)

## 📦 Instalação

### Pré-requisitos
- PHP 8.3+
- Composer
- MySQL
- Node.js e NPM

### Passos

1. **Clone o repositório**
```bash
git clone <repository-url>
cd prospectafreela-saas-app
```

2. **Instale as dependências**
```bash
composer install
npm install
```

3. **Configure o ambiente**
```bash
cp .env.example .env
php artisan key:generate
```

4. **Configure o banco de dados no `.env`**
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=prospectafreela
DB_USERNAME=root
DB_PASSWORD=
```

5. **Execute as migrations**
```bash
php artisan migrate
```

6. **Compile os assets**
```bash
npm run build
# ou para desenvolvimento
npm run dev
```

7. **Inicie o servidor**
```bash
php artisan serve
```

8. **Inicie o queue worker** (em outro terminal)
```bash
php artisan queue:work --queue=prospecting
```

## 🔧 Configuração

### Queue

O sistema usa a fila `prospecting` para processar as prospecções. Configure no `.env`:

```env
QUEUE_CONNECTION=database
```

### Rate Limiting

O sistema implementa rate limiting automático:
- 2 segundos entre requisições de scraping
- User agents rotativos para evitar bloqueios

**Nota:** Para produção, considere:
- Usar uma API oficial do Google Maps
- Implementar proxies rotativos
- Aumentar o delay entre requisições
- Monitorar bloqueios e ajustar estratégia

## 📝 Uso

### Criar uma Prospecção

1. Acesse **Prospects** → **Nova Prospecção**
2. Informe a **Cidade** e o **Nicho**
3. Clique em **Iniciar Prospecção**
4. O sistema processará em background
5. Os resultados aparecerão na lista de prospects

### Exportar Dados

1. Acesse a lista de **Prospects**
2. Use os filtros se necessário
3. Clique em **Exportar CSV**
4. O arquivo será baixado com todos os dados

## ⚠️ Limitações e Considerações

### Scraping do Google Maps

A implementação atual usa scraping HTML básico do Google Maps. Para produção, considere:

1. **API Oficial do Google Maps**
   - Mais confiável e estável
   - Requer chave de API
   - Limites de uso baseados em plano

2. **Bibliotecas Especializadas**
   - SerpAPI
   - ScraperAPI
   - Outras soluções de scraping

3. **Melhorias no Parsing**
   - Usar DOMDocument para parsing mais robusto
   - Implementar retry logic
   - Melhorar detecção de dados

### Segurança

- Rate limiting implementado
- Validação de inputs
- Isolamento de dados por usuário
- CSRF protection

### Performance

- Processamento em background
- Paginação de resultados
- Índices no banco de dados
- Cache pode ser implementado para melhorar performance

## 🧪 Testes

```bash
php artisan test
```

## 📄 Licença

Este projeto é open-source e está disponível sob a licença MIT.

## 🤝 Contribuindo

Contribuições são bem-vindas! Sinta-se à vontade para abrir issues ou pull requests.

## 📧 Suporte

Para suporte, abra uma issue no repositório.

---

**Desenvolvido com ❤️ usando Laravel**
