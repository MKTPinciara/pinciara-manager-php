<?php

namespace App\Controllers;

use App\Models\UserModel;
use CodeIgniter\Controller;

class Auth extends Controller
{
    protected $session;
    protected $userModel;

    public function __construct()
    {
        $this->session   = session();
        $this->userModel = new UserModel();
    }

    public function login()
    {
        if ($this->session->get('isLoggedIn')) {
            return redirect()->to('/imoveis');
        }

        return view('auth/login');
    }

    public function attemptLogin()
    {
        $email    = $this->request->getPost('email');
        $password = $this->request->getPost('password');

        if (empty($email) || empty($password)) {
            return redirect()->back()->withInput()->with('error', 'Preencha o e-mail e a senha.');
        }

        $user = $this->userModel->findByEmail($email);

        if (!$user) {
            return redirect()->back()->withInput()->with('error', 'Credenciais inválidas.');
        }

        if (!password_verify($password, $user['password'])) {
            return redirect()->back()->withInput()->with('error', 'Senha incorreta.');
        }

        $this->session->set([
            'isLoggedIn' => true,
            'userId'     => $user['id'],
            'userName'   => $user['nome'],
            'userEmail'  => $user['email'],
        ]);

        return redirect()->to('/imoveis')->with('success', 'Bem-vindo de volta, ' . $user['nome'] . '!');
    }

    public function logout()
    {
        $this->session->destroy();
        return redirect()->to('/login')->with('success', 'Sessão encerrada com sucesso.');
    }
}
