<?php
/**
 * BORAFRETE - Email Helper usando PHP mail() nativo
 * Para usar PHPMailer completo, instale via Composer: composer require phpmailer/phpmailer
 */

class EmailHelper {

    private $host;
    private $port;
    private $username;
    private $password;
    private $from;
    private $fromName;

    public function __construct() {
        $this->host = MAIL_HOST;
        $this->port = MAIL_PORT;
        $this->username = MAIL_USER;
        $this->password = MAIL_PASS;
        $this->from = MAIL_FROM;
        $this->fromName = MAIL_FROM_NAME;
    }

    /**
     * Enviar email usando SMTP
     *
     * @param string $to Email destinatário
     * @param string $subject Assunto
     * @param string $body Corpo do email (HTML)
     * @return bool
     */
    public function enviarEmail($to, $subject, $body) {

        // Headers para email HTML
        $headers = "MIME-Version: 1.0\r\n";
        $headers .= "Content-type: text/html; charset=UTF-8\r\n";
        $headers .= "From: {$this->fromName} <{$this->from}>\r\n";
        $headers .= "Reply-To: {$this->from}\r\n";
        $headers .= "X-Mailer: PHP/" . phpversion();

        // Configurar SMTP se disponível
        if (function_exists('ini_set')) {
            ini_set('SMTP', $this->host);
            ini_set('smtp_port', $this->port);
            ini_set('sendmail_from', $this->from);
        }

        // Enviar email
        $sucesso = mail($to, $subject, $body, $headers);

        if (!$sucesso) {
            error_log("Erro ao enviar email para: $to");
        }

        return $sucesso;
    }

    /**
     * Enviar email de recuperação de senha
     *
     * @param string $to Email destinatário
     * @param string $nome Nome do usuário
     * @param string $token Token de recuperação
     * @return bool
     */
    public function enviarRecuperacaoSenha($to, $nome, $token) {

        $linkRecuperacao = BASE_URL . 'views/redefinir-senha.php?token=' . urlencode($token);

        $subject = 'Recuperação de Senha - BoraFrete';

        $body = $this->templateRecuperacaoSenha($nome, $linkRecuperacao);

        return $this->enviarEmail($to, $subject, $body);
    }

    /**
     * Template HTML para email de recuperação de senha
     */
    private function templateRecuperacaoSenha($nome, $link) {
        return "
        <!DOCTYPE html>
        <html lang='pt-BR'>
        <head>
            <meta charset='UTF-8'>
            <meta name='viewport' content='width=device-width, initial-scale=1.0'>
            <style>
                body {
                    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
                    background-color: #f5f7fa;
                    margin: 0;
                    padding: 0;
                }
                .container {
                    max-width: 600px;
                    margin: 40px auto;
                    background: #ffffff;
                    border-radius: 20px;
                    overflow: hidden;
                    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
                }
                .header {
                    background: linear-gradient(135deg, #1E3A8A 0%, #4A90E2 100%);
                    padding: 40px 30px;
                    text-align: center;
                    color: white;
                }
                .header h1 {
                    margin: 0;
                    font-size: 32px;
                    font-weight: 300;
                }
                .content {
                    padding: 40px 30px;
                }
                .content h2 {
                    color: #1E3A8A;
                    font-size: 24px;
                    margin-top: 0;
                }
                .content p {
                    color: #6B7280;
                    font-size: 16px;
                    line-height: 1.6;
                }
                .button {
                    display: inline-block;
                    background: linear-gradient(135deg, #4A90E2 0%, #1E3A8A 100%);
                    color: white;
                    text-decoration: none;
                    padding: 16px 40px;
                    border-radius: 12px;
                    font-weight: 600;
                    margin: 20px 0;
                }
                .footer {
                    background: #f5f7fa;
                    padding: 30px;
                    text-align: center;
                    color: #6B7280;
                    font-size: 14px;
                }
                .warning {
                    background: #FEF3C7;
                    border-left: 4px solid #F59E0B;
                    padding: 15px;
                    margin: 20px 0;
                    border-radius: 8px;
                }
                .warning p {
                    margin: 0;
                    color: #92400E;
                }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h1>🚛 borafrete</h1>
                </div>
                <div class='content'>
                    <h2>Olá, {$nome}!</h2>
                    <p>Recebemos uma solicitação para redefinir a senha da sua conta no BoraFrete.</p>
                    <p>Clique no botão abaixo para criar uma nova senha:</p>

                    <center>
                        <a href='{$link}' class='button'>Redefinir Senha</a>
                    </center>

                    <div class='warning'>
                        <p><strong>⏰ Este link expira em 1 hora!</strong></p>
                    </div>

                    <p>Se você não solicitou a recuperação de senha, ignore este email. Sua senha permanecerá segura.</p>

                    <p style='font-size: 14px; color: #9CA3AF; margin-top: 30px;'>
                        Se o botão não funcionar, copie e cole este link no navegador:<br>
                        <a href='{$link}' style='color: #4A90E2;'>{$link}</a>
                    </p>
                </div>
                <div class='footer'>
                    <p><strong>BoraFrete</strong> - Plataforma de Logística de Fretes</p>
                    <p>Este é um email automático, não responda.</p>
                </div>
            </div>
        </body>
        </html>
        ";
    }

    /**
     * Enviar email de boas-vindas
     */
    public function enviarBoasVindas($to, $nome) {
        $subject = 'Bem-vindo ao BoraFrete!';

        $body = "
        <!DOCTYPE html>
        <html lang='pt-BR'>
        <head>
            <meta charset='UTF-8'>
            <style>
                body { font-family: Arial, sans-serif; background-color: #f5f7fa; margin: 0; padding: 0; }
                .container { max-width: 600px; margin: 40px auto; background: #ffffff; border-radius: 20px; overflow: hidden; }
                .header { background: linear-gradient(135deg, #1E3A8A 0%, #4A90E2 100%); padding: 40px 30px; text-align: center; color: white; }
                .content { padding: 40px 30px; color: #6B7280; }
                .content h2 { color: #1E3A8A; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h1>🚛 borafrete</h1>
                    <p>Bem-vindo à plataforma!</p>
                </div>
                <div class='content'>
                    <h2>Olá, {$nome}!</h2>
                    <p>Seja bem-vindo ao BoraFrete, a maior plataforma de logística de fretes do Brasil!</p>
                    <p>Agora você pode:</p>
                    <ul>
                        <li>Cadastrar seus veículos</li>
                        <li>Criar ofertas de frete</li>
                        <li>Conectar-se com transportadoras e motoristas</li>
                        <li>Gerenciar sua frota</li>
                    </ul>
                    <p>Comece agora mesmo acessando sua conta!</p>
                </div>
            </div>
        </body>
        </html>
        ";

        return $this->enviarEmail($to, $subject, $body);
    }
}
