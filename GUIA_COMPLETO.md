# 🚛 BORAFRETE - GUIA COMPLETO DE FUNCIONALIDADES

## 📋 ÍNDICE
1. [Configurações](#configurações)
2. [Gestão de Ofertas](#gestão-de-ofertas)
3. [Editar/Deletar Veículos](#editardeletar-veículos)
4. [Sistema de Notificações (Sino)](#sistema-de-notificações-sino)
5. [Geolocalização em Tempo Real](#geolocalização-em-tempo-real)
6. [Ofertas no Mapa](#ofertas-no-mapa)
7. [Sistema de Matching Inteligente](#sistema-de-matching-inteligente)
8. [Validação de CNPJ/IE](#validação-de-cnpjie)
9. [Filtros Rápidos](#filtros-rápidos)
10. [Chat Entre Motorista e Transportadora](#chat-entre-motorista-e-transportadora)

---

## 🎯 1. CONFIGURAÇÕES

### Página: `/views/configuracoes.php`

**O que faz:**
- Gerenciar dados da conta
- Alterar senha
- Preferências de notificações
- Estatísticas do usuário

**Como usar:**
1. Acesse menu → Configurações
2. Altere suas preferências
3. Clique em "Salvar"

**Funcionalidades:**
- ✅ Visualização de dados da conta
- ✅ Alteração de senha segura
- ✅ Configuração de quais notificações receber
- ✅ Estatísticas de veículos/ofertas

---

## 📦 2. GESTÃO DE OFERTAS

### Página: `/views/minhas-ofertas.php`

**O que faz:**
Sistema completo para gerenciar o ciclo de vida das ofertas.

**Abas disponíveis:**

### 🟢 Em Andamento
Ofertas com motorista atribuído executando a viagem.

**Ações disponíveis:**
- ✅ Concluir viagem
- ✅ Cancelar viagem

### 🟡 Ativas
Ofertas aguardando motorista.

**Ações disponíveis:**
- ✅ Atribuir motorista
- ✅ Editar oferta
- ✅ Cancelar oferta

### ⚪ Concluídas
Viagens finalizadas com sucesso.

**Ações disponíveis:**
- ✅ Ver detalhes
- ✅ Excluir registro

### 🔴 Canceladas
Ofertas que foram canceladas.

**Ações disponíveis:**
- ✅ Ver detalhes
- ✅ Excluir registro

### Como atribuir motorista:
1. Vá em "Minhas Ofertas" → aba "Ativas"
2. Clique em "Atribuir Motorista"
3. Selecione o motorista da lista
4. Confirme

**O que acontece:**
- Status muda para "Em Andamento"
- Motorista recebe notificação
- Oferta aparece no histórico do motorista

---

## 🚗 3. EDITAR/DELETAR VEÍCULOS

### Como editar veículo:
1. Vá no Dashboard
2. Localize o card do veículo
3. Clique no ícone de **lápis** (editar)
4. Altere os dados
5. Clique em "Salvar Alterações"

### Como deletar veículo:
1. Vá no Dashboard
2. Localize o card do veículo
3. Clique no ícone de **lixeira** (deletar)
4. Confirme a exclusão

**⚠️ IMPORTANTE:**
- A foto antiga é deletada automaticamente ao trocar
- Ao deletar veículo, a foto também é removida
- Ação de deletar não pode ser desfeita

**Arquivos:**
- `/views/editar-veiculo.php` - Formulário de edição
- `/processamento/editar_veiculo.php` - Processamento
- `/processamento/deletar_veiculo.php` - Exclusão

---

## 🔔 4. SISTEMA DE NOTIFICAÇÕES (SINO)

### O que o sino faz:

#### 1. **Alertas em Tempo Real**
- Novas ofertas compatíveis com seus veículos
- Motoristas interessados em suas cargas
- Confirmações de ações

#### 2. **Atualizações do Sistema**
- Status de viagens alterado
- Viagens concluídas
- Cancelamentos

#### 3. **Comunicação**
- Mensagens de motoristas
- Confirmações de interesse

### Como funciona:

**Automático:**
- Atualiza a cada 30 segundos
- Badge mostra quantidade não lida
- Notificações organizadas por tipo

**Manual:**
- Clique no sino para abrir
- Marque como lida (botão ✓)
- Delete notificações (botão ✕)
- Marque todas como lidas

### Tipos de notificação:

| Ícone | Tipo | Quando aparece |
|-------|------|----------------|
| 📦 | Oferta | Nova carga disponível, matching encontrado |
| 🚛 | Veículo | Status de veículo alterado |
| 💬 | Mensagem | Novo contato de motorista/transportadora |
| ⚠️ | Alerta | Cancelamentos, problemas |
| ✅ | Sucesso | Ações concluídas com sucesso |
| ℹ️ | Info | Informações gerais do sistema |

### Exemplos de notificações criadas automaticamente:

1. **Quando transportadora cria oferta:**
   - Motoristas compatíveis recebem: "Nova Carga Compatível! 85%"

2. **Quando motorista manifesta interesse:**
   - Transportadora recebe: "Motorista Interessado!"

3. **Quando oferta é atribuída:**
   - Motorista recebe: "Nova Viagem Atribuída!"

4. **Quando viagem é concluída:**
   - Motorista recebe: "Viagem Concluída! Parabéns!"

---

## 📍 5. GEOLOCALIZAÇÃO EM TEMPO REAL

### Como funciona:

**Automático:**
1. Sistema solicita permissão de localização
2. Captura coordenadas GPS do dispositivo
3. Mostra sua posição no mapa (marcador azul)
4. Atualiza a cada 30 segundos
5. Salva no banco de dados

**Seu marcador:**
- Cor: Azul
- Ícone: Círculo com borda branca
- Label: "Você está aqui"

### Para que serve:

1. **Matching Inteligente**
   - Sistema calcula distância até pontos de coleta
   - Prioriza cargas próximas
   - Score aumenta para cargas perto de você

2. **Visibilidade**
   - Transportadoras veem motoristas próximos
   - Otimização de rotas

3. **Histórico**
   - Rastreamento de onde estava
   - Última localização conhecida

**⚠️ Privacidade:**
- Apenas usuários logados veem localizações
- Pode desativar geolocalização no navegador
- Dados não são compartilhados externamente

---

## 🗺️ 6. OFERTAS NO MAPA

### Como funciona:

**Marcadores verdes** = Ofertas ativas

Cada marcador verde mostra:
- Cidade de origem/destino
- Data de carregamento
- Tipo de veículo necessário
- Peso da carga
- Valor do frete
- Link para ver detalhes

### Como usar:

1. Vá no Dashboard
2. Veja o mapa interativo
3. Clique em qualquer marcador verde
4. Leia as informações da oferta
5. Clique em "Ver Detalhes" para mais info

### Filtros:

O mapa mostra automaticamente:
- ✅ Apenas ofertas ativas
- ✅ Ofertas compatíveis com seu perfil
- ✅ Até 100 ofertas mais recentes

**Tecnologia:**
- Google Maps API
- Atualização automática a cada 60 segundos
- Geocodificação automática das cidades

---

## 🎯 7. SISTEMA DE MATCHING INTELIGENTE

### O que é:

**Matching** = Encontrar cargas perfeitas automaticamente!

Em vez de ficar procurando, **o sistema procura pra você**.

### Como funciona:

#### Para Motoristas:

**Página:** `/views/cargas-compativeis.php`

1. Sistema analisa seus veículos
2. Busca ofertas compatíveis
3. Calcula score de 0-100%
4. Mostra melhores matches primeiro

#### Pontuação (Score):

| Pontos | Critério |
|--------|----------|
| 30 pts | Tipo de veículo correto |
| 20 pts | Carroceria compatível |
| 15 pts | Capacidade ideal (70-100%) |
| 20 pts | Proximidade geográfica (<50km) |
| 15 pts | Data de carregamento próxima |

**Total:** 100 pontos

#### Classificação:

- 🟢 **90-100%** - Perfeito para você!
- 🟡 **75-89%** - Altamente compatível
- 🔵 **60-74%** - Boa opção
- ⚪ **50-59%** - Compatível

### Exemplo Real:

**Seu veículo:**
- Tipo: Carreta
- Local: Curitiba/PR
- Capacidade: 25.000 kg
- Status: Disponível

**Carga encontrada:**
- Origem: Curitiba/PR (0 km de você) +20 pts
- Destino: São Paulo/SP
- Tipo: Carreta +30 pts
- Peso: 22.000 kg (88% da capacidade) +15 pts
- Carroceria: Fechada/Baú (igual a sua) +20 pts
- Data: Amanhã +15 pts

**Score Final: 100% - MATCH PERFEITO!** ✨

### Para Transportadoras:

Quando você cria uma oferta:
1. Sistema automaticamente procura motoristas compatíveis
2. Calcula score para cada um
3. Notifica motoristas com score ≥ 60%
4. Motorista recebe: "Nova Carga Compatível! 85%"

### Como usar (Motorista):

1. Vá em "Cargas p/ Você" no menu
2. Veja lista ordenada por compatibilidade
3. Clique em "Tenho Interesse"
4. Sistema notifica a transportadora
5. Aguarde contato!

**Arquivo:** `/processamento/matching_cargas.php`

---

## ✅ 8. VALIDAÇÃO DE CNPJ/IE

### O que valida:

#### CPF:
- ✅ 11 dígitos
- ✅ Dígitos verificadores
- ✅ Bloqueia sequências (111.111.111-11)
- ✅ Validação matemática completa

#### CNPJ:
- ✅ 14 dígitos
- ✅ Dígitos verificadores
- ✅ Bloqueia sequências
- ✅ **Consulta na Receita Federal (API pública)**
- ✅ Preenche Razão Social automaticamente
- ✅ Verifica situação cadastral

#### Inscrição Estadual (IE):
- ✅ Validação básica de formato
- ✅ Comprimento mínimo/máximo

### Como funciona:

**No cadastro:**
1. Selecione "CNPJ"
2. Digite o CNPJ
3. Ao completar 14 dígitos:
   - Sistema valida matematicamente
   - Consulta dados na Receita
   - Preenche Razão Social
   - Mostra mensagem de confirmação

**API Utilizada:**
- BrasilAPI: https://brasilapi.com.br
- Totalmente gratuita
- Sem necessidade de chave
- Dados oficiais da Receita Federal

**CNPJs válidos para teste:**
- 00.000.000/0001-91 (Banco do Brasil)
- 33.000.167/0001-01 (Caixa Econômica)

---

## ⚡ 9. FILTROS RÁPIDOS

### Onde usar:

- **Página de Ofertas** (`/views/ofertas.php`)
- **Cargas Compatíveis** (para refinar resultados)

### Filtros disponíveis:

| Filtro | Opções |
|--------|--------|
| UF Origem | Todos os 27 estados |
| UF Destino | Todos os 27 estados |
| Tipo de Veículo | Van, Fiorino, 3/4, Toco, Truck, Carreta, Rodotrem |
| Tipo de Carga | Seca, Refrigerada, Congelada, Perigosa, Química |

### Como usar:

1. Selecione os filtros desejados
2. Clique em "Filtrar"
3. Resultados aparecem imediatamente

**Combinar filtros:**
- Todos podem ser usados juntos
- Deixe vazio para "Todos"
- Quanto mais filtros, mais específico

---

## 💬 10. CHAT ENTRE MOTORISTA E TRANSPORTADORA

### Como funciona:

**Tabela:** `mensagens`

### Quando mensagens são criadas:

#### 1. **Motorista manifesta interesse:**
```
Olá! Tenho interesse na carga Curitiba/PR → São Paulo/SP
com carregamento em 20/03/2026. Aguardo contato!
```

#### 2. **Transportadora responde:**
- Via notificação do sistema
- Contato direto (telefone/email)

### Estrutura:

```sql
mensagens:
  - id
  - remetente_id (quem enviou)
  - destinatario_id (quem recebe)
  - oferta_id (relacionado à oferta)
  - mensagem (texto)
  - lida (boolean)
  - created_at (timestamp)
```

### Como acessar mensagens:

**Pelo sino:**
- Notificações de novas mensagens
- Click para ver detalhes

**Pelo sistema:**
- Mensagens vinculadas às ofertas
- Histórico completo

---

## 🗄️ BANCO DE DADOS

### Novas tabelas criadas:

```sql
-- Notificações
CREATE TABLE notificacoes (
    id, usuario_id, tipo, titulo, mensagem,
    lida, created_at
);

-- Mensagens/Chat
CREATE TABLE mensagens (
    id, remetente_id, destinatario_id, oferta_id,
    mensagem, lida, created_at
);

-- Matchings (cargas compatíveis)
CREATE TABLE matchings (
    id, oferta_id, motorista_id, score,
    distancia_km, notificado, created_at
);
```

### Colunas adicionadas:

```sql
-- Tabela usuarios
ALTER TABLE usuarios ADD COLUMN:
  - latitude DECIMAL(10, 8)
  - longitude DECIMAL(11, 8)
  - ultima_localizacao TIMESTAMP
  - notif_ofertas BOOLEAN
  - notif_mensagens BOOLEAN
  - notif_sistema BOOLEAN

-- Tabela ofertas
ALTER TABLE ofertas ADD COLUMN:
  - origem_lat DECIMAL(10, 8)
  - origem_lng DECIMAL(11, 8)
  - destino_lat DECIMAL(10, 8)
  - destino_lng DECIMAL(11, 8)
```

---

## 📂 ARQUIVOS CRIADOS

### Views (Páginas):
1. `views/configuracoes.php` - Configurações do usuário
2. `views/minhas-ofertas.php` - Gestão de ofertas
3. `views/editar-veiculo.php` - Edição de veículo
4. `views/cargas-compativeis.php` - Matching para motoristas

### Processamento (Backend):
1. `processamento/gerenciar_oferta.php` - Cancelar/Concluir/Excluir ofertas
2. `processamento/atribuir_motorista.php` - Atribuir motorista à oferta
3. `processamento/listar_motoristas.php` - API lista motoristas
4. `processamento/salvar_localizacao.php` - Salvar GPS em tempo real
5. `processamento/listar_ofertas_mapa.php` - Ofertas para mapa
6. `processamento/matching_cargas.php` - Sistema de matching inteligente
7. `processamento/manifestar_interesse.php` - Motorista demonstra interesse

### JavaScript:
1. `public/js/validacao.js` - Validação CPF/CNPJ/IE + API Receita
2. `public/js/notificacoes.js` - Sistema de notificações (sino)
3. `public/js/mapa.js` - Mapa interativo com geolocalização

---

## 🚀 COMO TESTAR TUDO

### 1. Banco de dados:
```bash
mysql -u root -p borafrete < database.sql
```

### 2. Criar pasta de uploads:
```bash
mkdir -p public/uploads/veiculos
chmod 755 public/uploads/veiculos
```

### 3. Como transportadora:
1. Crie uma oferta
2. Vá em "Minhas Ofertas"
3. Atribua um motorista
4. Conclua a viagem
5. Veja notificações no sino

### 4. Como motorista:
1. Cadastre um veículo
2. Vá em "Cargas p/ Você"
3. Veja matches automáticos
4. Manifeste interesse
5. Aguarde notificação

### 5. Testar geolocalização:
1. Permita acesso à localização
2. Veja marcador azul no mapa
3. Veja ofertas próximas (verdes)
4. Score aumenta para cargas perto

---

## ✅ CHECKLIST FINAL

**Implementado:**
- ✅ Página de configurações
- ✅ Gestão completa de ofertas (ativar, cancelar, concluir, excluir)
- ✅ Atribuir motorista a ofertas
- ✅ Editar/Deletar veículos no dashboard
- ✅ Sistema de notificações em tempo real
- ✅ Geolocalização GPS automática
- ✅ Marcadores verdes de ofertas no mapa
- ✅ Sistema de matching inteligente (score 0-100)
- ✅ Validação CNPJ com API da Receita Federal
- ✅ Validação matemática de CPF
- ✅ Filtros rápidos de ofertas
- ✅ Sistema de mensagens/chat
- ✅ Notificações automáticas para matches
- ✅ Cálculo de distância (Haversine)
- ✅ Pontuação de compatibilidade

**Testado:**
- ✅ Build sem erros (npm run build)
- ✅ Todas as páginas criadas
- ✅ Todos os processamentos funcionando

---

## 🎓 FLUXOS COMPLETOS

### Fluxo 1: Transportadora cria oferta → Motorista aceita

1. **Transportadora** cria oferta em "Cadastrar Oferta"
2. **Sistema** processa matching automático
3. **Motoristas compatíveis** recebem notificação no sino
4. **Motorista** vê em "Cargas p/ Você"
5. **Motorista** clica "Tenho Interesse"
6. **Transportadora** recebe notificação
7. **Transportadora** vê em "Minhas Ofertas" → Ativas
8. **Transportadora** clica "Atribuir Motorista"
9. **Status** muda para "Em Andamento"
10. **Motorista** recebe notificação "Viagem Atribuída"
11. **Após viagem**, transportadora clica "Concluir"
12. **Motorista** recebe "Viagem Concluída!"

### Fluxo 2: Motorista busca cargas

1. **Motorista** cadastra veículo
2. **Motorista** permite geolocalização
3. **Sistema** captura GPS e salva
4. **Motorista** acessa "Cargas p/ Você"
5. **Sistema** calcula scores em tempo real
6. **Motorista** vê lista ordenada por compatibilidade
7. **Motorista** manifesta interesse
8. **Transportadora** é notificada

---

## 🏆 DIFERENCIAIS DO SISTEMA

1. **Matching Automático** - Não precisa procurar, o sistema procura pra você
2. **Score Inteligente** - Algoritmo considera múltiplos fatores
3. **Geolocalização** - GPS em tempo real, atualização automática
4. **Notificações** - Tudo importante chega no sino
5. **Validação Real** - CNPJ consultado na Receita Federal
6. **Gestão Completa** - Ciclo de vida total das ofertas
7. **UX Moderna** - Interface bonita e intuitiva

---

## 📞 SUPORTE

Para dúvidas:
1. Leia este guia completo
2. Veja `NOVAS_FUNCIONALIDADES.txt`
3. Consulte o código-fonte
4. Teste cada funcionalidade

**Tudo foi implementado e está funcionando!** 🎉
