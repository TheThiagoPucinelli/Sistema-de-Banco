<?php
session_start(); // Inicia a sessão, é uma forma de poder "ARMAZENAr" dados utilizando este comando junto com variavel salvarContas usando $_SESSION. 
// Tipo um banco de dados, não achei outro meio além desse no momento.

class ContaBanco {
    // Propriedades da classe ContaBanco
    private $numConta; // Número da conta
    private $tipo; // Tipo de conta (CC: Conta Corrente, CP: Conta Poupança)
    private $dono; // Dono da conta
    private $saldo; // Saldo da conta
    private $status; // Status da conta (ativa ou inativa)

    // Construtor da classe
    public function __construct($numConta, $tipo, $dono, $saldo = 0, $status = false) {
        $this->numConta = $numConta;
        $this->dono = $dono;
        $this->saldo = $saldo;
        $this->status = $status;
        $this->settipo($tipo);
    }

    // Getters e setters
    public function getnumConta() {
        return $this->numConta;
    }

    public function setnumConta($num) {
        $this->numConta = $num;
    }

    public function gettipo() {
        return $this->tipo;
    }

    public function settipo($tipo) {
        // Define o tipo de conta
        if ($tipo === 'CC') { // CC: Conta Corrente
            $this->tipo = 'Conta Corrente';
        } elseif ($tipo === 'CP') { // CP: Conta Poupança
            $this->tipo = 'Conta Poupança';
        }
    }

    public function getdono() {
        return $this->dono;
    }

    public function setdono($dono) {
        $this->dono = $dono;
    }

    public function getsaldo() {
        return $this->saldo;
    }

    public function setsaldo($saldo) {
        $this->saldo = $saldo;
    }

    public function getstatus() {
        return $this->status;
    }

    public function setstatus($status) {
        $this->status = $status;
    }

    // Abre uma conta e define o saldo inicial
    public function abrirConta($tipo) {
        if ($tipo === 'CC') { // CC: Conta Corrente
            $this->setstatus(true);
            $this->setsaldo(50); 
        } elseif ($tipo === 'CP') { // CP: Conta Poupança
            $this->setstatus(true);
            $this->setsaldo(100); 
        }
    }

    // Fecha uma conta se o saldo for zero
    public function fecharConta() {
        if ($this->getsaldo() == 0) {
            $this->setstatus(false);
            echo "Conta fechada com sucesso!<br>";
        } else {
            echo "Erro: Conta ainda tem saldo!<br>";
        }
    }

    // Reativar conta inativa
    public function reativarConta() {
        if (!$this->getstatus() && $this->getsaldo() == 0) {
            $this->setstatus(true);
            echo "Conta reativada com sucesso!<br>";
        } else {
            echo "Erro: Conta já está ativa ou não pode ser reativada!<br>";
        }
    }

    // Realiza um depósito na conta
    public function depositar($valor) {
        if ($this->getstatus()) {
            $this->setsaldo($this->getsaldo() + $valor);
            echo "Depósito de R$ $valor realizado com sucesso!<br>";
        } else {
            echo "Erro: Conta inativa ou inexistente!<br>";
        }
    }

    // Realiza um saque na conta se houver saldo suficiente
    public function sacar($valor) {
        if ($this->getstatus() && $this->getsaldo() >= $valor) {
            $this->setsaldo($this->getsaldo() - $valor);
            echo "Saque de R$ $valor realizado com sucesso!<br>";
        } else {
            echo "Erro: Saldo insuficiente ou conta inativa!<br>";
        }
    }

    // Paga a mensalidade da conta
    public function pagarMensalidade() {
        if ($this->getstatus()) {
            if ($this->gettipo() == 'Conta Corrente') { // Conta Corrente
                $valorMensalidade = 10; // Mensalidade de R$10
            } else { // Conta Poupança
                $valorMensalidade = 12; // Mensalidade de R$12
            }
            if ($this->getsaldo() >= $valorMensalidade) {
                $this->setsaldo($this->getsaldo() - $valorMensalidade);
                echo "Mensalidade de R$ $valorMensalidade debitada com sucesso!<br>";
            } else {
                echo "Erro: Saldo insuficiente para pagar a mensalidade!<br>";
            }
        } else {
            echo "Erro: Conta inativa!<br>";
        }
    }

    // Exibe os dados da conta
    public function mostrarDados() {
        echo 'Número da Conta: ' . $this->getnumConta() . '<br>';
        echo 'Tipo de Conta: ' . $this->gettipo() . '<br>';
        echo 'Dono da Conta: ' . $this->getdono() . '<br>';
        echo 'Saldo Atual: R$ ' . $this->getsaldo() . '<br>';
        echo 'Status da Conta: ' . ($this->getstatus() ? 'Ativa' : 'Inativa') . '<br>';
    }
}

// Função para carregar as contas da sessão
function carregarContas() {
    return isset($_SESSION['contas']) ? $_SESSION['contas'] : [];
}

// Função para salvar as contas na sessão
function salvarContas($contas) {
    $_SESSION['contas'] = $contas;
}

// Processamento do formulário
if ($_SERVER["REQUEST_METHOD"] == "POST") { // Verifica se o método de solicitação é POST
    $action = $_POST['action']; // Obtém a ação do formulário (abrirConta, depositar, sacar, pagarMensalidade, fecharConta, reativarConta)
    $numConta = $_POST['numConta']; 
    $contas = carregarContas(); // Carrega as contas (simulação de carregamento de um armazenamento de dados)

    if ($action == 'abrirConta') {
        // Verifica se já existe uma conta com o mesmo número
        if (isset($contas[$numConta])) {
            echo "Erro: Já existe uma conta com o número $numConta!<br>";
        } else {
            $tipo = $_POST['tipo'];
            $dono = $_POST['dono'];
            $conta = new ContaBanco($numConta, $tipo, $dono);
            $conta->abrirConta($tipo);
            $contas[$numConta] = $conta;
            salvarContas($contas);
            echo 'Conta criada com sucesso! <br>';
            $conta->mostrarDados();
        }
    } elseif (isset($contas[$numConta])) {
        $conta = $contas[$numConta];
        switch ($action) {
            case 'depositar':
                $valor = $_POST['valor'];
                $conta->depositar($valor);
                break;
            case 'sacar':
                $valor = $_POST['valor'];
                $conta->sacar($valor);
                break;
            case 'pagarMensalidade':
                $conta->pagarMensalidade();
                break;
            case 'fecharConta':
                $conta->fecharConta();
                break;
            case 'reativarConta': // Adicionado o caso para reativar a conta
                $conta->reativarConta();
                break;
        }
        $contas[$numConta] = $conta;
        salvarContas($contas);
        $conta->mostrarDados();
    } else {
        echo "Erro: Conta não encontrada!<br>";
    }
}
?>
