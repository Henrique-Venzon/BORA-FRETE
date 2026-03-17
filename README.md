# 🚛 BORAFRETE - Sistema de Logística de Fretes

Sistema completo de gestão de fretes e logística desenvolvido em PHP, MySQL e JavaScript Vanilla.

---

## 📋 Tecnologias Utilizadas

- **Backend**: PHP 7.4+ com PDO
- **Banco de Dados**: MySQL 5.7+
- **Frontend**: HTML5, CSS3, JavaScript Vanilla
- **Design**: Glassmorphism com cores azuis
- **API Externa**: IBGE (Estados e Municípios)

---

## 🚀 Instalação

### 1. Requisitos

- XAMPP, WAMP, MAMP ou servidor PHP local
- PHP 7.4 ou superior
- MySQL 5.7 ou superior
- Navegador moderno (Chrome, Firefox, Edge)

### 2. Configurar o Banco de Dados

1. Abra o phpMyAdmin
2. Crie um novo banco de dados chamado `borafrete`
3. Importe o arquivo `database.sql` localizado na raiz do projeto
4. O SQL criará todas as tabelas e dados de teste automaticamente

**Usuários de Teste:**

| Tipo | Email | Senha |
|------|-------|-------|
| Motorista | motorista@borafrete.com | 123456 |
| Transportadora | transportadora@borafrete.com | 123456 |

### 3. Configurar o Projeto

1. Copie a pasta do projeto para o diretório do servidor web:
   - XAMPP: `C:\xampp\htdocs\borafrete\`
   - WAMP: `C:\wamp64\www\borafrete\`
   - MAMP: `/Applications/MAMP/htdocs/borafrete/`

2. Edite o arquivo `config/config.php` e ajuste as configurações:

```php
// Configurações do banco de dados
define('DB_HOST', 'localhost');
define('DB_NAME', 'borafrete');
define('DB_USER', 'root');        // Ajuste se necessário
define('DB_PASS', '');            // Ajuste se necessário

// URL base do sistema (IMPORTANTE!)
define('BASE_URL', 'http://localhost/borafrete/');
```

3. Certifique-se de que a pasta `public/uploads/` tem permissão de escrita:
   - No Windows: clique com botão direito > Propriedades > Segurança
   - No Linux/Mac: `chmod -R 755 public/uploads/`

### 4. Acessar o Sistema

1. Inicie o servidor Apache e MySQL
2. Acesse: `http://localhost/borafrete/`
3. Faça login com um dos usuários de teste

---

## 📁 Estrutura do Projeto

```
borafrete/
│
├── config/
│   └── config.php                  # Configurações e conexão PDO
│
├── public/
│   ├── css/
│   │   └── style.css               # Estilos (Glassmorphism)
│   ├── js/
│   │   ├── main.js                 # JavaScript principal + lógica de placas
│   │   └── ibge.js                 # Integração API IBGE
│   ├── img/                        # Imagens do sistema
│   └── uploads/                    # Upload de fotos de veículos
│
├── views/
│   ├── layout/
│   │   ├── header.php              # Cabeçalho do sistema
│   │   ├── footer.php              # Rodapé
│   │   └── sidebar.php             # Menu lateral (futuro)
│   ├── dashboard.php               # Dashboard principal
│   ├── cadastro-veiculo.php        # Cadastro de veículos
│   ├── cadastro-oferta.php         # Cadastro de ofertas
│   └── perfil.php                  # Perfil do usuário
│
├── processamento/
│   ├── auth.php                    # Autenticação de usuários
│   ├── logout.php                  # Logout
│   ├── salvar_veiculo.php          # Processamento de veículos
│   ├── salvar_oferta.php           # Processamento de ofertas
│   └── atualizar_disponibilidade.php  # AJAX disponibilidade
│
├── database.sql                    # Script SQL completo
├── index.php                       # Página de login
└── README.md                       # Este arquivo
```

---

## ⚙️ Funcionalidades

### ✅ Sistema de Login
- Autenticação segura com `password_hash`
- Sessões PHP
- Proteção de rotas

### ✅ Dashboard
- Visualização de veículos cadastrados
- Mapa integrado (Google Maps)
- Toggle de disponibilidade de veículo
- Cards com design glassmorphism

### ✅ Cadastro de Veículos
- Tipos: Van, Fiorino, 3/4, Toco, Truck, Carreta, Rodotrem
- **Lógica de Placas Dinâmica:**
  - Van/Fiorino/3/4/Toco/Truck: 1 placa
  - Carreta: 2 placas
  - Rodotrem: 3 placas
- Upload de foto
- Capacidades (peso, volume, pallets)

### ✅ Cadastro de Ofertas
- Seleção de origem/destino via **API IBGE**
- Datas e horários de carregamento/entrega
- Tipos de carga (seca, refrigerada, congelada, etc)
- Financeiro:
  - Frete a combinar (toggle)
  - Valor + pedágio incluso
  - Tipo e fator de pagamento

### ✅ Perfil de Usuário
- Edição de dados pessoais
- Alteração de senha
- Informações de motorista (MOPP, CNH)

---

## 🎨 Design

- **Estilo**: Glassmorphism (blur + transparência)
- **Cores**: Azul #1E3A8A + Azul Claro #4A90E2
- **Bordas**: Arredondadas (20px)
- **Sombras**: Suaves e modernas
- **Responsivo**: Mobile, Tablet e Desktop

---

## 🔒 Segurança

- ✅ Senhas criptografadas com `password_hash`
- ✅ Prepared Statements (PDO) contra SQL Injection
- ✅ Sanitização de dados com `htmlspecialchars`
- ✅ Validação de sessões
- ✅ Proteção de rotas (verificarLogin)

---

## 📡 API IBGE

O sistema integra automaticamente com a API oficial do IBGE para:

- Carregar todos os estados brasileiros
- Carregar municípios por estado
- Cache inteligente para melhor performance
- Pré-carregamento de estados mais populosos

**Endpoints utilizados:**
- Estados: `https://servicodados.ibge.gov.br/api/v1/localidades/estados`
- Municípios: `https://servicodados.ibge.gov.br/api/v1/localidades/estados/{UF}/municipios`

---

## 🐛 Resolução de Problemas

### Erro: "Erro na conexão com o banco de dados"
- Verifique se o MySQL está rodando
- Confirme usuário e senha no `config.php`
- Certifique-se que o banco `borafrete` existe

### Erro: "Call to undefined function password_hash"
- Atualize o PHP para versão 7.4 ou superior

### Upload de imagens não funciona
- Verifique permissões da pasta `public/uploads/`
- No `php.ini`, certifique que `file_uploads = On`

### API IBGE não carrega
- Verifique conexão com internet
- Abra o console do navegador (F12) para ver erros
- A API é pública e não requer autenticação

---

## 📝 Próximas Melhorias

- [ ] Sistema de notificações em tempo real
- [ ] Chat entre motoristas e transportadoras
- [ ] Histórico de fretes realizados
- [ ] Avaliações e reviews
- [ ] Sistema de pagamento integrado
- [ ] App mobile (PWA)
- [ ] Rastreamento em tempo real

---

## 👨‍💻 Desenvolvimento

**Arquitetura:** MVC simplificado
**Padrão de Código:** PSR-12
**Banco de Dados:** Normalizado (3FN)

---

## 📄 Licença

Este projeto foi desenvolvido para fins educacionais e comerciais.

---

## 🆘 Suporte

Para dúvidas ou problemas, consulte a documentação inline no código ou abra uma issue.

---

**Desenvolvido com ❤️ em PHP puro**
