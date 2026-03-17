<?php
/**
 * BORAFRETE - Sistema de Chat
 */
require_once '../config/config.php';
verificarLogin();

$pageTitle = 'Mensagens';

// Buscar conversas do usuário
$stmt = $pdo->prepare("
    SELECT DISTINCT
        CASE
            WHEN m.remetente_id = ? THEN m.destinatario_id
            ELSE m.remetente_id
        END as contato_id,
        u.nome_razao_social as contato_nome,
        u.tipo_perfil as contato_tipo,
        (SELECT mensagem FROM mensagens
         WHERE (remetente_id = ? AND destinatario_id = contato_id)
            OR (remetente_id = contato_id AND destinatario_id = ?)
         ORDER BY created_at DESC LIMIT 1) as ultima_mensagem,
        (SELECT created_at FROM mensagens
         WHERE (remetente_id = ? AND destinatario_id = contato_id)
            OR (remetente_id = contato_id AND destinatario_id = ?)
         ORDER BY created_at DESC LIMIT 1) as ultima_data,
        (SELECT COUNT(*) FROM mensagens
         WHERE remetente_id = contato_id
           AND destinatario_id = ?
           AND lida = FALSE) as nao_lidas
    FROM mensagens m
    JOIN usuarios u ON u.id = CASE
        WHEN m.remetente_id = ? THEN m.destinatario_id
        ELSE m.remetente_id
    END
    WHERE m.remetente_id = ? OR m.destinatario_id = ?
    ORDER BY ultima_data DESC
");

$stmt->execute([
    $_SESSION['usuario_id'],
    $_SESSION['usuario_id'],
    $_SESSION['usuario_id'],
    $_SESSION['usuario_id'],
    $_SESSION['usuario_id'],
    $_SESSION['usuario_id'],
    $_SESSION['usuario_id'],
    $_SESSION['usuario_id'],
    $_SESSION['usuario_id']
]);

$conversas = $stmt->fetchAll();

// Se tem ID de contato, buscar mensagens
$contato_id = (int)($_GET['contato'] ?? 0);
$mensagens_contato = [];
$contato_info = null;

if ($contato_id > 0) {
    // Buscar info do contato
    $stmtContato = $pdo->prepare("SELECT * FROM usuarios WHERE id = ?");
    $stmtContato->execute([$contato_id]);
    $contato_info = $stmtContato->fetch();

    // Buscar mensagens
    $stmtMsgs = $pdo->prepare("
        SELECT m.*, u.nome_razao_social as remetente_nome
        FROM mensagens m
        JOIN usuarios u ON u.id = m.remetente_id
        WHERE (m.remetente_id = ? AND m.destinatario_id = ?)
           OR (m.remetente_id = ? AND m.destinatario_id = ?)
        ORDER BY m.created_at ASC
    ");

    $stmtMsgs->execute([
        $_SESSION['usuario_id'],
        $contato_id,
        $contato_id,
        $_SESSION['usuario_id']
    ]);

    $mensagens_contato = $stmtMsgs->fetchAll();

    // Marcar como lidas
    $stmtLer = $pdo->prepare("
        UPDATE mensagens SET lida = TRUE
        WHERE remetente_id = ? AND destinatario_id = ?
    ");
    $stmtLer->execute([$contato_id, $_SESSION['usuario_id']]);
}

require_once 'layout/header.php';
?>

<div class="page-container">

    <div class="page-header">
        <h1>💬 Mensagens</h1>
        <p>Converse com motoristas e transportadoras</p>
    </div>

    <div class="chat-container glass-card">

        <!-- Lista de Conversas -->
        <div class="chat-sidebar">
            <div class="sidebar-header">
                <h3>Conversas</h3>
            </div>

            <div class="conversas-list">
                <?php if (empty($conversas)): ?>
                    <div class="empty-conversations">
                        <p>Nenhuma conversa ainda</p>
                        <small>Manifeste interesse em uma carga para iniciar</small>
                    </div>
                <?php else: ?>
                    <?php foreach ($conversas as $conversa): ?>
                        <a href="?contato=<?php echo $conversa['contato_id']; ?>"
                           class="conversa-item <?php echo $contato_id === (int)$conversa['contato_id'] ? 'active' : ''; ?>">

                            <div class="conversa-avatar">
                                <?php echo strtoupper(substr($conversa['contato_nome'], 0, 1)); ?>
                            </div>

                            <div class="conversa-info">
                                <div class="conversa-nome">
                                    <?php echo htmlspecialchars($conversa['contato_nome']); ?>
                                    <span class="tipo-badge"><?php echo ucfirst($conversa['contato_tipo']); ?></span>
                                </div>
                                <div class="conversa-preview">
                                    <?php echo htmlspecialchars(substr($conversa['ultima_mensagem'], 0, 50)) . '...'; ?>
                                </div>
                                <div class="conversa-tempo">
                                    <?php echo date('d/m H:i', strtotime($conversa['ultima_data'])); ?>
                                </div>
                            </div>

                            <?php if ($conversa['nao_lidas'] > 0): ?>
                                <div class="badge-nao-lidas"><?php echo $conversa['nao_lidas']; ?></div>
                            <?php endif; ?>

                        </a>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>

        <!-- Área de Chat -->
        <div class="chat-main">
            <?php if ($contato_info): ?>

                <!-- Header do Chat -->
                <div class="chat-header">
                    <div class="chat-header-info">
                        <div class="chat-avatar">
                            <?php echo strtoupper(substr($contato_info['nome_razao_social'], 0, 1)); ?>
                        </div>
                        <div>
                            <h4><?php echo htmlspecialchars($contato_info['nome_razao_social']); ?></h4>
                            <span class="status-online">Online</span>
                        </div>
                    </div>
                    <div class="chat-actions">
                        <a href="tel:<?php echo preg_replace('/[^0-9]/', '', $contato_info['telefone']); ?>" class="btn-icon" title="Ligar">
                            📞
                        </a>
                    </div>
                </div>

                <!-- Mensagens -->
                <div class="chat-messages" id="chatMessages">
                    <?php foreach ($mensagens_contato as $msg): ?>
                        <div class="mensagem <?php echo $msg['remetente_id'] === $_SESSION['usuario_id'] ? 'enviada' : 'recebida'; ?>">
                            <div class="mensagem-conteudo">
                                <?php echo nl2br(htmlspecialchars($msg['mensagem'])); ?>
                            </div>
                            <div class="mensagem-hora">
                                <?php echo date('H:i', strtotime($msg['created_at'])); ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <!-- Input de Mensagem -->
                <div class="chat-input">
                    <form id="formEnviarMensagem" onsubmit="enviarMensagem(event)">
                        <input type="hidden" name="destinatario_id" value="<?php echo $contato_id; ?>">
                        <textarea
                            name="mensagem"
                            id="mensagemInput"
                            placeholder="Digite sua mensagem..."
                            rows="1"
                            required
                        ></textarea>
                        <button type="submit" class="btn btn-primary">
                            Enviar
                        </button>
                    </form>
                </div>

            <?php else: ?>
                <div class="chat-empty">
                    <div style="font-size: 80px; margin-bottom: 20px;">💬</div>
                    <h3>Selecione uma conversa</h3>
                    <p>Escolha uma conversa na lista à esquerda</p>
                </div>
            <?php endif; ?>
        </div>

    </div>

</div>

<style>
.chat-container {
    display: grid;
    grid-template-columns: 350px 1fr;
    height: calc(100vh - 250px);
    min-height: 600px;
    padding: 0;
    overflow: hidden;
}

.chat-sidebar {
    border-right: 2px solid var(--cinza-medio);
    display: flex;
    flex-direction: column;
}

.sidebar-header {
    padding: 20px;
    border-bottom: 2px solid var(--cinza-medio);
}

.sidebar-header h3 {
    margin: 0;
    font-size: 18px;
}

.conversas-list {
    overflow-y: auto;
    flex: 1;
}

.conversa-item {
    display: flex;
    gap: 12px;
    padding: 16px 20px;
    border-bottom: 1px solid var(--cinza-medio);
    cursor: pointer;
    transition: background 0.2s;
    text-decoration: none;
    color: inherit;
    position: relative;
}

.conversa-item:hover {
    background: var(--cinza-claro);
}

.conversa-item.active {
    background: rgba(74, 144, 226, 0.1);
    border-left: 4px solid var(--azul-claro);
}

.conversa-avatar {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    background: var(--azul-claro);
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 20px;
    font-weight: 700;
    flex-shrink: 0;
}

.conversa-info {
    flex: 1;
    min-width: 0;
}

.conversa-nome {
    font-weight: 600;
    margin-bottom: 4px;
    display: flex;
    align-items: center;
    gap: 8px;
}

.tipo-badge {
    background: var(--cinza-medio);
    padding: 2px 8px;
    border-radius: 8px;
    font-size: 11px;
    text-transform: uppercase;
}

.conversa-preview {
    font-size: 13px;
    color: var(--cinza-escuro);
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

.conversa-tempo {
    font-size: 11px;
    color: var(--cinza-escuro);
    margin-top: 4px;
}

.badge-nao-lidas {
    position: absolute;
    right: 20px;
    top: 50%;
    transform: translateY(-50%);
    background: var(--azul-claro);
    color: white;
    width: 24px;
    height: 24px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 12px;
    font-weight: 700;
}

.chat-main {
    display: flex;
    flex-direction: column;
}

.chat-header {
    padding: 20px;
    border-bottom: 2px solid var(--cinza-medio);
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.chat-header-info {
    display: flex;
    gap: 12px;
    align-items: center;
}

.chat-avatar {
    width: 45px;
    height: 45px;
    border-radius: 50%;
    background: var(--azul-principal);
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 18px;
    font-weight: 700;
}

.status-online {
    font-size: 12px;
    color: var(--verde-sucesso);
}

.chat-messages {
    flex: 1;
    overflow-y: auto;
    padding: 20px;
    display: flex;
    flex-direction: column;
    gap: 12px;
}

.mensagem {
    max-width: 70%;
    animation: slideIn 0.3s ease;
}

.mensagem.enviada {
    align-self: flex-end;
}

.mensagem.recebida {
    align-self: flex-start;
}

.mensagem-conteudo {
    padding: 12px 16px;
    border-radius: 16px;
    word-wrap: break-word;
}

.mensagem.enviada .mensagem-conteudo {
    background: linear-gradient(135deg, var(--azul-claro), var(--azul-principal));
    color: white;
    border-bottom-right-radius: 4px;
}

.mensagem.recebida .mensagem-conteudo {
    background: var(--cinza-claro);
    color: var(--preto);
    border-bottom-left-radius: 4px;
}

.mensagem-hora {
    font-size: 11px;
    color: var(--cinza-escuro);
    margin-top: 4px;
    text-align: right;
}

.mensagem.recebida .mensagem-hora {
    text-align: left;
}

.chat-input {
    padding: 20px;
    border-top: 2px solid var(--cinza-medio);
}

.chat-input form {
    display: flex;
    gap: 12px;
}

.chat-input textarea {
    flex: 1;
    padding: 12px 16px;
    border: 2px solid var(--cinza-medio);
    border-radius: 24px;
    resize: none;
    font-family: inherit;
    font-size: 14px;
}

.chat-input textarea:focus {
    outline: none;
    border-color: var(--azul-claro);
}

.chat-empty {
    flex: 1;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    color: var(--cinza-escuro);
}

.empty-conversations {
    padding: 40px 20px;
    text-align: center;
    color: var(--cinza-escuro);
}

@media (max-width: 768px) {
    .chat-container {
        grid-template-columns: 1fr;
    }

    .chat-sidebar {
        display: <?php echo $contato_id > 0 ? 'none' : 'flex'; ?>;
    }

    .chat-main {
        display: <?php echo $contato_id > 0 ? 'flex' : 'none'; ?>;
    }
}
</style>

<script>
function enviarMensagem(e) {
    e.preventDefault();

    const formData = new FormData(e.target);

    fetch(BASE_URL + 'processamento/enviar_mensagem.php', {
        method: 'POST',
        body: formData
    })
    .then(r => r.json())
    .then(data => {
        if (data.sucesso) {
            // Recarregar página
            window.location.reload();
        } else {
            alert('Erro ao enviar mensagem');
        }
    });
}

// Auto-scroll para última mensagem
const chatMessages = document.getElementById('chatMessages');
if (chatMessages) {
    chatMessages.scrollTop = chatMessages.scrollHeight;
}

// Auto-resize textarea
const textarea = document.getElementById('mensagemInput');
if (textarea) {
    textarea.addEventListener('input', function() {
        this.style.height = 'auto';
        this.style.height = (this.scrollHeight) + 'px';
    });
}
</script>

<?php require_once 'layout/footer.php'; ?>
