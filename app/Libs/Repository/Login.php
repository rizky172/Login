<?php

namespace App\Libs\Repository;

use Auth;

use Illuminate\Validation\Validator;
use Illuminate\Support\Facades\Validator as ValidatorFacade;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Hash;

use App\Models\User;

class Login
{
    public static function validOrThrow(Validator $validator)
    {
        if ($validator->fails()) {
            throw new ValidationException($validator);
        }
    }

    public static function validateRegister($name, $username, $email, $password, $confirmPassword)
    {
        $fields = [
            'name' => $name,
            'username' => $username,
            'email' => $email,
            'password' => $password,
            'confirm_password' => $confirmPassword
        ];

        $rules = [
            'name' => 'required',
            'username'=> 'required|unique:users,username',
            'email'=> 'required|email|unique:users,email',
            'password' => 'required|min:5',
            'confirm_password' => 'same:password',
        ];

        $messages = [
            'name.required' => 'Name wajib diisi.',
            'username.required' => 'Username wajib diisi.',
            'username.unique' => 'Username sudah ada.',
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Email tidak valid.',
            'email.unique' => 'Email sudah ada.',
            'password.required' => 'Password wajib diisi.',
            'password.min' => 'Password minimal 5 karakter.',
            'confirm_password.same' => 'Confrim Password tidak sama.',
        ];

        $validator = ValidatorFacade::make($fields, $rules, $messages);
        self::validOrThrow($validator);
    }

    public static function register($name, $username, $email, $password, $confirmPassword)
    {
        self::validateRegister($name, $username, $email, $password, $confirmPassword);

        $user = new User();
        $user->name = $name;
        $user->username = $username;
        $user->email = $email;
        $user->role = 'member';
        $user->password = Hash::make($password);
        $user->save();
    }

    public static function validateLogin($username, $password)
    {
        $fields = [
            'username' => $username,
            'password' => $password,
        ];

        $rules = [
            'username'=> 'required|exists:users,username',
            'password' => 'required',
        ];

        $messages = [
            'username.required' => 'Username wajib diisi.',
            'username.exists' => 'Username sudah ada.',
            'password.required' => 'Password wajib diisi.',
        ];

        $validator = ValidatorFacade::make($fields, $rules, $messages);
        self::validOrThrow($validator);
    }

    public static function login($username, $password)
    {
        self::validateLogin($username, $password);

        if (Auth::attempt(['username' => $username, 'password' => $password])) {
            return true;
        }
    
        return false;
    }

    public static function validateForgotPassword($username, $password, $confirmPassword)
    {
        $fields = [
            'username' => $username,
            'password' => $password,
            'confirm_password' => $confirmPassword
        ];

        $rules = [
            'username'=> 'required|exists:users,username',
            'password' => 'required|min:5',
            'confirm_password' => 'same:password',
        ];

        $messages = [
            'username.required' => 'Username wajib diisi.',
            'username.exists' => 'Username tidak ada.',
            'password.required' => 'Password wajib diisi.',
            'password.min' => 'Password minimal 5 karakter.',
            'confirm_password.same' => 'Confrim Password tidak sama.',
        ];

        $validator = ValidatorFacade::make($fields, $rules, $messages);
        self::validOrThrow($validator);
    }

    public static function forgotPassword($username, $password, $confirmPassword)
    {
        self::validateForgotPassword($username, $password, $confirmPassword);

        $user = User::firstOrNew(['username' => $username]);
        $user->password = Hash::make($password);
        $user->save();
    }
}
