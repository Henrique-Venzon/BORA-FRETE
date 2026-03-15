# Guia Rápido de Instalação

## Requisitos Mínimos
- PHP 7.4 ou superior
- MySQL 5.7 ou superior
- Apache ou Nginx (ou PHP built-in server para desenvolvimento)

## Passo 1: Banco de Dados

### 1.1 Criar o banco de dados
```sql
CREATE DATABASE logistica_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

### 1.2 Importar as tabelas
```bash
mysql -u root -p logistica_db < database.sql
```

Ou use phpMyAdmin:
1. Acesse phpMyAdmin
2. Selecione o banco `logistica_db`
3. Vá em "Importar"
4. Selecione o arquivo `database.sql`
5. Clique em "Executar"

## Passo 2: Configuração

### 2.1 Editar config/config.php

Abra o arquivo `config/config.php` e configure:

```php
// Configurações de Banco de Dados
define('DB_HOST', 'localhost');      // Host do MySQL
define('DB_NAME', 'logistica_db');   // Nome do banco
define('DB_USER', 'root');           // Usuário do MySQL
define('DB_PASS', 'sua_senha');      // Senha do MySQL

// URL Base do Sistema
define('BASE_URL', 'http://localhost');
```

**IMPORTANTE:** Ajuste o `BASE_URL` conforme seu ambiente:
- Desenvolvimento local: `http://localhost:80`
- Servidor: `https://seudominio.com.br`

## Passo 3: Executar o Sistema

### Opção A: PHP Built-in Server (Desenvolvimento)
```bash
php -S localhost
```

Ou usando npm:
```bash
npm start
```

Acesse: `http://localhost`

### Opção B: Apache

#### Usando VirtualHost
Crie um arquivo de configuração em `/etc/apache2/sites-available/logistica.conf`:

```apache
<VirtualHost *:80>
    ServerName logistica.local
    DocumentRoot /caminho/completo/do/projeto

    <Directory /caminho/completo/do/projeto>
        AllowOverride All
        Require all granted
    </Directory>

    ErrorLog ${APACHE_LOG_DIR}/logistica_error.log
    CustomLog ${APACHE_LOG_DIR}/logistica_access.log combined
</VirtualHost>
```

Ative o site:
```bash
sudo a2ensite logistica.conf
sudo systemctl reload apache2
```

Adicione ao `/etc/hosts`:
```
127.0.0.1   logistica.local
```

Acesse: `http://logistica.local`

#### Usando pasta do Apache
Copie o projeto para `/var/www/html/logistica/`

Acesse: `http://localhost/logistica`

### Opção C: Nginx

Configuração básica (`/etc/nginx/sites-available/logistica`):

```nginx
server {
    listen 80;
    server_name logistica.local;
    root /caminho/completo/do/projeto;
    index index.php;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php7.4-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\. {
        deny all;
    }
}
```

## Passo 4: Primeiro Acesso

### Credenciais Padrão
- **Email:** admin@fretebras.com.br
- **Senha:** admin123

### Após o Login
1. Acesse "Meu Perfil" e atualize os dados
2. Cadastre veículos em "Cadastrar Veículo"
3. Cadastre ofertas em "Cadastrar Oferta"

## Verificação de Instalação

### Checklist
- [ ] Banco de dados criado e tabelas importadas
- [ ] Arquivo `config/config.php` configurado corretamente
- [ ] BASE_URL configurada corretamente
- [ ] PHP 7.4+ instalado
- [ ] MySQL rodando
- [ ] Extensões PHP necessárias (PDO, PDO_MySQL) habilitadas
- [ ] Login funcionando

### Testando a Conexão

Crie um arquivo `teste-conexao.php` na raiz:

```php
<?php
require_once 'config/config.php';

try {
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM users");
    $result = $stmt->fetch();
    echo "✅ Conexão OK! Total de usuários: " . $result['total'];
} catch (PDOException $e) {
    echo "❌ Erro: " . $e->getMessage();
}
?>
```

Acesse: `http://localhost:8000/teste-conexao.php`

Se ver "✅ Conexão OK!", está tudo certo!

**Depois delete o arquivo `teste-conexao.php` por segurança.**

## Problemas Comuns

### Erro: "Access denied for user"
- Verifique usuário e senha do MySQL em `config/config.php`
- Verifique se o usuário tem permissões no banco

### Erro: "Call to undefined function pdo_mysql"
```bash
# Ubuntu/Debian
sudo apt-get install php-mysql
sudo systemctl restart apache2

# CentOS
sudo yum install php-mysql
sudo systemctl restart httpd
```

### Erro: "Base table or view not found"
- Execute novamente o `database.sql`
- Verifique se o banco está selecionado corretamente

### Página em branco
- Verifique os logs de erro do PHP
- Habilite display_errors em desenvolvimento:
```php
ini_set('display_errors', 1);
error_reporting(E_ALL);
```

### CSS/JS não carregam
- Verifique o `BASE_URL` em `config/config.php`
- Verifique permissões da pasta `public/`

## Segurança em Produção

### Antes de colocar em produção:

1. **Desabilitar erros no navegador:**
```php
// Em config/config.php
error_reporting(0);
ini_set('display_errors', 0);
```

2. **Alterar senha padrão:**
- Faça login e altere a senha do admin

3. **Configurar HTTPS:**
- Use certificado SSL (Let's Encrypt)

4. **Proteger arquivos:**
```bash
chmod 644 config/config.php
chmod 755 public/
```

5. **Backup regular:**
- Configure backup automático do banco de dados

## Suporte

Para mais informações, consulte:
- `README.md` - Documentação completa
- `RESUMO_TECNICO.md` - Detalhes técnicos
- `database.sql` - Estrutura do banco

## Comandos Úteis

```bash
# Iniciar servidor de desenvolvimento
npm start

# Verificar versão do PHP
php -v

# Verificar extensões do PHP
php -m | grep -i pdo

# Conectar ao MySQL
mysql -u root -p

# Backup do banco
mysqldump -u root -p logistica_db > backup.sql

# Restaurar backup
mysql -u root -p logistica_db < backup.sql
```

---

**Sistema pronto para uso!** 🚀
