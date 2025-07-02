<?php

// ------[ ÁREA DE PARAMETROS DE SEGURANÇA ]------
session_start();

if (!isset($_SESSION['usuario_id'])) {
    header("Location: ../../Firewall/login.php");
    exit;
}

include '../../Firewall/Auth/autenticacao.php';
include '../../Firewall/Auth/controle_sessao.php';
include '../../conexao.php';

// ------[ FIM DA ÁREA DE PARAMETROS DE SEGURANÇA ]------

// Buscar usuários
$sql = "SELECT id, nome, cpf, matricula, perfil, ativo, ultimo_login FROM usuarios";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <title>Gestão de Usuários - Administrador</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.min.js"></script>
    <link rel="stylesheet" href="usuarios.css">
</head>

<body>
    <header class="cabecalho">
        <h1>Painel de Gestão</h1>
        <nav>
            <a class="nav" href="../index.php">Início</a>
            <a class="nav" href="../Usuarios/usuarios.php">Usuários</a>
            <a class="nav" href="../Visitantes/visitantes.php">Visitantes</a>
            <a class="nav" href="../Setores/index.php">Setores</a>
            <a class="nav" href="../Visitas/visitas.php">Visitas</a>
            <a class="nav" href="../Documentos/documentos.php">Repositório</a>
            <a class="nav" href="../../Firewall/Auth/logout.php">Sair</a>
        </nav>
    </header>

    <main>
        <section class="Modulo">
            <div class="topo-modulo">
                <h1>Gestão de usuários</h1>

                <button class="btnAdicionar" id="btnAdicionarUsuario">
                    <span class="btn-conteudo">
                        <span>Novo Usuário</span>
                        <img src="../../Imagens/Icons/adicionar-usuario.png" alt="Novo usuário">
                    </span>
                </button>
            </div>
        </section>

        <section class="card">
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th class="col-nome">Nome</th>
                            <th class="col-matricula text-center">Matrícula</th>
                            <th class="text-center">Perfil</th>
                            <th th class=" text-center">Status</th>
                            <th th class="text-center">Último Login</th>
                            <th th class="text-center col-acoes"">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($user = $result->fetch_assoc()): ?>
                            <tr>
                                <td><?= htmlspecialchars($user['nome']) ?></td>
                                <td class=" text-center"><?= htmlspecialchars($user['matricula']) ?></td>
                            <td class="text-center"><?= htmlspecialchars($user['perfil']) ?></td>
                            <td class="col-status text-center"><?= $user['ativo'] ? 'Ativo' : 'Inativo' ?></td>
                            <td class="text-center"><?= $user['ultimo_login'] ? date('d/m/Y H:i', strtotime($user['ultimo_login'])) : '-' ?></td>
                            <td class="text-center">

                                <button class="btn-acao btnEditar" data-id="<?= $user['id'] ?>">
                                    <div class="btn-conteudo">
                                        Editar
                                        <img src="../../Imagens/Icons/editar.png" alt="Editar">
                                    </div>
                                </button>

                                <a href="excluir_usuario.php?id=<?= $user['id'] ?>" class="btn-acao btn-excluir" onclick="return confirm('Deseja excluir este usuário?')">
                                    Excluir
                                    <img src="../../Imagens/Icons/excluir.png" alt="Excluir">
                                </a>

                                <?php if ($user['ativo']): ?>
                                    <a class="btn-acao btn-inativo" href="alterar_status.php?id=<?= $user['id'] ?>&acao=desativar" onclick="return confirm('Deseja desativar este usuário?');">
                                        Desativar
                                    </a>
                                <?php else: ?>
                                    <a class="btn-acao btn-ativo" href="alterar_status.php?id=<?= $user['id'] ?>&acao=ativar" onclick="return confirm('Deseja ativar este usuário?');">
                                        Ativar
                                    </a>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </section>
    </main>

    <footer class="rodape">
        2025 SEAD | Todos os direitos reservados
    </footer>

    <div id="modalUsuario" class="modal" style="display:none;">
        <div class="modal-content">
            <span class="fechar">&times;</span>
            <h2 id="tituloModal">Adicionar Usuário</h2><br>

            <form id="formUsuario">

                <input type="hidden" name="id" id="usuarioId" value="">
                <div class="form-campo">
                    <label>Nome:</label><br>
                    <input type="text" name="nome" id="nome" required>
                </div>

                <div class="form-campo">
                    <label>Matrícula:</label><br>
                    <input type="text" name="matricula" id="matricula" required>
                </div>

                <div class="form-campo">
                    <label>Senha:</label><br>
                    <input type="password" name="senha" id="senha" maxlength="10">
                </div>

                <div class="form-perfil">
                    <label>Perfil:</label><br>
                    <select name="perfil" id="perfil" required>
                        <option value="ADM">Administrador</option>
                        <option value="Recepcionista">Recepcionista</option>
                        <option value="GEPES">GEPES</option>
                        <option value="GCP">GCP</option>
                    </select>
                </div>


                <label>Status do perfil:</label><br>
                <label>
                    <input type="checkbox" name="ativo" checked> Ativo
                </label><br><br>

                <div class="botao-modal">
                    <button class="btnSalvar" type="submit">
                        <span class="btn-conteudo">
                            <span>Salvar</span>
                            <img src="../../Imagens/Icons/salve.png" alt="Atualizar">
                        </span>
                    </button>
                </div>
            </form>
        </div>
    </div>

</body>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const modal = document.getElementById('modalUsuario');
        const btnAdicionar = document.getElementById('btnAdicionarUsuario');
        const spanFechar = modal.querySelector('.fechar');
        const form = document.getElementById('formUsuario');
        const tituloModal = document.getElementById('tituloModal');

        // Abrir modal para adicionar
        btnAdicionar.addEventListener('click', () => {
            tituloModal.textContent = 'Adicionar Usuário';
            form.reset();
            document.getElementById('usuarioId').value = '';
            modal.style.display = 'block';
        });

        // Fechar modal
        spanFechar.addEventListener('click', () => {
            modal.style.display = 'none';
        });

        // Fechar ao clicar fora do conteúdo
        window.addEventListener('click', (e) => {
            if (e.target == modal) {
                modal.style.display = 'none';
            }
        });

        // Abrir modal para editar ao clicar em botão
        document.querySelectorAll('.btnEditar').forEach(button => {
            button.addEventListener('click', () => {
                const id = button.getAttribute('data-id');
                tituloModal.textContent = 'Editar Usuário';

                // Limpa formulário
                form.reset();
                document.getElementById('usuarioId').value = id;

                // Buscar dados do usuário via AJAX e preencher o formulário
                fetch('buscar_usuario.php?id=' + id)
                    .then(response => response.json())
                    .then(data => {
                        document.getElementById('nome').value = data.nome;
                        document.getElementById('matricula').value = data.matricula;
                        document.getElementById('perfil').value = data.perfil;
                        // Não preencher senha para segurança (deixe em branco)
                        modal.style.display = 'block';
                    });
            });
        });

        // Enviar formulário (aqui você pode enviar via AJAX ou submit normal)
        form.addEventListener('submit', e => {
            e.preventDefault();

            const formData = new FormData(form);

            fetch('salvar_usuario.php', {
                    method: 'POST',
                    body: formData
                }).then(response => response.text())
                .then(result => {
                    alert(result);
                    modal.style.display = 'none';
                    location.reload(); // Recarrega a página para atualizar a lista
                }).catch(err => alert('Erro ao salvar usuário.'));
        });
    });

    function aplicarMascaraCPF(input) {
        let value = input.value.replace(/\D/g, '');
        value = value.replace(/(\d{3})(\d)/, '$1.$2');
        value = value.replace(/(\d{3})(\d)/, '$1.$2');
        value = value.replace(/(\d{3})(\d{1,2})$/, '$1-$2');
        input.value = value;
    }

    $('#matricula').mask('000.000-0 A', {
        translation: {
            'A': {
                pattern: /[A-Za-z]/
            }
        }
    });

    // Converte para maiúsculas ao digitar
    $('#matricula').on('input', function() {
        this.value = this.value.toUpperCase();
    });
</script>

</html>