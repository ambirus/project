<?php

namespace Project\auth\base;

use Project\auth\User;
use Project\values\auth\base\LoginValue;
use Project\values\auth\base\RegisterValue;

/**
 * Class Auth.
 */
class Auth
{
    /**
     * @throws \ReflectionException
     *
     * @return bool
     */
    public function check(): bool
    {
        if (!isset($_COOKIE['id']) || !isset($_COOKIE['hash'])) {
            return false;
        }

        $userData = (new User())
            ->read()
            ->select('*,INET_NTOA(ip) AS ip')
            ->where('id = :id', ['id' => intval($_COOKIE['id'])])
            ->one()
            ->execute();

        if (
            ($userData['hash'] !== $_COOKIE['hash']) || ($userData['id'] !== $_COOKIE['id']) ||
            (($userData['ip'] !== $_SERVER['REMOTE_ADDR']) && ($userData['ip'] !== '0.0.0.0'))
        ) {
            setcookie('id', '', time() - 3600 * 24 * 30 * 12, '/');
            setcookie('hash', '', time() - 3600 * 24 * 30 * 12, '/', null, null, true);

            return false;
        }

        return true;
    }

    /**
     * @param LoginValue $loginValue
     *
     * @throws \ReflectionException
     *
     * @return bool
     */
    public function login(LoginValue $loginValue): bool
    {
        $userInstance = new User();
        $userData = $userInstance
            ->read()
            ->select('id, password')
            ->where('login = :login', ['login' => $loginValue->getLogin()])
            ->one()
            ->execute();

        if (password_verify($loginValue->getPassword(), $userData['password'])) {
            $hash = md5($this->generateCode(10));
            $dataToUpdate['hash'] = $hash;

            if ($loginValue->getCheckIp()) {
                $dataToUpdate['ip'] = "INET_ATON('".$_SERVER['REMOTE_ADDR']."')";
            }

            $userInstance
                ->update($dataToUpdate)
                ->where('id = :id', ['id' => $userData['id']])
                ->execute();

            setcookie('id', $userData['id'], time() + 60 * 60 * 24 * 30, '/');
            setcookie('hash', $hash, time() + 60 * 60 * 24 * 30, '/', null, null, true);

            return true;
        }

        return false;
    }

    public function logout(): void
    {
        setcookie('id', '', time() - 3600 * 24 * 30 * 12, '/');
        setcookie('hash', '', time() - 3600 * 24 * 30 * 12, '/', null, null, true);
    }

    /**
     * @param RegisterValue $registerValue
     *
     * @return mixed
     */
    public function register(RegisterValue $registerValue)
    {
        return (new User())
            ->create([
                'login' => $registerValue->getLogin(),
                'password' => password_hash($registerValue->getPassword(), PASSWORD_BCRYPT),
                'role_id' => $registerValue->getRoleId(),
                'email' => $registerValue->getEmail(),
            ])
            ->execute();
    }

    /**
     * @param int $length
     *
     * @return string
     */
    private function generateCode(int $length = 6): string
    {
        $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHI JKLMNOPRQSTUVWXYZ0123456789';
        $code = '';
        $clen = strlen($chars) - 1;
        while (strlen($code) < $length) {
            $code .= $chars[mt_rand(0, $clen)];
        }

        return $code;
    }
}
